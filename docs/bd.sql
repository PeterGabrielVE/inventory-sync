-- Usar InnoDB para integridad y row-level locking
CREATE TABLE products (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  sku VARCHAR(64) NOT NULL,            -- referencia única (ej: "REF-001")
  name VARCHAR(255) NOT NULL,
  stock INT NOT NULL DEFAULT 0,        -- stock actual
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uk_products_sku (sku),
  INDEX idx_products_name (name(100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE inventory_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id BIGINT UNSIGNED NOT NULL,
  old_stock INT NOT NULL,
  new_stock INT NOT NULL,
  delta INT NOT NULL,                  -- new_stock - old_stock
  source VARCHAR(100) NOT NULL,        -- usuario o fuente (API, worker, ajuste, venta, compra)
  note VARCHAR(512) NULL,              -- comentario opcional
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_logs_product FOREIGN KEY (product_id) REFERENCES products(id)
    ON DELETE CASCADE
  INDEX idx_logs_product_date (product_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
