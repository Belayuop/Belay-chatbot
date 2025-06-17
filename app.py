from flask import Flask, request, jsonify
import requests
from bs4 import BeautifulSoup

app = Flask(__name__)

# Chatbot info repository
DATA = {}

def update_data():
    """Fetch info from DBU website and store in DATA."""
    url = "https://www.dbu.edu.et"
    DATA.clear()
    r = requests.get(url)
    soup = BeautifulSoup(r.text, "html.parser")

    # Example scrapes — adjust selectors to match your site
    DATA['campuses'] = extract_number(soup, 'Campus')
    DATA['colleges'] = extract_number(soup, 'Colleges')
    DATA['departments'] = extract_number(soup, 'Department')
    DATA['programs'] = extract_number(soup, 'Programs')
    DATA['academic_staff'] = extract_number(soup, 'Academic staff')
    DATA['admin_staff'] = extract_number(soup, 'Administrative staff')
    DATA['students'] = extract_number(soup, 'Students')

def extract_number(soup, label):
    """Find number next to a specific label on the page."""
    e = soup.find(text=label)
    if not e or not e.find_next():
        return None
    return e.find_next().get_text(strip=True)

def get_answer(msg):
    """Return chatbot answer based on the message."""
    m = msg.lower()
    if 'campus' in m:
        return f"Debre Berhan University has {DATA.get('campuses')} campuses."
    if 'college' in m:
        return f"There are {DATA.get('colleges')} colleges."
    if 'department' in m:
        return f"There are {DATA.get('departments')} departments."
    if 'program' in m:
        return f"There are {DATA.get('programs')} academic programs."
    if 'staff' in m:
        return (f"Academic staff: {DATA.get('academic_staff')}, "
                f"Administrative staff: {DATA.get('admin_staff')}.")
    if 'student' in m:
        return f"There are {DATA.get('students')} students."
    if 'dorm' in m or 'doerm' in m:
        return ("To get a dorm, please check the Admission or Services section "
                "on the DBU website.")
    if 'president' in m or 'office' in m:
        return ("The President’s Office is located in the Administration section "
                "on the DBU website.")
    return None

@app.route("/chat", methods=["POST"])
def chat():
    msg = request.json.get('message')
    if not msg:
        return jsonify({"error": "Please send a message in JSON as {'message': '...'}"}), 400

    if not DATA:
        update_data()

    answer = get_answer(msg)
    if answer:
        return jsonify({"response": answer})

    return jsonify({
        "response": (
            "Sorry, I don’t have that information. "
            "Please contact the campus technical team:\n"
            "📧 kassanewbelay@gmail.com\n"
            "📞 +251980353791"
        )
    })

if __name__ == "__main__":
    app.run(debug=True)

