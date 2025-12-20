from flask import Flask, render_template, request, redirect, url_for, jsonify, flash
from werkzeug.utils import secure_filename
import os
import sqlite3

app = Flask(__name__)
app.secret_key = 'supersecretkey'
UPLOAD_FOLDER = 'uploads'
if not os.path.exists(UPLOAD_FOLDER):
    os.makedirs(UPLOAD_FOLDER)

# DATABASE INITIALIZATION
def init_db():
    conn = sqlite3.connect('database.db')
    c = conn.cursor()
    # Users
    c.execute('''CREATE TABLE IF NOT EXISTS users(
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT, email TEXT UNIQUE, password TEXT, role TEXT
    )''')
    # Messages
    c.execute('''CREATE TABLE IF NOT EXISTS messages(
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT, email TEXT, message TEXT
    )''')
    # Files
    c.execute('''CREATE TABLE IF NOT EXISTS files(
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        filename TEXT, uploader TEXT
    )''')
    conn.commit()
    conn.close()

init_db()

# HOME
@app.route('/')
def index():
    return render_template('index.html')

# CONTACT FORM
@app.route('/contact', methods=['POST'])
def contact():
    data = request.get_json()
    name = data.get('name')
    email = data.get('email')
    message = data.get('message')
    conn = sqlite3.connect('database.db')
    c = conn.cursor()
    c.execute('INSERT INTO messages (name,email,message) VALUES (?,?,?)',(name,email,message))
    conn.commit()
    conn.close()
    return jsonify({'status':'success'})

# FILE UPLOAD
@app.route('/upload', methods=['POST'])
def upload_file():
    if 'file' not in request.files:
        flash('No file part')
        return redirect(request.url)
    file = request.files['file']
    if file.filename == '':
        flash('No selected file')
        return redirect(request.url)
    filename = secure_filename(file.filename)
    file.save(os.path.join(UPLOAD_FOLDER, filename))
    conn = sqlite3.connect('database.db')
    c = conn.cursor()
    c.execute('INSERT INTO files(filename,uploader) VALUES (?,?)',(filename,'anonymous'))
    conn.commit()
    conn.close()
    flash('File uploaded successfully!')
    return redirect(url_for('index'))

# RUN APP
if __name__ == '__main__':
    app.run(debug=True)
