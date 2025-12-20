<?php
session_start();
$host = "localhost";
$user = "root";
$password = "";
$dbname = "study_site";
$conn = new mysqli($host,$user,$password,$dbname);
if($conn->connect_error) die("DB error ".$conn->connect_error);

// LOGIN HANDLER
if(isset($_POST['login'])){
    $username=$_POST['username'];
    $password=$_POST['password'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s",$username);
    $stmt->execute();
    $res=$stmt->get_result();
    if($res->num_rows>0){
        $row=$res->fetch_assoc();
        if(password_verify($password,$row['password'])){
            $_SESSION['username']=$row['username'];
            $_SESSION['role']=$row['role'];
        } else $error="Invalid password";
    } else $error="User not found";
}

// REGISTER HANDLER
if(isset($_POST['register'])){
    $username=$_POST['username'];
    $password=password_hash($_POST['password'],PASSWORD_DEFAULT);
    $role='student';
    $stmt = $conn->prepare("INSERT INTO users(username,password,role) VALUES(?,?,?)");
    $stmt->bind_param("sss",$username,$password,$role);
    if($stmt->execute()) $success="Registered successfully! Login now.";
    else $error="Registration failed!";
}

// LOGOUT
if(isset($_GET['logout'])){ session_destroy(); header("Location:index.php"); exit; }

// CONTACT FORM
if(isset($_POST['contact'])){
    $name=$_POST['name']; $email=$_POST['email']; $message=$_POST['message'];
    $stmt=$conn->prepare("INSERT INTO messages(name,email,message) VALUES(?,?,?)");
    $stmt->bind_param("sss",$name,$email,$message);
    $stmt->execute();
    $success="Message sent!";
}

// FILE UPLOAD
if(isset($_POST['upload'])){
    $target_dir="uploads/";
    $target_file=$target_dir.basename($_FILES["file"]["name"]);
    if(move_uploaded_file($_FILES["file"]["tmp_name"],$target_file)) $success="File uploaded!";
    else $error="Upload error!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Belay Kassanew - CS & Health Student</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">

<header>
<h1>Belay Kassanew</h1>
<p>Computer Science & Health Student | Educator | IoT Enthusiast</p>
</header>

<nav>
<a class="nav-link active" data-target="home">Home</a>
<a class="nav-link" data-target="about">About Me</a>
<a class="nav-link" data-target="courses">Courses</a>
<a class="nav-link" data-target="youtube">YouTube</a>
<a class="nav-link" data-target="contact">Contact</a>
<?php if(!isset($_SESSION['username'])): ?>
    <a class="nav-link" data-target="login">Login</a>
<?php else: ?>
    <a class="nav-link" data-target="dashboard">Dashboard</a>
    <a href="?logout=true" class="nav-link">Logout</a>
<?php endif; ?>
</nav>

<!-- HOME -->
<section id="home" class="active">
<h2>Welcome!</h2>
<p>Hello! I’m Belay Kassanew. I teach programming, health science, and IoT projects.</p>
<img src="https://github.com/Belayuop/Belay-chatbot/blob/e20538ed3fed1d6445f922fd1f26015377657893/intersystems-ai-healthcare-1500-800.jpg?raw=true" alt="Healthcare AI" class="main-img">
</section>

<!-- ABOUT -->
<section id="about">
<h2>About Me</h2>
<p>I’m a Computer Science and Public Health student. I love programming, IoT, and helping students learn online.</p>
</section>

<!-- COURSES -->
<section id="courses">
<h2>Courses</h2>
<div class="learning-box">
<div class="course-card"><h3>Health Science</h3><p>Epidemiology, public health, medical statistics, and practical projects.</p></div>
<div class="course-card"><h3>Programming & CS</h3><p>Python, JavaScript, Flask, HTML/CSS, IoT projects, and software development.</p></div>
</div>
</section>

<!-- YOUTUBE -->
<section id="youtube">
<h2>My YouTube Video</h2>
<iframe src="https://www.youtube.com/embed/DHAD3JuETfs" frameborder="0" allowfullscreen></iframe>
</section>

<!-- CONTACT -->
<section id="contact">
<h2>Contact Me</h2>
<?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
<?php if(isset($success)) echo "<p class='success'>$success</p>"; ?>
<form method="POST">
<input type="text" name="name" placeholder="Name" required>
<input type="email" name="email" placeholder="Email" required>
<textarea name="message" placeholder="Message" required></textarea>
<button type="submit" name="contact">Send</button>
</form>
<div class="socials">
<a href="https://t.me/campusppt" class="telegram">Telegram</a>
<a href="https://linkedin.com/in/belay-k-54743720a" class="linkedin">LinkedIn</a>
<a href="https://facebook.com/belaykassanew.wasie" class="facebook">Facebook</a>
<a href="https://instagram.com/belaykassanew" class="instagram">Instagram</a>
</div>
</section>

<!-- LOGIN / REGISTER -->
<?php if(!isset($_SESSION['username'])): ?>
<section id="login">
<h2>Login</h2>
<form method="POST">
<input type="text" name="username" placeholder="Username" required>
<input type="password" name="password" placeholder="Password" required>
<button type="submit" name="login">Login</button>
</form>

<h3>Or Register</h3>
<form method="POST">
<input type="text" name="username" placeholder="Username" required>
<input type="password" name="password" placeholder="Password" required>
<button type="submit" name="register">Register</button>
</form>
<?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
<?php if(isset($success)) echo "<p class='success'>$success</p>"; ?>
</section>
<?php endif; ?>

<!-- DASHBOARD -->
<?php if(isset($_SESSION['username'])): ?>
<section id="dashboard">
<h2>Dashboard</h2>
<p>Welcome <?php echo $_SESSION['username']; ?>!</p>

<form method="POST" enctype="multipart/form-data">
<input type="file" name="file" required>
<button type="submit" name="upload">Upload File</button>
</form>

<h3>Resources</h3>
<p>Programming & Health notes, videos, and tutorials will appear here.</p>
</section>
<?php endif; ?>

<footer>
<p>© 2025 Belay Kassanew — All Rights Reserved</p>
</footer>
</div>
<script src="script.js"></script>
</body>
</html>
