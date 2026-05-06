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

-- ==========================================
-- 4. BASIC FOREIGN KEY CONSTRAINTS
-- ==========================================

-- User Constraints
ALTER TABLE `tbl_users` 
    ADD CONSTRAINT `fk_users_roles` FOREIGN KEY (`rolesID`) REFERENCES `tbl_roles`(`rolesID`);

-- Passenger Constraints
ALTER TABLE `tbl_passengers` 
    ADD CONSTRAINT `fk_passengers_users` FOREIGN KEY (`usersID`) REFERENCES `tbl_users`(`usersID`);

-- Geography Constraints
ALTER TABLE `tbl_airports` 
    ADD CONSTRAINT `fk_airports_countries` FOREIGN KEY (`airports_countryCode`) REFERENCES `tbl_countries`(`countriesCode`);

-- Fleet & Seating Constraints
ALTER TABLE `tbl_seatClasses` 
    ADD CONSTRAINT `fk_seatClasses_aircrafts` FOREIGN KEY (`aircraftsID`) REFERENCES `tbl_aircrafts`(`aircraftsID`);

-- Flight Schedule Constraints
ALTER TABLE `tbl_flights` 
    ADD CONSTRAINT `fk_flights_aircrafts` FOREIGN KEY (`aircraftsID`) REFERENCES `tbl_aircrafts`(`aircraftsID`),
    ADD CONSTRAINT `fk_flights_origin` FOREIGN KEY (`flights_originCode`) REFERENCES `tbl_airports`(`airportsCode`),
    ADD CONSTRAINT `fk_flights_destination` FOREIGN KEY (`flights_destinationCode`) REFERENCES `tbl_airports`(`airportsCode`);

-- Booking Constraints
ALTER TABLE `tbl_bookings` 
    ADD CONSTRAINT `fk_bookings_users` FOREIGN KEY (`usersID`) REFERENCES `tbl_users`(`usersID`);

-- Payment Constraints
ALTER TABLE `tbl_payments` 
    ADD CONSTRAINT `fk_payments_bookings` FOREIGN KEY (`payments_bookingsID`) REFERENCES `tbl_bookings`(`bookingsID`);

-- Ticket (Junction) Constraints
ALTER TABLE `tbl_tickets` 
    ADD CONSTRAINT `fk_tickets_passengers` FOREIGN KEY (`tickets_passengersID`) REFERENCES `tbl_passengers`(`passengersID`),
    ADD CONSTRAINT `fk_tickets_bookings` FOREIGN KEY (`tickets_bookingsID`) REFERENCES `tbl_bookings`(`bookingsID`),
    ADD CONSTRAINT `fk_tickets_flights` FOREIGN KEY (`tickets_flightID`) REFERENCES `tbl_flights`(`flightsID`),
    ADD CONSTRAINT `fk_tickets_seatClasses` FOREIGN KEY (`tickets_seatClassesID`) REFERENCES `tbl_seatClasses`(`seatClassesID`);