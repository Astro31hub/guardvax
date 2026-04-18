-- ============================================================
-- GuardVAX Hospital Management System — Expanded Schema
-- Add these tables to your existing guardvax database
-- Run this in phpMyAdmin after the original guardvax.sql
-- ============================================================

USE guardvax;

-- ── 1. DEPARTMENTS ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS departments (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL UNIQUE,
    description TEXT         NULL,
    location    VARCHAR(100) NULL,
    is_active   TINYINT(1)   NOT NULL DEFAULT 1,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO departments (name, description, location) VALUES
('Outpatient',        'General outpatient consultations',             'Building A, Ground Floor'),
('Emergency',         '24/7 emergency services',                      'Building A, Ground Floor'),
('Pediatrics',        'Child health and development',                 'Building B, 2nd Floor'),
('Internal Medicine', 'Adult internal medicine',                      'Building B, 3rd Floor'),
('Obstetrics',        'Maternal and prenatal care',                   'Building C, 2nd Floor'),
('Surgery',           'Surgical procedures and recovery',             'Building C, 3rd Floor'),
('Laboratory',        'Diagnostic laboratory services',               'Building A, 1st Floor'),
('Pharmacy',          'Medicines and pharmaceutical services',        'Building A, Ground Floor'),
('Radiology',         'X-ray, ultrasound, and imaging',               'Building D, 1st Floor'),
('Vaccination Unit',  'Immunization and vaccination services',        'Building A, 1st Floor');

-- ── 2. Update users table to include department ─────────────
ALTER TABLE users ADD COLUMN IF NOT EXISTS department_id INT UNSIGNED NULL AFTER role_id;
ALTER TABLE users ADD CONSTRAINT fk_users_dept FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL;

-- ── 3. APPOINTMENTS ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS appointments (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id       INT UNSIGNED NOT NULL,
    department_id    INT UNSIGNED NOT NULL,
    doctor_id        INT UNSIGNED NULL,
    appointment_date DATE         NOT NULL,
    appointment_time TIME         NOT NULL,
    reason           TEXT         NULL,
    status           ENUM('Pending','Confirmed','Cancelled','Completed','No-show')
                     NOT NULL DEFAULT 'Pending',
    notes            TEXT         NULL,
    created_by       INT UNSIGNED NOT NULL,
    created_at       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_appt_patient FOREIGN KEY (patient_id)    REFERENCES patients(id)    ON DELETE CASCADE,
    CONSTRAINT fk_appt_dept    FOREIGN KEY (department_id) REFERENCES departments(id),
    CONSTRAINT fk_appt_doctor  FOREIGN KEY (doctor_id)     REFERENCES users(id)       ON DELETE SET NULL,
    CONSTRAINT fk_appt_created FOREIGN KEY (created_by)    REFERENCES users(id)
) ENGINE=InnoDB;

-- ── 4. ADMISSIONS ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS admissions (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id     INT UNSIGNED NOT NULL,
    department_id  INT UNSIGNED NOT NULL,
    admitted_by    INT UNSIGNED NOT NULL,
    room_number    VARCHAR(20)  NULL,
    bed_number     VARCHAR(20)  NULL,
    admission_date DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    discharge_date DATETIME     NULL,
    reason         TEXT         NOT NULL,
    diagnosis      TEXT         NULL,
    status         ENUM('Admitted','Discharged','Transferred','Critical')
                   NOT NULL DEFAULT 'Admitted',
    discharge_notes TEXT        NULL,
    created_at     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_adm_patient FOREIGN KEY (patient_id)    REFERENCES patients(id)    ON DELETE CASCADE,
    CONSTRAINT fk_adm_dept    FOREIGN KEY (department_id) REFERENCES departments(id),
    CONSTRAINT fk_adm_staff   FOREIGN KEY (admitted_by)   REFERENCES users(id)
) ENGINE=InnoDB;

-- ── 5. PRESCRIPTIONS ────────────────────────────────────────
CREATE TABLE IF NOT EXISTS prescriptions (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id    INT UNSIGNED NOT NULL,
    prescribed_by INT UNSIGNED NOT NULL,
    admission_id  INT UNSIGNED NULL,
    medicine_name VARCHAR(200) NOT NULL,
    dosage        VARCHAR(100) NOT NULL,
    frequency     VARCHAR(100) NOT NULL,
    duration      VARCHAR(100) NULL,
    quantity      INT          NULL,
    instructions  TEXT         NULL,
    status        ENUM('Pending','Dispensed','Cancelled')
                  NOT NULL DEFAULT 'Pending',
    prescribed_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    dispensed_at  DATETIME     NULL,
    dispensed_by  INT UNSIGNED NULL,
    CONSTRAINT fk_presc_patient   FOREIGN KEY (patient_id)    REFERENCES patients(id)    ON DELETE CASCADE,
    CONSTRAINT fk_presc_doctor    FOREIGN KEY (prescribed_by) REFERENCES users(id),
    CONSTRAINT fk_presc_admission FOREIGN KEY (admission_id)  REFERENCES admissions(id)  ON DELETE SET NULL,
    CONSTRAINT fk_presc_dispensed FOREIGN KEY (dispensed_by)  REFERENCES users(id)       ON DELETE SET NULL
) ENGINE=InnoDB;

-- ── 6. LAB REQUESTS & RESULTS ───────────────────────────────
CREATE TABLE IF NOT EXISTS lab_results (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id     INT UNSIGNED NOT NULL,
    requested_by   INT UNSIGNED NOT NULL,
    admission_id   INT UNSIGNED NULL,
    test_name      VARCHAR(200) NOT NULL,
    test_category  ENUM('Blood','Urine','Imaging','Microbiology','Chemistry','Other')
                   NOT NULL DEFAULT 'Other',
    result         TEXT         NULL,
    normal_range   VARCHAR(100) NULL,
    unit           VARCHAR(50)  NULL,
    status         ENUM('Requested','Processing','Completed','Cancelled')
                   NOT NULL DEFAULT 'Requested',
    is_abnormal    TINYINT(1)   DEFAULT 0,
    notes          TEXT         NULL,
    requested_date DATE         NOT NULL,
    result_date    DATE         NULL,
    performed_by   INT UNSIGNED NULL,
    created_at     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_lab_patient   FOREIGN KEY (patient_id)   REFERENCES patients(id)   ON DELETE CASCADE,
    CONSTRAINT fk_lab_doctor    FOREIGN KEY (requested_by) REFERENCES users(id),
    CONSTRAINT fk_lab_admission FOREIGN KEY (admission_id) REFERENCES admissions(id) ON DELETE SET NULL,
    CONSTRAINT fk_lab_tech      FOREIGN KEY (performed_by) REFERENCES users(id)      ON DELETE SET NULL
) ENGINE=InnoDB;

-- ── 7. INVENTORY ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS inventory (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    item_name      VARCHAR(200) NOT NULL,
    category       ENUM('Medicine','Vaccine','Medical Supply','Equipment','Other')
                   NOT NULL DEFAULT 'Medicine',
    description    TEXT         NULL,
    quantity       INT          NOT NULL DEFAULT 0,
    unit           VARCHAR(50)  NOT NULL DEFAULT 'pcs',
    unit_price     DECIMAL(10,2) NULL,
    reorder_level  INT          NOT NULL DEFAULT 10,
    expiry_date    DATE         NULL,
    supplier       VARCHAR(200) NULL,
    location       VARCHAR(100) NULL,
    is_active      TINYINT(1)   NOT NULL DEFAULT 1,
    created_at     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO inventory (item_name, category, quantity, unit, reorder_level, unit_price) VALUES
('Paracetamol 500mg',     'Medicine',       500,  'tablets', 100, 2.50),
('Amoxicillin 500mg',     'Medicine',       300,  'capsules',50,  8.00),
('Ibuprofen 400mg',       'Medicine',       400,  'tablets', 100, 5.00),
('Metformin 500mg',       'Medicine',       200,  'tablets', 50,  4.00),
('Amlodipine 5mg',        'Medicine',       150,  'tablets', 50,  6.50),
('Disposable Gloves',     'Medical Supply', 1000, 'pairs',   200, 3.00),
('Surgical Mask',         'Medical Supply', 2000, 'pcs',     500, 2.00),
('Syringe 5ml',           'Medical Supply', 800,  'pcs',     200, 5.00),
('IV Fluids (PNSS)',      'Medical Supply', 200,  'bags',    50,  85.00),
('Blood Glucose Strips',  'Medical Supply', 300,  'pcs',     100, 12.00);

-- ── 8. BILLING ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS billing (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id      INT UNSIGNED NOT NULL,
    admission_id    INT UNSIGNED NULL,
    bill_number     VARCHAR(20)  NOT NULL UNIQUE,
    consultation_fee DECIMAL(10,2) DEFAULT 0.00,
    medicine_fee    DECIMAL(10,2) DEFAULT 0.00,
    lab_fee         DECIMAL(10,2) DEFAULT 0.00,
    room_fee        DECIMAL(10,2) DEFAULT 0.00,
    other_fee       DECIMAL(10,2) DEFAULT 0.00,
    total_amount    DECIMAL(10,2) DEFAULT 0.00,
    discount        DECIMAL(10,2) DEFAULT 0.00,
    amount_paid     DECIMAL(10,2) DEFAULT 0.00,
    payment_method  ENUM('Cash','PhilHealth','HMO','Credit Card','Other') NULL,
    status          ENUM('Unpaid','Partial','Paid','Waived')
                    NOT NULL DEFAULT 'Unpaid',
    notes           TEXT         NULL,
    created_by      INT UNSIGNED NOT NULL,
    paid_at         DATETIME     NULL,
    created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_bill_patient   FOREIGN KEY (patient_id)   REFERENCES patients(id)   ON DELETE CASCADE,
    CONSTRAINT fk_bill_admission FOREIGN KEY (admission_id) REFERENCES admissions(id) ON DELETE SET NULL,
    CONSTRAINT fk_bill_staff     FOREIGN KEY (created_by)   REFERENCES users(id)
) ENGINE=InnoDB;

-- ── Indexes for new tables ────────────────────────────────────
CREATE INDEX idx_appt_patient  ON appointments(patient_id);
CREATE INDEX idx_appt_date     ON appointments(appointment_date);
CREATE INDEX idx_appt_status   ON appointments(status);
CREATE INDEX idx_adm_patient   ON admissions(patient_id);
CREATE INDEX idx_adm_status    ON admissions(status);
CREATE INDEX idx_presc_patient ON prescriptions(patient_id);
CREATE INDEX idx_lab_patient   ON lab_results(patient_id);
CREATE INDEX idx_inv_category  ON inventory(category);
CREATE INDEX idx_bill_patient  ON billing(patient_id);
CREATE INDEX idx_bill_status   ON billing(status);
