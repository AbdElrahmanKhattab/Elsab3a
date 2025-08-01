if (typeof Swiper !== "undefined" && document.querySelector(".mySwiper")) {
    var swiper = new Swiper(".mySwiper", {
        slidesPerView: 1,
        spaceBetween: 30,
        loop: true,
        autoplay: {
            delay: 4000,
            disableOnInteraction: false,
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        on: {
            init: function () {
                updateBackgroundColor();
                resetImageAnimation(); // كمان نعيد الأنيميشن عند أول تحميل
            },
            slideChange: function () {
                setTimeout(() => {
                    updateBackgroundColor();
                    resetImageAnimation(); // إعادة تشغيل الأنيميشن مع كل سلايد
                }, 10);
            }
        }
    });

    function updateBackgroundColor() {
        const activeSlide = document.querySelector(".swiper-slide-active > div");
        if (activeSlide) {
            const bgColor = window.getComputedStyle(activeSlide).backgroundColor;
            document.querySelector(".slider-animation").style.backgroundColor = bgColor;
        }
    }

    function resetImageAnimation() {
        const activeImg = document.querySelector(".swiper-slide-active img");
        if (activeImg) {
            // وقف الأنيميشن
            activeImg.style.animation = "none";
            // إعادة تفعيل الـ animation (reflow)
            void activeImg.offsetWidth;
            // شغل الأنيميشن تاني
            activeImg.style.animation = "scaleUpDown 5000ms infinite ease-in-out";
        }
    }
}




// Cart logic using localStorage
const CART_KEY = "elsab3a_cart";

function getCart() {
    return JSON.parse(localStorage.getItem(CART_KEY) || "[]");
}
function saveCart(cart) {
    localStorage.setItem(CART_KEY, JSON.stringify(cart));
}
function addToCart(product) {
    let cart = getCart();
    const idx = cart.findIndex((item) => item.id === product.id);
    if (idx > -1) {
        cart[idx].quantity += 1;
    } else {
        cart.push({ ...product, quantity: 1 });
    }
    saveCart(cart);
    updateCartCount();
}
function removeFromCart(productId) {
    let cart = getCart();
    cart = cart.filter((item) => item.id !== productId);
    saveCart(cart);
    updateCartCount();
}
function updateCartQuantity(productId, qty) {
    let cart = getCart();
    const idx = cart.findIndex((item) => item.id === productId);
    if (idx > -1) {
        cart[idx].quantity = qty;
        if (cart[idx].quantity < 1) cart.splice(idx, 1);
        saveCart(cart);
        updateCartCount();
    }
}
function clearCart() {
    localStorage.removeItem(CART_KEY);
    updateCartCount();
}
function updateCartCount() {
    const cart = getCart();
    const count = cart.reduce((sum, item) => sum + item.quantity, 0);
    const cartCounts = document.querySelectorAll(".cart-count");
    cartCounts.forEach(cartCount => {
        cartCount.textContent = count > 0 ? count : "0";
    });
}

// Render products dynamically
function renderProducts(products) {
    const grid = document.querySelector(".products-grid");
    if (!grid) return;
    if (!products.length) {
        grid.innerHTML =
            '<div style="grid-column: 1/-1; text-align: center; color: #e63946; font-size: 1.2rem;">لا توجد منتجات مطابقة</div>';
        return;
    }
    grid.innerHTML = products
        .map(
            (product) => `
        <article class="product-card" style="position:relative;">
            <div class="product-image">
                <img src="assets/${product.image}" alt="${product.name}" />
            </div>
            <div class="product-details">
                <h2 class="product-title">${product.name}</h2>
                <div class="product-price">
                    ${product.original_price
                        ? `<span class="original-price">EGP${Number(
                              product.original_price
                          ).toLocaleString()}</span>`
                        : ""}
                    ${product.discounted_price
                        ? `<span class="discounted-price">EGP${Number(
                              product.discounted_price
                          ).toLocaleString()}</span>`
                        : ""}
                </div>
                <div class="stock">المتوفر: ${product.stock}</div>
                
            </div>
            <div class="product-actions">
                <button class="order-button">اطلب الأن</button>
                <button class="add-to-cart-btn" data-product='${JSON.stringify({
                    id: product.id,
                    name: product.name,
                    price: product.discounted_price, // always use discounted_price
                    image: product.image
                })}'>
                    <img src="assets/cart.png" alt="plus" style="width:32px;height:32px;">
                </button>
            </div>
        </article>
    `
        )
        .join("");
    // Initialize cart buttons for dynamically rendered products
    initializeCartButtons();
}

// Fetch products from backend with filters
function fetchAndRenderProducts() {
    const searchInput = document.querySelector(".search-input");
    const categorySelect = document.getElementById("category-select");
    const minPriceInput = document.querySelector('input[name="min_price"]');
    const maxPriceInput = document.querySelector('input[name="max_price"]');
    const sortSelect = document.getElementById("sort-select");
    
    const search = searchInput?.value || "";
    const category = categorySelect?.value || "";
    const min_price = minPriceInput?.value || "";
    const max_price = maxPriceInput?.value || "";
    const sort = sortSelect?.value || "";
    
    const params = new URLSearchParams({
        search,
        category,
        min_price,
        max_price,
        sort
    });
    fetch("get_products.php?" + params.toString())
        .then((res) => res.json())
        .then((data) => {
            if (data.success) {
                renderProducts(data.products);
            }
        });
}

// Cart Modal Logic
function showCartModal() {
    const modal = document.getElementById('cart-modal');
    const itemsDiv = document.getElementById('cart-items');
    const totalDiv = document.getElementById('cart-total');
    const cart = getCart();
    if (!modal || !itemsDiv || !totalDiv) return;
    if (cart.length === 0) {
        itemsDiv.innerHTML = '<div style="text-align:center;color:#888;">سلة المشتريات فارغة</div>';
        totalDiv.textContent = '';
        document.getElementById('place-order-btn').disabled = true;
    } else {
        itemsDiv.innerHTML = cart.map(item => `
            <div class="cart-item">
                <img src="assets/${item.image}" class="cart-item-img" alt="${item.name}">
                <div class="cart-item-details">
                    <div class="cart-item-title">${item.name}</div>
                    <div class="cart-item-qty">الكمية: <input type="number" min="1" max="99" value="${item.quantity}" data-cart-id="${item.id}" class="cart-qty-input" style="width:40px;text-align:center;border:1px solid #ccc;border-radius:4px;"> </div>
                    <div class="cart-item-price">EGP${Number(item.price).toLocaleString()}</div>
                </div>
                <button class="cart-item-remove" data-remove-id="${item.id}" title="حذف">&times;</button>
            </div>
        `).join('');
        const total = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
        totalDiv.textContent = `الإجمالي: EGP${total.toLocaleString()}`;
        document.getElementById('place-order-btn').disabled = false;
    }
    modal.style.display = 'flex';
}

function hideCartModal() {
    const modal = document.getElementById('cart-modal');
    if (modal) modal.style.display = 'none';
}

// AJAX pagination for store.php
function setupAjaxPagination() {
  const gridContainer = document.querySelector('[data-products-grid]')?.parentNode;
  if (!gridContainer) return;

  function showGridLoading() {
    let loader = document.createElement('div');
    loader.className = 'products-grid-loader';
    loader.style.position = 'absolute';
    loader.style.top = 0;
    loader.style.left = 0;
    loader.style.width = '100%';
    loader.style.height = '100%';
    loader.style.background = 'rgba(255,255,255,0.7)';
    loader.style.display = 'flex';
    loader.style.alignItems = 'center';
    loader.style.justifyContent = 'center';
    loader.style.zIndex = 10;
    loader.innerHTML = '<div class="loaderClass"><img src="assets/pagelogo.png" alt="Loading..." style="width:60px;height:60px;" /></div>';
    loader.setAttribute('data-grid-loader', '');
    gridContainer.style.position = 'relative';
    gridContainer.appendChild(loader);
  }
  function hideGridLoading() {
    let loader = gridContainer.querySelector('[data-grid-loader]');
    if (loader) loader.remove();
  }

  function ajaxifyLinks() {
    document.querySelectorAll('.ajax-page-link').forEach(function(link) {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        const url = new URL(link.href, window.location.origin);
        showGridLoading();
        fetch('store_products_ajax.php' + url.search)
          .then(res => res.text())
          .then(html => {
            // Replace products grid and pagination
            const temp = document.createElement('div');
            temp.innerHTML = html;
            const newGrid = temp.querySelector('[data-products-grid]');
            if (newGrid) {
              gridContainer.innerHTML = '';
              gridContainer.appendChild(newGrid);
              // Also append pagination if present
              const newPagination = temp.querySelector('.pagination');
              if (newPagination) gridContainer.appendChild(newPagination);
            }
            hideGridLoading();
            ajaxifyLinks(); // Re-attach events
            window.scrollTo({top: gridContainer.offsetTop - 40, behavior: 'smooth'});
          })
          .catch(() => hideGridLoading());
      });
    });
  }
  ajaxifyLinks();
}

document.addEventListener('DOMContentLoaded', function() {
  if (document.querySelector('[data-products-grid]')) {
    setupAjaxPagination();
  }
});

// Initialize cart buttons functionality
function initializeCartButtons() {
  // Remove existing event listeners first to prevent duplicates
  document.querySelectorAll(".add-to-cart-btn").forEach((btn) => {
    btn.replaceWith(btn.cloneNode(true));
  });
  
  document.querySelectorAll('.order-button').forEach((btn) => {
    btn.replaceWith(btn.cloneNode(true));
  });

  // Add to cart buttons
  document.querySelectorAll(".add-to-cart-btn").forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      const product = JSON.parse(btn.dataset.product);
      addToCart(product);
    });
  });

  // Order buttons
  document.querySelectorAll('.order-button').forEach((btn) => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      const productCard = btn.closest('.product-card');
      const addToCartBtn = productCard.querySelector('.add-to-cart-btn');
      if (addToCartBtn) {
        const product = JSON.parse(addToCartBtn.dataset.product);
        window.location.href = `order.php?product_id=${product.id}`;
      }
    });
  });
}

// Client-side pagination for store.php
function setupClientSidePagination() {
  if (!window.allProducts) return;

  function renderProducts(products, page) {
    const grid = document.querySelector('[data-products-grid]');
    if (!grid) return;

    if (products.length === 0) {
      grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: #e63946; font-size: 1.2rem;">لا توجد منتجات مطابقة</div>';
      return;
    }

    let html = '';
    products.forEach(product => {
      html += `
        <article class="product-card" style="position:relative;">
          <div class="product-image">
            <img src="assets/${product.image}" alt="${product.name}" />
          </div>
          <div class="product-details">
            <h2 class="product-title">${product.name.replace(/\n/g, '<br>')}</h2>
            <div class="product-price">
              ${product.original_price ? `<span class="original-price">EGP${parseFloat(product.original_price).toFixed(2)}</span>` : ''}
              ${product.discounted_price ? `<span class="discounted-price">EGP${parseFloat(product.discounted_price).toFixed(2)}</span>` : ''}
            </div>
            <div class="stock">المتوفر: ${parseInt(product.stock)}</div>
          </div>
          <div class="product-actions">
            <button class="order-button" onclick="window.location.href='order.php?product_id=${product.id}'">اطلب الأن</button>
            <button class="add-to-cart-btn" data-product='${JSON.stringify({
              id: product.id,
              name: product.name,
              price: product.discounted_price,
              image: product.image
            })}'>
                <img src="assets/cart.png" alt="plus" style="width:32px;height:32px;">
            </button>
          </div>
        </article>
      `;
    });
    grid.innerHTML = html;
    
    // Re-initialize cart functionality for new products
    initializeCartButtons();
  }

  function renderPagination(currentPage, totalPages) {
    const paginationContainer = document.querySelector('.pagination');
    if (!paginationContainer || totalPages <= 1) return;

    let html = '';
    for (let i = 1; i <= totalPages; i++) {
      if (i === currentPage) {
        html += `<span style="padding:7px 16px;border-radius:6px;background:#2f79c8;color:#fff;border:1.5px solid #2f79c8;font-weight:600;cursor:default;"> ${i} </span>`;
      } else {
        html += `<a href="#" class="client-page-link" data-page="${i}" style="padding:7px 16px;border-radius:6px;background:#f1f5fa;color:#2f79c8;text-decoration:none;font-weight:600;border:1.5px solid #d1d5db;transition:background 0.2s,color 0.2s;"> ${i} </a>`;
      }
    }
    paginationContainer.innerHTML = html;
    
    // Re-attach event listeners to new pagination links
    attachPaginationEvents();
  }

  function goToPage(page) {
    const startIndex = (page - 1) * window.perPage;
    const endIndex = startIndex + window.perPage;
    const pageProducts = window.allProducts.slice(startIndex, endIndex);
    
    renderProducts(pageProducts, page);
    renderPagination(page, window.totalPages);
    window.currentPage = page;
    
    // Scroll to top of products section
    const productsSection = document.querySelector('.products-section');
    if (productsSection) {
      productsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }

  // Attach event listeners to pagination links
  function attachPaginationEvents() {
    document.querySelectorAll('.client-page-link').forEach(link => {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        const page = parseInt(this.getAttribute('data-page'));
        goToPage(page);
      });
    });
  }

  // Initial setup
  attachPaginationEvents();
}

document.addEventListener('DOMContentLoaded', function() {
  // Fix newlines in product names before setting up pagination
  if (window.allProducts) {
    window.allProducts.forEach(product => {
      if (product.name) {
        // Convert escaped newlines to actual newlines
        product.name = product.name.replace(/\\n/g, '\n');
      }
    });
    setupClientSidePagination();
  } else {
    // Only initialize cart buttons if we're NOT using client-side pagination
    initializeCartButtons();
  }
});


document.addEventListener("DOMContentLoaded", function () {
    // Initialize dropdowns with a slight delay to ensure all scripts are loaded
    setTimeout(() => {
        initDropdowns();
    }, 100);
    
    // Initialize cart functionality
    updateCartCount();
    
    // Initialize cart buttons for statically rendered products
    initializeCartButtons();
    
    // Only run fetchAndRenderProducts if we're on a page with product filtering
    // ENABLE for store.php to allow live AJAX search/filtering
    const hasProductFilters = document.querySelector(".search-input") || document.getElementById("category-select");
    if (hasProductFilters) {
        // Live search and filter - only attach if elements exist
        const searchInput = document.querySelector(".search-input");
        if (searchInput) {
            searchInput.addEventListener("input", function() {
                fetchAndRenderProducts();
                // Hide PHP pagination
                const pagination = document.querySelector('.pagination');
                if (pagination) pagination.style.display = 'none';
            });
        }
        const categorySelect = document.getElementById("category-select");
        if (categorySelect) {
            categorySelect.addEventListener("change", function() {
                fetchAndRenderProducts();
                const pagination = document.querySelector('.pagination');
                if (pagination) pagination.style.display = 'none';
            });
        }
        const minPriceInput = document.querySelector('input[name="min_price"]');
        if (minPriceInput) {
            minPriceInput.addEventListener("input", function() {
                fetchAndRenderProducts();
                const pagination = document.querySelector('.pagination');
                if (pagination) pagination.style.display = 'none';
            });
        }
        const maxPriceInput = document.querySelector('input[name="max_price"]');
        if (maxPriceInput) {
            maxPriceInput.addEventListener("input", function() {
                fetchAndRenderProducts();
                const pagination = document.querySelector('.pagination');
                if (pagination) pagination.style.display = 'none';
            });
        }
        const sortSelect = document.getElementById("sort-select");
        if (sortSelect) {
            sortSelect.addEventListener("change", function() {
                fetchAndRenderProducts();
                const pagination = document.querySelector('.pagination');
                if (pagination) pagination.style.display = 'none';
            });
        }
    }
    
    // Cart icon
    const cartIcons = [
        document.getElementById("cart-icon"),
        document.getElementById("cart-icon-mobile")
    ];
    cartIcons.forEach(function(icon) {
        if (icon) icon.addEventListener("click", showCartModal);
    });
    
    // Close modal
    const modalClose = document.getElementById('cart-modal-close');
    if (modalClose) {
        modalClose.addEventListener('click', hideCartModal);
    }
    
    // Close modal on outside click
    const cartModal = document.getElementById('cart-modal');
    if (cartModal) {
        cartModal.addEventListener("click", function(e) {
            if (e.target === this) hideCartModal();
        });
    }
    
    // Remove item from cart
    document.body.addEventListener('click', function(e) {
        if (e.target.classList.contains('cart-item-remove')) {
            const id = e.target.getAttribute('data-remove-id');
            let cart = getCart();
            cart = cart.filter(item => String(item.id) !== String(id));
            saveCart(cart);
            showCartModal();
        }
    });
    // Update quantity in cart
    document.body.addEventListener('input', function(e) {
        if (e.target.classList.contains('cart-qty-input')) {
            const id = e.target.getAttribute('data-cart-id');
            let cart = getCart();
            const idx = cart.findIndex(item => String(item.id) === String(id));
            if (idx > -1) {
                let qty = parseInt(e.target.value);
                if (isNaN(qty) || qty < 1) qty = 1;
                cart[idx].quantity = qty;
                saveCart(cart);
                showCartModal();
            }
        }
    });
    // Place order button
    document.getElementById('place-order-btn').addEventListener('click', function() {
        window.location.href = 'order.php?cart=1';
    });
});
//////

/// Get the navigation element
const nav = document.querySelector('.main-header'); // Change selector to match your nav element

// Add smooth transition to the nav element
nav.style.transition = 'all 0.3s ease-in-out';

// Function to handle scroll event
function handleScroll() {
    // Get current scroll position
    const scrollY = window.scrollY || window.pageYOffset;
    
    // Check if scrolled past 150px
    if (scrollY >= 150) {
        // Add fixed positioning
        nav.style.position = 'fixed';
        nav.style.top = '0';
        nav.style.left = '0';
        nav.style.right = '0';
        nav.style.zIndex = '1000'; // Ensure it stays on top
        nav.style.transform = 'translateY(0)'; // Smooth slide in
        
        // Optional: Add a class for additional styling
        nav.classList.add('nav-fixed');
    } else {
        // Remove fixed positioning with smooth transition
        nav.style.position = '';
        nav.style.top = '';
        nav.style.left = '';
        nav.style.right = '';
        nav.style.zIndex = '';
        nav.style.transform = '';
        
        // Optional: Remove the class
        nav.classList.remove('nav-fixed');
    }
}

// Add scroll event listener
window.addEventListener('scroll', handleScroll);

// Optional: Call once on page load to check initial position
handleScroll();

// Dropdown functionality
function initDropdowns() {
    const dropdowns = document.querySelectorAll('.dropdown');
    
    dropdowns.forEach(dropdown => {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        
        if (toggle) {
            // Remove any existing event listeners to prevent conflicts
            const newToggle = toggle.cloneNode(true);
            toggle.parentNode.replaceChild(newToggle, toggle);
            
            // Handle click events
            newToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Close all other dropdowns
                dropdowns.forEach(otherDropdown => {
                    if (otherDropdown !== dropdown) {
                        otherDropdown.classList.remove('open');
                    }
                });
                
                // Toggle current dropdown
                dropdown.classList.toggle('open');
                
                // Debug log for mobile
                if (window.innerWidth <= 990) {
                    console.log('Mobile dropdown clicked, open state:', dropdown.classList.contains('open'));
                }
            });
            
            // Handle hover events for desktop only (not mobile)
            dropdown.addEventListener('mouseenter', function() {
                if (window.innerWidth > 990) { // Only on desktop
                    // Close all other dropdowns first
                    dropdowns.forEach(otherDropdown => {
                        if (otherDropdown !== dropdown) {
                            otherDropdown.classList.remove('open');
                        }
                    });
                    // Open current dropdown
                    dropdown.classList.add('open');
                }
            });
            
            dropdown.addEventListener('mouseleave', function() {
                if (window.innerWidth > 990) { // Only on desktop
                    // Add a small delay to allow moving mouse to dropdown menu
                    setTimeout(() => {
                        // Check if mouse is still over the dropdown or its menu
                        const isMouseOver = dropdown.matches(':hover') || dropdown.querySelector('.dropdown-menu').matches(':hover');
                        if (!isMouseOver) {
                            dropdown.classList.remove('open');
                        }
                    }, 100);
                }
            });
            
            // Handle dropdown menu hover to keep it open
            const dropdownMenu = dropdown.querySelector('.dropdown-menu');
            if (dropdownMenu) {
                dropdownMenu.addEventListener('mouseenter', function() {
                    if (window.innerWidth > 990) {
                        dropdown.classList.add('open');
                    }
                });
                
                dropdownMenu.addEventListener('mouseleave', function() {
                    if (window.innerWidth > 990) {
                        dropdown.classList.remove('open');
                    }
                });
            }
        }
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            dropdowns.forEach(dropdown => {
                dropdown.classList.remove('open');
            });
        }
    });
    
    // Close dropdowns on window resize (mobile/desktop switch)
    window.addEventListener('resize', function() {
        if (window.innerWidth <= 990) {
            dropdowns.forEach(dropdown => {
                dropdown.classList.remove('open');
            });
        }
    });
    
    // Close mobile overlay dropdowns when overlay is closed
    const closeOverlay = document.getElementById('close-overlay');
    const mobileOverlay = document.getElementById('mobile-overlay');
    
    if (closeOverlay) {
        closeOverlay.addEventListener('click', function() {
            dropdowns.forEach(dropdown => {
                dropdown.classList.remove('open');
            });
        });
    }
    
    if (mobileOverlay) {
        mobileOverlay.addEventListener('click', function(e) {
            if (e.target === mobileOverlay) {
                dropdowns.forEach(dropdown => {
                    dropdown.classList.remove('open');
                });
            }
        });
    }
}



// Loading screen functionality
function hideLoadingScreen() {
    const loadingScreen = document.getElementById('loading-screen');
    if (loadingScreen) {
        loadingScreen.style.opacity = '0';
        loadingScreen.style.transition = 'opacity 0.5s ease-out';
        setTimeout(() => {
            loadingScreen.style.display = 'none';
        }, 500);
    }
}

function checkAllImagesLoaded() {
    const images = document.querySelectorAll('img');
    const promises = [];

    images.forEach(img => {
        if (img.complete) {
            // Image is already loaded
            return;
        }
        
        const promise = new Promise((resolve, reject) => {
            img.onload = resolve;
            img.onerror = resolve; // Resolve even on error to not block loading
        });
        promises.push(promise);
    });

    return Promise.all(promises);
}

function waitForAllContent() {
    // Wait for DOM to be ready first
    if (document.readyState === 'loading') {
        return new Promise(resolve => {
            document.addEventListener('DOMContentLoaded', resolve);
        });
    } else {
        return Promise.resolve();
    }
}

async function initializeLoadingScreen() {
    try {
        // Wait for DOM to be ready
        await waitForAllContent();
        
        // Wait for all images to load
        await checkAllImagesLoaded();
        
        // Wait for the window load event (all resources including CSS, JS, etc.)
        if (document.readyState !== 'complete') {
            await new Promise(resolve => {
                window.addEventListener('load', resolve);
            });
        }
        
        // Additional small delay for smooth transition
        setTimeout(() => {
            hideLoadingScreen();
        }, 500);
        
    } catch (error) {
        console.warn('Error waiting for content to load:', error);
        // Hide loading screen anyway after a delay
        setTimeout(() => {
            hideLoadingScreen();
        }, 1000);
    }
}

// Initialize the loading screen handler
initializeLoadingScreen();

// Fallback: hide loading screen after 5 seconds maximum
setTimeout(() => {
    hideLoadingScreen();
}, 5000);