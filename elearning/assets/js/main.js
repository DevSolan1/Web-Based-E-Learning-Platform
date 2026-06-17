// E-Learning Platform JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 150);
        }, 5000);
    });

    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // Star rating system
    const ratingInputs = document.querySelectorAll('.rating-input');
    ratingInputs.forEach(container => {
        const stars = container.querySelectorAll('.star');
        const input = container.querySelector('input[type="hidden"]');
        
        stars.forEach((star, index) => {
            star.addEventListener('click', () => {
                input.value = index + 1;
                stars.forEach((s, i) => {
                    s.classList.toggle('bi-star-fill', i <= index);
                    s.classList.toggle('bi-star', i > index);
                });
            });
            
            star.addEventListener('mouseenter', () => {
                stars.forEach((s, i) => {
                    s.classList.toggle('bi-star-fill', i <= index);
                    s.classList.toggle('bi-star', i > index);
                });
            });
        });
        
        container.addEventListener('mouseleave', () => {
            const currentValue = parseInt(input.value) || 0;
            stars.forEach((s, i) => {
                s.classList.toggle('bi-star-fill', i < currentValue);
                s.classList.toggle('bi-star', i >= currentValue);
            });
        });
    });

    // Video progress tracking
    const videoPlayer = document.getElementById('courseVideo');
    if (videoPlayer) {
        let lastUpdate = 0;
        
        videoPlayer.addEventListener('timeupdate', function() {
            const currentTime = Math.floor(this.currentTime);
            if (currentTime - lastUpdate >= 10) { // Update every 10 seconds
                lastUpdate = currentTime;
                updateVideoProgress(currentTime);
            }
        });
        
        videoPlayer.addEventListener('ended', function() {
            markVideoComplete();
        });
    }

    // Confirm delete actions
    const deleteButtons = document.querySelectorAll('[data-confirm]');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm(this.dataset.confirm || 'Are you sure?')) {
                e.preventDefault();
            }
        });
    });

    // Search functionality
    const searchInput = document.getElementById('courseSearch');
    if (searchInput) {
        let timeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                const query = this.value.trim();
                if (query.length >= 2) {
                    window.location.href = `courses.php?search=${encodeURIComponent(query)}`;
                }
            }, 500);
        });
    }

    // File upload preview
    const fileInputs = document.querySelectorAll('input[type="file"][data-preview]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const preview = document.getElementById(this.dataset.preview);
            if (preview && this.files[0]) {
                const reader = new FileReader();
                reader.onload = e => preview.src = e.target.result;
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
});

// Video progress functions
function updateVideoProgress(currentTime) {
    const videoId = document.getElementById('courseVideo')?.dataset.videoId;
    if (!videoId) return;
    
    fetch('ajax/update_progress.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ video_id: videoId, watched_duration: currentTime })
    });
}

function markVideoComplete() {
    const videoId = document.getElementById('courseVideo')?.dataset.videoId;
    if (!videoId) return;
    
    fetch('ajax/update_progress.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ video_id: videoId, is_completed: true })
    }).then(() => {
        const playlistItem = document.querySelector(`[data-video-id="${videoId}"]`);
        if (playlistItem) {
            playlistItem.classList.add('completed');
        }
        checkCourseCompletion();
    });
}

function checkCourseCompletion() {
    const courseId = document.getElementById('courseVideo')?.dataset.courseId;
    if (!courseId) return;
    
    fetch(`ajax/check_completion.php?course_id=${courseId}`)
        .then(res => res.json())
        .then(data => {
            if (data.completed) {
                showCompletionModal();
            }
        });
}

function showCompletionModal() {
    const modal = new bootstrap.Modal(document.getElementById('completionModal'));
    modal.show();
}

// Load video
function loadVideo(videoId) {
    window.location.href = `watch.php?video=${videoId}`;
}

// Format duration
function formatDuration(seconds) {
    const h = Math.floor(seconds / 3600);
    const m = Math.floor((seconds % 3600) / 60);
    const s = seconds % 60;
    
    if (h > 0) {
        return `${h}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
    }
    return `${m}:${s.toString().padStart(2, '0')}`;
}
