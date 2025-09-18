<?php
require 'config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: view_profile.php?id=" . $_SESSION['user_id']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $users = read_json('data/users.json');
    $user = array_filter($users, fn($u) => $u['username'] == $username);

    if (!empty($user)) {
        $user = $user[array_key_first($user)];
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            header("Location: view_profile.php?id=" . $user['id']);
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="styles.php">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Poppins:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="dark-mode">
    <main class="main-content login-page">
        <div class="login-card">
            <h2>Login to Your Account</h2>
            <form method="POST" class="login-form">
                <label for="username">Username:
                    <input type="text" id="username" name="username" required placeholder="Enter your username">
                </label>
                <label for="password">Password:
                    <input type="password" id="password" name="password" required placeholder="Enter your password">
                </label>
                <button type="submit">Login</button>
                <?php if (isset($error)) echo "<p class='error-message'>$error</p>"; ?>
            </form>
        </div>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js"></script>
    <script>
        gsap.from(".login-card", { opacity: 0, y: 50, duration: 0.8, ease: "power3.out" });
    </script>
</body>
</html>