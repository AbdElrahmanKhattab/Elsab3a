<?php
require_once 'db.php';

// Fetch all in-stock products
$stmt = $pdo->prepare('SELECT * FROM products WHERE stock > 0 ORDER BY id DESC');
$stmt->execute();
$products = $stmt->fetchAll();

// Fetch categories for each product
$product_categories = [];
if ($products) {
    $ids = implode(',', array_map('intval', array_column($products, 'id')));
    if ($ids) {
        $cat_stmt = $pdo->query("SELECT pc.product_id, c.name FROM product_categories pc JOIN categories c ON pc.category_id = c.id WHERE pc.product_id IN ($ids)");
        foreach ($cat_stmt as $row) {
            $product_categories[$row['product_id']][] = $row['name'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Our Products</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', Arial, sans-serif; background: #f8fafc; margin: 0; }
        .container { max-width: 1200px; margin: 40px auto; padding: 0 16px; }
        h1 { text-align: center; color: #22223b; margin-bottom: 32px; }
        .products-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(270px, 1fr)); gap: 28px; }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); padding: 20px; display: flex; flex-direction: column; align-items: center; transition: box-shadow 0.2s; }
        .card:hover { box-shadow: 0 8px 32px rgba(0,0,0,0.12); }
        .card img { max-width: 180px; max-height: 180px; border-radius: 8px; margin-bottom: 16px; }
        .card h2 { font-size: 1.2rem; color: #22223b; margin: 0 0 10px 0; }
        .card .desc { color: #555; font-size: 0.98rem; margin-bottom: 10px; text-align: center; }
        .card .price { color: #38b000; font-size: 1.1rem; font-weight: 700; margin-bottom: 8px; }
        .card .stock { font-size: 0.95rem; margin-bottom: 6px; }
        .card .out-stock { color: #e63946; font-weight: 700; }
        .card .in-stock { color: #38b000; font-weight: 700; }
        .product-details .categories { font-size: 0.95rem; color: #555; margin-bottom: 6px; display: none;}
        @media (max-width: 700px) {
            .container { padding: 0 4px; }
            .products-grid { gap: 12px; }
            .card { padding: 10px; }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Our Products</h1>
    <div class="products-grid">
        <?php foreach ($products as $product): ?>
            <div class="card">
                <?php if ($product['image']): ?>
                    <img src="assets/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <?php endif; ?>
                <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                <div class="desc"><?php echo nl2br(htmlspecialchars($product['description'])); ?></div>
                <div class="price">Price: <?php echo number_format($product['price'], 2); ?></div>
                <div class="stock in-stock">
                    In Stock (<?php echo (int)$product['stock']; ?> available)
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html> 