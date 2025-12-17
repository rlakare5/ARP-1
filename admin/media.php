<?php
require_once '../include/config.php';
requireLogin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'upload') {
        $title = sanitize($_POST['title'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        
        if (!empty($_FILES['media_file']['name'])) {
            $result = uploadMedia($_FILES['media_file'], 'gallery');
            
            if ($result['success']) {
                $file_path = db_escape_raw($result['path']);
                $file_type = $result['type'];
                $title_esc = db_escape_raw($title);
                $description_esc = db_escape_raw($description);
                
                $query = "INSERT INTO media_gallery (title, description, file_path, file_type, is_active) 
                          VALUES ('$title_esc', '$description_esc', '$file_path', '$file_type', 1)";
                          
                if (db_query($query)) {
                    $success = ucfirst($file_type) . ' uploaded successfully!';
                } else {
                    $error = 'Database error occurred';
                }
            } else {
                $error = $result['error'];
            }
        } else {
            $error = 'Please select a file to upload';
        }
    }
    
    if ($_POST['action'] == 'update') {
        $id = (int)$_POST['id'];
        $title_esc = db_escape_raw(sanitize($_POST['title']));
        $description_esc = db_escape_raw(sanitize($_POST['description']));
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        db_query("UPDATE media_gallery SET title = '$title_esc', description = '$description_esc', is_active = $is_active WHERE id = $id");
        $success = 'Media updated successfully!';
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $result = db_query("SELECT * FROM media_gallery WHERE id = $id");
    $media = db_fetch($result);
    if ($media) {
        deleteImage($media['file_path']);
        db_query("DELETE FROM media_gallery WHERE id = $id");
        redirect('media.php?msg=deleted');
    }
}

if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    db_query("UPDATE media_gallery SET is_active = NOT is_active WHERE id = $id");
    redirect('media.php');
}

$filter = isset($_GET['filter']) ? sanitize($_GET['filter']) : '';
$where = "";
if ($filter == 'image' || $filter == 'video') {
    $where = "WHERE file_type = '$filter'";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Media Gallery - Admin Panel</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <style>
        .upload-section {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .upload-form {
            display: grid;
            grid-template-columns: 1fr 1fr 2fr auto;
            gap: 15px;
            align-items: end;
        }
        .upload-form .form-group {
            margin-bottom: 0;
        }
        .file-input-wrapper {
            position: relative;
        }
        .file-input-wrapper input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        .file-input-label {
            display: block;
            padding: 12px 20px;
            background: #f8f9fa;
            border: 2px dashed #ddd;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .file-input-label:hover {
            border-color: var(--primary-color, #2196F3);
            background: #e3f2fd;
        }
        .file-input-label i {
            margin-right: 8px;
        }
        .media-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        .media-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .media-card:hover {
            transform: translateY(-5px);
        }
        .media-preview {
            height: 200px;
            position: relative;
            background: #f0f0f0;
        }
        .media-preview img,
        .media-preview video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .media-type-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .media-type-badge.image {
            background: #4CAF50;
            color: white;
        }
        .media-type-badge.video {
            background: #FF5722;
            color: white;
        }
        .media-status {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid white;
        }
        .media-status.active {
            background: #4CAF50;
        }
        .media-status.inactive {
            background: #9e9e9e;
        }
        .media-info {
            padding: 15px;
        }
        .media-info h4 {
            margin: 0 0 5px;
            font-size: 16px;
            color: #333;
        }
        .media-info p {
            margin: 0 0 15px;
            color: #666;
            font-size: 13px;
            height: 40px;
            overflow: hidden;
        }
        .media-actions {
            display: flex;
            gap: 8px;
        }
        .media-actions .btn {
            flex: 1;
            padding: 8px;
            font-size: 13px;
        }
        .filter-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .filter-tabs a {
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }
        .filter-tabs a.active {
            background: var(--primary-color, #2196F3);
            color: white;
        }
        .filter-tabs a:not(.active) {
            background: #f0f0f0;
            color: #666;
        }
        .filter-tabs a:not(.active):hover {
            background: #e0e0e0;
        }
        @media (max-width: 992px) {
            .upload-form {
                grid-template-columns: 1fr;
            }
        }
        .video-icon-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 50px;
            color: white;
            text-shadow: 0 2px 10px rgba(0,0,0,0.5);
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>
        
        <main class="main-content">
            <header class="top-header">
                <h1><i class="fas fa-photo-video"></i> Media Gallery</h1>
            </header>
            
            <div class="dashboard-content">
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success || isset($_GET['msg'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> 
                        <?php echo $success ?: 'Media deleted successfully!'; ?>
                    </div>
                <?php endif; ?>
                
                <div class="upload-section">
                    <h3 style="margin-bottom: 20px;"><i class="fas fa-cloud-upload-alt"></i> Upload New Media</h3>
                    <form method="POST" enctype="multipart/form-data" class="upload-form">
                        <input type="hidden" name="action" value="upload">
                        
                        <div class="form-group">
                            <label>Title (Optional)</label>
                            <input type="text" name="title" class="form-control" placeholder="Enter title...">
                        </div>
                        
                        <div class="form-group">
                            <label>Description (Optional)</label>
                            <input type="text" name="description" class="form-control" placeholder="Brief description...">
                        </div>
                        
                        <div class="form-group">
                            <label>Select File *</label>
                            <div class="file-input-wrapper">
                                <label class="file-input-label" id="fileLabel">
                                    <i class="fas fa-cloud-upload-alt"></i> Choose Image or Video
                                </label>
                                <input type="file" name="media_file" accept="image/*,video/*" required 
                                       onchange="updateFileName(this)">
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Upload
                        </button>
                    </form>
                    <small style="display: block; margin-top: 15px; color: #666;">
                        <i class="fas fa-info-circle"></i> Supported formats: JPG, PNG, GIF, WEBP, MP4, WEBM, OGG, MOV, AVI, MKV
                    </small>
                </div>
                
                <div class="filter-tabs">
                    <a href="media.php" class="<?php echo !$filter ? 'active' : ''; ?>">
                        <i class="fas fa-th"></i> All
                    </a>
                    <a href="?filter=image" class="<?php echo $filter == 'image' ? 'active' : ''; ?>">
                        <i class="fas fa-image"></i> Images
                    </a>
                    <a href="?filter=video" class="<?php echo $filter == 'video' ? 'active' : ''; ?>">
                        <i class="fas fa-video"></i> Videos
                    </a>
                </div>
                
                <div class="media-grid">
                    <?php
                    $result = db_query("SELECT * FROM media_gallery $where ORDER BY created_at DESC");
                    
                    $hasMedia = false;
                    while ($row = db_fetch($result)):
                        $hasMedia = true;
                    ?>
                    <div class="media-card">
                        <div class="media-preview">
                            <?php if ($row['file_type'] == 'video'): ?>
                                <video src="<?php echo $row['file_path']; ?>" muted></video>
                                <div class="video-icon-overlay"><i class="fas fa-play-circle"></i></div>
                            <?php else: ?>
                                <img src="<?php echo $row['file_path']; ?>" alt="<?php echo $row['title'] ?: 'Gallery Image'; ?>">
                            <?php endif; ?>
                            <span class="media-type-badge <?php echo $row['file_type']; ?>">
                                <?php echo $row['file_type']; ?>
                            </span>
                            <span class="media-status <?php echo $row['is_active'] ? 'active' : 'inactive'; ?>"></span>
                        </div>
                        <div class="media-info">
                            <h4><?php echo $row['title'] ?: 'Untitled'; ?></h4>
                            <p><?php echo $row['description'] ?: 'No description'; ?></p>
                            <div class="media-actions">
                                <a href="?toggle=<?php echo $row['id']; ?>" class="btn <?php echo $row['is_active'] ? 'btn-warning' : 'btn-success'; ?>">
                                    <i class="fas fa-<?php echo $row['is_active'] ? 'eye-slash' : 'eye'; ?>"></i>
                                    <?php echo $row['is_active'] ? 'Hide' : 'Show'; ?>
                                </a>
                                <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger" 
                                   onclick="return confirm('Are you sure you want to delete this <?php echo $row['file_type']; ?>?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php 
                    endwhile;
                    
                    if (!$hasMedia):
                    ?>
                    <div style="grid-column: 1/-1; text-align: center; padding: 60px;">
                        <i class="fas fa-photo-video" style="font-size: 60px; color: #ccc;"></i>
                        <p style="margin-top: 15px; color: #999;">No media found. Upload your first image or video!</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    
    <script>
    function updateFileName(input) {
        const label = document.getElementById('fileLabel');
        if (input.files && input.files[0]) {
            const fileName = input.files[0].name;
            const fileType = input.files[0].type.startsWith('video') ? 'video' : 'image';
            const icon = fileType === 'video' ? 'fa-video' : 'fa-image';
            label.innerHTML = `<i class="fas ${icon}"></i> ${fileName}`;
        }
    }
    </script>
</body>
</html>
