# EduManage – Student Records Management System (MVC)

PHP 8+ / PDO / MySQL. Object-oriented, with a front controller, router, controllers, models, repositories and views.

## Run it

```bash
mysql -u root -p < database/database.sql      # creates DB students_records + sample data
php -S localhost:8000 -t public               # then open http://localhost:8000
```

* Admin login: `admin` / `admin123` (stored as a bcrypt hash in `config/config.php`)
* OOP demonstration (no database, no login): http://localhost:8000/demo
* DB credentials: edit `config/config.php` or set `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`.
* No MySQL handy? SQLite works too (needs `pdo_sqlite`):
  `sqlite3 database/app.sqlite < database/schema.sqlite.sql` then
  `DB_DRIVER=sqlite php -S localhost:8000 -t public`
* XAMPP: put the folder in `htdocs` and open `/sms-mvc/public/` (`public/.htaccess` handles routing).

## Structure

```
public/                  only folder exposed to the web
  index.php              FRONT CONTROLLER – every request enters here
  assets/style.css
config/
  config.php             DB + auth settings
  routes.php             URL -> Controller@action table
app/
  bootstrap.php          PSR-4 autoloader (App\ => app/) + helpers
  helpers.php            e(), url(), asset(), csrf_field()
  Core/                  Router, Request, Controller (base), View, Session, Auth, Database, Config
  Controllers/           Auth, Dashboard, Student, Course, Enrolment, Grade, Summary,
                         Transcript, Report, Settings, Demo
  Models/                Student, Course, Enrolment, Grade, Programme   (domain objects + validation)
  Repositories/          Student-, Course-, Enrolment-, ProgrammeRepository (all PDO/SQL lives here)
  Services/              ReportService (report figures from objects)
  Views/                 layouts/ (main, plain) + one folder per feature
database/database.sql         MySQL schema + sample data
database/schema.sqlite.sql    same schema for SQLite
tests/run.php                 domain tests:  php tests/run.php
```

## Request flow

`Browser -> public/index.php -> Router (matches config/routes.php, checks login)
 -> Controller action -> Repository (PDO) -> Model objects -> View -> HTML`

* **Model** – `Student`, `Course`, `Enrolment`, `Grade`, `Programme` hold data and business rules only.
* **Repository** – loads/saves models; the only place with SQL.
* **Controller** – reads input, calls models/repositories, flashes messages, redirects (POST-redirect-GET).
* **View** – plain templates; they only display what the controller passes in (all output escaped with `e()`).

## Where the assignment's OOP concepts live

| Concept | Where |
|---|---|
| Classes & objects | `Models/Student, Course, Enrolment, Grade` |
| Encapsulation + validation in setters | private properties and validating setters in every model (e.g. `Grade::setMark` rejects marks outside 0–100) |
| Constructors | `Student::__construct(firstName, lastName, programme, year)` |
| Composition | `Student` holds an array of `Enrolment`; `Enrolment` links `Student` and `Course` |
| Static members | `Student::$counter`, `generateStudentNumber()` -> `LGU-2026-001` |
| `getGrade()` letter scale | `Enrolment::getGrade()` via `Grade::convert()` (A, B+, B, C+, C, D, F) |
| `enrol()`, `recordMark()`, `getTranscript()` | `Student` |
| Exceptions | `InvalidArgumentException` for bad marks/duplicates, caught in controllers and `DemoController` |
| Stretch: PDO + weighted GPA | `Repositories/*`, `Student::calculateGpa()` |

## Features

Login/logout · dashboard · students (register, search, edit, delete) · courses (with programme link) ·
enrolment (enrol / remove, duplicates blocked) · grades (0–100 validated, letter grade + points) ·
academic summary with weighted GPA · printable transcript · reports with CSV export · programme settings ·
`/demo` page that exercises the OOP classes without a database.

Deleting a student also deletes their enrolments and marks (database `ON DELETE CASCADE`); the page asks for confirmation first.

## Tests

`php tests/run.php` checks the grade scale boundaries, validation in every setter, the static student-number counter,
duplicate enrolment, weighted GPA and the report figures. All SQL is standard enough to run on both MySQL and SQLite,
which is how the whole app (login, CSRF, every page, CSV export, cascades, HTML escaping) was exercised end to end.

## Notes on what changed from the page-per-file version

* One entry point instead of 12 scripts that each repeated session checks, SQL and HTML.
* GPA is now computed in one place (`Student::calculateGpa`) – the duplicate `GPA.php`, the CASE-expression GPA in the reports SQL and the copies of the grade map are gone.
* `SHOW COLUMNS` schema-guessing code removed; the schema in `database.sql` is the contract.
* Added CSRF tokens on every POST form, hashed admin password, and no raw DB error text shown to users.
* Student names are stored as first/last name (matching the DB); `getFullName()` joins them.
