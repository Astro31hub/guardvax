-- ============================================================
-- GuardVAX Hospital Vaccination & Health Data System
-- Database Schema v1.0
-- ============================================================

CREATE DATABASE IF NOT EXISTS guardvax CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE guardvax;

-- ============================================================
-- ROLES
-- ============================================================
CREATE TABLE roles (
    id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name     VARCHAR(50) NOT NULL UNIQUE,
    label    VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

INSERT INTO roles (name, label) VALUES
    ('admin',   'System Administrator'),
    ('nurse',   'Registered Nurse'),
    ('patient', 'Patient');

-- ============================================================
-- USERS
-- ============================================================
CREATE TABLE users (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(150)        NOT NULL,
    email         VARCHAR(150)        NOT NULL UNIQUE,
    password      VARCHAR(255)        NOT NULL,
    role_id       INT UNSIGNED        NOT NULL,
    status        ENUM('active','inactive','pending') NOT NULL DEFAULT 'pending',
    email_verified TINYINT(1)         NOT NULL DEFAULT 0,
    created_at    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB;

-- ============================================================
-- EMAIL VERIFICATION / 2FA CODES
-- ============================================================
CREATE TABLE verification_codes (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NOT NULL,
    code       VARCHAR(10)  NOT NULL,
    purpose    ENUM('email_verify','2fa_login','password_reset') NOT NULL DEFAULT 'email_verify',
    expires_at DATETIME     NOT NULL,
    used       TINYINT(1)   NOT NULL DEFAULT 0,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_vc_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- PATIENTS (extends users where role = patient)
-- ============================================================
CREATE TABLE patients (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id        INT UNSIGNED    NOT NULL UNIQUE,
    patient_code   VARCHAR(20)     NOT NULL UNIQUE,   -- e.g. GVX-000001
    date_of_birth  DATE            NOT NULL,
    gender         ENUM('Male','Female','Other') NOT NULL,
    blood_type     VARCHAR(5)      NULL,
    address        TEXT            NULL,
    phone          VARCHAR(20)     NULL,
    emergency_contact_name  VARCHAR(150) NULL,
    emergency_contact_phone VARCHAR(20)  NULL,
    allergies      TEXT            NULL,
    notes          TEXT            NULL,
    registered_by  INT UNSIGNED    NULL,              -- nurse user_id
    created_at     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_patients_user     FOREIGN KEY (user_id)       REFERENCES users(id),
    CONSTRAINT fk_patients_nurse    FOREIGN KEY (registered_by) REFERENCES users(id)
) ENGINE=InnoDB;

-- ============================================================
-- VACCINES
-- ============================================================
CREATE TABLE vaccines (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name           VARCHAR(150) NOT NULL,
    manufacturer   VARCHAR(150) NULL,
    description    TEXT         NULL,
    doses_required INT          NOT NULL DEFAULT 1,
    interval_days  INT          NULL,       -- recommended days between doses
    is_active      TINYINT(1)   NOT NULL DEFAULT 1,
    created_at     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO vaccines (name, manufacturer, description, doses_required, interval_days) VALUES
('COVID-19 (Pfizer-BioNTech)',  'Pfizer',       'mRNA vaccine for COVID-19',                2, 21),
('COVID-19 (Moderna)',          'Moderna',       'mRNA vaccine for COVID-19',                2, 28),
('Influenza (Flu Shot)',        'Sanofi Pasteur','Annual flu vaccine',                       1, NULL),
('Hepatitis B',                 'Merck',         'Hepatitis B 3-dose series',                3, 30),
('MMR (Measles, Mumps, Rubella)','Merck',        'Combined MMR vaccine',                     2, 28),
('Varicella (Chickenpox)',       'Merck',        'Varicella zoster virus vaccine',           2, 28),
('Tetanus (Tdap)',               'GSK',          'Tetanus, diphtheria, pertussis booster',   1, NULL),
('HPV (Gardasil)',               'Merck',        '9-valent HPV vaccine',                     3, 60),
('Pneumococcal (PCV13)',         'Pfizer',       'Pneumonia vaccine',                        1, NULL),
('Rabies',                       'Sanofi',       'Pre/post-exposure rabies prophylaxis',     3, 7);

-- ============================================================
-- VACCINATIONS (patient vaccination records)
-- ============================================================
CREATE TABLE vaccinations (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id      INT UNSIGNED    NOT NULL,
    vaccine_id      INT UNSIGNED    NOT NULL,
    dose_number     INT             NOT NULL DEFAULT 1,
    date_given      DATE            NOT NULL,
    administered_by INT UNSIGNED    NOT NULL,           -- nurse user_id
    batch_number    VARCHAR(50)     NULL,
    site            VARCHAR(50)     NULL,                -- e.g. Left Arm
    notes           TEXT            NULL,
    next_dose_date  DATE            NULL,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_vacc_patient  FOREIGN KEY (patient_id)      REFERENCES patients(id) ON DELETE CASCADE,
    CONSTRAINT fk_vacc_vaccine  FOREIGN KEY (vaccine_id)      REFERENCES vaccines(id),
    CONSTRAINT fk_vacc_nurse    FOREIGN KEY (administered_by) REFERENCES users(id)
) ENGINE=InnoDB;

-- ============================================================
-- MEDICAL RECORDS
-- ============================================================
CREATE TABLE medical_records (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id      INT UNSIGNED NOT NULL,
    record_type     ENUM('Consultation','Lab Result','Diagnosis','Prescription','Other') NOT NULL DEFAULT 'Consultation',
    title           VARCHAR(200) NOT NULL,
    description     TEXT         NOT NULL,
    recorded_by     INT UNSIGNED NOT NULL,
    record_date     DATE         NOT NULL,
    attachment_path VARCHAR(255) NULL,
    created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_mr_patient FOREIGN KEY (patient_id)  REFERENCES patients(id) ON DELETE CASCADE,
    CONSTRAINT fk_mr_user    FOREIGN KEY (recorded_by) REFERENCES users(id)
) ENGINE=InnoDB;

-- ============================================================
-- AUDIT LOGS
-- ============================================================
CREATE TABLE audit_logs (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED NULL,
    action      VARCHAR(100) NOT NULL,
    table_name  VARCHAR(100) NULL,
    record_id   INT UNSIGNED NULL,
    description TEXT         NULL,
    ip_address  VARCHAR(45)  NULL,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_al_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- DEFAULT ADMIN ACCOUNT
-- Password: Admin@GuardVAX1
-- ============================================================
INSERT INTO users (name, email, password, role_id, status, email_verified)
VALUES (
    'System Administrator',
    'admin@guardvax.com',
    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    1, 'active', 1
);
-- NOTE: Default password hash above is for "Admin@GuardVAX1"
-- Change this immediately after first login!

-- ============================================================
-- INDEXES for performance
-- ============================================================
CREATE INDEX idx_users_email         ON users(email);
CREATE INDEX idx_users_role          ON users(role_id);
CREATE INDEX idx_patients_user       ON patients(user_id);
CREATE INDEX idx_patients_code       ON patients(patient_code);
CREATE INDEX idx_vaccinations_patient ON vaccinations(patient_id);
CREATE INDEX idx_vaccinations_date   ON vaccinations(date_given);
CREATE INDEX idx_medical_patient     ON medical_records(patient_id);
CREATE INDEX idx_audit_user          ON audit_logs(user_id);
CREATE INDEX idx_audit_created       ON audit_logs(created_at);
