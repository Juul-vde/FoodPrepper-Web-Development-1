<?php
// View file for registration page
// This page displays a form where new users can create an account

// Set page title (shows in browser tab)
$pageTitle = 'Register';

// Start output buffering (captures HTML to save in variable)
ob_start();
?>

<!-- Center the registration form on page -->
<div class="row justify-content-center">
    <div class="col-md-6">
        <!-- Card with shadow for nice visual effect -->
        <div class="card shadow">
            <div class="card-body p-5">
                <!-- Page heading -->
                <h2 class="card-title text-center mb-4">Create Account</h2>
                
                <!-- Registration form - sends data to /auth/register -->
                <!-- POST method sends data securely (not visible in URL bar) -->
                <form method="POST" action="/auth/register">
                    <!-- CSRF Protection Token -->
                    <!-- This hidden field prevents hackers from creating fake accounts -->
                    <!-- The token proves this form came from our website, not a malicious site -->
                    <?php 
                    use App\Services\CsrfService; 
                    echo CsrfService::getTokenField(); 
                    ?>
                    
                    <!-- Name input field -->
                    <!-- required means user must fill this field before submitting -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>

                    <!-- Email input field -->
                    <!-- type="email" automatically checks if email format is valid -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <!-- Password input field -->
                    <!-- type="password" hides characters with dots as user types -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <!-- Confirm password field -->
                    <!-- User must type password twice to make sure they didn't make a typo -->
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>

                    <!-- Submit button (w-100 makes button full width) -->
                    <button type="submit" class="btn btn-primary w-100">Register</button>
                </form>

                <!-- Link to login page if user already has account -->
                <div class="text-center mt-3">
                    <p>Already have an account? <a href="/auth/index">Login here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Save all the HTML we generated into $content variable
$content = ob_get_clean();

// Include base layout (adds navbar, shows success/error messages, adds footer)
include __DIR__ . '/../layouts/base.php';
?>
