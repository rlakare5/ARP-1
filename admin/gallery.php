<?php
require_once '../include/config.php';
requireLogin();

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $img = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM project_images WHERE id = $id"));
    if ($img) {
        deleteImage('../' . $img['image_path']);
        mysqli_query($con, "DELETE FROM project_images WHERE id = $id");
    }
    redirect('gallery.php?msg=deleted');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery - Admin Panel</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>
        
        <main class="main-content">
            <header class="top-header">
                <h1><i class="fas fa-images"></i> Gallery</h1>
            </header>
            
            <div class="dashboard-content">
                <?php if (isset($_GET['msg'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> Image deleted successfully!
                    </div>
                <?php endif; ?>
                
                <div class="gallery-grid">
                    <?php
                    $result = mysqli_query($con, "SELECT pi.*, p.name as project_name 
                                                   FROM project_images pi 
                                                   LEFT JOIN projects p ON pi.project_id = p.id 
                                                   ORDER BY pi.created_at DESC");
                    
                    if (mysqli_num_rows($result) > 0):
                        while ($row = mysqli_fetch_assoc($result)):
                    ?>
                    <div class="gallery-item">
                        <img src="../<?php echo $row['image_path']; ?>" alt="Gallery Image">
                        <div class="overlay">
                            <small><?php echo $row['project_name'] ?: 'Unknown Project'; ?></small>
                            <a href="?delete=<?php echo $row['id']; ?>" class="btn-icon delete" 
                               onclick="return confirm('Delete this image?')" 
                               style="position: absolute; top: 10px; right: 10px; background: #c62828; color: white;">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <div class="text-center" style="grid-column: 1/-1; padding: 60px;">
                        <i class="fas fa-images" style="font-size: 60px; color: #ccc;"></i>
                        <p style="margin-top: 15px; color: #999;">No images in gallery</p>
                        <a href="project-form.php" class="btn btn-primary" style="margin-top: 15px;">
                            <i class="fas fa-plus"></i> Add Project with Images
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
