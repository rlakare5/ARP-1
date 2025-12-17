<?php $admin = getAdmin(); ?>
<footer class="main-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <h3><i class="fas fa-hard-hat"></i> <?php echo SITE_NAME; ?></h3>
                <p><?php echo substr($admin['about'] ?? 'Building quality construction projects with trust and transparency.', 0, 150); ?></p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
            <div class="footer-col">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="projects.php">Our Projects</a></li>
                    <li><a href="services.php">Services</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Our Services</h4>
                <ul>
                    <?php
                    $services = db_query("SELECT name FROM services WHERE is_active = 1 LIMIT 5");
                    while ($s = db_fetch($services)):
                    ?>
                    <li><a href="services.php"><?php echo $s['name']; ?></a></li>
                    <?php endwhile; ?>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Contact Info</h4>
                <ul class="contact-info">
                    <li><i class="fas fa-phone"></i> <?php echo $admin['phone'] ?? '+91 9876543210'; ?></li>
                    <li><i class="fas fa-envelope"></i> <?php echo $admin['email'] ?? 'info@bhairavnath.com'; ?></li>
                    <li><i class="fas fa-map-marker-alt"></i> <?php echo $admin['address'] ?? 'Maharashtra, India'; ?></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All Rights Reserved.</p>
            <p><a href="admin/login.php">Admin Login</a></p>
        </div>
    </div>
</footer>
