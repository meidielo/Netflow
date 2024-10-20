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
</head>

<body>
    <?php include 'header.inc'; ?> <!-- Including the header -->

    <section class="enh-section">
        <h1>Enhancements to the Specified Requirements</h1>
        <p>This page lists the additional enhancements that go beyond the requirements of the assignment. Each
            enhancement includes an explanation, the relevant code, and a link to where it is implemented on the site.
        </p>

        <div class="enhancement">
            <h2>Enhancement 1: nth-child Selectors for Styling Table Rows</h2>
            <p><strong>Description:</strong> The nth-child pseudo-class selector was used to style alternating rows in a
                table with different background colours. This improves readability and the visual appeal of the table
                content.</p>
            <p><strong>Code Example:</strong></p>
            <pre><code>
tbody tr:nth-child(odd) {
    background-color: #f2f2f2;
}

tbody tr:nth-child(even) {
    background-color: #fff;
}
            </code></pre>
            <p><strong>How it goes beyond the basics:</strong> This selector allows for targeted styling of specific
                child elements, offering more control over the design compared to general styles.</p>
            <p><strong>Source:</strong> <a href="https://developer.mozilla.org/en-US/docs/Web/CSS/:nth-child"
                    target="_blank">nth-child() CSS Selector Documentation</a></p>
            <p><strong>Applied Example:</strong> <a href="about.php">View the table with nth-child selectors on the
                    About Page</a></p>
        </div>

        <div class="enhancement">
            <h2>Enhancement 2: Before and After Pseudo-elements for Link Effects</h2>
            <p><strong>Description:</strong> Before and after pseudo-elements were used to create dynamic hover effects
                on links. This adds a visually appealing transition when links are hovered over.</p>
            <p><strong>Code Example:</strong></p>
            <pre><code>
section a::before {
    content: '';
    position: absolute;
    width: 100%;
    height: 2px;
    bottom: 0;
    left: 0;
    background-color: #000;
    visibility: hidden;
    transform: scaleX(0);
    transition: all 0.3s ease-in-out;
}

section a:hover::before {
    visibility: visible;
    transform: scaleX(1);
}
            </code></pre>
            <p><strong>How it goes beyond the basics:</strong> The use of before and after pseudo-elements allows for
                more complex designs without needing extra HTML elements.</p>
            <p><strong>Source:</strong> <a href="https://css-tricks.com/almanac/selectors/a/after-and-before/"
                    target="_blank">CSS Tricks: Using ::before and ::after for Advanced Effects</a></p>
            <p><strong>Applied Example:</strong> <a href="index.php">Hover effect on the Index Page</a></p>
        </div>

        <div class="enhancement">
            <h2>Enhancement 3: Responsive Image Gallery with Flexbox</h2>
            <p><strong>Description:</strong> A responsive image gallery was created using Flexbox. The images adjust
                based on the screen size, providing a consistent layout across different devices.</p>
            <p><strong>Code Example:</strong></p>
            <pre><code>
figure {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border: 5px double #333;
    border-radius: 10px;
    background-color: #f9f9f9;
    text-align: center;
}

figure img {
    width: 100%;
    height: auto;
    border-radius: 10px;
}

figure figcaption {
    margin-top: 10px;
    font-size: 1rem;
    color: #555;
}
            </code></pre>
            <p><strong>How it goes beyond the basics:</strong> Flexbox enables dynamic layouts that adjust based on
                screen size, enhancing the user experience on mobile devices.</p>
            <p><strong>Source:</strong> <a
                    href="https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_Flexible_Box_Layout/Basic_Concepts_of_Flexbox"
                    target="_blank">CSS Flexbox Documentation</a></p>
            <p><strong>Applied Example:</strong> <a href="about.php">Responsive gallery on the About Page</a></p>
        </div>
    </section>

    <section class="references">
        <h2>References</h2>
        <ul>
            <li>Design inspiration and layout was influenced by the <a href="https://www.samsung.com"
                    target="_blank">Samsung Official Website</a>.</li>
            <li>Product images were sourced from <a href="https://www.asus.com" target="_blank">ASUS Official
                    Website</a>.</li>
        </ul>
    </section>

    <?php include 'footer.inc'; ?> <!-- Including the footer -->
</body>

</html>