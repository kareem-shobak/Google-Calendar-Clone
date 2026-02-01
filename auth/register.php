<?php 

require '../connection.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userName = trim($_POST['name'] ?? '');
    $userEmail = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if(!$userName || !$userEmail || !$password){
        $error = "All fields are required";
    } elseif (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } 
    else {

        $stmt = $connection->prepare(
            "SELECT id From `users` WHERE email = ?"
        );

        $stmt->bind_param("s",$userEmail);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = 'Email already registered';
        } else {
            $passwordHashed = password_hash($password,PASSWORD_DEFAULT);

            $stmt = $connection->prepare(
                "INSERT INTO `users` (name,email,password) VALUES (?,?,?)"
            );

            $stmt->bind_param("sss",$userName,$userEmail,$passwordHashed);
            $stmt->execute();

            $_SESSION['user_id']   = $stmt->insert_id;
            $_SESSION['user_name'] = $name;

            header("Location: ../index.php");
            exit;
        }

        $stmt->close();
    }

}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create A new Account</title>
</head>
<body>
    <link rel="stylesheet" href="../register.css">

<div class="auth-container">
    <h2>Create New Account</h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="register.php" method="POST">

        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit">Create Account</button>
    </form>

    <div class="auth-footer">
        Already have an account?
        <a href="login.php">Login</a>
    </div>
</div>

</body>
</html>