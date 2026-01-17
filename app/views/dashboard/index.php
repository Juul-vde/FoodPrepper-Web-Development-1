<?php
$pageTitle = 'Dashboard';
ob_start();
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></h1>
        <p class="text-muted">Plan your meals and generate your shopping list for the week</p>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">📅 Week Planner</h5>
                <p class="card-text">Plan your meals for the week</p>
                <a href="/weekplanner/index" class="btn btn-primary">Go to Planner</a>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">🍽️ Recipes</h5>
                <p class="card-text">Browse and manage recipes</p>
                <a href="/recipe/index" class="btn btn-primary">View Recipes</a>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">🛒 Shopping List</h5>
                <p class="card-text">Auto-generated shopping list</p>
                <a href="/shoppinglist/index" class="btn btn-primary">View List</a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/base.php';
?>
