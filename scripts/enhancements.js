/* 
author: Meidie Fei 105236884
Target: enquire.php, payment.php
Purpose: This file is for enhancements
Created: 15/09/2024
Last modified: 22/09/2024*/

"use strict";

// Function to read URL parameters
function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

function enquiryPage() {
    // Get the required elements from the DOM
    const stateSelect = document.getElementById('state');
    const postcodeInput = document.getElementById('postcode');
    const quantityInput = document.getElementById('quantity');
    const productRadios = document.querySelectorAll('input[name="product"]');

    // Ensure all required elements are found
    if (!stateSelect || !postcodeInput || !quantityInput || !productRadios.length) {
        console.error('Missing required form elements.');
        return; // Exit the function if any element is missing
    }

    // Get the selected product from the URL parameters (if provided)
    const selectedProductParam = getQueryParam('product');
    if (selectedProductParam) {
        const selectedProductRadio = document.querySelector(`input[value="${selectedProductParam}"]`);
        if (selectedProductRadio) {
            selectedProductRadio.checked = true; // Select the product radio button based on the URL parameter
        }
    }

    // Event listener to change postcode placeholder based on state selection
    stateSelect.addEventListener('change', function () {
        updatePostcodePlaceholder(stateSelect.value, postcodeInput);
    });

    // Prices for the products
    const prices = {
        "router1": 329.00,
        "router2": 649.00,
        "router3": 1499.00
    };

    // Attach event listeners to each radio button for product selection
    productRadios.forEach((radio) => {
        radio.addEventListener('change', updateTotalPrice);
    });

    // Attach an event listener to the quantity input to update the total price
    quantityInput.addEventListener('input', updateTotalPrice);

    // Initialize total price calculation when the page loads
    updateTotalPrice();

    document.getElementById('addToCartButton').addEventListener('click', addProductToCart);
}

// Function to update total price
function updateTotalPrice() {
    const totalPriceElement = document.getElementById('total_price');  // Moved this inside the function
    const quantityInput = document.getElementById('quantity');  // Also moved quantityInput inside the function
    const selectedProductRadio = document.querySelector('input[name="product"]:checked');

    if (!totalPriceElement || !quantityInput) {
        console.error("Required elements missing in the DOM.");
        return;
    }

    if (!selectedProductRadio) {
        console.log("No product selected");
        totalPriceElement.innerText = "$0.00";
        return;
    }

    const selectedProduct = selectedProductRadio.value; // Get the value of the selected radio button
    const quantity = parseInt(quantityInput.value, 10) || 0;

    console.log('Selected product:', selectedProduct);
    console.log('Quantity:', quantity);

    const prices = {
        "router1": 329.00,
        "router2": 649.00,
        "router3": 1499.00
    };

    const price = prices[selectedProduct] || 0;
    const total = price * quantity;

    // Update total price on the page
    totalPriceElement.innerText = `$${total.toFixed(2)}`;
}

// Function to update the placeholder of the postcode input based on state
function updatePostcodePlaceholder(state, postcodeInput) {
    const postcodePlaceholders = {
        'VIC': '3XXX or 8XXX',
        'NSW': '1XXX or 2XXX',
        'QLD': '4XXX or 9XXX',
        'NT': '0XXX',
        'WA': '6XXX',
        'SA': '5XXX',
        'TAS': '7XXX',
        'ACT': '0XXX'
    };

    if (postcodePlaceholders[state]) {
        postcodeInput.placeholder = postcodePlaceholders[state];
    } else {
        postcodeInput.placeholder = 'XXXX'; // Default placeholder if no state is selected
    }
}

function paymentPage() {
    updateCardNumberPlaceholder();
    updateCartDisplay();
    document.getElementById('paymentForm').addEventListener('submit', function (event) {
        // Populate hidden fields before the form is submitted
        populateHiddenFields();
    });
}

function updateCardNumberPlaceholder() {
    // Enhancements page logic for dynamic card number format
    document.getElementById('cardType').addEventListener('change', function () {
        const cardNumberInput = document.getElementById('cardNumber');
        const cardType = this.value;
        const cardLogo = document.getElementById('card-logo');

        if (cardType === 'Visa') {
            cardNumberInput.setAttribute('maxlength', '16');
            cardNumberInput.setAttribute('pattern', '\\d{16}');
            cardNumberInput.setAttribute('placeholder', '16 digits (Starts with 4)');
            cardLogo.src = 'images/Visa.png';  // Path to your Visa logo
            cardLogo.alt = 'Visa Logo';
            cardLogo.style.display = 'block';
        } else if (cardType === 'MasterCard') {
            cardNumberInput.setAttribute('maxlength', '16');
            cardNumberInput.setAttribute('pattern', '\\d{16}');
            cardNumberInput.setAttribute('placeholder', '16 digits (Starts with 51-55)');
            cardLogo.src = 'images/Mastercard.png';  // Path to your MasterCard logo
            cardLogo.alt = 'MasterCard Logo';
            cardLogo.style.display = 'block';
        } else if (cardType === 'Amex') {
            cardNumberInput.setAttribute('maxlength', '15');
            cardNumberInput.setAttribute('pattern', '\\d{15}');
            cardNumberInput.setAttribute('placeholder', '15 digits (Starts with 34 or 37)');
            cardLogo.src = 'images/amex.png';  // Path to your Amex logo
            cardLogo.alt = 'Amex Logo';
            cardLogo.style.display = 'block';
        } else {
            // Hide the card logo if no card type is selected
            cardNumberInput.setAttribute('maxlength', '');
            cardNumberInput.setAttribute('pattern', '');
            cardNumberInput.setAttribute('placeholder', 'Enter card number');
            cardLogo.style.display = 'none';
        }
    });
}

function addProductToCart() {
    const selectedProduct = document.querySelector('input[name="product"]:checked');
    const quantityInput = document.getElementById('quantity');
    const featuresInputs = document.querySelectorAll('input[name="features"]:checked');  // Select checked feature checkboxes
    const commentsInput = document.getElementById('comments');

    if (!selectedProduct) {
        alert("Please select a product.");
        return;
    }

    const productID = selectedProduct.value;
    const productNames = {
        'router1': 'ASUS RT-BE92U',
        'router2': 'ASUS RT-BE88U',
        'router3': 'ROG Rapture GT-BE98'
    };
    const prices = {
        'router1': 329.00,
        'router2': 649.00,
        'router3': 1499.00
    };

    const productName = productNames[productID];
    const price = prices[productID];
    const quantity = parseInt(quantityInput.value);

    // Safeguard against null or empty selection
    const selectedFeatures = featuresInputs ? Array.from(featuresInputs).map(input => input.value) : [];

    const comments = commentsInput ? commentsInput.value : '';

    // Log the selected features to check if they are being captured
    console.log('Selected features:', selectedFeatures);

    addToCart(productID, productName, quantity, price, selectedFeatures, comments);
    alert(`${productName} added to cart.`);
}


// Add product to cart
function addToCart(productID, productName, quantity, price, features, comments) {
    initializeCart();  // Ensure the cart exists

    let cart = JSON.parse(localStorage.getItem('cart'));

    // Check if the product is already in the cart
    let existingProduct = cart.find(item => item.productID === productID);

    if (existingProduct) {
        // Update the quantity and features if the product already exists
        existingProduct.quantity += quantity;
        // Optionally, you can also merge features if different ones are added later
        existingProduct.features = [...new Set([...existingProduct.features, ...features])];
        existingProduct.comments = comments; // Update the comments
    } else {
        // Add new product to the cart, including features and comments
        cart.push({
            productID,
            productName,
            quantity,
            price,
            features,     // Add features here
            comments      // Add comments here
        });
    }

    localStorage.setItem('cart', JSON.stringify(cart));  // Save the updated cart
    updateCartDisplay();
}

// Initialize cart in localStorage
function initializeCart() {
    if (!localStorage.getItem('cart')) {
        localStorage.setItem('cart', JSON.stringify([]));
    }
}

// Update cart display
function updateCartDisplay() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const enquiryData = JSON.parse(localStorage.getItem('enquiryData')) || null;

    console.log("Retrieved cart data:", cart);
    console.log("Retrieved enquiry data:", enquiryData);

    const cartDisplay = document.getElementById('checkoutCartDisplay');

    if (!cartDisplay) {
        console.log('Cart display not found.');
        return;
    }

    cartDisplay.innerHTML = '';  // Clear any existing content

    // If both cart and enquiryData are empty, show an empty cart message
    if (cart.length === 0 && (!enquiryData || !enquiryData.productName)) {
        cartDisplay.innerHTML = '<p>Your cart is empty.</p>';
        console.log('Cart is empty, displaying empty message.');
        document.getElementById('checkoutTotal').innerText = `$0.00`;
        document.getElementById('shipping').innerText = `$0.00`;
        document.getElementById('total').innerText = `$0.00`;
        return;
    }

    let totalPrice = 0;

    // If cart has items, display cart items
    if (cart.length > 0) {
        cart.forEach(item => {
            if (!item.quantity || !item.price || !item.productName) {
                console.log('Invalid item data:', item);
                return;
            }
            const itemQuantity = parseInt(item.quantity) || 0;
            const itemPrice = parseFloat(item.price) || 0;
            const itemTotalPrice = itemQuantity * itemPrice;
            totalPrice += itemTotalPrice;

            // Start building the cart item HTML
            let cartItemHTML = `
                <div class="checkout-item">
                    <div class="product-row">
                        <span>${item.productName} (x${itemQuantity}) - $${itemTotalPrice.toFixed(2)}</span>
                        <button onclick="removeFromCart('${item.productID}')">
                            <img src="images/trash.png" alt="Remove" style="width: 20px; height: 20px;">
                        </button>
                    </div>
            `;

            // Display features if they exist
            if (item.features && item.features.length > 0 && item.features[0] !== "No features listed") {
                cartItemHTML += `
                    <div class="features">
                        <strong>Features:</strong> ${item.features.join(', ')}
                    </div>
                `;
            }

            // Add comments section if applicable
            if (item.comments && item.comments.trim() !== "") {
                cartItemHTML += `
                    <div class="comments">
                        <strong>Comments:</strong> ${item.comments}
                    </div>
                `;
            }

            cartItemHTML += `</div>`; // Close the cart item div

            // Append the cart item to the cart display
            cartDisplay.innerHTML += cartItemHTML;
        });
    } else if (enquiryData && enquiryData.productName) {
        // If there is enquiryData (single product order)
        const productPrice = {
            'router1': 329.00,
            'router2': 649.00,
            'router3': 1499.00
        };

        const productQuantity = parseInt(enquiryData.quantity) || 0;
        const pricePerUnit = productPrice[enquiryData.product] || 0;
        const enquiryPrice = pricePerUnit * productQuantity;

        if (enquiryPrice > 0) {
            let enquiryHTML = `
                <div class="checkout-item">
                    <div class="product-row">
                        <span>${enquiryData.productName} (x${productQuantity}) - $${enquiryPrice.toFixed(2)}</span>
                    </div>
            `;

            // Only display features if they exist
            if (enquiryData.features && enquiryData.features.length > 0) {
                enquiryHTML += `
                    <div class="features">
                        <strong>Features:</strong> ${Array.isArray(enquiryData.features) ? enquiryData.features.join(', ') : enquiryData.features}
                    </div>
                `;
            }

            // Add comments section if available
            if (enquiryData.comments && enquiryData.comments.trim() !== "") {
                enquiryHTML += `
                    <div class="comments">
                        <strong>Comments:</strong> ${enquiryData.comments}
                    </div>
                `;
            }

            enquiryHTML += `</div>`; // Close the enquiry item div

            // Append the enquiry item to the cart display
            cartDisplay.innerHTML += enquiryHTML;

            // Update total price for enquiryData
            totalPrice += enquiryPrice;
        }
    }

    // Set the total price fields
    const shipping = totalPrice > 0 ? 10.00 : 0.00;  // Only charge shipping if there are items
    const finalTotal = totalPrice + shipping;
    document.getElementById('checkoutTotal').innerText = `$${totalPrice.toFixed(2)}`;
    document.getElementById('shipping').innerText = `$${shipping.toFixed(2)}`;
    document.getElementById('total').innerText = `$${finalTotal.toFixed(2)}`;

    console.log("Total price before shipping:", totalPrice);
    console.log("Final total price:", finalTotal);
}

// Remove product from cart
function removeFromCart(productID) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];

    // Remove the selected product
    cart = cart.filter(item => item.productID !== productID);
    
    // If the cart is empty, also clear the enquiryData
    if (cart.length === 0) {
        localStorage.removeItem('enquiryData');
    }

    // Save the updated cart and refresh the display
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartDisplay();
}

function populateHiddenFields() {
    const cart = JSON.parse(localStorage.getItem('cart'));
    const enquiryData = JSON.parse(localStorage.getItem('enquiryData'));
    const hiddenFieldsContainer = document.getElementById('hiddenFieldsContainer');

    // Clear any existing hidden fields
    hiddenFieldsContainer.innerHTML = '';

    let total = 0;

    // If there's a cart with multiple products
    if (cart && cart.length > 0) {
        cart.forEach((item, index) => {
            // Handle features and comments consistently
            const productFeatures = Array.isArray(item.features) && item.features.length > 0
                ? item.features.join(', ')
                : '-';
            const productComments = item.comments && item.comments.trim() !== ''
                ? item.comments
                : '-';
            
            const itemTotal = item.price * item.quantity;
            total += itemTotal;

            // Create hidden fields for each cart item
            hiddenFieldsContainer.innerHTML += `
                <input type="hidden" name="products[${index}][productID]" value="${item.productID}">
                <input type="hidden" name="products[${index}][productName]" value="${item.productName}">
                <input type="hidden" name="products[${index}][productQuantity]" value="${item.quantity}">
                <input type="hidden" name="products[${index}][productPrice]" value="${item.price}">
                <input type="hidden" name="products[${index}][productFeatures]" value="${productFeatures}">
                <input type="hidden" name="products[${index}][productComments]" value="${productComments}">
            `;
        });

    // If there's enquiry data (single product order)
    } else if (enquiryData) {
        const productPrice = {
            'router1': 329.00,
            'router2': 649.00,
            'router3': 1499.00
        };

        const price = productPrice[enquiryData.product] || 0;
        const quantity = parseInt(enquiryData.quantity, 10) || 1;
        const itemTotal = price * quantity;
        total += itemTotal;

        const productFeatures = Array.isArray(enquiryData.features) && enquiryData.features.length > 0
            ? enquiryData.features.join(', ')
            : '-';
        const productComments = enquiryData.comments && enquiryData.comments.trim() !== ''
            ? enquiryData.comments
            : '-';

        // Create hidden fields for the enquiryData
        hiddenFieldsContainer.innerHTML += `
            <input type="hidden" name="products[0][productID]" value="${enquiryData.product}">
            <input type="hidden" name="products[0][productName]" value="${enquiryData.productName}">
            <input type="hidden" name="products[0][productQuantity]" value="${quantity}">
            <input type="hidden" name="products[0][productPrice]" value="${price}">
            <input type="hidden" name="products[0][productFeatures]" value="${productFeatures}">
            <input type="hidden" name="products[0][productComments]" value="${productComments}">
        `;
    }

    console.log("Final total to set in hidden field:", total);

    // Add the total price as a hidden field
    hiddenFieldsContainer.innerHTML += `<input type="hidden" name="totalPrice" value="${total.toFixed(2)}">`;
    console.log("Hidden total price set:", total.toFixed(2));
}

// Function to initialize the page
function init() {
    console.log("enhancements.js is running");

    // Get the current page URL path
    const currentPath = window.location.pathname;

    // Check if it's the enquiry page by looking for 'enquire.php' in the URL
    if (currentPath.includes('enquire.php')) {
        enquiryPage();
    }

    // Check if it's the payment page by looking for 'payment.php' in the URL
    if (currentPath.includes('payment.php')) {
        paymentPage();
    }
}

window.onload = init;