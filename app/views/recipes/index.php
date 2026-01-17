<?php
$pageTitle = 'Recipes';
ob_start();
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h1>🍽️ Recipes</h1>
        <p class="text-muted">Browse and manage your recipes</p>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
            <a href="/recipe/create" class="btn btn-success">+ Add New Recipe</a>
        <?php endif; ?>
        <form method="GET" action="/recipe/search" class="d-inline ms-2">
            <input type="text" name="q" class="form-control d-inline-block w-25" placeholder="Search recipes..." required>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>
</div>

<?php if (isset($recipes) && count($recipes) > 0): ?>
    <div class="row">
        <?php foreach ($recipes as $recipe): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <?php if (isset($recipe['image_url']) && $recipe['image_url']): ?>
                        <img src="<?php echo htmlspecialchars($recipe['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($recipe['title']); ?>">
                    <?php else: ?>
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <span class="text-muted">No Image</span>
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($recipe['title']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars(substr($recipe['description'] ?? '', 0, 100)); ?>...</p>
                        <small class="text-muted">
                            Prep: <?php echo htmlspecialchars($recipe['prep_time'] ?? '0'); ?> min | 
                            Cook: <?php echo htmlspecialchars($recipe['cook_time'] ?? '0'); ?> min
                        </small>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="/recipe/view?id=<?php echo $recipe['id']; ?>" class="btn btn-sm btn-primary">View</a>
                        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                            <a href="/recipe/edit?id=<?php echo $recipe['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="/recipe/delete?id=<?php echo $recipe['id']; ?>" class="btn btn-sm btn-danger">Delete</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        <p>No recipes found. <a href="/recipe/create">Create your first recipe!</a></p>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/base.php';
?>
