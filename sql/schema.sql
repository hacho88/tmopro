-- TMOPRO core schema (B2B foundation)

CREATE TABLE IF NOT EXISTS b2b_accounts (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  company_name VARCHAR(255) NOT NULL,
  inn VARCHAR(32) NULL,
  phone VARCHAR(64) NULL,
  email VARCHAR(255) NULL,
  price_tier VARCHAR(32) NOT NULL DEFAULT 'default',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS b2b_users (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  account_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role VARCHAR(32) NOT NULL DEFAULT 'buyer',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_email (email),
  KEY idx_account_id (account_id),
  CONSTRAINT fk_b2b_users_account FOREIGN KEY (account_id) REFERENCES b2b_accounts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS orders (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_number VARCHAR(32) NOT NULL,
  source VARCHAR(16) NOT NULL DEFAULT 'site',
  status VARCHAR(32) NOT NULL DEFAULT 'new',
  account_id BIGINT UNSIGNED NULL,
  company_name VARCHAR(255) NULL,
  inn VARCHAR(32) NULL,
  contact_person VARCHAR(255) NULL,
  phone VARCHAR(64) NULL,
  email VARCHAR(255) NULL,
  total_base DECIMAL(12,2) NOT NULL DEFAULT 0,
  total DECIMAL(12,2) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_order_number (order_number),
  KEY idx_status (status),
  KEY idx_created_at (created_at),
  KEY idx_account_id (account_id),
  CONSTRAINT fk_orders_account FOREIGN KEY (account_id) REFERENCES b2b_accounts(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS order_items (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_id BIGINT UNSIGNED NOT NULL,
  product_id BIGINT UNSIGNED NULL,
  article VARCHAR(64) NULL,
  name VARCHAR(255) NOT NULL,
  brand VARCHAR(255) NULL,
  category VARCHAR(255) NULL,
  qty INT NOT NULL DEFAULT 1,
  price_base DECIMAL(12,2) NOT NULL DEFAULT 0,
  price_wholesale DECIMAL(12,2) NOT NULL DEFAULT 0,
  unit_price DECIMAL(12,2) NOT NULL DEFAULT 0,
  line_total DECIMAL(12,2) NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY idx_order_id (order_id),
  CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
