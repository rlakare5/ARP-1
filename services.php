<?php include 'include/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services - <?php echo SITE_NAME; ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'include/header.php'; ?>
    
    <section class="page-banner">
        <div class="container">
            <h1>Our Services</h1>
            <nav class="breadcrumb">
                <a href="index.php">Home</a> / <span>Services</span>
            </nav>
        </div>
    </section>
    
    <section class="section services-page">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">What We Offer</span>
                <h2>Comprehensive Construction Services</h2>
                <p>From planning to completion, we provide end-to-end construction solutions</p>
            </div>
            
            <div class="services-detail-grid">
                <?php
                $result = db_query("SELECT * FROM services WHERE is_active = 1 ORDER BY id ASC");
                while ($service = db_fetch($result)):
                ?>
                <div class="service-detail-card">
                    <div class="service-icon-large">
                        <i class="<?php echo $service['icon'] ?: 'fas fa-hard-hat'; ?>"></i>
                    </div>
                    <div class="service-content">
                        <h3><?php echo $service['name']; ?></h3>
                        <p><?php echo $service['description']; ?></p>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>
    
    <section class="section why-choose-us bg-light">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Why Us</span>
                <h2>Why Choose Our Services</h2>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-medal"></i></div>
                    <h3>Quality Work</h3>
                    <p>Premium materials and skilled craftsmanship in every project.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-users"></i></div>
                    <h3>Experienced Team</h3>
                    <p>Certified engineers with decades of combined experience.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-handshake"></i></div>
                    <h3>Transparent Process</h3>
                    <p>Clear communication and honest pricing throughout.</p>
                </div>
            </div>
        </div>
    </section>
    
    <section class="cta-section">
        <div class="container">
            <h2>Need Our Services?</h2>
            <p>Contact us for a free consultation and project estimate</p>
            <a href="contact.php" class="btn btn-primary btn-large"><i class="fas fa-paper-plane"></i> Get a Quote</a>
        </div>
    </section>
    
    <?php include 'include/footer.php'; ?>
    
    <script src="assets/js/main.js"></script>
</body>
</html>
