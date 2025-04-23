// Wait for the DOM to load
document.addEventListener('DOMContentLoaded', () => {
    // Initial loading animation timeline
    const timeline = anime.timeline({
        easing: 'easeOutExpo'
    });

    // Animate the main title
    timeline
        .add({
            targets: '.main-title',
            opacity: [0, 1],
            translateY: [-50, 0],
            duration: 1200,
            delay: 300
        })
        .add({
            targets: '.welcome-section p',
            opacity: [0, 1],
            translateY: [-20, 0],
            duration: 800
        }, '-=800')
        .add({
            targets: '.name-form',
            opacity: [0, 1],
            translateY: [20, 0],
            duration: 800
        }, '-=400');

    // Category boxes animation with stagger
    anime({
        targets: '.category-box',
        scale: [0.5, 1],
        opacity: [0, 1],
        delay: anime.stagger(200, {start: 1000}),
        duration: 1500,
        easing: 'easeOutElastic(1, .5)'
    });

    // Admin button animation
    anime({
        targets: '.admin-link',
        translateX: [50, 0],
        opacity: [0, 1],
        delay: 1500,
        duration: 800,
        easing: 'easeOutCubic'
    });

    // Footer animation
    anime({
        targets: '.site-footer',
        translateY: [50, 0],
        opacity: [0, 1],
        delay: 1200,
        duration: 800,
        easing: 'easeOutCubic'
    });

    // Sound toggle button animation
    anime({
        targets: '.sound-toggle',
        translateX: [-50, 0],
        opacity: [0, 1],
        delay: 1500,
        duration: 800,
        easing: 'easeOutCubic'
    });

    // Add hover animations for category boxes
    const categoryBoxes = document.querySelectorAll('.category-box');
    categoryBoxes.forEach(box => {
        box.addEventListener('mouseenter', () => {
            anime({
                targets: box,
                scale: 1.05,
                boxShadow: '0 15px 30px rgba(0,0,0,0.4)',
                duration: 300,
                easing: 'easeOutElastic(1, .5)'
            });
            
            // Animate the image inside
            anime({
                targets: box.querySelector('img'),
                scale: 1.1,
                duration: 300,
                easing: 'easeOutCubic'
            });
            
            // Animate the title
            anime({
                targets: box.querySelector('h2'),
                translateY: -5,
                duration: 300,
                easing: 'easeOutCubic'
            });
        });

        box.addEventListener('mouseleave', () => {
            anime({
                targets: box,
                scale: 1,
                boxShadow: '0 10px 20px rgba(0,0,0,0.3)',
                duration: 300,
                easing: 'easeOutElastic(1, .5)'
            });
            
            // Reset image animation
            anime({
                targets: box.querySelector('img'),
                scale: 1,
                duration: 300,
                easing: 'easeOutCubic'
            });
            
            // Reset title animation
            anime({
                targets: box.querySelector('h2'),
                translateY: 0,
                duration: 300,
                easing: 'easeOutCubic'
            });
        });
    });

    // Form submission animation
    const nameForm = document.getElementById('nameForm');
    if (nameForm) {
        nameForm.addEventListener('submit', function(e) {
            const formElements = this.elements;
            anime({
                targets: formElements,
                scale: [1, 0.95],
                duration: 150,
                easing: 'easeInOutQuad',
                complete: () => {
                    anime({
                        targets: formElements,
                        scale: [0.95, 1],
                        duration: 150,
                        easing: 'easeInOutQuad'
                    });
                }
            });
        });
    }

    // Particle effect for the background
    const particlesContainer = document.createElement('div');
    particlesContainer.className = 'particles';
    document.body.appendChild(particlesContainer);

    for (let i = 0; i < 50; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        particlesContainer.appendChild(particle);

        const x = Math.random() * window.innerWidth;
        const y = Math.random() * window.innerHeight;
        const size = Math.random() * 3 + 1;

        particle.style.left = x + 'px';
        particle.style.top = y + 'px';
        particle.style.width = size + 'px';
        particle.style.height = size + 'px';

        anime({
            targets: particle,
            opacity: [0, 0.5, 0],
            translateY: [-50, 50],
            translateX: [-20, 20],
            scale: [1, 0.5],
            loop: true,
            duration: 3000 + Math.random() * 5000,
            delay: Math.random() * 2000,
            easing: 'easeInOutQuad',
            direction: 'alternate'
        });
    }
});
