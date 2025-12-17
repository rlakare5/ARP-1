# Bhairavnath Construction - Dynamic Portfolio Website

A complete, modern, responsive, database-driven portfolio website for a Civil Engineer / Builder with Admin Panel (ARP).

## Features

### Admin Panel (ARP)
- **Secure Login** - Email + Password authentication
- **Dashboard** - Statistics overview with project counts
- **Project Management** - Add, edit, delete, publish/unpublish projects with multiple images
- **Services Management** - Manage services with icons
- **Gallery** - View and manage all uploaded images
- **Inquiries** - View and manage contact form submissions
- **Profile Settings** - Update company information, contact details, and password

### User Website
- **Home Page** - Hero section, featured projects, services preview, why choose us
- **About Page** - Company profile, experience timeline, certifications, vision & mission
- **Projects Page** - Grid layout with category filters (Road, Residential, Commercial, Infrastructure)
- **Project Detail** - Image gallery, full description, project info
- **Services Page** - All services with descriptions
- **Contact Page** - Contact form, contact information, working hours

## Installation on XAMPP

### Step 1: Setup XAMPP
1. Download and install [XAMPP](https://www.apachefriends.org/)
2. Start Apache and MySQL from XAMPP Control Panel

### Step 2: Copy Project Files
1. Copy this entire project folder to `C:\xampp\htdocs\bhairavnath-construction\`
   (or your XAMPP htdocs directory)

### Step 3: Create Database
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Click "Import" tab
3. Choose the `DATABASE.sql` file from the project folder
4. Click "Go" to import

### Step 4: Configure Database Connection
1. Open `include/config.php`
2. Update the database settings if needed:
```php
$db_host = "localhost";
$db_user = "root";
$db_pass = "";  // Your MySQL password (blank by default in XAMPP)
$db_name = "bhairavnath_construction";
```

### Step 5: Access the Website
- **User Website**: `http://localhost/bhairavnath-construction/`
- **Admin Panel**: `http://localhost/bhairavnath-construction/admin/login.php`

## Default Admin Login
- **Email**: `admin@bhairavnath.com`
- **Password**: `admin123`

> **Important**: Change the default password after first login!

## Project Structure

```
bhairavnath-construction/
├── admin/                  # Admin Panel
│   ├── index.php          # Dashboard
│   ├── login.php          # Login page
│   ├── logout.php         # Logout handler
│   ├── projects.php       # Project list
│   ├── project-form.php   # Add/Edit project
│   ├── services.php       # Services list
│   ├── service-form.php   # Add/Edit service
│   ├── gallery.php        # Image gallery
│   ├── inquiries.php      # Contact inquiries
│   ├── profile.php        # Profile settings
│   ├── sidebar.php        # Sidebar component
│   └── style.css          # Admin styles
├── assets/
│   ├── css/
│   │   └── style.css      # Main website styles
│   ├── js/
│   │   └── main.js        # JavaScript functions
│   └── images/            # Static images
├── include/
│   ├── config.php         # Database configuration
│   ├── header.php         # Website header
│   └── footer.php         # Website footer
├── uploads/               # Uploaded files
│   ├── projects/          # Project images
│   ├── profiles/          # Profile images
│   └── services/          # Service images
├── index.php              # Home page
├── about.php              # About page
├── projects.php           # Projects page
├── project-detail.php     # Project detail page
├── services.php           # Services page
├── contact.php            # Contact page
├── DATABASE.sql           # Database SQL file
└── README.md              # This file
```

## Technologies Used

- **Backend**: PHP 8.x
- **Database**: MySQL / MariaDB
- **Frontend**: HTML5, CSS3, JavaScript
- **Icons**: Font Awesome 6
- **Fonts**: Google Fonts (Poppins, Montserrat)
- **Responsive**: Mobile-first design

## Color Scheme

- **Primary**: Steel Blue (#1e3a5f)
- **Secondary**: (#2d5a87)
- **Accent**: Construction Yellow (#f9a825)
- **Accent Orange**: (#ff7043)

## Customization

### Change Company Name
Edit `include/config.php`:
```php
define('SITE_NAME', 'Your Company Name');
```

### Update Admin Profile
1. Login to Admin Panel
2. Go to Profile Settings
3. Update name, experience, certifications, contact details, about text

### Add Projects
1. Login to Admin Panel
2. Go to Projects > Add Project
3. Fill in project details and upload images
4. Toggle "Publish on Website" to make it visible

### Add Services
1. Login to Admin Panel
2. Go to Services > Add Service
3. Select an icon, add name and description

## Security Notes

1. Change the default admin password immediately
2. Keep PHP and MySQL updated
3. Consider adding HTTPS for production
4. Regularly backup your database
5. Set proper file permissions on uploads folder

## Support

For any issues or customization requests, please contact the developer.

---

Made with ❤️ for Bhairavnath Construction
