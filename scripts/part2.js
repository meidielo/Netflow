/* 
author: Meidie Fei 105236884
Target: enquire.php, payment.php
Purpose: This file is for enquire and payment validation
Created: 15/09/2024
Last modified: 22/09/2024*/

"use strict";

let debug = false;

// Enquiry Page Logic
function part2EnquiryPage() {
    const quantityInput = document.getElementById('quantity');
    const stateSelect = document.getElementById('state');
    const postcodeInput = document.getElementById('postcode');
    const fnameInput = document.getElementById('fname');
    const lnameInput = document.getElementById('lname');
    const emailInput = document.getElementById('email');
    const addressInput = document.getElementById('address');
    const suburbInput = document.getElementById('suburb');
    const phoneInput = document.getElementById('phone');
    const commentsInput = document.getElementById('comments');

    // Mapping product to corresponding image
    const productImages = {
        'router1': 'images/router1.png',
        'router2': 'images/router2.png',
        'router3': 'images/router3.png'
    };

    document.querySelector('form').addEventListener('submit', function (e) {
        if (window.location.hostname !== 'localhost') {
            e.preventDefault();
        }

        let isValid = true;
        console.log("Form submitted!");

        const contactMethodInput = document.querySelector('input[name="contact_method"]:checked');
        console.log('Selected contact method:', contactMethodInput);

        // Get selected product features
        const selectedFeatures = Array.from(document.querySelectorAll('input[name="features"]:checked'))
            .map(feature => feature.value);
        console.log('Selected features:', selectedFeatures);

        if (!debug) {
            console.log("Debug is false, skipping client-side validation.");
        } else {
            if (!validateProductSelection()) {
                e.preventDefault();
                isValid = false;
            }
            if (!validateQuantity(quantityInput)) {
                e.preventDefault();
                isValid = false;
            }
            if (!validateStatePostcodeMatch(stateSelect, postcodeInput)) {
                e.preventDefault();
                isValid = false;
            }
        }
        if (isValid) {
            const selectedProduct = document.querySelector('input[name="product"]:checked');
            const productNames = {
                'router1': 'ASUS RT-BE92U',
                'router2': 'ASUS RT-BE88U',
                'router3': 'ROG Rapture GT-BE98'
            };

            const formData = {
                firstName: fnameInput.value,
                lastName: lnameInput.value,
                email: emailInput.value,
                address: addressInput.value,
                suburb: suburbInput.value,
                state: stateSelect.value,
                postcode: postcodeInput.value,
                phone: phoneInput.value,
                contactMethod: contactMethodInput ? contactMethodInput.value : '',
                product: selectedProduct ? selectedProduct.id : '',  // Store product ID (e.g., 'router1')
                productName: selectedProduct ? productNames[selectedProduct.id] : '',  // Store product name
                quantity: quantityInput.value,
                image: selectedProduct ? productImages[selectedProduct.id] : '',  // Store image based on product ID
                features: selectedFeatures.length > 0 ? selectedFeatures : 'No features selected',  // Store selected features
                comments: commentsInput.value
            };

            console.log("All validations passed. Storing data and redirecting...");

            // Store form data in localStorage
            localStorage.setItem('enquiryData', JSON.stringify(formData));

            // Redirect to payment page
            window.location.href = 'payment.php';
        }
    });
}

// Adjusted validation function for radio button selection
function validateProductSelection() {
    const selectedProduct = document.querySelector('input[name="product"]:checked');
    if (!selectedProduct) {
        alert('Please select a product.');
        return false;
    }
    return true;
}

function validateQuantity(quantityInput) {
    const quantity = parseInt(quantityInput.value);
    if (isNaN(quantity) || quantity <= 0) {
        alert('Please enter a valid positive quantity.');
        return false;
    }
    return true;
}

function validateStatePostcodeMatch(stateSelect, postcodeInput) {
    const state = stateSelect.value;
    const postcode = postcodeInput.value;
    const firstDigit = postcode.charAt(0);

    const validStatePostcode = {
        'VIC': ['3', '8'],
        'NSW': ['1', '2'],
        'QLD': ['4', '9'],
        'NT': ['0'],
        'WA': ['6'],
        'SA': ['5'],
        'TAS': ['7'],
        'ACT': ['0']
    };

    if (!validStatePostcode[state].includes(firstDigit)) {
        alert(`Postcode does not match the selected state. The first digit of ${state} postcodes should be ${validStatePostcode[state].join(' or ')}.`);
        return false;
    }
    return true;
}

// Payment Page Logic
function part2PaymentPage() {
    // Retrieve personal details from `enquiryData`
    const enquiryData = JSON.parse(localStorage.getItem('enquiryData'));
    console.log("Retrieved personal data:", enquiryData);

    if (enquiryData) {
        // Display personal information
        const fullName = `${enquiryData.firstName} ${enquiryData.lastName}`;
        // Construct full address dynamically, avoiding extra commas if fields are empty
        let fullAddress = `${enquiryData.address ? enquiryData.address : ''}`;
        if (enquiryData.suburb) {
            fullAddress += fullAddress ? `, ${enquiryData.suburb}` : enquiryData.suburb;
        }
        if (enquiryData.state) {
            fullAddress += fullAddress ? `, ${enquiryData.state}` : enquiryData.state;
        }
        if (enquiryData.postcode) {
            fullAddress += fullAddress ? `, ${enquiryData.postcode}` : enquiryData.postcode;
        }

        // Populate hidden fields for form submission
        document.getElementById('hiddenFullName').value = fullName;
        document.getElementById('hiddenEmail').value = enquiryData.email;
        document.getElementById('hiddenAddress').value = enquiryData.address;
        document.getElementById('hiddenSuburb').value = enquiryData.suburb;
        document.getElementById('hiddenState').value = enquiryData.state;
        document.getElementById('hiddenPostcode').value = enquiryData.postcode;
        document.getElementById('hiddenPhone').value = enquiryData.phone;
        document.getElementById('hiddenContactMethod').value = enquiryData.contactMethod;

        document.getElementById('fullNameDisplay').innerText = fullName;
        document.getElementById('emailDisplay').innerText = enquiryData.email;
        document.getElementById('fullAddressDisplay').innerText = fullAddress;
        document.getElementById('phoneDisplay').innerText = enquiryData.phone;
        document.getElementById('contactMethodDisplay').innerText = enquiryData.contactMethod;
    } else {
        alert('No personal data found.');
        window.location.href = 'enquire.php';
        return;
    }

    // Validate fields when the form is submitted
    const paymentForm = document.getElementById('paymentForm');
    paymentForm.addEventListener('submit', function (e) {
        const cardNumber = document.getElementById('cardNumber').value;
        const cardType = document.getElementById('cardType').value;
        const expiryDate = document.getElementById('expiryDate').value;
        const cvv = document.getElementById('cvv').value;

        let isValid = true;
        if (!debug) {
            console.log("Debug is false, skipping client-side validation.");
        } else {
            if (!validateCardNumber(cardNumber, cardType)) {
                isValid = false;
            }
            if (!isValidExpiryDate(expiryDate)) {
                isValid = false;
            }
            if (!validateCVV(cvv, cardType)) {
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();  // Prevent form submission if validation fails
            }
        }
    });
}

// Cancel order functionality with confirmation
function cancelOrder() {
    // Display confirmation dialog
    const confirmCancel = confirm("Are you sure you want to cancel the order?");

    // If the user confirms, clear the cart and redirect to the homepage
    if (confirmCancel) {
        localStorage.clear(); // Clear stored data
        window.location.href = 'index.php'; // Redirect to the home page
    }
}

// Validate Card Number
function validateCardNumber(cardNumber, cardType) {
    const visaRegex = /^4[0-9]{15}$/;  // Visa: 16 digits, starts with 4
    const masterCardRegex = /^5[1-5][0-9]{14}$/;  // MasterCard: 16 digits, starts with 51-55
    const amexRegex = /^3[47][0-9]{13}$/;  // Amex: 15 digits, starts with 34 or 37
    const cardNumberInput = document.getElementById('cardNumber');

    let isValid = false;

    if (cardType === 'Visa' && visaRegex.test(cardNumber)) {
        isValid = true;
    } else if (cardType === 'MasterCard' && masterCardRegex.test(cardNumber)) {
        isValid = true;
    } else if (cardType === 'Amex' && amexRegex.test(cardNumber)) {
        isValid = true;
    } else {
        return false;
    }
    return isValid;
}

// Validate Expiry Date
function isValidExpiryDate(expiry) {
    const expiryInput = document.getElementById('expiryDate');

    // Check if the expiry date matches MM/YY format
    if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(expiry)) {
        return false;
    }

    const [month, year] = expiry.split('/').map(Number);

    // Additional check for invalid month (like 44)
    if (month < 1 || month > 12) {
        return false;
    }

    const currentDate = new Date();
    const currentMonth = currentDate.getMonth() + 1; // Months are 0-indexed
    const currentYear = currentDate.getFullYear() % 100; // Get last two digits of the current year

    // Check if the expiry date is in the past
    if (year < currentYear || (year === currentYear && month < currentMonth)) {
        return false;
    }

    return true;
}

// Validate CVV
function validateCVV(cvv) {
    if (!/^\d{3}$/.test(cvv)) {
        return false;
    }
    return true;
}

// Initialization
document.addEventListener('DOMContentLoaded', function () {
    console.log("part2.js is running");

    // Get the current page URL path
    const currentPath = window.location.pathname;

    // Check if it's the enquiry page by looking for 'enquire.php' in the URL
    if (currentPath.includes('enquire.php')) {
        part2EnquiryPage();
    }

    // Check if it's the payment page by looking for 'payment.php' in the URL
    if (currentPath.includes('payment.php')) {
        part2PaymentPage();
    }

    // Cancel order functionality
    const cancelOrderBtn = document.getElementById('cancelOrderBtn');
    if (cancelOrderBtn) {
        cancelOrderBtn.addEventListener('click', cancelOrder);
    }
});