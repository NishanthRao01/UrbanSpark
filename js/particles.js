class ParticleNetwork {
    constructor(canvas) {
        this.canvas = canvas;
        this.ctx = canvas.getContext('2d');
        this.particleCount = 100;
        this.particles = [];
        this.mousePosition = { x: 0, y: 0 };
        this.connectionDistance = 100;
        this.init();
    }

    init() {
        // Set canvas size
        this.resizeCanvas();
        window.addEventListener('resize', () => this.resizeCanvas());

        // Track mouse movement
        document.addEventListener('mousemove', (e) => {
            this.mousePosition.x = e.clientX;
            this.mousePosition.y = e.clientY;
        });

        // Create particles
        for (let i = 0; i < this.particleCount; i++) {
            this.particles.push(new Particle(this.canvas));
        }

        // Start animation
        this.animate();
    }

    resizeCanvas() {
        this.canvas.width = window.innerWidth;
        this.canvas.height = window.innerHeight;
    }

    animate() {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);

        // Update and draw particles
        this.particles.forEach(particle => {
            particle.update();
            particle.draw(this.ctx);
        });

        // Draw connections
        this.drawConnections();

        // Continue animation
        requestAnimationFrame(() => this.animate());
    }

    drawConnections() {
        for (let i = 0; i < this.particles.length; i++) {
            for (let j = i + 1; j < this.particles.length; j++) {
                const dx = this.particles[i].x - this.particles[j].x;
                const dy = this.particles[i].y - this.particles[j].y;
                const distance = Math.sqrt(dx * dx + dy * dy);

                if (distance < this.connectionDistance) {
                    const opacity = 1 - (distance / this.connectionDistance);
                    this.ctx.beginPath();
                    this.ctx.strokeStyle = `rgba(100, 149, 237, ${opacity * 0.5})`;
                    this.ctx.lineWidth = 1;
                    this.ctx.moveTo(this.particles[i].x, this.particles[i].y);
                    this.ctx.lineTo(this.particles[j].x, this.particles[j].y);
                    this.ctx.stroke();
                }
            }
        }
    }
}

class Particle {
    constructor(canvas) {
        this.canvas = canvas;
        this.x = Math.random() * canvas.width;
        this.y = Math.random() * canvas.height;
        this.speed = 0.5;
        this.directionX = Math.random() * 2 - 1;
        this.directionY = Math.random() * 2 - 1;
        this.size = Math.random() * 2 + 1;
    }

    update() {
        // Update position
        this.x += this.directionX * this.speed;
        this.y += this.directionY * this.speed;

        // Bounce off edges
        if (this.x < 0 || this.x > this.canvas.width) this.directionX *= -1;
        if (this.y < 0 || this.y > this.canvas.height) this.directionY *= -1;
    }

    draw(ctx) {
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        ctx.fillStyle = 'rgba(100, 149, 237, 0.8)';
        ctx.fill();
    }
} 