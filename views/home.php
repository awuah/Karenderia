<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-utensils"></i> <?php echo SITE_NAME; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/restaurants">Restaurants</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/cart">
                            <i class="fas fa-shopping-cart"></i> Cart
                        </a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/profile">Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/logout">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/login">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/register">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-section bg-light py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold text-primary">Order Food Online</h1>
                    <p class="lead">Discover the best restaurants in your area and order your favorite meals online.</p>
                    <div class="search-box mt-4">
                        <form class="d-flex">
                            <input class="form-control me-2" type="search" placeholder="Search restaurants or food...">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="https://images.pexels.com/photos/1640777/pexels-photo-1640777.jpeg" class="img-fluid rounded" alt="Food delivery">
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Restaurants -->
    <?php if (!empty($featured_restaurants)): ?>
    <section class="featured-restaurants py-5">
        <div class="container">
            <h2 class="text-center mb-5">Featured Restaurants</h2>
            <div class="row">
                <?php foreach ($featured_restaurants as $restaurant): ?>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card restaurant-card h-100">
                        <img src="<?php echo $restaurant['image'] ?: 'https://images.pexels.com/photos/260922/pexels-photo-260922.jpeg'; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($restaurant['name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($restaurant['name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($restaurant['description']); ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-warning">
                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                        <i class="fas fa-star<?php echo $i < $restaurant['rating'] ? '' : '-o'; ?>"></i>
                                    <?php endfor; ?>
                                </span>
                                <span class="text-muted"><?php echo $restaurant['delivery_time']; ?> min</span>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="/restaurant/<?php echo $restaurant['id']; ?>" class="btn btn-primary w-100">View Menu</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- All Restaurants -->
    <section class="all-restaurants py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">All Restaurants</h2>
            
            <!-- Category Filter -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="category-filter text-center">
                        <button class="btn btn-outline-primary me-2 mb-2 active" data-category="all">All</button>
                        <?php foreach ($categories as $category): ?>
                            <button class="btn btn-outline-primary me-2 mb-2" data-category="<?php echo htmlspecialchars($category['category']); ?>">
                                <?php echo htmlspecialchars($category['category']); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="row" id="restaurants-grid">
                <?php foreach ($restaurants as $restaurant): ?>
                <div class="col-lg-4 col-md-6 mb-4 restaurant-item" data-category="<?php echo htmlspecialchars($restaurant['category']); ?>">
                    <div class="card restaurant-card h-100">
                        <img src="<?php echo $restaurant['image'] ?: 'https://images.pexels.com/photos/260922/pexels-photo-260922.jpeg'; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($restaurant['name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($restaurant['name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($restaurant['description']); ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-warning">
                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                        <i class="fas fa-star<?php echo $i < $restaurant['rating'] ? '' : '-o'; ?>"></i>
                                    <?php endfor; ?>
                                </span>
                                <span class="text-muted"><?php echo $restaurant['delivery_time']; ?> min</span>
                            </div>
                            <div class="mt-2">
                                <span class="badge bg-secondary"><?php echo htmlspecialchars($restaurant['category']); ?></span>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="/restaurant/<?php echo $restaurant['id']; ?>" class="btn btn-primary w-100">View Menu</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><?php echo SITE_NAME; ?></h5>
                    <p>Order food online from the best restaurants in your area.</p>
                </div>
                <div class="col-md-6">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="/about" class="text-white-50">About Us</a></li>
                        <li><a href="/contact" class="text-white-50">Contact</a></li>
                        <li><a href="/terms" class="text-white-50">Terms of Service</a></li>
                        <li><a href="/privacy" class="text-white-50">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p>&copy; 2024 <?php echo SITE_NAME; ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>