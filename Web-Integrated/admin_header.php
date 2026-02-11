<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug line - remove after testing
// echo '<pre>Session: '; print_r($_SESSION); echo '</pre>';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Hipz Vineyard</title>
    <link rel="shortcut icon" href="images/logo_HIPZ.jpeg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
   
<header class="header">
    <div class="menu-bar">

    <i class="fa fa-bars" aria-hidden="true" onclick="toggleMenu()"></i>
    <span style="padding-left: 10px">Explore</span>
        <ul class="dropdown-menu">
            <li><a href="admin.php">HOME</a></li>
            <li><a href="member_listing.php">MEMBER LISTING</a></li>
            <li><a href="add_product.php">ADD PRODUCT</a></li>
            <li><a href="product_listing.php">PRODUCT LISTING</a></li>
            <li><a href="admin_order_listing.php">ORDER LISTING</a></li>
            <li><a href="monthly_transaction.php">MONTHLY TRANSACTION</a></li>
            <li><a href="logout.php">LOGOUT</a></li>
        </ul>
    </div>
</header>

<script>
    function toggleMenu() {
        const menu = document.querySelector('.dropdown-menu');
        menu.classList.toggle('active');
    }

    function toggleUserMenu(event) {
        event.preventDefault();
        const menu = document.getElementById('userMenu');
        menu.classList.toggle('active');
    }

    // Close user menu when clicking outside
    window.onclick = function(event) {
        if (!event.target.matches('.fa-user')) {
            const menu = document.getElementById('userMenu');
            if (menu && menu.classList.contains('active')) {
                menu.classList.remove('active');
            }
        }
    }
</script>

</body>
</html>