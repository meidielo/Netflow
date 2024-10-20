<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Enquire about NETFLOW Products">
    <meta name="keywords" content="Wi-Fi router, NETFLOW, enquiry">
    <meta name="author" content="Meidie Fei">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enquire - NETFLOW</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/style.css">
    <script src="scripts/enhancements.js" defer></script>
    <script src="scripts/part2.js" defer></script>
</head>

<body>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Clear the cart from localStorage
            localStorage.removeItem('cart');
            console.log('Cart has been cleared.');
        });
    </script>
    
    <?php include 'header.inc'; ?> <!-- Including the header -->

    <section class="section3">
        <h2>Product Enquiry</h2>
        <form method="post" novalidate>
            <fieldset>
                <legend>Personal Details</legend>
                <label for="fname">First Name:</label>
                <input type="text" id="fname" name="fname" required pattern="[A-Za-z]{1,25}" maxlength="25"
                    placeholder="John"><br>
                <span class="error-message" id="fname-error"></span><br>

                <label for="lname">Last Name:</label>
                <input type="text" id="lname" name="lname" required pattern="[A-Za-z]{1,25}" maxlength="25"
                    placeholder="Doe"><br>
                <span class="error-message" id="lname-error"></span><br>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}"
                    placeholder="example@domain.com"><br>
                <span class="error-message" id="email-error"></span><br>
            </fieldset>

            <fieldset>
                <legend>Address</legend>
                <label for="address">Street Address:</label>
                <input type="text" id="address" name="address" required maxlength="40" pattern="[A-Za-z0-9\s]{1,40}"
                    placeholder="123 Street"><br>
                <span class="error-message" id="address-error"></span><br>

                <label for="suburb">Suburb/Town:</label>
                <input type="text" id="suburb" name="suburb" required maxlength="20" pattern="[A-Za-z\s]{1,20}"
                    placeholder="Suburb"><br>
                <span class="error-message" id="suburb-error"></span><br>

                <label for="state">State:</label>
                <select id="state" name="state" required>
                    <option value="" disabled selected>Select your state</option>
                    <option value="ACT">ACT</option>
                    <option value="NSW">NSW</option>
                    <option value="NT">NT</option>
                    <option value="QLD">QLD</option>
                    <option value="SA">SA</option>
                    <option value="TAS">TAS</option>
                    <option value="VIC">VIC</option>
                    <option value="WA">WA</option>
                </select><br>
                <span class="error-message" id="state-error"></span><br>

                <label for="postcode">Postcode:</label>
                <input type="text" id="postcode" name="postcode" required pattern="\d{4}" maxlength="4"
                    placeholder="3000"><br>
                <span class="error-message" id="postcode-error"></span><br>
            </fieldset>

            <fieldset>
                <legend>Contact Details</legend>

                <label for="phone">Phone Number:</label>
                <input type="text" id="phone" name="phone" required pattern="^04\d{8}$" maxlength="10"
                    placeholder="04XXXXXXXX">
                <span class="error-message" id="phone-error"></span><br>

                <label>Preferred Contact Method:</label>
                <div class="inputline">
                    <input type="radio" id="email_contact" name="contact_method" value="email" required>
                    <label for="email_contact">Email</label>
                </div>
                <div class="inputline">
                    <input type="radio" id="post_contact" name="contact_method" value="post">
                    <label for="post_contact">Post</label>
                </div>
                <div class="inputline">
                    <input type="radio" id="phone_contact" name="contact_method" value="phone">
                    <label for="phone_contact">Phone</label>
                </div>
            </fieldset>

            <fieldset>
                <legend>Product Inquiry</legend>

                <!-- Router Selection -->
                <div class="product-selection">
                    <label>Product:</label>
                    <div class="product-item">
                        <input type="radio" id="router1" name="product" value="router1">
                        <label for="router1">
                            <img src="images/router1.png" alt="ASUS RT-BE92U">
                            ASUS RT-BE92U
                        </label>
                    </div>
                    <div class="product-item">
                        <input type="radio" id="router2" name="product" value="router2">
                        <label for="router2">
                            <img src="images/router2.png" alt="ASUS RT-BE88U">
                            ASUS RT-BE88U
                        </label>
                    </div>
                    <div class="product-item">
                        <input type="radio" id="router3" name="product" value="router3">
                        <label for="router3">
                            <img src="images/router3.png" alt="ROG Rapture GT-BE98">
                            ROG Rapture GT-BE98
                        </label>
                    </div>
                    <span class="error-message" id="product-error"></span><br>
                </div>

                <label for="quantity">Quantity:</label>
                <span class="error-message" id="quantity-error"></span><br>
                <input type="number" id="quantity" name="quantity" min="1" max="100" value="1" required><br>

                <label>Total Price:</label>
                <span id="total_price">$0.00</span><br>

                <label>Product Features:</label>
                <div class="inputline">
                    <input type="checkbox" name="features" value="speed"> High Speed
                </div>
                <div class="inputline">
                    <input type="checkbox" name="features" value="range"> Extended Range
                </div>
                <label for="comments">Comments:</label>
                <textarea id="comments" name="comments" placeholder="Enter your comments"></textarea><br>
            </fieldset>

            <!-- Add to Cart Button -->
            <button type="button" id="addToCartButton">Add to Cart</button>
            <input type="submit" value="Pay Now">
        </form>
    </section>

    <?php include 'footer.inc'; ?> <!-- Including the footer -->
</body>

</html>