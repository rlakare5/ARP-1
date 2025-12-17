<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<header class="main-header">
    <div class="container">
        <a href="index.php" class="logo">
            <i class="fas fa-hard-hat"></i>
            <span><?php echo SITE_NAME; ?></span>
        </a>
        <nav class="main-nav">
            <ul class="nav-list">
                <li><a href="index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Home</a></li>
                <li><a href="about.php" class="<?php echo $current_page == 'about.php' ? 'active' : ''; ?>">About</a></li>
                <li><a href="projects.php" class="<?php echo in_array($current_page, ['projects.php', 'project-detail.php']) ? 'active' : ''; ?>">Projects</a></li>
                <li><a href="services.php" class="<?php echo $current_page == 'services.php' ? 'active' : ''; ?>">Services</a></li>
                <li><a href="gallery.php" class="<?php echo $current_page == 'gallery.php' ? 'active' : ''; ?>">Gallery</a></li>
                <li><a href="contact.php" class="<?php echo $current_page == 'contact.php' ? 'active' : ''; ?>">Contact</a></li>
            </ul>
        </nav>
        <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
            <i class="fas fa-bars"></i>
        </button>
    </div>
</header>

<div class="mobile-menu" id="mobileMenu">
    <button class="close-menu" onclick="toggleMobileMenu()">
        <i class="fas fa-times"></i>
    </button>
    <ul class="mobile-nav-list">
        <li><a href="index.php">Home</a></li>
        <li><a href="about.php">About</a></li>
        <li><a href="projects.php">Projects</a></li>
        <li><a href="services.php">Services</a></li>
        <li><a href="gallery.php">Gallery</a></li>
        <li><a href="contact.php">Contact</a></li>
    </ul>
</div>
