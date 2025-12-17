<?php
require_once '../include/config.php';
requireLogin();

$admin = getAdmin();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $experience = sanitize($_POST['experience']);
    $certifications = sanitize($_POST['certifications']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $about = sanitize($_POST['about']);
    
    if (!empty($_POST['new_password'])) {
        if (strlen($_POST['new_password']) < 6) {
            $error = 'Password must be at least 6 characters';
        } else {
            $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            $new_password_esc = db_escape_raw($new_password);
            db_query("UPDATE admin SET password = '$new_password_esc' WHERE id = " . $admin['id']);
        }
    }
    
    $profile_image = $admin['profile_image'];
    if (!empty($_FILES['profile_image']['name'])) {
        $uploaded = uploadImage($_FILES['profile_image'], 'profiles');
        if ($uploaded) {
            if ($admin['profile_image']) {
                deleteImage($admin['profile_image']);
            }
            $profile_image = $uploaded;
        }
    }
    
    $hero_image = $admin['hero_image'] ?? 'assets/images/hero-bg.jpg';
    if (!empty($_FILES['hero_image']['name'])) {
        $uploaded = uploadImage($_FILES['hero_image'], 'hero');
        if ($uploaded) {
            if ($admin['hero_image'] && $admin['hero_image'] != 'assets/images/hero-bg.jpg') {
                deleteImage($admin['hero_image']);
            }
            $hero_image = $uploaded;
        }
    }
    
    if (!$error) {
        $name_esc = db_escape_raw($name);
        $experience_esc = db_escape_raw($experience);
        $certifications_esc = db_escape_raw($certifications);
        $phone_esc = db_escape_raw($phone);
        $address_esc = db_escape_raw($address);
        $about_esc = db_escape_raw($about);
        $profile_image_esc = db_escape_raw($profile_image);
        $hero_image_esc = db_escape_raw($hero_image);
        
        $query = "UPDATE admin SET 
                  name = '$name_esc',
                  experience = '$experience_esc',
                  certifications = '$certifications_esc',
                  phone = '$phone_esc',
                  address = '$address_esc',
                  about = '$about_esc',
                  profile_image = '$profile_image_esc',
                  hero_image = '$hero_image_esc'
                  WHERE id = " . $admin['id'];
        
        if (db_query($query)) {
            $_SESSION['admin_name'] = $name;
            $success = 'Profile updated successfully!';
            $admin = getAdmin();
        } else {
            $error = 'Error updating profile';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Admin Panel</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>
        
        <main class="main-content">
            <header class="top-header">
                <h1><i class="fas fa-user"></i> Profile Settings</h1>
            </header>
            
            <div class="dashboard-content">
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data" class="form-card">
                    <div class="profile-header">
                        <div class="profile-image-container">
                            <img src="<?php echo $admin['profile_image'] ?: '../assets/images/placeholder.svg'; ?>" 
                                 alt="Profile" id="profilePreview">
                            <label class="change-photo-btn">
                                <i class="fas fa-camera"></i>
                                <input type="file" name="profile_image" accept="image/*" 
                                       onchange="previewProfile(this)" style="display:none">
                            </label>
                        </div>
                        <div class="profile-info">
                            <h2><?php echo $admin['name']; ?></h2>
                            <p><?php echo $admin['email']; ?></p>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Name</label>
                            <input type="text" name="name" class="form-control" 
                                   value="<?php echo $admin['name']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-briefcase"></i> Experience</label>
                            <input type="text" name="experience" class="form-control" 
                                   placeholder="e.g., 15+ Years"
                                   value="<?php echo $admin['experience']; ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-phone"></i> Phone</label>
                            <input type="text" name="phone" class="form-control" 
                                   value="<?php echo $admin['phone']; ?>">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-certificate"></i> Certifications</label>
                            <input type="text" name="certifications" class="form-control" 
                                   placeholder="e.g., Licensed Civil Engineer, ISO Certified"
                                   value="<?php echo $admin['certifications']; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-map-marker-alt"></i> Address</label>
                        <input type="text" name="address" class="form-control" 
                               value="<?php echo $admin['address']; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-info-circle"></i> About</label>
                        <textarea name="about" class="form-control" rows="5"><?php echo $admin['about']; ?></textarea>
                    </div>
                    
                    <hr style="margin: 30px 0; border: none; border-top: 1px solid #e0e0e0;">
                    
                    <h3 style="margin-bottom: 20px;"><i class="fas fa-image"></i> Homepage Hero Background</h3>
                    
                    <div class="form-group">
                        <div class="hero-preview-container">
                            <img src="<?php echo ($admin['hero_image'] && $admin['hero_image'] != 'assets/images/hero-bg.jpg') ? $admin['hero_image'] : '../assets/images/hero-bg.jpg'; ?>" 
                                 alt="Hero Background" id="heroPreview">
                            <div class="hero-overlay-text">
                                <span>Current Hero Background</span>
                            </div>
                        </div>
                        <div class="hero-upload-area" onclick="document.getElementById('heroInput').click()">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Click to change hero background image</p>
                            <small>Recommended: 1920x1080 pixels, JPG or PNG</small>
                        </div>
                        <input type="file" id="heroInput" name="hero_image" accept="image/*" 
                               style="display:none" onchange="previewHero(this)">
                    </div>
                    
                    <hr style="margin: 30px 0; border: none; border-top: 1px solid #e0e0e0;">
                    
                    <h3 style="margin-bottom: 20px;"><i class="fas fa-lock"></i> Change Password</h3>
                    
                    <div class="form-group">
                        <label>New Password (leave blank to keep current)</label>
                        <input type="password" name="new_password" class="form-control" 
                               placeholder="Enter new password (min 6 characters)">
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
    
    <script>
    function previewProfile(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profilePreview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    function previewHero(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('heroPreview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>
    
    <style>
    .profile-header {
        display: flex;
        align-items: center;
        gap: 25px;
        margin-bottom: 30px;
        padding-bottom: 30px;
        border-bottom: 1px solid #e0e0e0;
    }
    .profile-image-container {
        position: relative;
        width: 120px;
        height: 120px;
    }
    .profile-image-container img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #1e3a5f;
    }
    .change-photo-btn {
        position: absolute;
        bottom: 5px;
        right: 5px;
        width: 36px;
        height: 36px;
        background: #1e3a5f;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.3s;
    }
    .change-photo-btn:hover {
        background: #2d5a87;
    }
    .profile-info h2 {
        color: #1e3a5f;
        margin-bottom: 5px;
    }
    .profile-info p {
        color: #666;
    }
    .hero-preview-container {
        position: relative;
        width: 100%;
        height: 200px;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 15px;
    }
    .hero-preview-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .hero-overlay-text {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0,0,0,0.7));
        color: white;
        padding: 20px;
        text-align: center;
    }
    .hero-upload-area {
        border: 2px dashed #ddd;
        border-radius: 12px;
        padding: 30px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        background: #f9f9f9;
    }
    .hero-upload-area:hover {
        border-color: #1e3a5f;
        background: #e8eaf6;
    }
    .hero-upload-area i {
        font-size: 40px;
        color: #1e3a5f;
        margin-bottom: 10px;
    }
    .hero-upload-area p {
        margin: 0;
        color: #333;
        font-weight: 500;
    }
    .hero-upload-area small {
        color: #999;
    }
    </style>
</body>
</html>
