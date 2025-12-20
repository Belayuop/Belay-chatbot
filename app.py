from flask import Flask, render_template, request, redirect
import os

app = Flask(__name__)

UPLOAD_FOLDER = 'static/uploads'
os.makedirs(UPLOAD_FOLDER, exist_ok=True)

@app.route('/')
def home():
    return render_template('index.html')

@app.route('/upload', methods=['POST'])
def upload():
    file = request.files['student_file']
    if file:
        file.save(os.path.join(UPLOAD_FOLDER, file.filename))
    return redirect('/')

@app.route('/message', methods=['POST'])
def message():
    name = request.form['name']
    email = request.form['email']
    msg = request.form['message']
    print(name, email, msg)
    return redirect('/')

if __name__ == '__main__':
    app.run(debug=True)
