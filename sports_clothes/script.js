document.addEventListener("DOMContentLoaded", function () {
    var filterForm = document.getElementById("filterForm");
    var productsContainer = document.getElementById("productsContainer");
    var cart = loadCart();

    rememberCurrentAccount();
    showPreviousAccounts();
    createCartBox();
    createProductModal();
    updateCartBox();

    if (filterForm && productsContainer) {
        var controls = filterForm.querySelectorAll("select");
        var buttons = filterForm.querySelectorAll(".choice-button");

        controls.forEach(function (control) {
            control.addEventListener("change", loadFilteredProducts);
        });

        buttons.forEach(function (button) {
            button.addEventListener("click", function () {
                var targetName = button.getAttribute("data-filter-target");
                var targetValue = button.getAttribute("data-filter-value");
                var targetInput = filterForm.querySelector('[name="' + targetName + '"]');
                var relatedButtons = filterForm.querySelectorAll('[data-filter-target="' + targetName + '"]');

                if (!targetInput) {
                    return;
                }

                targetInput.value = targetValue;

                relatedButtons.forEach(function (relatedButton) {
                    relatedButton.classList.remove("active");
                });

                button.classList.add("active");
                loadFilteredProducts();
            });
        });

        filterForm.addEventListener("submit", function (event) {
            event.preventDefault();
            loadFilteredProducts();
        });
    }

    function loadFilteredProducts() {
        var formData = new FormData(filterForm);
        productsContainer.classList.add("loading");

        fetch("filter.php", {
            method: "POST",
            body: formData
        })
            .then(function (response) {
                return response.text();
            })
            .then(function (html) {
                productsContainer.innerHTML = html;
            })
            .catch(function () {
                productsContainer.innerHTML = '<div class="empty-state">Something went wrong while loading products.</div>';
            })
            .finally(function () {
                productsContainer.classList.remove("loading");
            });
    }

    validateForm("registerForm", ["name", "email", "password"]);
    validateForm("loginForm", ["email", "password"]);

    document.addEventListener("click", function (event) {
        if (event.target.classList.contains("add-to-cart")) {
            addToCart(event.target);
            return;
        }

        var productCard = event.target.closest(".product-card");
        if (productCard) {
            openProductModal(productCard);
            return;
        }

        if (event.target.classList.contains("cart-clear")) {
            cart = [];
            saveCart();
            updateCartBox();
        }

        if (event.target.classList.contains("product-modal") || event.target.classList.contains("modal-close")) {
            closeProductModal();
        }
    });

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape") {
            closeProductModal();
        }
    });

    function addToCart(button) {
        var product = {
            id: button.getAttribute("data-id"),
            name: button.getAttribute("data-name"),
            price: Number(button.getAttribute("data-price")),
            image: button.getAttribute("data-image"),
            quantity: 1
        };

        var existingProduct = cart.find(function (item) {
            return item.id === product.id;
        });

        if (existingProduct) {
            existingProduct.quantity += 1;
        } else {
            cart.push(product);
        }

        saveCart();
        updateCartBox();

        button.textContent = "Added";
        button.classList.add("added");

        setTimeout(function () {
            button.textContent = "Add to Cart";
            button.classList.remove("added");
        }, 900);
    }

    function loadCart() {
        var savedCart = localStorage.getItem("sportsCart");

        if (!savedCart) {
            return [];
        }

        try {
            return JSON.parse(savedCart);
        } catch (error) {
            return [];
        }
    }

    function saveCart() {
        localStorage.setItem("sportsCart", JSON.stringify(cart));
    }

    function createCartBox() {
        var cartBox = document.createElement("aside");
        cartBox.className = "cart-box";
        cartBox.innerHTML = '<div class="cart-header"><strong>Cart</strong><span id="cartCount">0 items</span></div><div id="cartItems" class="cart-items"></div><div class="cart-footer"><strong id="cartTotal">0.00 TND</strong><button type="button" class="cart-clear">Clear</button></div>';
        document.body.appendChild(cartBox);
    }

    function createProductModal() {
        var modal = document.createElement("section");
        modal.className = "product-modal";
        modal.id = "productModal";
        modal.innerHTML = '<div class="modal-content"><button type="button" class="modal-close" aria-label="Close product details">Close</button><div class="modal-image-wrap"><img id="modalProductImage" src="" alt=""></div><div class="modal-info"><p id="modalProductSport" class="sport-label"></p><h2 id="modalProductName"></h2><div class="modal-tags"><span id="modalProductCategory"></span><span id="modalProductGender"></span></div><strong id="modalProductPrice"></strong></div></div>';
        document.body.appendChild(modal);
    }

    function openProductModal(card) {
        var modal = document.getElementById("productModal");
        var image = document.getElementById("modalProductImage");

        if (!modal || !image) {
            return;
        }

        var name = card.getAttribute("data-product-name");
        image.src = card.getAttribute("data-product-image");
        image.alt = name;
        document.getElementById("modalProductSport").textContent = card.getAttribute("data-product-sport");
        document.getElementById("modalProductName").textContent = name;
        document.getElementById("modalProductCategory").textContent = card.getAttribute("data-product-category");
        document.getElementById("modalProductGender").textContent = card.getAttribute("data-product-gender");
        document.getElementById("modalProductPrice").textContent = card.getAttribute("data-product-price");
        modal.classList.add("open");
    }

    function closeProductModal() {
        var modal = document.getElementById("productModal");

        if (modal) {
            modal.classList.remove("open");
        }
    }

    function updateCartBox() {
        var cartItems = document.getElementById("cartItems");
        var cartCount = document.getElementById("cartCount");
        var cartTotal = document.getElementById("cartTotal");
        var totalItems = 0;
        var totalPrice = 0;

        if (!cartItems || !cartCount || !cartTotal) {
            return;
        }

        cartItems.innerHTML = "";

        if (cart.length === 0) {
            cartItems.innerHTML = '<p class="cart-empty">Your cart is empty.</p>';
        }

        cart.forEach(function (item) {
            totalItems += item.quantity;
            totalPrice += item.price * item.quantity;

            var row = document.createElement("div");
            row.className = "cart-item";
            row.innerHTML = '<img src="' + item.image + '" alt="' + item.name + '"><div><p>' + item.name + '</p><small>Qty: ' + item.quantity + '</small></div><strong>' + (item.price * item.quantity).toFixed(2) + ' TND</strong>';
            cartItems.appendChild(row);
        });

        cartCount.textContent = totalItems + (totalItems === 1 ? " item" : " items");
        cartTotal.textContent = totalPrice.toFixed(2) + " TND";
    }

    function rememberCurrentAccount() {
        var name = document.body.getAttribute("data-account-name");
        var email = document.body.getAttribute("data-account-email");

        if (!name || !email) {
            return;
        }

        var accounts = loadAccounts();
        var existingAccount = accounts.find(function (account) {
            return account.email === email;
        });

        if (existingAccount) {
            existingAccount.name = name;
            existingAccount.lastLogin = new Date().toLocaleString();
        } else {
            accounts.unshift({
                name: name,
                email: email,
                lastLogin: new Date().toLocaleString()
            });
        }

        localStorage.setItem("sportsAccounts", JSON.stringify(accounts));
    }

    function showPreviousAccounts() {
        var accountBox = document.getElementById("previousAccounts");
        var emailInput = document.getElementById("email");

        if (!accountBox || !emailInput) {
            return;
        }

        var accounts = loadAccounts();

        if (accounts.length === 0) {
            return;
        }

        var title = document.createElement("p");
        title.className = "previous-title";
        title.textContent = "Previous accounts";
        accountBox.appendChild(title);

        accounts.forEach(function (account) {
            var button = document.createElement("button");
            var name = document.createElement("strong");
            var email = document.createElement("span");

            button.type = "button";
            button.className = "account-choice";
            name.textContent = account.name;
            email.textContent = account.email;

            button.appendChild(name);
            button.appendChild(email);
            button.addEventListener("click", function () {
                emailInput.value = account.email;
                emailInput.focus();
            });

            accountBox.appendChild(button);
        });
    }

    function loadAccounts() {
        var savedAccounts = localStorage.getItem("sportsAccounts");

        if (!savedAccounts) {
            return [];
        }

        try {
            return JSON.parse(savedAccounts);
        } catch (error) {
            return [];
        }
    }
});

function validateForm(formId, fields) {
    var form = document.getElementById(formId);

    if (!form) {
        return;
    }

    form.addEventListener("submit", function (event) {
        var isValid = true;

        fields.forEach(function (fieldName) {
            var input = form.querySelector('[name="' + fieldName + '"]');
            var errorText = input.parentElement.querySelector(".error-text");
            var value = input.value.trim();

            errorText.textContent = "";
            input.classList.remove("input-error");

            if (value === "") {
                errorText.textContent = "This field is required.";
                input.classList.add("input-error");
                isValid = false;
                return;
            }

            if (fieldName === "email" && !isValidEmail(value)) {
                errorText.textContent = "Enter a valid email address.";
                input.classList.add("input-error");
                isValid = false;
            }
        });

        if (!isValid) {
            event.preventDefault();
        }
    });
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}
