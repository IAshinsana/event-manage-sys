-- Event Waitlist System Database Schema  
-- Safe import with existence checks and error handling
-- Run this SQL to create the waitlist table

-- Check if the table already exists
SET @table_exists = (
    SELECT COUNT(*) 
    FROM information_schema.tables 
    WHERE table_schema = DATABASE() 
    AND table_name = 'event_waitlist'
);

-- Drop table if it exists (for clean reinstall)
SET @sql = IF(@table_exists > 0, 'DROP TABLE event_waitlist', 'SELECT "Table does not exist, creating new one" as message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Create event_waitlist table with safe schema
CREATE TABLE IF NOT EXISTS event_waitlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    email VARCHAR(255) NULL,
    name VARCHAR(255) NULL,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('waiting', 'invited', 'purchased', 'expired') DEFAULT 'waiting',
    invited_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    expired_at TIMESTAMP NULL,
    reminder_sent_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes for performance
    INDEX idx_event_status (event_id, status),
    INDEX idx_user_id (user_id),
    INDEX idx_expires_at (expires_at),
    INDEX idx_status_expires (status, expires_at),
    
    -- Ensure one entry per user per event
    UNIQUE KEY unique_user_event (user_id, event_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add foreign key constraints safely
-- Check if events table exists before adding constraint
SET @events_exists = (
    SELECT COUNT(*) 
    FROM information_schema.tables 
    WHERE table_schema = DATABASE() 
    AND table_name = 'events'
);

SET @users_exists = (
    SELECT COUNT(*) 
    FROM information_schema.tables 
    WHERE table_schema = DATABASE() 
    AND table_name = 'users'
);

-- Add foreign key for events if events table exists
SET @sql = IF(@events_exists > 0, 
    'ALTER TABLE event_waitlist ADD CONSTRAINT fk_waitlist_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE',
    'SELECT "Events table not found, skipping foreign key constraint" as warning'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add foreign key for users if users table exists  
SET @sql = IF(@users_exists > 0,
    'ALTER TABLE event_waitlist ADD CONSTRAINT fk_waitlist_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE',
    'SELECT "Users table not found, skipping foreign key constraint" as warning'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add waitlist_enabled field to events table safely
SET @column_exists = (
    SELECT COUNT(*) 
    FROM information_schema.columns 
    WHERE table_schema = DATABASE() 
    AND table_name = 'events' 
    AND column_name = 'waitlist_enabled'
);

SET @sql = IF(@column_exists = 0 AND @events_exists > 0,
    'ALTER TABLE events ADD COLUMN waitlist_enabled TINYINT(1) DEFAULT 1 AFTER description',
    'SELECT "Waitlist column already exists or events table not found" as info'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Show success message
SELECT 'Event waitlist table created successfully!' as result;

-- Show table structure for verification
DESCRIBE event_waitlist;
