// ===== NAVIGATION & SMOOTH SCROLL =====
const navLinks = document.querySelectorAll('nav a');
const sections = document.querySelectorAll('section');

navLinks.forEach(link => {
    link.addEventListener('click', e => {
        e.preventDefault();
        const target = document.querySelector(link.getAttribute('href'));
        if (target) {
            // Smooth scroll to section
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});

// ===== ACTIVE NAV HIGHLIGHT ON SCROLL =====
window.addEventListener('scroll', () => {
    let current = '';
    sections.forEach(section => {
        const sectionTop = section.offsetTop - 100;
        if (pageYOffset >= sectionTop) {
            current = section.getAttribute('id');
        }
    });

    navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.dataset.target === current) {
            link.classList.add('active');
        }
    });
});

// ===== SCROLL ANIMATION (FADE-IN / SLIDE-IN) =====
const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if(entry.isIntersecting) {
            entry.target.classList.add('visible');
        }
    });
}, { threshold: 0.2 });

sections.forEach(section => {
    observer.observe(section);
});

// ===== ANIMATED STAT COUNTERS =====
const counters = document.querySelectorAll('.counter');

counters.forEach(counter => {
    counter.innerText = '0';
    const updateCounter = () => {
        const target = +counter.getAttribute('data-target');
        const current = +counter.innerText;
        const increment = target / 150; // speed of animation

        if (current < target) {
            counter.innerText = `${Math.ceil(current + increment)}`;
            setTimeout(updateCounter, 15);
        } else {
            counter.innerText = target;
        }
    };
    // Animate when visible
    const counterObserver = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if(entry.isIntersecting) {
                updateCounter();
                counterObserver.unobserve(counter);
            }
        });
    }, { threshold: 0.5 });
    counterObserver.observe(counter);
});

// ===== CONTACT FORM HANDLER =====
const form = document.getElementById('contactForm');
const status = document.getElementById('formStatus');

if(form){
    form.addEventListener('submit', async e => {
        e.preventDefault();
        status.textContent = "Sending...";
        status.style.color = "#8B0000";

        const data = {
            name: form.name.value.trim(),
            email: form.email.value.trim(),
            message: form.message.value.trim()
        };

        if(!data.name || !data.email || !data.message){
            status.textContent = "Please fill all fields!";
            status.style.color = "red";
            return;
        }

        // Simulate sending (replace with backend API)
        setTimeout(() => {
            status.textContent = "Message sent successfully!";
            status.style.color = "green";
            form.reset();
        }, 1200);
    });
}

// ===== OPTIONAL: NAVBAR SHRINK ON SCROLL =====
const nav = document.querySelector('nav');
window.addEventListener('scroll', () => {
    if(window.scrollY > 50){
        nav.style.padding = '6px 0';
        nav.style.boxShadow = '0 4px 12px rgba(0,0,0,0.2)';
    } else {
        nav.style.padding = '12px 0';
        nav.style.boxShadow = '0 2px 8px rgba(0,0,0,0.2)';
    }
});
