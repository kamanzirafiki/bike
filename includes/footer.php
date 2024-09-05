<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Professional Website</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    
footer {
    background-color: #000;
    color: #f0f0f0;
    padding: 50px 0 20px;
    font-family: 'Arial', sans-serif;
}

.footer-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.footer-section {
    flex: 1;
    min-width: 250px;
    margin-bottom: 30px;
}

.footer-section h3 {
    color: #fff;
    font-size: 1.2em;
    margin-bottom: 20px;
    position: relative;
}

.footer-section h3::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: -5px;
    width: 30px;
    height: 2px;
    background-color: #fff;
}

.footer-section ul {
    list-style: none;
    padding: 0;
}

.footer-section ul li {
    margin-bottom: 10px;
}

.footer-section ul li a {
    color: #fff;
    text-decoration: none;
    transition: color 0.3s ease;
}

.footer-section ul li a:hover {
    color: #8f8989;
}

.footer-section ul li a i {
    margin-right: 5px;
    color: #A4ABA6;
}

.social-links {
    margin-bottom: 20px;
}

.social-links a {
    display: inline-block;
    width: 35px;
    height: 35px;
    background-color: #A4ABA6;
    color: #fff;
    text-align: center;
    line-height: 35px;
    border-radius: 50%;
    margin-right: 10px;
    transition: all 0.3s ease;
}
.social-links a:hover {
    /*background-color: #8f8989;*/
    transform: translateY(-3px);
}

.newsletter h4 {
    margin-bottom: 15px;
}

.newsletter form {
    display: flex;
}

.newsletter input {
    flex-grow: 1;
    padding: 10px;
    border: none;
    border-radius: 30px  0px 0px 30px;
    outline: none;
}

.newsletter button {
    background-color: #a4aba6;
    color: #fff;
    font-weight:bold;
    padding: 10px 15px;
    border: none;
    border-radius: 0px  30px 30px 0px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.newsletter button:hover {
    background-color: #8f8989;
}

.footer-bottom {
    text-align: center;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #333;
    position: relative;
}

.back-to-top {
    position: absolute;
    right: 20px;
    bottom: 20px;
    background-color: #a4aba6;
    color: #fff;
    width: 40px;
    height: 40px;
    text-align: center;
    line-height: 40px;
    border-radius: 50%;
    transition: background-color 0.3s ease;
}

.back-to-top:hover {
    background-color: #8f8989;
}

/* Responsive design */
@media (max-width: 768px) {
    .footer-container {
        flex-direction: column;
    }

    .footer-section {
        margin-bottom: 30px;
    }

    .back-to-top {
        right: 10px;
        bottom: 10px;
    }
}
    </style>
</head>
<body>
    <!-- Your main content here -->

    <footer>
        <div class="footer-container">
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="../userdash/about us.php"><i class="fas fa-info-circle"></i>About Us</a></li>
                    <li><a href="../userdash/contact us.php"><i class="fas fa-envelope"></i> Contact</a></li>
                    <li><a href="../userdash/bike.php"><i class="fas fa-shield-alt"></i> Bike List</a></li>
                    <li><a href="../admin/log.php"><i class="fa-solid fa-lock"></i>Admin Login</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Our Services</h3>
                <ul>
                    <li><a href="../userdash/bike.php"><i class="fa-solid fa-bookmark"></i>Booking</a></li>
                    <li><a href="#"><i class="fa-solid fa-circle-question"></i></i>Support</a></li>
                    <li><a href="#"><i class="fa-solid fa-screwdriver-wrench"></i>Maintenance</a></li>
                </ul>
            </div>
            <div class="footer-section">
                     <h3>Connect With Us</h3>
                <div class="social-links">
                <a href="#" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" target="_blank" aria-label="Whatsapp"><i class="fa-brands fa-whatsapp"></i></a>
                <a href="#" target="_blank" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                <a href="#" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            </div>
        <div class="newsletter">
        <h4>Subscribe to Our Newsletter</h4>
        <form action="subscribe.php" method="POST">
            <input type="email" name="email" placeholder="Your Email" aria-label="Email for newsletter" required>
            <button type="submit">Subscribe</button>
        </form>

     </div>
    </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 KC Company. All rights reserved.</p>
            <a href="#" class="back-to-top" aria-label="Back to top"><i class="fas fa-chevron-up"></i></a>
        </div>
    </footer>

</body>
</html>