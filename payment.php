<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/style.css">
    <script src="scripts/enhancements.js" defer></script>
    <script src="scripts/part2.js" defer></script>
</head>

<body>
    <?php include 'header.inc'; ?> <!-- Including the header -->

    <main>
        <!-- Two-column layout -->
        <div class="container payment-layout">
            <!-- Left Column: Payment Form -->
            <div class="payment-form">
                <h2>Payment Information</h2>

                <h3>Customer Details</h3>
                <div class="customer-info">
                    <p><strong>Name:</strong> <span id="fullNameDisplay"></span></p>
                    <p><strong>Email:</strong> <span id="emailDisplay"></span></p>
                    <p><strong>Address:</strong> <span id="fullAddressDisplay"></span></p>
                    <p><strong>Phone:</strong> <span id="phoneDisplay"></span></p>
                    <p><strong>Preferred Contact Method:</strong> <span id="contactMethodDisplay"></span></p>
                </div>

                <h3>Select Payment Option</h3>
                <form id="paymentForm" action="process_order.php" method="post" novalidate>
                    <!-- Card Details -->
                    <div class="payment-options">
                        <div class="card-selection">
                            <label for="cardType">Card Type</label>
                            <div class="card-select-wrapper">
                                <select id="cardType" name="cardType" required>
                                    <option value="" disabled selected>Select Card Type</option>
                                    <option value="Visa">Visa</option>
                                    <option value="MasterCard">MasterCard</option>
                                    <option value="Amex">American Express</option>
                                </select>
                                <img id="card-logo" src="images/visa.png" alt="Card Logo" style="display: none;">
                            </div>
                        </div>
                        <label for="cardName">Name on Card</label>
                        <input type="text" id="cardName" name="cardName" required maxlength="40" pattern="[A-Za-z\s]+"
                            placeholder="John Doe" title="Only alphabetic characters and spaces are allowed.">

                        <label for="cardNumber">Card Number</label>
                        <input type="text" id="cardNumber" name="cardNumber" required pattern="\d{15,16}" maxlength="16"
                            placeholder="Enter card number"
                            title="Card number must be 15 or 16 digits depending on the card type.">

                        <label for="expiryDate">Expiry Date (MM/YY)</label>
                        <input type="text" id="expiryDate" name="expiryDate" required pattern="\d{2}/\d{2}" maxlength="5"
                            placeholder="MM/YY" title="Please enter the expiry date in MM/YY format.">

                        <label for="cvv">CVV</label>
                        <input type="text" id="cvv" name="cvv" maxlength="3" required pattern="\d{3}" placeholder="XXX"
                            title="CVV must be 3 digits.">
                    </div>

                    <!-- Hidden Inputs for Product Data -->
                    <div id="hiddenFieldsContainer"></div>

                    <!-- Hidden Inputs for Customer Details -->
                    <input type="hidden" id="hiddenFullName" name="fullName">
                    <input type="hidden" id="hiddenEmail" name="email">
                    <input type="hidden" id="hiddenAddress" name="address">
                    <input type="hidden" id="hiddenSuburb" name="suburb">
                    <input type="hidden" id="hiddenState" name="state">
                    <input type="hidden" id="hiddenPostcode" name="postcode">
                    <input type="hidden" id="hiddenPhone" name="phone">
                    <input type="hidden" id="hiddenContactMethod" name="contactMethod">

                    <input type="submit" value="Submit Payment">
                    <button type="button" class="cancel-btn" id="cancelOrderBtn">Cancel Order</button>
                </form>
            </div>

            <!-- Right Column: Order Summary -->
            <div class="order-summary">
                <h3>Your Cart</h3>

                <div id="checkoutCartDisplay"></div>

                <h3>Order Summary</h3>
                <div class="summary-details">
                    <p>Total: <span id="checkoutTotal">$0.00</span></p>
                    <p>Shipping: <span id="shipping">$10.00</span></p>
                    <p>Total: <span id="total">$0.00</span></p>
                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.inc'; ?> <!-- Including the footer -->
</body>

</html>