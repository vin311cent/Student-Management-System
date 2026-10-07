-- SQLite version of the schema (used by the automated tests and for a no-MySQL demo).
-- Enable with: DB_DRIVER=sqlite DB_PATH=/path/to/app.sqlite
PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS students (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    student_number TEXT NOT NULL UNIQUE,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    programme TEXT DEFAULT NULL,
    year_of_study INTEGER DEFAULT 1,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS courses (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    course_code TEXT NOT NULL UNIQUE,
    course_name TEXT NOT NULL,
    credit_hours INTEGER NOT NULL CHECK (credit_hours > 0),
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS enrollments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    student_id INTEGER NOT NULL REFERENCES students(id) ON DELETE CASCADE,
    course_id INTEGER NOT NULL REFERENCES courses(id) ON DELETE RESTRICT,
    grade TEXT DEFAULT NULL,
    marks REAL DEFAULT NULL,
    enrolled_at TEXT DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (student_id, course_id)
);
CREATE TABLE IF NOT EXISTS programs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    program_name TEXT NOT NULL UNIQUE
);
CREATE TABLE IF NOT EXISTS program_courses (
    program_id INTEGER NOT NULL REFERENCES programs(id) ON DELETE CASCADE,
    course_id INTEGER NOT NULL REFERENCES courses(id) ON DELETE CASCADE,
    PRIMARY KEY (program_id, course_id)
);
INSERT OR IGNORE INTO programs (program_name) VALUES
('Computer Science'), ('Law'), ('Social Work'), ('Business'),
('Bachelor of Computer Science'), ('Bachelor of Information Technology');
