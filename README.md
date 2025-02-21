<details>
  <summary><h2>Table of Contents</h2></summary>
  <ol>
    <li><a href="#about-the-project">About The Project</a></li>
    <li><a href="#getting-started">Getting Started</a></li>
    <li><a href="#installation-guide">Installation Guide</a></li>
    <li><a href="#login-information">Login Information</a></li>
  </ol>
</details>

## About The Project

The **Employee Time Tracker System** is a streamlined solution for tracking employee working hours and attendance. It simplifies administrative tasks, reduces errors, and provides real-time insights to enhance workforce management.

**Key Features:**

- **Real-Time Tracking**: Monitor employee attendance in real-time.
- **Multiple Shift Management**: Supports various employee schedules (part-time, full-time).
- **Automated Record Keeping**: Reduces manual effort in tracking attendance.
- **Increased Efficiency**: Improves workforce management and decision-making.

### Built With:

- [![Bootstrap][Bootstrap.com]][Bootstrap-url]
- ![PHP][PHP.com]
- ![JavaScript][Javascript.com]
- ![HTML][HTML.com]
- ![CSS][CSS.com]

## Getting Started

### Prerequisites

Ensure you have the following installed before proceeding:

1. **Operating System Compatibility**: Windows, macOS, or Linux.
2. **Internet Connection**: Stable internet for downloading dependencies.
3. **GitHub Account (Optional)**: To clone the repository.
4. **XAMPP**: Download and install XAMPP from [here](https://www.apachefriends.org/index.html).
5. **Web Browser**: A web browser supporting PHP.

## Installation Guide

### Step 1: Clone the Repository (Optional)

1. Open **GitHub Desktop**.
2. Click "File" > "Clone Repository".
3. Enter the repository URL and set the local path to `htdocs` (e.g., `C:\xampp\htdocs` on Windows).
4. Click **Clone**.

### Step 2: Download the Repository as a ZIP File

1. On GitHub, click the **Code** button, then **Download ZIP**.
2. Extract the ZIP file into the **htdocs** folder of XAMPP (e.g., `C:\xampp\htdocs`).

### Step 3: Run Locally with XAMPP

1. **Start XAMPP**:

   - Open XAMPP Control Panel and start **Apache** and **MySQL** services.

2. **Import Database**:

   - Navigate to `localhost/phpmyadmin/` in your browser.
   - Create a new database and import the `employee_db.sql` file from the extracted folder.

3. **Access the Website**:
   - In your browser, visit `localhost/Employee-Time-Tracker-System/` to access the system.

## Login Information

### Admin Login:

- **Username**: admin1
- **Password**: admin123

### Employee Login (for different schedules):

| Schedule                  | Employee ID | Password   |
| ------------------------- | ----------- | ---------- |
| **Part-Time (Afternoon)** | 17          | password17 |
| **Part-Time (Morning)**   | 18          | password18 |
| **Full-Time (Day)**       | 14          | password14 |

<p align="right">(<a href="#readme-top">back to top</a>)</p>

---

<!-- MARKDOWN LINKS & IMAGES -->

[Bootstrap.com]: https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white
[Bootstrap-url]: https://getbootstrap.com
[PHP.com]: https://img.shields.io/badge/PHP-7A86B8?style=for-the-badge&logo=PHP&logoColor=white
[Javascript.com]: https://img.shields.io/badge/Javascript-EDD84B?style=for-the-badge&logo=Javascript&logoColor=white
[HTML.com]: https://img.shields.io/badge/HTML-E34B24?style=for-the-badge&logo=HTML&logoColor=white
[CSS.com]: https://img.shields.io/badge/CSS-1370B5?style=for-the-badge&logo=css&logoColor=white
