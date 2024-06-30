<?php
include 'session_helper.php';
startSession();
?>

<nav class="navbar">
    <div class="nav-logo">
        <a href="index.php"><img src="images/Logo.svg" alt="Zunails Logo"></a>
    </div>

    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="about.php">About</a></li>
        <li><a href="gallery.php">Gallery</a></li>
        <li><a href="index.php#contact-section">Contact</a></li>
        <li><a href="services.php">Services</a></li>
        <li><a href="services.php">Book Now</a></li>
    </ul>

    <div class="nav-icons">
        <?php if(isset($_SESSION['username'])): ?>
            <a href="logout.php" class="login">Logout</a>
        <?php else: ?>
            <a href="login.php" class="login">Login</a>
        <?php endif; ?>  
        <a href="user.php" class="profile-icon"><img src="images/ProfileIcon.svg" alt="Profile"></a>
        <a href="cart.php" class="nav-icons"><img src="images/ShoppingBagIcon.svg" alt="Shopping Bag"></a> 
    </div>

    <div class="menu-icon">
        <img src="images/MenuIcon.svg" alt="Menu Icon">
    </div>
</nav>

<div class="popup-menu">
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="about.php">About</a></li>
        <li><a href="gallery.php">Gallery</a></li>
        <li><a href="index.php#contact-section">Contact</a></li>
        <li><a href="services.php">Services</a></li>
        <li><a href="services.php">Book Now</a></li>
        <?php if(isset($_SESSION['username'])): ?>
            <li><a href="logout.php" class="login">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php" class="login">Login</a></li>
        <?php endif; ?>  
        <li class="icons">
            <a href="user.php"><img src="images/ProfileIcon.svg" alt="Profile"></a>
            <a href="cart.php"><img src="images/ShoppingBagIcon.svg" alt="Shopping Bag"></a>
        </li>
    </ul>
</div>

<script src="js/responsive.js"></script>
