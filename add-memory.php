<?php
// ========================================================
// Digital Memory Jar - Add a Memory Page
// ========================================================

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth_check.php';

$user_id = $currentUser['id'];

$error_message = '';
$success_message = '';

$title = '';
$description = '';
$memory_date = date('Y-m-d');
$category = 'Happy Moment';
$mood = '😊 Happy';

$valid_categories = [
    'Happy Moment',
    'Achievement',
    'Friendship',
    'Family',
    'Travel',
    'Positive Message',
    'Other'
];

$valid_moods = [
    '😊 Happy',
    '🥰 Loved',
    '🌈 Hopeful',
    '🎉 Excited',
    '😌 Peaceful',
    '💖 Grateful'
];

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $memory_date = trim($_POST['memory_date'] ?? '');
    $category    = trim($_POST['category'] ?? 'Happy Moment');
    $mood        = trim($_POST['mood'] ?? '😊 Happy');

    if (empty($title) || empty($description) || empty($memory_date)) {
        $error_message = 'Please fill in all required fields!';
    } elseif (mb_strlen($title) > 150) {
        $error_message = 'Title cannot be longer than 150 characters.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO memories (user_id, title, description, memory_date, category, mood) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $title, $description, $memory_date, $category, $mood]);

        $success_message = 'A little memory has been safely tucked into your jar! ✨';
        $title = '';
        $description = '';
        $memory_date = date('Y-m-d');
    }
}

$pageTitle = "Add a Memory";
require_once __DIR__ . '/includes/header.php';
?>

<div class="add-memory-page-wrapper">
    <div class="page-header-simple text-center">
        <span class="page-badge-icon">🍯✨</span>
        <h1 class="page-title">Tuck a Memory into Your Jar</h1>
        <p class="page-subtitle">Capture a smile, an accomplishment, or a gentle whisper of hope.</p>
    </div>

    <div class="memory-form-card">
        <!-- Success Alert -->
        <?php if (!empty($success_message)): ?>
            <div class="alert-box alert-success bounce-in" role="alert">
                <span class="alert-icon">✨</span>
                <div>
                    <strong>Saved with love!</strong><br>
                    <?php echo e($success_message); ?>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="success-actions-box">
                <a href="memories.php" class="btn btn-secondary btn-pill">
                    <span class="btn-icon">📖</span> View All Memories
                </a>
                <a href="home.php" class="btn btn-outline btn-pill">
                    <span class="btn-icon">🏠</span> Return Home
                </a>
            </div>
        <?php endif; ?>

        <!-- Error Alert -->
        <?php if (!empty($error_message)): ?>
            <div class="alert-box alert-danger" role="alert">
                <span class="alert-icon">⚠️</span>
                <span><?php echo e($error_message); ?></span>
            </div>
        <?php endif; ?>

        <!-- Add Memory Form -->
        <form action="add-memory.php" method="POST" class="memory-form" id="addMemoryForm" novalidate>
            
            <!-- Memory Title -->
            <div class="form-group">
                <label for="mem_title" class="form-label">
                    <span class="label-icon">🏷️</span> Memory Title <span class="required-star">*</span>
                </label>
                <input type="text" 
                       id="mem_title" 
                       name="title" 
                       class="form-control" 
                       placeholder="e.g., Watching the sunset, Passed my exams!" 
                       value="<?php echo e($title); ?>" 
                       maxlength="150"
                       required>
                <div class="field-feedback" id="titleFeedback"></div>
            </div>

            <!-- Memory Category & Date -->
            <div class="form-row">
                <div class="form-group col-half">
                    <label for="mem_category" class="form-label">
                        <span class="label-icon">📂</span> Category <span class="required-star">*</span>
                    </label>
                    <select id="mem_category" name="category" class="form-control form-select" required>
                        <?php foreach ($valid_categories as $cat): ?>
                            <option value="<?php echo e($cat); ?>" <?php echo $category === $cat ? 'selected' : ''; ?>>
                                <?php 
                                    $meta = get_category_meta($cat);
                                    echo $meta['icon'] . ' ' . e($cat); 
                                ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="field-feedback" id="categoryFeedback"></div>
                </div>

                <div class="form-group col-half">
                    <label for="mem_date" class="form-label">
                        <span class="label-icon">📅</span> Memory Date <span class="required-star">*</span>
                    </label>
                    <input type="date" 
                           id="mem_date" 
                           name="memory_date" 
                           class="form-control" 
                           value="<?php echo e($memory_date); ?>" 
                           max="<?php echo date('Y-m-d'); ?>"
                           required>
                    <div class="field-feedback" id="dateFeedback"></div>
                </div>
            </div>

            <!-- Mood Selection Pills -->
            <div class="form-group">
                <label class="form-label">
                    <span class="label-icon">🎭</span> How did this moment make you feel?
                </label>
                <div class="mood-selector-grid">
                    <?php foreach ($valid_moods as $m): ?>
                        <label class="mood-pill-option <?php echo $mood === $m ? 'selected' : ''; ?>">
                            <input type="radio" 
                                   name="mood" 
                                   value="<?php echo e($m); ?>" 
                                   <?php echo $mood === $m ? 'checked' : ''; ?>>
                            <span class="mood-pill-content"><?php echo e($m); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Memory Description -->
            <div class="form-group">
                <label for="mem_desc" class="form-label">
                    <span class="label-icon">📝</span> Memory Story <span class="required-star">*</span>
                </label>
                <textarea id="mem_desc" 
                          name="description" 
                          rows="5" 
                          class="form-control" 
                          placeholder="Write down the little details and feelings you never want to forget..." 
                          required><?php echo e($description); ?></textarea>
                <div class="textarea-helper">
                    <span class="char-counter" id="charCounter">0 characters</span>
                    <span class="field-feedback" id="descFeedback"></span>
                </div>
            </div>

            <!-- Submit Button & Links -->
            <div class="form-submit-row">
                <button type="submit" class="btn btn-primary btn-magic btn-submit-memory">
                    <span class="btn-sparkle-icon">✨</span>
                    <span>Tuck Memory in Jar</span>
                </button>
                <div class="form-alt-links">
                    <a href="home.php" class="btn btn-outline btn-pill">🏠 Return Home</a>
                    <a href="memories.php" class="btn btn-outline btn-pill">📖 View All Memories</a>
                </div>
            </div>

        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
