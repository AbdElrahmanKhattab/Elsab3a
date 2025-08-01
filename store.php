<?php
require_once 'db.php';
// Get filters
$search = $_GET['search'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
if ($category !== '') {
    // Remove invisible Unicode characters and normalize spaces
    $category = preg_replace('/\x{200C}|\x{200D}|\x{FEFF}/u', '', $category); // Remove ZWNJ, ZWJ, BOM
    $category = preg_replace('/\s+/', ' ', $category); // Normalize spaces
}
// Debug: See what category is being searched for
// echo "<pre>Category param: |$category|</pre>";
$sort = $_GET['sort'] ?? '';
$page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;

// Build query for ALL products (no pagination limit)
$query = 'SELECT p.* FROM products p';
$joins = '';
$where = ' WHERE p.stock > 0';
$params = [];

if ($category !== '') {
    $joins .= ' INNER JOIN product_categories pc ON p.id = pc.product_id INNER JOIN categories c ON pc.category_id = c.id';
    $where .= ' AND c.name = :category';
    $params[':category'] = $category;
}
if ($search !== '') {
    $where .= ' AND (p.name LIKE :search)';
    $params[':search'] = "%$search%";
}
if ($min_price !== '' && is_numeric($min_price)) {
    $where .= ' AND p.discounted_price >= :min_price';
    $params[':min_price'] = $min_price;
}
if ($max_price !== '' && is_numeric($max_price)) {
    $where .= ' AND p.discounted_price <= :max_price';
    $params[':max_price'] = $max_price;
}

// Get ALL products (no pagination)
$all_products_query = "SELECT p.* FROM products p $joins $where ORDER BY p.id DESC";
$all_products_stmt = $pdo->prepare($all_products_query);
foreach ($params as $key => $value) {
    $all_products_stmt->bindValue($key, $value);
}
$all_products_stmt->execute();
$all_products = $all_products_stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate pagination info for JavaScript
$total_products = count($all_products);
$total_pages = max(1, ceil($total_products / $per_page));

// Get current page products for initial display
$products = array_slice($all_products, $offset, $per_page);

// Fetch all categories for the filter dropdown
$categories = $pdo->query("SELECT * FROM categories ORDER BY id DESC")->fetchAll();

// Helper: get category id by name
function get_category_id($pdo, $name) {
    $stmt = $pdo->prepare('SELECT id FROM categories WHERE name = ?');
    $stmt->execute([$name]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row['id'] : null;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  
  <title>سباكة ستور | خلاطات مياه، أدوات صحية، تأسيس سباكة – Elsab3a</title>
  <meta name="description" content="متجر سباكة ستور – Elsab3a لبيع خلاطات المياه، الأدوات الصحية، ولوازم تأسيس سباكة الحمام والمطبخ في مصر. جودة ممتازة وأسعار منافسة.">
  <!-- ملاحظة: Google لا تعتمد على وسم keywords ولكن يمكنك الحفاظ عليه إذا أحببت -->
  <meta name="keywords" content="خلاطات مياه, خلاطات مطبخ, خلاطات بانيو, خلاطات دش, خلاطات جروهي, خلاطات ايطالي, ارخص خلاطات مياه, أدوات صحية, احواض مطبخ, احواض حمام, مواسير سباكة, اذواق صحية, صمامات مياه, فلاتر مياه, سخانات مياه, انظمة توفير المياه, تركيبات سباكة, وصلات سباكة, تجهيز حمامات, ادوات سباكة, قطع غيار خلاطات, محابس مياه, مفاتيح مياه, صنابير, اكسسوارات حمام, اكسسوارات مطبخ, سيفون, شنيور, فلتر, تانك مياه, تانكات مياه, عوازل سباكة, حل مشاكل تسرب المياه, صيانة سباكة, دليل السباكة في مصر, اسعار سباكة مصر, elsab3a, elsab3a store, el sab3a, el sab3a store, السبعة, السبعة ستور, السبعة للسباكة, متجر elsab3a, موقع elsab3a store, Sab3a Plumbing Store" >
<meta name="author" content="Elsab3a Store">

<script async="" src="https://www.googletagmanager.com/gtag/js?id=G-3PJEZYW2H5"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-3PJEZYW2H5');
</script>
  <!-- الروابط المعيارية -->
  <link rel="canonical" href="https://elsab3a-store.com/">

  <!-- Open Graph -->
  <meta property="og:locale" content="ar_EG">
  <meta property="og:type" content="website">
  <meta property="og:title" content="سباكة ستور | خلاطات مياه، أدوات صحية – Elsab3a">
  <meta property="og:description" content="تسوّق من سباكة ستور – Elsab3a خلاطات مياه، أدوات صحية، وتأسيس سباكة بأفضل الأسعار في مصر.">
  <meta property="og:url" content="https://elsab3a-store.com/">
  <meta property="og:site_name" content="سباكة ستور – Elsab3a">
  <meta property="og:image" content="https://elsab3a-store.com/assets/pagelogo.png">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">
  <meta property="og:image:alt" content="شعار سباكة ستور – Elsab3a">

  <!-- Twitter Cards -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:site" content="@elsab3a_store">
  <meta name="twitter:title" content="سباكة ستور | خلاطات مياه وأدوات صحية – Elsab3a">
  <meta name="twitter:description" content="متجر سباكة ستور – Elsab3a يوفر خلاطات مياه، أدوات صحية، وقطع غيار سباكة بأسعار منافسة.">
  <meta name="twitter:image" content="https://elsab3a-store.com/assets/pagelogo.png">

  <!-- Schema.org JSON‑LD -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@graph": [
      {
        "@type": "WebSite",
        "@id": "https://elsab3a-store.com/#website",
        "url": "https://elsab3a-store.com/",
        "name": "سباكة ستور – Elsab3a",
        "inLanguage": "ar",
        "potentialAction": {
          "@type": "SearchAction",
          "target": "https://elsab3a-store.com/?s={search_term_string}",
          "query-input": "required name=search_term_string"
        }
      },
      {
        "@type": "LocalBusiness",
        "@id": "https://elsab3a-store.com/#localbusiness",
        "name": "سباكة ستور – Elsab3a",
        "description": "متجر سباكة ستور – Elsab3a لبيع خلاطات المياه والأدوات الصحية ولوازم تأسيس سباكة.",
        "url": "https://elsab3a-store.com/",
        "logo": "https://elsab3a-store.com/assets/pagelogo.png",
        "image": "https://elsab3a-store.com/assets/pagelogo.png",
        "address": {
          "@type": "PostalAddress",
          "addressCountry": "EG",
          "addressLocality": "Cairo"
        },
        "contactPoint": {
          "@type": "ContactPoint",
          "contactType": "customer service",
          "availableLanguage": ["Arabic"]
        }
      }
    ]
  }
  </script>
  <link rel="stylesheet" href="styles/store.css" />
  <link rel="icon" type="image/png" href="assets/pagelogo.png">
</head>
<body>
    <!-- Loading Screen -->
  <div class="loading" id="loading-screen">
        <div class="loaderClass">
            <img src="assets/pagelogo.png" alt="LSBA3A Logo" />
        </div>
        <div class="logo-container">

            <div class="text-logo">
                <h1>LSAB<span>3</span>A</h1>
            </div>
            <div class="css-logo">
                <div class="bar1"></div>
                <div class="bar2"></div>
                <div class="bar3"></div>
            </div>
        </div>
    </div>
<header class="main-header">
   <nav class="main-nav">
      <div class="logo">
        <a href="index.php"><img src="assets/logo.jpg" alt="LSAB3AH Logo" class="navbar-logo" /></a>
      </div>
      <ul class="nav-links">
        <li><a href="index.php" class="nav-link">الرئيسية</a></li>
        <li><a href="store.php?category=تأسيس" class="nav-link">تأسيس</a></li>
        <li><a href="store.php?category=إكسسوار الحمام" class="nav-link">إكسسوار الحمام</a></li>
        <li class="dropdown">
            <a href="#" class="nav-link dropdown-toggle">تشطيب</a>
            <ul class="dropdown-menu">
                <li><a href="store.php?category=طقم حمام كامل" class="nav-link">طقم حمام كامل</a></li>
                <li><a href="store.php?category=وحدات حمام" class="nav-link">وحدات حمام</a></li>
                <li><a href="store.php?category=البانيو" class="nav-link">بانيوهات</a></li>
                <li><a href="store.php?category=لوازم حمام" class="nav-link">لوازم حمام</a></li>
                <li><a href="store.php?category=الدش" class="nav-link">انظمة الدش</a></li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#" class="nav-link dropdown-toggle">احواض</a>
            <ul class="dropdown-menu">
                <li><a href="store.php?category=حوض المطبخ الإستانلس" class="nav-link">احواض الاستانلس</a></li>
                <li><a href="store.php?category=احواض ديكور" class="nav-link">احواض الديكور</a></li>
            </ul>
        </li>
        <li class="dropdown">
        <a href="#" class="nav-link dropdown-toggle">خلاطات</a>
            <ul class="dropdown-menu">
                <li><a href="store.php?category=طقم خلاط كامل" class="nav-link">اطقم خلاطات</a></li>
                <li><a href="store.php?category= خلاط الحمام الديكور" class="nav-link">خلاطات حمام ديكور</a></li>
                <li><a href="store.php?category=خلاط مطبخ ديكور" class="nav-link">خلاطات مطبخ ديكور</a></li>
            </ul>
        </li>
      </ul>
    <div class="desktop-social-links">
      <div class="cart-icon" id="cart-icon" style="position:relative;cursor:pointer;float:right;margin:0 20px;">
        <img src="assets/cart.png" alt="cart" style="width:32px;height:32px;">
        <span class="cart-count" style="position:absolute;top:-8px;right:-8px;background:#e63946;color:#fff;border-radius:50%;padding:2px 7px;font-size:13px;min-width:20px;text-align:center;">0</span>
      </div>
      <div class="social-links">
        <a href="#" aria-label="WhatsApp">للأستفسار</a>
      </div>
    </div>
      <!-- Hamburger menu icon for mobile -->
      <div class="menu-icon" id="menu-icon" aria-label="Open menu" tabindex="0">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </nav>

   <!-- Mobile overlay menu -->
    <div class="mobile-overlay" id="mobile-overlay">
      <div class="mobile-overlay-content">
        <span class="close-overlay" id="close-overlay">&times;</span>
        <ul class="nav-links mobile-nav-links">
          <li><a href="index.php" class="nav-link">الرئيسية</a></li>
          <li><a href="store.php?category=تأسيس" class="nav-link">تأسيس</a></li>
          <li><a href="store.php?category=إكسسوار الحمام" class="nav-link">إكسسوار الحمام</a></li>
           <li class="dropdown">
            <a href="#" class="nav-link dropdown-toggle">تشطيب</a>
            <ul class="dropdown-menu">
                <li><a href="store.php?category=طقم حمام كامل" class="nav-link">طقم حمام كامل</a></li>
                <li><a href="store.php?category=وحدات حمام" class="nav-link">وحدات حمام</a></li>
                <li><a href="store.php?category=البانيو" class="nav-link">بانيوهات</a></li>
                <li><a href="store.php?category=لوازم حمام" class="nav-link">لوازم حمام</a></li>
                <li><a href="store.php?category=الدش" class="nav-link">انظمة الدش</a></li>
            </ul>
        </li>
          <li class="dropdown">
          <a href="#" class="nav-link dropdown-toggle">احواض</a>
            <ul class="dropdown-menu">
                <li><a href="store.php?category=حوض المطبخ الإستانلس" class="nav-link">احواض الاستانلس</a></li>
                <li><a href="store.php?category=احواض ديكور" class="nav-link">احواض الديكور</a></li>
            </ul>
        </li>
        <li class="dropdown">
        <a href="#" class="nav-link dropdown-toggle">خلاطات</a>
            <ul class="dropdown-menu">
                <li><a href="store.php?category=طقم خلاط كامل" class="nav-link">اطقم خلاطات</a></li>
                <li><a href="store.php?category= خلاط الحمام الديكور" class="nav-link">خلاطات حمام ديكور</a></li>
                <li><a href="store.php?category=خلاط مطبخ ديكور" class="nav-link">خلاطات مطبخ ديكور</a></li>
            </ul>
        </li>
        </ul>
        <div class="cart-icon" id="cart-icon-mobile" style="position:relative;cursor:pointer;margin:20px 0;">
          <img src="assets/cart.png" alt="cart" style="width:32px;height:32px;">
          <span class="cart-count" style="position:absolute;top:-8px;right:-8px;background:#e63946;color:#fff;border-radius:50%;padding:2px 7px;font-size:13px;min-width:20px;text-align:center;">0</span>
        </div>
        <div class="social-links mobile-social-links">
          <a href="#" aria-label="WhatsApp">للأستفسار</a>
        </div>
        <div class="footer-content">
      <div class="footer-icons">
        
        <ul>
          <li>
            <a href="#"><img src="assets/tiktok.svg" alt="tiktok" /></a>
          </li>
          <li>
            <a href="#"><img src="assets/facebook.svg" alt="facebook" /></a>
          </li>
          <li>
            <a href="#"><img src="assets/whatsapp.svg" alt="whatsapp" /></a>
          </li>
          <li style="height: 41px;display: flex;justify-content: center;align-items: center;background: #2f79c8;border-radius: 50%;width: 41px;">
            <a href="#"><img src="assets/material-symbols_call.svg" alt="call" /></a>
          </li>
        </ul>
      </div>
      
    </div>
      </div>
    </div>
  </header>
 <div class="search-container" style="margin:32px auto 0 auto;display:flex;align-items:center;gap:0;">
  <button class="search-icon" aria-label="بحث" style="background:#2f79c8;border:none;border-radius:0 8px 8px 0;padding:0 18px;height:54px;display:flex;align-items:center;justify-content:center;cursor:pointer;">
  <img class="img" src="assets/Search Icon.svg" />
    </button>  
  <input type="text" class="search-input" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="ابحث عن المنتجات" aria-label="ابحث عن المنتجات" style="width:100%;border-radius:8px 0 0 8px;padding:12px 16px;font-size:1.1rem;border:1.5px solid #d1d5db;outline:none;">
    
  </div>
  <div class="filter-container" style="">
    <form method="get" class="search-form" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
      <select id="category-select" class="category-select" name="category" style="min-width:150px;width: 29.5%;">
        <option value="">كل التصنيفات</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?php echo htmlspecialchars($cat['name']); ?>" <?php echo ($category === $cat['name']) ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($cat['name']); ?>
          </option>
        <?php endforeach; ?>
      </select>
      <div class="price-container" style="display: flex;gap:10px;align-items:center;">
      <input type="number" class="price-input" name="min_price" value="<?php echo htmlspecialchars($min_price); ?>" placeholder="أقل سعر" style="width:100px;">
      <input type="number" class="price-input" name="max_price" value="<?php echo htmlspecialchars($max_price); ?>" placeholder="أعلى سعر" style="width:100px;">
      <button type="submit" class="filter-btn" style="background:#2f79c8;color:#fff;border:none;border-radius:6px;padding:8px 16px;cursor:pointer;font-size:14px;">فلترة</button>
      </div>
      
      <select id="sort-select" class="sort-select" name="sort" ">
        <option value="">ترتيب افتراضي</option>
        <option value="price_asc" <?php echo ($sort === 'price_asc') ? 'selected' : ''; ?>>السعر: من الأقل للأعلى</option>
        <option value="price_desc" <?php echo ($sort === 'price_desc') ? 'selected' : ''; ?>>السعر: من الأعلى للأقل</option>
      </select>
  
    </form>
    
  </div>
  <main class="product-listing">
    <section class="products-section">
      <div class="products-grid" data-products-grid>
        <?php if (empty($products)): ?>
          <div style="grid-column: 1/-1; text-align: center; color: #e63946; font-size: 1.2rem;">لا توجد منتجات مطابقة</div>
        <?php else: ?>
          <?php foreach ($products as $product): ?>
            <article class="product-card" style="position:relative;">
              <div class="product-image">
                <img src="assets/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" />
              </div>
              <div class="product-details">
                <h2 class="product-title"><?php echo nl2br(htmlspecialchars($product['name'])); ?></h2>
                <div class="product-price">
                  <?php if (!empty($product['original_price'])): ?>
                    <span class="original-price">EGP<?php echo number_format($product['original_price'], 2); ?></span>
                  <?php endif; ?>
                  <?php if (!empty($product['discounted_price'])): ?>
                    <span class="discounted-price">EGP<?php echo number_format($product['discounted_price'], 2); ?></span>
                  <?php endif; ?>
                </div>
                <div class="stock">المتوفر: <?php echo (int)$product['stock']; ?></div>
              </div>
              <div class="product-actions">
                <button class="order-button" onclick="window.location.href='order.php?product_id=<?php echo $product['id']; ?>'">اطلب الأن</button>
                <button class="add-to-cart-btn" data-product='<?php echo json_encode(["id"=>$product["id"],"name"=>$product["name"],"price"=>$product["discounted_price"],"image"=>$product["image"]]); ?>'>
                    <img src="assets/cart.png" alt="plus" style="width:32px;height:32px;">
                </button>
              </div>
            </article>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
      <!-- Pagination -->
      <?php if ($total_pages > 1): ?>
      <div class="pagination" style="display:flex;gap:6px;justify-content:center;margin:2.5rem 0 1rem 0;">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
          <?php if ($i == $page): ?>
            <span style="padding:7px 16px;border-radius:6px;background:#2f79c8;color:#fff;border:1.5px solid #2f79c8;font-weight:600;cursor:default;"> <?php echo $i; ?> </span>
          <?php else: ?>
            <a href="#" class="client-page-link" data-page="<?php echo $i; ?>" style="padding:7px 16px;border-radius:6px;background:#f1f5fa;color:#2f79c8;text-decoration:none;font-weight:600;border:1.5px solid #d1d5db;transition:background 0.2s,color 0.2s;"> <?php echo $i; ?> </a>
          <?php endif; ?>
        <?php endfor; ?>
      </div>
      <?php endif; ?>
    </section>
  </main>
  <footer class="main-footer">
    <div class="footer-content">
      <div class="footer-logo">
        <img src="assets/footerlogo.png" alt="LSAB3AH Logo" class="navbar-logo" />
      </div>
      <nav class="footer-nav">
          <ul>
          <li><a href="index.php" class="footer-link">الرئيسية</a></li>
        <li><a href="store.php?category=تأسيس" class="footer-link">تأسيس</a></li>
        <li><a href="store.php?category=إكسسوار الحمام" class="footer-link">إكسسوار الحمام</a></li>
         <li class="dropdown">
            <a href="#" class="footer-link dropdown-toggle">تشطيب</a>
            <ul class="dropdown-menu">
                <li><a href="store.php?category=طقم حمام كامل" class="nav-link">طقم حمام كامل</a></li>
                <li><a href="store.php?category=وحدات حمام" class="nav-link">وحدات حمام</a></li>
                <li><a href="store.php?category=البانيو" class="nav-link">بانيوهات</a></li>
                <li><a href="store.php?category=لوازم حمام" class="nav-link">لوازم حمام</a></li>
                <li><a href="store.php?category=الدش" class="nav-link">انظمة الدش</a></li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#" class="footer-link dropdown-toggle">احواض</a>
            <ul class="dropdown-menu" style="    top: -100px;">
                <li><a href="store.php?category=حوض المطبخ الإستانلس" class="footer-link">احواض الاستانلس</a></li>
                <li><a href="store.php?category=احواض ديكور" class="footer-link">احواض الديكور</a></li>
            </ul>
        </li>
        <li class="dropdown">
        <a href="#" class="footer-link dropdown-toggle">خلاطات</a>
            <ul class="dropdown-menu">
                <li><a href="store.php?category=طقم خلاط كامل" class="footer-link">اطقم خلاطات</a></li>
                <li><a href="store.php?category= خلاط الحمام الديكور" class="footer-link">خلاطات حمام ديكور</a></li>
                <li><a href="store.php?category=خلاط مطبخ ديكور" class="footer-link">خلاطات مطبخ ديكور</a></li>
            </ul>
        </li>
          </ul>
      </nav>
    </div>
    <div class="footer-content">
      <div class="footer-icons">
        
        <ul>
          <li>
            <a href="#"><img src="assets/tiktok.svg" alt="tiktok" /></a>
          </li>
          <li>
            <a href="#"><img src="assets/facebook.svg" alt="facebook" /></a>
          </li>
          <li>
            <a href="#"><img src="assets/whatsapp.svg" alt="whatsapp" /></a>
          </li>
          <li style="height: 41px;display: flex;justify-content: center;align-items: center;background: #2f79c8;border-radius: 50%;width: 41px;">
            <a href="#"><img src="assets/material-symbols_call.svg" alt="call" /></a>
          </li>
        </ul>
      </div>
      <div class="footer-created">
        <h1>: Created by <img src="assets/mynaui_user-solid.svg" alt="user icon"> <span>Moataz Anous</span></h1>
      </div>
    </div>
  </footer>
  <script src="main.js"></script>

  <!-- Cart Modal -->
  <div id="cart-modal" class="cart-modal" style="display:none;">
    <div class="cart-modal-content">
      <span class="cart-modal-close" id="cart-modal-close">&times;</span>
      <h2 style="margin-bottom: 1rem;">سلة المشتريات</h2>
      <div id="cart-items"></div>
      <div id="cart-total" style="margin: 1rem 0; font-weight: bold;"></div>
      <button id="place-order-btn" class="place-order-btn" style="width:100%;padding:12px 0;background:var(--primary-blue);color:#fff;border:none;border-radius:8px;font-size:1.1rem;cursor:pointer;">إتمام الطلب</button>
    </div>
  </div>
    <script>
    // Store all products data for client-side pagination
    window.allProducts = <?php echo json_encode($all_products); ?>;
    window.currentPage = <?php echo $page; ?>;
    window.perPage = <?php echo $per_page; ?>;
    window.totalPages = <?php echo $total_pages; ?>;
    window.currentFilters = {
      search: '<?php echo htmlspecialchars($search); ?>',
      category: '<?php echo htmlspecialchars($category); ?>',
      min_price: '<?php echo htmlspecialchars($min_price); ?>',
      max_price: '<?php echo htmlspecialchars($max_price); ?>',
      sort: '<?php echo htmlspecialchars($sort); ?>'
    };
    
    // Fix newlines in product names for client-side pagination
    if (window.allProducts) {
      window.allProducts.forEach(product => {
        if (product.name) {
          product.name = product.name.replace(/\\n/g, '\n');
        }
      });
    }
  </script>
  <script>
    // Mobile menu logic
    document.addEventListener('DOMContentLoaded', function() {
      var menuIcon = document.getElementById('menu-icon');
      var overlay = document.getElementById('mobile-overlay');
      var closeOverlay = document.getElementById('close-overlay');
      menuIcon && menuIcon.addEventListener('click', function() {
        overlay.classList.add('active');
      });
      closeOverlay && closeOverlay.addEventListener('click', function() {
        overlay.classList.remove('active');
      });
      overlay && overlay.addEventListener('click', function(e) {
        if (e.target === overlay) overlay.classList.remove('active');
      });
      // Filter form auto-submit on change (only for dropdowns)
      var filterForm = document.querySelector('.search-form');
      if (filterForm) {
        // Auto-submit for dropdowns only
        filterForm.querySelectorAll('select').forEach(function(el) {
          el.addEventListener('change', function() {
            filterForm.submit();
          });
        });
        
        // Enter key support for price inputs
        filterForm.querySelectorAll('input[type="number"]').forEach(function(el) {
          el.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
              e.preventDefault();
              filterForm.submit();
            }
          });
        });
      }
      // Cart count logic for both desktop and mobile
      function updateCartCount() {
        var cart = JSON.parse(localStorage.getItem('elsab3a_cart') || '[]');
        var count = 0;
        cart.forEach(function(item) { count += item.quantity || 1; });
        if (!count) count = 0;
        var cartCountEls = document.querySelectorAll('.cart-count');
        cartCountEls.forEach(function(el) { el.textContent = count; });
      }
      updateCartCount();
      window.addEventListener('storage', updateCartCount);
    });
  </script>
</body>
</html> 