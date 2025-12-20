// SMOOTH SCROLL
document.querySelectorAll('nav a').forEach(link => {
    link.addEventListener('click', e => {
        e.preventDefault();
        const target = document.querySelector(link.getAttribute('href'));
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
});

// CONTACT FORM
const form = document.getElementById('contactForm');
const status = document.getElementById('formStatus');

if (form) {
    form.addEventListener('submit', async e => {
        e.preventDefault();
        status.textContent = "Sending...";
        status.style.color = "#27ae60";

        const data = {
            name: form.name.value.trim(),
            email: form.email.value.trim(),
            message: form.message.value.trim()
        };

        if (!data.name || !data.email || !data.message) {
            status.textContent = "Please fill all fields!";
            status.style.color = "red";
            return;
        }

        try {
            const res = await fetch('/contact', {
                method: 'POST',
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(data)
            });
            const result = await res.json();
            if (result.status === "success") {
                status.textContent = "Message sent!";
                status.style.color = "green";
                form.reset();
            } else {
                status.textContent = "Error sending!";
                status.style.color = "red";
            }
        } catch (err) {
            status.textContent = "Error sending!";
            status.style.color = "red";
        }
    });
}

// ANIMATION ON SCROLL
const sections = document.querySelectorAll('.section');
const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if(entry.isIntersecting) entry.target.classList.add('visible');
    });
}, { threshold: 0.2 });
sections.forEach(section => observer.observe(section));
