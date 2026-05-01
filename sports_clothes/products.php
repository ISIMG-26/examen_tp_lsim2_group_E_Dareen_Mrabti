<?php
session_start();
require_once "filter.php";
$products = get_products();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - SportFit</title>
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
                <a href="products.php" class="active">Products</a>
                <?php if (isset($_SESSION["user_id"])): ?>
                    <span class="nav-user">Hi, <?php echo htmlspecialchars($_SESSION["user_name"]); ?></span>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main>
        <section class="page-heading">
            <p>Automatic Catalog</p>
            <h1>Products from image folders</h1>
        </section>

        <section class="filters-section compact">
            <form id="filterForm" class="filters">
                <div class="filter-group">
                    <label for="sport">Sport</label>
                    <select id="sport" name="sport">
                        <option value="">All Sports</option>
                        <option value="tennis">Tennis</option>
                        <option value="football">Football</option>
                        <option value="basketball">Basketball</option>
                        <option value="gym">Gym</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender">
                        <option value="">All Genders</option>
                        <option value="male">Men</option>
                        <option value="female">Women</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="category">Category</label>
                    <select id="category" name="category">
                        <option value="">All Categories</option>
                        <option value="set">Sets</option>
                        <option value="shoes">Shoes</option>
                        <option value="shirt">Shirts</option>
                        <option value="shorts">Shorts</option>
                    </select>
                </div>
            </form>
        </section>

        <section class="products-section">
            <div id="productsContainer" class="products-grid">
                <?php render_products($products); ?>
            </div>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> SportFit. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>
