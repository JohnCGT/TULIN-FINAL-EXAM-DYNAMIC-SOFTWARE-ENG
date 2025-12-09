<?php
require_once "core/dbConfig.php";
session_start();

// Redirect if already logged in
if(isset($_SESSION['user'])){
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $username = trim($_POST['username'] ?? '');
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if(!$username || !$firstname || !$lastname || !$password || !$confirm){
        $error = "All fields are required.";
    } elseif($password !== $confirm){
        $error = "Passwords do not match.";
    } else {
        // Check if username exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username=? LIMIT 1");
        $stmt->execute([$username]);
        if($stmt->fetch()){
            $error = "Username already exists.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users(username,firstname,lastname,password) VALUES(?,?,?,?)");
            $success = $stmt->execute([$username,$firstname,$lastname,$hash]);
            if($success){
                $success = "Registration successful! <a href='login.php'>Login here</a>.";
            } else {
                $error = "Registration failed. Try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5 bg-white p-4 rounded shadow">
            <h2 class="mb-4 text-center">Register</h2>

            <?php if($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php elseif($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required value="<?= htmlspecialchars($username ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label>First Name</label>
                    <input type="text" name="firstname" class="form-control" required value="<?= htmlspecialchars($firstname ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label>Last Name</label>
                    <input type="text" name="lastname" class="form-control" required value="<?= htmlspecialchars($lastname ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm" class="form-control" required>
                </div>
                <button class="btn btn-success w-100">Register</button>
            </form>
            <p class="mt-3 text-center">Already have an account? <a href="login.php">Login</a></p>
        </div>
    </div>
</div>
</body>
</html>
