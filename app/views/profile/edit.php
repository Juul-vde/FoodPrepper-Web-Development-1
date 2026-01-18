<?php
$pageTitle = 'Edit Profile';
ob_start();
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h1>✏️ Edit Profile</h1>
        <p class="text-muted">Update your account information and preferences</p>
    </div>
</div>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <form method="POST" action="/profile/handleUpdate" enctype="multipart/form-data">
            <!-- Profile Photo Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Profile Photo</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <?php if (isset($user['profile_photo']) && $user['profile_photo']): ?>
                            <img src="<?php echo htmlspecialchars($user['profile_photo']); ?>" 
                                 class="rounded-circle mb-3" 
                                 id="profilePhotoPreview"
                                 style="width: 150px; height: 150px; object-fit: cover;">
                        <?php else: ?>
                            <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" 
                                 id="profilePhotoPreview"
                                 style="width: 150px; height: 150px;">
                                <span class="text-muted">No Photo</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="profile_photo_file" class="form-label">Upload New Photo</label>
                        <input type="file" 
                               class="form-control" 
                               id="profile_photo_file" 
                               name="profile_photo_file"
                               accept="image/jpeg,image/png,image/jpg,image/gif"
                               onchange="previewPhoto(this)">
                        <small class="text-muted">Accepted formats: JPG, PNG, GIF. Max size: 5MB</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="profile_photo" class="form-label">Or Enter Photo URL</label>
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
                    <div class="mb-3">
                        <label for="name" class="form-label">Name *</label>
                        <input type="text" 
                               class="form-control" 
                               id="name" 
                               name="name"
                               value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>"
                               required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
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
                    <div class="mb-3">
                        <label for="dietary_preferences" class="form-label">Dietary Preferences</label>
                        <select class="form-select" id="dietary_preferences" name="dietary_preferences">
                            <option value="">None</option>
                            <option value="Vegetarian" <?php echo (isset($user['dietary_preferences']) && $user['dietary_preferences'] === 'Vegetarian') ? 'selected' : ''; ?>>Vegetarian</option>
                            <option value="Vegan" <?php echo (isset($user['dietary_preferences']) && $user['dietary_preferences'] === 'Vegan') ? 'selected' : ''; ?>>Vegan</option>
                            <option value="Pescatarian" <?php echo (isset($user['dietary_preferences']) && $user['dietary_preferences'] === 'Pescatarian') ? 'selected' : ''; ?>>Pescatarian</option>
                            <option value="Gluten-Free" <?php echo (isset($user['dietary_preferences']) && $user['dietary_preferences'] === 'Gluten-Free') ? 'selected' : ''; ?>>Gluten-Free</option>
                            <option value="Dairy-Free" <?php echo (isset($user['dietary_preferences']) && $user['dietary_preferences'] === 'Dairy-Free') ? 'selected' : ''; ?>>Dairy-Free</option>
                            <option value="Keto" <?php echo (isset($user['dietary_preferences']) && $user['dietary_preferences'] === 'Keto') ? 'selected' : ''; ?>>Keto</option>
                            <option value="Paleo" <?php echo (isset($user['dietary_preferences']) && $user['dietary_preferences'] === 'Paleo') ? 'selected' : ''; ?>>Paleo</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="allergies" class="form-label">Allergies</label>
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
                    <button type="submit" class="btn btn-primary">💾 Save Changes</button>
                    <a href="/profile/index" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function previewPhoto(input) {
    const preview = document.getElementById('profilePhotoPreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            // If preview is an img element
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                // Replace div with img
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
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/base.php';
?>
