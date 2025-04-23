document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const bgVideo = document.getElementById('bgVideo');
    const soundToggle = document.getElementById('soundToggle');
    const soundIcon = soundToggle.querySelector('i');
    const nameForm = document.getElementById('nameForm');
    
    // User name storage
    let userName = localStorage.getItem('reelUserName') || '';
    
    // Initialize sound (muted by default)
    let isMuted = true;
    
    // Ensure video plays (handle autoplay restrictions)
    function attemptPlayVideo() {
        // We need to keep it muted first to get around autoplay restrictions
        bgVideo.muted = true;
        
        // Try to play the video
        let playPromise = bgVideo.play();
        
        // Modern browsers return a promise from the play function
        if (playPromise !== undefined) {
            playPromise.then(_ => {
                // Video playback started successfully
                console.log("Video is playing");
            })
            .catch(error => {
                // Auto-play was prevented
                console.log("Video playback was prevented:", error);
                
                // Add a play button overlay as fallback
                createPlayButton();
            });
        }
    }
    
    // Create a play button overlay if autoplay is prevented
    function createPlayButton() {
        let playButtonOverlay = document.createElement('div');
        playButtonOverlay.className = 'video-play-overlay';
        playButtonOverlay.innerHTML = '<button><i class="fas fa-play"></i></button>';
        playButtonOverlay.style.position = 'fixed';
        playButtonOverlay.style.top = '0';
        playButtonOverlay.style.left = '0';
        playButtonOverlay.style.width = '100%';
        playButtonOverlay.style.height = '100%';
        playButtonOverlay.style.background = 'rgba(0, 0, 0, 0.5)';
        playButtonOverlay.style.zIndex = '99';
        playButtonOverlay.style.display = 'flex';
        playButtonOverlay.style.alignItems = 'center';
        playButtonOverlay.style.justifyContent = 'center';
        
        playButtonOverlay.querySelector('button').addEventListener('click', function() {
            bgVideo.play();
            playButtonOverlay.remove();
        });
        
        document.body.appendChild(playButtonOverlay);
    }
    
    // Try to play the video
    attemptPlayVideo();
    
    // Sound toggle functionality
    soundToggle.addEventListener('click', function() {
        if (isMuted) {
            // Unmute video audio
            bgVideo.muted = false;
            soundIcon.classList.remove('fa-volume-mute');
            soundIcon.classList.add('fa-volume-up');
            isMuted = false;
        } else {
            // Mute video audio
            bgVideo.muted = true;
            soundIcon.classList.remove('fa-volume-up');
            soundIcon.classList.add('fa-volume-mute');
            isMuted = true;
        }
    });
    
    // Form submission
    if (nameForm) {
        // Pre-fill the name if it exists
        const userNameInput = document.getElementById('userName');
        if (userName && userNameInput) {
            userNameInput.value = userName;
        }
          nameForm.addEventListener('submit', function(e) {
            e.preventDefault();
            userName = document.getElementById('userName').value;
            
            if (userName) {
                // Save the name in local storage
                localStorage.setItem('reelUserName', userName);
                
                // Hide the form with animation
                nameForm.classList.add('hide');
                
                // Show categories with animation
                setTimeout(() => {
                    const categoriesContainer = document.querySelector('.categories-container');
                    categoriesContainer.classList.add('show');
                }, 500);
            }
        });
    }
    
    // Video player functionality for category pages
    const videoCards = document.querySelectorAll('.video-card');
    const videoOverlay = document.querySelector('.video-overlay');
    const playerContainer = document.querySelector('.video-player-container');
    
    if (videoCards.length > 0) {
        videoCards.forEach(card => {
            card.addEventListener('click', function() {
                const videoSrc = this.getAttribute('data-video-src');
                const videoPlayer = document.getElementById('videoPlayer');
                
                // Set the video source
                videoPlayer.src = videoSrc;
                videoPlayer.load();
                
                // Add event listener to detect when video metadata is loaded
                videoPlayer.addEventListener('loadedmetadata', function() {
                    // Check if video is portrait (height > width)
                    if (this.videoHeight > this.videoWidth) {
                        playerContainer.classList.add('portrait-video');
                    } else {
                        playerContainer.classList.remove('portrait-video');
                    }
                });
                
                // Show overlay and play video
                videoOverlay.classList.add('active');
                videoPlayer.play();
                
                // Mute the background music while playing the video
                if (bgMusic) {
                    bgMusic.pause();
                }
            });
        });
    
        // Close video overlay
        const closeVideo = document.querySelector('.close-video');
        if (closeVideo) {
            closeVideo.addEventListener('click', function() {
                videoOverlay.classList.remove('active');
                const videoPlayer = document.getElementById('videoPlayer');
                videoPlayer.pause();
                
                // Resume background music if it was playing before
                if (bgMusic && !isMuted) {
                    bgMusic.play();
                }
            });
        }
    }
    
    // Check if we need to auto-play background music based on user preference
    const checkMusicPreference = () => {
        const musicPreference = localStorage.getItem('reelMusicEnabled');
        if (musicPreference === 'true') {
            // User previously enabled music
            bgMusic.play();
            bgMusic.muted = false;
            soundIcon.classList.remove('fa-volume-mute');
            soundIcon.classList.add('fa-volume-up');
            isMuted = false;
        }
    };
    
    // Try to play background music after user interaction
    document.addEventListener('click', function musicInitializer() {
        bgMusic.load();
        checkMusicPreference();
        // Remove this event listener after first click
        document.removeEventListener('click', musicInitializer);
    }, { once: true });
});
