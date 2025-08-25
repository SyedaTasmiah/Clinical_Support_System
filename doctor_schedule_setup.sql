-- Doctor Schedule Management Tables

-- Create doctor_schedule table for weekly schedules
CREATE TABLE IF NOT EXISTS doctor_schedule (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT(11) NOT NULL,
    day_of_week ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    slot_duration INT(11) DEFAULT 30, -- in minutes
    is_active TINYINT(1) DEFAULT 1,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    UNIQUE KEY unique_doctor_day (doctor_id, day_of_week)
);

-- Create appointment_slots table for detailed slot management (optional for future expansion)
CREATE TABLE IF NOT EXISTS appointment_slots (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT(11) NOT NULL,
    appointment_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_booked TINYINT(1) DEFAULT 0,
    appointment_id INT(11) NULL,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    FOREIGN KEY (appointment_id) REFERENCES appointment(id) ON DELETE SET NULL,
    UNIQUE KEY unique_doctor_datetime (doctor_id, appointment_date, start_time)
);

-- Insert some sample schedules (optional)
-- You can remove this section if you don't want sample data
INSERT INTO doctor_schedule (doctor_id, day_of_week, start_time, end_time, slot_duration) VALUES
(1, 'Monday', '09:00:00', '17:00:00', 30),
(1, 'Tuesday', '09:00:00', '17:00:00', 30),
(1, 'Wednesday', '09:00:00', '17:00:00', 30),
(1, 'Thursday', '09:00:00', '17:00:00', 30),
(1, 'Friday', '09:00:00', '17:00:00', 30);
