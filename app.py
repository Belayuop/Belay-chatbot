from flask import Flask, render_template, request, redirect, url_for, session, jsonify, flash
from werkzeug.security import generate_password_hash, check_password_hash
import sqlite3
from datetime import datetime, timedelta
import os

app = Flask(__name__)
app.secret_key = 'supersecretkey'

UPLOAD_FOLDER = 'uploads'
if not os.path.exists(UPLOAD_FOLDER):
    os.makedirs(UPLOAD_FOLDER)

# ===== DATABASE =====
def init_db():
    conn = sqlite3.connect('database.db')
    c = conn.cursor()
    # Users table
    c.execute('''CREATE TABLE IF NOT EXISTS users(
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT, email TEXT UNIQUE, password TEXT, role TEXT, trial_end DATE
    )''')
    # Courses table
    c.execute('''CREATE TABLE IF NOT EXISTS courses(
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT, category TEXT, duration TEXT, difficulty TEXT, price REAL
    )''')
    # Enrollments
    c.execute('''CREATE TABLE IF NOT EXISTS enrollments(
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER, course_id INTEGER,
        start_date DATE, status TEXT,
        FOREIGN KEY(user_id) REFERENCES users(id),
        FOREIGN KEY(course_id) REFERENCES courses(id)
    )''')
    conn.commit()
    conn.close()

init_db()

# ===== ROUTES =====
@app.route('/')
def index():
    return render_template('index.html')

# REGISTER
@app.route('/register', methods=['GET', 'POST'])
def register():
    if request.method == 'POST':
        name = request.form['name']
        email = request.form['email']
        password = generate_password_hash(request.form['password'])
        role = request.form['role']
        trial_end = (datetime.today() + timedelta(days=30)).strftime("%Y-%m-%d")
        conn = sqlite3.connect('database.db')
        c = conn.cursor()
        try:
            c.execute('INSERT INTO users (name,email,password,role,trial_end) VALUES (?,?,?,?,?)',
                      (name,email,password,role,trial_end))
            conn.commit()
            flash('Account created! You have a 30-day free trial.', 'success')
            return redirect(url_for('login'))
        except sqlite3.IntegrityError:
            flash('Email already registered.', 'danger')
        finally:
            conn.close()
    return render_template('register.html')

# LOGIN
@app.route('/login', methods=['GET','POST'])
def login():
    if request.method == 'POST':
        email = request.form['email']
        password = request.form['password']
        conn = sqlite3.connect('database.db')
        c = conn.cursor()
        c.execute('SELECT * FROM users WHERE email=?', (email,))
        user = c.fetchone()
        conn.close()
        if user and check_password_hash(user[3], password):
            session['user_id'] = user[0]
            session['role'] = user[4]
            session['name'] = user[1]
            flash('Login successful!', 'success')
            if user[4] == 'teacher':
                return redirect(url_for('admin_dashboard'))
            return redirect(url_for('dashboard'))
        else:
            flash('Invalid credentials!', 'danger')
    return render_template('login.html')

# LOGOUT
@app.route('/logout')
def logout():
    session.clear()
    flash('Logged out.', 'info')
    return redirect(url_for('index'))

# DASHBOARD
@app.route('/dashboard')
def dashboard():
    if 'user_id' not in session:
        flash('Please login first.', 'warning')
        return redirect(url_for('login'))
    conn = sqlite3.connect('database.db')
    c = conn.cursor()
    # Get all courses
    c.execute('SELECT * FROM courses')
    courses = c.fetchall()
    # Get user's enrollments
    c.execute('SELECT course_id FROM enrollments WHERE user_id=?', (session['user_id'],))
    enrolled_ids = [row[0] for row in c.fetchall()]
    conn.close()
    return render_template('dashboard.html', courses=courses, enrolled_ids=enrolled_ids)

# ENROLL IN COURSE
@app.route('/enroll/<int:course_id>')
def enroll(course_id):
    if 'user_id' not in session:
        flash('Login first.', 'warning')
        return redirect(url_for('login'))
    conn = sqlite3.connect('database.db')
    c = conn.cursor()
    # Check if already enrolled
    c.execute('SELECT * FROM enrollments WHERE user_id=? AND course_id=?', (session['user_id'], course_id))
    if c.fetchone():
        flash('Already enrolled!', 'info')
    else:
        start_date = datetime.today().strftime("%Y-%m-%d")
        c.execute('INSERT INTO enrollments(user_id, course_id, start_date, status) VALUES (?,?,?,?)',
                  (session['user_id'], course_id, start_date, 'active'))
        flash('Enrolled successfully!', 'success')
    conn.commit()
    conn.close()
    return redirect(url_for('dashboard'))

# TEACHER DASHBOARD
@app.route('/admin')
def admin_dashboard():
    if 'role' not in session or session['role'] != 'teacher':
        flash('Access denied!', 'danger')
        return redirect(url_for('login'))
    conn = sqlite3.connect('database.db')
    c = conn.cursor()
    c.execute('SELECT * FROM courses')
    courses = c.fetchall()
    conn.close()
    return render_template('admin.html', courses=courses)

# ADD COURSE
@app.route('/add_course', methods=['POST'])
def add_course():
    if 'role' not in session or session['role'] != 'teacher':
        flash('Access denied!', 'danger')
        return redirect(url_for('login'))
    title = request.form['title']
    category = request.form['category']
    duration = request.form['duration']
    difficulty = request.form['difficulty']
    price = request.form['price']
    conn = sqlite3.connect('database.db')
    c = conn.cursor()
    c.execute('INSERT INTO courses(title,category,duration,difficulty,price) VALUES (?,?,?,?,?)',
              (title,category,duration,difficulty,price))
    conn.commit()
    conn.close()
    flash('Course added!', 'success')
    return redirect(url_for('admin_dashboard'))

if __name__ == '__main__':
    app.run(debug=True)
