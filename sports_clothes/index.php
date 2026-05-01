<?php
session_start();
require_once "filter.php";
$featuredProducts = array_slice(get_products(), 0, 6);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportFit Store</title>
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
                <a href="index.php" class="active">Home</a>
                <a href="products.php">Products</a>
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
        <section class="hero">
            <div class="hero-content">
                <p>Dynamic Sports Clothing Store</p>
                <h1>Shop tennis, football, basketball, and gym outfits.</h1>
                <a href="products.php" class="primary-link">Browse Products</a>
            </div>
        </section>

        <section class="filters-section">
            <div class="section-title">
                <p>Quick Search</p>
                <h2>Find your sports style</h2>
            </div>

            <form id="filterForm" class="filters">
                <div class="filter-group">
                    <label>Sport</label>
                    <input type="hidden" id="sport" name="sport" value="">
                    <div class="choice-buttons">
                        <button type="button" class="choice-button active" data-filter-target="sport" data-filter-value="">All</button>
                        <button type="button" class="choice-button" data-filter-target="sport" data-filter-value="tennis">Tennis</button>
                        <button type="button" class="choice-button" data-filter-target="sport" data-filter-value="football">Football</button>
                        <button type="button" class="choice-button" data-filter-target="sport" data-filter-value="basketball">Basketball</button>
                        <button type="button" class="choice-button" data-filter-target="sport" data-filter-value="gym">Gym</button>
                    </div>
                </div>

                <div class="filter-group">
                    <label>Gender</label>
                    <input type="hidden" id="gender" name="gender" value="">
                    <div class="choice-buttons">
                        <button type="button" class="choice-button active" data-filter-target="gender" data-filter-value="">All</button>
                        <button type="button" class="choice-button" data-filter-target="gender" data-filter-value="male">Men</button>
                        <button type="button" class="choice-button" data-filter-target="gender" data-filter-value="female">Women</button>
                    </div>
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
            <div class="section-title">
                <p>Featured</p>
                <h2>Latest Products</h2>
            </div>
            <div id="productsContainer" class="products-grid">
                <?php render_products($featuredProducts); ?>
            </div>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> SportFit. All rights reserved.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>
