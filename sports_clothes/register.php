<?php
session_start();
require_once "db.php";

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if ($name === "" || $email === "" || $password === "") {
        $message = "All fields are required.";
        $messageType = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageType = "error";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashedPassword);

        if ($stmt->execute()) {
            $message = "Account created successfully. You can now login.";
            $messageType = "success";
        } else {
            $message = "This email is already registered.";
            $messageType = "error";
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
    <title>Register - SportFit</title>
    <link rel="stylesheet" href="style.css">
</head>
<body
    <?php if (isset($_SESSION["user_id"]) && isset($_SESSION["user_email"])): ?>
        data-account-name="<?php echo htmlspecialchars($_SESSION["user_name"], ENT_QUOTES); ?>"
        data-account-email="<?php echo htmlspecialchars($_SESSION["user_email"], ENT_QUOTES); ?>"
    <?php endif; ?>
>
    <header class="site-header">
        <nav class="navbar">
            <a class="logo" href="index.php">SportFit</a>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="products.php">Products</a>
                <?php if (isset($_SESSION["user_id"])): ?>
                    <span class="nav-user">Hi, <?php echo htmlspecialchars($_SESSION["user_name"]); ?></span>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php" class="active">Register</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main class="auth-main">
        <section class="auth-card">
            <p class="form-label">Create Account</p>
            <h1>Register</h1>

            <?php if ($message !== ""): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form id="registerForm" method="POST" action="register.php" class="auth-form" novalidate>
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" placeholder="Your name">
                    <small class="error-text"></small>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="you@example.com">
                    <small class="error-text"></small>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Password">
                    <small class="error-text"></small>
                </div>

                <button type="submit" class="form-button">Register</button>
            </form>

            <p class="auth-switch">Already have an account? <a href="login.php">Login</a></p>
        </section>
    </main>

    <script src="script.js"></script>
</body>
</html>
