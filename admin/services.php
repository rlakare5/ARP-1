<?php
require_once '../include/config.php';
requireLogin();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    db_query("DELETE FROM services WHERE id = $id");
    redirect('services.php?msg=deleted');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services - Admin Panel</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>
        
        <main class="main-content">
            <header class="top-header">
                <h1><i class="fas fa-cogs"></i> Services</h1>
                <a href="service-form.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Service
                </a>
            </header>
            
            <div class="dashboard-content">
                <?php if (isset($_GET['msg'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?php 
                        $msgs = [
                            'created' => 'Service created successfully!',
                            'updated' => 'Service updated successfully!',
                            'deleted' => 'Service deleted successfully!'
                        ];
                        echo $msgs[$_GET['msg']] ?? 'Action completed!';
                        ?>
                    </div>
                <?php endif; ?>
                
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Icon</th>
                                <th>Service Name</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = db_query("SELECT * FROM services ORDER BY id ASC");
                            
                            $hasRows = false;
                            while ($row = db_fetch($result)):
                                $hasRows = true;
                            ?>
                            <tr>
                                <td><i class="<?php echo $row['icon'] ?: 'fas fa-cog'; ?>" style="font-size: 24px; color: #1e3a5f;"></i></td>
                                <td><strong><?php echo $row['name']; ?></strong></td>
                                <td><?php echo substr($row['description'], 0, 100) . '...'; ?></td>
                                <td>
                                    <span class="badge <?php echo $row['is_active'] ? 'badge-success' : 'badge-secondary'; ?>">
                                        <?php echo $row['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="service-form.php?id=<?php echo $row['id']; ?>" class="btn-icon" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="?delete=<?php echo $row['id']; ?>" class="btn-icon delete" 
                                       onclick="return confirm('Are you sure you want to delete this service?')" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php 
                            endwhile;
                            
                            if (!$hasRows):
                            ?>
                            <tr>
                                <td colspan="5" class="text-center">No services found</td>
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
