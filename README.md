# Student Management System

A simple PHP-based Student Management System for managing students, courses, enrolments, grades, academic summaries, and reports. The system includes an administrator login and dashboard for accessing the different management modules.

## Features

The system currently provides the following functionality:

* Administrator login and authentication
* Student management
* Course management
* Student enrolment management
* Grade management
* Marks validation
* Automatic marks-to-grade conversion
* Academic summary
* Weighted GPA calculation
* Reports
* Database-driven student records
* Exception handling and input validation
* Unit testing and test data generation

---

## Recent Updates

### Application Entry Point

The application uses a single public front controller:

* `index.php?route=login` displays the login form.
* Other pages are dispatched through `index.php?route=...` after authentication.

This keeps routing, authentication, and request handling in one place.

### Admin Dashboard

After signing in, administrators are taken to a dedicated dashboard with:

* Welcome header
* Student records overview
* Course overview
* Enrolment overview
* Quick-action guidance
* Navigation to the major system modules

---

## System Modules

### 1. Students

The Student Management module is used to manage student records, including student identification and personal information.

### 2. Courses

The Course Management module manages available courses and their associated credit hours.

Credit hours are important for calculating a student's weighted GPA.

### 3. Enrolment

The Enrolment module connects students with the courses they are taking.

Each enrolment represents a student taking a particular course and can subsequently receive a mark and grade.

### 4. Grades

The Grade Management module allows administrators to enter marks for enrolled students.

Marks are validated to ensure that:

* The value is numeric
* Marks are not below `0`
* Marks are not above `100`

The system then automatically converts the mark into a letter grade.

Current grading scale:

|  Marks | Grade | Grade Point |
| -----: | :---: | ----------: |
| 80–100 |   A   |         4.0 |
|  70–79 |   B   |         3.0 |
|  60–69 |   C   |         2.0 |
|  50–59 |   D   |         1.0 |
|   0–49 |   F   |         0.0 |

> **Note:** The grading scale should be changed if a different grading scale is specified by the course or institution.

### 5. Academic Summary

The Academic Summary provides an overview of each student's academic progress.

It displays information such as:

* Student number
* Student name
* Number of enrolled courses
* Number of graded courses
* Total credit hours
* GPA

### 6. GPA Calculation

The system includes a GPA calculation component implemented in `GPA.php`.

GPA is calculated using the credit hours of each course:

**GPA = Σ(Grade Point × Credit Hours) ÷ Σ(Credit Hours)**

For example:

| Course      | Grade | Grade Point | Credit Hours | Quality Points |
| ----------- | :---: | ----------: | -----------: | -------------: |
| Programming |   A   |         4.0 |            3 |           12.0 |
| Mathematics |   B   |         3.0 |            4 |           12.0 |
| Database    |   A   |         4.0 |            3 |           12.0 |
| **Total**   |       |             |       **10** |       **36.0** |

Therefore:

`GPA = 36 ÷ 10 = 3.60`

The GPA calculation also validates credit hours and grade values before performing the calculation.

---

# QA and Testing

The project includes dedicated Quality Assurance and Testing functionality.

The QA/Testing Developer is responsible for verifying that the system works correctly, handling invalid inputs, testing individual components, generating test data, and documenting identified bugs and fixes.

## Exception Handling

The system uses exception handling to prevent invalid data from causing unexpected application failures.

`InvalidArgumentException` is used for invalid input such as:

* Non-numeric marks
* Marks below `0`
* Marks above `100`
* Invalid grade values
* Invalid credit hours
* Empty course data when calculating GPA

Controllers show validation and expected write errors to the user. Database
connectivity and unexpected failures still require a configured database and
should be monitored through the PHP/server error log.

Example:

```php
try {
    $grade = Grade::convert($marks);
} catch (InvalidArgumentException $e) {
    $error = $e->getMessage();
}
```

---

## Unit Testing

The `tests/` directory contains test cases for important system components.

The lightweight domain/service checks run without a database:

```powershell
php tests/run.php
```

The checks cover:

* Student class methods
* Course class methods
* Enrolment functionality
* Grade conversion
* Grade boundaries
* Weighted GPA calculation
* Invalid input handling
* Credit-hour validation

The tests cover grade boundaries, invalid marks, invalid domain values, and an
example weighted GPA. Database integration and browser workflows still need to
be tested against a configured local database.

# Bug Report and Fix Documentation

Testing identified several issues during development.

The major issues included:

| Bug ID  | Module           | Issue                                                        | Status  |
| ------- | ---------------- | ------------------------------------------------------------ | ------- |
| BUG-001 | Grades           | Invalid grades could be entered manually                     | Fixed   |
| BUG-002 | Grades           | Marks validation was missing                                 | Fixed   |
| BUG-003 | Grades           | Automatic marks-to-grade conversion was missing              | Fixed   |
| BUG-004 | Academic Summary | Student number was not included in the SQL query             | Fixed   |
| BUG-005 | Academic Summary | GPA was not calculated or displayed                          | Fixed   |
| BUG-006 | GPA              | Invalid credit hours could affect calculation                | Fixed   |
| BUG-007 | GPA              | Empty course records could cause invalid GPA calculations    | Fixed   |
| BUG-008 | Grades/Database  | Errors were not handled gracefully                           | Fixed   |
| BUG-009 | Reports          | Report page implemented; database integration testing remains | Testing |

Detailed information about these issues, their causes, fixes, and retesting results is documented in:

`Bug Report and Fix Documentation`

---

# Object-Oriented Architecture

The application uses a lightweight MVC and layered design. `index.php` is the
single public front controller; route values select controllers, which use
repositories and services before rendering a view.

Examples: `index.php?route=login`, `index.php?route=dashboard`, and
`index.php?route=academic-summary`. Do not link directly to controller or view
files.

```text
app/
├── Application.php
├── bootstrap.php
├── Controllers/       # HTTP request handling and validation
├── Core/              # Database, router, base controller, and view renderer
├── Domain/            # Student, Course, and Enrollment entities
├── Repositories/      # SQL persistence and read queries
└── Services/          # Authentication and grading rules
views/                 # PHP templates and shared layout
tests/                 # Dependency-free domain/service checks
```

Controllers depend on repositories and services rather than executing SQL
directly. Domain objects enforce invariants, and the templates render data
without accessing the database.

---

# Database Requirements

The system uses a relational database to store student management information.

The main entities include:

* Students
* Courses
* Enrolments

The `enrollments` table stores the student's mark and calculated grade.

The system requires the enrolment table to contain a marks field similar to:

```sql
marks DECIMAL(5,2) NULL
```

Courses should also contain a `credit_hours` field for GPA calculations.

---

# Installation and Setup

1. Install a PHP development environment such as XAMPP.
2. Start the Apache and MySQL services.
3. Copy the project into the web server directory.
4. Create/import the project database.
5. Configure the database connection using environment variables:

```text
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=students_records
DB_USER=root
DB_PASS=
```

Set `ADMIN_USERNAME` and `ADMIN_PASSWORD` in the environment to override the
demo login without changing source code.

6. Open the application through the local PHP server.
7. Sign in using the administrator credentials.

---

# Demo Access

Unless overridden using environment variables, use:

* **Username:** `admin`
* **Password:** `admin123`

> For a production system, default credentials should be changed and passwords should be securely hashed.

---

# QA Testing Checklist

Before considering the system ready, the following should be verified:

* [ ] Login works correctly
* [ ] Unauthenticated users cannot access protected pages
* [ ] Students can be added and managed
* [ ] Courses can be added and managed
* [ ] Credit hours are stored correctly
* [ ] Students can be enrolled in courses
* [ ] Valid marks can be entered
* [ ] Marks below 0 are rejected
* [ ] Marks above 100 are rejected
* [ ] Non-numeric marks are rejected
* [ ] Marks are converted to the correct grade
* [ ] Grades are saved correctly
* [ ] Student numbers display correctly
* [ ] Graded courses are counted correctly
* [ ] Total credit hours are calculated correctly
* [ ] GPA is calculated correctly
* [ ] Invalid credit hours are rejected
* [ ] GPA handles students without graded courses
* [ ] Database errors are handled gracefully
* [ ] Reports have been tested
* [ ] Unit tests pass successfully

---

# Contributors

The project is developed as a group project, with members responsible for different system components.

### Member 4 – QA/Testing Developer

Responsibilities include:

* Exception handling
* Input validation
* Unit testing
* Test data generation
* Grade conversion testing
* GPA implementation and testing
* Bug identification
* Bug fixing documentation
* Regression testing
* Verification of system functionality

Key deliverables:

* `tests/run.php`
* `GPA.php`
* Bug Report and Fix Documentation

---

# Conclusion

The Student Management System provides a centralized platform for managing student records, courses, enrolments, grades, academic summaries, and reports.

The QA and testing component helps ensure that the system produces accurate results, rejects invalid data, handles exceptions appropriately, and maintains reliable functionality across its different modules.
