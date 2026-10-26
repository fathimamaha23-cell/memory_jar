<?php
// ========================================================
// Digital Memory Jar - Login & Registration Page
// ========================================================

require_once __DIR__ . '/config/db.php';

// If already logged in, redirect to home page
if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit;
}

$login_error = '';
$register_error = '';
$success_message = '';
$active_tab = 'login';

// Check for feedback messages in URL
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'logged_out') {
        $success_message = 'You have safely closed your memory jar. See you soon! 🌸';
    } elseif ($_GET['msg'] === 'auth_required') {
        $login_error = 'Please log in to peek inside your memory jar! 🍯';
    } elseif ($_GET['msg'] === 'registered') {
        $success_message = 'Welcome! Your jar has been created. Please log in below ✨';
    }
}

// Variables to keep inputs if an error occurs
$reg_name = '';
$reg_email = '';
$login_email = '';

// Handle Registration Form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'register') {
    $active_tab = 'register';
    $reg_name  = trim($_POST['name'] ?? '');
    $reg_email = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';

    if (empty($reg_name) || empty($reg_email) || empty($password) || empty($confirm)) {
        $register_error = 'Please fill in all fields.';
    } elseif (!filter_var($reg_email, FILTER_VALIDATE_EMAIL)) {
        $register_error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $register_error = 'Password should be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $register_error = 'Passwords do not match.';
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$reg_email]);

        if ($stmt->fetch()) {
            $register_error = 'An account with this email already exists.';
        } else {
            // Save new user with hashed password
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$reg_name, $reg_email, $hashed]);

            header("Location: index.php?msg=registered");
            exit;
        }
    }
}

// Handle Login Form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'login') {
    $active_tab = 'login';
    $login_email = trim($_POST['email'] ?? '');
    $password    = $_POST['password'] ?? '';

    if (empty($login_email) || empty($password)) {
        $login_error = 'Please enter both your email and password.';
    } else {
        $stmt = $pdo->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
        $stmt->execute([$login_email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Store user info in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];

            header("Location: home.php");
            exit;
        } else {
            $login_error = 'Invalid email or password. Please try again.';
        }
    }
}

$pageTitle = "Welcome";
require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-page-wrapper">
    <div class="auth-hero-deco">
        <span class="floating-deco star-1">✨</span>
        <span class="floating-deco heart-1">💖</span>
        <span class="floating-deco note-1">💌</span>
        <span class="floating-deco star-2">🌟</span>
        <span class="floating-deco flower-1">🌸</span>
    </div>

    <div class="auth-card-container">
        <!-- Brand Header -->
        <div class="auth-card-header">
            <div class="auth-jar-icon">🍯</div>
            <h1 class="auth-title">Digital Memory Jar</h1>
            <p class="auth-subtitle">"Little moments, forever memories."</p>
        </div>

        <!-- Alert Messages -->
        <?php if (!empty($success_message)): ?>
            <div class="alert-box alert-success" role="alert">
                <span class="alert-icon">✨</span>
                <span><?php echo e($success_message); ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($login_error) && $active_tab === 'login'): ?>
            <div class="alert-box alert-danger" role="alert">
                <span class="alert-icon">⚠️</span>
                <span><?php echo e($login_error); ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($register_error) && $active_tab === 'register'): ?>
            <div class="alert-box alert-danger" role="alert">
                <span class="alert-icon">⚠️</span>
                <span><?php echo e($register_error); ?></span>
            </div>
        <?php endif; ?>

        <!-- Tabs Switcher -->
        <div class="auth-tabs" role="tablist">
            <button type="button" 
                    class="auth-tab-btn <?php echo $active_tab === 'login' ? 'active' : ''; ?>" 
                    id="tabBtnLogin" 
                    data-tab="login"
                    role="tab">
                Sign In
            </button>
            <button type="button" 
                    class="auth-tab-btn <?php echo $active_tab === 'register' ? 'active' : ''; ?>" 
                    id="tabBtnRegister" 
                    data-tab="register"
                    role="tab">
                Create Jar
            </button>
        </div>

        <!-- LOGIN FORM -->
        <div class="auth-tab-pane <?php echo $active_tab === 'login' ? 'active' : ''; ?>" id="loginPane" role="tabpanel">
            <form action="index.php" method="POST" class="auth-form" id="loginForm" novalidate>
                <input type="hidden" name="action" value="login">

                <div class="form-group">
                    <label for="login_email" class="form-label">
                        <span class="label-icon">✉️</span> Email Address
                    </label>
                    <input type="email" 
                           id="login_email" 
                           name="email" 
                           class="form-control" 
                           placeholder="Enter your email" 
                           value="<?php echo e($login_email); ?>" 
                           required 
                           autocomplete="email">
                    <div class="field-feedback" id="login_email_feedback"></div>
                </div>

                <div class="form-group">
                    <label for="login_password" class="form-label">
                        <span class="label-icon">🔒</span> Password
                    </label>
                    <div class="password-input-wrapper">
                        <input type="password" 
                               id="login_password" 
                               name="password" 
                               class="form-control" 
                               placeholder="Enter your password" 
                               required 
                               autocomplete="current-password">
                        <button type="button" class="btn-toggle-pw" aria-label="Toggle password visibility">👁️</button>
                    </div>
                    <div class="field-feedback" id="login_password_feedback"></div>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-magic">
                    <span>Open My Memory Jar ✨</span>
                </button>
            </form>
        </div>

        <!-- REGISTRATION FORM -->
        <div class="auth-tab-pane <?php echo $active_tab === 'register' ? 'active' : ''; ?>" id="registerPane" role="tabpanel">
            <form action="index.php" method="POST" class="auth-form" id="registerForm" novalidate>
                <input type="hidden" name="action" value="register">

                <div class="form-group">
                    <label for="reg_name" class="form-label">
                        <span class="label-icon">🌸</span> Full Name
                    </label>
                    <input type="text" 
                           id="reg_name" 
                           name="name" 
                           class="form-control" 
                           placeholder="What should your jar call you?" 
                           value="<?php echo e($reg_name); ?>" 
                           required 
                           autocomplete="name">
                    <div class="field-feedback" id="reg_name_feedback"></div>
                </div>

                <div class="form-group">
                    <label for="reg_email" class="form-label">
                        <span class="label-icon">✉️</span> Email Address
                    </label>
                    <input type="email" 
                           id="reg_email" 
                           name="email" 
                           class="form-control" 
                           placeholder="Enter your email" 
                           value="<?php echo e($reg_email); ?>" 
                           required 
                           autocomplete="email">
                    <div class="field-feedback" id="reg_email_feedback"></div>
                </div>

                <div class="form-row">
                    <div class="form-group col-half">
                        <label for="reg_password" class="form-label">
                            <span class="label-icon">🔒</span> Password
                        </label>
                        <div class="password-input-wrapper">
                            <input type="password" 
                                   id="reg_password" 
                                   name="password" 
                                   class="form-control" 
                                   placeholder="At least 6 chars" 
                                   required 
                                   autocomplete="new-password">
                            <button type="button" class="btn-toggle-pw" aria-label="Toggle password visibility">👁️</button>
                        </div>
                        <div class="field-feedback" id="reg_password_feedback"></div>
                    </div>

                    <div class="form-group col-half">
                        <label for="reg_confirm" class="form-label">
                            <span class="label-icon">🛡️</span> Confirm Password
                        </label>
                        <div class="password-input-wrapper">
                            <input type="password" 
                                   id="reg_confirm" 
                                   name="confirm_password" 
                                   class="form-control" 
                                   placeholder="Re-type password" 
                                   required 
                                   autocomplete="new-password">
                            <button type="button" class="btn-toggle-pw" aria-label="Toggle password visibility">👁️</button>
                        </div>
                        <div class="field-feedback" id="reg_confirm_feedback"></div>
                    </div>
                </div>

                <button type="submit" class="btn btn-secondary btn-block btn-magic">
                    <span>Create My Personal Jar 🍯</span>
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
