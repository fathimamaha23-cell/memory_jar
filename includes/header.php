<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$is_logged_in = isset($_SESSION['user_id']);
$user_display_name = $is_logged_in ? htmlspecialchars($_SESSION['user_name'] ?? 'Friend') : '';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' — Digital Memory Jar' : 'Digital Memory Jar — Little Moments, Forever Memories'; ?></title>
    
    <!-- Google Fonts: Nunito & Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Favicon using SVG Data URI -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🍯</text></svg>">
</head>
<body>
    <!-- Background Sparkles Container -->
    <div class="dreamy-bg-sparkles" aria-hidden="true" id="bgSparkles"></div>

    <!-- Navigation Header -->
    <header class="main-header">
        <div class="header-container">
            <a href="<?php echo $is_logged_in ? 'home.php' : 'index.php'; ?>" class="brand-logo">
                <span class="logo-jar">🍯</span>
                <span class="logo-text">
                    <strong>Digital Memory Jar</strong>
                    <small>Little moments, forever memories</small>
                </span>
            </a>

            <?php if ($is_logged_in): ?>
            <!-- Mobile Menu Toggle Button -->
            <button class="mobile-toggle-btn" id="mobileMenuBtn" aria-label="Toggle Navigation">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <!-- Navigation Links -->
            <nav class="main-nav" id="mainNav">
                <ul class="nav-links">
                    <li>
                        <a href="home.php" class="<?php echo $current_page === 'home.php' ? 'active' : ''; ?>">
                            <span class="nav-icon">🏠</span> Home
                        </a>
                    </li>
                    <li>
                        <a href="add-memory.php" class="<?php echo $current_page === 'add-memory.php' ? 'active' : ''; ?>">
                            <span class="nav-icon">✨</span> Add a Memory
                        </a>
                    </li>
                    <li>
                        <a href="memories.php" class="<?php echo ($current_page === 'memories.php' || $current_page === 'edit-memory.php') ? 'active' : ''; ?>">
                            <span class="nav-icon">📖</span> My Memories
                        </a>
                    </li>
                </ul>

                <div class="user-action-group">
                    <span class="user-greeting-pill" title="Signed in as <?php echo $user_display_name; ?>">
                        <span class="user-avatar-emoji">🌸</span>
                        <span class="user-name-text"><?php echo $user_display_name; ?></span>
                    </span>
                    <a href="logout.php" class="btn-logout" title="Sign out of your jar">
                        <span class="nav-icon">🚪</span> Logout
                    </a>
                </div>
            </nav>
            <?php else: ?>
            <div class="header-auth-badge">
                <span class="auth-quote">Save your happy moments ✨</span>
            </div>
            <?php endif; ?>
        </div>
    </header>

    <main class="page-main-content">
