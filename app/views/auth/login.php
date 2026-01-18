<?php
// View file for login page
// This page displays a login form where users enter email and password

// Set page title (shows in browser tab)
$pageTitle = 'Login';

// Start output buffering (captures HTML to variable)
ob_start();
?>

<!-- Center the login card on the page using Bootstrap grid -->
<div class="row justify-content-center">
    <div class="col-md-6">
        <!-- Card with shadow for nice visual effect -->
        <div class="card shadow">
            <div class="card-body p-5">
                <!-- Page heading -->
                <h2 class="card-title text-center mb-4">FoodPrepper Login</h2>
                
                <!-- Login form - submits to /auth/login when user clicks button -->
                <!-- method="POST" means form data is sent securely (not visible in URL) -->
                <form method="POST" action="/auth/login">
                    <!-- CSRF Protection Token -->
                    <!-- This hidden field contains a security code that proves the form came from our site -->
                    <!-- Prevents hackers from tricking users into logging in from fake sites -->
                    <?php 
                    use App\Services\CsrfService; 
                    echo CsrfService::getTokenField(); 
                    ?>
                    
                    <!-- Email input field -->
                    <!-- type="email" validates email format, required means field must be filled -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <!-- Password input field -->
                    <!-- type="password" hides the characters as user types -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <!-- Submit button (w-100 means full width) -->
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>

                <!-- Link to registration page for new users -->
                <div class="text-center mt-3">
                    <p>Don't have an account? <a href="/auth/register">Register here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Save all the HTML we generated into $content variable
$content = ob_get_clean();

// Include the base layout (wraps content in navbar, header, footer)
include __DIR__ . '/../layouts/base.php';
?>
