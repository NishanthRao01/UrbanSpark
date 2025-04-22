// Initialize GSAP
gsap.registerPlugin(ScrollTrigger);

// Animate cards on scroll
function initCardAnimations() {
    gsap.utils.toArray('.idea-card').forEach((card, i) => {
        gsap.from(card, {
            scrollTrigger: {
                trigger: card,
                start: "top bottom-=100",
                toggleActions: "play none none reverse"
            },
            y: 100,
            opacity: 0,
            duration: 0.8,
            ease: "power3.out",
            delay: i * 0.1
        });
    });
}

// Animate comments when they appear
function animateNewComment(commentElement) {
    gsap.from(commentElement, {
        y: 30,
        opacity: 0,
        duration: 0.5,
        ease: "back.out(1.7)"
    });
}

// Floating effect for buttons
function initButtonAnimations() {
    const buttons = document.querySelectorAll('button, .filter-button');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', () => {
            gsap.to(button, {
                y: -5,
                scale: 1.05,
                duration: 0.3,
                ease: "power2.out"
            });
        });
        
        button.addEventListener('mouseleave', () => {
            gsap.to(button, {
                y: 0,
                scale: 1,
                duration: 0.3,
                ease: "power2.in"
            });
        });
    });
}

// Magnetic effect for buttons
function initMagneticButtons() {
    const buttons = document.querySelectorAll('.magnetic-button');
    
    buttons.forEach(button => {
        button.addEventListener('mousemove', (e) => {
            const rect = button.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;
            
            gsap.to(button, {
                x: x * 0.2,
                y: y * 0.2,
                duration: 0.3,
                ease: "power2.out"
            });
        });
        
        button.addEventListener('mouseleave', () => {
            gsap.to(button, {
                x: 0,
                y: 0,
                duration: 0.3,
                ease: "power2.out"
            });
        });
    });
}

// Text scramble effect
class TextScramble {
    constructor(el) {
        this.el = el;
        this.chars = '!<>-_\\/[]{}—=+*^?#________';
        this.update = this.update.bind(this);
    }
    
    setText(newText) {
        const oldText = this.el.innerText;
        const length = Math.max(oldText.length, newText.length);
        const promise = new Promise((resolve) => this.resolve = resolve);
        this.queue = [];
        
        for (let i = 0; i < length; i++) {
            const from = oldText[i] || '';
            const to = newText[i] || '';
            const start = Math.floor(Math.random() * 40);
            const end = start + Math.floor(Math.random() * 40);
            this.queue.push({ from, to, start, end });
        }
        
        cancelAnimationFrame(this.frameRequest);
        this.frame = 0;
        this.update();
        return promise;
    }
    
    update() {
        let output = '';
        let complete = 0;
        
        for (let i = 0, n = this.queue.length; i < n; i++) {
            let { from, to, start, end, char } = this.queue[i];
            
            if (this.frame >= end) {
                complete++;
                output += to;
            } else if (this.frame >= start) {
                if (!char || Math.random() < 0.28) {
                    char = this.randomChar();
                    this.queue[i].char = char;
                }
                output += `<span class="text-blue-500">${char}</span>`;
            } else {
                output += from;
            }
        }
        
        this.el.innerHTML = output;
        
        if (complete === this.queue.length) {
            this.resolve();
        } else {
            this.frameRequest = requestAnimationFrame(this.update);
            this.frame++;
        }
    }
    
    randomChar() {
        return this.chars[Math.floor(Math.random() * this.chars.length)];
    }
}

// Initialize all animations
document.addEventListener('DOMContentLoaded', () => {
    initCardAnimations();
    initButtonAnimations();
    initMagneticButtons();
    
    // Initialize text scramble effect for headings
    document.querySelectorAll('.scramble-text').forEach(el => {
        const fx = new TextScramble(el);
        fx.setText(el.textContent);
    });
}); 