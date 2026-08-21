-- ================================================================
-- E-CHANNELING CENTER - DATABASE SCHEMA
-- Run this file first in phpMyAdmin / MySQL CLI to create the DB
-- ================================================================

CREATE DATABASE IF NOT EXISTS echanneling_db;
USE echanneling_db;

-- ----------------------------------------------------------------
-- Table: doctors
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS doctors (
    doctor_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    specialization VARCHAR(100) NOT NULL,
    available_days VARCHAR(150) NOT NULL,      -- e.g. "Monday, Wednesday, Friday"
    available_times VARCHAR(100) NOT NULL,     -- e.g. "5.00 PM - 8.00 PM"
    channeling_fee DECIMAL(10,2) NOT NULL,
    profile_image VARCHAR(255) DEFAULT 'default-doctor.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ----------------------------------------------------------------
-- Table: appointments
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS appointments (
    appointment_id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT NOT NULL,
    patient_name VARCHAR(100) NOT NULL,
    patient_age INT NOT NULL,
    patient_contact VARCHAR(20) NOT NULL,
    appointment_date DATE NOT NULL,
    amount_paid DECIMAL(10,2) NOT NULL,
    payment_status ENUM('Pending','Paid','Cancelled') DEFAULT 'Pending',
    card_last4 VARCHAR(4) DEFAULT NULL,
    booked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(doctor_id) ON DELETE CASCADE
);

-- ----------------------------------------------------------------
-- Table: admins
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL   -- stored as password_hash()
);

-- NOTE: Default admin account is created by running setup_admin.php once
-- in your browser (see README). Do NOT insert a plain-text password here.

-- ----------------------------------------------------------------
-- Sample doctors
-- ----------------------------------------------------------------
INSERT INTO doctors (name, specialization, available_days, available_times, channeling_fee, profile_image) VALUES
('Dr. Nimal Perera', 'Cardiologist', 'Monday, Wednesday, Friday', '5.00 PM - 8.00 PM', 2500.00, 'default-doctor.png'),
('Dr. Kamala Silva', 'Dentist', 'Tuesday, Thursday, Saturday', '9.00 AM - 12.00 PM', 1500.00, 'default-doctor.png'),
('Dr. Ruwan Fernando', 'General Physician', 'Monday, Tuesday, Wednesday, Thursday, Friday', '6.00 PM - 9.00 PM', 1000.00, 'default-doctor.png'),
('Dr. Ayesha Jayasuriya', 'Pediatrician', 'Monday, Wednesday, Friday, Saturday', '2.00 PM - 5.00 PM', 2000.00, 'default-doctor.png'),
('Dr. Sunil Wickramasinghe', 'Cardiologist', 'Tuesday, Thursday', '4.00 PM - 7.00 PM', 3000.00, 'default-doctor.png'),
('Dr. Dilani Rathnayake', 'Dermatologist', 'Monday, Thursday, Saturday', '10.00 AM - 1.00 PM', 2200.00, 'default-doctor.png');
