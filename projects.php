<?php include 'include/config.php'; 

$filter = isset($_GET['type']) ? sanitize($_GET['type']) : '';
$where = "WHERE is_published = 1";
if ($filter && in_array($filter, ['Road', 'Residential', 'Commercial', 'Infrastructure'])) {
    $where .= " AND project_type = '$filter'";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects - <?php echo SITE_NAME; ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'include/header.php'; ?>
    
    <section class="page-banner">
        <div class="container">
            <h1>Our Projects</h1>
            <nav class="breadcrumb">
                <a href="index.php">Home</a> / <span>Projects</span>
            </nav>
        </div>
    </section>
    
    <section class="section projects-page">
        <div class="container">
            <div class="filter-buttons">
                <a href="projects.php" class="filter-btn <?php echo !$filter ? 'active' : ''; ?>">All</a>
                <a href="?type=Road" class="filter-btn <?php echo $filter == 'Road' ? 'active' : ''; ?>">Road</a>
                <a href="?type=Residential" class="filter-btn <?php echo $filter == 'Residential' ? 'active' : ''; ?>">Residential</a>
                <a href="?type=Commercial" class="filter-btn <?php echo $filter == 'Commercial' ? 'active' : ''; ?>">Commercial</a>
                <a href="?type=Infrastructure" class="filter-btn <?php echo $filter == 'Infrastructure' ? 'active' : ''; ?>">Infrastructure</a>
            </div>
            
            <div class="projects-grid">
                <?php
                $result = db_query("SELECT p.*, (SELECT image_path FROM project_images WHERE project_id = p.id LIMIT 1) as image 
                                    FROM projects p $where ORDER BY created_at DESC");
                
                $hasProjects = false;
                while ($project = db_fetch($result)):
                    $hasProjects = true;
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
                        <p class="project-duration"><i class="fas fa-clock"></i> <?php echo $project['duration']; ?></p>
                        <a href="project-detail.php?id=<?php echo $project['id']; ?>" class="btn btn-small">View Details</a>
                    </div>
                </div>
                <?php 
                endwhile;
                
                if (!$hasProjects):
                ?>
                <div class="no-projects">
                    <i class="fas fa-folder-open"></i>
                    <h3>No Projects Found</h3>
                    <p>We're working on new projects. Check back soon!</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <?php include 'include/footer.php'; ?>
    
    <script src="assets/js/main.js"></script>
</body>
</html>
