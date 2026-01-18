<?php
// View file for week planner main page
// Shows all meals planned for the week in a table

use App\Services\CsrfService;

// Set page title
$pageTitle = 'Week Planner';

// Start output buffering
ob_start();
?>

<!-- Page header -->
<div class="row mb-4">
    <div class="col-md-12">
        <h1>📅 Week Planner</h1>
        <p class="text-muted">Plan your meals for the week ahead</p>
    </div>
</div>

<!-- Check if weekly plan exists -->
<?php if (!isset($weeklyPlan) || !$weeklyPlan): ?>
    <!-- No plan exists yet - show message with button to create one -->
    <div class="alert alert-info">
        <p>No weekly plan exists yet. Create one to get started!</p>
        <a href="/weekplanner/create" class="btn btn-primary">Create Weekly Plan</a>
    </div>
<?php else: ?>
    <!-- Weekly plan exists - show it -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <!-- Show which week this plan is for -->
                    <h5>Week of <?php echo htmlspecialchars($weeklyPlan['week_start_date'] ?? ''); ?></h5>
                </div>
                <div class="card-body">
                    <!-- Button to add new meal -->
                    <a href="/weekplanner/addmeal" class="btn btn-success">Add Meal</a>
                    <!-- Button to generate shopping list from this plan -->
                    <a href="/shoppinglist/index" class="btn btn-info">Generate Shopping List</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Table of planned meals -->
    <div class="row mt-4">
        <div class="col-md-12">
            <h4>Planned Meals</h4>
            <?php if (isset($meals) && count($meals) > 0): ?>
                <!-- Show meals in a table -->
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <!-- Table column headers -->
                            <th>Day</th>
                            <th>Meal Type</th>
                            <th>Recipe</th>
                            <th>Servings</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Loop through each meal and display it -->
                        <?php foreach ($meals as $meal): ?>
                            <!-- Each row is clickable to view recipe (double-click) -->
                            <!-- data-recipe-id stores recipe ID for JavaScript -->
                            <tr class="meal-row clickable-row" data-recipe-id="<?php echo htmlspecialchars($meal['recipe_id'] ?? ''); ?>" title="Double-click to view recipe">
                                <!-- Display meal information -->
                                <td><?php echo htmlspecialchars($meal['day_of_week'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($meal['meal_type'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($meal['recipe_title'] ?? 'Unknown'); ?></td>
                                <td><?php echo htmlspecialchars($meal['servings'] ?? 1); ?></td>
                                <td>
                                    <!-- Edit button -->
                                    <a href="/weekplanner/edit?meal_id=<?php echo htmlspecialchars($meal['item_id']); ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <!-- Remove meal form -->
                                    <form method="POST" action="/weekplanner/removemeal" class="form-inline">
                                        <!-- CSRF Protection Token -->
                                        <!-- Prevents hackers from removing meals without permission -->
                                        <?php echo CsrfService::getTokenField(); ?>
                                        <!-- Hidden field with meal ID -->
                                        <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($meal['item_id']); ?>">
                                        <!-- Remove button with confirmation -->
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Remove this meal?');">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <!-- No meals planned yet -->
                <p class="text-muted">No meals planned yet.</p>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<script>
// JavaScript to make meal rows clickable
document.addEventListener('DOMContentLoaded', function() {
    // Get all meal rows
    const mealRows = document.querySelectorAll('.meal-row');
    
    // Add click handlers to each row
    mealRows.forEach(row => {
        // Double-click handler - opens recipe page
        row.addEventListener('dblclick', function(e) {
            // Don't trigger if clicking on buttons or links
            if (e.target.closest('button') || e.target.closest('a')) {
                return;
            }
            
            // Get recipe ID from row's data attribute
            const recipeId = this.getAttribute('data-recipe-id');
            if (recipeId) {
                // Navigate to recipe view page
                window.location.href = '/recipe/view?id=' + recipeId;
            }
        });
        
        // Hover effect - change background when mouse over row
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
        });
        
        // Remove hover effect when mouse leaves
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });
});
</script>

<?php
// Save HTML to $content variable
$content = ob_get_clean();

// Include base layout
include __DIR__ . '/../layouts/base.php';
?>
