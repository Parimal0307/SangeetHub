# SangeetHub 🎵

SangeetHub is a music-themed social web platform that allows users to share, explore, and interact with music-related content. Inspired by the idea of combining music enthusiasm with social networking, SangeetHub offers an engaging and intuitive interface where users can upload YouTube videos, like and save posts, comment on them, and discover music shared by others.


---

## 📌 Features

- 🔐 User Authentication (Login & Registration)
- 👤 Profile display with username and profile picture
- 🎥 Post sharing with YouTube video links
- ❤️ Like and 💾 Save functionalities with real-time feedback
- 💬 Commenting system
- 🔍 Search functionality by username
- 📄 Post metadata display (likes, comments, timestamps)
- 📱 Mobile-first responsive design *(Upcoming)*

---

## 🛠️ Technologies Used

### Frontend:
- HTML5
- CSS3
- JavaScript (Vanilla)

### Backend:
- PHP
- MySQL

### Other Tools:
- XAMPP / LAMP (for local server setup)
- VS Code

---

## 📋 Installation

1. **Clone the repository**
   git clone https://github.com/your-username/sangeethub.git

---

## Move project to your server root
* For XAMPP: htdocs/sangeethub/
* For LAMP: /var/www/html/sangeethub/

---

## Setup the Database
* Import the provided SQL file (if any) using phpMyAdmin or MySQL CLI.

---

## Configure Database Connection
* Navigate to /includes/config.php
* Set your database name, username, and password.

---

## Start Apache and MySQL
* Access the project at: http://localhost/sangeethub/

---

## 🧩 Folder Structure

sangeethub/
│
├── actions/               # Backend action handlers (like, save, comment)
├── assets/                # Images, icons, styles
├── auth/                  # User login and logout
├── includes/              # config.php, DB connection
├── uploads/               # User profile images
├── pages/                 # Login, Register, Home, etc.
├── index.php              # Main entry point
├── style.css              # Common styling
└── README.md

---

## 🎯 Future Scope
* Mobile responsiveness
* Search enhancement (tag-based)
* Notification system for likes, comments, and new posts
* Share feature for internal user content sharing
* User following/follower system

--- 

## 📃 License
* This project is for academic and learning purposes only.