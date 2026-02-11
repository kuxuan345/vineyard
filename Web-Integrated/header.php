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
            <li><a href="menu.php">Home</a></li>
            <li><a href="cart.php">Our Vintage</a></li>
            <li><a href="event.php">Hipz Event</a></li>
            <li><a href="history.php">The Heritage</a></li>
            <li><a href="login.php">Administrator</a></li>
        </ul>
    </div>

    <div class="lang-shop">
        <a href="modify_cart.php"><i class="fa fa-shopping-cart"></i></a>
        <div class="vertical-line"></div>
        <?php if (!isset($_SESSION['user'])): ?>
            <!-- Not logged in - direct link to login -->
            <a href="login.php" aria-label="Login"><i class="fa fa-user"></i></a>
        <?php else: ?>
            <!-- Logged in - dropdown menu -->
            <div class="user-dropdown">
                <a href="#" onclick="toggleUserMenu(event)" aria-label="User menu">
                    <i class="fa fa-user"></i>
                </a>
                <div class="user-menu" id="userMenu">
                    <div class="welcome-message">
                        Welcome, <?php echo htmlspecialchars($_SESSION['user']['name']); ?>!
                    </div>
                    <a href="profile.php">Profile</a>
                    <a href="view_rewards.php">My Gift</a>
                    <a href="mem_order_history.php">Order History</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        <?php endif; ?>
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