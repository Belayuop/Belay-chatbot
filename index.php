<?php
session_start();
$host="localhost"; $user="root"; $password=""; $dbname="study_site";
$conn = new mysqli($host,$user,$password,$dbname);
if($conn->connect_error) die("DB Error: ".$conn->connect_error);

// ===== LOGIN =====
if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s",$username);
    $stmt->execute();
    $res = $stmt->get_result();
    if($res->num_rows>0){
        $user = $res->fetch_assoc();
        if(password_verify($password,$user['password'])){
            $_SESSION['user_id']=$user['id'];
            $_SESSION['username']=$user['username'];
            $_SESSION['role']=$user['role'];
            $_SESSION['trial_end']=$user['trial_end'];
        } else $error="Invalid password!";
    } else $error="User not found!";
}

// ===== REGISTER =====
if(isset($_POST['register'])){
    $username=$_POST['username'];
    $password=password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role='student';
    $trial_end = date('Y-m-d', strtotime('+30 days')); // 30-day free trial
    $stmt=$conn->prepare("INSERT INTO users(username,password,role,trial_end) VALUES(?,?,?,?)");
    $stmt->bind_param("ssss",$username,$password,$role,$trial_end);
    if($stmt->execute()) $success="Registered! 30-day free trial active.";
    else $error="Registration failed!";
}

// ===== LOGOUT =====
if(isset($_GET['logout'])){ session_destroy(); header("Location:index.php"); exit; }

// ===== CONTACT FORM =====
if(isset($_POST['contact'])){
    $name=$_POST['name']; $email=$_POST['email']; $message=$_POST['message'];
    $stmt=$conn->prepare("INSERT INTO messages(name,email,message) VALUES(?,?,?)");
    $stmt->bind_param("sss",$name,$email,$message);
    $stmt->execute();
    $success="Message sent!";
}

// ===== FILE UPLOAD =====
if(isset($_POST['upload'])){
    $target_dir="uploads/";
    if(!is_dir($target_dir)) mkdir($target_dir,0777,true);
    $filename = basename($_FILES["file"]["name"]);
    $target_file=$target_dir.$filename;
    if(move_uploaded_file($_FILES["file"]["tmp_name"],$target_file)){
        $stmt=$conn->prepare("INSERT INTO uploads(filename,uploader) VALUES(?,?)");
        $stmt->bind_param("si",$filename,$_SESSION['user_id']);
        $stmt->execute();
        $success="File uploaded!";
    } else $error="Upload failed!";
}

// ===== ENROLL IN COURSE =====
if(isset($_GET['enroll']) && isset($_SESSION['user_id'])){
    $course_id=intval($_GET['enroll']);
    $stmt=$conn->prepare("SELECT * FROM enrollments WHERE user_id=? AND course_id=?");
    $stmt->bind_param("ii",$_SESSION['user_id'],$course_id);
    $stmt->execute();
    if($stmt->get_result()->num_rows==0){
        $stmt=$conn->prepare("INSERT INTO enrollments(user_id,course_id,start_date) VALUES(?,?,NOW())");
        $stmt->bind_param("ii",$_SESSION['user_id'],$course_id);
        $stmt->execute();
        $success="Enrolled successfully!";
    } else $error="Already enrolled!";
}

// ===== GET COURSES =====
$courses=$conn->query("SELECT * FROM courses ORDER BY category,title ASC");
?>
