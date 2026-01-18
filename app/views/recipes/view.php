<?php
// View file for displaying a single recipe
// Shows recipe details, ingredients, instructions, and tags
// Allows adding recipe to week planner

// Set page title to recipe name
$pageTitle = isset($recipe) ? $recipe['title'] : 'Recipe';

// Start output buffering
ob_start();
?>

<!-- Back button to return to recipes list -->
<div class="mb-4">
    <a href="/recipe/index" class="btn btn-secondary">← Back to Recipes</a>
</div>

<?php if (isset($recipe)): ?>
    <div class="row">
        <!-- Main content (left side - takes 2/3 of width) -->
        <div class="col-md-8">
            <!-- Recipe Header Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <!-- Recipe title -->
                    <h1 class="card-title"><?php echo htmlspecialchars($recipe['title']); ?></h1>
                    
                    <!-- Category badges -->
                    <div class="mb-3">
                        <?php if (isset($recipe['categories']) && is_array($recipe['categories']) && count($recipe['categories']) > 0): ?>
                            <?php foreach ($recipe['categories'] as $category): ?>
                                <!-- Badge with custom color from database -->
                                <span class="badge me-2 mb-2" style="background-color: <?php echo htmlspecialchars($category['color']); ?>; font-size: 0.95rem;">
                                    <?php echo htmlspecialchars($category['icon']); ?> <?php echo htmlspecialchars($category['name']); ?>
                                </span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Recipe description -->
                    <p class="lead text-muted"><?php echo htmlspecialchars($recipe['description'] ?? ''); ?></p>

                    <!-- Quick info boxes (4 boxes in a row) -->
                    <div class="row mb-4">
                        <!-- Prep time box -->
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="h5">⏱️ Prep Time</div>
                                <div class="fs-6"><?php echo htmlspecialchars($recipe['prep_time'] ?? '0'); ?> min</div>
                            </div>
                        </div>
                        <!-- Cook time box -->
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="h5">🔥 Cook Time</div>
                                <div class="fs-6"><?php echo htmlspecialchars($recipe['cook_time'] ?? '0'); ?> min</div>
                            </div>
                        </div>
                        <!-- Servings box -->
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="h5">🍽️ Servings</div>
                                <div class="fs-6"><?php echo htmlspecialchars($recipe['servings'] ?? '1'); ?></div>
                            </div>
                        </div>
                        <!-- Difficulty box -->
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="h5">📊 Difficulty</div>
                                <div class="fs-6"><?php echo htmlspecialchars($recipe['difficulty'] ?? 'Unknown'); ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Actions (only show if user is admin) -->
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <div class="mb-4">
                            <!-- Edit button -->
                            <a href="/recipe/edit?id=<?php echo $recipe['id']; ?>" class="btn btn-warning btn-sm">✏️ Edit Recipe</a>
                            <!-- Delete button with confirmation -->
                            <a href="/recipe/delete?id=<?php echo $recipe['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this recipe?');">🗑️ Delete Recipe</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Instructions Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">📝 Instructions</h5>
                </div>
                <div class="card-body">
                    <div class="recipe-instructions">
                        <!-- Numbered list of instructions -->
                        <ol class="ps-3">
                            <?php 
                            // Get instructions text
                            $instructions = $recipe['instructions'] ?? '';
                            
                            // Split instructions into sentences
                            // preg_split splits by period, exclamation, or question mark
                            $sentences = preg_split('/(?<=[.!?])\s+/', trim($instructions), -1, PREG_SPLIT_NO_EMPTY);
                            
                            // Loop through each sentence
                            foreach ($sentences as $sentence):
                                $sentence = trim($sentence);
                                if (!empty($sentence)): ?>
                                    <!-- Each sentence is a numbered step -->
                                    <li class="mb-2">
                                        <span class="text-dark"><?php echo htmlspecialchars($sentence); ?></span>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Tags Section (if recipe has tags) -->
            <?php if (isset($recipe['tags']) && is_array($recipe['tags']) && count($recipe['tags']) > 0): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">🏷️ Tags</h5>
                    </div>
                    <div class="card-body">
                        <div>
                            <?php foreach ($recipe['tags'] as $tag): ?>
                                <!-- Each tag as a gray badge -->
                                <span class="badge bg-secondary me-2 mb-2"><?php echo htmlspecialchars(is_array($tag) ? $tag['name'] : $tag); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar (right side - takes 1/3 of width) -->
        <div class="col-md-4">
            <!-- Ingredients Card (sticky means it stays visible when scrolling) -->
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">🥘 Ingredients</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($recipe['ingredients']) && count($recipe['ingredients']) > 0): ?>
                        <!-- List of ingredients -->
                        <div class="list-group list-group-flush">
                            <?php foreach ($recipe['ingredients'] as $ingredient): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-bottom">
                                    <span>
                                        <!-- Ingredient name -->
                                        <strong><?php echo htmlspecialchars($ingredient['ingredient_name'] ?? $ingredient['name'] ?? ''); ?></strong>
                                        <!-- Quantity and unit (smaller text) -->
                                        <div class="small text-muted">
                                            <?php echo htmlspecialchars($ingredient['quantity'] ?? 0); ?> <?php echo htmlspecialchars($ingredient['unit'] ?? ''); ?>
                                        </div>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <!-- No ingredients message -->
                        <p class="text-muted">No ingredients listed.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Add to Weekplanner Card (only show if user is logged in) -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="card mt-3">
                    <?php if (isset($recipeInWeekplan) && count($recipeInWeekplan) > 0): ?>
                        <!-- Recipe is already in week planner -->
                        <div class="card-body">
                            <div class="alert alert-info mb-3">
                                <strong>✓ In your weekplanner!</strong> 
                                <br><small>Appears on <?php echo count($recipeInWeekplan); ?> day(s) this week.</small>
                            </div>
                            <!-- Button to view week planner -->
                            <a href="/weekplanner/index" class="btn btn-primary w-100">📅 View in Weekplanner</a>
                        </div>
                    <?php else: ?>
                        <!-- Recipe not in week planner yet -->
                        <div class="card-body">
                            <!-- Button opens modal popup -->
                            <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#addToWeekplanModal">
                                📅 Add to Weekplanner
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php else: ?>
    <!-- Recipe not found error -->
    <div class="alert alert-warning">
        <p>Recipe not found.</p>
    </div>
<?php endif; ?>

<!-- Modal popup for adding recipe to week planner -->
<!-- Only show if user is logged in and recipe not already in plan -->
<?php if (isset($_SESSION['user_id']) && (!isset($recipeInWeekplan) || count($recipeInWeekplan) == 0)): ?>
<div class="modal fade" id="addToWeekplanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">📅 Add to Weekplanner</h5>
                <!-- Close button (X) -->
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <!-- Form submits to /weekplanner/addmeal -->
            <form method="POST" action="/weekplanner/addmeal">
                <div class="modal-body">
                    <!-- Day selection dropdown -->
                    <div class="mb-3">
                        <label for="weekplanDay" class="form-label">Select Day</label>
                        <select class="form-select" id="weekplanDay" name="day_of_week" required>
                            <option value="">-- Choose a day --</option>
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
                        <label for="weekplanMealType" class="form-label">Meal Type</label>
                        <select class="form-select" id="weekplanMealType" name="meal_type" required>
                            <option value="breakfast">🌅 Breakfast</option>
                            <option value="lunch" selected>🍽️ Lunch</option>
                            <option value="dinner">🌙 Dinner</option>
                            <option value="snack">🍎 Snack</option>
                        </select>
                    </div>

                    <!-- Number of servings -->
                    <div class="mb-3">
                        <label for="weekplanServings" class="form-label">Servings</label>
                        <input type="number" class="form-control" id="weekplanServings" name="servings" value="1" min="1" max="20" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- Hidden field with recipe ID -->
                    <input type="hidden" name="recipe_id" value="<?php echo isset($recipe) ? $recipe['id'] : ''; ?>">
                    <!-- Cancel button -->
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <!-- Submit button -->
                    <button type="submit" class="btn btn-primary">Add to Weekplanner</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Add to Weekplanner Modal -->
<?php if (isset($_SESSION['user_id']) && (!isset($recipeInWeekplan) || count($recipeInWeekplan) == 0)): ?>
<div class="modal fade" id="addToWeekplanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">📅 Add to Weekplanner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/weekplanner/addmeal">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="weekplanDay" class="form-label">Select Day</label>
                        <select class="form-select" id="weekplanDay" name="day_of_week" required>
                            <option value="">-- Choose a day --</option>
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
                        <label for="weekplanMealType" class="form-label">Meal Type</label>
                        <select class="form-select" id="weekplanMealType" name="meal_type" required>
                            <option value="breakfast">🌅 Breakfast</option>
                            <option value="lunch" selected>🍽️ Lunch</option>
                            <option value="dinner">🌙 Dinner</option>
                            <option value="snack">🍎 Snack</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="weekplanServings" class="form-label">Servings</label>
                        <input type="number" class="form-control" id="weekplanServings" name="servings" value="1" min="1" max="20" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="recipe_id" value="<?php echo isset($recipe) ? $recipe['id'] : ''; ?>">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add to Weekplanner</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/base.php';
?>
