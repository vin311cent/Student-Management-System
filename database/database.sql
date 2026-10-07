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
    marks DECIMAL(5,2) DEFAULT NULL,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    UNIQUE KEY unique_enrolment (student_id, course_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    program_name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS program_courses (
    program_id INT NOT NULL,
    course_id INT NOT NULL,
    PRIMARY KEY (program_id, course_id),
    CONSTRAINT fk_program_courses_program FOREIGN KEY (program_id)
        REFERENCES programs(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_program_courses_course FOREIGN KEY (course_id)
        REFERENCES courses(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO programs (program_name) VALUES
('Computer Science'), ('Law'), ('Social Work'), ('Business'),
('Bachelor of Computer Science'), ('Bachelor of Information Technology');

INSERT INTO students (student_number, first_name, last_name, programme, year_of_study)
VALUES
('LGU-2026-001', 'Lewis', 'Chingwamari', 'Computer Science', 2),
('LGU-2026-002', 'Kieth', 'Tim', 'Computer Science', 3),
('LGU-2026-003', 'Lumpombwe', 'Mwansa', 'Bachelor of Information Technology', 1)
ON DUPLICATE KEY UPDATE student_number=student_number;

INSERT INTO courses (course_code, course_name, credit_hours)
VALUES
('CSC101', 'Programming Fundamentals', 3),
('CSC205', 'Database Systems', 3),
('CSC210', 'Web Programming 2', 4),
('MAT120', 'Discrete Mathematics', 3),
('ENG110', 'Academic Communication', 2)
ON DUPLICATE KEY UPDATE course_code=course_code;

INSERT INTO enrollments (student_id, course_id, grade, marks)
VALUES
(1, 1, 'A', 85.00),
(1, 2, 'B', 72.00),
(2, 1, 'B+', 78.00),
(3, 1, NULL, NULL),
(3, 2, NULL, NULL)
ON DUPLICATE KEY UPDATE student_id=student_id;
