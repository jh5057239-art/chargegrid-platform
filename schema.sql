CREATE TABLE IF NOT EXISTS users (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(120) NOT NULL,
 email VARCHAR(190) NOT NULL UNIQUE,
 password VARCHAR(255) NOT NULL,
 balance DECIMAL(12,2) NOT NULL DEFAULT 0.00,
 is_admin TINYINT(1) NOT NULL DEFAULT 0,
 ref_code VARCHAR(20) NOT NULL UNIQUE,
 referred_by VARCHAR(20) NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 INDEX idx_users_referred_by (referred_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS requests (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id BIGINT UNSIGNED NOT NULL,
 type ENUM('deposit','withdraw') NOT NULL,
 amount DECIMAL(12,2) NOT NULL,
 reference VARCHAR(120) NULL,
 status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_requests_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
 INDEX idx_requests_status (status),
 INDEX idx_requests_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Después de registrarte, convierte tu cuenta en administradora desde phpMyAdmin:
-- UPDATE users SET is_admin = 1 WHERE email = 'TU_CORREO_DE_REGISTRO';

CREATE TABLE IF NOT EXISTS login_attempts (
 email VARCHAR(190) NOT NULL,
 ip_hash CHAR(64) NOT NULL,
 attempts SMALLINT UNSIGNED NOT NULL DEFAULT 0,
 last_attempt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (email, ip_hash),
 INDEX idx_login_attempts_time (last_attempt)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
