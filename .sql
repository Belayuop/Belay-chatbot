CREATE DATABASE study_site;

USE study_site;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('student','teacher') DEFAULT 'student',
    trial_end DATE
);
-- DATABASE
CREATE DATABASE IF NOT EXISTS academy;
USE academy;

-- USERS TABLE
CREATE TABLE IF NOT EXISTS users(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    role VARCHAR(20),
    subscription VARCHAR(20) DEFAULT 'free'
);

-- MESSAGES TABLE
CREATE TABLE IF NOT EXISTS messages(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    message TEXT
);

-- COURSES TABLE
CREATE TABLE IF NOT EXISTS courses(
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100),
    category VARCHAR(50),
    hours VARCHAR(50),
    description TEXT
);

-- FILES TABLE
CREATE TABLE IF NOT EXISTS files(
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255),
    uploader VARCHAR(50)
);

-- INSERT SAMPLE COURSES
INSERT INTO courses(title,category,hours,description) VALUES
('Python Programming','cs','4-6 hours/week','Beginner to Advanced'),
('Java Programming','cs','4-5 hours/week','Intermediate'),
('HTML & CSS','cs','3-4 hours/week','Beginner'),
('C++ Programming','cs','5 hours/week','Intermediate'),
('Human Anatomy','health','4-6 hours/week','Learn anatomy'),
('Physiology','health','4-5 hours/week','Learn physiology'),
('Biochemistry','health','3-5 hours/week','Learn biochemistry'),
('Pathology','health','4-6 hours/week','Learn pathology'),
('History of Ethiopia','social','3-4 hours/week','History lessons'),
('Geography','social','3-5 hours/week','Learn geography'),
('English Literature','social','3-4 hours/week','Literature lessons'),
('Economics','social','4 hours/week','Economics lessons'),
('Ethiopian Entrance Exam Prep','grade12','6-8 hours/week','Full preparation'),
('Mathematics','remedial','2-4 hours/week','Math remedial'),
('Biology','remedial','2-4 hours/week','Biology remedial'),
('Chemistry','remedial','2-4 hours/week','Chemistry remedial'),
('Physics','remedial','2-4 hours/week','Physics remedial'),
('Agriculture','remedial','2-3 hours/week','Agriculture remedial'),
('Cybersecurity Basics','short','2-3 hours/week','Short cybersecurity course'),
('Data Science Fundamentals','short','3-4 hours/week','Short data science course'),
('AI and Machine Learning','short','4-5 hours/week','Short AI course');

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
