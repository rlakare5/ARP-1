<?php include 'include/config.php';

$admin = getAdmin();
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $message = sanitize($_POST['message']);
    
    if (empty($name) || empty($email) || empty($message)) {
        $error = 'Please fill in all fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
        $name_esc = db_escape_raw($name);
        $email_esc = db_escape_raw($email);
        $message_esc = db_escape_raw($message);
        
        $query = "INSERT INTO inquiries (name, email, message) VALUES ('$name_esc', '$email_esc', '$message_esc')";
        if (db_query($query)) {
            $success = 'Thank you for your message! We will get back to you soon.';
        } else {
            $error = 'Something went wrong. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - <?php echo SITE_NAME; ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'include/header.php'; ?>
    
    <section class="page-banner">
        <div class="container">
            <h1>Contact Us</h1>
            <nav class="breadcrumb">
                <a href="index.php">Home</a> / <span>Contact</span>
            </nav>
        </div>
    </section>
    
    <section class="section contact-section">
        <div class="container">
            <div class="contact-grid">
                <div class="contact-form-container">
                    <h2><i class="fas fa-envelope"></i> Send Us a Message</h2>
                    <p>Have a project in mind? Get in touch with us and let's discuss how we can help.</p>
                    
                    <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="contact-form">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Your Name</label>
                            <input type="text" name="name" class="form-control" required 
                                   placeholder="Enter your full name"
                                   value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i> Email Address</label>
                            <input type="email" name="email" class="form-control" required 
                                   placeholder="Enter your email address"
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-comment"></i> Message</label>
                            <textarea name="message" class="form-control" rows="6" required 
                                      placeholder="Describe your project or inquiry..."><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-large">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                    </form>
                </div>
                
                <div class="contact-info-container">
                    <h2><i class="fas fa-info-circle"></i> Contact Information</h2>
                    <p>Reach out to us through any of the following channels.</p>
                    
                    <div class="contact-info-list">
                        <div class="contact-info-item">
                            <div class="contact-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="contact-details">
                                <h4>Phone</h4>
                                <p><?php echo $admin['phone'] ?? '+91 9876543210'; ?></p>
                            </div>
                        </div>
                        
                        <div class="contact-info-item">
                            <div class="contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact-details">
                                <h4>Email</h4>
                                <p><?php echo $admin['email'] ?? 'info@bhairavnath.com'; ?></p>
                            </div>
                        </div>
                        
                        <div class="contact-info-item">
                            <div class="contact-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="contact-details">
                                <h4>Office Address</h4>
                                <p><?php echo $admin['address'] ?? 'Maharashtra, India'; ?></p>
                            </div>
                        </div>
                        
                        <div class="contact-info-item">
                            <div class="contact-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="contact-details">
                                <h4>Working Hours</h4>
                                <p>Mon - Sat: 9:00 AM - 6:00 PM</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="social-links-contact">
                        <h4>Follow Us</h4>
                        <div class="social-icons">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-whatsapp"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <section class="map-section">
        <div class="map-placeholder">
            <i class="fas fa-map-marked-alt"></i>
            <p>Google Maps Integration</p>
            <small>Add your Google Maps embed code here</small>
        </div>
    </section>
    
    <?php include 'include/footer.php'; ?>
    
    <script src="assets/js/main.js"></script>
</body>
</html>
