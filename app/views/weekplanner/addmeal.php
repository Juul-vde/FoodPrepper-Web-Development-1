<?php
$pageTitle = 'Add Meal to Week Plan';
ob_start();
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h1>📅 Add Meal to Week Plan</h1>
        <p class="text-muted">Select a recipe and assign it to a specific day and meal type</p>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <!-- Filters Section -->
        <div class="card">
            <div class="card-header">
                <h5>Filter Recipes</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="/weekplanner/addmeal" id="filterForm">
                    <div class="mb-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" placeholder="Recipe name..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">All Categories</option>
                            <?php if (isset($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat['id']); ?>" 
                                        <?php echo (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="tag" class="form-label">Tag/Diet Type</label>
                        <select class="form-select" id="tag" name="tag">
                            <option value="">All Tags</option>
                            <?php if (isset($tags)): ?>
                                <?php foreach ($tags as $tag): ?>
                                    <option value="<?php echo htmlspecialchars($tag['id']); ?>" 
                                        <?php echo (isset($_GET['tag']) && $_GET['tag'] == $tag['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($tag['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                    <a href="/weekplanner/addmeal" class="btn btn-secondary w-100 mt-2">Clear Filters</a>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- Recipe Selection -->
        <div class="card">
            <div class="card-header">
                <h5>Available Recipes (<?php echo isset($recipes) ? count($recipes) : 0; ?>)</h5>
            </div>
            <div class="card-body">
                <?php if (isset($recipes) && count($recipes) > 0): ?>
                    <div class="row">
                        <?php foreach ($recipes as $recipe): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card recipe-card" data-recipe-id="<?php echo $recipe['id']; ?>">
                                    <div class="card-body">
                                        <h6 class="card-title"><?php echo htmlspecialchars($recipe['title']); ?></h6>
                                        <p class="card-text small text-muted"><?php echo htmlspecialchars(substr($recipe['description'] ?? '', 0, 80)); ?>...</p>
                                        <div class="mb-2">
                                            <small class="badge bg-info"><?php echo htmlspecialchars($recipe['category_name'] ?? 'Uncategorized'); ?></small>
                                        </div>
                                        <div class="mb-2">
                                            <small>⏱️ Prep: <?php echo htmlspecialchars($recipe['prep_time'] ?? 0); ?>m | Cook: <?php echo htmlspecialchars($recipe['cook_time'] ?? 0); ?>m</small>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-success select-recipe" data-recipe-id="<?php echo $recipe['id']; ?>" data-recipe-title="<?php echo htmlspecialchars($recipe['title']); ?>">
                                            Select
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <p>No recipes match your filters. Try adjusting them!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal for meal assignment -->
<div class="modal fade" id="mealAssignmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Meal Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/weekplanner/addmeal">
                <div class="modal-body">
                    <input type="hidden" name="recipe_id" id="modalRecipeId" value="">
                    
                    <div class="mb-3">
                        <label for="recipeName" class="form-label">Recipe</label>
                        <input type="text" class="form-control" id="recipeName" disabled>
                    </div>

                    <div class="mb-3">
                        <label for="dayOfWeek" class="form-label">Day of Week <span class="text-danger">*</span></label>
                        <select class="form-select" id="dayOfWeek" name="day_of_week" required>
                            <option value="">Select a day...</option>
                            <option value="1">Monday</option>
                            <option value="2">Tuesday</option>
                            <option value="3">Wednesday</option>
                            <option value="4">Thursday</option>
                            <option value="5">Friday</option>
                            <option value="6">Saturday</option>
                            <option value="7">Sunday</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="mealType" class="form-label">Meal Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="mealType" name="meal_type" required>
                            <option value="">Select meal type...</option>
                            <option value="breakfast">🥐 Breakfast</option>
                            <option value="lunch">🍽️ Lunch</option>
                            <option value="dinner">🍴 Dinner</option>
                            <option value="snack">🥜 Snack</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="servings" class="form-label">Servings <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="servings" name="servings" min="1" max="20" value="1" required>
                        <small class="text-muted">Number of servings for this meal</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add to Weekly Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = new bootstrap.Modal(document.getElementById('mealAssignmentModal'));
    
    // Handle recipe selection
    document.querySelectorAll('.select-recipe').forEach(btn => {
        btn.addEventListener('click', function() {
            const recipeId = this.dataset.recipeId;
            const recipeTitle = this.dataset.recipeTitle;
            
            document.getElementById('modalRecipeId').value = recipeId;
            document.getElementById('recipeName').value = recipeTitle;
            
            // Reset form fields
            document.getElementById('dayOfWeek').value = '';
            document.getElementById('mealType').value = '';
            document.getElementById('servings').value = '1';
            
            modal.show();
        });
    });

    // Handle auto-filter when category/tag changes
    document.getElementById('category').addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });

    document.getElementById('tag').addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });

    // Allow search with Enter key
    document.getElementById('search').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            document.getElementById('filterForm').submit();
        }
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/base.php';
?>
