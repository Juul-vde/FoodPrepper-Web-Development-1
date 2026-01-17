<?php
$pageTitle = 'Edit Meal';
ob_start();
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h1>📝 Edit Meal</h1>
        <p class="text-muted">Update your meal assignment</p>
    </div>
</div>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h5>Update Meal Details</h5>
            </div>
            <div class="card-body">
                <?php if (isset($mealItem) && $mealItem): ?>
                    <form method="POST" action="/weekplanner/update">
                        <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($mealItem['item_id']); ?>">

                        <div class="mb-3">
                            <label for="recipe_title" class="form-label">Recipe</label>
                            <input type="text" class="form-control" id="recipe_title" value="<?php echo htmlspecialchars($mealItem['recipe_title'] ?? 'Unknown'); ?>" disabled>
                        </div>

                        <div class="mb-3">
                            <label for="dayOfWeek" class="form-label">Day of Week <span class="text-danger">*</span></label>
                            <select class="form-select" id="dayOfWeek" name="day_of_week" required>
                                <option value="">Select a day...</option>
                                <option value="1" <?php echo $mealItem['day_of_week'] == 1 ? 'selected' : ''; ?>>Monday</option>
                                <option value="2" <?php echo $mealItem['day_of_week'] == 2 ? 'selected' : ''; ?>>Tuesday</option>
                                <option value="3" <?php echo $mealItem['day_of_week'] == 3 ? 'selected' : ''; ?>>Wednesday</option>
                                <option value="4" <?php echo $mealItem['day_of_week'] == 4 ? 'selected' : ''; ?>>Thursday</option>
                                <option value="5" <?php echo $mealItem['day_of_week'] == 5 ? 'selected' : ''; ?>>Friday</option>
                                <option value="6" <?php echo $mealItem['day_of_week'] == 6 ? 'selected' : ''; ?>>Saturday</option>
                                <option value="7" <?php echo $mealItem['day_of_week'] == 7 ? 'selected' : ''; ?>>Sunday</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="mealType" class="form-label">Meal Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="mealType" name="meal_type" required>
                                <option value="">Select meal type...</option>
                                <option value="breakfast" <?php echo $mealItem['meal_type'] == 'breakfast' ? 'selected' : ''; ?>>🥐 Breakfast</option>
                                <option value="lunch" <?php echo $mealItem['meal_type'] == 'lunch' ? 'selected' : ''; ?>>🍽️ Lunch</option>
                                <option value="dinner" <?php echo $mealItem['meal_type'] == 'dinner' ? 'selected' : ''; ?>>🍴 Dinner</option>
                                <option value="snack" <?php echo $mealItem['meal_type'] == 'snack' ? 'selected' : ''; ?>>🥜 Snack</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="servings" class="form-label">Servings <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="servings" name="servings" min="1" max="20" value="<?php echo htmlspecialchars($mealItem['servings'] ?? 1); ?>" required>
                            <small class="text-muted">Number of servings for this meal</small>
                        </div>

                        <div class="mb-3">
                            <a href="/weekplanner/index" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success">Save Changes</button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="alert alert-danger">
                        <p>Meal not found. <a href="/weekplanner/index">Back to Week Planner</a></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/base.php';
?>
