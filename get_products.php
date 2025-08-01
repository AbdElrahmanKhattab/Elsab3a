<?php
require_once 'db.php';
header('Content-Type: application/json; charset=utf-8');

// Get filters
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';

// Build query
$query = 'SELECT * FROM products WHERE stock > 0';
$params = [];
if ($search !== '') {
    $query .= ' AND (name LIKE :search)'; // Only search by name
    $params[':search'] = "%$search%";
}
if ($category !== '') {
    // We'll filter by category in PHP after fetching, since categories are many-to-many
}
if ($min_price !== '' && is_numeric($min_price)) {
    $query .= ' AND discounted_price >= :min_price';
    $params[':min_price'] = $min_price;
}
if ($max_price !== '' && is_numeric($max_price)) {
    $query .= ' AND discounted_price <= :max_price';
    $params[':max_price'] = $max_price;
}
$sort = $_GET['sort'] ?? '';
if ($sort === 'price_asc') {
    $query .= ' ORDER BY CAST(discounted_price AS DECIMAL(10,2)) ASC';
} elseif ($sort === 'price_desc') {
    $query .= ' ORDER BY CAST(discounted_price AS DECIMAL(10,2)) DESC';
} else {
    $query .= ' ORDER BY id DESC';
}
$stmt = $pdo->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$products = $stmt->fetchAll();

// Fetch categories for each product
$product_ids = array_column($products, 'id');
$product_categories = [];
if ($product_ids) {
    $ids = implode(',', array_map('intval', $product_ids));
    $cat_stmt = $pdo->query("SELECT pc.product_id, c.name FROM product_categories pc JOIN categories c ON pc.category_id = c.id WHERE pc.product_id IN ($ids)");
    foreach ($cat_stmt as $row) {
        $product_categories[$row['product_id']][] = $row['name'];
    }
}

// Add categories to each product
$filtered_products = [];
foreach ($products as &$product) {
    $product['categories'] = $product_categories[$product['id']] ?? [];
    // If category filter is set, only include products with that category
    if ($category !== '') {
        if (!in_array($category, $product['categories'])) continue;
    }
    $filtered_products[] = $product;
}

echo json_encode([
    'success' => true,
    'products' => $filtered_products
]); 