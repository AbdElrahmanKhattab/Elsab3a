<?php
require_once 'db.php';
// Helper: get category id by name
function get_category_id($pdo, $name) {
    $stmt = $pdo->prepare('SELECT id FROM categories WHERE name = ?');
    $stmt->execute([$name]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row['id'] : null;
}
// Fetch category IDs
$cat_offers = get_category_id($pdo, 'عروض');
$cat_shower = get_category_id($pdo, 'الدش');
$cat_bath = get_category_id($pdo, 'البانيو');
// Fetch products for each section
function get_products_by_category($pdo, $cat_id, $limit = 16) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE stock > 0 AND id IN (SELECT product_id FROM product_categories WHERE category_id = ?) ORDER BY id DESC LIMIT ?');
    $stmt->bindValue(1, $cat_id, PDO::PARAM_INT);
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
$products_offers = $cat_offers ? get_products_by_category($pdo, $cat_offers) : [];
$products_shower = $cat_shower ? get_products_by_category($pdo, $cat_shower) : [];
$products_bath = $cat_bath ? get_products_by_category($pdo, $cat_bath) : [];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- العنوان والوصف -->
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

  

  <link rel="stylesheet" href="styles/index.css">
  <link rel="stylesheet" href="styles/store.css">
  <link rel="icon" type="image/png" href="assets/pagelogo.png">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  
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
  <main>
    <section class="hero-section">
      <div class="search-container">
        <button class="search-icon" aria-label="بحث" id="home-search-btn">
          <img class="img" alt="search icon" src="assets/Search Icon.svg" />
        </button>
        <input type="text" class="home-search-input" id="home-search-input" style="width: 100%;border-radius: 8px 0 0 8px;" placeholder="ابحث عن المنتجات" aria-label="ابحث عن المنتجات" />
        
      </div>
      <!-- Swiper -->
     <div class="slider-animation">
     <div class="swiper-pagination"></div>
     <div class="swiper mySwiper">
        <div class="swiper-wrapper">
          <div class="swiper-slide"><div style="background: #5f5b53;height: 100%;"><img src="assets/slider1.png"  alt="slider1"></div></div>
          <div class="swiper-slide"><div style="background: #090909;height: 100%;"><img src="assets/slider2.png"  alt="slider2"></div></div>
          <div class="swiper-slide"><div style="background: #7884ff;height: 100%;"><img src="assets/slider3.png" alt="slider3"></div></div>
          <div class="swiper-slide"><div style="background: #373e98;height: 100%;"><img src="assets/slider4.png" alt="slider4"></div></div>
        </div>
        
      </div>
     </div>
    </section>
    <section class="catalog-section">
      <div class="catalog-container">
        <div class="catalog-grid">
            <a href="store.php?category=وحدات حمام" class="catalog-item">
                <div class="item-image">
                    <img src="assets/cataloge1.png"
                        alt="وحدات حمام" />
                </div>
                <div class="item-title">وحدات حمام</div>
            </a>

            <a href="store.php?category=طقم خلاط كامل" class="catalog-item">
                <div class="item-image">
                    <img src="assets/cataloge2.png"
                        alt="طقم خلاط كامل" />
                </div>
                <div class="item-title">طقم خلاط كامل</div>
            </a>
            
            <a href="store.php?category=تأسيس " class="catalog-item">
                <div class="item-image">
                    <img src="assets/foundation.jpg"
                        alt="تأسيس" />
                </div>
                <div class="item-title">تأسيس</div>
            </a>
            <a href="store.php?category=إكسسوار الحمام" class="catalog-item">
                <div class="item-image">
                    <img src="assets/cataloge3.png"
                        alt="إكسسوار الحمام" />
                </div>
                <div class="item-title">إكسسوار الحمام</div>
            </a>

            <a href="store.php?category= احواض ديكور" class="catalog-item">
                <div class="item-image">
                    <img src="assets/cataloge4.png"
                        alt="احواض ديكور" />
                </div>
                <div class="item-title">احواض ديكور</div>
            </a>
            <a href="store.php?category=طقم حمام كامل" class="catalog-item">
                <div class="item-image">
                    <img src="assets/cataloge9.png"
                        alt="طقم حمام كامل" />
                </div>
                <div class="item-title">طقم حمام كامل</div>
            </a>
            

            <a href="store.php?category=البانيو" class="catalog-item">
                <div class="item-image">
                    <img src="assets/cataloge6.png"
                        alt="البانيو" />
                </div>
                <div class="item-title">البانيو</div>
            </a>

            <a href="store.php?category= خلاط الحمام الديكور" class="catalog-item">
                <div class="item-image">
                    <img src="assets/cataloge7.png"
                        alt="خلاط الحمام الديكور" />
                </div>
                <div class="item-title">خلاطات حمام الديكور</div>
            </a>

            <a href="store.php?category=خلاط مطبخ ديكور" class="catalog-item">
                <div class="item-image">
                    <img src="assets/cataloge8.png"
                        alt="خلاطات مطبخ ديكور" />
                </div>
                <div class="item-title">خلاطات مطبخ ديكور</div>
            </a>

            <a href="store.php?category=حوض المطبخ الإستانلس" class="catalog-item">
                <div class="item-image">
                    <img src="assets/cataloge5.png"
                        alt=" حوض المطبخ الإستانلس" />
                </div>
                <div class="item-title">حوض المطبخ الإستانلس</div>
            </a>
            <a href="store.php?category=لوازم حمام	" class="catalog-item">
                <div class="item-image">
                    <img src="assets/photo_٢٠٢٥-٠٧-٢٩_٠١-٠٧-٣٦.jpg"
                        alt="انظمة حمام" />
                </div>
                <div class="item-title">لوازم حمام</div>
            </a>
            <a href="store.php?category=الدش" class="catalog-item">
                <div class="item-image">
                    <img src="assets/0b763a9ea83184c80ae6986ac07a12e2_9c4310c4-bd35-4a90-9ab2-644eabe08ad6.png"
                        alt="انظمة الدش" />
                </div>
                <div class="item-title">انظمة الدش</div>
            </a>
        </div>
      </div>
    </section>
    <section class="brands-section">
      <div class="brands-container">
        <div class="brands-header">
          <h2>اختار من أشهر الماركات بأفضل الأسعار في السوق المصري</h2>
          <p>جودة مضمونة من براندات موثوق</p>
        </div>
        <div class="brands-grid">
          <div class="brand-card">
            <img src="assets/grohe.png" alt="GROHE">
            <div class="brand-name">GROHE</div>
          </div>
          <div class="brand-card">
            <img src="assets/Ideal Standard.png" alt="Ideal Standard">
            <div class="brand-name">Ideal Standard</div>
          </div>
          <div class="brand-card">
            <img src="assets/el-shreif.jpg" alt="El-Shreif">
            <div class="brand-name">El-Shreif</div>
          </div>
          <div class="brand-card">
            <img src="assets/egic.jpg" alt="EGIC">
            <div class="brand-name">EGIC</div>
          </div>
          <div class="brand-card">
            <img src="assets/duravit.png" alt="Duravit">
            <div class="brand-name">Duravit</div>
          </div>
          <div class="brand-card">
            <img src="assets/Sani Pure.png" alt="Sani Pure">
            <div class="brand-name">Sani Pure</div>
          </div>
          <div class="brand-card">
            <img src="assets/el-tib.png" alt="El‑Tib">
            <div class="brand-name">El‑Tib</div>
          </div>
          <div class="brand-card">
            <img src="assets/sobek.png" alt="Sobek">
            <div class="brand-name">Sobek</div>
          </div>
          <div class="brand-card">
            <img src="assets/Global Standard.png" alt="Global Standard">
            <div class="brand-name">Global Standard</div>
          </div>
          <div class="brand-card">
            <img src="assets/haway.png" alt="haway">
            <div class="brand-name">haway</div>
          </div>
        </div>
      </div>
    </section>
   
    <section class="products-section prdouct-offer-section">
      <div class="offer-header">
        <h2 class="offer-header-text">عروضنا ما تتفوتش!</h2>
      </div>
      <div class="products-grid" style="padding-top: 70px;">
        <?php foreach ($products_offers as $product): ?>
        <article class="product-card">
          <img src="assets/logo.jpg" alt="logo" class="product-logo" />
          <div class="product-image">
            <img src="assets/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" />
          </div>
          <div class="product-details">
            <h2 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h2>
            <div class="product-price">
              <?php if (!empty($product['original_price'])): ?>
                <span class="original-price">EGP<?php echo number_format($product['original_price'], 2); ?></span>
              <?php endif; ?>
              <?php if (!empty($product['discounted_price'])): ?>
                <span class="discounted-price">EGP<?php echo number_format($product['discounted_price'], 2); ?></span>
              <?php endif; ?>
            </div>
          </div>
          <div class="product-actions">
                <button class="order-button" onclick="window.location.href='order.php?product_id=<?php echo $product['id']; ?>'">اطلب الأن</button>
                <button class="add-to-cart-btn" data-product='<?php echo json_encode(["id"=>$product["id"],"name"=>$product["name"],"price"=>$product["discounted_price"],"image"=>$product["image"]]); ?>'>
                    <img src="assets/cart.png" alt="plus" style="width:32px;height:32px;">
                </button>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </section>
    <section class="products-section product-shower-section">
      <div class="offer-header product-lines" style="position: relative;">
        <img src="assets/fluent_showerhead-32-filled.svg" alt="shower icon">
      </div>
      <div class="products-grid" style="padding-top: 70px;">
        <?php foreach ($products_shower as $product): ?>
        <article class="product-card">
          <img src="assets/logo.jpg" alt="logo" class="product-logo" />
          <div class="product-image">
            <img src="assets/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" />
          </div>
          <div class="product-details">
            <h2 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h2>
            <div class="product-price">
              <?php if (!empty($product['original_price'])): ?>
                <span class="original-price">EGP<?php echo number_format($product['original_price'], 2); ?></span>
              <?php endif; ?>
              <?php if (!empty($product['discounted_price'])): ?>
                <span class="discounted-price">EGP<?php echo number_format($product['discounted_price'], 2); ?></span>
              <?php endif; ?>
            </div>
          </div>
          <div class="product-actions">
                <button class="order-button" onclick="window.location.href='order.php?product_id=<?php echo $product['id']; ?>'">اطلب الأن</button>
                <button class="add-to-cart-btn" data-product='<?php echo json_encode(["id"=>$product["id"],"name"=>$product["name"],"price"=>$product["discounted_price"],"image"=>$product["image"]]); ?>'>
                    <img src="assets/cart.png" alt="plus" style="width:32px;height:32px;">
                </button>
            </div>
        </article>
        <?php endforeach; ?>
      </div>
    </section>
    <section class="products-section product-bath-section">
      <div class="offer-header product-lines" style="position: relative;">
        <img src="assets/solar_bath-bold.svg" alt="bath icon">
      </div>
      <div class="products-grid" style="padding-top: 70px;">
        <?php foreach ($products_bath as $product): ?>
        <article class="product-card">
          <img src="assets/logo.jpg" alt="logo" class="product-logo" />
          <div class="product-image">
            <img src="assets/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" />
          </div>
          <div class="product-details">
            <h2 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h2>
            <div class="product-price">
              <?php if (!empty($product['original_price'])): ?>
                <span class="original-price">EGP<?php echo number_format($product['original_price'], 2); ?></span>
              <?php endif; ?>
              <?php if (!empty($product['discounted_price'])): ?>
                <span class="discounted-price">EGP<?php echo number_format($product['discounted_price'], 2); ?></span>
              <?php endif; ?>
            </div>
          </div>
          <div class="product-actions">
                <button class="order-button" onclick="window.location.href='order.php?product_id=<?php echo $product['id']; ?>'">اطلب الأن</button>
                <button class="add-to-cart-btn" data-product='<?php echo json_encode(["id"=>$product["id"],"name"=>$product["name"],"price"=>$product["discounted_price"],"image"=>$product["image"]]); ?>'>
                    <img src="assets/cart.png" alt="plus" style="width:32px;height:32px;">
                </button>
            </div>
        </article>
        <?php endforeach; ?>
      </div>
    </section>
    <section class="feature-section">
      <div class="feature-container">
        <div class="feature-grid">
          <div class="feature-card">
            <div class="icon-circle">
              <img src="assets/mdi_encryption-secure.svg" alt="Secure">
            </div>
            <h1>خيارات دفع آمنة وسهلة</h1>
            <p>نقدًا أو إلكترونيًا، اختار وسيلة الدفع المناسبة ليك بكل أمان</p>
          </div>
          <div class="feature-card">
            <div class="icon-circle">
              <img src="assets/bxs_offer.svg" alt="Offer">
            </div>
            <h1>خصومات مستمرة</h1>
            <p>استمتع بأفضل الأسعار والعروض الحصرية طول السنة</p>
          </div>
          <div class="feature-card">
            <div class="icon-circle">
              <img src="assets/mynaui_check-waves-solid.svg" alt="circle icon">
            </div>
            <h1>جودة تستحق الثقة</h1>
            <p>استمتع بأفضل الخامات والتصنيع الدقيق لمنتجات تدوم معك</p>
          </div>
          <div class="feature-card">
            <div class="icon-circle">
              <img src="assets/game-icons_egypt.svg" alt="Egypt">
            </div>
            <h1>توصيل سريع في كل مكان</h1>
            <p>بنوصلك لحد باب البيت في أي مكان في مصر خلال 1 إلى 3 أيام عمل كحد أقصى</p>
          </div>
        </div>
      </div>
    </section>
  </main>
  <footer class="main-footer" id="footer">
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
            <a href="#footer" class="footer-link dropdown-toggle">احواض</a>
            <ul class="dropdown-menu" style="top: -100px;">
                <li><a href="store.php?category=حوض المطبخ الإستانلس" class="footer-link">احواض الاستانلس</a></li>
                <li><a href="store.php?category=احواض ديكور" class="footer-link">احواض الديكور</a></li>
            </ul>
        </li>
        <li class="dropdown">
        <a href="#footer" class="footer-link dropdown-toggle">خلاطات</a>
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
  <div id="cart-modal" class="cart-modal" style="display:none;">
    <div class="cart-modal-content">
      <span class="cart-modal-close" id="cart-modal-close">&times;</span>
      <h2 style="margin-bottom: 1rem;">سلة المشتريات</h2>
      <div id="cart-items"></div>
      <div id="cart-total" style="margin: 1rem 0; font-weight: bold;"></div>
      <button id="place-order-btn" class="place-order-btn" style="width:100%;padding:12px 0;background:var(--primary-blue);color:#fff;border:none;border-radius:8px;font-size:1.1rem;cursor:pointer;">إتمام الطلب</button>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script src="main.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Mobile menu logic
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
      // Home search bar redirect
      var homeSearchInput = document.getElementById('home-search-input');
      var homeSearchBtn = document.getElementById('home-search-btn');
      function goToStoreSearch() {
        var q = homeSearchInput.value.trim();
        if (q) {
          window.location.href = 'store.php?search=' + encodeURIComponent(q);
        }
      }
      if (homeSearchBtn && homeSearchInput) {
        homeSearchBtn.addEventListener('click', function(e) {
          e.preventDefault();
          goToStoreSearch();
        });
        homeSearchInput.addEventListener('keydown', function(e) {
          if (e.key === 'Enter') {
            e.preventDefault();
            goToStoreSearch();
          }
        });
      }

    });
  </script>
</body>
</html> 