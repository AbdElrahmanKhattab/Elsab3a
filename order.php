<?php
require_once 'db.php';

// Set timezone to Egypt
date_default_timezone_set('Africa/Cairo');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Helper: fetch product by ID
function getProductById($pdo, $id) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Handle form submission
$success = false;
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $customer_name = $_POST['customer_name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $address = $_POST['address'] ?? '';
        $products_json = $_POST['products_json'] ?? '';
        $total_price = $_POST['total_price'] ?? 0;
        $products = json_decode($products_json, true);
        
        if (!$customer_name || !$phone || !$address || !$products || !is_array($products)) {
            $error = 'يرجى ملء جميع الحقول بشكل صحيح.';
        } else {
            // Check if orders table exists
            $tableCheck = $pdo->query("SHOW TABLES LIKE 'orders'");
            if ($tableCheck->rowCount() == 0) {
                // Create orders table if it doesn't exist
                $createTable = $pdo->prepare("
                    CREATE TABLE orders (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        customer_name VARCHAR(255) NOT NULL,
                        phone VARCHAR(50) NOT NULL,
                        address TEXT NOT NULL,
                        products JSON NOT NULL,
                        total_price DECIMAL(10,2) NOT NULL,
                        order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        status VARCHAR(50) DEFAULT 'pending'
                    )
                ");
                $createTable->execute();
            }
            
            // Get current date/time in Egypt timezone
            $current_datetime = date('Y-m-d H:i:s');
            $stmt = $pdo->prepare('INSERT INTO orders (customer_name, phone, address, products, total_price, order_date, status) VALUES (?, ?, ?, ?, ?, ?, "pending")');
            $stmt->execute([$customer_name, $phone, $address, json_encode($products, JSON_UNESCAPED_UNICODE), $total_price, $current_datetime]);
            $success = true;
        }
    } catch (Exception $e) {
        $error = 'خطأ في قاعدة البيانات: ' . $e->getMessage();
        error_log('Order error: ' . $e->getMessage());
    }
}

// Detect order type
$is_cart = isset($_GET['cart']) && $_GET['cart'] == 1;
$product = null;
if (!$is_cart && isset($_GET['product_id'])) {
    $product = getProductById($pdo, $_GET['product_id']);
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إتمام الطلب</title>
    <link rel="stylesheet" href="styles/store.css">
    <style>
        .order-form-container {
            max-width: 500px;
            margin: 40px auto;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 16px rgba(47,121,200,0.09);
            padding: 2rem 1.5rem 1.5rem 1.5rem;
        }
        .order-form-container h2 {
            margin-bottom: 1.2rem;
            color: var(--primary-blue);
        }
        .order-form-group {
            margin-bottom: 1.2rem;
        }
        .order-form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
        }
        .order-form-group input, .order-form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1.5px solid #d1d5db;
            border-radius: 7px;
            font-size: 1rem;
        }
        .order-form-group textarea { resize: vertical; }
        .order-summary {
            background: #f8fafc;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.2rem;
        }
        .order-summary-title { font-weight: bold; margin-bottom: 0.5rem; }
        .order-summary-list { margin: 0; padding: 0; list-style: none; }
        .order-summary-list li { margin-bottom: 0.5rem; }
        .order-summary-total { font-weight: bold; color: var(--primary-blue); margin-top: 0.7rem; }
        .order-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 1rem; border-radius: 8px; text-align: center; margin-bottom: 1rem; }
        .order-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 1rem; border-radius: 8px; text-align: center; margin-bottom: 1rem; }
        .order-submit-btn {
            background: var(--primary-blue);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            padding: 12px 0;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }
        .order-submit-btn:hover { background: #245a8d; }
        .back-to-store-btn {
            background: #28a745;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            padding: 10px 20px;
            cursor: pointer;
            margin-top: 15px;
            text-decoration: none;
            display: inline-block;
            transition: background 0.2s;
        }
        .back-to-store-btn:hover { 
            background: #218838; 
            text-decoration: none;
            color: #fff;
        }
        .success-actions {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="order-form-container">
        <h2>إتمام الطلب</h2>
        <?php if ($success): ?>
            <div class="order-success">تم إرسال طلبك بنجاح! سنقوم بالتواصل معك قريباً.</div>
            <div class="success-actions">
                <a href="store.php" class="back-to-store-btn">العودة للمتجر</a>
            </div>
            <script>if (window.localStorage) localStorage.removeItem('elsab3a_cart');</script>
        <?php elseif ($error): ?>
            <div class="order-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (!$success): ?>
        <form method="post" id="order-form">
            <div class="order-summary" id="order-summary">
                <div class="order-summary-title">ملخص الطلب</div>
                <ul class="order-summary-list" id="order-summary-list">
                    <!-- JS will fill for cart, PHP for single -->
                    <?php if ($product): ?>
                        <li id="single-product-summary">
                            <?php echo htmlspecialchars($product['name']); ?>
                            (x<span id="single-qty">1</span>) - EGP<span id="single-total-price"><?php echo number_format($product['discounted_price'], 2); ?></span>
                        </li>
                    <?php endif; ?>
                </ul>
                <div class="order-summary-total" id="order-summary-total">
                    <?php if ($product): ?>
                        الإجمالي: EGP<span id="single-grand-total"><?php echo number_format($product['discounted_price'], 2); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ($product): ?>
            <div class="order-form-group">
                <label for="single-quantity">الكمية *</label>
                <input type="number" id="single-quantity" name="single_quantity" value="1" min="1" max="99" required>
            </div>
            <?php endif; ?>
            <div class="order-form-group">
                <label for="customer_name">الاسم الكامل *</label>
                <input type="text" id="customer_name" name="customer_name" required>
            </div>
            <div class="order-form-group">
                <label for="phone">رقم الهاتف *</label>
                <input type="tel" id="phone" name="phone" required>
            </div>
            <div class="order-form-group">
                <label for="address">العنوان *</label>
                <textarea id="address" name="address" rows="3" required></textarea>
            </div>
            <input type="hidden" name="products_json" id="products_json" value=''>
            <input type="hidden" name="total_price" id="total_price" value=''>
            <button type="submit" class="order-submit-btn">تأكيد الطلب</button>
        </form>
        <?php endif; ?>
    </div>
    <?php if ($is_cart): ?>
    <script>
    // Fill order summary and hidden fields from localStorage cart
    function fillCartOrderSummary() {
        const cart = JSON.parse(localStorage.getItem('elsab3a_cart') || '[]');
        const list = document.getElementById('order-summary-list');
        const totalDiv = document.getElementById('order-summary-total');
        let total = 0;
        list.innerHTML = '';
        cart.forEach(item => {
            const li = document.createElement('li');
            li.textContent = `${item.name} (x${item.quantity}) - EGP${Number(item.price).toLocaleString()}`;
            list.appendChild(li);
            total += item.price * item.quantity;
        });
        totalDiv.textContent = `الإجمالي: EGP${total.toLocaleString()}`;
        document.getElementById('products_json').value = JSON.stringify(cart);
        document.getElementById('total_price').value = total;
    }
    fillCartOrderSummary();
    </script>
    <?php elseif ($product): ?>
    <script>
    // Fill hidden fields for single product and update on quantity change
    const price = <?php echo (float)$product['discounted_price']; ?>;
    const name = <?php echo json_encode($product['name']); ?>;
    const id = <?php echo (int)$product['id']; ?>;
    const image = <?php echo json_encode($product['image']); ?>;
    const qtyInput = document.getElementById('single-quantity');
    const qtySpan = document.getElementById('single-qty');
    const totalPriceSpan = document.getElementById('single-total-price');
    const grandTotalSpan = document.getElementById('single-grand-total');
    function updateSingleOrderSummary() {
        const qty = parseInt(qtyInput.value) || 1;
        qtySpan.textContent = qty;
        const total = price * qty;
        totalPriceSpan.textContent = total.toLocaleString();
        grandTotalSpan.textContent = total.toLocaleString();
        document.getElementById('products_json').value = JSON.stringify([
            { id, name, price, quantity: qty, image }
        ]);
        document.getElementById('total_price').value = total;
    }
    qtyInput.addEventListener('input', updateSingleOrderSummary);
    updateSingleOrderSummary();
    </script>
    <?php endif; ?>
</body>
</html> 