<?php
// =======================
// CONFIGURATION / DATABASE
// =======================
$servername = "localhost";
$username = "root";        // Change to your DB username
$password = "";            // Change to your DB password
$dbname = "belay_website"; // Change to your DB name

// Connect
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// =======================
// HANDLE REGISTRATION
// =======================
$reg_msg = "";
if(isset($_POST['register'])){
    $uname = $conn->real_escape_string($_POST['uname']);
    $email = $conn->real_escape_string($_POST['email']);
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username,email,password) VALUES ('$uname','$email','$pass')";
    if($conn->query($sql)){ $reg_msg = "Registration successful!"; } 
    else { $reg_msg = "Error: " . $conn->error; }
}

// =======================
// HANDLE LOGIN
// =======================
$login_msg = "";
session_start();
if(isset($_POST['login'])){
    $email = $conn->real_escape_string($_POST['email']);
    $pass = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);
    if($result->num_rows == 1){
        $row = $result->fetch_assoc();
        if(password_verify($pass, $row['password'])){
            $_SESSION['user'] = $row['username'];
            $login_msg = "Login successful!";
        } else { $login_msg = "Incorrect password."; }
    } else { $login_msg = "Email not found."; }
}

// =======================
// HANDLE CONTACT FORM
// =======================
$contact_msg = "";
if(isset($_POST['contact'])){
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $message = $conn->real_escape_string($_POST['message']);

    $sql = "INSERT INTO messages (name,email,message) VALUES ('$name','$email','$message')";
    if($conn->query($sql)){ $contact_msg = "Message sent successfully!"; }
    else { $contact_msg = "Error: " . $conn->error; }
}

// =======================
// HANDLE FILE UPLOAD
// =======================
$upload_msg = "";
if(isset($_POST['upload'])){
    $filename = $_FILES['file']['name'];
    $tmpname = $_FILES['file']['tmp_name'];
    $target = "uploads/".$filename;

    if(move_uploaded_file($tmpname,$target)){
        $sql = "INSERT INTO uploads (filename,user) VALUES ('$filename','".$_SESSION['user']."')";
        $conn->query($sql);
        $upload_msg = "File uploaded successfully!";
    } else { $upload_msg = "Error uploading file."; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Belay Kassanew - Full Feature Website</title>

<!-- FONTS & ICONS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<style>
/* ===== GENERAL ===== */
body{font-family:'Segoe UI',Arial,sans-serif;margin:0;padding:0;background:#f3f7f3;color:#333;overflow-x:hidden;}
h1,h2,h3,h4,h5,h6{margin:0;}
a{text-decoration:none;color:inherit;}

/* ===== HEADER ===== */
header{
    background: linear-gradient(135deg,#6a0dad,#00ff7f);
    color:white;text-align:center;padding:60px 20px;
    border-bottom-left-radius:50px;border-bottom-right-radius:50px;box-shadow:0 5px 15px rgba(0,0,0,0.2);
    animation: fadeIn 2s ease-in-out;
}
header h1{font-size:3rem;margin-bottom:10px;}
header p{font-size:1.2rem;opacity:0.9;}

/* ===== NAVIGATION ===== */
nav{
    background:#6a0dad;display:flex;justify-content:center;position:sticky;top:0;z-index:1000;
}
nav a{color:white;padding:12px 25px;font-weight:bold;transition:0.3s;}
nav a:hover{background:#00ff7f;border-radius:8px;color:#000;}

/* ===== SECTIONS ===== */
.section{width:90%;max-width:1200px;margin:50px auto;padding:30px;background:white;border-radius:20px;box-shadow:0 5px 20px rgba(0,0,0,0.1);transition:transform 0.3s ease;}
.section:hover{transform:translateY(-5px);box-shadow:0 10px 25px rgba(0,0,0,0.2);}
.section h2{color:#6a0dad;margin-bottom:20px;text-align:center;}

/* ===== INTRO ===== */
.intro-box{display:flex;flex-wrap:wrap;align-items:center;justify-content:center;gap:30px;}
.profile-image{width:200px;height:200px;border-radius:50%;border:5px solid #6a0dad;object-fit:cover;transition:transform 0.3s;}
.profile-image:hover{transform:scale(1.05);}

/* ===== FORMS ===== */
input,textarea{width:100%;padding:12px;margin:10px 0;border-radius:12px;border:1px solid #bbb;font-size:1rem;box-sizing:border-box;}
textarea{height:150px;}
button.btn{background:linear-gradient(45deg,#6a0dad,#00ff7f);color:white;padding:15px 30px;border:none;border-radius:25px;font-size:1.1rem;cursor:pointer;transition:0.3s;}
button.btn:hover{transform:scale(1.05);background:#6a0dad;}

/* ===== PORTFOLIO & UPLOAD ===== */
.portfolio-list{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;}
.card{padding:20px;background:#f0f8ff;border-left:5px solid #6a0dad;border-radius:15px;box-shadow:0 3px 12px rgba(0,0,0,0.1);transition:0.3s;}
.card:hover{transform:scale(1.03);box-shadow:0 10px 20px rgba(0,0,0,0.2);text-align:center;}

/* ===== YOUTUBE ===== */
.youtube-container{position:relative;padding-bottom:56.25%;height:0;overflow:hidden;margin:20px 0;}
.youtube-container iframe{position:absolute;top:0;left:0;width:100%;height:100%;border-radius:20px;}

/* ===== FOOTER ===== */
footer{text-align:center;padding:20px;background:#6a0dad;color:white;border-radius:20px 20px 0 0;margin-top:50px;}

/* ===== ANIMATION ===== */
@keyframes fadeIn{0%{opacity:0;transform:translateY(-20px);}100%{opacity:1;transform:translateY(0);}}

@media(max-width:768px){.intro-box{flex-direction:column;text-align:center;}}
</style>

<script>
// ===== SMOOTH SCROLL =====
document.addEventListener('DOMContentLoaded',()=>{
    document.querySelectorAll('nav a').forEach(link=>{
        link.addEventListener('click',e=>{
            e.preventDefault();
            const target=document.querySelector(link.getAttribute('href'));
            target.scrollIntoView({behavior:'smooth',block:'start'});
        });
    });
});
</script>
</head>

<body>

<header>
<h1>Belay Kassanew</h1>
<p>Computer Science & Public Health | Educator | Developer</p>
</header>

<nav>
<a href="#home">Home</a>
<a href="#about">About</a>
<a href="#courses">Courses</a>
<a href="#youtube">YouTube</a>
<a href="#contact">Contact</a>
<a href="#upload">Upload</a>
<a href="#login">Login/Register</a>
</nav>

<!-- HOME / INTRO -->
<section class="section" id="home">
<div class="intro-box">
<img src="belay.jpg" class="profile-image">
<div>
<h2>Welcome!</h2>
<p>Hi, I am <strong>Belay Kassanew</strong>. I create content for learning, software projects, and health education.</p>
</div>
</div>
</section>

<!-- ABOUT -->
<section class="section" id="about">
<h2>About Me</h2>
<p>I study Computer Science and Public Health. I am passionate about teaching, IoT systems, epidemiology research, and software development.</p>
</section>

<!-- COURSES / HEALTH & PROGRAMMING -->
<section class="section" id="courses">
<h2>Courses & Notes</h2>
<div class="portfolio-list">
<div class="card"><strong>Health Science Notes</strong></div>
<div class="card"><strong>Programming Tutorials</strong></div>
<div class="card"><strong>IoT Projects</strong></div>
<div class="card"><strong>Past Exam Papers</strong></div>
</div>
</section>

<!-- YOUTUBE -->
<section class="section" id="youtube">
<h2>My YouTube Video</h2>
<div class="youtube-container">
<iframe src="https://www.youtube.com/embed/DHAD3JuETfs" frameborder="0" allowfullscreen></iframe>
</div>
</section>

<!-- CONTACT -->
<section class="section" id="contact">
<h2>Contact Me</h2>
<?php if($contact_msg) echo "<p style='color:green;'>$contact_msg</p>"; ?>
<form method="POST">
<input type="text" name="name" placeholder="Your Name" required>
<input type="email" name="email" placeholder="Your Email" required>
<textarea name="message" placeholder="Your Message"></textarea>
<button class="btn" name="contact">Send Message</button>
</form>
</section>

<!-- FILE UPLOAD -->
<section class="section" id="upload">
<h2>Upload Your File</h2>
<?php if($upload_msg) echo "<p style='color:green;'>$upload_msg</p>"; ?>
<?php if(isset($_SESSION['user'])): ?>
<form method="POST" enctype="multipart/form-data">
<input type="file" name="file" required>
<button class="btn" name="upload">Upload</button>
</form>
<?php else: ?>
<p>Please login to upload files.</p>
<?php endif; ?>
</section>

<!-- LOGIN / REGISTER -->
<section class="section" id="login">
<h2>Login / Register</h2>
<?php if($reg_msg) echo "<p style='color:green;'>$reg_msg</p>"; ?>
<?php if($login_msg) echo "<p style='color:green;'>$login_msg</p>"; ?>
<form method="POST">
<input type="text" name="uname" placeholder="Username (for register)">
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>
<button class="btn" name="register">Register</button>
<button class="btn" name="login">Login</button>
</form>
</section>

<footer>
<p>© 2025 Belay Kassanew | All Rights Reserved</p>
</footer>

</body>
</html>
