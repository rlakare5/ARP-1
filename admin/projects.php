<?php
require_once '../include/config.php';
requireLogin();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    $images = db_query("SELECT image_path FROM project_images WHERE project_id = $id");
    while ($img = db_fetch($images)) {
        deleteImage($img['image_path']);
    }
    
    db_query("DELETE FROM projects WHERE id = $id");
    redirect('projects.php?msg=deleted');
}

if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    db_query("UPDATE projects SET is_published = NOT is_published WHERE id = $id");
    redirect('projects.php');
}

$filter = isset($_GET['filter']) ? sanitize($_GET['filter']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

$where = "WHERE 1=1";
if ($filter && in_array($filter, ['Road', 'Residential', 'Commercial', 'Infrastructure'])) {
    $where .= " AND project_type = '$filter'";
}
if ($search) {
    $search_esc = db_escape_raw($search);
    $where .= " AND (name LIKE '%$search_esc%' OR location LIKE '%$search_esc%')";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects - Admin Panel</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>
        
        <main class="main-content">
            <header class="top-header">
                <h1><i class="fas fa-building"></i> Projects</h1>
                <a href="project-form.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Project
                </a>
            </header>
            
            <div class="dashboard-content">
                <?php if (isset($_GET['msg'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?php 
                        $msgs = [
                            'created' => 'Project created successfully!',
                            'updated' => 'Project updated successfully!',
                            'deleted' => 'Project deleted successfully!'
                        ];
                        echo $msgs[$_GET['msg']] ?? 'Action completed!';
                        ?>
                    </div>
                <?php endif; ?>
                
                <div class="filter-bar">
                    <form method="GET" class="search-form">
                        <input type="text" name="search" placeholder="Search projects..." 
                               value="<?php echo $search; ?>" class="form-control">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </form>
                    <div class="filter-buttons">
                        <a href="projects.php" class="btn <?php echo !$filter ? 'btn-primary' : 'btn-secondary'; ?>">All</a>
                        <a href="?filter=Road" class="btn <?php echo $filter == 'Road' ? 'btn-primary' : 'btn-secondary'; ?>">Road</a>
                        <a href="?filter=Residential" class="btn <?php echo $filter == 'Residential' ? 'btn-primary' : 'btn-secondary'; ?>">Residential</a>
                        <a href="?filter=Commercial" class="btn <?php echo $filter == 'Commercial' ? 'btn-primary' : 'btn-secondary'; ?>">Commercial</a>
                        <a href="?filter=Infrastructure" class="btn <?php echo $filter == 'Infrastructure' ? 'btn-primary' : 'btn-secondary'; ?>">Infrastructure</a>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Project Name</th>
                                <th>Type</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Published</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT p.*, (SELECT image_path FROM project_images WHERE project_id = p.id LIMIT 1) as image 
                                      FROM projects p $where ORDER BY created_at DESC";
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
                                <td><strong><?php echo $row['name']; ?></strong></td>
                                <td><span class="badge badge-info"><?php echo $row['project_type']; ?></span></td>
                                <td><?php echo $row['location']; ?></td>
                                <td>
                                    <span class="badge <?php echo $row['status'] == 'Completed' ? 'badge-success' : 'badge-warning'; ?>">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="?toggle=<?php echo $row['id']; ?>" class="toggle-switch-link">
                                        <span class="badge <?php echo $row['is_published'] ? 'badge-success' : 'badge-secondary'; ?>">
                                            <?php echo $row['is_published'] ? 'Published' : 'Draft'; ?>
                                        </span>
                                    </a>
                                </td>
                                <td>
                                    <a href="project-form.php?id=<?php echo $row['id']; ?>" class="btn-icon" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="?delete=<?php echo $row['id']; ?>" class="btn-icon delete" 
                                       onclick="return confirm('Are you sure you want to delete this project?')" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php 
                            endwhile;
                            
                            if (!$hasRows):
                            ?>
                            <tr>
                                <td colspan="7" class="text-center">No projects found</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    
    <style>
    .filter-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }
    .search-form {
        display: flex;
        gap: 10px;
    }
    .search-form .form-control {
        width: 250px;
    }
    .filter-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .toggle-switch-link {
        text-decoration: none;
    }
    </style>
</body>
</html>
