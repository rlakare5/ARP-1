# Bhairavnath Construction - Portfolio Website

## Overview
A dynamic PHP portfolio website for Bhairavnath Construction company featuring project showcasing, services display, media gallery, and admin panel for content management.

## Project Structure
```
├── admin/                  # Admin panel
│   ├── uploads/           # Uploaded media files
│   │   ├── projects/      # Project images/videos
│   │   ├── profiles/      # Profile images
│   │   ├── gallery/       # Gallery media
│   │   └── hero/          # Hero background images
│   ├── index.php          # Dashboard
│   ├── projects.php       # Manage projects
│   ├── project-form.php   # Add/edit projects
│   ├── services.php       # Manage services
│   ├── media.php          # Media gallery management
│   ├── profile.php        # Admin profile settings
│   └── inquiries.php      # View contact inquiries
├── include/               # Shared includes
│   ├── config.php         # Database config & helper functions
│   ├── header.php         # Site header
│   └── footer.php         # Site footer
├── assets/                # Static assets
│   ├── css/style.css      # Frontend styles
│   ├── js/main.js         # Frontend scripts
│   └── images/            # Static images
├── index.php              # Homepage
├── projects.php           # Projects listing
├── project-detail.php     # Project detail view
├── services.php           # Services page
├── gallery.php            # Media gallery
├── about.php              # About page
├── contact.php            # Contact form
└── DATABASE.sql           # Database schema
```

## Database Configuration

### For Local XAMPP (MySQL)
The site auto-detects the environment. For XAMPP:
1. Create database `bhairavnath_construction` in phpMyAdmin
2. Import `DATABASE.sql` file
3. Default config in `include/config.php`:
   - Host: localhost
   - User: root
   - Password: (empty)
   - Database: bhairavnath_construction

### Admin Login
- Email: admin@bhairavnath.com
- Password: admin123

## Key Features
- **Projects**: Add/edit projects with multiple images and videos
- **Services**: Manage service offerings with icons
- **Media Gallery**: Separate gallery for images and videos
- **Contact Form**: Inquiry management system
- **Profile**: Customize admin profile and hero image

## Image Upload Fix (December 2024)
Fixed image upload functionality:
1. Updated upload path to use absolute paths with `__DIR__`
2. Added proper error handling and debugging
3. Fixed all admin files to use database helper functions
4. Ensured upload folders exist with proper permissions

### For XAMPP Users
If images still don't upload:
1. Check folder permissions on `admin/uploads/` (give full access)
2. In `php.ini`, set:
   ```ini
   upload_max_filesize = 50M
   post_max_size = 50M
   ```
3. Restart Apache after changes

## Database Helper Functions
All database operations use abstracted functions in `config.php`:
- `db_query($query)` - Execute query
- `db_fetch($result)` - Fetch row
- `db_escape_raw($value)` - Escape strings for SQL
- `db_insert_id()` - Get last insert ID

These work with both MySQL (XAMPP) and PostgreSQL (Replit).
