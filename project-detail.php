<?php include 'include/config.php';

if (!isset($_GET['id'])) {
    redirect('projects.php');
}

$id = (int)$_GET['id'];
$result = db_query("SELECT * FROM projects WHERE id = $id AND is_published = 1");
$project = db_fetch($result);

if (!$project) {
    redirect('projects.php');
}

$mediaResult = db_query("SELECT * FROM project_images WHERE project_id = $id ORDER BY is_primary DESC, created_at ASC");
$images = [];
$videos = [];
while ($media = db_fetch($mediaResult)) {
    $media_type = $media['media_type'] ?? 'image';
    if ($media_type == 'video') {
        $videos[] = $media;
    } else {
        $images[] = $media;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $project['name']; ?> - <?php echo SITE_NAME; ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .project-videos { margin-top: 30px; }
        .project-videos h3 { margin-bottom: 20px; color: #333; }
        .video-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .video-item { border-radius: 10px; overflow: hidden; box-shadow: 0 3px 15px rgba(0,0,0,0.1); background: #000; }
        .video-item video { width: 100%; max-height: 250px; display: block; }
        .media-tabs { display: flex; gap: 15px; margin-bottom: 20px; }
        .media-tab { padding: 10px 25px; border: none; border-radius: 25px; cursor: pointer; font-weight: 600; transition: all 0.3s; background: #f0f0f0; color: #666; }
        .media-tab.active { background: #1a237e; color: white; }
        .media-tab i { margin-right: 8px; }
        .media-panel { display: none; }
        .media-panel.active { display: block; }
    </style>
</head>
<body>
    <?php include 'include/header.php'; ?>
    
    <section class="page-banner">
        <div class="container">
            <h1><?php echo $project['name']; ?></h1>
            <nav class="breadcrumb">
                <a href="index.php">Home</a> / <a href="projects.php">Projects</a> / <span><?php echo $project['name']; ?></span>
            </nav>
        </div>
    </section>
    
    <section class="section project-detail">
        <div class="container">
            <div class="project-detail-grid">
                <div class="project-gallery">
                    <?php if (!empty($images) || !empty($videos)): ?>
                        <?php if (!empty($videos) && !empty($images)): ?>
                        <div class="media-tabs">
                            <button class="media-tab active" onclick="switchMediaTab('images')">
                                <i class="fas fa-images"></i> Images (<?php echo count($images); ?>)
                            </button>
                            <button class="media-tab" onclick="switchMediaTab('videos')">
                                <i class="fas fa-video"></i> Videos (<?php echo count($videos); ?>)
                            </button>
                        </div>
                        <?php endif; ?>
                        
                        <div id="imagesPanel" class="media-panel active">
                            <?php if (!empty($images)): 
                                $mainImage = $images[0]['image_path'];
                            ?>
                            <div class="main-image">
                                <img src="<?php echo $mainImage; ?>" alt="<?php echo $project['name']; ?>" id="mainImage">
                            </div>
                            <?php if (count($images) > 1): ?>
                            <div class="thumbnail-list">
                                <?php foreach ($images as $index => $img): ?>
                                <div class="thumbnail <?php echo $index == 0 ? 'active' : ''; ?>" onclick="changeImage('<?php echo $img['image_path']; ?>', this)">
                                    <img src="<?php echo $img['image_path']; ?>" alt="Thumbnail">
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                            <?php else: ?>
                            <div class="main-image">
                                <img src="assets/images/placeholder.svg" alt="No images">
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($videos)): ?>
                        <div id="videosPanel" class="media-panel <?php echo empty($images) ? 'active' : ''; ?>">
                            <div class="video-grid">
                                <?php foreach ($videos as $vid): ?>
                                <div class="video-item">
                                    <video src="<?php echo $vid['image_path']; ?>" controls></video>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php else: ?>
                    <div class="main-image">
                        <img src="assets/images/placeholder.svg" alt="No media">
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="project-info-detail">
                    <div class="project-meta">
                        <span class="badge type"><?php echo $project['project_type']; ?></span>
                        <span class="badge status <?php echo strtolower($project['status']); ?>"><?php echo $project['status']; ?></span>
                    </div>
                    
                    <h2><?php echo $project['name']; ?></h2>
                    
                    <div class="info-list">
                        <div class="info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <span class="label">Location</span>
                                <span class="value"><?php echo $project['location']; ?></span>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <div>
                                <span class="label">Duration</span>
                                <span class="value"><?php echo $project['duration']; ?></span>
                            </div>
                        </div>
                        <?php if ($project['budget']): ?>
                        <div class="info-item">
                            <i class="fas fa-rupee-sign"></i>
                            <div>
                                <span class="label">Budget</span>
                                <span class="value"><?php echo $project['budget']; ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="project-description">
                        <h3>Project Description</h3>
                        <p><?php echo nl2br($project['description']); ?></p>
                    </div>
                    
                    <a href="contact.php" class="btn btn-primary btn-large">
                        <i class="fas fa-phone"></i> Contact Engineer
                    </a>
                </div>
            </div>
        </div>
    </section>
    
    <section class="section related-projects bg-light">
        <div class="container">
            <h2 class="section-title">Related Projects</h2>
            <div class="projects-grid">
                <?php
                $project_type_esc = db_escape_raw($project['project_type']);
                $related = db_query("SELECT p.*, (SELECT image_path FROM project_images WHERE project_id = p.id AND (media_type = 'image' OR media_type IS NULL) LIMIT 1) as image 
                                     FROM projects p 
                                     WHERE is_published = 1 AND id != $id AND project_type = '$project_type_esc'
                                     ORDER BY created_at DESC LIMIT 3");
                
                while ($rel = db_fetch($related)):
                ?>
                <div class="project-card">
                    <div class="project-image">
                        <img src="<?php echo $rel['image'] ?: 'assets/images/placeholder.svg'; ?>" alt="<?php echo $rel['name']; ?>">
                        <span class="project-type"><?php echo $rel['project_type']; ?></span>
                    </div>
                    <div class="project-info">
                        <h3><?php echo $rel['name']; ?></h3>
                        <a href="project-detail.php?id=<?php echo $rel['id']; ?>" class="btn btn-small">View Details</a>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>
    
    <?php include 'include/footer.php'; ?>
    
    <script>
    function changeImage(src, el) {
        document.getElementById('mainImage').src = src;
        document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
        el.classList.add('active');
    }
    
    function switchMediaTab(tab) {
        document.querySelectorAll('.media-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.media-panel').forEach(p => p.classList.remove('active'));
        
        if (tab === 'images') {
            document.querySelector('.media-tab:first-child').classList.add('active');
            document.getElementById('imagesPanel').classList.add('active');
        } else {
            document.querySelector('.media-tab:last-child').classList.add('active');
            document.getElementById('videosPanel').classList.add('active');
        }
    }
    </script>
    <script src="assets/js/main.js"></script>
</body>
</html>
