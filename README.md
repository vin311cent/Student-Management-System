# Student Management System

**Web Programming 2 – BSc. Comp 222 · Project 1: Student Records Management System**

PHP 8+ object-oriented implementation that meets all core requirements and includes the MySQL stretch goal.

## OOP Concepts Demonstrated

| Concept            | Where it is used |
|--------------------|------------------|
| Classes & objects  | `Student`, `Course`, `Enrolment`, `Grade` (in `src/`) |
| Encapsulation      | Private properties + validated getters/setters |
| Constructors       | `Student`, `Course`, `Enrolment`, `Grade` |
| Composition        | `Student` holds an array of `Enrolment` objects |
| Static members     | `Student::$counter` + `generateStudentNumber()` → `LGU-2026-001` |
| Exceptions         | `InvalidArgumentException` for marks outside 0–100 (caught in demo) |
| Stretch – GPA      | `Student::calculateGpa()` weights marks by credit hours |
| Stretch – Database | PDO singleton (`Database`) + `database.sql` |

## Project Structure

```
Student-Management-System/
├── src/
│   ├── autoload.php      # spl_autoload_register
│   ├── Student.php
│   ├── Course.php
│   ├── Enrolment.php
│   ├── Grade.php
│   └── Database.php      # PDO singleton (stretch)
├── oop_demo.php          # MAIN DEMO SCRIPT (creates 3+ students, enrols, marks, HTML transcripts)
├── index.php             # Landing page
├── Login.php             # Admin login (admin / admin123)
├── dashboard.php
├── Student.php / add_student.php
├── Courses.php
├── Enrolment.php
├── Grades.php
├── AcademicSummary.php
├── Reports.php
├── settings.php
├── database.sql
├── style.css
└── README.md
```

## How to run the required demonstration

1. Place the folder on a PHP 8+ web server (or use `php -S localhost:8000` from this directory).
2. Open **`oop_demo.php`** in the browser  
   (or start at `index.php` → “Run OOP Demo”).
3. You will see:
   - At least 3 students, each enrolled in ≥ 3 courses
   - Marks recorded and converted to letter grades
   - Transcript tables in HTML
   - Graceful catching of invalid marks (0–100 rule)
   - Weighted GPA (stretch)

## Admin web application (stretch)

1. Import `database.sql` into MySQL.
2. Configure credentials via environment variables or edit `src/Database.php` defaults.
3. Login: **admin** / **admin123**
4. Use the dashboard to manage students, courses, enrolments and grades stored in the database.

## Class responsibilities (summary)

- **Student** – identity, programme, year; enrols in courses; records marks; produces transcript & GPA.
- **Course** – code, name, credit hours.
- **Enrolment** – composition link between Student and Course; holds mark and Grade.
- **Grade** – converts numeric mark (0–100) to letter grade (A, B+, B, C+, C, D, F).
- **Database** – PDO connection singleton for the optional persistent store.
