<?php
require_once '../include/config.php';
requireLogin();

$project = null;
$images = [];
$videos = [];
$isEdit = false;

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $result = db_query("SELECT * FROM projects WHERE id = $id");
    $project = db_fetch($result);
    
    if ($project) {
        $isEdit = true;
        $mediaResult = db_query("SELECT * FROM project_images WHERE project_id = $id ORDER BY is_primary DESC, created_at ASC");
        while ($media = db_fetch($mediaResult)) {
            $media_type = $media['media_type'] ?? 'image';
            if ($media_type == 'video') {
                $videos[] = $media;
            } else {
                $images[] = $media;
            }
        }
    }
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $project_type = sanitize($_POST['project_type']);
    $location = sanitize($_POST['location']);
    $duration = sanitize($_POST['duration']);
    $budget = sanitize($_POST['budget']);
    $description = sanitize($_POST['description']);
    $status = sanitize($_POST['status']);
    $is_published = isset($_POST['is_published']) ? 1 : 0;
    
    if (empty($name) || empty($project_type)) {
        $error = 'Project name and type are required';
    } else {
        $name_esc = db_escape_raw($name);
        $project_type_esc = db_escape_raw($project_type);
        $location_esc = db_escape_raw($location);
        $duration_esc = db_escape_raw($duration);
        $budget_esc = db_escape_raw($budget);
        $description_esc = db_escape_raw($description);
        $status_esc = db_escape_raw($status);
        
        if ($isEdit) {
            $query = "UPDATE projects SET 
                      name = '$name_esc', 
                      project_type = '$project_type_esc', 
                      location = '$location_esc', 
                      duration = '$duration_esc', 
                      budget = '$budget_esc', 
                      description = '$description_esc', 
                      status = '$status_esc', 
                      is_published = $is_published 
                      WHERE id = $id";
            db_query($query);
            $project_id = $id;
            $msg = 'updated';
        } else {
            $query = "INSERT INTO projects (name, project_type, location, duration, budget, description, status, is_published) 
                      VALUES ('$name_esc', '$project_type_esc', '$location_esc', '$duration_esc', '$budget_esc', '$description_esc', '$status_esc', $is_published)";
            db_query($query);
            $project_id = db_insert_id();
            $msg = 'created';
        }
        
        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['images']['error'][$key] == 0) {
                    $file = [
                        'name' => $_FILES['images']['name'][$key],
                        'tmp_name' => $tmp_name,
                        'error' => $_FILES['images']['error'][$key]
                    ];
                    $uploaded_path = uploadImage($file, 'projects');
                    if ($uploaded_path) {
                        $uploaded_path_esc = db_escape_raw($uploaded_path);
                        $is_primary = ($key == 0 && !$isEdit) ? 1 : 0;
                        db_query("INSERT INTO project_images (project_id, image_path, media_type, is_primary) 
                                 VALUES ($project_id, '$uploaded_path_esc', 'image', $is_primary)");
                    }
                }
            }
        }
        
        if (!empty($_FILES['videos']['name'][0])) {
            foreach ($_FILES['videos']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['videos']['error'][$key] == 0) {
                    $file = [
                        'name' => $_FILES['videos']['name'][$key],
                        'tmp_name' => $tmp_name,
                        'error' => $_FILES['videos']['error'][$key]
                    ];
                    $result = uploadMedia($file, 'projects');
                    if ($result['success'] && $result['type'] == 'video') {
                        $uploaded_path_esc = db_escape_raw($result['path']);
                        db_query("INSERT INTO project_images (project_id, image_path, media_type, is_primary) 
                                 VALUES ($project_id, '$uploaded_path_esc', 'video', 0)");
                    }
                }
            }
        }
        
        redirect('projects.php?msg=' . $msg);
    }
}

if (isset($_GET['delete_image'])) {
    $img_id = (int)$_GET['delete_image'];
    $result = db_query("SELECT * FROM project_images WHERE id = $img_id");
    $img = db_fetch($result);
    if ($img) {
        deleteImage($img['image_path']);
        db_query("DELETE FROM project_images WHERE id = $img_id");
    }
    redirect('project-form.php?id=' . $_GET['id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEdit ? 'Edit' : 'Add'; ?> Project - Admin Panel</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <style>
        .toggle-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .media-section {
            margin-bottom: 25px;
        }
        .media-section h4 {
            margin: 20px 0 10px;
            color: #333;
        }
        .video-preview-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            background: #000;
        }
        .video-preview-item video {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        .video-preview-item .remove-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background: #c62828;
            color: white;
            border: none;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .video-badge {
            position: absolute;
            bottom: 5px;
            left: 5px;
            background: #FF5722;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 11px;
        }
        .upload-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        .upload-tab {
            padding: 10px 20px;
            border: 2px solid #ddd;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            background: white;
        }
        .upload-tab.active {
            border-color: #1a237e;
            background: #e8eaf6;
        }
        .upload-tab i {
            margin-right: 8px;
        }
        .upload-panel {
            display: none;
        }
        .upload-panel.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>
        
        <main class="main-content">
            <header class="top-header">
                <h1><i class="fas fa-<?php echo $isEdit ? 'edit' : 'plus'; ?>"></i> <?php echo $isEdit ? 'Edit' : 'Add'; ?> Project</h1>
                <a href="projects.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Projects
                </a>
            </header>
            
            <div class="dashboard-content">
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data" class="form-card">
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-project-diagram"></i> Project Name *</label>
                            <input type="text" name="name" class="form-control" required
                                   value="<?php echo $project['name'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-tags"></i> Project Type *</label>
                            <select name="project_type" class="form-control" required>
                                <option value="">Select Type</option>
                                <?php 
                                $types = ['Road', 'Residential', 'Commercial', 'Infrastructure'];
                                foreach ($types as $type):
                                    $selected = (isset($project['project_type']) && $project['project_type'] == $type) ? 'selected' : '';
                                ?>
                                <option value="<?php echo $type; ?>" <?php echo $selected; ?>><?php echo $type; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-map-marker-alt"></i> Location</label>
                            <input type="text" name="location" class="form-control"
                                   value="<?php echo $project['location'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-clock"></i> Duration</label>
                            <input type="text" name="duration" class="form-control" placeholder="e.g., 12 Months"
                                   value="<?php echo $project['duration'] ?? ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-rupee-sign"></i> Budget (Optional)</label>
                            <input type="text" name="budget" class="form-control" placeholder="e.g., ₹5 Crore"
                                   value="<?php echo $project['budget'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-tasks"></i> Status</label>
                            <select name="status" class="form-control">
                                <option value="Ongoing" <?php echo (isset($project['status']) && $project['status'] == 'Ongoing') ? 'selected' : ''; ?>>Ongoing</option>
                                <option value="Completed" <?php echo (isset($project['status']) && $project['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-align-left"></i> Description</label>
                        <textarea name="description" class="form-control" rows="5"><?php echo $project['description'] ?? ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-photo-video"></i> Project Media</label>
                        
                        <div class="upload-tabs">
                            <div class="upload-tab active" onclick="switchUploadTab('images')">
                                <i class="fas fa-images"></i> Images
                            </div>
                            <div class="upload-tab" onclick="switchUploadTab('videos')">
                                <i class="fas fa-video"></i> Videos
                            </div>
                        </div>
                        
                        <div id="imagesPanel" class="upload-panel active">
                            <div class="image-upload-area" onclick="document.getElementById('images').click()">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Click to upload images</p>
                                <small>JPG, PNG, GIF, WEBP (Multiple files allowed)</small>
                            </div>
                            <input type="file" id="images" name="images[]" multiple accept="image/*" style="display:none" onchange="previewImages(this)">
                            <div class="image-preview-grid" id="newImagePreviews"></div>
                        </div>
                        
                        <div id="videosPanel" class="upload-panel">
                            <div class="image-upload-area" onclick="document.getElementById('videos').click()">
                                <i class="fas fa-film"></i>
                                <p>Click to upload videos</p>
                                <small>MP4, WEBM, OGG, MOV, AVI, MKV (Multiple files allowed)</small>
                            </div>
                            <input type="file" id="videos" name="videos[]" multiple accept="video/*" style="display:none" onchange="previewVideos(this)">
                            <div class="image-preview-grid" id="newVideoPreviews"></div>
                        </div>
                        
                        <?php if (!empty($images)): ?>
                        <div class="media-section">
                            <h4><i class="fas fa-images"></i> Current Images</h4>
                            <div class="image-preview-grid">
                                <?php foreach ($images as $img): ?>
                                <div class="image-preview-item">
                                    <img src="<?php echo $img['image_path']; ?>" alt="Project Image">
                                    <a href="?id=<?php echo $id; ?>&delete_image=<?php echo $img['id']; ?>" 
                                       class="remove-btn" onclick="return confirm('Delete this image?')">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($videos)): ?>
                        <div class="media-section">
                            <h4><i class="fas fa-video"></i> Current Videos</h4>
                            <div class="image-preview-grid">
                                <?php foreach ($videos as $vid): ?>
                                <div class="video-preview-item">
                                    <video src="<?php echo $vid['image_path']; ?>" muted></video>
                                    <span class="video-badge"><i class="fas fa-play"></i> Video</span>
                                    <a href="?id=<?php echo $id; ?>&delete_image=<?php echo $vid['id']; ?>" 
                                       class="remove-btn" onclick="return confirm('Delete this video?')">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label class="toggle-label">
                            <span><i class="fas fa-globe"></i> Publish on Website</span>
                            <label class="toggle-switch">
                                <input type="checkbox" name="is_published" 
                                       <?php echo (!isset($project['is_published']) || $project['is_published']) ? 'checked' : ''; ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </label>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> <?php echo $isEdit ? 'Update' : 'Save'; ?> Project
                        </button>
                        <a href="projects.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>
    
    <script>
    function switchUploadTab(tab) {
        document.querySelectorAll('.upload-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.upload-panel').forEach(p => p.classList.remove('active'));
        
        if (tab === 'images') {
            document.querySelector('.upload-tab:first-child').classList.add('active');
            document.getElementById('imagesPanel').classList.add('active');
        } else {
            document.querySelector('.upload-tab:last-child').classList.add('active');
            document.getElementById('videosPanel').classList.add('active');
        }
    }
    
    function previewImages(input) {
        const preview = document.getElementById('newImagePreviews');
        preview.innerHTML = '';
        
        if (input.files) {
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'image-preview-item';
                    div.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                    preview.appendChild(div);
                }
                reader.readAsDataURL(file);
            });
        }
    }
    
    function previewVideos(input) {
        const preview = document.getElementById('newVideoPreviews');
        preview.innerHTML = '';
        
        if (input.files) {
            Array.from(input.files).forEach(file => {
                const div = document.createElement('div');
                div.className = 'video-preview-item';
                div.innerHTML = `
                    <video src="${URL.createObjectURL(file)}" muted></video>
                    <span class="video-badge"><i class="fas fa-play"></i> ${file.name}</span>
                `;
                preview.appendChild(div);
            });
        }
    }
    </script>
</body>
</html>
