-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 15, 2026 at 08:21 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `guardvax`
--

-- --------------------------------------------------------

--
-- Table structure for table `admissions`
--

CREATE TABLE `admissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `patient_id` int(10) UNSIGNED NOT NULL,
  `department_id` int(10) UNSIGNED NOT NULL,
  `admitted_by` int(10) UNSIGNED NOT NULL,
  `room_number` varchar(20) DEFAULT NULL,
  `bed_number` varchar(20) DEFAULT NULL,
  `admission_date` datetime NOT NULL DEFAULT current_timestamp(),
  `discharge_date` datetime DEFAULT NULL,
  `reason` text NOT NULL,
  `diagnosis` text DEFAULT NULL,
  `status` enum('Admitted','Discharged','Transferred','Critical') NOT NULL DEFAULT 'Admitted',
  `discharge_notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admissions`
--

INSERT INTO `admissions` (`id`, `patient_id`, `department_id`, `admitted_by`, `room_number`, `bed_number`, `admission_date`, `discharge_date`, `reason`, `diagnosis`, `status`, `discharge_notes`, `created_at`, `notes`) VALUES
(1, 5, 2, 2, '201', 'A', '2026-03-01 14:30:00', '2026-03-05 10:00:00', 'Severe chest pain and shortness of breath', 'Acute myocardial infarction — stable', 'Discharged', 'Patient discharged with medications. Follow-up in 2 weeks.', '2026-04-13 08:56:38', NULL),
(2, 8, 4, 3, '305', 'B', '2026-03-10 09:00:00', '2026-03-14 11:00:00', 'High fever and difficulty breathing', 'Community-acquired pneumonia', 'Discharged', 'Completed antibiotic course. Chest X-ray clear.', '2026-04-13 08:56:38', NULL),
(3, 12, 6, 4, '401', 'A', '2026-03-15 16:00:00', '2026-03-17 09:00:00', 'Appendicitis — scheduled appendectomy', 'Acute appendicitis — post-op recovery', 'Discharged', 'Surgery successful. No complications.', '2026-04-13 08:56:38', NULL),
(4, 14, 3, 5, '302', 'C', '2026-03-20 11:00:00', '2026-03-25 10:30:00', 'Dengue fever — platelet monitoring', 'Dengue hemorrhagic fever Grade 1', 'Discharged', 'Platelet count normalized. Patient stable.', '2026-04-13 08:56:38', NULL),
(5, 18, 4, 6, '306', 'A', '2026-03-22 08:30:00', NULL, 'Uncontrolled hypertension and dizziness', NULL, 'Admitted', NULL, '2026-04-13 08:56:38', NULL),
(6, 21, 5, 7, '501', 'B', '2026-03-25 13:00:00', NULL, 'Labor and delivery — 38 weeks gestation', NULL, 'Admitted', NULL, '2026-04-13 08:56:38', NULL),
(7, 3, 4, 8, '307', 'D', '2026-03-28 10:00:00', NULL, 'Diabetic ketoacidosis — blood sugar 450', NULL, 'Critical', NULL, '2026-04-13 08:56:38', NULL),
(8, 25, 2, 9, '202', 'A', '2026-04-01 22:00:00', '2026-04-03 15:00:00', 'Road traffic accident — head trauma', 'Mild traumatic brain injury — observation', 'Discharged', 'CT scan normal. Patient advised bed rest.', '2026-04-13 08:56:38', NULL),
(9, 7, 4, 10, '308', 'B', '2026-04-05 09:30:00', NULL, 'Asthma exacerbation — nebulization needed', NULL, 'Admitted', NULL, '2026-04-13 08:56:38', NULL),
(10, 19, 1, 2, '101', 'A', '2026-04-07 14:00:00', NULL, 'Elective colonoscopy and GI evaluation', NULL, 'Admitted', NULL, '2026-04-13 08:56:38', NULL),
(11, 31, 4, 4, '309', 'A', '2026-03-05 10:00:00', '2026-03-08 09:00:00', 'Severe migraine and vomiting', 'Migraine with aura — managed conservatively', 'Discharged', 'Discharged with pain medication. Follow-up in 2 weeks.', '2026-04-13 08:56:38', NULL),
(12, 35, 2, 5, '203', 'B', '2026-03-12 14:00:00', '2026-03-14 11:00:00', 'Hypoglycemic episode — blood sugar 42', 'Severe hypoglycemia — insulin adjusted', 'Discharged', 'Medication adjusted. Dietary counseling done.', '2026-04-13 08:56:38', NULL),
(13, 41, 4, 6, '310', 'C', '2026-03-18 09:30:00', NULL, 'Uncontrolled asthma exacerbation', NULL, 'Admitted', NULL, '2026-04-13 08:56:38', NULL),
(14, 46, 5, 7, '502', 'A', '2026-03-22 08:00:00', NULL, 'Active labor — 39 weeks gestation', NULL, 'Admitted', NULL, '2026-04-13 08:56:38', NULL),
(15, 50, 4, 8, '311', 'B', '2026-04-01 16:00:00', '2026-04-04 10:00:00', 'UTI with high fever — 39.5°C', 'Complicated UTI — IV antibiotics completed', 'Discharged', 'Oral antibiotics to continue for 5 days.', '2026-04-13 08:56:38', NULL),
(16, 55, 4, 9, '312', 'A', '2026-04-05 11:00:00', NULL, 'Chest pain and palpitations', NULL, 'Critical', NULL, '2026-04-13 08:56:38', NULL),
(17, 101, 3, 112, '231', 'B', '2026-04-13 03:15:46', NULL, 'BROKEN', NULL, 'Admitted', NULL, '2026-04-13 03:15:46', 'hearted'),
(18, 101, 1, 112, '232', 'V', '2026-04-13 03:16:10', NULL, 'oUT', NULL, 'Admitted', NULL, '2026-04-13 03:16:10', 'na sya'),
(19, 101, 4, 112, '231', 'B', '2026-04-13 03:48:56', NULL, 'BROKEN', NULL, 'Admitted', NULL, '2026-04-13 03:48:56', 'huhuhuh');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(10) UNSIGNED NOT NULL,
  `patient_id` int(10) UNSIGNED NOT NULL,
  `department_id` int(10) UNSIGNED NOT NULL,
  `doctor_id` int(10) UNSIGNED DEFAULT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('Pending','Confirmed','Cancelled','Completed','No-show') NOT NULL DEFAULT 'Pending',
  `notes` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `department_id`, `doctor_id`, `appointment_date`, `appointment_time`, `reason`, `status`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 2, '2026-04-10', '09:00:00', 'Regular checkup and blood pressure monitoring', 'Confirmed', NULL, 2, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(2, 2, 3, 3, '2026-04-10', '10:00:00', 'Pediatric consultation for child health assessment', 'Confirmed', NULL, 3, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(3, 3, 4, 4, '2026-04-11', '08:30:00', 'Follow-up for hypertension management', 'Pending', NULL, 4, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(4, 4, 5, 5, '2026-04-11', '11:00:00', 'Prenatal checkup — 2nd trimester', 'Confirmed', NULL, 5, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(5, 5, 1, 6, '2026-04-12', '09:30:00', 'Diabetes management consultation', 'Pending', NULL, 6, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(6, 6, 2, 7, '2026-04-08', '14:00:00', 'Emergency — chest pain evaluation', 'Completed', NULL, 7, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(7, 7, 10, 8, '2026-04-13', '10:30:00', 'Booster vaccine consultation', 'Confirmed', NULL, 8, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(8, 8, 4, 9, '2026-04-14', '13:00:00', 'Thyroid disorder follow-up', 'Pending', NULL, 9, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(9, 9, 1, 10, '2026-04-14', '08:00:00', 'Annual physical examination', 'Confirmed', NULL, 10, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(10, 10, 7, 2, '2026-04-15', '11:30:00', 'Complete blood count request', 'Confirmed', NULL, 2, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(11, 11, 4, 3, '2026-04-15', '14:30:00', 'Respiratory infection follow-up', 'Pending', NULL, 3, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(12, 12, 6, 4, '2026-04-16', '09:00:00', 'Pre-operative assessment', 'Confirmed', NULL, 4, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(13, 13, 1, 5, '2026-04-16', '10:00:00', 'Routine wellness visit', 'Pending', NULL, 5, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(14, 14, 3, 6, '2026-04-17', '08:30:00', 'Geriatric consultation', 'Confirmed', NULL, 6, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(15, 15, 10, 7, '2026-04-17', '13:00:00', 'HPV vaccine schedule consultation', 'Confirmed', NULL, 7, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(16, 16, 4, 8, '2026-04-18', '09:30:00', 'Asthma management review', 'Pending', NULL, 8, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(17, 17, 5, 9, '2026-04-18', '11:00:00', 'OB-GYN regular checkup', 'Confirmed', NULL, 9, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(18, 18, 1, 10, '2026-04-19', '14:00:00', 'Blood sugar monitoring — diabetic patient', 'Pending', NULL, 10, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(19, 19, 4, 2, '2026-04-19', '10:30:00', 'Cardiac evaluation', 'Confirmed', NULL, 2, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(20, 20, 7, 3, '2026-04-20', '08:00:00', 'Urinalysis and CBC request', 'Confirmed', NULL, 3, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(21, 31, 1, 2, '2026-04-21', '09:00:00', 'Annual physical examination', 'Confirmed', NULL, 2, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(22, 32, 10, 3, '2026-04-21', '10:30:00', 'COVID-19 booster vaccine consultation', 'Pending', NULL, 3, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(23, 33, 4, 4, '2026-04-22', '08:00:00', 'Hypertension follow-up', 'Confirmed', NULL, 4, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(24, 34, 3, 5, '2026-04-22', '11:00:00', 'Child health assessment', 'Pending', NULL, 5, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(25, 35, 1, 6, '2026-04-23', '09:30:00', 'Diabetes management review', 'Confirmed', NULL, 6, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(26, 36, 5, 7, '2026-04-23', '14:00:00', 'OB-GYN prenatal checkup', 'Pending', NULL, 7, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(27, 37, 4, 8, '2026-04-24', '10:00:00', 'Asthma follow-up consultation', 'Confirmed', NULL, 8, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(28, 38, 7, 9, '2026-04-24', '13:30:00', 'Blood work request — CBC and lipid panel', 'Confirmed', NULL, 9, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(29, 39, 1, 10, '2026-04-25', '08:30:00', 'Routine wellness check', 'Pending', NULL, 10, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(30, 40, 4, 2, '2026-04-25', '11:00:00', 'Thyroid function follow-up', 'Confirmed', NULL, 2, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(31, 41, 6, 3, '2026-04-26', '09:00:00', 'Pre-surgical evaluation', 'Pending', NULL, 3, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(32, 42, 1, 4, '2026-04-26', '14:00:00', 'General consultation — fever and cough', 'Confirmed', NULL, 4, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(33, 43, 4, 5, '2026-04-27', '10:30:00', 'Respiratory infection follow-up', 'Pending', NULL, 5, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(34, 44, 3, 6, '2026-04-27', '08:00:00', 'Pediatric immunization schedule', 'Confirmed', NULL, 6, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(35, 45, 1, 7, '2026-04-28', '09:00:00', 'Blood pressure monitoring — hypertensive', 'Pending', NULL, 7, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(36, 46, 5, 8, '2026-04-28', '11:30:00', 'Prenatal checkup — 3rd trimester', 'Confirmed', NULL, 8, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(37, 47, 4, 9, '2026-04-29', '13:00:00', 'GERD and stomach pain consultation', 'Pending', NULL, 9, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(38, 48, 2, 10, '2026-04-29', '08:00:00', 'Emergency — severe headache evaluation', 'Completed', NULL, 10, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(39, 49, 7, 2, '2026-04-30', '10:00:00', 'Urinalysis and kidney function test', 'Confirmed', NULL, 2, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(40, 50, 10, 3, '2026-04-30', '14:30:00', 'Vaccine booster consultation', 'Pending', NULL, 3, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(41, 101, 8, 9, '2026-04-29', '09:00:00', 'rest', 'Pending', 'broken', 112, '2026-04-13 03:09:36', '2026-04-13 03:09:36'),
(42, 101, 6, 114, '2026-04-14', '09:00:00', 'PAPATULI', 'Pending', 'wala naman', 112, '2026-04-14 04:01:40', '2026-04-14 04:01:40');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `record_id` int(10) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `table_name`, `record_id`, `description`, `ip_address`, `created_at`) VALUES
(1, NULL, 'LOGIN_FAIL', NULL, NULL, 'Failed attempt for: admin@guardvax.com', '::1', '2026-04-13 02:59:44'),
(2, 1, 'LOGIN_SUCCESS', 'users', 1, 'Admin login — 2FA bypassed', '::1', '2026-04-13 03:02:46'),
(3, 1, 'DEPT_CREATED', 'departments', 11, 'Created: TRY', '::1', '2026-04-13 03:03:09'),
(4, 1, 'VACCINE_CREATED', 'vaccines', 11, 'Created: try', '::1', '2026-04-13 03:03:32'),
(5, 1, 'INVENTORY_ADDED', 'inventory', 26, 'Added: gloves (qty: 1234)', '::1', '2026-04-13 03:04:04'),
(6, 1, 'LOGOUT', NULL, NULL, NULL, '::1', '2026-04-13 03:04:27'),
(7, NULL, 'REGISTER', 'users', 112, 'New nurse registration: tisoyangelo31@gmail.com', '::1', '2026-04-13 03:05:00'),
(8, NULL, 'EMAIL_VERIFIED', 'users', 112, NULL, '::1', '2026-04-13 03:05:27'),
(9, 112, 'LOGIN_SUCCESS', 'users', 112, 'Logged in as nurse', '::1', '2026-04-13 03:06:01'),
(10, 112, 'PATIENT_CREATED', 'patients', 101, 'Registered: GVX-000101 — ANGELO GABRIEL TISOY', '::1', '2026-04-13 03:06:49'),
(11, 113, 'LOGIN_SUCCESS', 'users', 113, 'Logged in as patient', '::1', '2026-04-13 03:07:54'),
(12, 113, 'PASSWORD_CHANGED', 'users', 113, 'Patient changed own password', '::1', '2026-04-13 03:08:17'),
(13, 113, 'LOGOUT', NULL, NULL, NULL, '::1', '2026-04-13 03:08:25'),
(14, 112, 'LOGIN_SUCCESS', 'users', 112, 'Logged in as nurse', '::1', '2026-04-13 03:08:48'),
(15, 112, 'APPOINTMENT_CREATED', 'appointments', 41, 'Patient ID 101 — 2026-04-29 09:00', '::1', '2026-04-13 03:09:36'),
(16, 112, 'PATIENT_ADMITTED', 'admissions', 17, 'Patient ID 101 — Dept ID 3', '::1', '2026-04-13 03:15:46'),
(17, 112, 'PATIENT_ADMITTED', 'admissions', 18, 'Patient ID 101 — Dept ID 1', '::1', '2026-04-13 03:16:10'),
(18, 112, 'PATIENT_ADMITTED', 'admissions', 19, 'Patient ID 101 — Dept ID 4', '::1', '2026-04-13 03:48:56'),
(19, NULL, 'LOGOUT', NULL, NULL, NULL, '::1', '2026-04-13 04:20:41'),
(20, 113, 'LOGIN_SUCCESS', 'users', 113, 'Logged in as patient', '::1', '2026-04-13 05:58:12'),
(21, NULL, 'LOGOUT', NULL, NULL, NULL, '::1', '2026-04-13 09:08:38'),
(22, NULL, 'LOGOUT', NULL, NULL, NULL, '::1', '2026-04-13 09:29:24'),
(23, 112, 'LOGIN_SUCCESS', 'users', 112, 'Logged in as nurse', '::1', '2026-04-13 09:29:56'),
(24, 112, 'LOGOUT', NULL, NULL, NULL, '::1', '2026-04-13 09:37:27'),
(25, 112, 'LOGIN_SUCCESS', 'users', 112, 'Logged in as nurse', '::1', '2026-04-13 09:46:48'),
(26, 112, 'RECORD_ADDED', 'medical_records', 31, 'Type: Diagnosis for patient ID 101', '::1', '2026-04-13 09:53:11'),
(27, 112, 'LOGOUT', NULL, NULL, NULL, '::1', '2026-04-13 09:54:28'),
(28, 113, 'LOGIN_SUCCESS', 'users', 113, 'Logged in as patient', '::1', '2026-04-13 09:56:06'),
(29, 113, 'LOGOUT', NULL, NULL, NULL, '::1', '2026-04-13 10:29:33'),
(30, 1, 'LOGIN_SUCCESS', 'users', 1, 'Admin login — 2FA bypassed', '::1', '2026-04-13 10:29:58'),
(31, 1, 'REPORT_GENERATED', 'patients', NULL, 'Patient list PDF generated', '::1', '2026-04-13 10:34:37'),
(32, 113, 'LOGIN_SUCCESS', 'users', 113, 'Logged in as patient', '::1', '2026-04-13 12:58:07'),
(33, 113, 'LOGOUT', NULL, NULL, NULL, '::1', '2026-04-13 12:58:18'),
(34, 112, 'LOGIN_SUCCESS', 'users', 112, 'Logged in as nurse', '::1', '2026-04-13 12:58:56'),
(35, 112, 'VACCINATION_ADDED', 'vaccinations', 103, 'Vaccine ID 7 — Patient ID 101 — Dose 4', '::1', '2026-04-13 12:59:55'),
(36, 112, 'PRESCRIPTION_ADDED', 'prescriptions', 31, 'metformin for Patient ID 101', '::1', '2026-04-13 13:00:25'),
(37, 112, 'LAB_REQUESTED', 'lab_results', 31, 'Blood test for Patient ID 101', '::1', '2026-04-13 13:00:48'),
(38, 112, 'RECORD_ADDED', 'medical_records', 32, 'Type: Lab Result for patient ID 101', '::1', '2026-04-13 13:01:09'),
(39, 112, 'LOGOUT', NULL, NULL, NULL, '::1', '2026-04-13 13:01:24'),
(40, 113, 'LOGIN_SUCCESS', 'users', 113, 'Logged in as patient', '::1', '2026-04-13 13:02:00'),
(41, 113, 'LOGOUT', NULL, NULL, NULL, '::1', '2026-04-13 13:34:09'),
(42, NULL, 'LOGOUT', NULL, NULL, NULL, '::1', '2026-04-13 14:59:09'),
(43, 113, 'LOGIN_SUCCESS', 'users', 113, 'Logged in as patient', '::1', '2026-04-14 01:11:54'),
(44, 113, 'REPORT_GENERATED', 'patients', 101, 'Patient PDF report for: GVX-000101', '::1', '2026-04-14 02:07:02'),
(45, 112, 'LOGIN_SUCCESS', 'users', 112, 'Logged in as nurse', '::1', '2026-04-14 03:56:06'),
(46, 113, 'LOGIN_SUCCESS', 'users', 113, 'Logged in as patient', '::1', '2026-04-14 03:58:06'),
(47, 112, 'LOGIN_SUCCESS', 'users', 112, 'Logged in as nurse', '::1', '2026-04-14 04:00:19'),
(48, 112, 'APPOINTMENT_CREATED', 'appointments', 42, 'Patient ID 101 — 2026-04-14 09:00', '::1', '2026-04-14 04:01:40'),
(49, 1, 'LOGIN_SUCCESS', 'users', 1, 'Admin login — 2FA bypassed', '::1', '2026-04-14 04:03:55'),
(50, 1, 'BILL_CREATED', 'billing', 26, 'Bill BILL-2026-00026 — Total ₱5,000.00', '::1', '2026-04-14 04:06:11'),
(51, 1, 'BILL_CREATED', 'billing', 27, 'Bill BILL-2026-00027 — Total ₱0.00', '::1', '2026-04-14 04:06:58'),
(52, NULL, 'LOGOUT', NULL, NULL, NULL, '::1', '2026-04-14 06:23:26'),
(53, 112, 'LOGIN_SUCCESS', 'users', 112, 'Logged in as nurse', '::1', '2026-04-14 06:25:48'),
(54, 113, 'LOGIN_SUCCESS', 'users', 113, 'Logged in as patient', '::1', '2026-04-14 06:46:59'),
(55, 1, 'LOGIN_SUCCESS', 'users', 1, 'Admin login — 2FA bypassed', '::1', '2026-04-14 06:47:29'),
(56, 112, 'VACCINATION_ADDED', 'vaccinations', 104, 'Vaccine ID 10 — Patient ID 101 — Dose 4', '::1', '2026-04-14 06:49:49'),
(57, 113, 'LOGOUT', NULL, NULL, NULL, '::1', '2026-04-14 06:59:29');

-- --------------------------------------------------------

--
-- Table structure for table `billing`
--

CREATE TABLE `billing` (
  `id` int(10) UNSIGNED NOT NULL,
  `patient_id` int(10) UNSIGNED NOT NULL,
  `admission_id` int(10) UNSIGNED DEFAULT NULL,
  `bill_number` varchar(20) NOT NULL,
  `consultation_fee` decimal(10,2) DEFAULT 0.00,
  `medicine_fee` decimal(10,2) DEFAULT 0.00,
  `lab_fee` decimal(10,2) DEFAULT 0.00,
  `room_fee` decimal(10,2) DEFAULT 0.00,
  `other_fee` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `discount` decimal(10,2) DEFAULT 0.00,
  `amount_paid` decimal(10,2) DEFAULT 0.00,
  `payment_method` enum('Cash','PhilHealth','HMO','Credit Card','Other') DEFAULT NULL,
  `status` enum('Unpaid','Partial','Paid','Waived') NOT NULL DEFAULT 'Unpaid',
  `notes` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `paid_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `billing`
--

INSERT INTO `billing` (`id`, `patient_id`, `admission_id`, `bill_number`, `consultation_fee`, `medicine_fee`, `lab_fee`, `room_fee`, `other_fee`, `total_amount`, `discount`, `amount_paid`, `payment_method`, `status`, `notes`, `created_by`, `paid_at`, `created_at`) VALUES
(1, 5, NULL, 'BILL-2026-00001', 500.00, 2500.00, 1800.00, 12000.00, 500.00, 17300.00, 0.00, 17300.00, 'PhilHealth', 'Paid', 'PhilHealth covered hospitalization', 2, NULL, '2026-04-13 08:56:38'),
(2, 8, NULL, 'BILL-2026-00002', 500.00, 3200.00, 2500.00, 16000.00, 800.00, 23000.00, 500.00, 22500.00, 'Cash', 'Paid', 'Discount for senior citizen', 3, NULL, '2026-04-13 08:56:38'),
(3, 12, NULL, 'BILL-2026-00003', 1500.00, 5000.00, 1500.00, 8000.00, 2000.00, 18000.00, 0.00, 10000.00, 'Cash', 'Partial', 'Partial payment. Balance pending', 4, NULL, '2026-04-13 08:56:38'),
(4, 14, NULL, 'BILL-2026-00004', 500.00, 1800.00, 3200.00, 20000.00, 500.00, 26000.00, 2600.00, 23400.00, 'HMO', 'Paid', 'HMO covered 90%', 5, NULL, '2026-04-13 08:56:38'),
(5, 25, NULL, 'BILL-2026-00005', 800.00, 500.00, 2000.00, 8000.00, 300.00, 11600.00, 0.00, 11600.00, 'Cash', 'Paid', 'Emergency admission fully paid', 6, NULL, '2026-04-13 08:56:38'),
(6, 1, NULL, 'BILL-2026-00006', 300.00, 650.00, 0.00, 0.00, 0.00, 950.00, 0.00, 950.00, 'Cash', 'Paid', 'Outpatient consultation', 7, NULL, '2026-04-13 08:56:38'),
(7, 2, NULL, 'BILL-2026-00007', 300.00, 320.00, 500.00, 0.00, 0.00, 1120.00, 0.00, 0.00, NULL, 'Unpaid', 'Awaiting payment', 8, NULL, '2026-04-13 08:56:38'),
(8, 3, NULL, 'BILL-2026-00008', 300.00, 500.00, 1800.00, 0.00, 0.00, 2600.00, 0.00, 1300.00, 'Cash', 'Partial', 'Partial payment received', 9, NULL, '2026-04-13 08:56:38'),
(9, 6, NULL, 'BILL-2026-00009', 300.00, 750.00, 0.00, 0.00, 0.00, 1050.00, 0.00, 1050.00, 'Cash', 'Paid', 'Outpatient visit', 10, NULL, '2026-04-13 08:56:38'),
(10, 9, NULL, 'BILL-2026-00010', 300.00, 200.00, 350.00, 0.00, 0.00, 850.00, 0.00, 850.00, 'Cash', 'Paid', 'Consultation and lab', 2, NULL, '2026-04-13 08:56:38'),
(11, 10, NULL, 'BILL-2026-00011', 300.00, 0.00, 1500.00, 0.00, 0.00, 1800.00, 0.00, 0.00, NULL, 'Unpaid', 'Lab fees pending', 3, NULL, '2026-04-13 08:56:38'),
(12, 13, NULL, 'BILL-2026-00012', 300.00, 850.00, 0.00, 0.00, 0.00, 1150.00, 0.00, 1150.00, '', 'Paid', 'Paid via GCash', 4, NULL, '2026-04-13 08:56:38'),
(13, 16, NULL, 'BILL-2026-00013', 300.00, 1200.00, 800.00, 0.00, 0.00, 2300.00, 0.00, 0.00, NULL, 'Unpaid', 'Pending payment', 5, NULL, '2026-04-13 08:56:38'),
(14, 20, NULL, 'BILL-2026-00014', 300.00, 300.00, 2000.00, 0.00, 0.00, 2600.00, 0.00, 2600.00, 'Cash', 'Paid', 'Outpatient lab and consult', 6, NULL, '2026-04-13 08:56:38'),
(15, 18, NULL, 'BILL-2026-00015', 300.00, 1800.00, 1500.00, 0.00, 0.00, 3600.00, 0.00, 1800.00, 'PhilHealth', 'Partial', 'PhilHealth partial coverage', 7, NULL, '2026-04-13 08:56:38'),
(16, 31, NULL, 'BILL-2026-00016', 500.00, 800.00, 3500.00, 8000.00, 500.00, 13300.00, 0.00, 13300.00, 'PhilHealth', 'Paid', 'MRI and hospitalization covered by PhilHealth', 4, NULL, '2026-04-13 08:56:38'),
(17, 32, NULL, 'BILL-2026-00017', 300.00, 350.00, 500.00, 0.00, 0.00, 1150.00, 0.00, 1150.00, 'Cash', 'Paid', 'Outpatient consultation and lab', 5, NULL, '2026-04-13 08:56:38'),
(18, 33, NULL, 'BILL-2026-00018', 300.00, 750.00, 1200.00, 0.00, 0.00, 2250.00, 0.00, 0.00, NULL, 'Unpaid', 'Awaiting payment', 6, NULL, '2026-04-13 08:56:38'),
(19, 34, NULL, 'BILL-2026-00019', 300.00, 950.00, 800.00, 0.00, 0.00, 2050.00, 0.00, 2050.00, 'HMO', 'Paid', 'HMO fully covered', 7, NULL, '2026-04-13 08:56:38'),
(20, 35, NULL, 'BILL-2026-00020', 500.00, 600.00, 1500.00, 6000.00, 300.00, 8900.00, 890.00, 8010.00, 'Cash', 'Paid', '10% senior citizen discount applied', 8, NULL, '2026-04-13 08:56:38'),
(21, 36, NULL, 'BILL-2026-00021', 300.00, 450.00, 900.00, 0.00, 0.00, 1650.00, 0.00, 825.00, 'Cash', 'Partial', 'Partial payment. Follow up for balance.', 9, NULL, '2026-04-13 08:56:38'),
(22, 37, NULL, 'BILL-2026-00022', 300.00, 1200.00, 500.00, 0.00, 0.00, 2000.00, 0.00, 2000.00, 'Cash', 'Paid', 'Outpatient asthma consultation', 10, NULL, '2026-04-13 08:56:38'),
(23, 38, NULL, 'BILL-2026-00023', 1500.00, 0.00, 2500.00, 0.00, 0.00, 4000.00, 0.00, 4000.00, 'Credit Card', 'Paid', 'Executive checkup package', 2, NULL, '2026-04-13 08:56:38'),
(24, 39, NULL, 'BILL-2026-00024', 300.00, 200.00, 1500.00, 0.00, 0.00, 2000.00, 0.00, 0.00, NULL, 'Unpaid', 'Pending payment from patient', 3, NULL, '2026-04-13 08:56:38'),
(25, 40, NULL, 'BILL-2026-00025', 300.00, 650.00, 1800.00, 0.00, 0.00, 2750.00, 0.00, 2750.00, 'PhilHealth', 'Paid', 'PhilHealth outpatient benefit used', 4, NULL, '2026-04-13 08:56:38'),
(26, 101, 18, 'BILL-2026-00026', 5000.00, 0.00, 0.00, 0.00, 0.00, 5000.00, 0.00, 0.00, 'Cash', 'Unpaid', '', 1, NULL, '2026-04-14 04:06:11'),
(27, 101, NULL, 'BILL-2026-00027', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 4999.99, NULL, 'Paid', '', 1, '2026-04-14 04:06:58', '2026-04-14 04:06:58');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `description`, `location`, `is_active`, `created_at`) VALUES
(1, 'Outpatient', 'General outpatient consultations', 'Building A, Ground Floor', 1, '2026-04-13 08:56:24'),
(2, 'Emergency', '24/7 emergency services', 'Building A, Ground Floor', 1, '2026-04-13 08:56:24'),
(3, 'Pediatrics', 'Child health and development', 'Building B, 2nd Floor', 1, '2026-04-13 08:56:24'),
(4, 'Internal Medicine', 'Adult internal medicine', 'Building B, 3rd Floor', 1, '2026-04-13 08:56:24'),
(5, 'Obstetrics', 'Maternal and prenatal care', 'Building C, 2nd Floor', 1, '2026-04-13 08:56:24'),
(6, 'Surgery', 'Surgical procedures and recovery', 'Building C, 3rd Floor', 1, '2026-04-13 08:56:24'),
(7, 'Laboratory', 'Diagnostic laboratory services', 'Building A, 1st Floor', 1, '2026-04-13 08:56:24'),
(8, 'Pharmacy', 'Medicines and pharmaceutical services', 'Building A, Ground Floor', 1, '2026-04-13 08:56:24'),
(9, 'Radiology', 'X-ray, ultrasound, and imaging', 'Building D, 1st Floor', 1, '2026-04-13 08:56:24'),
(10, 'Vaccination Unit', 'Immunization and vaccination services', 'Building A, 1st Floor', 1, '2026-04-13 08:56:24'),
(11, 'TRY', 'try lang', 'Building A, Fround Floor', 1, '2026-04-13 03:03:09');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(10) UNSIGNED NOT NULL,
  `item_name` varchar(200) NOT NULL,
  `category` enum('Medicine','Vaccine','Medical Supply','Equipment','Other') NOT NULL DEFAULT 'Medicine',
  `description` text DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `unit` varchar(50) NOT NULL DEFAULT 'pcs',
  `unit_price` decimal(10,2) DEFAULT NULL,
  `reorder_level` int(11) NOT NULL DEFAULT 10,
  `expiry_date` date DEFAULT NULL,
  `supplier` varchar(200) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `item_name`, `category`, `description`, `quantity`, `unit`, `unit_price`, `reorder_level`, `expiry_date`, `supplier`, `location`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Paracetamol 500mg', 'Medicine', NULL, 500, 'tablets', 2.50, 100, NULL, NULL, NULL, 1, '2026-04-13 08:56:24', '2026-04-13 08:56:24'),
(2, 'Amoxicillin 500mg', 'Medicine', NULL, 300, 'capsules', 8.00, 50, NULL, NULL, NULL, 1, '2026-04-13 08:56:24', '2026-04-13 08:56:24'),
(3, 'Ibuprofen 400mg', 'Medicine', NULL, 400, 'tablets', 5.00, 100, NULL, NULL, NULL, 1, '2026-04-13 08:56:24', '2026-04-13 08:56:24'),
(4, 'Metformin 500mg', 'Medicine', NULL, 200, 'tablets', 4.00, 50, NULL, NULL, NULL, 1, '2026-04-13 08:56:24', '2026-04-13 08:56:24'),
(5, 'Amlodipine 5mg', 'Medicine', NULL, 150, 'tablets', 6.50, 50, NULL, NULL, NULL, 1, '2026-04-13 08:56:24', '2026-04-13 08:56:24'),
(6, 'Disposable Gloves', 'Medical Supply', NULL, 1000, 'pairs', 3.00, 200, NULL, NULL, NULL, 1, '2026-04-13 08:56:24', '2026-04-13 08:56:24'),
(7, 'Surgical Mask', 'Medical Supply', NULL, 2000, 'pcs', 2.00, 500, NULL, NULL, NULL, 1, '2026-04-13 08:56:24', '2026-04-13 08:56:24'),
(8, 'Syringe 5ml', 'Medical Supply', NULL, 800, 'pcs', 5.00, 200, NULL, NULL, NULL, 1, '2026-04-13 08:56:24', '2026-04-13 08:56:24'),
(9, 'IV Fluids (PNSS)', 'Medical Supply', NULL, 200, 'bags', 85.00, 50, NULL, NULL, NULL, 1, '2026-04-13 08:56:24', '2026-04-13 08:56:24'),
(10, 'Blood Glucose Strips', 'Medical Supply', NULL, 300, 'pcs', 12.00, 100, NULL, NULL, NULL, 1, '2026-04-13 08:56:24', '2026-04-13 08:56:24'),
(11, 'Amoxicillin 500mg caps', 'Medicine', NULL, 800, 'capsules', 8.50, 100, '2027-06-30', 'Unilab Philippines', NULL, 1, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(12, 'Amlodipine 5mg tabs', 'Medicine', NULL, 600, 'tablets', 6.50, 100, '2027-12-31', 'Pfizer Philippines', NULL, 1, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(13, 'Metformin 500mg tabs', 'Medicine', NULL, 1000, 'tablets', 4.00, 200, '2027-09-30', 'Merck Philippines', NULL, 1, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(14, 'Omeprazole 20mg caps', 'Medicine', NULL, 500, 'capsules', 7.50, 100, '2027-03-31', 'AstraZeneca', NULL, 1, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(15, 'Cetirizine 10mg tabs', 'Medicine', NULL, 400, 'tablets', 5.00, 100, '2026-12-31', 'UCB Philippines', NULL, 1, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(16, 'Salbutamol Inhaler', 'Medicine', NULL, 50, 'pieces', 285.00, 20, '2027-06-30', 'GSK Philippines', NULL, 1, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(17, 'Losartan 50mg tabs', 'Medicine', NULL, 300, 'tablets', 9.00, 100, '2027-06-30', 'Merck Philippines', NULL, 1, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(18, 'Bandage Roll 2 inch', 'Medical Supply', NULL, 200, 'rolls', 18.00, 50, NULL, 'Mediline Supply', NULL, 1, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(19, 'Alcohol 70% 500ml', 'Medical Supply', NULL, 100, 'bottles', 45.00, 30, NULL, 'RiteMed', NULL, 1, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(20, 'Cotton Balls 200pcs', 'Medical Supply', NULL, 150, 'packs', 35.00, 30, NULL, 'Mediline Supply', NULL, 1, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(21, 'Thermometer Digital', 'Equipment', NULL, 20, 'pieces', 250.00, 5, NULL, 'Omron Philippines', NULL, 1, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(22, 'Blood Pressure Monitor', 'Equipment', NULL, 10, 'pieces', 1800.00, 3, NULL, 'Omron Philippines', NULL, 1, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(23, 'Pulse Oximeter', 'Equipment', NULL, 15, 'pieces', 450.00, 5, NULL, 'Contec Medical', NULL, 1, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(24, 'Nebulizer Machine', 'Equipment', NULL, 8, 'pieces', 2500.00, 2, NULL, 'APEX Medical', NULL, 1, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(25, 'Stethoscope', 'Equipment', NULL, 12, 'pieces', 1200.00, 3, NULL, 'Littmann Philippines', NULL, 1, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(26, 'gloves', 'Medical Supply', '', 1234, 'pcs', 10.00, 10, '2030-01-16', 'GELo', 'Building A, Fround Floor', 1, '2026-04-13 03:04:04', '2026-04-13 03:04:04');

-- --------------------------------------------------------

--
-- Table structure for table `lab_results`
--

CREATE TABLE `lab_results` (
  `id` int(10) UNSIGNED NOT NULL,
  `patient_id` int(10) UNSIGNED NOT NULL,
  `requested_by` int(10) UNSIGNED NOT NULL,
  `admission_id` int(10) UNSIGNED DEFAULT NULL,
  `test_name` varchar(200) NOT NULL,
  `test_category` enum('Blood','Urine','Imaging','Microbiology','Chemistry','Other') NOT NULL DEFAULT 'Other',
  `result` text DEFAULT NULL,
  `normal_range` varchar(100) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `status` enum('Requested','Processing','Completed','Cancelled') NOT NULL DEFAULT 'Requested',
  `is_abnormal` tinyint(1) DEFAULT 0,
  `notes` text DEFAULT NULL,
  `requested_date` date NOT NULL,
  `result_date` date DEFAULT NULL,
  `performed_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_results`
--

INSERT INTO `lab_results` (`id`, `patient_id`, `requested_by`, `admission_id`, `test_name`, `test_category`, `result`, `normal_range`, `unit`, `status`, `is_abnormal`, `notes`, `requested_date`, `result_date`, `performed_by`, `created_at`) VALUES
(1, 1, 2, NULL, 'Complete Blood Count (CBC)', 'Blood', '4.8 x10^9/L WBC, 13.5 g/dL Hgb, 150 x10^9 Plt', '4.0-11.0, 12-16, 150-400', 'various', 'Completed', 0, NULL, '2026-01-10', '2026-01-11', 3, '2026-04-13 08:56:38'),
(2, 2, 3, NULL, 'Blood Glucose Fasting', 'Blood', '95', '70-100', 'mg/dL', 'Completed', 0, NULL, '2026-01-12', '2026-01-12', 4, '2026-04-13 08:56:38'),
(3, 3, 4, NULL, 'HbA1c', 'Blood', '8.2', '4.0-5.6', '%', 'Completed', 1, NULL, '2026-01-20', '2026-01-21', 5, '2026-04-13 08:56:38'),
(4, 4, 5, NULL, 'Urinalysis', 'Urine', 'Color: Yellow, Clarity: Clear, pH: 6.5, Protein: Neg', 'Normal ranges', 'N/A', 'Completed', 0, NULL, '2026-02-05', '2026-02-05', 6, '2026-04-13 08:56:38'),
(5, 5, 6, NULL, 'Lipid Panel', 'Blood', 'TC: 245, LDL: 165, HDL: 42, TG: 190', '<200, <100, >40, <150', 'mg/dL', 'Completed', 1, NULL, '2025-12-01', '2025-12-02', 7, '2026-04-13 08:56:38'),
(6, 6, 7, NULL, 'Chest X-Ray', 'Imaging', 'No active infiltrates. Heart size normal. Clear lung fields', 'Normal', 'N/A', 'Completed', 0, NULL, '2026-01-08', '2026-01-09', 8, '2026-04-13 08:56:38'),
(7, 7, 8, NULL, 'Pulmonary Function Test', 'Other', 'FEV1: 68%, FVC: 82%, FEV1/FVC: 0.72 — Mild obstruction', 'FEV1/FVC >0.70', 'ratio', 'Completed', 1, NULL, '2026-01-14', '2026-01-15', 9, '2026-04-13 08:56:38'),
(8, 8, 9, NULL, 'Thyroid Function Test (TSH)', 'Blood', '6.8', '0.4-4.0', 'mIU/L', 'Completed', 1, NULL, '2026-01-30', '2026-01-31', 10, '2026-04-13 08:56:38'),
(9, 9, 10, NULL, 'Uric Acid', 'Blood', '5.2', '3.5-7.2', 'mg/dL', 'Completed', 0, NULL, '2026-02-18', '2026-02-19', 2, '2026-04-13 08:56:38'),
(10, 10, 2, NULL, 'Creatinine', 'Blood', '0.9', '0.6-1.2', 'mg/dL', 'Completed', 0, NULL, '2025-10-15', '2025-10-16', 3, '2026-04-13 08:56:38'),
(11, 11, 3, NULL, 'Sputum Culture', 'Microbiology', 'Streptococcus pneumoniae — sensitive to Amoxicillin', 'No growth', 'N/A', 'Completed', 1, NULL, '2026-01-22', '2026-01-25', 4, '2026-04-13 08:56:38'),
(12, 12, 4, NULL, 'Pre-operative Blood Typing', 'Blood', 'Blood type: B+, Crossmatch compatible', 'N/A', 'N/A', 'Completed', 0, NULL, '2026-03-15', '2026-03-15', 5, '2026-04-13 08:56:38'),
(13, 13, 5, NULL, 'Blood Pressure Monitoring (24hr)', 'Other', 'Average: 145/92 mmHg. Diurnal pattern normal', '<120/80 mmHg', 'mmHg', 'Completed', 1, NULL, '2026-03-01', '2026-03-02', 6, '2026-04-13 08:56:38'),
(14, 14, 6, NULL, 'Dengue NS1 Antigen', 'Blood', 'Positive', 'Negative', 'N/A', 'Completed', 1, NULL, '2026-03-20', '2026-03-20', 7, '2026-04-13 08:56:38'),
(15, 15, 7, NULL, 'Pap Smear', 'Other', 'NILM — No intraepithelial lesion or malignancy', 'NILM', 'N/A', 'Completed', 0, NULL, '2026-02-01', '2026-02-03', 8, '2026-04-13 08:56:38'),
(16, 16, 8, NULL, 'Spirometry', 'Other', 'Moderate obstructive pattern. FEV1: 62%', 'FEV1 >80%', '%', 'Completed', 1, NULL, '2026-02-25', '2026-02-26', 9, '2026-04-13 08:56:38'),
(17, 17, 9, NULL, 'Prenatal Panel', 'Blood', 'Hgb: 11.8 g/dL, Blood type: O+, VDRL: Non-reactive', 'Hgb >11, Non-reactive', 'various', 'Completed', 0, NULL, '2026-03-05', '2026-03-06', 10, '2026-04-13 08:56:38'),
(18, 18, 10, NULL, 'Fasting Blood Sugar', 'Blood', '186', '70-100', 'mg/dL', 'Completed', 1, NULL, '2026-02-28', '2026-02-28', 2, '2026-04-13 08:56:38'),
(19, 19, 2, NULL, 'ECG (12-lead)', 'Other', 'Normal sinus rhythm. QRS: 80ms. No ST changes', 'Normal sinus rhythm', 'N/A', 'Completed', 0, NULL, '2026-04-07', '2026-04-07', 3, '2026-04-13 08:56:38'),
(20, 20, 3, NULL, 'Urinalysis + Culture', 'Urine', 'WBC: 15/hpf — Mild UTI. E.coli sensitive to Ciprofloxacin', 'WBC <5/hpf', '/hpf', 'Completed', 1, NULL, '2026-01-28', '2026-01-29', 4, '2026-04-13 08:56:38'),
(21, 31, 4, NULL, 'MRI Brain', 'Imaging', 'No intracranial hemorrhage. Normal brain parenchyma.', 'Normal', 'N/A', 'Completed', 0, NULL, '2026-03-05', '2026-03-06', 5, '2026-04-13 08:56:38'),
(22, 32, 5, NULL, 'Complete Blood Count', 'Blood', 'WBC: 9.2, Hgb: 13.8, Plt: 245 — All within normal', '4.0-11.0', 'x10^9', 'Completed', 0, NULL, '2026-01-08', '2026-01-09', 6, '2026-04-13 08:56:38'),
(23, 33, 6, NULL, 'Blood Pressure 24hr Holter', 'Other', 'Average 152/94 mmHg. No nocturnal dipping.', '<130/80', 'mmHg', 'Completed', 1, NULL, '2026-02-12', '2026-02-13', 7, '2026-04-13 08:56:38'),
(24, 34, 7, NULL, 'Chest X-Ray PA View', 'Imaging', 'Hyperinflation noted. Flattened diaphragm consistent with COPD.', 'Normal', 'N/A', 'Completed', 1, NULL, '2026-03-01', '2026-03-01', 8, '2026-04-13 08:56:38'),
(25, 35, 8, NULL, 'HbA1c', 'Blood', '7.8', '4.0-5.6', '%', 'Completed', 1, NULL, '2025-12-10', '2025-12-11', 9, '2026-04-13 08:56:38'),
(26, 36, 9, NULL, 'Iron Studies (Serum Ferritin)', 'Blood', '8.5', '15-200', 'ng/mL', 'Completed', 1, NULL, '2026-01-14', '2026-01-15', 10, '2026-04-13 08:56:38'),
(27, 37, 10, NULL, 'Peak Flow Measurement', 'Other', '320 L/min — 68% of predicted value', '>450 L/min', 'L/min', 'Completed', 1, NULL, '2026-03-01', '2026-03-01', 2, '2026-04-13 08:56:38'),
(28, 38, 2, NULL, 'Lipid Profile', 'Blood', 'TC: 198, LDL: 118, HDL: 52, TG: 140 — All normal', '<200', 'mg/dL', 'Completed', 0, NULL, '2026-01-18', '2026-01-19', 3, '2026-04-13 08:56:38'),
(29, 39, 3, NULL, 'Urinalysis', 'Urine', 'Color: Yellow, pH: 6.0, Protein: Neg, Glucose: Neg', 'Normal', 'N/A', 'Completed', 0, NULL, '2026-02-05', '2026-02-05', 4, '2026-04-13 08:56:38'),
(30, 40, 4, NULL, 'TSH and Free T4', 'Blood', 'TSH: 7.2, Free T4: 0.8 — Hypothyroid confirmed', 'TSH 0.4-4.0', 'mIU/L', 'Completed', 1, NULL, '2026-01-22', '2026-01-23', 5, '2026-04-13 08:56:38'),
(31, 101, 112, NULL, 'Blood test', 'Microbiology', NULL, NULL, NULL, 'Requested', 0, 'CHECK UP broken', '2026-04-29', NULL, NULL, '2026-04-13 13:00:48');

-- --------------------------------------------------------

--
-- Table structure for table `medical_records`
--

CREATE TABLE `medical_records` (
  `id` int(10) UNSIGNED NOT NULL,
  `patient_id` int(10) UNSIGNED NOT NULL,
  `record_type` enum('Consultation','Lab Result','Diagnosis','Prescription','Other') NOT NULL DEFAULT 'Consultation',
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `recorded_by` int(10) UNSIGNED NOT NULL,
  `record_date` date NOT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medical_records`
--

INSERT INTO `medical_records` (`id`, `patient_id`, `record_type`, `title`, `description`, `recorded_by`, `record_date`, `attachment_path`, `created_at`) VALUES
(1, 1, 'Consultation', 'Hypertension Follow-up', 'BP: 145/90 mmHg. Patient reports occasional headaches. Advised low-sodium diet and regular exercise. Continued Amlodipine 5mg.', 2, '2026-01-10', NULL, '2026-04-13 08:56:38'),
(2, 2, 'Lab Result', 'Blood Glucose Result', 'Fasting blood sugar: 95 mg/dL — within normal range. Patient maintaining diet control. Continue monitoring monthly.', 3, '2026-01-12', NULL, '2026-04-13 08:56:38'),
(3, 3, 'Diagnosis', 'Type 2 Diabetes Mellitus', 'Patient presents with HbA1c of 8.2%. Diagnosed with poorly controlled T2DM. Initiated Metformin 500mg BID. Diet counseling provided.', 4, '2026-01-20', NULL, '2026-04-13 08:56:38'),
(4, 4, 'Consultation', 'Prenatal Checkup — 2nd Trimester', 'Fetal heart rate: 148 bpm. Fundal height: 24 cm. BP: 110/70. No complaints. Iron supplementation prescribed.', 5, '2026-02-05', NULL, '2026-04-13 08:56:38'),
(5, 5, 'Diagnosis', 'Hypercholesterolemia', 'Lipid panel shows TC: 245, LDL: 165. Diagnosed with hypercholesterolemia. Lifestyle modification counseling. Started Atorvastatin 20mg.', 6, '2025-12-01', NULL, '2026-04-13 08:56:38'),
(6, 6, 'Consultation', 'Hypertension Management', 'BP controlled at 130/85 mmHg with Losartan. Patient compliant with medication. Continue current regimen. Next visit in 1 month.', 7, '2026-01-08', NULL, '2026-04-13 08:56:38'),
(7, 7, 'Diagnosis', 'Bronchial Asthma — Mild', 'Patient presents with recurrent wheezing and cough. PFT shows mild obstruction. Diagnosed with mild persistent asthma. Prescribed Salbutamol inhaler.', 8, '2026-01-14', NULL, '2026-04-13 08:56:38'),
(8, 8, 'Lab Result', 'TSH Elevated — Hypothyroidism', 'TSH: 6.8 mIU/L. Confirmed hypothyroidism. Started Levothyroxine 50mcg. Repeat TSH in 6 weeks to adjust dose.', 9, '2026-01-30', NULL, '2026-04-13 08:56:38'),
(9, 9, 'Consultation', 'GERD Management', 'Patient reports epigastric pain especially after meals. Prescribed Omeprazole 20mg daily. Advised to avoid spicy and fatty foods.', 10, '2026-02-18', NULL, '2026-04-13 08:56:38'),
(10, 10, 'Consultation', 'Post-Vaccination Monitoring', 'Patient completed Hepatitis B 3-dose series. Anti-HBs titer requested to confirm immunity. Patient advised to return for results.', 2, '2026-02-15', NULL, '2026-04-13 08:56:38'),
(11, 11, 'Lab Result', 'Sputum Culture — Positive', 'Sputum culture positive for S. pneumoniae sensitive to Amoxicillin. Antibiotic therapy continued. Follow-up chest X-ray in 2 weeks.', 3, '2026-01-25', NULL, '2026-04-13 08:56:38'),
(12, 12, 'Consultation', 'Pre-operative Assessment', 'Patient cleared for appendectomy. CBC, blood typing, ECG all normal. Consent signed. Scheduled for laparoscopic appendectomy.', 4, '2026-03-15', NULL, '2026-04-13 08:56:38'),
(13, 13, 'Consultation', 'Hypertension — New Diagnosis', '24-hour BP monitoring shows average 145/92 mmHg. Diagnosed with Stage 1 hypertension. Lifestyle changes recommended. Will initiate medication if not controlled in 3 months.', 5, '2026-03-02', NULL, '2026-04-13 08:56:38'),
(14, 14, 'Diagnosis', 'Dengue Hemorrhagic Fever', 'NS1 antigen positive. Platelet: 85,000. Temperature: 38.8°C. Admitted for close monitoring. IV fluids started. Daily platelet count ordered.', 6, '2026-03-20', NULL, '2026-04-13 08:56:38'),
(15, 15, 'Consultation', 'Annual Well-Woman Exam', 'Pap smear: NILM. Breast exam: no masses. BP: 110/70. BMI: 22.4. Recommended annual follow-up. HPV vaccine series ongoing.', 7, '2026-02-01', NULL, '2026-04-13 08:56:38'),
(16, 16, 'Diagnosis', 'Moderate Persistent Asthma', 'Spirometry confirms moderate obstruction. FEV1: 62%. Nighttime symptoms 3-4x per week. Added Montelukast 10mg to Salbutamol regimen.', 8, '2026-02-25', NULL, '2026-04-13 08:56:38'),
(17, 17, 'Consultation', 'Prenatal — Third Trimester', 'Week 32 checkup. Fetal position: cephalic. Estimated fetal weight: 1.8kg. BP: 118/74. Hemoglobin: 11.8 g/dL. Iron supplement continued.', 9, '2026-03-05', NULL, '2026-04-13 08:56:38'),
(18, 18, 'Diagnosis', 'Poorly Controlled Diabetes', 'FBS: 186 mg/dL despite Metformin. Added Glipizide 5mg before breakfast. Dietary counseling reinforced. HbA1c to be checked in 3 months.', 10, '2026-02-28', NULL, '2026-04-13 08:56:38'),
(19, 19, 'Lab Result', 'ECG — Normal Sinus Rhythm', 'ECG shows normal sinus rhythm. No ST changes or arrhythmias. Cleared for elective colonoscopy procedure. No cardiac concerns noted.', 2, '2026-04-07', NULL, '2026-04-13 08:56:38'),
(20, 20, 'Diagnosis', 'Urinary Tract Infection', 'UA shows WBC 15/hpf. Culture positive for E.coli sensitive to Ciprofloxacin. Prescribed Ciprofloxacin 500mg BID for 7 days. Advised increased fluid intake.', 3, '2026-01-29', NULL, '2026-04-13 08:56:38'),
(21, 31, 'Diagnosis', 'Chronic Migraine', 'Patient presents with 3-4 migraine episodes per month lasting 12-24 hours. Triggered by stress and lack of sleep. Started on Sumatriptan for acute management.', 4, '2026-03-05', NULL, '2026-04-13 08:56:38'),
(22, 32, 'Consultation', 'Allergic Rhinitis Follow-up', 'Nasal congestion and itchy eyes persistent. Started Cetirizine. Advised to avoid allergens. Will reassess in 4 weeks.', 5, '2026-01-08', NULL, '2026-04-13 08:56:38'),
(23, 33, 'Diagnosis', 'Stage 2 Hypertension', 'BP consistently >150/90 despite lifestyle modification. Initiated Losartan 50mg. DASH diet counseling provided. Monthly BP monitoring required.', 6, '2026-02-12', NULL, '2026-04-13 08:56:38'),
(24, 34, 'Diagnosis', 'COPD — Moderate Stage', 'FEV1/FVC ratio 0.65. Smoking history 20 pack-years. Initiated Salbutamol inhaler. Pulmonary rehabilitation recommended.', 7, '2026-03-01', NULL, '2026-04-13 08:56:38'),
(25, 35, 'Lab Result', 'HbA1c 7.8% — Suboptimal Control', 'HbA1c elevated at 7.8%. Metformin dose maintained. Added dietary counseling session. Target HbA1c below 7.0% in 3 months.', 8, '2025-12-11', NULL, '2026-04-13 08:56:38'),
(26, 36, 'Diagnosis', 'Iron Deficiency Anemia', 'Serum ferritin critically low at 8.5 ng/mL. Hgb: 10.2 g/dL. Patient complains of fatigue. Started Ferrous Sulfate 325mg. Repeat CBC in 8 weeks.', 9, '2026-01-15', NULL, '2026-04-13 08:56:38'),
(27, 37, 'Consultation', 'Bronchial Asthma — Worsening', 'Peak flow 320 L/min — 68% predicted. Nighttime symptoms increasing. Added Montelukast to regimen. Reviewed proper inhaler technique.', 10, '2026-03-01', NULL, '2026-04-13 08:56:38'),
(28, 38, 'Consultation', 'Annual Executive Checkup', 'All parameters within normal limits. BP: 120/78. BMI: 23.1. Lipid profile normal. Advised to maintain current lifestyle. Next checkup in 12 months.', 2, '2026-01-18', NULL, '2026-04-13 08:56:38'),
(29, 39, 'Consultation', 'Acute Febrile Illness', 'Fever 38.8°C for 2 days. No cough, rash, or bleeding manifestations. Dengue ruled out — NS1 negative. Started symptomatic treatment. Advised hydration.', 3, '2026-02-05', NULL, '2026-04-13 08:56:38'),
(30, 40, 'Diagnosis', 'Hypothyroidism — Confirmed', 'TSH 7.2 mIU/L with low free T4. Patient reports fatigue and weight gain. Initiated Levothyroxine 50mcg. Repeat TSH in 6 weeks to titrate dose.', 4, '2026-01-23', NULL, '2026-04-13 08:56:38'),
(31, 101, 'Diagnosis', 'Checkup', 'Health reason', 112, '2026-04-30', NULL, '2026-04-13 09:53:11'),
(32, 101, 'Lab Result', 'monthly checkup', 'super broken', 112, '2026-04-30', NULL, '2026-04-13 13:01:09');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `patient_code` varchar(20) NOT NULL,
  `date_of_birth` date NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `blood_type` varchar(5) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `emergency_contact_name` varchar(150) DEFAULT NULL,
  `emergency_contact_phone` varchar(20) DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `registered_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `user_id`, `patient_code`, `date_of_birth`, `gender`, `blood_type`, `address`, `phone`, `emergency_contact_name`, `emergency_contact_phone`, `allergies`, `notes`, `registered_by`, `created_at`, `updated_at`) VALUES
(1, 12, 'GVX-000001', '1992-05-14', 'Male', 'O+', 'Blk 5 Lot 12 Sampaguita St, Caloocan City', '+63 912 345 6789', 'Maria dela Cruz', '+63 912 345 6780', 'None', NULL, 2, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(2, 13, 'GVX-000002', '1988-08-22', 'Female', 'A+', 'Phase 2 Dahlia Ave, Quezon City', '+63 917 654 3210', 'Roberto Reyes', '+63 917 654 3200', 'Penicillin', NULL, 2, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(3, 14, 'GVX-000003', '1975-03-01', 'Male', 'B-', '123 Rizal Street, Marikina City', '+63 920 111 2222', 'Lorna Santos', '+63 920 111 2220', 'None', NULL, 3, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(4, 15, 'GVX-000004', '1995-11-30', 'Female', 'AB+', '456 Mabini Ave, Pasig City', '+63 915 333 4444', 'Jorge Fernandez', '+63 915 333 4440', 'Sulfa drugs', NULL, 3, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(5, 16, 'GVX-000005', '1960-07-19', 'Male', 'O-', '789 Bonifacio St, Mandaluyong', '+63 918 555 6666', 'Carla Gomez', '+63 918 555 6660', 'Aspirin', NULL, 4, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(6, 17, 'GVX-000006', '1983-04-25', 'Female', 'A-', '321 Luna Blvd, Las Pinas City', '+63 916 777 8888', 'Marco Villanueva', '+63 916 777 8880', 'None', NULL, 4, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(7, 18, 'GVX-000007', '1999-09-13', 'Male', 'B+', '654 Del Pilar St, Parañaque City', '+63 919 999 0000', 'Jenny Lim', '+63 919 999 0001', 'None', NULL, 5, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(8, 19, 'GVX-000008', '1970-12-05', 'Female', 'O+', '987 Katipunan Ave, Antipolo City', '+63 913 111 3333', 'Rene Aquino', '+63 913 111 3330', 'Iodine', NULL, 5, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(9, 20, 'GVX-000009', '2001-06-18', 'Male', 'A+', '147 Espana Blvd, Sampaloc, Manila', '+63 914 222 5555', 'Linda Ramos', '+63 914 222 5550', 'None', NULL, 6, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(10, 21, 'GVX-000010', '1987-02-28', 'Female', 'AB-', '258 Taft Ave, Malate, Manila', '+63 921 444 7777', 'Dante Cruz', '+63 921 444 7770', 'Latex', NULL, 6, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(11, 22, 'GVX-000011', '1993-10-07', 'Male', 'O+', '369 EDSA, Mandaluyong City', '+63 922 666 9999', 'Nora Torres', '+63 922 666 9990', 'None', NULL, 7, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(12, 23, 'GVX-000012', '1978-01-15', 'Female', 'B+', '741 C.M. Recto Ave, Manila', '+63 923 888 1111', 'Oscar Bautista', '+63 923 888 1110', 'NSAIDs', NULL, 7, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(13, 24, 'GVX-000013', '2005-08-20', 'Male', 'A+', '852 Commonwealth Ave, Quezon City', '+63 924 000 2222', 'Perla Castillo', '+63 924 000 2220', 'None', NULL, 8, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(14, 25, 'GVX-000014', '1965-03-30', 'Female', 'O-', '963 Quirino Ave, Paco, Manila', '+63 925 222 4444', 'Quintin Navarro', '+63 925 222 4440', 'Shellfish', NULL, 8, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(15, 26, 'GVX-000015', '1990-07-04', 'Male', 'AB+', '159 Shaw Blvd, Pasig City', '+63 926 444 6666', 'Rachel Morales', '+63 926 444 6660', 'None', NULL, 9, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(16, 27, 'GVX-000016', '1982-11-11', 'Female', 'A+', '357 Session Road, Baguio City', '+63 927 666 8888', 'Samuel Ocampo', '+63 927 666 8880', 'Pollen', NULL, 9, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(17, 28, 'GVX-000017', '1998-04-17', 'Male', 'B-', '246 Colon Street, Cebu City', '+63 928 888 0000', 'Thelma Pascual', '+63 928 888 0001', 'None', NULL, 10, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(18, 29, 'GVX-000018', '1973-09-23', 'Female', 'O+', '135 JP Laurel Ave, Davao City', '+63 929 000 1111', 'Ulysses Velarde', '+63 929 000 1110', 'Eggs', NULL, 10, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(19, 30, 'GVX-000019', '2003-12-31', 'Male', 'A-', '468 Osmeña Blvd, Cebu City', '+63 930 111 2222', 'Vivian Aguilar', '+63 930 111 2220', 'None', NULL, 2, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(20, 31, 'GVX-000020', '1985-05-05', 'Female', 'B+', '579 Abad Santos, Sta Cruz, Manila', '+63 931 222 3333', 'William Domingo', '+63 931 222 3330', 'Dust mites', NULL, 3, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(21, 32, 'GVX-000021', '1967-08-14', 'Male', 'O+', '682 P. Burgos St, Makati City', '+63 932 333 4444', 'Ximena Padilla', '+63 932 333 4440', 'None', NULL, 4, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(22, 33, 'GVX-000022', '1991-03-21', 'Female', 'AB+', '791 Ayala Ave, Makati City', '+63 933 444 5555', 'Yolanda Fuentes', '+63 933 444 5550', 'Amoxicillin', NULL, 5, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(23, 34, 'GVX-000023', '1979-10-10', 'Male', 'A+', '824 Timog Ave, Quezon City', '+63 934 555 6666', 'Zachary Ibarra', '+63 934 555 6660', 'None', NULL, 6, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(24, 35, 'GVX-000024', '1994-06-06', 'Female', 'O-', '935 Morayta, Sampaloc, Manila', '+63 935 666 7777', 'Alma Jacinto', '+63 935 666 7770', 'None', NULL, 7, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(25, 36, 'GVX-000025', '2000-01-20', 'Male', 'B+', '1046 Bambang, Sta Cruz, Manila', '+63 936 777 8888', 'Bernard Kampos', '+63 936 777 8880', 'Peanuts', NULL, 8, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(26, 37, 'GVX-000026', '1972-07-07', 'Female', 'A-', '1157 Lacson St, Bacolod City', '+63 937 888 9999', 'Cecile Luna', '+63 937 888 9990', 'None', NULL, 9, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(27, 38, 'GVX-000027', '1986-02-14', 'Male', 'O+', '1268 Legazpi St, Iloilo City', '+63 938 999 0000', 'Diana Magtanggol', '+63 938 999 0001', 'Codeine', NULL, 10, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(28, 39, 'GVX-000028', '1997-09-09', 'Female', 'AB-', '1379 Magallanes, Zamboanga City', '+63 939 000 1111', 'Ernest Narciso', '+63 939 000 1110', 'None', NULL, 2, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(29, 40, 'GVX-000029', '1963-04-04', 'Male', 'B-', '1480 Nationale, Cagayan de Oro City', '+63 940 111 2222', 'Felicia Orozco', '+63 940 111 2220', 'Tree nuts', NULL, 3, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(30, 41, 'GVX-000030', '1989-11-25', 'Female', 'O+', '1591 Osmena St, General Santos City', '+63 941 222 3333', 'Gregorio Perez', '+63 941 222 3330', 'None', NULL, 4, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(31, 42, 'GVX-000031', '1994-03-15', 'Male', 'O+', 'Blk 2 Lot 3 Sampaguita, Caloocan', '+63 912 100 0031', 'Ana Reyes', '+63 912 100 0001', 'None', NULL, 2, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(32, 43, 'GVX-000032', '1988-07-22', 'Female', 'A+', 'Phase 3 Rose Ave, Quezon City', '+63 917 100 0032', 'Pedro Santos', '+63 917 100 0002', 'Penicillin', NULL, 3, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(33, 44, 'GVX-000033', '1976-11-08', 'Male', 'B+', '55 Rizal St, Marikina City', '+63 920 100 0033', 'Luz Cruz', '+63 920 100 0003', 'None', NULL, 4, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(34, 45, 'GVX-000034', '1992-05-30', 'Female', 'AB+', '88 Mabini Ave, Pasig City', '+63 915 100 0034', 'Jose Bautista', '+63 915 100 0004', 'Sulfa drugs', NULL, 5, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(35, 46, 'GVX-000035', '1962-09-14', 'Male', 'O-', '22 Bonifacio St, Mandaluyong', '+63 918 100 0035', 'Maria Garcia', '+63 918 100 0005', 'Aspirin', NULL, 6, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(36, 47, 'GVX-000036', '1984-02-28', 'Female', 'A-', '101 Luna Blvd, Las Pinas', '+63 916 100 0036', 'Roberto Torres', '+63 916 100 0006', 'None', NULL, 7, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(37, 48, 'GVX-000037', '2000-06-17', 'Male', 'B+', '44 Del Pilar St, Paranaque', '+63 919 100 0037', 'Linda Lim', '+63 919 100 0007', 'None', NULL, 8, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(38, 49, 'GVX-000038', '1971-12-25', 'Female', 'O+', '77 Katipunan Ave, Antipolo', '+63 913 100 0038', 'Danilo Aquino', '+63 913 100 0008', 'Iodine', NULL, 9, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(39, 50, 'GVX-000039', '2002-04-10', 'Male', 'A+', '99 Espana Blvd, Manila', '+63 914 100 0039', 'Perla Ramos', '+63 914 100 0009', 'None', NULL, 10, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(40, 51, 'GVX-000040', '1987-08-05', 'Female', 'AB-', '33 Taft Ave, Manila', '+63 921 100 0040', 'Mario Cruz', '+63 921 100 0010', 'Latex', NULL, 2, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(41, 52, 'GVX-000041', '1993-01-19', 'Male', 'O+', '66 EDSA, Mandaluyong', '+63 922 100 0041', 'Nida Torres', '+63 922 100 0011', 'None', NULL, 3, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(42, 53, 'GVX-000042', '1979-06-12', 'Female', 'B-', '12 CM Recto Ave, Manila', '+63 923 100 0042', 'Oscar Bautista', '+63 923 100 0012', 'NSAIDs', NULL, 4, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(43, 54, 'GVX-000043', '2004-09-23', 'Male', 'A+', '45 Commonwealth Ave, QC', '+63 924 100 0043', 'Perla Castillo', '+63 924 100 0013', 'None', NULL, 5, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(44, 55, 'GVX-000044', '1966-03-07', 'Female', 'O-', '78 Quirino Ave, Manila', '+63 925 100 0044', 'Quintin Navarro', '+63 925 100 0014', 'Shellfish', NULL, 6, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(45, 56, 'GVX-000045', '1991-07-20', 'Male', 'AB+', '321 Shaw Blvd, Pasig', '+63 926 100 0045', 'Rachel Morales', '+63 926 100 0015', 'None', NULL, 7, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(46, 57, 'GVX-000046', '1983-11-11', 'Female', 'A+', '654 Session Rd, Baguio', '+63 927 100 0046', 'Samuel Ocampo', '+63 927 100 0016', 'Pollen', NULL, 8, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(47, 58, 'GVX-000047', '1998-05-03', 'Male', 'B-', '987 Colon St, Cebu City', '+63 928 100 0047', 'Thelma Pascual', '+63 928 100 0017', 'None', NULL, 9, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(48, 59, 'GVX-000048', '1974-10-16', 'Female', 'O+', '246 JP Laurel Ave, Davao', '+63 929 100 0048', 'Ulysses Velarde', '+63 929 100 0018', 'Eggs', NULL, 10, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(49, 60, 'GVX-000049', '2003-01-29', 'Male', 'A-', '579 Osmena Blvd, Cebu', '+63 930 100 0049', 'Vivian Aguilar', '+63 930 100 0019', 'None', NULL, 2, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(50, 61, 'GVX-000050', '1985-06-06', 'Female', 'B+', '813 Abad Santos, Manila', '+63 931 100 0050', 'William Domingo', '+63 931 100 0020', 'Dust', NULL, 3, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(51, 62, 'GVX-000051', '1968-09-09', 'Male', 'O+', '147 PBurgos St, Makati', '+63 932 100 0051', 'Ximena Padilla', '+63 932 100 0021', 'None', NULL, 4, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(52, 63, 'GVX-000052', '1990-02-14', 'Female', 'AB+', '258 Ayala Ave, Makati', '+63 933 100 0052', 'Yolanda Fuentes', '+63 933 100 0022', 'Amoxicillin', NULL, 5, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(53, 64, 'GVX-000053', '1980-07-07', 'Male', 'A+', '369 Timog Ave, QC', '+63 934 100 0053', 'Zachary Ibarra', '+63 934 100 0023', 'None', NULL, 6, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(54, 65, 'GVX-000054', '1995-12-12', 'Female', 'O-', '482 Morayta, Manila', '+63 935 100 0054', 'Alma Jacinto', '+63 935 100 0024', 'None', NULL, 7, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(55, 66, 'GVX-000055', '2001-03-25', 'Male', 'B+', '593 Bambang, Manila', '+63 936 100 0055', 'Bernard Kampos', '+63 936 100 0025', 'Peanuts', NULL, 8, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(56, 67, 'GVX-000056', '1973-08-18', 'Female', 'A-', '614 Lacson St, Bacolod', '+63 937 100 0056', 'Cecile Luna', '+63 937 100 0026', 'None', NULL, 9, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(57, 68, 'GVX-000057', '1987-01-01', 'Male', 'O+', '725 Legazpi St, Iloilo', '+63 938 100 0057', 'Diana Magtanggol', '+63 938 100 0027', 'Codeine', NULL, 10, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(58, 69, 'GVX-000058', '1997-04-04', 'Female', 'AB-', '836 Magallanes, Zamboanga', '+63 939 100 0058', 'Ernest Narciso', '+63 939 100 0028', 'None', NULL, 2, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(59, 70, 'GVX-000059', '1964-11-11', 'Male', 'B-', '947 Nationale, Cagayan de Oro', '+63 940 100 0059', 'Felicia Orozco', '+63 940 100 0029', 'Tree nuts', NULL, 3, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(60, 71, 'GVX-000060', '1990-06-30', 'Female', 'O+', '1058 Osmena St, Gen Santos', '+63 941 100 0060', 'Gregorio Perez', '+63 941 100 0030', 'None', NULL, 4, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(61, 72, 'GVX-000061', '1977-02-08', 'Male', 'A+', 'Blk 7 Maharlika, Taguig City', '+63 942 100 0061', 'Helen Salazar', '+63 942 100 0031', 'None', NULL, 5, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(62, 73, 'GVX-000062', '1996-09-21', 'Female', 'B+', '23 Bayani Rd, Fort Bonifacio, Taguig', '+63 943 100 0062', 'Ivan Tamayo', '+63 943 100 0032', 'Penicillin', NULL, 6, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(63, 74, 'GVX-000063', '1969-04-17', 'Male', 'AB+', '56 McKinley Hill, Taguig', '+63 944 100 0063', 'Julia Umali', '+63 944 100 0033', 'None', NULL, 7, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(64, 75, 'GVX-000064', '1993-11-03', 'Female', 'O+', '89 Ortigas Ave, San Juan', '+63 945 100 0064', 'Karl Valentin', '+63 945 100 0034', 'None', NULL, 8, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(65, 76, 'GVX-000065', '2005-07-14', 'Male', 'A-', '112 Wilson St, San Juan', '+63 946 100 0065', 'Luz Wenceslao', '+63 946 100 0035', 'None', NULL, 9, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(66, 77, 'GVX-000066', '1981-02-22', 'Female', 'B-', '135 Connecticut Ave, Greenhills', '+63 947 100 0066', 'Manuel Xavier', '+63 947 100 0036', 'Latex', NULL, 10, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(67, 78, 'GVX-000067', '1959-08-09', 'Male', 'O-', '158 Santolan Rd, Pasig', '+63 948 100 0067', 'Nancy Yap', '+63 948 100 0037', 'Aspirin', NULL, 2, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(68, 79, 'GVX-000068', '1999-05-05', 'Female', 'AB+', '181 Meralco Ave, Pasig', '+63 949 100 0068', 'Oscar Zabala', '+63 949 100 0038', 'None', NULL, 3, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(69, 80, 'GVX-000069', '1975-12-28', 'Male', 'A+', '204 Kapitolyo, Pasig City', '+63 950 100 0069', 'Patria Abella', '+63 950 100 0039', 'Sulfa', NULL, 4, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(70, 81, 'GVX-000070', '1988-03-11', 'Female', 'B+', '227 Malinao, Pasig City', '+63 951 100 0070', 'Quirino Buena', '+63 951 100 0040', 'None', NULL, 5, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(71, 82, 'GVX-000071', '1970-10-10', 'Male', 'O+', '250 Kalayaan Ave, Makati', '+63 952 100 0071', 'Rosa Cabrera', '+63 952 100 0041', 'None', NULL, 6, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(72, 83, 'GVX-000072', '1994-07-07', 'Female', 'A-', '273 Chino Roces Ave, Makati', '+63 953 100 0072', 'Samuel Delos', '+63 953 100 0042', 'Iodine', NULL, 7, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(73, 84, 'GVX-000073', '2006-01-15', 'Male', 'B+', '296 Pasay Rd, Makati', '+63 954 100 0073', 'Tina Espiritu', '+63 954 100 0043', 'None', NULL, 8, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(74, 85, 'GVX-000074', '1983-04-20', 'Female', 'AB-', '319 Vito Cruz, Manila', '+63 955 100 0074', 'Ursula Flores', '+63 955 100 0044', 'NSAIDs', NULL, 9, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(75, 86, 'GVX-000075', '1967-09-09', 'Male', 'O+', '342 Libertad St, Pasay', '+63 956 100 0075', 'Victor Guerrero', '+63 956 100 0045', 'None', NULL, 10, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(76, 87, 'GVX-000076', '1998-06-16', 'Female', 'A+', '365 Cuneta Ave, Pasay', '+63 957 100 0076', 'Wendy Hermosa', '+63 957 100 0046', 'None', NULL, 2, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(77, 88, 'GVX-000077', '1978-11-30', 'Male', 'B-', '388 Buendia Ave, Makati', '+63 958 100 0077', 'Xavier Icasiano', '+63 958 100 0047', 'Peanuts', NULL, 3, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(78, 89, 'GVX-000078', '2001-08-08', 'Female', 'O-', '411 South Superhighway, Muntinlupa', '+63 959 100 0078', 'Yolanda Jimenez', '+63 959 100 0048', 'None', NULL, 4, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(79, 90, 'GVX-000079', '1972-03-03', 'Male', 'AB+', '434 Alabang-Zapote Rd, Las Pinas', '+63 960 100 0079', 'Zaldy Kanapi', '+63 960 100 0049', 'Codeine', NULL, 5, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(80, 91, 'GVX-000080', '1989-10-24', 'Female', 'A+', '457 Daang Hari, Bacoor, Cavite', '+63 961 100 0080', 'Amor Lacson', '+63 961 100 0050', 'None', NULL, 6, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(81, 92, 'GVX-000081', '1963-05-19', 'Male', 'O+', '480 Aguinaldo Hi-way, Imus, Cavite', '+63 962 100 0081', 'Boris Macapagal', '+63 962 100 0051', 'None', NULL, 7, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(82, 93, 'GVX-000082', '1997-02-11', 'Female', 'B+', '503 Palico Rd, Imus, Cavite', '+63 963 100 0082', 'Corazon Natividad', '+63 963 100 0052', 'Sulfa drugs', NULL, 8, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(83, 94, 'GVX-000083', '1985-07-27', 'Male', 'A-', '526 Sampaloc, Tanay, Rizal', '+63 964 100 0083', 'Doming Oliva', '+63 964 100 0053', 'None', NULL, 9, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(84, 95, 'GVX-000084', '1960-12-15', 'Female', 'AB+', '549 National Rd, Antipolo, Rizal', '+63 965 100 0084', 'Ester Paredes', '+63 965 100 0054', 'Tree nuts', NULL, 10, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(85, 96, 'GVX-000085', '2004-04-01', 'Male', 'O-', '572 Circumferential Rd, Cainta, Rizal', '+63 966 100 0085', 'Felix Quizon', '+63 966 100 0055', 'None', NULL, 2, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(86, 97, 'GVX-000086', '1982-09-18', 'Female', 'B-', '595 Sumulong Hi-way, Antipolo', '+63 967 100 0086', 'Gloria Rebueno', '+63 967 100 0056', 'None', NULL, 3, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(87, 98, 'GVX-000087', '1975-06-06', 'Male', 'A+', '618 MacArthur Hi-way, Valenzuela', '+63 968 100 0087', 'Herman Santiago', '+63 968 100 0057', 'Aspirin', NULL, 4, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(88, 99, 'GVX-000088', '1993-01-01', 'Female', 'O+', '641 McArthur Hi-way, Malabon', '+63 969 100 0088', 'Irene Tiongson', '+63 969 100 0058', 'None', NULL, 5, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(89, 100, 'GVX-000089', '1968-08-20', 'Male', 'AB-', '664 Gov. Pascual Ave, Malabon', '+63 970 100 0089', 'Joel Ureta', '+63 970 100 0059', 'None', NULL, 6, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(90, 101, 'GVX-000090', '1986-03-30', 'Female', 'A+', '687 Juan Luna Ave, Navotas', '+63 971 100 0090', 'Karen Villafuerte', '+63 971 100 0060', 'Latex', NULL, 7, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(91, 102, 'GVX-000091', '2000-11-11', 'Male', 'B+', '710 M. Naval St, Navotas', '+63 972 100 0091', 'Leo Wagan', '+63 972 100 0061', 'None', NULL, 8, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(92, 103, 'GVX-000092', '1977-07-04', 'Female', 'O-', '733 A. Mabini St, Cavite City', '+63 973 100 0092', 'Marina Reyes', '+63 973 100 0062', 'Iodine', NULL, 9, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(93, 104, 'GVX-000093', '1991-04-16', 'Male', 'A-', '756 M. Alvarez Ave, Gen Trias, Cavite', '+63 974 100 0093', 'Nestor Santos', '+63 974 100 0063', 'None', NULL, 10, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(94, 105, 'GVX-000094', '1972-10-02', 'Female', 'B+', '779 Emilio Aguinaldo Hi-way, Dasmarinas', '+63 975 100 0094', 'Ofelia Cruz', '+63 975 100 0064', 'None', NULL, 2, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(95, 106, 'GVX-000095', '1996-05-28', 'Male', 'AB+', '802 Governor Drive, Silang, Cavite', '+63 976 100 0095', 'Pedro Bautista', '+63 976 100 0065', 'Penicillin', NULL, 3, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(96, 107, 'GVX-000096', '1984-02-14', 'Female', 'O+', '825 Santa Rosa-Tagaytay Rd, Laguna', '+63 977 100 0096', 'Quirina Garcia', '+63 977 100 0066', 'None', NULL, 4, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(97, 108, 'GVX-000097', '1961-09-07', 'Male', 'A+', '848 National Hi-way, Sta Rosa, Laguna', '+63 978 100 0097', 'Roberto Torres', '+63 978 100 0067', 'NSAIDs', NULL, 5, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(98, 109, 'GVX-000098', '2007-03-22', 'Female', 'B-', '871 Brgy. Market, Calamba, Laguna', '+63 979 100 0098', 'Shirley Lim', '+63 979 100 0068', 'None', NULL, 6, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(99, 110, 'GVX-000099', '1979-08-14', 'Male', 'O+', '894 National Rd, Los Banos, Laguna', '+63 980 100 0099', 'Teofilo Aquino', '+63 980 100 0069', 'Sulfa drugs', NULL, 7, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(100, 111, 'GVX-000100', '1988-12-25', 'Female', 'AB+', '917 Real St, San Pablo, Laguna', '+63 981 100 0100', 'Ursula Ramos', '+63 981 100 0070', 'None', NULL, 8, '2026-04-13 08:56:38', '2026-04-13 08:56:38'),
(101, 113, 'GVX-000101', '2014-07-09', 'Male', 'B+', '430 wellington bldg', '+639458973561', 'Joshua duro', '09458973562', 'sakit', 'sa puso', 112, '2026-04-13 03:06:49', '2026-04-13 03:06:49');

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `id` int(10) UNSIGNED NOT NULL,
  `patient_id` int(10) UNSIGNED NOT NULL,
  `prescribed_by` int(10) UNSIGNED NOT NULL,
  `admission_id` int(10) UNSIGNED DEFAULT NULL,
  `medicine_name` varchar(200) NOT NULL,
  `dosage` varchar(100) NOT NULL,
  `frequency` varchar(100) NOT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `status` enum('Pending','Dispensed','Cancelled') NOT NULL DEFAULT 'Pending',
  `prescribed_at` datetime NOT NULL DEFAULT current_timestamp(),
  `dispensed_at` datetime DEFAULT NULL,
  `dispensed_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescriptions`
--

INSERT INTO `prescriptions` (`id`, `patient_id`, `prescribed_by`, `admission_id`, `medicine_name`, `dosage`, `frequency`, `duration`, `quantity`, `instructions`, `status`, `prescribed_at`, `dispensed_at`, `dispensed_by`) VALUES
(1, 1, 2, NULL, 'Amlodipine 5mg', '1 tablet', 'Once daily', '30 days', 30, 'Take in the morning with or without food', 'Dispensed', '2026-04-13 08:56:38', NULL, NULL),
(2, 2, 3, NULL, 'Amoxicillin 500mg', '1 capsule', '3 times daily', '7 days', 21, 'Take after meals. Complete the full course', 'Dispensed', '2026-04-13 08:56:38', NULL, NULL),
(3, 3, 4, NULL, 'Metformin 500mg', '1 tablet', 'Twice daily', '30 days', 60, 'Take with meals to reduce stomach upset', 'Dispensed', '2026-04-13 08:56:38', NULL, NULL),
(4, 4, 5, NULL, 'Ferrous Sulfate 325mg', '1 tablet', 'Once daily', '60 days', 60, 'Take on empty stomach. Avoid with milk', 'Dispensed', '2026-04-13 08:56:38', NULL, NULL),
(5, 5, 6, NULL, 'Atorvastatin 20mg', '1 tablet', 'Once at night', '30 days', 30, 'Take at bedtime for best effectiveness', 'Pending', '2026-04-13 08:56:38', NULL, NULL),
(6, 6, 7, NULL, 'Losartan 50mg', '1 tablet', 'Once daily', '30 days', 30, 'Monitor blood pressure regularly', 'Dispensed', '2026-04-13 08:56:38', NULL, NULL),
(7, 7, 8, NULL, 'Salbutamol Inhaler', '2 puffs', 'As needed', '30 days', 1, 'Shake well before use. Max 4 puffs per day', 'Dispensed', '2026-04-13 08:56:38', NULL, NULL),
(8, 8, 9, NULL, 'Levothyroxine 50mcg', '1 tablet', 'Once daily', '30 days', 30, 'Take 30 minutes before breakfast', 'Pending', '2026-04-13 08:56:38', NULL, NULL),
(9, 9, 10, NULL, 'Omeprazole 20mg', '1 capsule', 'Once daily', '14 days', 14, 'Take 30 minutes before breakfast', 'Dispensed', '2026-04-13 08:56:38', NULL, NULL),
(10, 10, 2, NULL, 'Paracetamol 500mg', '1-2 tablets', 'Every 6 hours', '5 days', 20, 'Take only when needed for pain or fever', 'Dispensed', '2026-04-13 08:56:38', NULL, NULL),
(11, 11, 3, NULL, 'Cetirizine 10mg', '1 tablet', 'Once daily', '14 days', 14, 'Take at night as may cause drowsiness', 'Pending', '2026-04-13 08:56:38', NULL, NULL),
(12, 12, 4, NULL, 'Tramadol 50mg', '1 capsule', 'Every 8 hours', '5 days', 15, 'Post-operative pain management. Do not drive', 'Dispensed', '2026-04-13 08:56:38', NULL, NULL),
(13, 13, 5, NULL, 'Vitamin D3 1000IU', '1 tablet', 'Once daily', '90 days', 90, 'Take with a meal for better absorption', 'Dispensed', '2026-04-13 08:56:38', NULL, NULL),
(14, 14, 6, NULL, 'Aspirin 75mg', '1 tablet', 'Once daily', '30 days', 30, 'Take with food. Do not crush or chew', 'Pending', '2026-04-13 08:56:38', NULL, NULL),
(15, 15, 7, NULL, 'Folic Acid 5mg', '1 tablet', 'Once daily', '90 days', 90, 'Essential for fetal development', 'Dispensed', '2026-04-13 08:56:38', NULL, NULL),
(16, 16, 8, NULL, 'Montelukast 10mg', '1 tablet', 'Once at night', '30 days', 30, 'For asthma and allergic rhinitis', 'Dispensed', '2026-04-13 08:56:38', NULL, NULL),
(17, 17, 9, NULL, 'Calcium Carbonate 500mg', '1 tablet', 'Twice daily', '60 days', 120, 'Take with meals for better absorption', 'Pending', '2026-04-13 08:56:38', NULL, NULL),
(18, 18, 10, NULL, 'Glipizide 5mg', '1 tablet', 'Once before breakfast', '30 days', 30, 'Monitor blood sugar daily', 'Dispensed', '2026-04-13 08:56:38', NULL, NULL),
(19, 19, 2, NULL, 'Bisoprolol 5mg', '1 tablet', 'Once daily', '30 days', 30, 'Do not stop suddenly. Taper as directed', 'Pending', '2026-04-13 08:56:38', NULL, NULL),
(20, 20, 3, NULL, 'Vitamin B Complex', '1 tablet', 'Once daily', '30 days', 30, 'Take after meals', 'Dispensed', '2026-04-13 08:56:38', NULL, NULL),
(21, 31, 4, NULL, 'Sumatriptan 50mg', '1 tablet', 'At onset of migraine', '10 days', 10, 'Take at first sign of migraine. Max 2 per day', 'Dispensed', '2026-04-13 08:56:38', NULL, NULL),
(22, 32, 5, NULL, 'Cetirizine 10mg', '1 tablet', 'Once daily at night', '14 days', 14, 'May cause drowsiness. Do not drive', 'Dispensed', '2026-04-13 08:56:38', NULL, NULL),
(23, 33, 6, NULL, 'Losartan 50mg', '1 tablet', 'Once daily morning', '30 days', 30, 'Monitor blood pressure daily', 'Pending', '2026-04-13 08:56:38', NULL, NULL),
(24, 34, 7, NULL, 'Salbutamol Inhaler', '2 puffs', 'As needed', '30 days', 1, 'Use for breathing difficulty only', 'Dispensed', '2026-04-13 08:56:38', NULL, NULL),
(25, 35, 8, NULL, 'Metformin 500mg', '1 tablet', 'Twice daily', '30 days', 60, 'Take with meals. Monitor blood sugar', 'Dispensed', '2026-04-13 08:56:38', NULL, NULL),
(26, 36, 9, NULL, 'Ferrous Sulfate 325mg', '1 tablet', 'Once daily', '60 days', 60, 'Take on empty stomach', 'Pending', '2026-04-13 08:56:38', NULL, NULL),
(27, 37, 10, NULL, 'Montelukast 10mg', '1 tablet', 'Once at night', '30 days', 30, 'For asthma prevention', 'Dispensed', '2026-04-13 08:56:38', NULL, NULL),
(28, 38, 2, NULL, 'Vitamin C 500mg', '1 tablet', 'Once daily', '30 days', 30, 'Take after meals', 'Dispensed', '2026-04-13 08:56:38', NULL, NULL),
(29, 39, 3, NULL, 'Paracetamol 500mg', '1-2 tabs', 'Every 6 hours PRN', '5 days', 20, 'Take only for fever or pain over 38°C', 'Dispensed', '2026-04-13 08:56:38', NULL, NULL),
(30, 40, 4, NULL, 'Levothyroxine 50mcg', '1 tablet', 'Once daily before breakfast', '30 days', 30, 'Take 30 mins before eating', 'Pending', '2026-04-13 08:56:38', NULL, NULL),
(31, 101, 112, NULL, 'metformin', '1', '2', '5', 21, 'NEED to take it every meal', 'Pending', '2026-04-13 13:00:25', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `label` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `label`) VALUES
(1, 'admin', 'System Administrator'),
(2, 'nurse', 'Registered Nurse'),
(3, 'patient', 'Patient');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `department_id` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('active','inactive','pending') NOT NULL DEFAULT 'pending',
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role_id`, `department_id`, `status`, `email_verified`, `created_at`, `updated_at`) VALUES
(1, 'System Administrator', 'admin@guardvax.com', '$2y$12$4QhqLvmQAwNiqcAnliVhCOUWuxfn9hZ74t1xDqiAFpWJo8ogSQzau', 1, NULL, 'active', 1, '2026-04-13 08:56:14', '2026-04-13 03:01:21'),
(2, 'Maria Santos', 'msantos@guardvax.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(3, 'Jose Reyes', 'jreyes@guardvax.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(4, 'Ana Dela Cruz', 'adelacruz@guardvax.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(5, 'Carlos Bautista', 'cbautista@guardvax.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(6, 'Rosa Mendoza', 'rmendoza@guardvax.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(7, 'Pedro Garcia', 'pgarcia@guardvax.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(8, 'Lourdes Torres', 'ltorres@guardvax.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(9, 'Ramon Villanueva', 'rvillanueva@guardvax.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(10, 'Elena Castillo', 'ecastillo@guardvax.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(11, 'Fernando Aquino', 'faquino@guardvax.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(12, 'Juan dela Cruz', 'juan.delacruz@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(13, 'Maria Clara Reyes', 'mclara.reyes@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(14, 'Roberto Santos', 'roberto.santos@yahoo.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(15, 'Luisa Fernandez', 'lfernandez@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(16, 'Antonio Gomez', 'agomez@hotmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(17, 'Carmen Villanueva', 'cvillanueva@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(18, 'Eduardo Lim', 'elim@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(19, 'Josephine Aquino', 'jaquino@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(20, 'Miguel Ramos', 'mramos@yahoo.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(21, 'Sofia Cruz', 'scruz@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(22, 'Benjamin Torres', 'btorres@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(23, 'Isabella Bautista', 'ibautista@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(24, 'Ricardo Castillo', 'rcastillo@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(25, 'Margarita Navarro', 'mnavarro@yahoo.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(26, 'Francisco Morales', 'fmorales@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(27, 'Teresita Ocampo', 'tocampo@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(28, 'Andres Pascual', 'apascual@hotmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(29, 'Corazon Velarde', 'cvelarde@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(30, 'Domingo Aguilar', 'daguilar@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(31, 'Natividad Domingo', 'ndomingo@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(32, 'Ernesto Padilla', 'epadilla@yahoo.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(33, 'Gloria Fuentes', 'gfuentes@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(34, 'Hector Ibarra', 'hibarra@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(35, 'Imelda Jacinto', 'ijacinto@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(36, 'Jaime Kampos', 'jkampos@yahoo.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(37, 'Kristina Luna', 'kluna@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(38, 'Leonardo Magtanggol', 'lmagtanggol@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(39, 'Melissa Narciso', 'mnarciso@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(40, 'Nicolas Orozco', 'norozco@hotmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(41, 'Olivia Perez', 'operez@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(42, 'Paolo Reyes', 'paolos.reyes31@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(43, 'Angelica Santos', 'agelicas.santos@yahoo.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(44, 'Ramon Cruz', 'rmns.cruz@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(45, 'Liza Bautista', 'liza1s.bautista@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(46, 'Marco Garcia', 'marcoss.garcia@hotmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(47, 'Rowena Torres', 'rowenass.torres@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(48, 'Dennis Lim', 'dennisss.lim@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(49, 'Cherry Aquino', 'cherryss.aquino@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(50, 'Vincent Ramos', 'vincentss.ramos@yahoo.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(51, 'Maricel Dela Cruz', 'maricelss.delacruz@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(52, 'Ronaldo Navarro', 'ronaldoss.navarro@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(53, 'Sheila Morales', 'sheilass.morales@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(54, 'Ariel Ocampo', 'arielss.ocampo@hotmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(55, 'Maribel Pascual', 'maribelss.pascual@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(56, 'Gilbert Velarde', 'gilbertss.velarde@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(57, 'Florinda Aguilar', 'florindass.aguilar@yahoo.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(58, 'Rommel Domingo', 'rommelsa.domingo@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(59, 'Josefa Padilla', 'josefasa.padilla@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(60, 'Efren Fuentes', 'efrensa.fuentes@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(61, 'Ligaya Ibarra', 'ligayasa.ibarra@hotmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(62, 'Ruben Jacinto', 'rubensa.jacinto@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(63, 'Norma Kampos', 'normasa.kampos@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(64, 'Danilo Luna', 'danilosa.luna@yahoo.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(65, 'Teresita Magtanggol', 'teresitasa.mgt@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(66, 'Wilfredo Narciso', 'wilfredosa.narciso@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(67, 'Perpetua Orozco', 'perpetuasa.orozco@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(68, 'Edilberto Perez', 'edilbertosa.perez@hotmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(69, 'Clarita Quinto', 'claritasa.quinto@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(70, 'Simplicio Roque', 'simpliciosa.roque@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(71, 'Adoracion Salazar', 'adoracionsa.salazar@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(72, 'Froilan Tamayo', 'froilansa.tamayo@yahoo.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(73, 'Zenaida Umali', 'zenaidasa.umali@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(74, 'Anastacio Valentin', 'anastaciosa.valentin@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(75, 'Resurreccion Wenceslao', 'rwrsa.wenceslao@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(76, 'Arsenio Xavier', 'arseniosa.xavier@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(77, 'Bienvenida Yap', 'bienvenaidasyap@hotmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(78, 'Crisostomo Zabala', 'crisostomosa.zabala@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(79, 'Dolores Abella', 'doloressa.abella@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(80, 'Emilio Buenaventura', 'emiliosa.buena@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(81, 'Felisa Cabrera', 'felisasa.cabrera@yahoo.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(82, 'Gaudencio Delos Reyes', 'gdelossa.reyes@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(83, 'Herculano Espiritu', 'herculanosa.esp@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(84, 'Immaculada Flores', 'immaculadasa.flores@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(85, 'Jacinto Guerrero', 'jacintosa.guerrero@hotmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(86, 'Kasandra Hermosa', 'kasandrasa.hermosa@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(87, 'Lorenzo Icasiano', 'lorenzosa.icasiano@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(88, 'Magdalena Jimenez', 'magdalenasa.jimenez@yahoo.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(89, 'Napoleon Kanapi', 'napoleonsa.kanapi@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(90, 'Olympia Lacson', 'olympiasa.lacson@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(91, 'Primitivo Macapagal', 'primitivosa.mcp@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(92, 'Quirina Natividad', 'quirinasa.natividad@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(93, 'Rodrigo Oliva', 'rodrigosa.oliva@hotmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(94, 'Susana Paredes', 'susanasa.paredes@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(95, 'Tomas Quizon', 'tomasa.squizon@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(96, 'Ursula Rebueno', 'ursulasa.rebueno@yahoo.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(97, 'Valentin Santiago', 'valentinsa.santiago@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(98, 'Warlita Tiongson', 'warlitasa.tiongson@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(99, 'Xyza Ureta', 'xyzasa.ureta@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(100, 'Yolanda Villafuerte', 'yolandasa.vf@hotmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(101, 'Zosimo Wagan', 'zosimosa.wagan@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(102, 'Amelia Reyes', 'ameliasa.reyes@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(103, 'Bernardo Santos', 'bernardosa.santos@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(104, 'Catalina Cruz', 'catalinasa.cruz@yahoo.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(105, 'Diosdado Bautista', 'diosdadosa.bautista@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(106, 'Esperanza Garcia', 'esperanzasa.garcia@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(107, 'Fausto Torres', 'faustosa.torres@hotmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(108, 'Gregoria Lim', 'gregoriasa.lim@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(109, 'Higino Aquino', 'higinosa.aquino@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(110, 'Ines Ramos', 'inessa.ramos@yahoo.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(111, 'Juanito Dela Cruz', 'juanitosa.delacruz@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, NULL, 'active', 1, '2026-04-13 08:56:37', '2026-04-13 08:56:37'),
(112, 'Brian jess Manalo', 'tisoyangelo31@gmail.com', '$2y$12$675eEdvfG93/zMOOkoVQYOQ0B/9yZNvxDK04Z9mBfBD0ql5os2UPa', 2, NULL, 'active', 1, '2026-04-13 03:04:55', '2026-04-13 03:05:27'),
(113, 'ANGELO GABRIEL TISOY', 'magtisoy@tip.edu.ph', '$2y$12$BC5ameTTIxzEzEHVDFezwO6CNj834IBgQu8geG6uEbff.da0lMiSG', 3, NULL, 'active', 1, '2026-04-13 03:06:49', '2026-04-13 03:08:17'),
(114, 'Cristina Reyes', 'creyes@guardvax.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, NULL, 'active', 1, '2026-04-13 21:23:22', '2026-04-13 21:23:22');

-- --------------------------------------------------------

--
-- Table structure for table `vaccinations`
--

CREATE TABLE `vaccinations` (
  `id` int(10) UNSIGNED NOT NULL,
  `patient_id` int(10) UNSIGNED NOT NULL,
  `vaccine_id` int(10) UNSIGNED NOT NULL,
  `dose_number` int(11) NOT NULL DEFAULT 1,
  `date_given` date NOT NULL,
  `administered_by` int(10) UNSIGNED NOT NULL,
  `batch_number` varchar(50) DEFAULT NULL,
  `site` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `next_dose_date` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vaccinations`
--

INSERT INTO `vaccinations` (`id`, `patient_id`, `vaccine_id`, `dose_number`, `date_given`, `administered_by`, `batch_number`, `site`, `notes`, `next_dose_date`, `created_at`) VALUES
(1, 1, 1, 1, '2026-01-10', 2, 'PFZ-2026-001', 'Left Arm', 'No adverse reaction', NULL, '2026-04-13 08:56:38'),
(2, 1, 1, 2, '2026-02-01', 2, 'PFZ-2026-002', 'Left Arm', 'Mild soreness reported', NULL, '2026-04-13 08:56:38'),
(3, 1, 3, 1, '2026-01-15', 3, 'FLU-2026-001', 'Right Arm', 'Annual flu vaccine', NULL, '2026-04-13 08:56:38'),
(4, 2, 1, 1, '2026-01-12', 3, 'PFZ-2026-001', 'Right Arm', 'No adverse reaction', NULL, '2026-04-13 08:56:38'),
(5, 2, 1, 2, '2026-02-03', 3, 'PFZ-2026-003', 'Right Arm', 'No adverse reaction', NULL, '2026-04-13 08:56:38'),
(6, 2, 4, 1, '2025-11-20', 4, 'HEP-2025-001', 'Left Arm', 'First dose Hep B series', NULL, '2026-04-13 08:56:38'),
(7, 2, 4, 2, '2025-12-20', 4, 'HEP-2025-002', 'Left Arm', 'Second dose Hep B series', NULL, '2026-04-13 08:56:38'),
(8, 3, 2, 1, '2026-01-20', 4, 'MOD-2026-001', 'Left Arm', 'No adverse reaction', NULL, '2026-04-13 08:56:38'),
(9, 3, 3, 1, '2026-01-20', 4, 'FLU-2026-002', 'Right Arm', 'Flu vaccine administered', NULL, '2026-04-13 08:56:38'),
(10, 3, 7, 1, '2025-10-05', 5, 'TDP-2025-001', 'Right Arm', 'Tdap booster', NULL, '2026-04-13 08:56:38'),
(11, 4, 1, 1, '2026-02-05', 5, 'PFZ-2026-004', 'Left Arm', 'No adverse reaction', NULL, '2026-04-13 08:56:38'),
(12, 4, 5, 1, '2026-02-10', 5, 'MMR-2026-001', 'Right Arm', 'MMR first dose', NULL, '2026-04-13 08:56:38'),
(13, 4, 5, 2, '2026-03-10', 5, 'MMR-2026-002', 'Right Arm', 'MMR second dose', NULL, '2026-04-13 08:56:38'),
(14, 5, 9, 1, '2025-12-01', 6, 'PCV-2025-001', 'Left Arm', 'Pneumonia vaccine', NULL, '2026-04-13 08:56:38'),
(15, 5, 3, 1, '2026-01-25', 6, 'FLU-2026-003', 'Right Arm', 'Annual flu shot', NULL, '2026-04-13 08:56:38'),
(16, 6, 1, 1, '2026-01-08', 6, 'PFZ-2026-005', 'Left Arm', 'No adverse reaction', NULL, '2026-04-13 08:56:38'),
(17, 6, 1, 2, '2026-01-29', 7, 'PFZ-2026-006', 'Left Arm', 'Mild fever after', NULL, '2026-04-13 08:56:38'),
(18, 6, 4, 1, '2026-02-15', 7, 'HEP-2026-001', 'Right Arm', 'Hep B first dose', NULL, '2026-04-13 08:56:38'),
(19, 7, 1, 1, '2026-01-14', 7, 'PFZ-2026-007', 'Right Arm', 'No adverse reaction', NULL, '2026-04-13 08:56:38'),
(20, 7, 8, 1, '2025-11-10', 8, 'HPV-2025-001', 'Left Arm', 'HPV first dose', NULL, '2026-04-13 08:56:38'),
(21, 7, 8, 2, '2026-01-10', 8, 'HPV-2026-001', 'Left Arm', 'HPV second dose', NULL, '2026-04-13 08:56:38'),
(22, 8, 3, 1, '2026-01-30', 8, 'FLU-2026-004', 'Right Arm', 'Annual flu', NULL, '2026-04-13 08:56:38'),
(23, 8, 9, 1, '2025-12-15', 9, 'PCV-2025-002', 'Left Arm', 'Pneumonia vaccine', NULL, '2026-04-13 08:56:38'),
(24, 9, 1, 1, '2026-02-18', 9, 'PFZ-2026-008', 'Left Arm', 'No adverse reaction', NULL, '2026-04-13 08:56:38'),
(25, 9, 5, 1, '2026-02-20', 9, 'MMR-2026-003', 'Right Arm', 'MMR first dose', NULL, '2026-04-13 08:56:38'),
(26, 10, 4, 1, '2025-10-15', 10, 'HEP-2025-003', 'Left Arm', 'Hep B series started', NULL, '2026-04-13 08:56:38'),
(27, 10, 4, 2, '2025-11-15', 10, 'HEP-2025-004', 'Left Arm', 'Hep B second dose', NULL, '2026-04-13 08:56:38'),
(28, 10, 4, 3, '2026-02-15', 10, 'HEP-2026-002', 'Left Arm', 'Hep B completed', NULL, '2026-04-13 08:56:38'),
(29, 11, 1, 1, '2026-01-22', 2, 'PFZ-2026-009', 'Right Arm', 'No adverse reaction', NULL, '2026-04-13 08:56:38'),
(30, 11, 3, 1, '2026-01-22', 2, 'FLU-2026-005', 'Left Arm', 'Same day flu shot', NULL, '2026-04-13 08:56:38'),
(31, 12, 7, 1, '2025-09-30', 3, 'TDP-2025-002', 'Right Arm', 'Tdap booster given', NULL, '2026-04-13 08:56:38'),
(32, 12, 6, 1, '2026-02-25', 3, 'VAR-2026-001', 'Left Arm', 'Varicella dose 1', NULL, '2026-04-13 08:56:38'),
(33, 13, 1, 1, '2026-03-01', 4, 'PFZ-2026-010', 'Left Arm', 'No adverse reaction', NULL, '2026-04-13 08:56:38'),
(34, 13, 1, 2, '2026-03-22', 4, 'PFZ-2026-011', 'Left Arm', 'Mild arm soreness', NULL, '2026-04-13 08:56:38'),
(35, 14, 3, 1, '2026-02-10', 5, 'FLU-2026-006', 'Right Arm', 'Annual influenza', NULL, '2026-04-13 08:56:38'),
(36, 14, 9, 1, '2026-01-05', 5, 'PCV-2026-001', 'Left Arm', 'Pneumonia vaccine', NULL, '2026-04-13 08:56:38'),
(37, 15, 8, 1, '2025-12-20', 6, 'HPV-2025-002', 'Left Thigh', 'HPV Gardasil dose 1', NULL, '2026-04-13 08:56:38'),
(38, 15, 8, 2, '2026-02-20', 6, 'HPV-2026-002', 'Left Thigh', 'HPV Gardasil dose 2', NULL, '2026-04-13 08:56:38'),
(39, 16, 2, 1, '2026-01-17', 7, 'MOD-2026-002', 'Right Arm', 'Moderna dose 1', NULL, '2026-04-13 08:56:38'),
(40, 16, 2, 2, '2026-02-14', 7, 'MOD-2026-003', 'Right Arm', 'Moderna dose 2', NULL, '2026-04-13 08:56:38'),
(41, 17, 5, 1, '2026-03-05', 8, 'MMR-2026-004', 'Right Arm', 'MMR first dose', NULL, '2026-04-13 08:56:38'),
(42, 18, 3, 1, '2026-02-28', 9, 'FLU-2026-007', 'Left Arm', 'Flu vaccine', NULL, '2026-04-13 08:56:38'),
(43, 19, 1, 1, '2026-03-10', 10, 'PFZ-2026-012', 'Right Arm', 'No adverse reaction', NULL, '2026-04-13 08:56:38'),
(44, 20, 4, 1, '2026-01-28', 2, 'HEP-2026-003', 'Left Arm', 'Hepatitis B dose 1', NULL, '2026-04-13 08:56:38'),
(45, 21, 7, 1, '2025-11-25', 3, 'TDP-2025-003', 'Right Deltoid', 'Tdap booster', NULL, '2026-04-13 08:56:38'),
(46, 22, 1, 1, '2026-03-15', 4, 'PFZ-2026-013', 'Left Arm', 'No adverse reaction', NULL, '2026-04-13 08:56:38'),
(47, 23, 9, 1, '2026-02-05', 5, 'PCV-2026-002', 'Left Arm', 'PCV13 vaccine', NULL, '2026-04-13 08:56:38'),
(48, 24, 6, 1, '2026-01-20', 6, 'VAR-2026-002', 'Right Arm', 'Varicella first dose', NULL, '2026-04-13 08:56:38'),
(49, 25, 3, 1, '2026-02-08', 7, 'FLU-2026-008', 'Right Arm', 'Influenza vaccine', NULL, '2026-04-13 08:56:38'),
(50, 26, 8, 1, '2025-10-30', 8, 'HPV-2025-003', 'Left Thigh', 'HPV first dose', NULL, '2026-04-13 08:56:38'),
(51, 27, 1, 1, '2026-03-18', 9, 'PFZ-2026-014', 'Left Arm', 'No adverse reaction', NULL, '2026-04-13 08:56:38'),
(52, 28, 2, 1, '2026-01-05', 10, 'MOD-2026-004', 'Right Arm', 'Moderna first dose', NULL, '2026-04-13 08:56:38'),
(53, 29, 4, 1, '2026-02-20', 2, 'HEP-2026-004', 'Left Arm', 'Hepatitis B dose 1', NULL, '2026-04-13 08:56:38'),
(54, 30, 5, 1, '2026-03-08', 3, 'MMR-2026-005', 'Right Arm', 'MMR vaccine', NULL, '2026-04-13 08:56:38'),
(55, 1, 7, 1, '2025-08-15', 4, 'TDP-2025-004', 'Right Arm', 'Tdap booster', NULL, '2026-04-13 08:56:38'),
(56, 5, 7, 1, '2025-07-20', 5, 'TDP-2025-005', 'Right Arm', 'Tdap booster', NULL, '2026-04-13 08:56:38'),
(57, 10, 7, 1, '2025-09-10', 6, 'TDP-2025-006', 'Left Arm', 'Tdap', NULL, '2026-04-13 08:56:38'),
(58, 15, 3, 1, '2026-02-01', 7, 'FLU-2026-009', 'Right Arm', 'Annual flu', NULL, '2026-04-13 08:56:38'),
(59, 20, 3, 1, '2026-01-18', 8, 'FLU-2026-010', 'Left Arm', 'Flu shot', NULL, '2026-04-13 08:56:38'),
(60, 31, 3, 1, '2026-01-05', 2, 'FLU-2026-011', 'Right Arm', NULL, NULL, '2026-04-13 08:56:38'),
(61, 32, 1, 1, '2026-01-08', 3, 'PFZ-2026-015', 'Left Arm', NULL, NULL, '2026-04-13 08:56:38'),
(62, 32, 1, 2, '2026-01-29', 3, 'PFZ-2026-016', 'Left Arm', NULL, NULL, '2026-04-13 08:56:38'),
(63, 33, 4, 1, '2025-11-10', 4, 'HEP-2025-010', 'Left Arm', NULL, NULL, '2026-04-13 08:56:38'),
(64, 33, 3, 1, '2026-02-12', 4, 'FLU-2026-012', 'Right Arm', NULL, NULL, '2026-04-13 08:56:38'),
(65, 34, 7, 1, '2025-10-20', 5, 'TDP-2025-010', 'Right Arm', NULL, NULL, '2026-04-13 08:56:38'),
(66, 35, 9, 1, '2025-12-10', 6, 'PCV-2025-005', 'Left Arm', NULL, NULL, '2026-04-13 08:56:38'),
(67, 36, 1, 1, '2026-02-22', 7, 'PFZ-2026-017', 'Left Arm', NULL, NULL, '2026-04-13 08:56:38'),
(68, 37, 5, 1, '2026-03-01', 8, 'MMR-2026-006', 'Right Arm', NULL, NULL, '2026-04-13 08:56:38'),
(69, 38, 3, 1, '2026-01-18', 9, 'FLU-2026-013', 'Left Arm', NULL, NULL, '2026-04-13 08:56:38'),
(70, 39, 8, 1, '2026-02-05', 10, 'HPV-2026-003', 'Left Thigh', NULL, NULL, '2026-04-13 08:56:38'),
(71, 40, 4, 1, '2026-01-22', 2, 'HEP-2026-005', 'Left Arm', NULL, NULL, '2026-04-13 08:56:38'),
(72, 40, 4, 2, '2026-02-22', 2, 'HEP-2026-006', 'Left Arm', NULL, NULL, '2026-04-13 08:56:38'),
(73, 41, 2, 1, '2026-03-10', 3, 'MOD-2026-005', 'Right Arm', NULL, NULL, '2026-04-13 08:56:38'),
(74, 42, 3, 1, '2026-02-28', 4, 'FLU-2026-014', 'Right Arm', NULL, NULL, '2026-04-13 08:56:38'),
(75, 43, 1, 1, '2026-03-05', 5, 'PFZ-2026-018', 'Left Arm', NULL, NULL, '2026-04-13 08:56:38'),
(76, 44, 7, 1, '2025-09-15', 6, 'TDP-2025-011', 'Right Arm', NULL, NULL, '2026-04-13 08:56:38'),
(77, 45, 6, 1, '2026-01-14', 7, 'VAR-2026-003', 'Left Arm', NULL, NULL, '2026-04-13 08:56:38'),
(78, 46, 3, 1, '2026-03-20', 8, 'FLU-2026-015', 'Right Arm', NULL, NULL, '2026-04-13 08:56:38'),
(79, 47, 9, 1, '2026-02-08', 9, 'PCV-2026-003', 'Left Arm', NULL, NULL, '2026-04-13 08:56:38'),
(80, 48, 1, 1, '2026-03-22', 10, 'PFZ-2026-019', 'Left Arm', NULL, NULL, '2026-04-13 08:56:38'),
(81, 49, 4, 1, '2026-01-30', 2, 'HEP-2026-007', 'Left Arm', NULL, NULL, '2026-04-13 08:56:38'),
(82, 50, 5, 1, '2026-03-15', 3, 'MMR-2026-007', 'Right Arm', NULL, NULL, '2026-04-13 08:56:38'),
(83, 51, 3, 1, '2026-02-20', 4, 'FLU-2026-016', 'Right Arm', NULL, NULL, '2026-04-13 08:56:38'),
(84, 52, 1, 1, '2026-03-18', 5, 'PFZ-2026-020', 'Left Arm', NULL, NULL, '2026-04-13 08:56:38'),
(85, 53, 7, 1, '2025-11-05', 6, 'TDP-2025-012', 'Right Arm', NULL, NULL, '2026-04-13 08:56:38'),
(86, 54, 8, 1, '2026-01-25', 7, 'HPV-2026-004', 'Left Thigh', NULL, NULL, '2026-04-13 08:56:38'),
(87, 55, 2, 1, '2026-02-15', 8, 'MOD-2026-006', 'Right Arm', NULL, NULL, '2026-04-13 08:56:38'),
(88, 56, 3, 1, '2026-03-08', 9, 'FLU-2026-017', 'Left Arm', NULL, NULL, '2026-04-13 08:56:38'),
(89, 57, 4, 1, '2026-01-10', 10, 'HEP-2026-008', 'Left Arm', NULL, NULL, '2026-04-13 08:56:38'),
(90, 58, 9, 1, '2026-02-18', 2, 'PCV-2026-004', 'Left Arm', NULL, NULL, '2026-04-13 08:56:38'),
(91, 59, 1, 1, '2026-03-25', 3, 'PFZ-2026-021', 'Left Arm', NULL, NULL, '2026-04-13 08:56:38'),
(92, 60, 6, 1, '2026-01-08', 4, 'VAR-2026-004', 'Right Arm', NULL, NULL, '2026-04-13 08:56:38'),
(93, 61, 3, 1, '2026-02-25', 5, 'FLU-2026-018', 'Right Arm', NULL, NULL, '2026-04-13 08:56:38'),
(94, 62, 7, 1, '2025-10-10', 6, 'TDP-2025-013', 'Right Arm', NULL, NULL, '2026-04-13 08:56:38'),
(95, 63, 5, 1, '2026-03-12', 7, 'MMR-2026-008', 'Right Arm', NULL, NULL, '2026-04-13 08:56:38'),
(96, 64, 3, 1, '2026-01-28', 8, 'FLU-2026-019', 'Left Arm', NULL, NULL, '2026-04-13 08:56:38'),
(97, 65, 1, 1, '2026-03-28', 9, 'PFZ-2026-022', 'Left Arm', NULL, NULL, '2026-04-13 08:56:38'),
(98, 66, 4, 1, '2026-02-10', 10, 'HEP-2026-009', 'Left Arm', NULL, NULL, '2026-04-13 08:56:38'),
(99, 67, 9, 1, '2026-01-20', 2, 'PCV-2026-005', 'Left Arm', NULL, NULL, '2026-04-13 08:56:38'),
(100, 68, 2, 1, '2026-02-28', 3, 'MOD-2026-007', 'Right Arm', NULL, NULL, '2026-04-13 08:56:38'),
(101, 69, 3, 1, '2026-03-18', 4, 'FLU-2026-020', 'Right Arm', NULL, NULL, '2026-04-13 08:56:38'),
(102, 70, 8, 1, '2026-01-15', 5, 'HPV-2026-005', 'Left Thigh', NULL, NULL, '2026-04-13 08:56:38'),
(103, 101, 7, 4, '2026-04-13', 112, '1', 'Right Arm', 'NONE', '2026-04-30', '2026-04-13 12:59:55'),
(104, 101, 10, 4, '2026-04-30', 112, '1', 'Left Arm', 'none', '2026-04-30', '2026-04-14 06:49:49');

-- --------------------------------------------------------

--
-- Table structure for table `vaccines`
--

CREATE TABLE `vaccines` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `manufacturer` varchar(150) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `doses_required` int(11) NOT NULL DEFAULT 1,
  `interval_days` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vaccines`
--

INSERT INTO `vaccines` (`id`, `name`, `manufacturer`, `description`, `doses_required`, `interval_days`, `is_active`, `created_at`) VALUES
(1, 'COVID-19 (Pfizer-BioNTech)', 'Pfizer', 'mRNA vaccine for COVID-19', 2, 21, 1, '2026-04-13 08:56:14'),
(2, 'COVID-19 (Moderna)', 'Moderna', 'mRNA vaccine for COVID-19', 2, 28, 1, '2026-04-13 08:56:14'),
(3, 'Influenza (Flu Shot)', 'Sanofi Pasteur', 'Annual flu vaccine', 1, NULL, 1, '2026-04-13 08:56:14'),
(4, 'Hepatitis B', 'Merck', 'Hepatitis B 3-dose series', 3, 30, 1, '2026-04-13 08:56:14'),
(5, 'MMR (Measles, Mumps, Rubella)', 'Merck', 'Combined MMR vaccine', 2, 28, 1, '2026-04-13 08:56:14'),
(6, 'Varicella (Chickenpox)', 'Merck', 'Varicella zoster virus vaccine', 2, 28, 1, '2026-04-13 08:56:14'),
(7, 'Tetanus (Tdap)', 'GSK', 'Tetanus, diphtheria, pertussis booster', 1, NULL, 1, '2026-04-13 08:56:14'),
(8, 'HPV (Gardasil)', 'Merck', '9-valent HPV vaccine', 3, 60, 1, '2026-04-13 08:56:14'),
(9, 'Pneumococcal (PCV13)', 'Pfizer', 'Pneumonia vaccine', 1, NULL, 1, '2026-04-13 08:56:14'),
(10, 'Rabies', 'Sanofi', 'Pre/post-exposure rabies prophylaxis', 3, 7, 1, '2026-04-13 08:56:14'),
(11, 'try', 'gelo', 'try lang', 1, 24, 1, '2026-04-13 03:03:32');

-- --------------------------------------------------------

--
-- Table structure for table `verification_codes`
--

CREATE TABLE `verification_codes` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `code` varchar(10) NOT NULL,
  `purpose` enum('email_verify','2fa_login','password_reset') NOT NULL DEFAULT 'email_verify',
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `verification_codes`
--

INSERT INTO `verification_codes` (`id`, `user_id`, `code`, `purpose`, `expires_at`, `used`, `created_at`) VALUES
(1, 112, '233936', 'email_verify', '2026-04-13 03:19:55', 1, '2026-04-13 03:04:56'),
(2, 112, '756331', '2fa_login', '2026-04-13 03:20:46', 1, '2026-04-13 03:05:46'),
(3, 113, '504384', '2fa_login', '2026-04-13 03:22:39', 1, '2026-04-13 03:07:39'),
(4, 112, '525981', '2fa_login', '2026-04-13 03:23:37', 1, '2026-04-13 03:08:37'),
(5, 113, '678857', '2fa_login', '2026-04-13 06:12:48', 1, '2026-04-13 05:57:48'),
(6, 112, '808053', '2fa_login', '2026-04-13 09:44:35', 1, '2026-04-13 09:29:35'),
(7, 112, '985021', '2fa_login', '2026-04-13 10:01:23', 1, '2026-04-13 09:46:23'),
(8, 113, '587314', '2fa_login', '2026-04-13 10:09:55', 1, '2026-04-13 09:54:55'),
(9, 113, '278344', '2fa_login', '2026-04-13 13:12:53', 1, '2026-04-13 12:57:53'),
(10, 112, '131283', '2fa_login', '2026-04-13 13:13:33', 1, '2026-04-13 12:58:33'),
(11, 113, '270543', '2fa_login', '2026-04-13 13:16:37', 1, '2026-04-13 13:01:37'),
(12, 113, '181011', '2fa_login', '2026-04-14 01:21:55', 1, '2026-04-14 01:06:55'),
(13, 112, '790709', '2fa_login', '2026-04-14 04:10:09', 1, '2026-04-14 03:55:09'),
(14, 113, '061318', '2fa_login', '2026-04-14 04:12:18', 1, '2026-04-14 03:57:18'),
(15, 113, '914641', '2fa_login', '2026-04-14 04:12:30', 1, '2026-04-14 03:57:30'),
(16, 112, '278727', '2fa_login', '2026-04-14 04:14:57', 1, '2026-04-14 03:59:57'),
(17, 112, '448096', '2fa_login', '2026-04-14 06:40:05', 1, '2026-04-14 06:25:05'),
(18, 112, '023961', '2fa_login', '2026-04-14 06:40:11', 1, '2026-04-14 06:25:11'),
(19, 112, '740152', '2fa_login', '2026-04-14 06:40:17', 1, '2026-04-14 06:25:17'),
(20, 113, '088821', '2fa_login', '2026-04-14 07:01:33', 1, '2026-04-14 06:46:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admissions`
--
ALTER TABLE `admissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_adm_dept` (`department_id`),
  ADD KEY `fk_adm_staff` (`admitted_by`),
  ADD KEY `idx_adm_patient` (`patient_id`),
  ADD KEY `idx_adm_status` (`status`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_appt_dept` (`department_id`),
  ADD KEY `fk_appt_doctor` (`doctor_id`),
  ADD KEY `fk_appt_created` (`created_by`),
  ADD KEY `idx_appt_patient` (`patient_id`),
  ADD KEY `idx_appt_date` (`appointment_date`),
  ADD KEY `idx_appt_status` (`status`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_audit_user` (`user_id`),
  ADD KEY `idx_audit_created` (`created_at`);

--
-- Indexes for table `billing`
--
ALTER TABLE `billing`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bill_number` (`bill_number`),
  ADD KEY `fk_bill_admission` (`admission_id`),
  ADD KEY `fk_bill_staff` (`created_by`),
  ADD KEY `idx_bill_patient` (`patient_id`),
  ADD KEY `idx_bill_status` (`status`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_inv_category` (`category`);

--
-- Indexes for table `lab_results`
--
ALTER TABLE `lab_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_lab_doctor` (`requested_by`),
  ADD KEY `fk_lab_admission` (`admission_id`),
  ADD KEY `fk_lab_tech` (`performed_by`),
  ADD KEY `idx_lab_patient` (`patient_id`);

--
-- Indexes for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_mr_user` (`recorded_by`),
  ADD KEY `idx_medical_patient` (`patient_id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `patient_code` (`patient_code`),
  ADD KEY `fk_patients_nurse` (`registered_by`),
  ADD KEY `idx_patients_user` (`user_id`),
  ADD KEY `idx_patients_code` (`patient_code`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_presc_doctor` (`prescribed_by`),
  ADD KEY `fk_presc_admission` (`admission_id`),
  ADD KEY `fk_presc_dispensed` (`dispensed_by`),
  ADD KEY `idx_presc_patient` (`patient_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_email` (`email`),
  ADD KEY `idx_users_role` (`role_id`),
  ADD KEY `fk_users_dept` (`department_id`);

--
-- Indexes for table `vaccinations`
--
ALTER TABLE `vaccinations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_vacc_vaccine` (`vaccine_id`),
  ADD KEY `fk_vacc_nurse` (`administered_by`),
  ADD KEY `idx_vaccinations_patient` (`patient_id`),
  ADD KEY `idx_vaccinations_date` (`date_given`);

--
-- Indexes for table `vaccines`
--
ALTER TABLE `vaccines`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `verification_codes`
--
ALTER TABLE `verification_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_vc_user` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admissions`
--
ALTER TABLE `admissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `billing`
--
ALTER TABLE `billing`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `lab_results`
--
ALTER TABLE `lab_results`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `medical_records`
--
ALTER TABLE `medical_records`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT for table `vaccinations`
--
ALTER TABLE `vaccinations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `vaccines`
--
ALTER TABLE `vaccines`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `verification_codes`
--
ALTER TABLE `verification_codes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admissions`
--
ALTER TABLE `admissions`
  ADD CONSTRAINT `fk_adm_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `fk_adm_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_adm_staff` FOREIGN KEY (`admitted_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `fk_appt_created` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_appt_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `fk_appt_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_appt_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `fk_al_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `billing`
--
ALTER TABLE `billing`
  ADD CONSTRAINT `fk_bill_admission` FOREIGN KEY (`admission_id`) REFERENCES `admissions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_bill_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bill_staff` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `lab_results`
--
ALTER TABLE `lab_results`
  ADD CONSTRAINT `fk_lab_admission` FOREIGN KEY (`admission_id`) REFERENCES `admissions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_lab_doctor` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_lab_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_lab_tech` FOREIGN KEY (`performed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD CONSTRAINT `fk_mr_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_mr_user` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `fk_patients_nurse` FOREIGN KEY (`registered_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_patients_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD CONSTRAINT `fk_presc_admission` FOREIGN KEY (`admission_id`) REFERENCES `admissions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_presc_dispensed` FOREIGN KEY (`dispensed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_presc_doctor` FOREIGN KEY (`prescribed_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_presc_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_dept` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Constraints for table `vaccinations`
--
ALTER TABLE `vaccinations`
  ADD CONSTRAINT `fk_vacc_nurse` FOREIGN KEY (`administered_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_vacc_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_vacc_vaccine` FOREIGN KEY (`vaccine_id`) REFERENCES `vaccines` (`id`);

--
-- Constraints for table `verification_codes`
--
ALTER TABLE `verification_codes`
  ADD CONSTRAINT `fk_vc_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
