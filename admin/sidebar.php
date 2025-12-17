<aside class="sidebar">
    <div class="sidebar-header">
        <h2><i class="fas fa-hard-hat"></i> ARP</h2>
        <p>Admin Panel</p>
    </div>
    <nav class="sidebar-nav">
        <a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="projects.php" class="nav-link <?php echo in_array(basename($_SERVER['PHP_SELF']), ['projects.php', 'project-form.php']) ? 'active' : ''; ?>">
            <i class="fas fa-building"></i> Projects
        </a>
        <a href="services.php" class="nav-link <?php echo in_array(basename($_SERVER['PHP_SELF']), ['services.php', 'service-form.php']) ? 'active' : ''; ?>">
            <i class="fas fa-cogs"></i> Services
        </a>
        <a href="gallery.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'gallery.php' ? 'active' : ''; ?>">
            <i class="fas fa-images"></i> Project Gallery
        </a>
        <a href="media.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'media.php' ? 'active' : ''; ?>">
            <i class="fas fa-photo-video"></i> Media Gallery
        </a>
        <a href="inquiries.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'inquiries.php' ? 'active' : ''; ?>">
            <i class="fas fa-envelope"></i> Inquiries
            <?php 
            $unread = getUnreadInquiries();
            if ($unread > 0): 
            ?>
            <span class="badge-count"><?php echo $unread; ?></span>
            <?php endif; ?>
        </a>
        <a href="profile.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
            <i class="fas fa-user"></i> Profile
        </a>
        <div class="nav-divider"></div>
        <a href="../index.php" class="nav-link" target="_blank">
            <i class="fas fa-external-link-alt"></i> View Website
        </a>
        <a href="logout.php" class="nav-link logout">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>
</aside>
