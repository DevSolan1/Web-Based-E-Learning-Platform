
#  E-Learning Web-Based System

A complete web-based E-Learning Platform developed using PHP, MySQL, HTML, CSS, JavaScript, and Bootstrap. The system enables students to enroll in courses, teachers to create and manage learning content, and administrators to monitor and manage the entire platform.

---

##  Project Overview

The E-Learning Web-Based System is designed to provide an online learning environment where:

- Students can browse, enroll in, and complete courses.
- Teachers can create and manage educational content.
- Administrators can manage users, courses, payments, and platform activities.

The platform supports video-based learning, course enrollment, progress tracking, messaging, notifications, certificates, and payment management.

---

##  Features

### 👨‍🎓 Student Features
- User Registration and Login
- Browse Available Courses
- Search Courses by Category
- Course Enrollment
- Video-Based Learning
- Track Learning Progress
- View Notifications
- Send and Receive Messages
- Manage Profile
- Download Certificates
- View Enrolled Courses

### 👨‍🏫 Teacher Features
- Teacher Dashboard
- Create New Courses
- Edit Existing Courses
- Upload Course Videos
- Manage Students
- Track Earnings
- Messaging System
- Notifications Management
- Profile Management

### 👨‍💼 Admin Features
- Admin Dashboard
- Manage Users
- Manage Courses
- Manage Categories
- Manage Payments
- Manage Notifications
- Platform Monitoring

---

##  Technologies Used

### Frontend
- HTML5
- CSS3
- JavaScript
- Bootstrap
- Bootstrap Icons

### Backend
- PHP

### Database
- MySQL

### Server
- Apache (XAMPP/WAMP/LAMP)

---

## 📂 Project Structure

```text
elearning/
│
├── admin/
│   ├── dashboard.php
│   ├── users.php
│   ├── courses.php
│   ├── categories.php
│   └── payments.php
│
├── student/
│   ├── dashboard.php
│   ├── my-courses.php
│   ├── certificate.php
│   └── profile.php
│
├── teacher/
│   ├── dashboard.php
│   ├── create-course.php
│   ├── edit-course.php
│   ├── manage-videos.php
│   └── students.php
│
├── classes/
│   ├── User.php
│   ├── Course.php
│   ├── Enrollment.php
│   ├── Payment.php
│   ├── Certificate.php
│   └── Notification.php
│
├── config/
│   ├── config.php
│   └── database.php
│
├── database/
│   └── schema.sql
│
├── uploads/
│   ├── videos/
│   ├── thumbnails/
│   └── certificates/
│
└── assets/
    ├── css/
    ├── js/
    └── img/
```

---

##  Database Design

Main tables included:

- Users
- Categories
- Courses
- Videos
- Enrollments
- Payments
- Certificates
- Ratings
- Messages
- Notifications

---

##  Installation Guide

### Step 1: Clone the Repository

```bash
https://github.com/DevSolan1/Web-Based-E-Learning-Platform.git
```

### Step 2: Move Project to Web Server

Place the project folder inside:

```text
xampp/htdocs/
```

or

```text
www/
```

depending on your local server setup.

### Step 3: Create Database

Open phpMyAdmin and create a database:

```sql
elearning_db
```

### Step 4: Import Database

Import:

```text
database/schema.sql
```

into the created database.

### Step 5: Configure Database Connection

Open:

```php
config/database.php
```

Update the database credentials:

```php
$host = "localhost";
$username = "root";
$password = "";
$database = "elearning_db";
```

### Step 6: Run the Project

Start:

- Apache
- MySQL

Open browser:

```text
http://localhost/elearning
```

---

##  User Roles

### Student
- Enroll in courses
- Watch learning videos
- Track progress
- Download certificates

### Teacher
- Create and manage courses
- Upload videos
- Monitor enrolled students

### Admin
- Manage users
- Manage courses
- Manage payments
- Manage categories

---


---

##  Future Enhancements

- Online Live Classes
- Quiz and Examination Module
- Assignment Submission
- Video Streaming Optimization
- Email Verification
- Mobile Application
- Payment Gateway Integration
- Discussion Forums

---

##  Author

**Solan Abate**

Student Project – Web-Based E-Learning Platform

---

##  License

This project is developed for educational and learning purposes.

Feel free to modify and enhance the system according to your requirements.
