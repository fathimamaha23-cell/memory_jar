<?php
// ========================================================
// Digital Memory Jar - My Memories Page
// ========================================================

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth_check.php';

$user_id = $currentUser['id'];

// Feedback alerts
$feedback_message = '';
$feedback_type = 'info';

if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'updated') {
        $feedback_message = 'Your memory was beautifully updated! ✨';
        $feedback_type = 'success';
    } elseif ($_GET['msg'] === 'deleted') {
        $feedback_message = 'Memory has been removed from your jar.';
    } elseif ($_GET['msg'] === 'not_found') {
        $feedback_message = 'Memory not found or access denied.';
        $feedback_type = 'danger';
    }
}

// Search, filter, and sort parameters
$search_query    = trim($_GET['q'] ?? '');
$filter_category = trim($_GET['category'] ?? '');
$sort_order      = trim($_GET['sort'] ?? 'newest');

$valid_categories = [
    'Happy Moment',
    'Achievement',
    'Friendship',
    'Family',
    'Travel',
    'Positive Message',
    'Other'
];

// Build SQL Query for user's memories
$sql = "SELECT id, title, description, memory_date, category, mood FROM memories WHERE user_id = ?";
$params = [$user_id];

if (!empty($search_query)) {
    $sql .= " AND (title LIKE ? OR description LIKE ?)";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
}

if (!empty($filter_category) && in_array($filter_category, $valid_categories, true)) {
    $sql .= " AND category = ?";
    $params[] = $filter_category;
}

$sql .= ($sort_order === 'oldest') ? " ORDER BY memory_date ASC, id ASC" : " ORDER BY memory_date DESC, id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$memories = $stmt->fetchAll();

// Total count to distinguish empty jar vs no search matches
$total_stmt = $pdo->prepare("SELECT COUNT(*) FROM memories WHERE user_id = ?");
$total_stmt->execute([$user_id]);
$total_user_memories = (int)$total_stmt->fetchColumn();

$pageTitle = "My Memories";
require_once __DIR__ . '/includes/header.php';
?>

<div class="memories-page-wrapper">
    <!-- Header Section -->
    <div class="page-header-simple text-center">
        <span class="page-badge-icon">📖✨</span>
        <h1 class="page-title">My Collection of Moments</h1>
        <p class="page-subtitle">Browse, reminisce, and treasure the little memories you've tucked away.</p>
    </div>

    <!-- Alert / Feedback Notification -->
    <?php if (!empty($feedback_message)): ?>
        <div class="alert-box alert-<?php echo $feedback_type; ?> bounce-in" role="alert">
            <span class="alert-icon"><?php echo $feedback_type === 'success' ? '✨' : 'ℹ️'; ?></span>
            <span><?php echo e($feedback_message); ?></span>
        </div>
    <?php endif; ?>

    <!-- Controls Toolbar (Search, Filter, Sort) -->
    <?php if ($total_user_memories > 0): ?>
    <section class="memories-toolbar-card">
        <form action="memories.php" method="GET" class="filter-controls-form" id="filterForm">
            
            <!-- Search Input -->
            <div class="toolbar-search-group">
                <span class="search-icon">🔍</span>
                <input type="text" 
                       id="searchInput" 
                       name="q" 
                       class="form-control search-input" 
                       placeholder="Search memories by title or keywords..." 
                       value="<?php echo e($search_query); ?>" 
                       autocomplete="off">
                <?php if (!empty($search_query)): ?>
                    <a href="memories.php<?php echo !empty($filter_category) ? '?category=' . urlencode($filter_category) : ''; ?>" class="clear-search-btn" title="Clear search">&times;</a>
                <?php endif; ?>
            </div>

            <!-- Filters & Sort -->
            <div class="toolbar-selects-group">
                <!-- Category Filter -->
                <div class="toolbar-select-item">
                    <label for="categoryFilter" class="select-label">Category:</label>
                    <select name="category" id="categoryFilter" class="form-control form-select select-pill">
                        <option value="">All Categories</option>
                        <?php foreach ($valid_categories as $cat): ?>
                            <option value="<?php echo e($cat); ?>" <?php echo $filter_category === $cat ? 'selected' : ''; ?>>
                                <?php 
                                    $meta = get_category_meta($cat);
                                    echo $meta['icon'] . ' ' . e($cat); 
                                ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Sort Order -->
                <div class="toolbar-select-item">
                    <label for="sortFilter" class="select-label">Sort by:</label>
                    <select name="sort" id="sortFilter" class="form-control form-select select-pill">
                        <option value="newest" <?php echo $sort_order === 'newest' ? 'selected' : ''; ?>>📅 Newest First</option>
                        <option value="oldest" <?php echo $sort_order === 'oldest' ? 'selected' : ''; ?>>📅 Oldest First</option>
                    </select>
                </div>

                <!-- Reset Button -->
                <?php if (!empty($search_query) || !empty($filter_category) || $sort_order !== 'newest'): ?>
                    <a href="memories.php" class="btn btn-outline btn-pill btn-reset-filters">
                        Reset Filters
                    </a>
                <?php endif; ?>
            </div>

        </form>
    </section>
    <?php endif; ?>

    <!-- Memories Grid / Empty States -->
    <section class="memories-content-area">
        <?php if ($total_user_memories === 0): ?>
            <!-- EMPTY STATE 1: User has 0 memories in database -->
            <div class="empty-state-box">
                <div class="empty-state-jar">🍯</div>
                <h2 class="empty-state-title">Your Jar is Waiting</h2>
                <p class="empty-state-desc">
                    Your jar is waiting for its first little memory. Add one to get started! 💌
                </p>
                <a href="add-memory.php" class="btn btn-primary btn-magic btn-pill">
                    <span class="btn-icon">✨</span> Add Your First Memory
                </a>
            </div>

        <?php elseif (empty($memories)): ?>
            <!-- EMPTY STATE 2: Search or category filter yielded 0 results -->
            <div class="empty-state-box">
                <div class="empty-state-icon">🔍</div>
                <h2 class="empty-state-title">No Memories Found</h2>
                <p class="empty-state-desc">
                    We couldn't find any memories matching your search
                    <?php if (!empty($search_query)): ?>
                        for <strong>"<?php echo e($search_query); ?>"</strong>
                    <?php endif; ?>
                    <?php if (!empty($filter_category)): ?>
                        in <strong><?php echo e($filter_category); ?></strong>
                    <?php endif; ?>.
                </p>
                <div class="empty-state-actions">
                    <a href="memories.php" class="btn btn-secondary btn-pill">
                        <span>Show All Memories</span>
                    </a>
                    <a href="add-memory.php" class="btn btn-outline btn-pill">
                        <span>Add a Memory</span>
                    </a>
                </div>
            </div>

        <?php else: ?>
            <!-- MEMORIES CARDS GRID -->
            <div class="memories-grid" id="memoriesGrid">
                <?php foreach ($memories as $mem): 
                    $cat_meta = get_category_meta($mem['category']);
                    $short_desc = mb_strlen($mem['description']) > 140 
                        ? mb_substr($mem['description'], 0, 140) . '...' 
                        : $mem['description'];
                ?>
                    <article class="memory-card" 
                             id="memory-card-<?php echo (int)$mem['id']; ?>"
                             data-id="<?php echo (int)$mem['id']; ?>"
                             data-title="<?php echo e($mem['title']); ?>"
                             data-category="<?php echo e($mem['category']); ?>"
                             data-date="<?php echo e($mem['memory_date']); ?>"
                             data-mood="<?php echo e($mem['mood'] ?: '😊 Happy'); ?>"
                             data-full-desc="<?php echo e($mem['description']); ?>">
                        
                        <div class="card-top-bar">
                            <span class="memory-cat-pill" style="--cat-color: <?php echo $cat_meta['color']; ?>;">
                                <span class="cat-icon"><?php echo $cat_meta['icon']; ?></span>
                                <span><?php echo e($mem['category']); ?></span>
                            </span>
                            <span class="memory-mood-pill" title="Mood: <?php echo e($mem['mood'] ?: 'Happy'); ?>">
                                <?php echo e($mem['mood'] ?: '😊'); ?>
                            </span>
                        </div>

                        <div class="card-body">
                            <h3 class="memory-card-title"><?php echo e($mem['title']); ?></h3>
                            <div class="memory-card-date">
                                <span class="date-icon">📅</span>
                                <time datetime="<?php echo e($mem['memory_date']); ?>"><?php echo format_memory_date($mem['memory_date']); ?></time>
                            </div>
                            <p class="memory-card-snippet"><?php echo nl2br(e($short_desc)); ?></p>
                        </div>

                        <div class="card-footer">
                            <button type="button" class="btn-card-action btn-view-memory" data-id="<?php echo (int)$mem['id']; ?>">
                                <span>📖 Read Note</span>
                            </button>
                            
                            <div class="card-btn-group">
                                <a href="edit-memory.php?id=<?php echo (int)$mem['id']; ?>" class="btn-icon-action btn-edit" title="Edit this memory">
                                    ✏️
                                </a>
                                <button type="button" 
                                        class="btn-icon-action btn-delete" 
                                        data-id="<?php echo (int)$mem['id']; ?>" 
                                        data-title="<?php echo e($mem['title']); ?>"
                                        title="Delete this memory">
                                    🗑️
                                </button>
                            </div>
                        </div>

                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>

<!-- ========================================================
     MODAL: VIEW FULL MEMORY DETAILS
     ======================================================== -->
<div class="modal-overlay" id="viewDetailModalOverlay" aria-hidden="true">
    <div class="modal-dialog memory-detail-modal" role="dialog" aria-modal="true" aria-labelledby="detailTitle">
        <button type="button" class="modal-btn-close" id="btnCloseDetailModal" aria-label="Close details">&times;</button>
        
        <div class="modal-content-card detail-modal-card">
            <div class="detail-header">
                <div class="badge-group">
                    <span class="memory-category-badge" id="detailCategoryBadge">
                        <span id="detailCategoryIcon">🌸</span>
                        <span id="detailCategoryText">Category</span>
                    </span>
                    <span class="memory-mood-badge" id="detailMoodBadge">😊 Happy</span>
                </div>
                <span class="memory-date-stamp" id="detailDate">October 18, 2026</span>
            </div>

            <div class="detail-story-content">
                <div class="card-pin">📌</div>
                <h2 class="detail-title" id="detailTitle">Memory Title</h2>
                <div class="detail-desc-scroll">
                    <p class="detail-description" id="detailDescription"></p>
                </div>
            </div>

            <div class="detail-modal-footer">
                <a href="#" id="detailBtnEdit" class="btn btn-secondary btn-pill">
                    <span>✏️ Edit Memory</span>
                </a>
                <button type="button" class="btn btn-outline btn-pill" id="detailBtnClose">
                    Close Note
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================
     MODAL: SWEET DELETE CONFIRMATION
     ======================================================== -->
<div class="modal-overlay" id="deleteConfirmModalOverlay" aria-hidden="true">
    <div class="modal-dialog delete-confirm-modal" role="dialog" aria-modal="true">
        <div class="modal-content-card text-center">
            <div class="delete-icon-alert">🥺🗑️</div>
            <h3 class="delete-modal-title">Remove from Jar?</h3>
            <p class="delete-modal-message">
                Are you sure you want to gently remove <strong id="deleteTargetTitle">this memory</strong> from your jar?
                This action cannot be undone.
            </p>

            <form action="delete-memory.php" method="POST" id="deleteConfirmForm">
                <input type="hidden" name="id" id="deleteInputId" value="0">
                
                <div class="modal-btn-actions">
                    <button type="submit" class="btn btn-danger btn-pill" id="btnConfirmDelete">
                        Yes, Remove Note
                    </button>
                    <button type="button" class="btn btn-outline btn-pill" id="btnCancelDelete">
                        Keep It in Jar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
