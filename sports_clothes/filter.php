<?php
//te7mi el site mel bugs wala security issues
function clean_text($value) {
    return htmlspecialchars($value, ENT_QUOTES, "UTF-8");
}
//t5ali el text looks nice
function title_text($value) {
    return ucwords(str_replace("_", " ", $value));
}
//tnathem les categories mt3 el dbach
function allowed_categories_for_sport($sport) {
    if ($sport === "tennis" || $sport === "gym") {
        return ["set"];
    }

    if ($sport === "football" || $sport === "basketball") {
        return ["shoes", "shirt", "shorts"];
    }

    return [];
}
//ya9ra el tsawer mn esm el file
function category_from_filename($filename) {
    $name = pathinfo($filename, PATHINFO_FILENAME);
    $parts = explode("_", $name);

    if (count($parts) < 2) {
        return "";
    }

    $categoryPart = $parts[1];
    $category = preg_replace("/[0-9]+$/", "", $categoryPart);

    return strtolower($category);
}
//ya9ra el gender ml file name
function gender_from_filename($filename) {
    $name = strtolower(pathinfo($filename, PATHINFO_FILENAME));

    if (strpos($name, "men_") === 0) {
        return "male";
    }

    if (strpos($name, "women_") === 0) {
        return "female";
    }

    return "";
}
//yasna3 soum wahmi automatiquement
function product_price($sport, $category, $filename) {
    $basePrices = [
        "set" => 79,
        "shirt" => 35,
        "shorts" => 29,
        "shoes" => 89
    ];

    $base = isset($basePrices[$category]) ? $basePrices[$category] : 49;
    $variation = strlen($sport . $filename) % 17;

    return $base + $variation;
}

function get_products($selectedSport = "", $selectedGender = "", $selectedCategory = "") {
    $sports = ["tennis", "football", "basketball", "gym"];//define sports
    $products = [];
    $basePath = __DIR__ . DIRECTORY_SEPARATOR . "images";
    //loop through each sport
    foreach ($sports as $sport) {
        if ($selectedSport !== "" && $selectedSport !== $sport) {
            continue;
        }

        $folder = $basePath . DIRECTORY_SEPARATOR . $sport;

        if (!is_dir($folder)) {
            continue;
        }

        $files = scandir($folder);//read files
        //loop each image
        foreach ($files as $file) {
            if ($file === "." || $file === "..") {
                continue;
            }

            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (!in_array($extension, ["jpg", "jpeg", "png", "gif", "webp"])) {
                continue;
            }
            $gender = gender_from_filename($file);//extract data
            $category = category_from_filename($file);
            $allowedCategories = allowed_categories_for_sport($sport);

            if ($gender === "" || $category === "" || !in_array($category, $allowedCategories)) {
                continue;
            }

            if ($selectedGender !== "" && $selectedGender !== $gender) {
                continue;
            }

            if ($selectedCategory !== "" && $selectedCategory !== $category) {
                continue;
            }

            $products[] = [
                "sport" => $sport,
                "gender" => $gender,
                "category" => $category,
                "image" => $file,
                "price" => product_price($sport, $category, $file)
            ];
        }
    }

    return $products;
}

function render_products($products) {
    if (count($products) === 0) {
        echo '<div class="empty-state">No products found for these filters.</div>';
        return;
    }

    foreach ($products as $product) {
        $sport = clean_text($product["sport"]);
        $category = clean_text($product["category"]);
        $gender = clean_text($product["gender"]);
        $image = clean_text($product["image"]);
        $name = title_text($product["sport"] . " " . $product["category"]);
        $price = number_format($product["price"], 2);

        $productId = clean_text($product["sport"] . "-" . $product["gender"] . "-" . $product["category"] . "-" . pathinfo($product["image"], PATHINFO_FILENAME));
        $imagePath = "images/" . $sport . "/" . $image;

        echo '
        <article
            class="product-card"
            data-product-name="' . clean_text($name) . '"
            data-product-sport="' . title_text($sport) . '"
            data-product-category="' . title_text($category) . '"
            data-product-gender="' . title_text($gender) . '"
            data-product-price="' . $price . ' TND"
            data-product-image="' . clean_text($imagePath) . '"
        >
            <div class="product-image-wrap">
                <img src="' . clean_text($imagePath) . '" alt="' . clean_text($name) . '">
            </div>
            <div class="product-info">
                <p class="sport-label">' . title_text($sport) . '</p>
                <h3>' . clean_text($name) . '</h3>
                <div class="product-tags">
                    <span>' . title_text($category) . '</span>
                    <span>' . title_text($gender) . '</span>
                </div>
                <div class="product-bottom">
                    <strong>' . $price . ' TND</strong>
                    <button
                        type="button"
                        class="add-to-cart"
                        data-id="' . $productId . '"
                        data-name="' . clean_text($name) . '"
                        data-price="' . clean_text($price) . '"
                        data-image="' . clean_text($imagePath) . '"
                    >Add to Cart</button>
                </div>
            </div>
        </article>';
    }
}

if (basename($_SERVER["SCRIPT_NAME"]) === "filter.php") {
    $sport = isset($_POST["sport"]) ? strtolower(trim($_POST["sport"])) : "";
    $gender = isset($_POST["gender"]) ? strtolower(trim($_POST["gender"])) : "";
    $category = isset($_POST["category"]) ? strtolower(trim($_POST["category"])) : "";

    $products = get_products($sport, $gender, $category);
    render_products($products);
}
?>
