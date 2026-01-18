<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Character encoding for proper text display -->
    <meta charset="UTF-8">
    <!-- Makes page responsive on mobile devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Page title (shows in browser tab) -->
    <title><?php echo $pageTitle ?? 'FoodPrepper'; ?></title>
    <!-- Link to Bootstrap CSS (provides styling for buttons, cards, navigation, etc.) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Link to custom stylesheet (custom styles for all pages) -->
    <link href="/css/stylesheet.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation bar (only show if user is logged in) -->
    <!-- isset($_SESSION['user_id']) checks if user has logged in -->
    <?php if (isset($_SESSION['user_id'])): ?>
        <!-- Dark navigation bar at top of page -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <!-- Logo/Brand name (links to dashboard) -->
                <a class="navbar-brand" href="/dashboard/index">FoodPrepper</a>
                
                <!-- Hamburger menu button (shows on mobile devices) -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <!-- Navigation links (collapse on mobile) -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <!-- ms-auto means: push these links to right side of navbar -->
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="/weekplanner/index">Weekplanner</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/recipe/index">Recipes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/shoppinglist/index">Shopping List</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/profile/index">Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/auth/logout">Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    <?php endif; ?>

    <!-- Main container for page content -->
    <div class="container mt-4">
        <!-- Success message (green alert) -->
        <!-- Only show if success message exists in session -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <!-- Display success message -->
                <?php echo htmlspecialchars($_SESSION['success']); ?>
                <!-- Close button (X) to dismiss alert -->
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <!-- Remove message from session so it doesn't show again -->
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <!-- Error message (red alert) -->
        <!-- Only show if error message exists in session -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <!-- Display error message -->
                <?php echo htmlspecialchars($_SESSION['error']); ?>
                <!-- Close button (X) to dismiss alert -->
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <!-- Remove message from session so it doesn't show again -->
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Main page content goes here -->
        <!-- Each page sets $content variable which gets displayed here -->
        <?php echo $content ?? ''; ?>
    </div>

    <!-- Bootstrap JavaScript (provides interactivity for dropdowns, modals, etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
