<?php
// ========================================================
// Digital Memory Jar - Edit Memory Page
// ========================================================

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth_check.php';

$user_id = $currentUser['id'];
$memory_id = (int)($_GET['id'] ?? 0);

if ($memory_id <= 0) {
    header("Location: memories.php");
    exit;
}

// Fetch memory and verify it belongs to the logged-in user
$stmt = $pdo->prepare("SELECT * FROM memories WHERE id = ? AND user_id = ?");
$stmt->execute([$memory_id, $user_id]);
$memory = $stmt->fetch();

if (!$memory) {
    header("Location: memories.php?msg=not_found");
    exit;
}

$error_message = '';
$title       = $memory['title'];
$description = $memory['description'];
$memory_date = $memory['memory_date'];
$category    = $memory['category'];
$mood        = $memory['mood'] ?: '😊 Happy';

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
        $error_message = 'Please fill in all required fields.';
    } elseif (mb_strlen($title) > 150) {
        $error_message = 'Title cannot be longer than 150 characters.';
    } else {
        $update_stmt = $pdo->prepare("UPDATE memories SET title = ?, description = ?, memory_date = ?, category = ?, mood = ? WHERE id = ? AND user_id = ?");
        $update_stmt->execute([$title, $description, $memory_date, $category, $mood, $memory_id, $user_id]);

        header("Location: memories.php?msg=updated");
        exit;
    }
}

$pageTitle = "Edit Memory";
require_once __DIR__ . '/includes/header.php';
?>

<div class="add-memory-page-wrapper">
    <div class="page-header-simple text-center">
        <span class="page-badge-icon">✏️✨</span>
        <h1 class="page-title">Edit Your Memory</h1>
        <p class="page-subtitle">Polish your note before gently slipping it back into the jar.</p>
    </div>

    <div class="memory-form-card">
        <!-- Error Alert -->
        <?php if (!empty($error_message)): ?>
            <div class="alert-box alert-danger" role="alert">
                <span class="alert-icon">⚠️</span>
                <span><?php echo e($error_message); ?></span>
            </div>
        <?php endif; ?>

        <!-- Edit Form -->
        <form action="edit-memory.php?id=<?php echo (int)$memory_id; ?>" method="POST" class="memory-form" id="editMemoryForm" novalidate>
            
            <!-- Memory Title -->
            <div class="form-group">
                <label for="mem_title" class="form-label">
                    <span class="label-icon">🏷️</span> Memory Title <span class="required-star">*</span>
                </label>
                <input type="text" 
                       id="mem_title" 
                       name="title" 
                       class="form-control" 
                       value="<?php echo e($title); ?>" 
                       maxlength="150"
                       required>
                <div class="field-feedback" id="titleFeedback"></div>
            </div>

            <!-- Category & Date -->
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
                          required><?php echo e($description); ?></textarea>
                <div class="textarea-helper">
                    <span class="char-counter" id="charCounter"><?php echo mb_strlen($description); ?> characters</span>
                    <span class="field-feedback" id="descFeedback"></span>
                </div>
            </div>

            <!-- Submit Button & Links -->
            <div class="form-submit-row">
                <button type="submit" class="btn btn-primary btn-magic">
                    <span>💾 Save Changes</span>
                </button>
                <div class="form-alt-links">
                    <a href="memories.php" class="btn btn-outline btn-pill">Cancel &amp; Return</a>
                </div>
            </div>

        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
