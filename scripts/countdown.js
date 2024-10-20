document.addEventListener("DOMContentLoaded", function() {
    var countdown = 3;
    var countdownElement = document.getElementById("countdown");

    var interval = setInterval(function() {
        countdown--;
        countdownElement.textContent = countdown;

        if (countdown === 0) {
            clearInterval(interval);
            window.location.href = "index.php"; // Redirect to the homepage
        }
    }, 1000);
});
