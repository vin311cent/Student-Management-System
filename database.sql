
CREATE DATABASE IF NOT EXISTS students_records
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE students_records;

CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_number VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    programme VARCHAR(100) DEFAULT NULL,
    year_of_study INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_code VARCHAR(20) UNIQUE NOT NULL,
    course_name VARCHAR(100) NOT NULL,
    credit_hours INT NOT NULL CHECK (credit_hours > 0),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    grade VARCHAR(2) DEFAULT NULL,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    

    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    UNIQUE KEY unique_enrolment (student_id, course_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



INSERT INTO students (student_number, first_name, last_name, programme, year_of_study)
VALUES
('LGU-2026-001', 'Lewis', 'Chingwamari', 'Computer Science', 2),
('LGU-2026-002', 'Kieth', 'Tim', 'Computer Science', 3)
ON DUPLICATE KEY UPDATE student_number=student_number;

INSERT INTO courses (course_code, course_name, credit_hours)
VALUES
('CSC101', 'Programming Fundamentals', 3),
('CSC205', 'Database Systems', 3)
ON DUPLICATE KEY UPDATE course_code=course_code;

INSERT INTO enrollments (student_id, course_id, grade)
VALUES
(1, 1, 'A'),
(1, 2, 'B+'),
(2, 1, 'A-')
ON DUPLICATE KEY UPDATE student_id=student_id;
