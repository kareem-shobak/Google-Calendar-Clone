<?php 

require '../connection.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $userEmail = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$userEmail || !$password) {
        $error = 'Email and password are required';
    } else {
        $stmt = $connection->prepare(
            "SELECT id, name, email, password FROM `users` WHERE email = ?"
        );

        $stmt->bind_param("s",$userEmail);
        $stmt->execute();

        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {

            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            header("Location: ../index.php");
            exit;
        } else {
            $error = 'Invalid email or password';
        }

        $stmt->close();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../login.css">
</head>
<body>

<div class="auth-container">
    <h2>Welcome Back</h2>
    <p class="subtitle">Login to your calendar</p>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit">Login</button>
    </form>

    <div class="auth-footer">
        Don’t have an account?
        <a href="register.php">Create one</a>
    </div>
</div>

</body>
</html>