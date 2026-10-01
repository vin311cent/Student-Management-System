# Student Management System

A PHP-based Student Management System for managing students, courses, enrolments, grades, academic summaries, transcripts, and reports. Includes administrator login and a full OOP demonstration script.

## Features

* Administrator login and authentication (`admin` / `admin123`)
* Student management (auto LGU-YYYY-NNN student numbers)
* Course management (validated credit hours, optional programme linking)
* Student enrolment management (duplicate enrolment prevention)
* Grade management with marks validation (0–100)
* Automatic marks-to-grade conversion: **A, B+, B, C+, C, D, F**
* Official transcript view with weighted GPA
* Academic summary with weighted GPA
* Reports module
* Settings module
* OOP demo at `index.php` (≥3 students, ≥3 courses each, exception handling)
* Classes: Student, Course, Enrolment, Grade, Database (composition, encapsulation, static counters)
* Database-driven records (MySQL / PDO)

## How to run

```bash
# Import database
mysql -u root -p < database.sql

# PHP built-in server
php -S localhost:8000
```

- **OOP demo (assignment main script):** http://localhost:8000/index.php  
- **Admin UI:** http://localhost:8000/Login.php — credentials `admin` / `admin123`

## Directory tree (unchanged layout)

```
Student-Management-System/
├── index.php              ← OOP demonstration
├── Login.php
├── dashboard.php
├── Student.php
├── add_student.php
├── Courses.php
├── Enrolment.php
├── Grades.php / grades.php
├── AcademicSummary.php
├── Transcript.php         ← official transcript + GPA
├── Reports.php
├── Settings.php / settings.php
├── GPA.php
├── style.css              ← design preserved
├── database.sql
├── src/
│   ├── autoload.php
│   ├── Student.php
│   ├── Course.php
│   ├── Enrolment.php
│   ├── Grade.php
│   └── Database.php
└── README.md
```

## Grading scale

| Marks  | Grade | Grade Point |
|-------:|:-----:|------------:|
| 80–100 |   A   |         4.0 |
| 75–79  |  B+   |         3.5 |
| 70–74  |   B   |         3.0 |
| 65–69  |  C+   |         2.5 |
| 60–64  |   C   |         2.0 |
| 50–59  |   D   |         1.0 |
|  0–49  |   F   |         0.0 |

Weighted GPA = Σ(grade point × credit hours) / Σ(credit hours).
