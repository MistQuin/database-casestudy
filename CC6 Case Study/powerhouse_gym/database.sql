CREATE DATABASE IF NOT EXISTS powerhouse_gym;
USE powerhouse_gym;

CREATE TABLE Member (
    MemberID INT NOT NULL AUTO_INCREMENT,
    FirstName VARCHAR(50) NOT NULL,
    LastName VARCHAR(50) NOT NULL,
    DateOfBirth DATE NOT NULL,
    ContactNo VARCHAR(13) NOT NULL,
    Email VARCHAR(100) NULL,
    Address VARCHAR(150) NOT NULL,
    EmergencyContactName VARCHAR(100) NOT NULL,
    EmergencyContactNo VARCHAR(13) NOT NULL,
    JoinDate DATE NOT NULL DEFAULT (CURRENT_DATE),
    HealthConsent TINYINT(1) NOT NULL DEFAULT 0,
    ConsentDate DATE NULL,
    PRIMARY KEY (MemberID),
    UNIQUE KEY uq_member_identity (FirstName, LastName, DateOfBirth),
    CONSTRAINT chk_consent CHECK (HealthConsent = 0 OR ConsentDate IS NOT NULL)
);

INSERT INTO Member (
    FirstName,
    LastName,
    DateOfBirth,
    ContactNo,
    Email,
    Address,
    EmergencyContactName,
    EmergencyContactNo,
    JoinDate,
    HealthConsent,
    ConsentDate
) VALUES
('Maria', 'Santos', '1995-03-14', '09171234501', 'maria.santos@gmail.com', 'Brgy. San Roque, Baliuag, Bulacan', 'Ana Santos', '09171234502', '2026-01-05', 1, '2026-01-05'),
('Juan', 'Dela Cruz', '1990-07-22', '09181234503', 'juan.delacruz@gmail.com', 'Brgy. Poblacion, Baliuag, Bulacan', 'Rosa Dela Cruz', '09181234504', '2026-01-12', 0, NULL),
('Angelica', 'Reyes', '2001-11-02', '09291234505', 'angel.reyes@gmail.com', 'Brgy. Tarcan, Baliuag, Bulacan', 'Miguel Reyes', '09291234506', '2026-02-03', 1, '2026-02-03'),
('Carlo', 'Mendoza', '1988-05-30', '09391234507', NULL, 'Brgy. Sabang, Baliuag, Bulacan', 'Liza Mendoza', '09391234508', '2026-02-18', 0, NULL),
('Patricia', 'Villanueva', '1999-09-09', '09451234509', 'patricia.v@gmail.com', 'Brgy. Pagala, Baliuag, Bulacan', 'Jose Villanueva', '09451234510', '2026-03-01', 1, '2026-03-02'),
('Rafael', 'Garcia', '1985-12-17', '09561234511', 'rafael.garcia@gmail.com', 'Brgy. Bagong Nayon, Baliuag, Bulacan', 'Elena Garcia', '09561234512', '2026-03-15', 1, '2026-03-15'),
('Kristine', 'Bautista', '2003-04-25', '09671234513', 'kristine.b@gmail.com', 'Brgy. Concepcion, Baliuag, Bulacan', 'Ramon Bautista', '09671234514', '2026-04-04', 0, NULL),
('Miguel', 'Aquino', '1993-01-08', '09771234515', 'miguel.aquino@gmail.com', 'Brgy. Santa Barbara, Baliuag, Bulacan', 'Carmen Aquino', '09771234516', '2026-04-20', 1, '2026-04-20'),
('Jasmine', 'Ramos', '1997-06-19', '09881234517', NULL, 'Brgy. Hinukay, Baliuag, Bulacan', 'Dennis Ramos', '09881234518', '2026-05-07', 0, NULL),
('Paolo', 'Navarro', '1992-10-03', '09991234519', 'paolo.navarro@gmail.com', 'Brgy. Makinabang, Baliuag, Bulacan', 'Luz Navarro', '09991234520', '2026-05-25', 1, '2026-05-26'),
('Bianca', 'Castillo', '2000-08-28', '09051234521', 'bianca.castillo@gmail.com', 'Brgy. Tilapayong, Baliuag, Bulacan', 'Arnel Castillo',  '09051234522', '2026-06-10', 0, NULL),
('Eduardo', 'Lim', '1980-02-11', '09161234523', 'eduardo.lim@gmail.com', 'Brgy. Matangtubig, Baliuag, Bulacan', 'Grace Lim', '09161234524', '2026-07-01', 1, '2026-07-01');
