<?php
// View file for shopping list
// Shows auto-generated shopping list from week planner
// Has checkboxes to mark items as bought, download and copy features

// Set page title
$pageTitle = 'Shopping List';

// Start output buffering
ob_start();
?>

<!-- CSRF Protection Token (used by JavaScript for AJAX requests) -->
<!-- This security token is required for checkbox toggle operations -->
<!-- Prevents hackers from manipulating shopping lists via fake requests -->
<?php 
use App\Services\CsrfService; 
echo CsrfService::getTokenField(); 
?>

<!-- Page header -->
<div class="row mb-4">
    <div class="col-md-12">
        <h1>🛒 Shopping List</h1>
        <p class="text-muted">Your auto-generated shopping list based on weekly plan</p>
    </div>
</div>

<?php if (!isset($shoppingList) || !$shoppingList): ?>
    <!-- No shopping list exists yet -->
    <div class="alert alert-info">
        <p>No shopping list generated yet. Create a weekly plan first!</p>
        <a href="/weekplanner/index" class="btn btn-primary">Go to Week Planner</a>
    </div>
<?php else: ?>
    <!-- Shopping list exists - show it -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <!-- Show generation date -->
                    <h5>Generated on <?php echo htmlspecialchars($shoppingList['generated_date'] ?? date('Y-m-d')); ?></h5>
                </div>
                <div class="card-body">
                    <!-- Progress bar showing how many items are checked -->
                    <div class="progress mb-3">
                        <!-- Width is percentage of items checked -->
                        <div class="progress-bar" role="progressbar" style="width: <?php echo htmlspecialchars($progress['percentage'] ?? 0); ?>%" aria-valuenow="<?php echo htmlspecialchars($progress['checked'] ?? 0); ?>" aria-valuemin="0" aria-valuemax="<?php echo htmlspecialchars($progress['total'] ?? 1); ?>">
                            <!-- Show "X/Y" text inside bar -->
                            <?php echo htmlspecialchars($progress['checked'] ?? 0); ?>/<?php echo htmlspecialchars($progress['total'] ?? 0); ?>
                        </div>
                    </div>
                    
                    <!-- Action buttons -->
                    <!-- Download button - saves list as text file -->
                    <button type="button" class="btn btn-secondary" id="downloadListBtn" onclick="downloadShoppingList()">📥 Download</button>
                    <!-- Copy button - copies list to clipboard -->
                    <button type="button" class="btn btn-secondary" id="copyListBtn" onclick="copyShoppingListToClipboard()">📋 Copy List</button>
                    <!-- Regenerate button - creates new list from current week plan -->
                    <form method="POST" action="/shoppinglist/generate" class="form-inline">
                        <?php echo CsrfService::getTokenField(); ?>
                        <button type="submit" class="btn btn-primary" onclick="return confirm('This will regenerate the shopping list from your current weekly plan. Continue?');">🔄 Regenerate</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Shopping list items table -->
    <div class="row">
        <div class="col-md-12">
            <h4>Items</h4>
            <?php if (isset($items) && count($items) > 0): ?>
                <!-- Table showing all shopping list items -->
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="50px"></th> <!-- Checkbox column -->
                            <th>Ingredient</th>
                            <th width="150px">Quantity</th>
                            <th width="100px">Unit</th>
                            <th width="100px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Loop through each shopping list item -->
                        <?php foreach ($items as $item): ?>
                            <!-- Row ID used by JavaScript to update row -->
                            <!-- Add 'item-checked' class if item is already checked -->
                            <tr id="item-<?php echo htmlspecialchars($item['id']); ?>" class="<?php echo ($item['is_checked'] ?? 0) ? 'item-checked' : ''; ?>">
                                <td>
                                    <!-- Checkbox for marking item as bought -->
                                    <!-- data-item-id stores item ID for JavaScript -->
                                    <!-- checked attribute if item is already checked -->
                                    <input type="checkbox" 
                                           class="form-check-input shopping-item-checkbox" 
                                           data-item-id="<?php echo htmlspecialchars($item['id']); ?>"
                                           <?php echo ($item['is_checked'] ?? 0) ? 'checked' : ''; ?>>
                                </td>
                                <!-- Ingredient name with strikethrough if checked -->
                                <td class="item-name <?php echo ($item['is_checked'] ?? 0) ? 'text-checked' : ''; ?>">
                                    <?php echo htmlspecialchars($item['ingredient_name'] ?? 'Unknown'); ?>
                                </td>
                                <!-- Quantity with strikethrough if checked -->
                                <td class="<?php echo ($item['is_checked'] ?? 0) ? 'text-checked' : ''; ?>"><?php echo htmlspecialchars($item['quantity'] ?? '0'); ?></td>
                                <!-- Unit with strikethrough if checked -->
                                <td class="<?php echo ($item['is_checked'] ?? 0) ? 'text-checked' : ''; ?>"><?php echo htmlspecialchars($item['unit'] ?? ''); ?></td>
                                <td>
                                    <!-- Delete button form -->
                                    <form method="POST" action="/shoppinglist/deleteitem" class="form-inline">
                                        <?php echo CsrfService::getTokenField(); ?>
                                        <!-- Hidden field with item ID -->
                                        <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item['id']); ?>">
                                        <!-- Delete button with confirmation -->
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this item?');">🗑️ Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <!-- No items in list -->
                <p class="text-muted">No items in shopping list.</p>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<script>
// JavaScript for interactive shopping list features

// Wait for page to fully load
document.addEventListener('DOMContentLoaded', function() {
    // Get all checkboxes in the list
    const checkboxes = document.querySelectorAll('.shopping-item-checkbox');
    
    // Add click handler to each checkbox
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function(e) {
            e.preventDefault(); // Prevent any default behavior
            
            // Get item ID from checkbox's data attribute
            const itemId = this.getAttribute('data-item-id');
            // Get the table row for this item
            const row = document.getElementById('item-' + itemId);
            // Get all text cells (skip first and last columns)
            const textCells = row.querySelectorAll('td:not(:first-child):not(:last-child)');
            // Remember if checkbox is checked
            const isChecked = this.checked;
            
            // Disable checkbox during AJAX request
            this.disabled = true;
            
            // Get CSRF token from hidden input field
            // This security token proves the request came from our site
            const csrfToken = document.querySelector('input[name="csrf_token"]')?.value || '';
            
            // Send AJAX request to toggle item checked status
            fetch('/shoppinglist/toggleitem', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                // Send item ID and CSRF token
                body: 'item_id=' + encodeURIComponent(itemId) + '&csrf_token=' + encodeURIComponent(csrfToken)
            })
            .then(response => response.json()) // Parse JSON response
            .then(data => {
                if (data.success) {
                    // Toggle visual state if successful
                    if (isChecked) {
                        // Add green background and strikethrough
                        row.classList.add('item-checked');
                        textCells.forEach(cell => {
                            cell.classList.add('text-checked');
                        });
                    } else {
                        // Remove green background and strikethrough
                        row.classList.remove('item-checked');
                        textCells.forEach(cell => {
                            cell.classList.remove('text-checked');
                        });
                    }
                    
                    // Update progress bar with new counts
                    updateProgressBar();
                } else {
                    // Show error if toggle failed
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
                // Re-enable checkbox after request completes
                this.disabled = false;
            });
        });
    });
    
    // Function to update progress bar without page reload
    function updateProgressBar() {
        const allCheckboxes = document.querySelectorAll('.shopping-item-checkbox');
        const total = allCheckboxes.length; // Total items
        const checked = document.querySelectorAll('.shopping-item-checkbox:checked').length; // Checked items
        const percentage = total > 0 ? Math.round((checked / total) * 100) : 0; // Calculate percentage
        
        const progressBar = document.querySelector('.progress-bar');
        if (progressBar) {
            // Update progress bar width
            progressBar.style.width = percentage + '%';
            progressBar.setAttribute('aria-valuenow', checked);
            // Update text inside bar
            progressBar.textContent = checked + '/' + total;
        }
    }
});

// Function to copy shopping list to clipboard
// Only copies unchecked items (items still need to buy)
function copyShoppingListToClipboard() {
    const rows = document.querySelectorAll('table tbody tr');
    let textList = '🛒 Shopping List\n\n';
    
    // Loop through each row
    rows.forEach(row => {
        // Skip checked items
        const checkbox = row.querySelector('.shopping-item-checkbox');
        if (checkbox && checkbox.checked) {
            return; // Skip this item
        }
        
        // Get ingredient, quantity, and unit from table cells
        const cells = row.querySelectorAll('td');
        if (cells.length >= 4) {
            const ingredient = cells[1].textContent.trim();
            const quantity = cells[2].textContent.trim();
            const unit = cells[3].textContent.trim();
            
            // Add to text list
            textList += `${ingredient} - ${quantity} ${unit}\n`;
        }
    });
    
    // Try modern clipboard API first (works in newer browsers)
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(textList)
            .then(() => {
                // Show success message on button
                const btn = document.getElementById('copyListBtn');
                const originalText = btn.innerHTML;
                btn.innerHTML = '✅ Copied!';
                btn.classList.remove('btn-secondary');
                btn.classList.add('btn-success');
                
                // Reset button after 2 seconds
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
        // Create temporary textarea element
        const textarea = document.createElement('textarea');
        textarea.value = textList;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        
        try {
            // Use old copy command
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
            // Remove temporary textarea
            document.body.removeChild(textarea);
        }
    }
}

// Function to download shopping list as text file
// Only includes unchecked items (items still need to buy)
function downloadShoppingList() {
    const rows = document.querySelectorAll('table tbody tr');
    let textList = '🛒 Shopping List\n\n';
    
    // Loop through each row
    rows.forEach(row => {
        // Skip checked items
        const checkbox = row.querySelector('.shopping-item-checkbox');
        if (checkbox && checkbox.checked) {
            return; // Skip this item
        }
        
        // Get ingredient, quantity, and unit
        const cells = row.querySelectorAll('td');
        if (cells.length >= 4) {
            const ingredient = cells[1].textContent.trim();
            const quantity = cells[2].textContent.trim();
            const unit = cells[3].textContent.trim();
            
            textList += `${ingredient} - ${quantity} ${unit}\n`;
        }
    });
    
    // Create a blob (binary large object) from text
    const blob = new Blob([textList], { type: 'text/plain' });
    // Create temporary download link
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'shopping-list.txt'; // Filename for download
    document.body.appendChild(a);
    a.click(); // Trigger download
    
    // Cleanup
    window.URL.revokeObjectURL(url);
    document.body.removeChild(a);
    
    // Visual feedback on button
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
// Save HTML to $content variable
$content = ob_get_clean();

// Include base layout
include __DIR__ . '/../layouts/base.php';
?>
