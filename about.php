<?php include 'include/config.php'; 
$admin = getAdmin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - <?php echo SITE_NAME; ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'include/header.php'; ?>
    
    <!-- Page Banner -->
    <section class="page-banner">
        <div class="container">
            <h1>About Us</h1>
            <nav class="breadcrumb">
                <a href="index.php">Home</a> / <span>About</span>
            </nav>
        </div>
    </section>
    
    <!-- About Content -->
    <section class="section about-section">
        <div class="container">
            <div class="about-grid">
                <div class="about-image">
                    <img src="<?php echo $admin['profile_image'] ?? 'assets/images/about-placeholder.jpg'; ?>" alt="About Us">
                    <div class="experience-badge">
                        <span class="years"><?php echo preg_replace('/[^0-9]/', '', $admin['experience'] ?? '15'); ?>+</span>
                        <span class="text">Years Experience</span>
                    </div>
                </div>
                <div class="about-content">
                    <span class="section-tag">Who We Are</span>
                    <h2><?php echo $admin['name'] ?? 'Bhairavnath Construction'; ?></h2>
                    <p class="lead"><?php echo $admin['about'] ?? 'We are a trusted construction company specializing in building construction and road projects.'; ?></p>
                    
                    <div class="about-features">
                        <div class="about-feature">
                            <i class="fas fa-check-circle"></i>
                            <span>Licensed & Certified Engineers</span>
                        </div>
                        <div class="about-feature">
                            <i class="fas fa-check-circle"></i>
                            <span>Quality Construction Materials</span>
                        </div>
                        <div class="about-feature">
                            <i class="fas fa-check-circle"></i>
                            <span>On-Time Project Delivery</span>
                        </div>
                        <div class="about-feature">
                            <i class="fas fa-check-circle"></i>
                            <span>Transparent Pricing</span>
                        </div>
                    </div>
                    
                    <a href="contact.php" class="btn btn-primary"><i class="fas fa-phone"></i> Contact Us</a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Experience Timeline -->
    <section class="section timeline-section bg-light">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Our Journey</span>
                <h2>Experience & Milestones</h2>
            </div>
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-icon"><i class="fas fa-flag"></i></div>
                    <div class="timeline-content">
                        <h3>Company Founded</h3>
                        <p>Started with a vision to provide quality construction services.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-icon"><i class="fas fa-building"></i></div>
                    <div class="timeline-content">
                        <h3>First Major Project</h3>
                        <p>Successfully completed our first large-scale residential complex.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-icon"><i class="fas fa-road"></i></div>
                    <div class="timeline-content">
                        <h3>Infrastructure Expansion</h3>
                        <p>Expanded into road construction and infrastructure development.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-icon"><i class="fas fa-award"></i></div>
                    <div class="timeline-content">
                        <h3>Industry Recognition</h3>
                        <p>Received certifications and recognition for quality work.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Certifications -->
    <section class="section certifications-section">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Credentials</span>
                <h2>Certifications & Expertise</h2>
            </div>
            <div class="cert-grid">
                <?php
                $certs = explode(',', $admin['certifications'] ?? 'Licensed Civil Engineer, ISO Certified, Quality Approved');
                foreach ($certs as $cert):
                    $cert = trim($cert);
                    if (!empty($cert)):
                ?>
                <div class="cert-card">
                    <i class="fas fa-certificate"></i>
                    <span><?php echo $cert; ?></span>
                </div>
                <?php 
                    endif;
                endforeach; 
                ?>
            </div>
        </div>
    </section>
    
    <!-- Vision & Mission -->
    <section class="section vision-mission bg-dark">
        <div class="container">
            <div class="vm-grid">
                <div class="vm-card">
                    <div class="vm-icon"><i class="fas fa-eye"></i></div>
                    <h3>Our Vision</h3>
                    <p>To be the most trusted name in construction, known for quality, integrity, and customer satisfaction.</p>
                </div>
                <div class="vm-card">
                    <div class="vm-icon"><i class="fas fa-bullseye"></i></div>
                    <h3>Our Mission</h3>
                    <p>To deliver exceptional construction services that exceed client expectations while maintaining the highest safety and quality standards.</p>
                </div>
            </div>
        </div>
    </section>
    
    <?php include 'include/footer.php'; ?>
    
    <script src="assets/js/main.js"></script>
</body>
</html>
