CREATE DATABASE IF NOT EXISTS pci dss CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pci dss;

-- Users table (simple auth)
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(64) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('Admin','Auditor','User') DEFAULT 'User',
  `mfa_secret` varchar(32) DEFAULT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `username`, `password_hash`, `role`, `mfa_secret`, `reset_token`, `token_expiry`) VALUES
(1, 'tejk', '$2y$10$pXGfPo/ti0dOD2Wfn056eOtibR2w2rlQJFH1RKQah7L3B8zNPElSW', 'Auditor', NULL, NULL, '2025-11-09 21:34:30');--defaultpassword is NewStrongPassword1!

-- Control library
CREATE TABLE controls (
  id INT AUTO_INCREMENT PRIMARY KEY,
  requirement VARCHAR(16) NOT NULL,
  title VARCHAR(255) NOT NULL,
  guidance TEXT,
  status ENUM('Compliant','Partial','Gap') DEFAULT 'Gap',
  owner VARCHAR(64),
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE(requirement)
);

-- Evidence
CREATE TABLE evidence (
  id INT AUTO_INCREMENT PRIMARY KEY,
  control_id INT NOT NULL,
  file_name VARCHAR(255) NOT NULL,
  note TEXT,
  uploaded_by VARCHAR(64),
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (control_id) REFERENCES controls(id) ON DELETE CASCADE
);
ALTER TABLE controls ADD COLUMN company_comments TEXT NULL, ADD COLUMN client_comments TEXT NULL, ADD COLUMN comment_log TEXT NULL;

-- Audit log
CREATE TABLE audit_log (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user VARCHAR(64),
  action VARCHAR(255),
  ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  ip VARCHAR(45)
);