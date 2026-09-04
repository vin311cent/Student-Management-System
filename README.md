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

The application entry point now sends visitors directly to the login page:

* `index.php` redirects to `Login.php`
* `index.html` also redirects to `Login.php`

This makes the homepage behave as a landing page for authentication instead of displaying a blank screen.

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

Database and unexpected application errors are also caught and handled gracefully.

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

Testing covers:

* Student class methods
* Course class methods
* Enrolment functionality
* Grade conversion
* Grade boundaries
* Static counter functionality
* GPA calculation
* Invalid input handling
* Credit-hour validation

### Grade Boundary Tests

The grade conversion is tested using boundary values including:

| Test Mark | Expected Grade |
| --------: | :------------: |
|         0 |        F       |
|        49 |        F       |
|        50 |        D       |
|        59 |        D       |
|        60 |        C       |
|        69 |        C       |
|        70 |        B       |
|        79 |        B       |
|        80 |        A       |
|       100 |        A       |

### Invalid Input Tests

The system is also tested using invalid values such as:

* `-1`
* `101`
* `"abc"`
* Empty marks
* Invalid grade letters
* Zero or negative credit hours
* Missing course information

The expected behaviour is for the system to reject the invalid input and provide an appropriate error message.

---

## Test Data

`TestData.php` provides realistic data for testing the system.

Test data includes:

* Valid student records
* Valid course records
* Valid enrolment records
* Valid marks
* Boundary marks
* Invalid marks
* Invalid or empty input values
* GPA test cases

This allows the system to be tested under both normal and exceptional conditions.

---

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
| BUG-009 | Reports          | Report functionality requires further implementation/testing | Pending |

Detailed information about these issues, their causes, fixes, and retesting results is documented in:

`Bug Report and Fix Documentation`

---

# Project Structure

A simplified project structure is shown below:

```text
Student-Management-System/
│
├── index.php
├── index.html
├── Login.php
├── dashboard.php
├── Student.php
├── Courses.php
├── Enrolment.php
├── Grades.php
├── AcademicSummary.php
├── Reports.php
├── Settings.php
│
├── src/
│   ├── Database.php
│   ├── Grade.php
│   └── ...
│
├── tests/
│   ├── ...
│   └── ...
│
├── TestData.php
├── GPA.php
├── style.css
│
└── README.md
```

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
5. Configure the database connection in:

```text
src/Database.php
```

6. Open the application through the local PHP server.
7. The application will redirect to `Login.php`.
8. Sign in using the administrator credentials.

---

# Demo Access

Use the following credentials to sign in:

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

* `tests/`
* `TestData.php`
* `GPA.php`
* Bug Report and Fix Documentation
* Test cases and testing evidence

---

# Conclusion

The Student Management System provides a centralized platform for managing student records, courses, enrolments, grades, academic summaries, and reports.

The QA and testing component helps ensure that the system produces accurate results, rejects invalid data, handles exceptions appropriately, and maintains reliable functionality across its different modules.
