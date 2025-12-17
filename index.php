<?php include 'include/config.php'; 
$admin = getAdmin();
$heroImage = $admin['hero_image'] ?? 'assets/images/hero-bg.jpg';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Civil Engineering & Construction</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .hero {
            background: linear-gradient(rgba(30, 58, 95, 0.7), rgba(45, 90, 135, 0.8)), url('<?php echo $heroImage; ?>');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
    </style>
</head>
<body>
    <?php include 'include/header.php'; ?>
    
    <section class="hero">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <h1><?php echo $admin['name'] ?? 'Bhairavnath Construction'; ?></h1>
            <p class="hero-tagline">Building Dreams Into Reality</p>
            <p class="hero-subtitle">Expert Civil Engineering & Construction Services</p>
            <div class="hero-buttons">
                <a href="projects.php" class="btn btn-primary"><i class="fas fa-eye"></i> View Projects</a>
                <a href="contact.php" class="btn btn-outline"><i class="fas fa-phone"></i> Contact Us</a>
            </div>
        </div>
    </section>
    
    <section class="section featured-projects">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Our Work</span>
                <h2>Featured Projects</h2>
                <p>Discover our latest construction achievements</p>
            </div>
            <div class="projects-grid">
                <?php
                $result = db_query("SELECT p.*, (SELECT image_path FROM project_images WHERE project_id = p.id LIMIT 1) as image 
                                    FROM projects p WHERE is_published = 1 ORDER BY created_at DESC LIMIT 3");
                while ($project = db_fetch($result)):
                ?>
                <div class="project-card">
                    <div class="project-image">
                        <img src="<?php echo $project['image'] ?: 'assets/images/placeholder.svg'; ?>" alt="<?php echo $project['name']; ?>">
                        <span class="project-type"><?php echo $project['project_type']; ?></span>
                        <span class="project-status <?php echo strtolower($project['status']); ?>"><?php echo $project['status']; ?></span>
                    </div>
                    <div class="project-info">
                        <h3><?php echo $project['name']; ?></h3>
                        <p class="project-location"><i class="fas fa-map-marker-alt"></i> <?php echo $project['location']; ?></p>
                        <a href="project-detail.php?id=<?php echo $project['id']; ?>" class="btn btn-small">View Details</a>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <div class="text-center" style="margin-top: 40px;">
                <a href="projects.php" class="btn btn-primary"><i class="fas fa-th"></i> View All Projects</a>
            </div>
        </div>
    </section>
    
    <section class="section services-preview bg-light">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">What We Do</span>
                <h2>Our Services</h2>
                <p>Comprehensive construction solutions for all your needs</p>
            </div>
            <div class="services-grid">
                <?php
                $result = db_query("SELECT * FROM services WHERE is_active = 1 LIMIT 6");
                while ($service = db_fetch($result)):
                ?>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="<?php echo $service['icon'] ?: 'fas fa-hard-hat'; ?>"></i>
                    </div>
                    <h3><?php echo $service['name']; ?></h3>
                    <p><?php echo substr($service['description'], 0, 100); ?>...</p>
                </div>
                <?php endwhile; ?>
            </div>
            <div class="text-center" style="margin-top: 40px;">
                <a href="services.php" class="btn btn-primary"><i class="fas fa-cogs"></i> All Services</a>
            </div>
        </div>
    </section>
    
    <section class="section why-choose-us">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Why Us</span>
                <h2>Why Choose Us</h2>
                <p>Experience the difference of working with professionals</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-medal"></i>
                    </div>
                    <h3>Quality Work</h3>
                    <p>We deliver excellence in every project with premium materials and skilled craftsmanship.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Experienced Team</h3>
                    <p>Our team of certified engineers and skilled workers bring decades of combined experience.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3>Transparent Process</h3>
                    <p>Clear communication, honest pricing, and regular updates throughout your project.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>On-Time Delivery</h3>
                    <p>We respect your time and deliver projects within the agreed timeline.</p>
                </div>
            </div>
        </div>
    </section>
    
    <section class="cta-section">
        <div class="container">
            <h2>Ready to Start Your Project?</h2>
            <p>Contact us today for a free consultation and quote</p>
            <a href="contact.php" class="btn btn-primary btn-large"><i class="fas fa-paper-plane"></i> Get In Touch</a>
        </div>
    </section>
    
    <?php include 'include/footer.php'; ?>
    
    <script src="assets/js/main.js"></script>
</body>
</html>
