-- Create the database
CREATE DATABASE jacky_salon;

-- Use the created database
USE jacky_salon;

-- Create the `submissions` table to store both appointment and contact form data
CREATE TABLE submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,   -- Unique ID for each submission
    form_type ENUM('appointment', 'contact') NOT NULL, -- To distinguish between appointment and contact
    name VARCHAR(255) NOT NULL,          -- Full name
    phone VARCHAR(20) NOT NULL,          -- Phone number
    email VARCHAR(255) NOT NULL,         -- Email address
    message TEXT,                        -- Optional message (for contact form)
    service ENUM('braiding', 'hairdressing', 'pedicure', 'manicure') DEFAULT NULL, -- Selected service (for appointment form)
    preferred_date DATE DEFAULT NULL,    -- Preferred date (for appointment form)
    preferred_time TIME DEFAULT NULL,    -- Preferred time (for appointment form)
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Timestamp of submission
);
