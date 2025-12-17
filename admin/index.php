<?php
require_once '../include/config.php';
requireLogin();

$total_projects = getProjectCount();
$completed_projects = getProjectCount('Completed');
$ongoing_projects = getProjectCount('Ongoing');
$unread_inquiries = getUnreadInquiries();

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Panel</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>
        
        <main class="main-content">
            <header class="top-header">
                <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
                <div class="header-right">
                    <span>Welcome, <?php echo $_SESSION['admin_name'] ?? 'Admin'; ?></span>
                </div>
            </header>
            
            <div class="dashboard-content">
                <div class="stats-grid">
                    <a href="?filter=all" class="stat-card <?php echo $filter == 'all' ? 'active' : ''; ?>">
                        <div class="stat-icon"><i class="fas fa-folder-open"></i></div>
                        <div class="stat-info">
                            <h3><?php echo $total_projects; ?></h3>
                            <p>Total Projects</p>
                        </div>
                    </a>
                    <a href="?filter=Completed" class="stat-card completed <?php echo $filter == 'Completed' ? 'active' : ''; ?>">
                        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                        <div class="stat-info">
                            <h3><?php echo $completed_projects; ?></h3>
                            <p>Completed Projects</p>
                        </div>
                    </a>
                    <a href="?filter=Ongoing" class="stat-card ongoing <?php echo $filter == 'Ongoing' ? 'active' : ''; ?>">
                        <div class="stat-icon"><i class="fas fa-spinner"></i></div>
                        <div class="stat-info">
                            <h3><?php echo $ongoing_projects; ?></h3>
                            <p>Ongoing Projects</p>
                        </div>
                    </a>
                    <a href="inquiries.php" class="stat-card inquiries">
                        <div class="stat-icon"><i class="fas fa-envelope"></i></div>
                        <div class="stat-info">
                            <h3><?php echo $unread_inquiries; ?></h3>
                            <p>New Inquiries</p>
                        </div>
                    </a>
                </div>
                
                <div class="section-header">
                    <h2><i class="fas fa-list"></i> Recent Projects</h2>
                    <a href="projects.php" class="btn btn-primary">
                        <i class="fas fa-eye"></i> View All
                    </a>
                </div>
                
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Project Name</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Published</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $where = "";
                            if ($filter == 'Completed' || $filter == 'Ongoing') {
                                $where = "WHERE status = '$filter'";
                            }
                            $query = "SELECT p.*, (SELECT image_path FROM project_images WHERE project_id = p.id LIMIT 1) as image 
                                      FROM projects p $where ORDER BY created_at DESC LIMIT 5";
                            $result = db_query($query);
                            
                            $hasRows = false;
                            while ($row = db_fetch($result)):
                                $hasRows = true;
                            ?>
                            <tr>
                                <td>
                                    <img src="<?php echo $row['image'] ?: '../assets/images/placeholder.svg'; ?>" 
                                         alt="Project" class="table-img">
                                </td>
                                <td><?php echo $row['name']; ?></td>
                                <td><span class="badge badge-info"><?php echo $row['project_type']; ?></span></td>
                                <td>
                                    <span class="badge <?php echo $row['status'] == 'Completed' ? 'badge-success' : 'badge-warning'; ?>">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?php echo $row['is_published'] ? 'badge-success' : 'badge-secondary'; ?>">
                                        <?php echo $row['is_published'] ? 'Published' : 'Draft'; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="project-form.php?id=<?php echo $row['id']; ?>" class="btn-icon" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php 
                            endwhile;
                            
                            if (!$hasRows):
                            ?>
                            <tr>
                                <td colspan="6" class="text-center">No projects found</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
