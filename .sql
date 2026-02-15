CREATE DATABASE study_site;

USE study_site;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('student','teacher') DEFAULT 'student',
    trial_end DATE
);

CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    category VARCHAR(50),
    duration VARCHAR(50),
    difficulty VARCHAR(20),
    price DECIMAL(8,2)
);

CREATE TABLE enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    course_id INT,
    start_date DATE,
    status ENUM('active','expired') DEFAULT 'active',
    FOREIGN KEY(user_id) REFERENCES users(id),
    FOREIGN KEY(course_id) REFERENCES courses(id)
);

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    message TEXT
);

CREATE TABLE uploads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255),
    uploader INT,
    upload_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(uploader) REFERENCES users(id)
);
