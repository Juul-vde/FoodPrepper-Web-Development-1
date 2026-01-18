<?php
// View file for user profile page
// This page displays user information, dietary preferences, and profile photo

// Set page title
$pageTitle = 'Profile';

// Start output buffering
ob_start();
?>

<!-- Page header -->
<div class="row mb-4">
    <div class="col-md-12">
        <h1>👤 Profile Settings</h1>
        <p class="text-muted">Manage your account information and preferences</p>
    </div>
</div>

<!-- Check if user data exists -->
<!-- If $user variable is set, show profile. Otherwise, show error -->
<?php if (isset($user) && $user): ?>
    <div class="row">
        <!-- Left column: Profile photo card -->
        <!-- col-md-4 means: takes 4/12 (1/3) of width on medium+ screens -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <!-- Profile photo -->
                    <!-- If user has photo, show it. Otherwise show placeholder -->
                    <?php if (isset($user['profile_photo']) && $user['profile_photo']): ?>
                        <!-- Show profile photo as circle (150x150 pixels) -->
                        <img src="<?php echo htmlspecialchars($user['profile_photo']); ?>" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    <?php else: ?>
                        <!-- Show placeholder if no photo -->
                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width: 150px; height: 150px;">
                            <span class="text-muted">No Photo</span>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Display user name and email -->
                    <h5><?php echo htmlspecialchars($user['name'] ?? 'Unknown'); ?></h5>
                    <p class="text-muted"><?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
                    
                    <!-- Button to edit profile -->
                    <a href="/profile/edit" class="btn btn-primary">Edit Profile</a>
                </div>
            </div>
        </div>

        <!-- Right column: Information cards -->
        <!-- col-md-8 means: takes 8/12 (2/3) of width -->
        <div class="col-md-8">
            <!-- Account Information Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Account Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <!-- Name field -->
                        <div class="col-md-6">
                            <p><strong>Name:</strong></p>
                            <!-- Show user name, or '-' if not set -->
                            <p><?php echo htmlspecialchars($user['name'] ?? '-'); ?></p>
                        </div>
                        <!-- Email field -->
                        <div class="col-md-6">
                            <p><strong>Email:</strong></p>
                            <p><?php echo htmlspecialchars($user['email'] ?? '-'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dietary Information Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Dietary Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <!-- Dietary preferences (e.g., Vegetarian, Vegan) -->
                        <div class="col-md-6">
                            <p><strong>Dietary Preferences:</strong></p>
                            <!-- Show preferences, or 'None specified' if empty -->
                            <p><?php echo htmlspecialchars($user['dietary_preferences'] ?? 'None specified'); ?></p>
                        </div>
                        <!-- Allergies -->
                        <div class="col-md-6">
                            <p><strong>Allergies:</strong></p>
                            <p><?php echo htmlspecialchars($user['allergies'] ?? 'None specified'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Actions Card -->
            <div class="card">
                <div class="card-header">
                    <h5>Account Actions</h5>
                </div>
                <div class="card-body">
                    <!-- Logout button (red for warning) -->
                    <a href="/auth/logout" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- Error message if user data not found -->
    <div class="alert alert-danger">
        <p>Profile not found. Please try logging in again.</p>
        <a href="/auth/index" class="btn btn-primary">Go to Login</a>
    </div>
<?php endif; ?>

<?php
// Save HTML to $content variable
$content = ob_get_clean();

// Include base layout (adds navbar and structure)
include __DIR__ . '/../layouts/base.php';
?>
