<?php
// View file for editing a meal in the week planner
// This page allows changing the day, meal type, or servings for a planned meal

use App\Services\CsrfService;

// Set page title
$pageTitle = 'Edit Meal';

// Start output buffering
ob_start();
?>

<!-- Page header -->
<div class="row mb-4">
    <div class="col-md-12">
        <h1>📝 Edit Meal</h1>
        <p class="text-muted">Update your meal assignment</p>
    </div>
</div>

<div class="row">
    <!-- Center the form (offset pushes it 2 columns from left) -->
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h5>Update Meal Details</h5>
            </div>
            <div class="card-body">
                <!-- Check if meal data exists -->
                <?php if (isset($mealItem) && $mealItem): ?>
                    <!-- Edit form - sends data to /weekplanner/update -->
                    <form method="POST" action="/weekplanner/update">
                        <!-- Hidden field - sends meal ID without showing it -->
                        <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($mealItem['id'] ?? ''); ?>">
                        
                        <!-- CSRF security token -->
                        <?php echo CsrfService::getTokenField(); ?>

                        <!-- Recipe selection dropdown -->
                        <div class="mb-3">
                            <label for="recipe_id" class="form-label">Recipe <span class="text-danger">*</span></label>
                            <select class="form-select" id="recipe_id" name="recipe_id" required>
                                <option value="">Select a recipe...</option>
                                <?php if (isset($recipes) && is_array($recipes)): ?>
                                    <?php foreach ($recipes as $recipe): ?>
                                        <option value="<?php echo htmlspecialchars($recipe['id'] ?? ''); ?>" 
                                            <?php echo (isset($mealItem['recipe_id']) && $mealItem['recipe_id'] == $recipe['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($recipe['title'] ?? 'Unknown Recipe'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Day of week dropdown -->
                        <div class="mb-3">
                            <label for="dayOfWeek" class="form-label">Day of Week <span class="text-danger">*</span></label>
                            <!-- required means user must select a day before submitting -->
                            <select class="form-select" id="dayOfWeek" name="day_of_week" required>
                                <option value="">Select a day...</option>
                                <!-- Each option checks if it matches current day -->
                                <!-- If yes, add 'selected' to show it as selected in dropdown -->
                                <option value="1" <?php echo $mealItem['day_of_week'] == 1 ? 'selected' : ''; ?>>Monday</option>
                                <option value="2" <?php echo $mealItem['day_of_week'] == 2 ? 'selected' : ''; ?>>Tuesday</option>
                                <option value="3" <?php echo $mealItem['day_of_week'] == 3 ? 'selected' : ''; ?>>Wednesday</option>
                                <option value="4" <?php echo $mealItem['day_of_week'] == 4 ? 'selected' : ''; ?>>Thursday</option>
                                <option value="5" <?php echo $mealItem['day_of_week'] == 5 ? 'selected' : ''; ?>>Friday</option>
                                <option value="6" <?php echo $mealItem['day_of_week'] == 6 ? 'selected' : ''; ?>>Saturday</option>
                                <option value="7" <?php echo $mealItem['day_of_week'] == 7 ? 'selected' : ''; ?>>Sunday</option>
                            </select>
                        </div>

                        <!-- Meal type dropdown -->
                        <div class="mb-3">
                            <label for="mealType" class="form-label">Meal Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="mealType" name="meal_type" required>
                                <option value="">Select meal type...</option>
                                <!-- Icons make it easier to identify meal types -->
                                <option value="breakfast" <?php echo $mealItem['meal_type'] == 'breakfast' ? 'selected' : ''; ?>>🥐 Breakfast</option>
                                <option value="lunch" <?php echo $mealItem['meal_type'] == 'lunch' ? 'selected' : ''; ?>>🍽️ Lunch</option>
                                <option value="dinner" <?php echo $mealItem['meal_type'] == 'dinner' ? 'selected' : ''; ?>>🍴 Dinner</option>
                                <option value="snack" <?php echo $mealItem['meal_type'] == 'snack' ? 'selected' : ''; ?>>🥜 Snack</option>
                            </select>
                        </div>

                        <!-- Number of servings input -->
                        <div class="mb-3">
                            <label for="servings" class="form-label">Servings <span class="text-danger">*</span></label>
                            <!-- type="number" provides +/- buttons and validates numeric input -->
                            <!-- min="1" max="20" limits range to reasonable values -->
                            <input type="number" class="form-control" id="servings" name="servings" min="1" max="20" value="<?php echo htmlspecialchars($mealItem['servings'] ?? 1); ?>" required>
                            <small class="text-muted">Number of servings for this meal</small>
                        </div>

                        <!-- Action buttons -->
                        <div class="mb-3">
                            <!-- Cancel button - goes back to week planner -->
                            <a href="/weekplanner/index" class="btn btn-secondary">Cancel</a>
                            <!-- Save button - submits form -->
                            <button type="submit" class="btn btn-success">Save Changes</button>
                        </div>
                    </form>
                <?php else: ?>
                    <!-- Error message if meal not found -->
                    <div class="alert alert-danger">
                        <p>Meal not found. <a href="/weekplanner/index">Back to Week Planner</a></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
// Save HTML to $content variable
$content = ob_get_clean();

// Include base layout (adds navbar and structure)
include __DIR__ . '/../layouts/base.php';
?>
