<?php
$pageTitle = 'Shopping List';
ob_start();
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h1>🛒 Shopping List</h1>
        <p class="text-muted">Your auto-generated shopping list based on weekly plan</p>
    </div>
</div>

<?php if (!isset($shoppingList) || !$shoppingList): ?>
    <div class="alert alert-info">
        <p>No shopping list generated yet. Create a weekly plan first!</p>
        <a href="/weekplanner/index" class="btn btn-primary">Go to Week Planner</a>
    </div>
<?php else: ?>
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Generated on <?php echo htmlspecialchars($shoppingList['generated_date'] ?? date('Y-m-d')); ?></h5>
                </div>
                <div class="card-body">
                    <div class="progress mb-3">
                        <div class="progress-bar" role="progressbar" style="width: <?php echo htmlspecialchars($progress['percentage'] ?? 0); ?>%" aria-valuenow="<?php echo htmlspecialchars($progress['checked'] ?? 0); ?>" aria-valuemin="0" aria-valuemax="<?php echo htmlspecialchars($progress['total'] ?? 1); ?>">
                            <?php echo htmlspecialchars($progress['checked'] ?? 0); ?>/<?php echo htmlspecialchars($progress['total'] ?? 0); ?>
                        </div>
                    </div>
                    <a href="/shoppinglist/download?id=<?php echo $shoppingList['id']; ?>" class="btn btn-secondary">📥 Download PDF</a>
                    <a href="/shoppinglist/generate" class="btn btn-primary">🔄 Regenerate</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h4>Items</h4>
            <?php if (isset($items) && count($items) > 0): ?>
                <form method="POST" action="/shoppinglist/update">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="50px"></th>
                                <th>Ingredient</th>
                                <th width="150px">Quantity</th>
                                <th width="100px">Unit</th>
                                <th width="100px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr <?php echo ($item['is_checked'] ?? 0) ? 'class="table-success"' : ''; ?>>
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="items[<?php echo $item['id']; ?>]" 
                                            <?php echo ($item['is_checked'] ?? 0) ? 'checked' : ''; ?> onchange="this.form.submit();">
                                    </td>
                                    <td <?php echo ($item['is_checked'] ?? 0) ? 'style="text-decoration: line-through;"' : ''; ?>>
                                        <?php echo htmlspecialchars($item['ingredient_name'] ?? 'Unknown'); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($item['quantity'] ?? '0'); ?></td>
                                    <td><?php echo htmlspecialchars($item['unit'] ?? ''); ?></td>
                                    <td>
                                        <a href="/shoppinglist/deleteitem?item_id=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </form>
            <?php else: ?>
                <p class="text-muted">No items in shopping list.</p>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/base.php';
?>
