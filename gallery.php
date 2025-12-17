<?php include 'include/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery - <?php echo SITE_NAME; ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .gallery-hero { background: linear-gradient(135deg, #1a237e 0%, #283593 100%); padding: 100px 0 60px; text-align: center; color: white; }
        .gallery-hero h1 { font-size: 3rem; margin-bottom: 15px; }
        .gallery-hero p { font-size: 1.2rem; opacity: 0.9; }
        .gallery-filter { display: flex; justify-content: center; gap: 15px; padding: 30px 0; flex-wrap: wrap; }
        .filter-btn { padding: 12px 30px; border: none; border-radius: 30px; cursor: pointer; font-weight: 600; transition: all 0.3s; text-decoration: none; display: inline-block; }
        .filter-btn.active { background: #1a237e; color: white; }
        .filter-btn:not(.active) { background: #f0f0f0; color: #333; }
        .filter-btn:hover:not(.active) { background: #e0e0e0; }
        .gallery-section { padding: 40px 0 80px; }
        .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 25px; }
        .gallery-item { position: relative; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: transform 0.3s, box-shadow 0.3s; background: #f0f0f0; }
        .gallery-item:hover { transform: translateY(-10px); box-shadow: 0 15px 40px rgba(0,0,0,0.2); }
        .gallery-item img, .gallery-item video { width: 100%; height: 280px; object-fit: cover; display: block; }
        .gallery-item .media-type { position: absolute; top: 15px; left: 15px; padding: 6px 15px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
        .gallery-item .media-type.image { background: rgba(76, 175, 80, 0.9); color: white; }
        .gallery-item .media-type.video { background: rgba(255, 87, 34, 0.9); color: white; }
        .gallery-item .play-overlay { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 60px; color: white; text-shadow: 0 2px 15px rgba(0,0,0,0.5); pointer-events: none; opacity: 0.9; transition: opacity 0.3s, transform 0.3s; }
        .gallery-item:hover .play-overlay { opacity: 1; transform: translate(-50%, -50%) scale(1.1); }
        .gallery-overlay { position: absolute; bottom: 0; left: 0; right: 0; padding: 20px; background: linear-gradient(transparent, rgba(0,0,0,0.8)); color: white; transform: translateY(100%); transition: transform 0.3s; }
        .gallery-item:hover .gallery-overlay { transform: translateY(0); }
        .gallery-overlay h4 { margin: 0 0 5px; font-size: 18px; }
        .gallery-overlay p { margin: 0; font-size: 14px; opacity: 0.8; }
        .no-media { grid-column: 1 / -1; text-align: center; padding: 80px 20px; color: #666; }
        .no-media i { font-size: 80px; color: #ddd; margin-bottom: 20px; }
        .lightbox { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.95); z-index: 9999; justify-content: center; align-items: center; }
        .lightbox.active { display: flex; }
        .lightbox-content { max-width: 90%; max-height: 90%; }
        .lightbox-content img, .lightbox-content video { max-width: 100%; max-height: 85vh; border-radius: 10px; }
        .lightbox-close { position: absolute; top: 20px; right: 30px; font-size: 40px; color: white; cursor: pointer; transition: opacity 0.3s; }
        .lightbox-close:hover { opacity: 0.7; }
        .lightbox-info { text-align: center; color: white; margin-top: 20px; }
        .lightbox-info h3 { margin: 0 0 5px; }
        .lightbox-info p { margin: 0; opacity: 0.7; }
        @media (max-width: 768px) { .gallery-hero h1 { font-size: 2rem; } .gallery-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <?php include 'include/header.php'; ?>
    
    <section class="gallery-hero">
        <div class="container">
            <h1><i class="fas fa-photo-video"></i> Our Gallery</h1>
            <p>Explore our collection of images and videos</p>
        </div>
    </section>
    
    <section class="gallery-section">
        <div class="container">
            <?php
            $filter = isset($_GET['type']) ? sanitize($_GET['type']) : '';
            ?>
            <div class="gallery-filter">
                <a href="gallery.php" class="filter-btn <?php echo !$filter ? 'active' : ''; ?>">
                    <i class="fas fa-th"></i> All
                </a>
                <a href="?type=image" class="filter-btn <?php echo $filter == 'image' ? 'active' : ''; ?>">
                    <i class="fas fa-image"></i> Images
                </a>
                <a href="?type=video" class="filter-btn <?php echo $filter == 'video' ? 'active' : ''; ?>">
                    <i class="fas fa-video"></i> Videos
                </a>
            </div>
            
            <div class="gallery-grid">
                <?php
                $where = "WHERE is_active = 1";
                if ($filter == 'image' || $filter == 'video') {
                    $where .= " AND file_type = '$filter'";
                }
                
                $result = db_query("SELECT * FROM media_gallery $where ORDER BY display_order ASC, created_at DESC");
                
                $hasMedia = false;
                while ($row = db_fetch($result)):
                    $hasMedia = true;
                ?>
                <div class="gallery-item" onclick="openLightbox('<?php echo $row['file_path']; ?>', '<?php echo $row['file_type']; ?>', '<?php echo addslashes($row['title'] ?: 'Gallery Item'); ?>', '<?php echo addslashes($row['description'] ?: ''); ?>')">
                    <?php if ($row['file_type'] == 'video'): ?>
                        <video src="<?php echo $row['file_path']; ?>" muted></video>
                        <div class="play-overlay"><i class="fas fa-play-circle"></i></div>
                    <?php else: ?>
                        <img src="<?php echo $row['file_path']; ?>" alt="<?php echo $row['title'] ?: 'Gallery Image'; ?>" loading="lazy">
                    <?php endif; ?>
                    <span class="media-type <?php echo $row['file_type']; ?>"><?php echo $row['file_type']; ?></span>
                    <?php if ($row['title'] || $row['description']): ?>
                    <div class="gallery-overlay">
                        <?php if ($row['title']): ?>
                        <h4><?php echo $row['title']; ?></h4>
                        <?php endif; ?>
                        <?php if ($row['description']): ?>
                        <p><?php echo $row['description']; ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php 
                endwhile;
                
                if (!$hasMedia):
                ?>
                <div class="no-media">
                    <i class="fas fa-photo-video"></i>
                    <h3>No media available</h3>
                    <p>Check back later for our latest photos and videos</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <div class="lightbox" id="lightbox">
        <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
        <div class="lightbox-content">
            <div id="lightboxMedia"></div>
            <div class="lightbox-info">
                <h3 id="lightboxTitle"></h3>
                <p id="lightboxDesc"></p>
            </div>
        </div>
    </div>
    
    <?php include 'include/footer.php'; ?>
    
    <script>
    function openLightbox(src, type, title, desc) {
        const lightbox = document.getElementById('lightbox');
        const mediaContainer = document.getElementById('lightboxMedia');
        const titleEl = document.getElementById('lightboxTitle');
        const descEl = document.getElementById('lightboxDesc');
        
        if (type === 'video') {
            mediaContainer.innerHTML = `<video src="${src}" controls autoplay style="max-width:100%; max-height:85vh; border-radius:10px;"></video>`;
        } else {
            mediaContainer.innerHTML = `<img src="${src}" alt="${title}" style="max-width:100%; max-height:85vh; border-radius:10px;">`;
        }
        
        titleEl.textContent = title;
        descEl.textContent = desc;
        
        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    function closeLightbox() {
        const lightbox = document.getElementById('lightbox');
        const mediaContainer = document.getElementById('lightboxMedia');
        
        lightbox.classList.remove('active');
        document.body.style.overflow = '';
        
        const video = mediaContainer.querySelector('video');
        if (video) video.pause();
        mediaContainer.innerHTML = '';
    }
    
    document.getElementById('lightbox').addEventListener('click', function(e) {
        if (e.target === this) closeLightbox();
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeLightbox();
    });
    </script>
    
    <script src="assets/js/main.js"></script>
</body>
</html>
