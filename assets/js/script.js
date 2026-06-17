/* ========================================
   NC Traders - Main JavaScript
   ======================================== */

document.addEventListener('DOMContentLoaded', function() {
  initializeApp();
});

// Initialize the application
function initializeApp() {
  setupNavigation();
  setupCart();
  setupProductFilters();
  setupFormValidation();
  setupSearch();
  setupModals();
}

/* ========================================
   Navigation Functions
   ======================================== */

function setupNavigation() {
  const hamburger = document.querySelector('.hamburger');
  const navMenu = document.querySelector('.nav-menu');

  if (hamburger) {
    hamburger.addEventListener('click', function() {
      navMenu.classList.toggle('active');
    });
  }

  // Close menu when a link is clicked
  const navLinks = document.querySelectorAll('.nav-menu a');
  navLinks.forEach(link => {
    link.addEventListener('click', function() {
      navMenu.classList.remove('active');
    });
  });

  // Close menu when clicking outside
  document.addEventListener('click', function(event) {
    if (navMenu && !event.target.closest('nav')) {
      navMenu.classList.remove('active');
    }
  });
}

/* ========================================
   Cart Functions
   ======================================== */

function setupCart() {
  const addToCartButtons = document.querySelectorAll('.btn-add-to-cart');
  
  addToCartButtons.forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      addToCart(this);
    });
  });

  // Update cart summary
  updateCartSummary();
}

function addToCart(button) {
  const productId = button.getAttribute('data-product-id');
  const productName = button.getAttribute('data-product-name');
  const productPrice = parseFloat(button.getAttribute('data-product-price'));
  const quantity = parseInt(button.getAttribute('data-quantity') || 1);

  // Get cart from localStorage
  let cart = getCart();

  // Check if product already exists in cart
  const existingItem = cart.find(item => item.id === productId);

  if (existingItem) {
    existingItem.quantity += quantity;
  } else {
    cart.push({
      id: productId,
      name: productName,
      price: productPrice,
      quantity: quantity,
      image: button.getAttribute('data-product-image') || ''
    });
  }

  // Save cart to localStorage
  saveCart(cart);

  // Show notification
  showNotification('Product added to cart!', 'success');

  // Update cart count
  updateCartCount();
}

function removeFromCart(productId) {
  let cart = getCart();
  cart = cart.filter(item => item.id !== productId);
  saveCart(cart);
  updateCartSummary();
  updateCartCount();
  showNotification('Product removed from cart', 'info');
}

function updateCartQuantity(productId, newQuantity) {
  if (newQuantity <= 0) {
    removeFromCart(productId);
    return;
  }

  let cart = getCart();
  const item = cart.find(item => item.id === productId);

  if (item) {
    item.quantity = newQuantity;
    saveCart(cart);
    updateCartSummary();
  }
}

function getCart() {
  const cart = localStorage.getItem('nctraders_cart');
  return cart ? JSON.parse(cart) : [];
}

function saveCart(cart) {
  localStorage.setItem('nctraders_cart', JSON.stringify(cart));
}

function updateCartCount() {
  const cartCountElements = document.querySelectorAll('.cart-count');
  if (!cartCountElements.length) return;

  const hasServerCount = Array.from(cartCountElements).some(el => el.dataset.serverCount !== undefined);
  if (hasServerCount) return;

  const cart = getCart();
  const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);

  cartCountElements.forEach(element => {
    element.textContent = totalItems;
    element.style.display = totalItems > 0 ? 'inline' : 'none';
  });
}

function updateCartSummary() {
  const cart = getCart();
  const cartItemsContainer = document.querySelector('.cart-items-list');
  const subtotalElement = document.querySelector('.subtotal-value');
  const totalElement = document.querySelector('.total-value');

  if (!cartItemsContainer) return;

  // Clear container
  cartItemsContainer.innerHTML = '';

  if (cart.length === 0) {
    cartItemsContainer.innerHTML = '<p class="text-center">Your cart is empty</p>';
    if (subtotalElement) subtotalElement.textContent = 'R0.00';
    if (totalElement) totalElement.textContent = 'R0.00';
    return;
  }

  let subtotal = 0;

  cart.forEach(item => {
    const itemTotal = item.price * item.quantity;
    subtotal += itemTotal;

    const cartItem = document.createElement('div');
    cartItem.className = 'cart-item';
    cartItem.innerHTML = `
      <div class="cart-item-info">
        <h4>${item.name}</h4>
        <p>Price: R${item.price.toFixed(2)}</p>
      </div>
      <div class="cart-item-quantity">
        <button class="qty-btn" onclick="updateCartQuantity('${item.id}', ${item.quantity - 1})">-</button>
        <input type="number" value="${item.quantity}" min="1" onchange="updateCartQuantity('${item.id}', this.value)">
        <button class="qty-btn" onclick="updateCartQuantity('${item.id}', ${item.quantity + 1})">+</button>
      </div>
      <div class="cart-item-total">
        <p>R${itemTotal.toFixed(2)}</p>
        <button class="btn-remove" onclick="removeFromCart('${item.id}')">Remove</button>
      </div>
    `;
    cartItemsContainer.appendChild(cartItem);
  });

  const tax = subtotal * 0.1; // 10% tax
  const shipping = subtotal > 100 ? 0 : 10;
  const total = subtotal + tax + shipping;

  if (subtotalElement) subtotalElement.textContent = 'R' + subtotal.toFixed(2);
  if (totalElement) totalElement.textContent = 'R' + total.toFixed(2);

  // Update tax and shipping
  const taxElement = document.querySelector('.tax-value');
  const shippingElement = document.querySelector('.shipping-value');

  if (taxElement) taxElement.textContent = 'R' + tax.toFixed(2);
  if (shippingElement) shippingElement.textContent = shipping === 0 ? 'FREE' : 'R' + shipping.toFixed(2);
}

/* ========================================
   Product Filter Functions
   ======================================== */

function setupProductFilters() {
  const filterInputs = document.querySelectorAll('.filter-input');

  filterInputs.forEach(input => {
    input.addEventListener('change', function() {
      applyFilters();
    });
  });
}

function applyFilters() {
  const priceMin = document.querySelector('[data-filter="price-min"]')?.value || 0;
  const priceMax = document.querySelector('[data-filter="price-max"]')?.value || 10000;
  const category = document.querySelector('[data-filter="category"]')?.value || '';
  const searchTerm = document.querySelector('[data-filter="search"]')?.value.toLowerCase() || '';

  const products = document.querySelectorAll('.product-card');

  products.forEach(product => {
    const price = parseFloat(product.getAttribute('data-price'));
    const productCategory = product.getAttribute('data-category');
    const productName = product.querySelector('.product-name')?.textContent.toLowerCase() || '';

    const priceMatch = price >= priceMin && price <= priceMax;
    const categoryMatch = category === '' || productCategory === category;
    const searchMatch = productName.includes(searchTerm);

    product.style.display = priceMatch && categoryMatch && searchMatch ? 'block' : 'none';
  });
}

/* ========================================
   Form Validation Functions
   ======================================== */

function setupFormValidation() {
  const forms = document.querySelectorAll('form[data-validate="true"]');

  forms.forEach(form => {
    form.addEventListener('submit', function(e) {
      if (!validateForm(this)) {
        e.preventDefault();
      }
    });
  });
}

function validateForm(form) {
  const inputs = form.querySelectorAll('input, textarea, select');
  let isValid = true;

  inputs.forEach(input => {
    if (!validateField(input)) {
      isValid = false;
    }
  });

  return isValid;
}

function validateField(field) {
  const value = field.value.trim();
  const type = field.type;
  const required = field.hasAttribute('required');

  // Check required
  if (required && value === '') {
    showFieldError(field, 'This field is required');
    return false;
  }

  // Check email
  if (type === 'email' && value !== '') {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(value)) {
      showFieldError(field, 'Please enter a valid email');
      return false;
    }
  }

  // Check password length
  if (type === 'password' && value !== '' && value.length < 6) {
    showFieldError(field, 'Password must be at least 6 characters');
    return false;
  }

  // Check number
  if (type === 'number' && value !== '') {
    if (isNaN(value) || value < 0) {
      showFieldError(field, 'Please enter a valid number');
      return false;
    }
  }

  clearFieldError(field);
  return true;
}

function showFieldError(field, message) {
  clearFieldError(field);
  field.classList.add('error');

  const errorElement = document.createElement('span');
  errorElement.className = 'field-error';
  errorElement.textContent = message;

  field.parentNode.insertBefore(errorElement, field.nextSibling);
}

function clearFieldError(field) {
  field.classList.remove('error');
  const errorElement = field.parentNode.querySelector('.field-error');
  if (errorElement) {
    errorElement.remove();
  }
}

/* ========================================
   Search Functions
   ======================================== */

function setupSearch() {
  const searchInput = document.querySelector('.search-input');

  if (searchInput) {
    searchInput.addEventListener('input', debounce(function() {
      applyFilters();
    }, 300));
  }
}

function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

/* ========================================
   Modal Functions
   ======================================== */

function setupModals() {
  const modalTriggers = document.querySelectorAll('[data-modal-trigger]');
  const closeButtons = document.querySelectorAll('[data-modal-close]');

  modalTriggers.forEach(trigger => {
    trigger.addEventListener('click', function(e) {
      e.preventDefault();
      const modalId = this.getAttribute('data-modal-trigger');
      openModal(modalId);
    });
  });

  closeButtons.forEach(button => {
    button.addEventListener('click', function() {
      const modal = this.closest('.modal');
      if (modal) {
        closeModal(modal.id);
      }
    });
  });

  // Close modal when clicking outside
  document.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal')) {
      closeModal(event.target.id);
    }
  });
}

function openModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }
}

function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
  }
}

/* ========================================
   Notification Functions
   ======================================== */

function showNotification(message, type = 'info', duration = 3000) {
  const notification = document.createElement('div');
  notification.className = `notification notification-${type}`;
  notification.textContent = message;

  // Add to page
  document.body.appendChild(notification);

  // Trigger animation
  setTimeout(() => {
    notification.classList.add('show');
  }, 10);

  // Remove after duration
  setTimeout(() => {
    notification.classList.remove('show');
    setTimeout(() => {
      notification.remove();
    }, 300);
  }, duration);
}

/* ========================================
   Product Functions
   ======================================== */

function loadProductDetails(productId) {
  // This would typically fetch from an API
  console.log('Loading product details for ID:', productId);
  // Implementation would depend on your backend
}

function sortProducts(sortBy) {
  const products = Array.from(document.querySelectorAll('.product-card'));
  const container = document.querySelector('.products');

  products.sort((a, b) => {
    const priceA = parseFloat(a.getAttribute('data-price'));
    const priceB = parseFloat(b.getAttribute('data-price'));
    const nameA = a.getAttribute('data-name');
    const nameB = b.getAttribute('data-name');

    switch(sortBy) {
      case 'price-low':
        return priceA - priceB;
      case 'price-high':
        return priceB - priceA;
      case 'name-asc':
        return nameA.localeCompare(nameB);
      case 'name-desc':
        return nameB.localeCompare(nameA);
      default:
        return 0;
    }
  });

  // Reorder in DOM
  products.forEach(product => {
    container.appendChild(product);
  });
}

/* ========================================
   User Functions
   ======================================== */

function logout() {
  // Clear session/localStorage if needed
  localStorage.removeItem('nctraders_user');
  localStorage.removeItem('nctraders_token');
  
  // Redirect to login
  window.location.href = 'login.php';
}

function updateUserProfile(data) {
  // Send to backend API
  fetch('api/user/profile', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(data)
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      showNotification('Profile updated successfully', 'success');
    } else {
      showNotification(data.message || 'Error updating profile', 'error');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showNotification('Error updating profile', 'error');
  });
}

/* ========================================
   Utility Functions
   ======================================== */

function formatCurrency(value) {
  return 'R' + parseFloat(value).toFixed(2);
}

function formatDate(date) {
  return new Date(date).toLocaleDateString();
}

function formatTime(time) {
  return new Date(time).toLocaleTimeString();
}

function getCookie(name) {
  const nameEQ = name + "=";
  const cookies = document.cookie.split(';');
  for(let cookie of cookies) {
    cookie = cookie.trim();
    if (cookie.indexOf(nameEQ) === 0) {
      return cookie.substring(nameEQ.length);
    }
  }
  return null;
}

function setCookie(name, value, days = 30) {
  const date = new Date();
  date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
  const expires = "expires=" + date.toUTCString();
  document.cookie = name + "=" + value + ";" + expires + ";path=/";
}

/* ========================================
   Initialization on Page Load
   ======================================== */

// Update cart count on page load
updateCartCount();

// Export functions for use in inline scripts if needed
window.addToCart = addToCart;
window.removeFromCart = removeFromCart;
window.updateCartQuantity = updateCartQuantity;
window.updateCartSummary = updateCartSummary;
window.applyFilters = applyFilters;
window.sortProducts = sortProducts;
window.openModal = openModal;
window.closeModal = closeModal;
window.showNotification = showNotification;
window.logout = logout;
window.validateField = validateField;
window.updateUserProfile = updateUserProfile;
