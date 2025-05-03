# Student Blog Management System

A comprehensive blog management system designed for students and administrators to create, manage, and share blog posts.

## Table of Contents
- [Features](#features)
- [Technical Requirements](#technical-requirements)
- [Installation Guide](#installation-guide)
- [Default Credentials](#default-credentials)
- [Project Structure](#project-structure)
- [Security Features](#security-features)
- [Troubleshooting](#troubleshooting)
- [Support](#support)
- [License](#license)

## Features

### Student Features
- **Dashboard**: View recent posts and quick actions
- **My Posts**: Manage personal blog posts
  - Create new posts
  - Edit existing posts
  - Delete posts
  - View post details
- **Add New Post**: Create blog posts with:
  - Title and content
  - Category selection
  - Photo upload support
  - Draft/Published status

### Admin Features
- **Dashboard**: Overview of system statistics
- **Manage Posts**: Full control over all blog posts
- **Manage Users**: Student account management
  - Add new students
  - Edit student details
  - Delete students (with last student protection)
- **Manage Categories**: Category management
  - Add new categories
  - Edit categories
  - Delete categories (with last category protection)

## Technical Requirements
- PHP 8.0 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (for dependency management)
- Modern web browser

## Installation Guide

1. **Download and Extract**
   ```bash
   git clone [repository-url]
   cd blog-project
   ```

2. **Configure Database**
   - Create a new MySQL database
   - Update `includes/config.php` with your database credentials:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'blogs');
     define('DB_USER', 'your_username');
     define('DB_PASS', 'your_password');
     ```

3. **Run Setup Script**
   - Access `setup.php` in your browser
   - This will:
     - Create necessary database tables
     - Set up default users and categories
     - Create required directories

4. **Configure Web Server**
   - Point your web server to the project directory
   - Ensure proper permissions:
     ```bash
     chmod 755 -R .
     chmod 777 -R uploads/
     ```

5. **Access the System**
   - Open your browser and navigate to the project URL
   - Use default credentials to log in

## Default Credentials

### Admin Access
- **Username**: admin
- **Password**: password
- **Role**: Administrator

### Student Access
- **Email**: student@example.com
- **Password**: password
- **Name**: student

### Default Category
- **Name**: Tech

## Project Structure

```
blog-project/
├── admin/           # Admin-specific functionality
│   ├── dashboard.php
│   ├── manageposts.php
│   ├── manageusers.php
│   ├── managecategories.php
│   └── [other admin files]
├── assets/          # Static assets
│   └── css/        # CSS files
├── includes/        # Core configuration
│   ├── config.php
│   ├── DatabaseConnection.php
│   └── DatabaseFunctions.php
├── student/         # Student functionality
│   ├── dashboard.php
│   ├── myposts.php
│   ├── addpost.php
│   ├── editpost.php
│   ├── viewpost.php
│   └── [other student files]
├── templates/       # Navigation templates
│   ├── admin_navigation.php
│   └── student_navigation.php
├── uploads/         # Uploaded files
│   └── posts/      # Post photos
├── index.php        # Main entry point
├── login.php        # Login page
├── register.php     # Registration page
├── setup.php        # Setup script
├── blogs.sql        # Database schema
└── README.md        # Documentation
```

## Security Features

1. **Authentication**
   - Secure password hashing
   - Session management
   - Role-based access control

2. **Data Protection**
   - Input validation
   - SQL injection prevention
   - XSS protection
   - CSRF protection

3. **File Upload Security**
   - File type validation
   - Size restrictions
   - Secure file naming

4. **User Management**
   - Last student protection
   - Last category protection
   - Secure password reset

## Troubleshooting

### Common Issues

1. **Database Connection**
   - Verify database credentials in `config.php`
   - Ensure MySQL service is running
   - Check database permissions

2. **File Upload Issues**
   - Verify upload directory permissions
   - Check PHP upload limits
   - Ensure proper file types

3. **Login Problems**
   - Verify credentials
   - Check session configuration
   - Clear browser cache

### Error Logging
- Check PHP error logs
- Enable debug mode in `config.php`
- Monitor database errors

## Support

For support, please:
1. Check the troubleshooting guide
2. Review the documentation
3. Contact the system administrator

## License

This project is licensed under the MIT License - see the LICENSE file for details. 
