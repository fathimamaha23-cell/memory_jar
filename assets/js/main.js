/**
 * ========================================================
 * Digital Memory Jar - Main Vanilla JavaScript
 * Interactions, Animations, Modals, and Validations
 * ========================================================
 */

document.addEventListener('DOMContentLoaded', () => {

    /* ========================================================
       1. AMBIENT BACKGROUND SPARKLES
       ======================================================== */
    const bgContainer = document.getElementById('bgSparkles');
    if (bgContainer) {
        const sparkleChars = ['✨', '⭐', '🌸', '💫'];
        const count = 18;
        for (let i = 0; i < count; i++) {
            const span = document.createElement('span');
            span.className = 'dreamy-sparkle';
            span.textContent = sparkleChars[Math.floor(Math.random() * sparkleChars.length)];
            span.style.top = `${Math.random() * 95}%`;
            span.style.left = `${Math.random() * 95}%`;
            span.style.fontSize = `${10 + Math.random() * 12}px`;
            span.style.animationDelay = `${Math.random() * 5}s`;
            span.style.animationDuration = `${3 + Math.random() * 4}s`;
            bgContainer.appendChild(span);
        }
    }

    /* ========================================================
       2. MOBILE MENU TOGGLE
       ======================================================== */
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mainNav = document.getElementById('mainNav');
    if (mobileMenuBtn && mainNav) {
        mobileMenuBtn.addEventListener('click', () => {
            mainNav.classList.toggle('nav-open');
        });

        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!mainNav.contains(e.target) && !mobileMenuBtn.contains(e.target) && mainNav.classList.contains('nav-open')) {
                mainNav.classList.remove('nav-open');
            }
        });
    }

    /* ========================================================
       3. AUTH TABS & VALIDATION (index.php)
       ======================================================== */
    const tabBtnLogin = document.getElementById('tabBtnLogin');
    const tabBtnRegister = document.getElementById('tabBtnRegister');
    const loginPane = document.getElementById('loginPane');
    const registerPane = document.getElementById('registerPane');

    if (tabBtnLogin && tabBtnRegister) {
        tabBtnLogin.addEventListener('click', () => {
            tabBtnLogin.classList.add('active');
            tabBtnRegister.classList.remove('active');
            loginPane.classList.add('active');
            registerPane.classList.remove('active');
        });

        tabBtnRegister.addEventListener('click', () => {
            tabBtnRegister.classList.add('active');
            tabBtnLogin.classList.remove('active');
            registerPane.classList.add('active');
            loginPane.classList.remove('active');
        });
    }

    // Toggle Password Visibility
    const pwToggleBtns = document.querySelectorAll('.btn-toggle-pw');
    pwToggleBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.previousElementSibling;
            if (input && input.tagName === 'INPUT') {
                if (input.type === 'password') {
                    input.type = 'text';
                    btn.textContent = '🙈';
                } else {
                    input.type = 'password';
                    btn.textContent = '👁️';
                }
            }
        });
    });

    // Client-side Registration Validation
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        const regName = document.getElementById('reg_name');
        const regEmail = document.getElementById('reg_email');
        const regPass = document.getElementById('reg_password');
        const regConfirm = document.getElementById('reg_confirm');

        registerForm.addEventListener('submit', (e) => {
            let isValid = true;

            // Clear feedbacks
            document.querySelectorAll('#registerPane .field-feedback').forEach(el => el.textContent = '');

            if (!regName.value.trim() || regName.value.trim().length < 2) {
                document.getElementById('reg_name_feedback').textContent = 'Please enter your name (at least 2 letters).';
                isValid = false;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!regEmail.value.trim() || !emailRegex.test(regEmail.value.trim())) {
                document.getElementById('reg_email_feedback').textContent = 'Please enter a valid email address.';
                isValid = false;
            }

            if (!regPass.value || regPass.value.length < 6) {
                document.getElementById('reg_password_feedback').textContent = 'Password must be at least 6 characters.';
                isValid = false;
            }

            if (regPass.value !== regConfirm.value) {
                document.getElementById('reg_confirm_feedback').textContent = 'Passwords do not match.';
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    }

    /* ========================================================
       4. ADD / EDIT MEMORY FORM INTERACTIONS
       ======================================================== */
    // Textarea Character Counter
    const descTextarea = document.getElementById('mem_desc');
    const charCounter = document.getElementById('charCounter');
    if (descTextarea && charCounter) {
        const updateCount = () => {
            const len = descTextarea.value.length;
            charCounter.textContent = `${len} character${len === 1 ? '' : 's'}`;
        };
        descTextarea.addEventListener('input', updateCount);
        updateCount();
    }

    // Mood Selector Radio Pills
    const moodPills = document.querySelectorAll('.mood-pill-option');
    moodPills.forEach(pill => {
        pill.addEventListener('click', () => {
            moodPills.forEach(p => p.classList.remove('selected'));
            pill.classList.add('selected');
            const radio = pill.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
        });
    });

    // Form Client-Side Validation for Memory Form
    const memoryForm = document.getElementById('addMemoryForm') || document.getElementById('editMemoryForm');
    if (memoryForm) {
        memoryForm.addEventListener('submit', (e) => {
            let isValid = true;
            const titleInput = document.getElementById('mem_title');
            const dateInput = document.getElementById('mem_date');
            const descInput = document.getElementById('mem_desc');

            const titleFeedback = document.getElementById('titleFeedback');
            const dateFeedback = document.getElementById('dateFeedback');
            const descFeedback = document.getElementById('descFeedback');

            if (titleFeedback) titleFeedback.textContent = '';
            if (dateFeedback) dateFeedback.textContent = '';
            if (descFeedback) descFeedback.textContent = '';

            if (!titleInput.value.trim()) {
                if (titleFeedback) titleFeedback.textContent = 'Please give your memory a title.';
                titleInput.focus();
                isValid = false;
            }

            if (!dateInput.value) {
                if (dateFeedback) dateFeedback.textContent = 'Please pick a date.';
                isValid = false;
            }

            if (!descInput.value.trim()) {
                if (descFeedback) descFeedback.textContent = 'Please write a brief story for this memory.';
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    }

    /* ========================================================
       5. MAIN CREATIVE FEATURE: "OPEN MY JAR" MODAL (home.php)
       ======================================================== */
    const btnOpenJar = document.getElementById('btnOpenJar');
    const memoryJar = document.getElementById('memoryJar');
    const jarModalOverlay = document.getElementById('jarModalOverlay');
    const btnCloseModal = document.getElementById('btnCloseModal');
    const btnCloseModalBottom = document.getElementById('btnCloseModalBottom');
    const btnPickAnother = document.getElementById('btnPickAnother');
    const modalLoading = document.getElementById('modalLoading');
    const modalEmptyState = document.getElementById('modalEmptyState');
    const modalMemoryBody = document.getElementById('modalMemoryBody');
    const modalParticles = document.getElementById('modalParticles');

    let currentMemoryId = 0;

    const createBurstParticles = () => {
        if (!modalParticles) return;
        modalParticles.innerHTML = '';
        const items = ['✨', '⭐', '🌸', '💌', '💖', '🎉', '🌟'];
        for (let i = 0; i < 16; i++) {
            const p = document.createElement('span');
            p.className = 'pop-particle';
            p.textContent = items[Math.floor(Math.random() * items.length)];
            
            // Random angle and distance
            const angle = Math.random() * Math.PI * 2;
            const distance = 80 + Math.random() * 140;
            const tx = Math.cos(angle) * distance;
            const ty = Math.sin(angle) * distance;

            p.style.setProperty('--tx', `${tx}px`);
            p.style.setProperty('--ty', `${ty}px`);
            p.style.top = '40%';
            p.style.left = '50%';
            modalParticles.appendChild(p);
        }
    };

    const fetchRandomMemory = async (excludeId = 0) => {
        if (!modalLoading || !modalEmptyState || !modalMemoryBody) return;

        // Show loading state
        modalLoading.style.display = 'block';
        modalEmptyState.style.display = 'none';
        modalMemoryBody.style.display = 'none';

        try {
            const url = `api/get-random-memory.php${excludeId > 0 ? `?exclude=${excludeId}` : ''}`;
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();

            // Simulate tiny dreamy delay for smooth visual transition
            await new Promise(r => setTimeout(r, 400));

            modalLoading.style.display = 'none';

            if (!data.success || data.total_count === 0 || !data.memory) {
                // Empty jar state
                modalEmptyState.style.display = 'block';
                modalMemoryBody.style.display = 'none';
                return;
            }

            // Populate Memory
            const mem = data.memory;
            currentMemoryId = mem.id;

            const modalCatBadge = document.getElementById('modalCategoryBadge');
            const modalCatIcon = document.getElementById('modalCategoryIcon');
            const modalCatText = document.getElementById('modalCategoryText');
            const modalMoodBadge = document.getElementById('modalMoodBadge');
            const modalDate = document.getElementById('modalMemoryDate');
            const modalTitle = document.getElementById('modalMemoryTitle');
            const modalDesc = document.getElementById('modalMemoryDesc');

            if (modalCatIcon) modalCatIcon.textContent = mem.category_icon || '✨';
            if (modalCatText) modalCatText.textContent = mem.category || 'Memory';
            if (modalCatBadge && mem.category_color) {
                modalCatBadge.style.backgroundColor = mem.category_color;
            }
            if (modalMoodBadge) modalMoodBadge.textContent = mem.mood || '😊 Happy';
            if (modalDate) modalDate.textContent = mem.formatted_date || mem.memory_date;
            if (modalTitle) modalTitle.textContent = mem.title;
            if (modalDesc) modalDesc.textContent = mem.description;

            modalMemoryBody.style.display = 'block';
            createBurstParticles();

        } catch (err) {
            console.error('Failed to fetch memory:', err);
            modalLoading.style.display = 'none';
            modalEmptyState.style.display = 'block';
            const emptyMsg = modalEmptyState.querySelector('.empty-message');
            if (emptyMsg) {
                emptyMsg.textContent = 'Could not reach your jar right now. Please ensure MySQL is running in XAMPP!';
            }
        }
    };

    if (btnOpenJar && memoryJar && jarModalOverlay) {
        // Handle Open Jar Button Click
        btnOpenJar.addEventListener('click', () => {
            // 1. Gentle shaking animation on the jar
            memoryJar.classList.add('jar-shaking');
            
            setTimeout(() => {
                memoryJar.classList.remove('jar-shaking');
            }, 750);

            // 2. Open Modal with random memory
            jarModalOverlay.classList.add('active');
            fetchRandomMemory(0);
        });

        // Also allow clicking directly on the jar to open!
        memoryJar.addEventListener('click', () => {
            btnOpenJar.click();
        });

        // "Pick Another Memory" Button
        if (btnPickAnother) {
            btnPickAnother.addEventListener('click', () => {
                btnPickAnother.classList.add('btn-magic');
                fetchRandomMemory(currentMemoryId);
            });
        }

        // Close Modal Handlers
        const closeModal = () => {
            jarModalOverlay.classList.remove('active');
        };

        if (btnCloseModal) btnCloseModal.addEventListener('click', closeModal);
        if (btnCloseModalBottom) btnCloseModalBottom.addEventListener('click', closeModal);
        
        document.querySelectorAll('.btn-close-modal-alt').forEach(btn => {
            btn.addEventListener('click', closeModal);
        });

        jarModalOverlay.addEventListener('click', (e) => {
            if (e.target === jarModalOverlay) {
                closeModal();
            }
        });
    }

    /* ========================================================
       6. MY MEMORIES PAGE (memories.php)
       Live Filter, Detail Modal & Delete Confirmation
       ======================================================== */
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const sortFilter = document.getElementById('sortFilter');
    const memoriesGrid = document.getElementById('memoriesGrid');
    const filterForm = document.getElementById('filterForm');

    // Live Instant Filtering without page reload
    if (searchInput && memoriesGrid) {
        const filterCards = () => {
            const query = searchInput.value.toLowerCase().trim();
            const selectedCat = categoryFilter ? categoryFilter.value.trim() : '';
            const cards = memoriesGrid.querySelectorAll('.memory-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const title = (card.getAttribute('data-title') || '').toLowerCase();
                const desc = (card.getAttribute('data-full-desc') || '').toLowerCase();
                const cat = (card.getAttribute('data-category') || '');

                const matchesSearch = !query || title.includes(query) || desc.includes(query);
                const matchesCat = !selectedCat || cat === selectedCat;

                if (matchesSearch && matchesCat) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Show or hide live "no results" placeholder
            let noResultsNotice = document.getElementById('liveNoResultsNotice');
            if (visibleCount === 0) {
                if (!noResultsNotice) {
                    noResultsNotice = document.createElement('div');
                    noResultsNotice.id = 'liveNoResultsNotice';
                    noResultsNotice.className = 'empty-state-box';
                    noResultsNotice.innerHTML = `
                        <div class="empty-state-icon">🔍</div>
                        <h2 class="empty-state-title">No Memories Found</h2>
                        <p class="empty-state-desc">No memories matched your search keywords or filter.</p>
                    `;
                    memoriesGrid.parentNode.appendChild(noResultsNotice);
                }
                noResultsNotice.style.display = 'block';
            } else if (noResultsNotice) {
                noResultsNotice.style.display = 'none';
            }
        };

        searchInput.addEventListener('input', filterCards);
        if (categoryFilter) {
            categoryFilter.addEventListener('change', () => {
                // If form submit preferred for bookmarking, submit, otherwise filter live
                filterCards();
            });
        }
        if (sortFilter && filterForm) {
            sortFilter.addEventListener('change', () => {
                filterForm.submit();
            });
        }
    }

    // Modal: View Full Memory Details
    const viewDetailModalOverlay = document.getElementById('viewDetailModalOverlay');
    const btnCloseDetailModal = document.getElementById('btnCloseDetailModal');
    const detailBtnClose = document.getElementById('detailBtnClose');

    if (viewDetailModalOverlay) {
        document.querySelectorAll('.btn-view-memory').forEach(btn => {
            btn.addEventListener('click', () => {
                const card = btn.closest('.memory-card');
                if (!card) return;

                const id = card.getAttribute('data-id');
                const title = card.getAttribute('data-title');
                const cat = card.getAttribute('data-category');
                const date = card.getAttribute('data-date');
                const mood = card.getAttribute('data-mood');
                const fullDesc = card.getAttribute('data-full-desc');

                // Populate Details Modal
                const titleEl = document.getElementById('detailTitle');
                const descEl = document.getElementById('detailDescription');
                const dateEl = document.getElementById('detailDate');
                const catBadgeEl = document.getElementById('detailCategoryBadge');
                const catTextEl = document.getElementById('detailCategoryText');
                const moodBadgeEl = document.getElementById('detailMoodBadge');
                const editLinkEl = document.getElementById('detailBtnEdit');

                if (titleEl) titleEl.textContent = title;
                if (descEl) descEl.textContent = fullDesc;
                if (dateEl) dateEl.textContent = date;
                if (catTextEl) catTextEl.textContent = cat;
                if (moodBadgeEl) moodBadgeEl.textContent = mood;
                if (editLinkEl) editLinkEl.href = `edit-memory.php?id=${id}`;

                viewDetailModalOverlay.classList.add('active');
            });
        });

        const closeDetail = () => viewDetailModalOverlay.classList.remove('active');
        if (btnCloseDetailModal) btnCloseDetailModal.addEventListener('click', closeDetail);
        if (detailBtnClose) detailBtnClose.addEventListener('click', closeDetail);
        viewDetailModalOverlay.addEventListener('click', (e) => {
            if (e.target === viewDetailModalOverlay) closeDetail();
        });
    }

    // Modal: Delete Memory Confirmation
    const deleteModalOverlay = document.getElementById('deleteConfirmModalOverlay');
    const deleteTargetTitle = document.getElementById('deleteTargetTitle');
    const deleteInputId = document.getElementById('deleteInputId');
    const btnCancelDelete = document.getElementById('btnCancelDelete');
    const deleteConfirmForm = document.getElementById('deleteConfirmForm');

    if (deleteModalOverlay) {
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                const title = btn.getAttribute('data-title');

                if (deleteTargetTitle) deleteTargetTitle.textContent = `"${title}"`;
                if (deleteInputId) deleteInputId.value = id;

                deleteModalOverlay.classList.add('active');
            });
        });

        const closeDeleteModal = () => deleteModalOverlay.classList.remove('active');
        if (btnCancelDelete) btnCancelDelete.addEventListener('click', closeDeleteModal);
        deleteModalOverlay.addEventListener('click', (e) => {
            if (e.target === deleteModalOverlay) closeDeleteModal();
        });

        // Smooth asynchronous deletion
        if (deleteConfirmForm) {
            deleteConfirmForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const memId = deleteInputId.value;
                if (!memId) return;

                const formData = new FormData();
                formData.append('id', memId);

                try {
                    const res = await fetch('delete-memory.php', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    const result = await res.json();

                    if (result.success) {
                        closeDeleteModal();
                        // Animate card removal
                        const targetCard = document.getElementById(`memory-card-${memId}`);
                        if (targetCard) {
                            targetCard.style.transition = 'all 0.4s ease';
                            targetCard.style.transform = 'scale(0.8) translateY(20px)';
                            targetCard.style.opacity = '0';
                            setTimeout(() => {
                                targetCard.remove();
                                // Check if grid is now empty
                                const remaining = document.querySelectorAll('.memory-card');
                                if (remaining.length === 0) {
                                    window.location.reload();
                                }
                            }, 400);
                        } else {
                            window.location.href = 'memories.php?msg=deleted';
                        }
                    } else {
                        alert(result.message || 'Could not delete memory.');
                        closeDeleteModal();
                    }
                } catch (err) {
                    // Fallback to normal form submit
                    deleteConfirmForm.submit();
                }
            });
        }
    }

    // Global ESC key listener to close active modals
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.active').forEach(modal => {
                modal.classList.remove('active');
            });
        }
    });

});
