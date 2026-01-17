<?php
$pageTitle = 'Profile';
ob_start();
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h1>👤 Profile Settings</h1>
        <p class="text-muted">Manage your account information and preferences</p>
    </div>
</div>

<?php if (isset($user) && $user): ?>
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <?php if (isset($user['profile_photo']) && $user['profile_photo']): ?>
                        <img src="<?php echo htmlspecialchars($user['profile_photo']); ?>" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    <?php else: ?>
                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width: 150px; height: 150px;">
                            <span class="text-muted">No Photo</span>
                        </div>
                    <?php endif; ?>
                    <h5><?php echo htmlspecialchars($user['name'] ?? 'Unknown'); ?></h5>
                    <p class="text-muted"><?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
                    <a href="/profile/edit" class="btn btn-primary">Edit Profile</a>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Account Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Name:</strong></p>
                            <p><?php echo htmlspecialchars($user['name'] ?? '-'); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Email:</strong></p>
                            <p><?php echo htmlspecialchars($user['email'] ?? '-'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5>Dietary Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Dietary Preferences:</strong></p>
                            <p><?php echo htmlspecialchars($user['dietary_preferences'] ?? 'None specified'); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Allergies:</strong></p>
                            <p><?php echo htmlspecialchars($user['allergies'] ?? 'None specified'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>Account Actions</h5>
                </div>
                <div class="card-body">
                    <a href="/profile/edit" class="btn btn-warning">Edit Profile</a>
                    <a href="/auth/logout" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-danger">
        <p>Profile not found. Please try logging in again.</p>
        <a href="/auth/index" class="btn btn-primary">Go to Login</a>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/base.php';
?>
