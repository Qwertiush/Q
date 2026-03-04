# 📝 Simple Blog Application

## 📌 Project Goal

The goal of this project is to create a web-based blog application that allows users to share their opinions publicly. The platform enables registered users to create posts, comment on them, and interact through reactions in a clean and minimalist interface.

---

## 🛠 Technologies Used

- PHP
- HTML5
- CSS
- MariaDB
- XAMPP

---

## 🚀 Functional Requirements

### 👤 User Management

- User registration and login system
- Only one account per unique email address
- Passwords stored securely in hashed format
- Ability to edit user avatar

### 📝 Posts

- Users can create posts
- Users can delete their own posts
- Each post includes:
  - Title
  - Content
  - Creation date
  - Author reference

### 💬 Comments

- Users can add comments to posts
- Each comment includes:
  - Content
  - Creation date
  - Author reference
  - Related post reference

### 👍 Reactions

- Users can react to both posts and comments
- Reaction types:
  - Like (positive)
  - Dislike (negative)
- A reaction can belong to:
  - A post (comment_id = NULL)
  - A comment (post_id = NULL)

### ✅ Validation & Error Handling

- Form input validation
- Displaying user-friendly error messages

---

## 🎨 Non-Functional Requirements

- Responsive UI/UX
- Intuitive and user-friendly interface
- Minimalist design
- Proper UI scaling depending on device screen size

---

## 🗄 Database Structure

### 👤 Users

- `id`
- `username`
- `email` (unique)
- `password` (hashed)
- `avatar` (stored as BLOB)

### 📝 Posts

- `id`
- `user_id` (author)
- `title`
- `content`
- `created_at`

### 💬 Comments

- `id`
- `post_id`
- `user_id`
- `content`
- `created_at`

### 👍 Reactions

- `id`
- `user_id`
- `post_id` (nullable)
- `comment_id` (nullable)
- `type` (like / dislike)

---

## ⚙️ Installation

1. Install XAMPP
2. Start Apache and MariaDB
3. Import the database schema into MariaDB
4. Place project files inside the `htdocs` directory
5. Open your browser and go to: http://localhost/project-folder
---

## 📖 Project Overview

This project demonstrates:

- Backend development with PHP
- Relational database design
- Secure authentication mechanisms
- CRUD operations
- Form validation
- Responsive front-end design
- User interaction systems (comments & reactions)

---

## 📌 Status

This project was created for educational purposes to demonstrate full-stack web development fundamentals using PHP and MariaDB.
