-- 1. Modify users role column
ALTER TABLE users MODIFY COLUMN role ENUM('customer', 'pharmacist', 'driver', 'admin') DEFAULT 'customer';

-- 2. Modify orders status column safely
ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'delivered', 'pending_review', 'packed', 'out_for_delivery', 'picked_up') DEFAULT 'pending';
UPDATE orders SET status = 'pending_review' WHERE status = 'pending';
ALTER TABLE orders MODIFY COLUMN status ENUM('pending_review', 'approved', 'packed', 'out_for_delivery', 'delivered', 'picked_up', 'rejected') DEFAULT 'pending_review';

-- 3. Update orders delivery details and constraints
ALTER TABLE orders MODIFY COLUMN shipping_address TEXT NULL;
ALTER TABLE orders ADD COLUMN delivery_method ENUM('delivery', 'pickup') DEFAULT 'delivery';
ALTER TABLE orders ADD COLUMN driver_id INT NULL;
ALTER TABLE orders ADD CONSTRAINT fk_driver FOREIGN KEY (driver_id) REFERENCES users(id) ON DELETE SET NULL;

-- 4. Create product_batches table
CREATE TABLE IF NOT EXISTS product_batches (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    batch_number VARCHAR(50) NOT NULL,
    expiry_date DATE NOT NULL,
    quantity INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- 5. Create drug_conflicts table
CREATE TABLE IF NOT EXISTS drug_conflicts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    generic_name_1 VARCHAR(150) NOT NULL,
    generic_name_2 VARCHAR(150) NOT NULL,
    severity ENUM('minor', 'moderate', 'severe') DEFAULT 'severe',
    warning_message TEXT NOT NULL
);

-- Seed drug conflicts
INSERT INTO drug_conflicts (generic_name_1, generic_name_2, severity, warning_message) VALUES
('Aspirin', 'Warfarin', 'severe', 'Both drugs are blood thinners. Using them together increases the risk of severe internal bleeding.'),
('Ibuprofen', 'Aspirin', 'moderate', 'Ibuprofen may decrease the cardioprotective effect of Aspirin and increase the risk of gastrointestinal irritation.'),
('Sildenafil', 'Nitroglycerin', 'severe', 'Using Sildenafil with Nitroglycerin can cause a severe, life-threatening drop in blood pressure.'),
('Spironolactone', 'Potassium Chloride', 'severe', 'Using these together can cause dangerously high blood potassium levels (hyperkalemia).');

-- 6. Create refill_reminders table
CREATE TABLE IF NOT EXISTS refill_reminders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    order_id INT NOT NULL,
    last_reminder_sent TIMESTAMP NULL,
    next_reminder_date DATE NOT NULL,
    status ENUM('active', 'paused', 'completed') DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- 7. Add chronic condition marker to products
ALTER TABLE products ADD COLUMN is_chronic TINYINT(1) DEFAULT 0;
