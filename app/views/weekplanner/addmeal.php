<?php
// View file for adding a meal to week planner
// This page shows recipes with filters and lets user select one to add to their week

// Set page title
$pageTitle = 'Add Meal to Week Plan';

// Start output buffering
ob_start();
?>

<!-- Page header -->
<div class="row mb-4">
    <div class="col-md-12">
        <h1>📅 Add Meal to Week Plan</h1>
        <p class="text-muted">Select a recipe and assign it to a specific day and meal type</p>
    </div>
</div>

<div class="row">
    <!-- Left column: Filters (takes 1/3 of width) -->
    <div class="col-md-4">
        <!-- Filters Card -->
        <div class="card">
            <div class="card-header">
                <h5>🔍 Filter Recipes</h5>
            </div>
            <div class="card-body">
                <!-- Filter form (doesn't submit - uses JavaScript) -->
                <form id="filterForm">
                    <!-- Search input -->
                    <div class="mb-3">
                        <label for="search" class="form-label">Search</label>
                        <!-- value keeps search text if page was filtered -->
                        <input type="text" class="form-control" id="search" name="search" placeholder="Recipe name..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                    </div>

                    <!-- Category dropdown filter -->
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">All Categories</option>
                            <!-- Loop through categories from database -->
                            <?php if (isset($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <!-- selected means this option shows as chosen -->
                                    <option value="<?php echo htmlspecialchars($cat['id']); ?>" 
                                        <?php echo (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['icon'] ?? ''); ?> <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Button to clear all filters -->
                    <button type="button" class="btn btn-secondary w-100" id="clearFilters">Clear Filters</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right column: Recipe list (takes 2/3 of width) -->
    <div class="col-md-8">
        <!-- Recipes Card -->
        <div class="card">
            <div class="card-header">
                <!-- Show count of recipes -->
                <h5>Available Recipes (<?php echo isset($recipes) ? count($recipes) : 0; ?>)</h5>
            </div>
            <div class="card-body">
                <!-- Container for recipes (updated by JavaScript when filtering) -->
                <div id="recipesContainer">
                    <?php if (isset($recipes) && count($recipes) > 0): ?>
                        <!-- Grid of recipe cards -->
                        <div class="row">
                            <!-- Loop through each recipe -->
                            <?php foreach ($recipes as $recipe): ?>
                                <!-- Each recipe takes half the width (2 per row) -->
                                <div class="col-md-6 mb-3">
                                    <!-- Recipe card with recipe ID stored -->
                                    <div class="card recipe-card" data-recipe-id="<?php echo $recipe['id']; ?>">
                                        <div class="card-body">
                                            <!-- Recipe title -->
                                            <h6 class="card-title"><?php echo htmlspecialchars($recipe['title']); ?></h6>
                                            <!-- Short description (first 80 characters) -->
                                            <p class="card-text small text-muted"><?php echo htmlspecialchars(substr($recipe['description'] ?? '', 0, 80)); ?>...</p>
                                            
                                            <!-- Category badges -->
                                            <div class="mb-2">
                                                <?php if (isset($recipe['categories']) && is_array($recipe['categories'])): ?>
                                                    <?php foreach ($recipe['categories'] as $category): ?>
                                                        <!-- Badge with category color -->
                                                        <span class="badge me-1 mb-1" style="background-color: <?php echo htmlspecialchars($category['color']); ?>;">
                                                            <?php echo htmlspecialchars($category['icon']); ?> <?php echo htmlspecialchars($category['name']); ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <!-- Prep and cook times -->
                                            <div class="mb-2">
                                                <small>⏱️ Prep: <?php echo htmlspecialchars($recipe['prep_time'] ?? 0); ?>m | Cook: <?php echo htmlspecialchars($recipe['cook_time'] ?? 0); ?>m</small>
                                            </div>
                                            
                                            <!-- Action buttons -->
                                            <div class="d-flex gap-2">
                                                <!-- Select button - opens modal -->
                                                <!-- data attributes store recipe info for JavaScript -->
                                                <button type="button" class="btn btn-sm btn-success select-recipe" data-recipe-id="<?php echo $recipe['id']; ?>" data-recipe-title="<?php echo htmlspecialchars($recipe['title']); ?>">
                                                    ✓ Select
                                                </button>
                                                <!-- View button - opens recipe in new tab -->
                                                <a href="/recipe/view?id=<?php echo $recipe['id']; ?>" class="btn btn-sm btn-info" target="_blank">
                                                    👁️ View
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <!-- No recipes found message -->
                        <div class="alert alert-info">
                            <p>No recipes match your filters. Try adjusting them!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal popup for assigning meal to day/time -->
<!-- fade makes it animate in/out -->
<div class="modal fade" id="mealAssignmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">📅 Add to Weekplanner</h5>
                <!-- Close button (X) -->
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <!-- Form inside modal -->
            <form method="POST" action="/weekplanner/addmeal">
                <div class="modal-body">
                    <!-- CSRF Protection Token -->
                    <!-- Prevents unauthorized meal additions from fake sites -->
                    <?php 
                    use App\Services\CsrfService; 
                    echo CsrfService::getTokenField(); 
                    ?>
                    
                    <!-- Hidden field with selected recipe ID (filled by JavaScript) -->
                    <input type="hidden" name="recipe_id" id="modalRecipeId" value="">
                    
                    <!-- Day selection dropdown -->
                    <div class="mb-3">
                        <label for="dayOfWeek" class="form-label">Select Day</label>
                        <select class="form-select" id="dayOfWeek" name="day_of_week" required>
                            <option value="">-- Choose a day --</option>
                            <!-- Day options (1-7) -->
                            <option value="1">Monday</option>
                            <option value="2">Tuesday</option>
                            <option value="3">Wednesday</option>
                            <option value="4">Thursday</option>
                            <option value="5">Friday</option>
                            <option value="6">Saturday</option>
                            <option value="7">Sunday</option>
                        </select>
                    </div>

                    <!-- Meal type dropdown -->
                    <div class="mb-3">
                        <label for="mealType" class="form-label">Meal Type</label>
                        <select class="form-select" id="mealType" name="meal_type" required>
                            <option value="breakfast">🌅 Breakfast</option>
                            <!-- lunch is selected by default -->
                            <option value="lunch" selected>🍽️ Lunch</option>
                            <option value="dinner">🌙 Dinner</option>
                            <option value="snack">🍎 Snack</option>
                        </select>
                    </div>

                    <!-- Number of servings -->
                    <div class="mb-3">
                        <label for="servings" class="form-label">Servings</label>
                        <!-- type="number" adds +/- buttons -->
                        <input type="number" class="form-control" id="servings" name="servings" value="1" min="1" max="20" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- Cancel button -->
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <!-- Submit button -->
                    <button type="submit" class="btn btn-primary">Add to Weekplanner</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// JavaScript for filtering recipes and handling modal
document.addEventListener('DOMContentLoaded', function() {
    // Get modal object from Bootstrap
    const modal = new bootstrap.Modal(document.getElementById('mealAssignmentModal'));
    
    // Get form elements
    const searchInput = document.getElementById('search');
    const categorySelect = document.getElementById('category');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const recipesContainer = document.getElementById('recipesContainer');
    
    let searchTimeout; // Timer for debouncing search
    
    // Debounced search - waits for user to stop typing before searching
    function performSearch() {
        clearTimeout(searchTimeout);
        // Wait 300ms after user stops typing
        searchTimeout = setTimeout(() => {
            updateRecipes();
        }, 300);
    }

    // Update recipes based on current filter values
    function updateRecipes() {
        const searchValue = searchInput.value;
        const categoryValue = categorySelect.value;

        // Build URL query string from filters
        const params = new URLSearchParams();
        if (searchValue) params.append('search', searchValue);
        if (categoryValue) params.append('category', categoryValue);

        const queryString = params.toString();
        const url = '/weekplanner/addmeal' + (queryString ? '?' + queryString : '');

        // Fetch filtered recipes using AJAX
        fetch(url)
            .then(response => response.text())
            .then(html => {
                // Parse HTML response
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContainer = doc.getElementById('recipesContainer');
                
                if (newContainer) {
                    // Replace old recipes with filtered ones
                    recipesContainer.innerHTML = newContainer.innerHTML();
                    // Reattach click handlers to new select buttons
                    attachSelectButtonListeners();
                }
            })
            .catch(error => {
                console.error('Error fetching recipes:', error);
            });
    }

    // Attach click handlers to "Select" buttons
    function attachSelectButtonListeners() {
        document.querySelectorAll('.select-recipe').forEach(btn => {
            btn.addEventListener('click', function() {
                // Get recipe ID from button's data attribute
                const recipeId = this.dataset.recipeId;
                
                // Fill hidden field in modal with recipe ID
                document.getElementById('modalRecipeId').value = recipeId;
                
                // Reset form fields to defaults
                document.getElementById('dayOfWeek').value = '';
                document.getElementById('mealType').value = 'lunch';
                document.getElementById('servings').value = '1';
                
                // Show modal popup
                modal.show();
            });
        });
    }

    // Event listeners for filters
    searchInput.addEventListener('input', performSearch); // Trigger search on typing
    categorySelect.addEventListener('change', updateRecipes); // Trigger on category change

    // Clear filters button
    clearFiltersBtn.addEventListener('click', () => {
        searchInput.value = '';
        categorySelect.value = '';
        updateRecipes();
    });

    // Initial attachment of select button listeners
    attachSelectButtonListeners();
});
</script>

<?php
// Save HTML to $content variable
$content = ob_get_clean();

// Include base layout
include __DIR__ . '/../layouts/base.php';
?>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = new bootstrap.Modal(document.getElementById('mealAssignmentModal'));
    const searchInput = document.getElementById('search');
    const categorySelect = document.getElementById('category');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const recipesContainer = document.getElementById('recipesContainer');
    let searchTimeout;
    
    // Debounced search function
    function performSearch() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            updateRecipes();
        }, 300); // Wait 300ms after user stops typing
    }

    // Update recipes based on filters
    function updateRecipes() {
        const searchValue = searchInput.value;
        const categoryValue = categorySelect.value;

        // Build query string
        const params = new URLSearchParams();
        if (searchValue) params.append('search', searchValue);
        if (categoryValue) params.append('category', categoryValue);

        const queryString = params.toString();
        const url = '/weekplanner/addmeal' + (queryString ? '?' + queryString : '');

        // Fetch filtered recipes
        fetch(url)
            .then(response => response.text())
            .then(html => {
                // Parse the HTML response
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContainer = doc.getElementById('recipesContainer');
                
                if (newContainer) {
                    recipesContainer.innerHTML = newContainer.innerHTML;
                    // Reattach event listeners to new select buttons
                    attachSelectButtonListeners();
                }
            })
            .catch(error => {
                console.error('Error fetching recipes:', error);
            });
    }

    // Attach event listeners to select buttons
    function attachSelectButtonListeners() {
        document.querySelectorAll('.select-recipe').forEach(btn => {
            btn.addEventListener('click', function() {
                const recipeId = this.dataset.recipeId;
                
                document.getElementById('modalRecipeId').value = recipeId;
                
                // Reset form fields
                document.getElementById('dayOfWeek').value = '';
                document.getElementById('mealType').value = 'lunch';
                document.getElementById('servings').value = '1';
                
                modal.show();
            });
        });
    }

    // Event listeners
    searchInput.addEventListener('input', performSearch);
    categorySelect.addEventListener('change', updateRecipes);

    clearFiltersBtn.addEventListener('click', () => {
        searchInput.value = '';
        categorySelect.value = '';
        updateRecipes();
    });

    // Initial attachment of select button listeners
    attachSelectButtonListeners();
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/base.php';
?>
