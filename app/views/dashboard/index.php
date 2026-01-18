<?php
// View file for main dashboard (home page after login)
// This page shows a welcome message and 3 cards linking to main features

// Set page title
$pageTitle = 'Dashboard';

// Start output buffering (saves HTML to variable)
ob_start();
?>

<!-- Welcome message row -->
<div class="row mb-4">
    <div class="col-md-12">
        <!-- Display user's name from session -->
        <!-- htmlspecialchars() prevents security issues if name contains HTML -->
        <!-- ?? 'User' means: if user_name doesn't exist, show 'User' instead -->
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></h1>
        <p class="text-muted">Plan your meals and generate your shopping list for the week</p>
    </div>
</div>

<!-- Three feature cards in a row -->
<div class="row">
    <!-- Week Planner Card -->
    <!-- col-md-4 means: on medium+ screens, take up 4/12 (1/3) of width -->
    <!-- mb-4 adds margin at bottom for spacing -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">📅 Week Planner</h5>
                <p class="card-text">Plan your meals for the week</p>
                <!-- Button links to week planner page -->
                <a href="/weekplanner/index" class="btn btn-primary">Go to Planner</a>
            </div>
        </div>
    </div>

    <!-- Recipes Card -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">🍽️ Recipes</h5>
                <p class="card-text">Browse and manage recipes</p>
                <!-- Button links to recipes page -->
                <a href="/recipe/index" class="btn btn-primary">View Recipes</a>
            </div>
        </div>
    </div>

    <!-- Shopping List Card -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">🛒 Shopping List</h5>
                <p class="card-text">Auto-generated shopping list</p>
                <!-- Button links to shopping list page -->
                <a href="/shoppinglist/index" class="btn btn-primary">View List</a>
            </div>
        </div>
    </div>
</div>

<?php
// Save generated HTML into $content variable
$content = ob_get_clean();

// Include base layout (wraps this content in navbar and structure)
include __DIR__ . '/../layouts/base.php';
?>
