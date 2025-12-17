<?php
require_once '../include/config.php';
requireLogin();

if (isset($_GET['read'])) {
    $id = (int)$_GET['read'];
    db_query("UPDATE inquiries SET is_read = 1 WHERE id = $id");
    redirect('inquiries.php');
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    db_query("DELETE FROM inquiries WHERE id = $id");
    redirect('inquiries.php?msg=deleted');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiries - Admin Panel</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>
        
        <main class="main-content">
            <header class="top-header">
                <h1><i class="fas fa-envelope"></i> Inquiries</h1>
            </header>
            
            <div class="dashboard-content">
                <?php if (isset($_GET['msg'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> Inquiry deleted successfully!
                    </div>
                <?php endif; ?>
                
                <?php
                $result = db_query("SELECT * FROM inquiries ORDER BY created_at DESC");
                
                $hasInquiries = false;
                while ($row = db_fetch($result)):
                    $hasInquiries = true;
                ?>
                <div class="inquiry-card <?php echo !$row['is_read'] ? 'unread' : ''; ?>">
                    <div class="inquiry-header">
                        <div>
                            <h4>
                                <?php if (!$row['is_read']): ?>
                                <span class="badge badge-warning">New</span>
                                <?php endif; ?>
                                <?php echo htmlspecialchars($row['name']); ?>
                            </h4>
                            <p class="inquiry-email">
                                <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($row['email']); ?>
                            </p>
                        </div>
                        <div>
                            <small><i class="fas fa-calendar"></i> <?php echo formatDate($row['created_at']); ?></small>
                            <div style="margin-top: 10px;">
                                <?php if (!$row['is_read']): ?>
                                <a href="?read=<?php echo $row['id']; ?>" class="btn-icon" title="Mark as Read">
                                    <i class="fas fa-check"></i>
                                </a>
                                <?php endif; ?>
                                <a href="?delete=<?php echo $row['id']; ?>" class="btn-icon delete" 
                                   onclick="return confirm('Delete this inquiry?')" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <p class="inquiry-message"><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>
                </div>
                <?php 
                endwhile;
                
                if (!$hasInquiries):
                ?>
                <div class="text-center" style="padding: 60px; background: white; border-radius: 12px;">
                    <i class="fas fa-inbox" style="font-size: 60px; color: #ccc;"></i>
                    <p style="margin-top: 15px; color: #999;">No inquiries yet</p>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
