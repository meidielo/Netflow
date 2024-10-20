<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Enhancements to the Web Application">
    <meta name="keywords" content="HTML, CSS, Enhancements">
    <meta name="author" content="Your Name">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enhancements - NETFLOW</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/style.css">
    <script src="scripts/enhancements.js" defer></script>
</head>

<body>
    <?php include 'header.inc'; ?> <!-- Including the header -->

    <section class="enh-section">
        <h1>Enhancements to the Specified Requirements</h1>
        <p>This page lists the additional enhancements that go beyond the requirements of the assignment. Each
            enhancement includes an explanation, the relevant code, and a link to where it is implemented on the site.
        </p>

        <div class="enhancement">
            <h2>Enhancement 1: Cart Functionality</h2>
            <p><strong>Description:</strong> The cart function has been enhanced to handle multiple products, each with
                their own selected features and comments. The cart updates dynamically as products are added, quantities
                are changed, and specific features or comments are updated. This enhancement allows for a more flexible
                and interactive cart system.</p>
            <div class="content">
                <pre><code>
    function addToCart(productID, productName, quantity, price, features, comments) {
        initializeCart();  // Ensure the cart exists
        let cart = JSON.parse(localStorage.getItem('cart'));
    
        let existingProduct = cart.find(item => item.productID === productID);
    
        if (existingProduct) {
            existingProduct.quantity += quantity;  
            existingProduct.features = [...new Set([...existingProduct.features, ...features])];
            existingProduct.comments = comments;  
        } else {
            cart.push({ productID, productName, quantity, price, features, comments });
        }
    
        localStorage.setItem('cart', JSON.stringify(cart));  // Save the updated cart
        updateCartDisplay();
    }
                    </code></pre>
            </div>
            <p><strong>How it goes beyond the basics:</strong> The cart allows for multiple items to be added or
                removed, and it dynamically handles quantities, features, and comments. This goes beyond simply storing
                a single item by offering users more flexibility in managing their selections.</p>
            <p><strong>Applied Example:</strong> <a href="payment.php">View the enhanced cart on the Payment Page</a>
            </p>
        </div>

        <div class="enhancement">
            <h2>Enhancement 2: Dynamic Card Logo</h2>
            <p><strong>Description:</strong> The card logo on the payment page dynamically updates based on the selected
                card type (Visa, MasterCard, or Amex). This provides visual feedback to users about the card they are
                entering details for, enhancing the user experience.</p>
            <div class="content">
                <pre><code>
    function updateCardNumberPlaceholder() {
        document.getElementById('cardType').addEventListener('change', function () {
            const cardNumberInput = document.getElementById('cardNumber');
            const cardType = this.value;
    
            if (cardType === 'Visa') {
                cardNumberInput.placeholder = '16 digits (Starts with 4)';
                document.getElementById('card-logo').src = 'images/Visa.png';
            } else if (cardType === 'MasterCard') {
                cardNumberInput.placeholder = '16 digits (Starts with 51-55)';
                document.getElementById('card-logo').src = 'images/Mastercard.png';
            } else if (cardType === 'Amex') {
                cardNumberInput.placeholder = '15 digits (Starts with 34 or 37)';
                document.getElementById('card-logo').src = 'images/amex.png';
            } else {
                cardNumberInput.placeholder = 'Enter card number';
                document.getElementById('card-logo').style.display = 'none';  // Hide logo for unsupported types
            }
        });
    }
                    </code></pre>
            </div>

            <p><strong>How it goes beyond the basics:</strong> The use of dynamic logos and placeholders improves the
                user experience by providing contextual feedback as the user interacts with the form.</p>
            <p><strong>Applied Example:</strong> <a href="payment.php">View the dynamic card logo on the Payment
                    Page</a></p>
        </div>

        <div class="enhancement">
            <h2>Enhancement 3: Dynamic Placeholders</h2>
            <p><strong>Description:</strong> On the enquiry page, the postcode input field dynamically updates its
                placeholder based on the selected state. This helps users enter the correct format for their postcode
                based on their location.</p>
            <div class="content">
                <pre><code>
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
    
        postcodeInput.placeholder = postcodePlaceholders[state] || 'XXXX';  // Default placeholder if no state is selected
    }
                    </code></pre>
            </div>
            <p><strong>How it goes beyond the basics:</strong> By dynamically changing the placeholder text, users are
                given context-sensitive instructions, improving the accuracy of their input.</p>
            <p><strong>Applied Example:</strong> <a href="enquire.php">View the dynamic placeholders on the Enquiry
                    Page</a></p>
        </div>
    </section>

    <section class="references">
        <h2>References</h2>
        <ul>
            <li>Design inspiration and card logos were sourced from <a href="https://www.google.com"
                    target="_blank">Google</a>.</li>
        </ul>
    </section>

    <?php include 'footer.inc'; ?> <!-- Including the footer -->
</body>

</html>