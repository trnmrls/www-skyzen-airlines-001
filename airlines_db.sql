USE airlines_db;

CREATE TABLE `tbl_roles` (
    `rolesID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `roles_name` VARCHAR(50) NOT NULL DEFAULT 'Customer', 
    `roles_createdAt` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `roles_updatedAt` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO tbl_roles (roles_name) VALUES ('Admin'), ('Customer');
ALTER TABLE `tbl_users` AUTO_INCREMENT = 10;
select * from tbl_roles;
select * from tbl_users;
insert into tbl_users values (1, 1, 'trine@abc.com', 'Trine Lise', 'SP', 'Morales', 09999999999, '2006-05-11', 'trnmrls', 'trine', current_timestamp(), current_timestamp());

CREATE TABLE `tbl_countries` (
    `countriesCode` VARCHAR(10) NOT NULL PRIMARY KEY,
    `countriesName` VARCHAR(255) NOT NULL,
    `countries_createdAt` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `countries_updatedAt` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
 
CREATE TABLE `tbl_aircrafts` (
    `aircraftsID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `aircraftsNum` VARCHAR(50) NOT NULL UNIQUE, 
    `aircraftsModel` VARCHAR(255) NOT NULL,
    `aircrafts_maxCap` INT NOT NULL, 
    `aircrafts_status` VARCHAR(50) NOT NULL DEFAULT 'Active', 
    `aircrafts_createdAt` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `aircrafts_updatedAt` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE `tbl_users` (
    `usersID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `rolesID` INT NOT NULL, 
    `users_email` VARCHAR(255) NOT NULL UNIQUE,
    `users_firstName` VARCHAR(255) NOT NULL,
    `users_middleIn` CHAR(5) NOT NULL, 
    `users_lastName` VARCHAR(255) NOT NULL,
    `users_phoneNum` VARCHAR(50) NOT NULL,
    `users_createdAt` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `users_updatedAt` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
ALTER TABLE `tbl_users`
ADD `users_birthday` date NOT NULL,
ADD `users_username` VARCHAR(255) NOT NULL unique,
ADD `users_password` VARCHAR(255) NOT NULL;
DESCRIBE tbl_users;

CREATE TABLE `tbl_airports` (
    `airportsCode` VARCHAR(10) NOT NULL PRIMARY KEY,
    `airports_countryCode` VARCHAR(10) NOT NULL, 
    `airportsName` VARCHAR(255) NOT NULL,
    `airports_createdAt` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `airports_updatedAt` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE `tbl_seatClasses` (
    `seatClassesID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `aircraftsID` INT NOT NULL, 
    `seatClasses_name` VARCHAR(50) NOT NULL DEFAULT 'Economy', -- Replaced ENUM
    `seatClasses_capacity` INT NOT NULL,
    `seatClasses_baggageAllow` INT NOT NULL DEFAULT 0,
    `seatClasses_prices` DECIMAL(8, 2) NOT NULL DEFAULT 1.00,
    `seatClasses_createdAt` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `seatClasses_updatedAt` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE `tbl_passengers` (
    `passengersID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `usersID` INT NOT NULL, 
    `passengers_passportID` VARCHAR(255) NOT NULL,
    `passengers_createdAt` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `passengers_updatedAt` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE `tbl_flights` (
    `flightsID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `aircraftsID` INT NOT NULL, 
    `flightsNum` VARCHAR(255) NOT NULL,
    `flights_originCode` VARCHAR(10) NOT NULL,
    `flights_destinationCode` VARCHAR(10) NOT NULL,
    `flights_departureTime` DATETIME NOT NULL,
    `flights_arrivalTime` DATETIME NOT NULL,
    `flights_basePrice` DECIMAL(10, 2) NOT NULL,
    `flights_availSeats` INT NOT NULL,
    `flights_createdAt` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `flights_updatedAt` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE `tbl_bookings` (
    `bookingsID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `usersID` INT NOT NULL, 
    `bookings_amount` DECIMAL(10, 2) NOT NULL,
    `bookings_status` VARCHAR(50) NOT NULL DEFAULT 'Pending', -- Replaced ENUM
    `bookings_pnrCode` VARCHAR(50) NOT NULL UNIQUE,
    `bookings_createdAt` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `bookings_updatedAt` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE `tbl_tickets` (
    `ticketsID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `tickets_passengersID` INT NOT NULL,
    `tickets_bookingsID` INT NOT NULL,
    `tickets_flightID` INT NOT NULL,
    `tickets_seatClassesID` INT NOT NULL, 
    `tickets_seatNum` VARCHAR(10) NOT NULL,
    `tickets_createdAt` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `tickets_updatedAt` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE `tbl_payments` (
    `paymentsID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `payments_bookingsID` INT NOT NULL,
    `payments_amount` DECIMAL(10, 2) NOT NULL,
    `payments_method` VARCHAR(50) NOT NULL,
    `payments_refCode` VARCHAR(255) NOT NULL UNIQUE,
    `payments_status` VARCHAR(50) NOT NULL DEFAULT 'Pending', -- Replaced ENUM
    `payments_createdAt` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `payments_updatedAt` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Constraints
ALTER TABLE `tbl_users` 
    ADD CONSTRAINT `fk_users_roles` FOREIGN KEY (`rolesID`) REFERENCES `tbl_roles`(`rolesID`);

ALTER TABLE `tbl_passengers` 
    ADD CONSTRAINT `fk_passengers_users` FOREIGN KEY (`usersID`) REFERENCES `tbl_users`(`usersID`);

ALTER TABLE `tbl_airports` 
    ADD CONSTRAINT `fk_airports_countries` FOREIGN KEY (`airports_countryCode`) REFERENCES `tbl_countries`(`countriesCode`);

ALTER TABLE `tbl_seatClasses` 
    ADD CONSTRAINT `fk_seatClasses_aircrafts` FOREIGN KEY (`aircraftsID`) REFERENCES `tbl_aircrafts`(`aircraftsID`);

ALTER TABLE `tbl_flights` 
    ADD CONSTRAINT `fk_flights_aircrafts` FOREIGN KEY (`aircraftsID`) REFERENCES `tbl_aircrafts`(`aircraftsID`),
    ADD CONSTRAINT `fk_flights_origin` FOREIGN KEY (`flights_originCode`) REFERENCES `tbl_airports`(`airportsCode`),
    ADD CONSTRAINT `fk_flights_destination` FOREIGN KEY (`flights_destinationCode`) REFERENCES `tbl_airports`(`airportsCode`);

ALTER TABLE `tbl_bookings` 
    ADD CONSTRAINT `fk_bookings_users` FOREIGN KEY (`usersID`) REFERENCES `tbl_users`(`usersID`);

ALTER TABLE `tbl_payments` 
    ADD CONSTRAINT `fk_payments_bookings` FOREIGN KEY (`payments_bookingsID`) REFERENCES `tbl_bookings`(`bookingsID`);

ALTER TABLE `tbl_tickets` 
    ADD CONSTRAINT `fk_tickets_passengers` FOREIGN KEY (`tickets_passengersID`) REFERENCES `tbl_passengers`(`passengersID`),
    ADD CONSTRAINT `fk_tickets_bookings` FOREIGN KEY (`tickets_bookingsID`) REFERENCES `tbl_bookings`(`bookingsID`),
    ADD CONSTRAINT `fk_tickets_flights` FOREIGN KEY (`tickets_flightID`) REFERENCES `tbl_flights`(`flightsID`),
    ADD CONSTRAINT `fk_tickets_seatClasses` FOREIGN KEY (`tickets_seatClassesID`) REFERENCES `tbl_seatClasses`(`seatClassesID`);
    
    -- INPUTS
INSERT INTO tbl_countries (countriesCode, countriesName) VALUES 
('PH', 'Philippines'),
('US', 'United States'),
('JP', 'Japan'),
('KR', 'South Korea'),
('SG', 'Singapore'),
('TH', 'Thailand'),
('AU', 'Australia'),
('GB', 'United Kingdom'),
('AE', 'United Arab Emirates'),
('TW', 'Taiwan'),
('FR', 'France'),
('CA', 'Canada');

INSERT INTO tbl_airports (airportsCode, airportsName, airports_countryCode) VALUES 
('MNL', 'Ninoy Aquino International Airport', 'PH'),
('KIX', 'Kansai International Airport', 'JP'),
('JFK', 'John F. Kennedy International Airport', 'US'),
('ICN', 'Incheon International Airport', 'KR'),
('SIN', 'Singapore Changi Airport', 'SG'),
('BKK', 'Suvarnabhumi Airport', 'TH'),
('SYD', 'Sydney Kingsford Smith Airport', 'AU'),
('DXB', 'Dubai International Airport', 'AE'),
('TPE', 'Taoyuan International Airport', 'TW'),
('CDG', 'Charles de Gaulle Airport', 'FR'),
('YVR', 'Vancouver International Airport', 'CA');

INSERT INTO tbl_aircrafts (aircraftsNum, aircraftsModel, aircrafts_maxCap, aircrafts_status) VALUES 
('SZ-777-01', 'Boeing 777-300ER', 350, 'Active'),
('SZ-777-02', 'Boeing 777-300ER', 350, 'Active'),
('SZ-737-01', 'Boeing 737 MAX 8', 180, 'Active'),
('SZ-737-02', 'Boeing 737 MAX 8', 180, 'Active'),
('SZ-320-01', 'Airbus A320neo', 165, 'Active'),
('SZ-320-02', 'Airbus A320neo', 165, 'Maintenance'),
('SZ-350-01', 'Airbus A350-900', 300, 'Active'),
('SZ-380-01', 'Airbus A380-800', 500, 'Active');

INSERT INTO tbl_seatClasses (aircraftsID, seatClasses_name, seatClasses_prices, seatClasses_capacity) VALUES 
-- Plane 1 (Boeing 777, Cap: 350)
(1, 'Economy', 15000.00, 250),
(1, 'Business', 45000.00, 80),
(1, 'First Class', 90000.00, 20),

-- Plane 3 (Boeing 737, Cap: 180) - Smaller plane, no First Class!
(3, 'Economy', 8000.00, 150),
(3, 'Business', 25000.00, 30),

-- Plane 5 (Airbus A320, Cap: 165)
(5, 'Economy', 7500.00, 145),
(5, 'Business', 22000.00, 20),

-- Plane 8 (Airbus A380, Cap: 500) - The massive flagship!
(8, 'Economy', 20000.00, 350),
(8, 'Business', 60000.00, 100),
(8, 'First Class', 120000.00, 50);

INSERT INTO tbl_flights (aircraftsID, flights_originCode, flights_destinationCode, flights_departureTime, flights_arrivalTime, flights_status, flights_duration, flights_price) VALUES 
(1, 'MNL', 'NRT', '2026-11-15 08:00:00', '2026-11-15 13:00:00', 'Scheduled', '05:00:00', 15000.00),
(3, 'CEB', 'ICN', '2026-11-16 10:30:00', '2026-11-16 15:45:00', 'Scheduled', '05:15:00', 12500.00),
(5, 'MNL', 'SIN', '2026-11-17 06:00:00', '2026-11-17 09:40:00', 'Scheduled', '03:40:00', 9800.00),
(8, 'JFK', 'DXB', '2026-12-01 22:00:00', '2026-12-02 19:30:00', 'Scheduled', '13:30:00', 45000.00),
(1, 'NRT', 'MNL', '2026-11-20 15:00:00', '2026-11-20 18:30:00', 'Scheduled', '04:30:00', 14500.00),

(3, 'SYD', 'MNL', DATE_ADD(NOW(), INTERVAL -2 HOUR), DATE_ADD(NOW(), INTERVAL 6 HOUR), 'Active', '08:20:00', 25000.00),

(7, 'LAX', 'TPE', '2026-11-10 14:00:00', '2026-11-11 05:00:00', 'Delayed', '15:00:00', 32000.00),
(5, 'MNL', 'BKK', '2026-10-05 09:00:00', '2026-10-05 11:30:00', 'Cancelled', '02:30:00', 8500.00),

(1, 'SIN', 'MNL', '2026-05-10 08:00:00', '2026-05-10 11:40:00', 'Landed', '03:40:00', 9500.00),
(8, 'DXB', 'MNL', '2026-06-15 14:00:00', '2026-06-16 03:00:00', 'Landed', '09:00:00', 38000.00);

INSERT INTO tbl_bookings (usersID, bookings_amount, bookings_status, bookings_pnrCode, bookings_createdAt) VALUES 
(10, 30000.00, 'Confirmed', 'PNR-A1B2C3', '2026-05-01 10:00:00'),
(8, 12500.00, 'Confirmed', 'PNR-X9Y8Z7', '2026-06-05 14:30:00'),
(9, 90000.00, 'Pending', 'PNR-QWERT5', NOW());

INSERT INTO tbl_payments (payments_bookingsID, payments_amount, payments_method, payments_refCode, payments_status, payments_createdAt) VALUES 
(1, 30000.00, 'Credit Card', 'REF-CC-889900', 'Confirmed', '2026-05-01 10:05:00'),
(2, 12500.00, 'E-Wallet', 'REF-EW-554433', 'Confirmed', '2026-06-05 14:35:00'),
(3, 90000.00, 'Bank Transfer', 'REF-BT-112233', 'Pending', NOW());

INSERT INTO tbl_passengers (usersID, passengers_passportID) VALUES 
(10, 'P1234567A'),
(8, 'P9876543B'),
(9, 'US1122334');

INSERT INTO tbl_tickets (tickets_passengersID, tickets_bookingsID, tickets_flightID, tickets_seatClassesID, tickets_seatNum) 
VALUES 
(
    (SELECT passengersID FROM tbl_passengers ORDER BY passengersID ASC LIMIT 1 OFFSET 0),
    (SELECT bookingsID FROM tbl_bookings ORDER BY bookingsID ASC LIMIT 1 OFFSET 0),
    (SELECT flightsID FROM tbl_flights WHERE flightsNum = 'SZ-101' LIMIT 1),
    (SELECT seatClassesID FROM tbl_seatClasses ORDER BY seatClassesID ASC LIMIT 1 OFFSET 0),
    '12A'
),

(
    (SELECT passengersID FROM tbl_passengers ORDER BY passengersID ASC LIMIT 1 OFFSET 1),
    (SELECT bookingsID FROM tbl_bookings ORDER BY bookingsID ASC LIMIT 1 OFFSET 1),
    (SELECT flightsID FROM tbl_flights WHERE flightsNum = 'SZ-102' LIMIT 1),
    (SELECT seatClassesID FROM tbl_seatClasses ORDER BY seatClassesID ASC LIMIT 1 OFFSET 3),
    '5C'
),

(
    (SELECT passengersID FROM tbl_passengers ORDER BY passengersID ASC LIMIT 1 OFFSET 2),
    (SELECT bookingsID FROM tbl_bookings ORDER BY bookingsID ASC LIMIT 1 OFFSET 2),
    (SELECT flightsID FROM tbl_flights WHERE flightsNum = 'SZ-104' LIMIT 1),
    (SELECT seatClassesID FROM tbl_seatClasses ORDER BY seatClassesID DESC LIMIT 1),
    '2A'
);