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
                    <button type="button" class="btn btn-secondary" id="downloadListBtn" onclick="downloadShoppingList()">📥 Download</button>
                    <button type="button" class="btn btn-secondary" id="copyListBtn" onclick="copyShoppingListToClipboard()">📋 Copy List</button>
                    <form method="POST" action="/shoppinglist/generate" style="display: inline;">
                        <button type="submit" class="btn btn-primary" onclick="return confirm('This will regenerate the shopping list from your current weekly plan. Continue?');">🔄 Regenerate</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h4>Items</h4>
            <?php if (isset($items) && count($items) > 0): ?>
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
                            <tr id="item-<?php echo htmlspecialchars($item['id']); ?>" class="<?php echo ($item['is_checked'] ?? 0) ? 'item-checked' : ''; ?>">
                                <td>
                                    <input type="checkbox" 
                                           class="form-check-input shopping-item-checkbox" 
                                           data-item-id="<?php echo htmlspecialchars($item['id']); ?>"
                                           <?php echo ($item['is_checked'] ?? 0) ? 'checked' : ''; ?>>
                                </td>
                                <td class="item-name <?php echo ($item['is_checked'] ?? 0) ? 'text-checked' : ''; ?>">
                                    <?php echo htmlspecialchars($item['ingredient_name'] ?? 'Unknown'); ?>
                                </td>
                                <td class="<?php echo ($item['is_checked'] ?? 0) ? 'text-checked' : ''; ?>"><?php echo htmlspecialchars($item['quantity'] ?? '0'); ?></td>
                                <td class="<?php echo ($item['is_checked'] ?? 0) ? 'text-checked' : ''; ?>"><?php echo htmlspecialchars($item['unit'] ?? ''); ?></td>
                                <td>
                                    <form method="POST" action="/shoppinglist/deleteitem" style="display: inline;">
                                        <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item['id']); ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this item?');">🗑️ Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-muted">No items in shopping list.</p>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<style>
.item-checked {
    background-color: #d1e7dd !important;
}

.text-checked {
    text-decoration: line-through;
    color: #6c757d;
}

.shopping-item-checkbox {
    cursor: pointer;
}

.shopping-item-checkbox:hover {
    transform: scale(1.1);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.shopping-item-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function(e) {
            e.preventDefault(); // Prevent any default behavior
            
            const itemId = this.getAttribute('data-item-id');
            const row = document.getElementById('item-' + itemId);
            const textCells = row.querySelectorAll('td:not(:first-child):not(:last-child)');
            const isChecked = this.checked;
            
            // Disable checkbox during request
            this.disabled = true;
            
            // Send AJAX request
            fetch('/shoppinglist/toggleitem', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'item_id=' + encodeURIComponent(itemId)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Toggle visual state
                    if (isChecked) {
                        row.classList.add('item-checked');
                        textCells.forEach(cell => {
                            cell.classList.add('text-checked');
                        });
                    } else {
                        row.classList.remove('item-checked');
                        textCells.forEach(cell => {
                            cell.classList.remove('text-checked');
                        });
                    }
                    
                    // Update progress bar dynamically
                    updateProgressBar();
                } else {
                    throw new Error(data.error || 'Failed to update item');
                }
            })
            .catch(error => {
                console.error('Error toggling item:', error);
                // Revert checkbox state on error
                this.checked = !isChecked;
                alert('Failed to update item: ' + error.message);
            })
            .finally(() => {
                this.disabled = false;
            });
        });
    });
    
    // Function to update progress bar without page reload
    function updateProgressBar() {
        const allCheckboxes = document.querySelectorAll('.shopping-item-checkbox');
        const total = allCheckboxes.length;
        const checked = document.querySelectorAll('.shopping-item-checkbox:checked').length;
        const percentage = total > 0 ? Math.round((checked / total) * 100) : 0;
        
        const progressBar = document.querySelector('.progress-bar');
        if (progressBar) {
            progressBar.style.width = percentage + '%';
            progressBar.setAttribute('aria-valuenow', checked);
            progressBar.textContent = checked + '/' + total;
        }
    }
});

// Function to copy shopping list to clipboard (excludes checked items)
function copyShoppingListToClipboard() {
    const rows = document.querySelectorAll('table tbody tr');
    let textList = '🛒 Shopping List\n\n';
    
    rows.forEach(row => {
        // Skip checked items
        const checkbox = row.querySelector('.shopping-item-checkbox');
        if (checkbox && checkbox.checked) {
            return; // Skip this item
        }
        
        const cells = row.querySelectorAll('td');
        if (cells.length >= 4) {
            const ingredient = cells[1].textContent.trim();
            const quantity = cells[2].textContent.trim();
            const unit = cells[3].textContent.trim();
            
            textList += `${ingredient} - ${quantity} ${unit}\n`;
        }
    });
    
    // Try modern clipboard API first
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(textList)
            .then(() => {
                const btn = document.getElementById('copyListBtn');
                const originalText = btn.innerHTML;
                btn.innerHTML = '✅ Copied!';
                btn.classList.remove('btn-secondary');
                btn.classList.add('btn-success');
                
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-secondary');
                }, 2000);
            })
            .catch(err => {
                console.error('Failed to copy:', err);
                alert('Failed to copy to clipboard. Please try again.');
            });
    } else {
        // Fallback for older browsers
        const textarea = document.createElement('textarea');
        textarea.value = textList;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        
        try {
            document.execCommand('copy');
            const btn = document.getElementById('copyListBtn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '✅ Copied!';
            btn.classList.remove('btn-secondary');
            btn.classList.add('btn-success');
            
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-secondary');
            }, 2000);
        } catch (err) {
            console.error('Fallback copy failed:', err);
            alert('Failed to copy to clipboard');
        } finally {
            document.body.removeChild(textarea);
        }
    }
}

// Function to download shopping list as text file (excludes checked items)
function downloadShoppingList() {
    const rows = document.querySelectorAll('table tbody tr');
    let textList = '🛒 Shopping List\n\n';
    
    rows.forEach(row => {
        // Skip checked items
        const checkbox = row.querySelector('.shopping-item-checkbox');
        if (checkbox && checkbox.checked) {
            return; // Skip this item
        }
        
        const cells = row.querySelectorAll('td');
        if (cells.length >= 4) {
            const ingredient = cells[1].textContent.trim();
            const quantity = cells[2].textContent.trim();
            const unit = cells[3].textContent.trim();
            
            textList += `${ingredient} - ${quantity} ${unit}\n`;
        }
    });
    
    // Create a blob and download it
    const blob = new Blob([textList], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'shopping-list.txt';
    document.body.appendChild(a);
    a.click();
    
    // Cleanup
    window.URL.revokeObjectURL(url);
    document.body.removeChild(a);
    
    // Visual feedback
    const btn = document.getElementById('downloadListBtn');
    const originalText = btn.innerHTML;
    btn.innerHTML = '✅ Downloaded!';
    btn.classList.remove('btn-secondary');
    btn.classList.add('btn-success');
    
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.classList.remove('btn-success');
        btn.classList.add('btn-secondary');
    }, 2000);
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/base.php';
?>
