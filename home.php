<?php
// ========================================================
// Digital Memory Jar - Home Page
// ========================================================

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth_check.php';

// Fetch current user's memory count
$user_id = $currentUser['id'];
$count_stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM memories WHERE user_id = :user_id");
$count_stmt->execute([':user_id' => $user_id]);
$memory_count = (int)$count_stmt->fetchColumn();

// Fetch up to 8 recent memory categories/moods for realistic dynamic jar notes
$preview_stmt = $pdo->prepare("SELECT title, category, mood FROM memories WHERE user_id = :user_id ORDER BY id DESC LIMIT 8");
$preview_stmt->execute([':user_id' => $user_id]);
$preview_notes = $preview_stmt->fetchAll();

$pageTitle = "Home";
require_once __DIR__ . '/includes/header.php';
?>

<div class="home-page-container">
    <!-- Hero Header & Greeting -->
    <section class="home-hero-section text-center">
        <div class="greeting-badge">
            <span class="badge-sparkle">✨</span>
            <span>Welcome, <strong><?php echo e($currentUser['name']); ?></strong>!</span>
            <span class="badge-sparkle">🌸</span>
        </div>

        <h1 class="main-title">Digital Memory Jar</h1>
        <p class="main-tagline">"Little moments, forever memories."</p>

        <!-- Dynamic Counter Card -->
        <div class="memory-counter-wrapper">
            <div class="counter-bubble">
                <span class="counter-number" id="jarMemoryCount"><?php echo $memory_count; ?></span>
                <span class="counter-label"><?php echo $memory_count === 1 ? 'precious memory tucked inside' : 'precious memories tucked inside'; ?></span>
            </div>
        </div>
    </section>

    <!-- Main Visual Jar Section -->
    <section class="jar-showcase-section">
        <!-- Floating Ambient Elements -->
        <div class="ambient-decorations" aria-hidden="true">
            <span class="float-item deco-1">🌟</span>
            <span class="float-item deco-2">💌</span>
            <span class="float-item deco-3">🕊️</span>
            <span class="float-item deco-4">🌸</span>
            <span class="float-item deco-5">✨</span>
        </div>

        <!-- The CSS Glass Memory Jar -->
        <div class="glass-jar-wrapper">
            <div class="glass-jar" id="memoryJar" role="img" aria-label="A glass memory jar filled with colorful origami notes">
                <!-- Jar Cork / Wooden Lid -->
                <div class="jar-lid">
                    <div class="lid-knob"></div>
                    <div class="lid-cork"></div>
                    <div class="lid-ring"></div>
                </div>

                <!-- Jar Glass Neck & Tie Ribbon -->
                <div class="jar-neck">
                    <div class="jar-ribbon">
                        <div class="ribbon-band"></div>
                        <div class="ribbon-tag">
                            <span>Moments</span>
                        </div>
                    </div>
                </div>

                <!-- Jar Glass Body -->
                <div class="jar-body">
                    <!-- Glass Specular Reflection Highlights -->
                    <div class="jar-shine shine-left"></div>
                    <div class="jar-shine shine-right"></div>
                    <div class="jar-inner-glow"></div>

                    <!-- Jar Paper Notes Container -->
                    <div class="jar-notes-container" id="jarNotesInner">
                        <?php if ($memory_count === 0): ?>
                            <!-- Empty Jar Prompt -->
                            <div class="empty-jar-note">
                                <span class="empty-icon">💌</span>
                                <p>Your jar is quiet and waiting.<br>Drop your very first memory!</p>
                            </div>
                        <?php else: ?>
                            <!-- Render folded paper notes inside jar -->
                            <?php 
                            $note_colors = ['#F4C7D9', '#FFE8A3', '#C8B6E2', '#C8D8C0', '#FAD4C0', '#D8E2DC', '#FFD6E0'];
                            $note_rotations = ['-12deg', '8deg', '-5deg', '15deg', '-18deg', '6deg', '-9deg', '12deg'];
                            foreach ($preview_notes as $idx => $note): 
                                $bg = $note_colors[$idx % count($note_colors)];
                                $rot = $note_rotations[$idx % count($note_rotations)];
                                $category_meta = get_category_meta($note['category']);
                            ?>
                                <div class="paper-note note-item-<?php echo $idx + 1; ?>" 
                                     style="--note-bg: <?php echo $bg; ?>; --note-rot: <?php echo $rot; ?>;"
                                     title="<?php echo e($note['title']); ?>">
                                    <span class="note-icon"><?php echo $category_meta['icon']; ?></span>
                                    <span class="note-fold"></span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Gentle sparkles inside jar -->
                    <div class="jar-fireflies" aria-hidden="true">
                        <span class="firefly ff-1"></span>
                        <span class="firefly ff-2"></span>
                        <span class="firefly ff-3"></span>
                    </div>
                </div>

                <!-- Jar Base Shadow -->
                <div class="jar-shadow"></div>
            </div>
        </div>

        <!-- Jar Interactive Action Buttons -->
        <div class="jar-action-buttons">
            <button type="button" class="btn btn-magic btn-open-jar" id="btnOpenJar">
                <span class="btn-sparkle-icon">✨</span>
                <span class="btn-text">Open My Jar</span>
                <span class="btn-sparkle-icon">✨</span>
            </button>

            <div class="secondary-actions">
                <a href="add-memory.php" class="btn btn-outline btn-pill">
                    <span class="btn-icon">➕</span> Add a Memory
                </a>
                <a href="memories.php" class="btn btn-outline btn-pill">
                    <span class="btn-icon">📖</span> My Memories
                </a>
            </div>
        </div>
    </section>

    <!-- Warm Welcoming Quote & Message Section -->
    <section class="home-message-card">
        <div class="message-card-inner">
            <div class="message-card-icon">💌</div>
            <div class="message-card-text">
                <h3>A Safe Haven For Your Thoughts</h3>
                <p>
                    Life moves swiftly, but the smiles, breakthroughs, peaceful sunrises, and loving words deserve a home that never forgets. 
                    Whenever you need a dose of warmth, gentle encouragement, or nostalgic joy, simply open your jar and let your memories greet you like an old friend.
                </p>
            </div>
        </div>
    </section>
</div>

<!-- ========================================================
     OPEN MY JAR POPUP / MODAL (MAIN CREATIVE FEATURE)
     ======================================================== -->
<div class="modal-overlay" id="jarModalOverlay" aria-hidden="true">
    <div class="modal-dialog memory-reveal-modal" role="dialog" aria-modal="true" aria-labelledby="modalMemoryTitle">
        
        <!-- Animated Particles / Floating Notes when Jar Opens -->
        <div class="modal-particles" id="modalParticles" aria-hidden="true"></div>

        <button type="button" class="modal-btn-close" id="btnCloseModal" aria-label="Close memory modal">&times;</button>

        <div class="modal-content-card">
            
            <!-- Loading State -->
            <div class="modal-loading-state" id="modalLoading">
                <div class="spinner-jar">🍯</div>
                <p class="loading-text">Peeking into your jar of memories...</p>
            </div>

            <!-- Empty State (When user has 0 memories) -->
            <div class="modal-empty-state" id="modalEmptyState" style="display: none;">
                <div class="empty-icon-lg">💌</div>
                <h3 class="empty-title">Your Jar is Waiting</h3>
                <p class="empty-message">
                    Your jar is waiting for its first little memory. Add one to get started! 💌
                </p>
                <div class="empty-actions">
                    <a href="add-memory.php" class="btn btn-primary btn-pill">
                        <span>✨ Add a Memory</span>
                    </a>
                    <button type="button" class="btn btn-outline btn-pill btn-close-modal-alt">
                        Close
                    </button>
                </div>
            </div>

            <!-- Memory Reveal Card (When memory is fetched) -->
            <div class="modal-memory-body" id="modalMemoryBody" style="display: none;">
                <div class="memory-reveal-header">
                    <div class="badge-group">
                        <span class="memory-category-badge" id="modalCategoryBadge">
                            <span id="modalCategoryIcon">🌸</span>
                            <span id="modalCategoryText">Category</span>
                        </span>
                        <span class="memory-mood-badge" id="modalMoodBadge">😊 Happy</span>
                    </div>
                    <span class="memory-date-stamp" id="modalMemoryDate">October 18, 2026</span>
                </div>

                <div class="memory-reveal-content">
                    <div class="card-pin">📌</div>
                    <h2 class="memory-reveal-title" id="modalMemoryTitle">Memory Title</h2>
                    <div class="memory-reveal-desc-scroll">
                        <p class="memory-reveal-desc" id="modalMemoryDesc">
                            Full memory description will appear here...
                        </p>
                    </div>
                </div>

                <!-- Modal Action Buttons -->
                <div class="modal-action-footer">
                    <button type="button" class="btn btn-secondary btn-pill" id="btnPickAnother">
                        <span class="btn-icon">🎲</span> Pick Another Memory
                    </button>
                    <button type="button" class="btn btn-outline btn-pill" id="btnCloseModalBottom">
                        <span>Close Jar</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
