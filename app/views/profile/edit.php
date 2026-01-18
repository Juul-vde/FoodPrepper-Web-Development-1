<?php
// View file for editing user profile
// This page has a form with sections for photo, account info, and dietary preferences

// Set page title
$pageTitle = 'Edit Profile';

// Start output buffering
ob_start();
?>

<!-- Page header -->
<div class="row mb-4">
    <div class="col-md-12">
        <h1>✏️ Edit Profile</h1>
        <p class="text-muted">Update your account information and preferences</p>
    </div>
</div>

<div class="row">
    <!-- Center the form (offset-md-2 pushes it 2 columns from left) -->
    <div class="col-md-8 offset-md-2">
        <!-- Form - sends data to /profile/handleUpdate -->
        <!-- enctype="multipart/form-data" allows file uploads (for photos) -->
        <form method="POST" action="/profile/handleUpdate" enctype="multipart/form-data">
            <!-- CSRF Protection Token -->
            <!-- This security token prevents hackers from submitting fake profile updates -->
            <?php 
            use App\Services\CsrfService; 
            echo CsrfService::getTokenField(); 
            ?>
            
            <!-- Profile Photo Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Profile Photo</h5>
                </div>
                <div class="card-body">
                    <!-- Current photo preview (centered) -->
                    <div class="text-center mb-3">
                        <?php if (isset($user['profile_photo']) && $user['profile_photo']): ?>
                            <!-- Show existing photo -->
                            <img src="<?php echo htmlspecialchars($user['profile_photo']); ?>" 
                                 class="rounded-circle mb-3 profile-photo" 
                                 id="profilePhotoPreview">
                        <?php else: ?>
                            <!-- Show placeholder if no photo -->
                            <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3 profile-photo-placeholder" 
                                 id="profilePhotoPreview">
                                <span class="text-muted">No Photo</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- File upload field (for uploading from computer) -->
                    <div class="mb-3">
                        <label for="profile_photo_file" class="form-label">Upload New Photo</label>
                        <!-- accept limits file types to images only -->
                        <!-- onchange calls JavaScript to preview photo before uploading -->
                        <input type="file" 
                               class="form-control" 
                               id="profile_photo_file" 
                               name="profile_photo_file"
                               accept="image/jpeg,image/png,image/jpg,image/gif"
                               onchange="previewPhoto(this)">
                        <small class="text-muted">Accepted formats: JPG, PNG, GIF. Max size: 5MB</small>
                    </div>
                    
                    <!-- URL field (for entering web link to photo) -->
                    <div class="mb-3">
                        <label for="profile_photo" class="form-label">Or Enter Photo URL</label>
                        <!-- type="url" validates URL format -->
                        <input type="url" 
                               class="form-control" 
                               id="profile_photo" 
                               name="profile_photo"
                               value=""
                               placeholder="https://example.com/photo.jpg">
                        <small class="text-muted">Enter a direct URL to an external image (leave empty to keep current photo)</small>
                    </div>
                </div>
            </div>

            <!-- Account Information Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Account Information</h5>
                </div>
                <div class="card-body">
                    <!-- Name field (required) -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Name *</label>
                        <!-- value fills in current name from database -->
                        <input type="text" 
                               class="form-control" 
                               id="name" 
                               name="name"
                               value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>"
                               required>
                    </div>
                    
                    <!-- Email field (disabled - can't be changed) -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <!-- disabled means user can see but not edit -->
                        <input type="email" 
                               class="form-control" 
                               id="email"
                               value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"
                               disabled>
                        <small class="text-muted">Email cannot be changed</small>
                    </div>
                </div>
            </div>

            <!-- Dietary Information Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Dietary Information</h5>
                </div>
                <div class="card-body">
                    <!-- Dietary preferences dropdown -->
                    <div class="mb-3">
                        <label for="dietary_preferences" class="form-label">Dietary Preferences</label>
                        <select class="form-select" id="dietary_preferences" name="dietary_preferences">
                            <option value="">None</option>
                            <!-- Each option checks if it matches user's current preference -->
                            <!-- If yes, add 'selected' attribute to show it as selected -->
                            <option value="Vegetarian" <?php echo (isset($user['dietary_preferences']) && $user['dietary_preferences'] === 'Vegetarian') ? 'selected' : ''; ?>>Vegetarian</option>
                            <option value="Vegan" <?php echo (isset($user['dietary_preferences']) && $user['dietary_preferences'] === 'Vegan') ? 'selected' : ''; ?>>Vegan</option>
                            <option value="Pescatarian" <?php echo (isset($user['dietary_preferences']) && $user['dietary_preferences'] === 'Pescatarian') ? 'selected' : ''; ?>>Pescatarian</option>
                            <option value="Gluten-Free" <?php echo (isset($user['dietary_preferences']) && $user['dietary_preferences'] === 'Gluten-Free') ? 'selected' : ''; ?>>Gluten-Free</option>
                            <option value="Dairy-Free" <?php echo (isset($user['dietary_preferences']) && $user['dietary_preferences'] === 'Dairy-Free') ? 'selected' : ''; ?>>Dairy-Free</option>
                            <option value="Keto" <?php echo (isset($user['dietary_preferences']) && $user['dietary_preferences'] === 'Keto') ? 'selected' : ''; ?>>Keto</option>
                            <option value="Paleo" <?php echo (isset($user['dietary_preferences']) && $user['dietary_preferences'] === 'Paleo') ? 'selected' : ''; ?>>Paleo</option>
                        </select>
                    </div>
                    
                    <!-- Allergies text area -->
                    <div class="mb-3">
                        <label for="allergies" class="form-label">Allergies</label>
                        <!-- rows="3" makes text area 3 lines tall -->
                        <textarea class="form-control" 
                                  id="allergies" 
                                  name="allergies"
                                  rows="3"
                                  placeholder="E.g., Peanuts, Shellfish, Eggs"><?php echo htmlspecialchars($user['allergies'] ?? ''); ?></textarea>
                        <small class="text-muted">List any food allergies or intolerances</small>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card mb-4">
                <div class="card-body">
                    <!-- Save button (submits form) -->
                    <button type="submit" class="btn btn-primary">💾 Save Changes</button>
                    <!-- Cancel button (goes back to profile page) -->
                    <a href="/profile/index" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// JavaScript function to preview photo before uploading
// Shows photo immediately when user selects a file (before form submit)
function previewPhoto(input) {
    // Get the preview element (img or div)
    const preview = document.getElementById('profilePhotoPreview');
    
    // Check if user selected a file
    if (input.files && input.files[0]) {
        // FileReader reads the file content
        const reader = new FileReader();
        
        // When file is loaded, update preview
        reader.onload = function(e) {
            // If preview is already an img tag
            if (preview.tagName === 'IMG') {
                // Just update the source
                preview.src = e.target.result;
            } else {
                // If preview is a div (placeholder), replace it with img tag
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'rounded-circle mb-3';
                img.id = 'profilePhotoPreview';
                img.style.width = '150px';
                img.style.height = '150px';
                img.style.objectFit = 'cover';
                preview.parentNode.replaceChild(img, preview);
            }
        };
        
        // Read the file as a data URL (converts image to text that can be shown in img tag)
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php
// Save HTML to $content variable
$content = ob_get_clean();

// Include base layout
include __DIR__ . '/../layouts/base.php';
?>
