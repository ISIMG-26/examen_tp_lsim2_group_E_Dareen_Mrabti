<?php
require_once "db.php";
session_start();

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if ($email === "" || $password === "") {
        $message = "Email and password are required.";
        $messageType = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageType = "error";
    } else {
        $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user["password"])) {
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_name"] = $user["name"];
                $_SESSION["user_email"] = $user["email"];
                header("Location: index.php");
                exit();
            } else {
                $message = "Incorrect email or password.";
                $messageType = "error";
            }
        } else {
            $message = "Incorrect email or password.";
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
    <title>Login - SportFit</title>
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
                    <a href="login.php" class="active">Login</a>
                    <a href="register.php">Register</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main class="auth-main">
        <section class="auth-card">
            <p class="form-label">Welcome Back</p>
            <h1>Login</h1>

            <?php if ($message !== ""): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form id="loginForm" method="POST" action="login.php" class="auth-form" novalidate>
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

                <button type="submit" class="form-button">Login</button>
            </form>

            <div id="previousAccounts" class="previous-accounts"></div>

            <p class="auth-switch">Need an account? <a href="register.php">Register</a></p>
        </section>
    </main>

    <script src="script.js"></script>
</body>
</html>
