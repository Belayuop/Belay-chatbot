body {
    margin: 0;
    font-family: 'Georgia', serif;
    background: #f8f8f8;
    color: #222;
    scroll-behavior: smooth;
}

header {
    text-align: center;
    padding: 60px 20px;
    background: #8B0000; /* Harvard Crimson */
    color: white;
}

header h1 {
    margin: 0;
    font-size: 3rem;
    letter-spacing: 1px;
}

header p {
    font-size: 1.2rem;
    opacity: 0.9;
}

nav {
    display: flex;
    justify-content: center;
    background-color: #5a0000;
    padding: 10px 0;
}

nav a {
    color: white;
    text-decoration: none;
    padding: 15px 25px;
    font-weight: bold;
    letter-spacing: 0.5px;
}

nav a:hover {
    background-color: #a00000;
    transition: 0.3s ease;
}

section {
    padding: 80px 20px;
    max-width: 1100px;
    margin: auto;
    border-bottom: 1px solid #ddd;
    opacity: 0;
    transform: translateY(40px);
    transition: all 0.8s ease;
}

section.visible {
    opacity: 1;
    transform: translateY(0);
}

h2 {
    color: #8B0000;
    margin-bottom: 20px;
    font-size: 2rem;
}

.course-card {
    background: white;
    padding: 25px;
    margin: 20px 0;
    border-left: 6px solid #8B0000;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

button {
    background: #8B0000;
    color: white;
    border: none;
    padding: 12px 28px;
    cursor: pointer;
    font-weight: bold;
}

button:hover {
    background: #a00000;
}

footer {
    text-align: center;
    padding: 30px;
    background: #5a0000;
    color: white;
}
