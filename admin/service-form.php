<?php
require_once '../include/config.php';
requireLogin();

$service = null;
$isEdit = false;

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $result = db_query("SELECT * FROM services WHERE id = $id");
    $service = db_fetch($result);
    if ($service) {
        $isEdit = true;
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    $icon = sanitize($_POST['icon']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    if (empty($name)) {
        $error = 'Service name is required';
    } else {
        $name_esc = db_escape_raw($name);
        $description_esc = db_escape_raw($description);
        $icon_esc = db_escape_raw($icon);
        
        if ($isEdit) {
            $query = "UPDATE services SET 
                      name = '$name_esc', 
                      description = '$description_esc', 
                      icon = '$icon_esc', 
                      is_active = $is_active 
                      WHERE id = $id";
            db_query($query);
            $msg = 'updated';
        } else {
            $query = "INSERT INTO services (name, description, icon, is_active) 
                      VALUES ('$name_esc', '$description_esc', '$icon_esc', $is_active)";
            db_query($query);
            $msg = 'created';
        }
        
        redirect('services.php?msg=' . $msg);
    }
}

$icons = [
    'fas fa-building' => 'Building',
    'fas fa-road' => 'Road',
    'fas fa-city' => 'City/Infrastructure',
    'fas fa-hammer' => 'Hammer/Construction',
    'fas fa-hard-hat' => 'Hard Hat',
    'fas fa-drafting-compass' => 'Design',
    'fas fa-clipboard-list' => 'Consultation',
    'fas fa-search' => 'Inspection',
    'fas fa-home' => 'Home',
    'fas fa-warehouse' => 'Warehouse',
    'fas fa-tools' => 'Tools',
    'fas fa-paint-roller' => 'Painting'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEdit ? 'Edit' : 'Add'; ?> Service - Admin Panel</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>
        
        <main class="main-content">
            <header class="top-header">
                <h1><i class="fas fa-<?php echo $isEdit ? 'edit' : 'plus'; ?>"></i> <?php echo $isEdit ? 'Edit' : 'Add'; ?> Service</h1>
                <a href="services.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Services
                </a>
            </header>
            
            <div class="dashboard-content">
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="form-card">
                    <div class="form-group">
                        <label><i class="fas fa-tag"></i> Service Name *</label>
                        <input type="text" name="name" class="form-control" required
                               value="<?php echo $service['name'] ?? ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-icons"></i> Select Icon</label>
                        <div class="icon-grid">
                            <?php foreach ($icons as $iconClass => $iconName): 
                                $selected = (isset($service['icon']) && $service['icon'] == $iconClass) ? 'selected' : '';
                            ?>
                            <label class="icon-option <?php echo $selected; ?>">
                                <input type="radio" name="icon" value="<?php echo $iconClass; ?>" 
                                       <?php echo $selected ? 'checked' : ''; ?>>
                                <i class="<?php echo $iconClass; ?>"></i>
                                <span><?php echo $iconName; ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-align-left"></i> Description</label>
                        <textarea name="description" class="form-control" rows="5"><?php echo $service['description'] ?? ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="toggle-label">
                            <span><i class="fas fa-check-circle"></i> Active</span>
                            <label class="toggle-switch">
                                <input type="checkbox" name="is_active" 
                                       <?php echo (!isset($service['is_active']) || $service['is_active']) ? 'checked' : ''; ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </label>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> <?php echo $isEdit ? 'Update' : 'Save'; ?> Service
                        </button>
                        <a href="services.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>
    
    <style>
    .icon-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 10px;
    }
    .icon-option {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 15px 10px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s;
    }
    .icon-option:hover, .icon-option.selected {
        border-color: #1e3a5f;
        background: #f0f7ff;
    }
    .icon-option input {
        display: none;
    }
    .icon-option i {
        font-size: 24px;
        color: #1e3a5f;
        margin-bottom: 8px;
    }
    .icon-option span {
        font-size: 11px;
        text-align: center;
    }
    .toggle-label {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
    }
    </style>
    
    <script>
    document.querySelectorAll('.icon-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.icon-option').forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
        });
    });
    </script>
</body>
</html>
