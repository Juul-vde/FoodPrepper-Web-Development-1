<?php
// View file for recipes listing page
// Shows all recipes in a grid with filters for searching and categories
// Uses JavaScript for live filtering without page reload

// Set page title
$pageTitle = 'Recipes';

// Start output buffering
ob_start();
?>

<!-- Page header with title and admin button -->
<div class="row mb-4">
    <div class="col-md-12 d-flex justify-content-between align-items-center">
        <div>
            <h1>🍽️ Recipes</h1>
            <p class="text-muted">Browse and manage your recipes</p>
        </div>
        <!-- Only show "Add Recipe" button if user is admin -->
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
            <a href="/recipe/create" class="btn btn-success">+ Add New Recipe</a>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <!-- Left sidebar: Filters (takes 1/4 of width) -->
    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">🔍 Filters</h5>
            </div>
            <div class="card-body">
                <!-- Filter form (uses JavaScript, doesn't submit normally) -->
                <form id="filterForm">
                    <!-- Search input -->
                    <div class="mb-3">
                        <label for="searchInput" class="form-label">Search</label>
                        <!-- value keeps search text if page was filtered -->
                        <input type="text" class="form-control" id="searchInput" name="q" placeholder="Type to search..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
                    </div>

                    <!-- Category dropdown filter -->
                    <div class="mb-3">
                        <label for="categoryFilter" class="form-label">Category</label>
                        <select class="form-select" id="categoryFilter" name="category">
                            <option value="">All Categories</option>
                            <!-- Loop through categories from database -->
                            <?php if (isset($categories)): ?>
                                <?php foreach ($categories as $category): ?>
                                    <!-- data-color stores color for later use -->
                                    <!-- selected shows this option as chosen if it matches URL -->
                                    <option value="<?php echo $category['id']; ?>" 
                                            data-color="<?php echo htmlspecialchars($category['color'] ?? '#6c757d'); ?>"
                                            <?php echo (isset($_GET['category']) && $_GET['category'] == $category['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['icon'] ?? ''); ?> <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Clear filters button -->
                    <button type="button" class="btn btn-secondary w-100" id="clearFilters">Clear Filters</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right side: Recipes grid (takes 3/4 of width) -->
    <div class="col-md-9">
        <!-- Container for recipes (updated by JavaScript when filtering) -->
        <div id="recipesContainer">
            <?php if (isset($recipes) && count($recipes) > 0): ?>
                <!-- Grid layout for recipe cards -->
                <div class="row">
                    <!-- Loop through each recipe -->
                    <?php foreach ($recipes as $recipe): ?>
                        <!-- Each recipe card takes 1/3 of width (3 per row) -->
                        <div class="col-md-4 mb-4 recipe-card" data-recipe-id="<?php echo $recipe['id']; ?>">
                            <!-- Card with auto height -->
                            <div class="card h-100">
                                <div class="card-body">
                                    <!-- Recipe title -->
                                    <h5 class="card-title"><?php echo htmlspecialchars($recipe['title']); ?></h5>
                                    
                                    <!-- Category badges -->
                                    <div class="mb-2">
                                        <?php if (isset($recipe['categories']) && is_array($recipe['categories'])): ?>
                                            <?php foreach ($recipe['categories'] as $category): ?>
                                                <!-- Badge with custom background color -->
                                                <span class="badge me-1 mb-1" style="background-color: <?php echo htmlspecialchars($category['color']); ?>;">
                                                    <?php echo htmlspecialchars($category['icon']); ?> <?php echo htmlspecialchars($category['name']); ?>
                                                </span>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Short description (first 100 characters) -->
                                    <p class="card-text"><?php echo htmlspecialchars(substr($recipe['description'] ?? '', 0, 100)); ?>...</p>
                                    
                                    <!-- Time information -->
                                    <small class="text-muted">
                                        ⏱️ Prep: <?php echo htmlspecialchars($recipe['prep_time'] ?? '0'); ?> min | 
                                        Cook: <?php echo htmlspecialchars($recipe['cook_time'] ?? '0'); ?> min
                                    </small>
                                </div>
                                <!-- Card footer with action buttons -->
                                <div class="card-footer bg-white">
                                    <!-- View button (everyone can see) -->
                                    <a href="/recipe/view?id=<?php echo $recipe['id']; ?>" class="btn btn-sm btn-primary">View</a>
                                    
                                    <!-- Edit and Delete buttons (only for admins) -->
                                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                                        <a href="/recipe/edit?id=<?php echo $recipe['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <!-- Delete with confirmation popup -->
                                        <a href="/recipe/delete?id=<?php echo $recipe['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this recipe?');">Delete</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- No recipes found message -->
                <div class="alert alert-info">
                    <p>No recipes found. <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?><a href="/recipe/create">Create your first recipe!</a><?php endif; ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// JavaScript for live search and filtering
// Get references to form elements
const searchInput = document.getElementById('searchInput');
const categoryFilter = document.getElementById('categoryFilter');
const clearFiltersBtn = document.getElementById('clearFilters');
const recipesContainer = document.getElementById('recipesContainer');

let searchTimeout; // Timer for debouncing (waiting for user to stop typing)

// Debounced search function
// Waits 300ms after user stops typing before searching
function performSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        updateRecipes();
    }, 300); // Wait 300ms after user stops typing
}

// Update recipes based on current filter values
function updateRecipes() {
    const searchValue = searchInput.value;
    const categoryValue = categoryFilter.value;

    // Build URL query string from filter values
    const params = new URLSearchParams();
    if (searchValue) params.append('q', searchValue);
    if (categoryValue) params.append('category', categoryValue);

    const queryString = params.toString();
    const url = '/recipe/index' + (queryString ? '?' + queryString : '');

    // Fetch filtered recipes using AJAX (doesn't reload whole page)
    fetch(url)
        .then(response => response.text())
        .then(html => {
            // Parse the HTML response
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContainer = doc.getElementById('recipesContainer');
            
            if (newContainer) {
                // Replace old recipes with filtered ones
                recipesContainer.innerHTML = newContainer.innerHTML;
            }
        })
        .catch(error => {
            console.error('Error fetching recipes:', error);
        });
}

// Event listeners
// Trigger search when user types in search box
searchInput.addEventListener('input', performSearch);

// Trigger update when user selects a category
categoryFilter.addEventListener('change', updateRecipes);

// Clear filters button - resets both inputs and shows all recipes
clearFiltersBtn.addEventListener('click', () => {
    searchInput.value = '';
    categoryFilter.value = '';
    updateRecipes();
});
</script>

<?php
// Save HTML to $content variable
$content = ob_get_clean();

// Include base layout
include __DIR__ . '/../layouts/base.php';
?>
