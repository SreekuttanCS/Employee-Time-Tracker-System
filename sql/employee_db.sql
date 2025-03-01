CREATE DATABASE employee_db;
USE employee_db;

-- --------------------------------------------------------
-- Table structure for table `admin`
-- --------------------------------------------------------

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(25) NOT NULL,
  `password` varchar(30) NOT NULL,
  `full_name` varchar(30) NOT NULL,
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `atlog`
-- --------------------------------------------------------

CREATE TABLE `atlog` (
  `atlog_id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` int(11) NOT NULL,
  `atlog_DATE` date DEFAULT NULL,
  `am_in` time DEFAULT NULL,
  `am_out` time DEFAULT NULL,
  `pm_in` time DEFAULT NULL,
  `pm_out` time DEFAULT NULL,
  `am_late` varchar(3) DEFAULT NULL,
  `am_underTIME` varchar(3) DEFAULT NULL,
  `pm_late` varchar(3) DEFAULT NULL,
  `pm_underTIME` varchar(3) DEFAULT NULL,
  `overtime` time DEFAULT NULL, 
  `work_hour` time NOT NULL,
  `status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`atlog_id`),
  FOREIGN KEY (`emp_id`) REFERENCES `employee`(`emp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `employee`
-- --------------------------------------------------------

CREATE TABLE `employee` (
  `emp_id` int(11) NOT NULL AUTO_INCREMENT,
  `password` varchar(30) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `zip` varchar(4) DEFAULT NULL,
  `contact_number` varchar(11) DEFAULT NULL,
  `email_address` varchar(50) DEFAULT NULL,
  `contract` varchar(50) NOT NULL,
  `shift` varchar(25) NOT NULL,
  PRIMARY KEY (`emp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Data for table `admin`
-- --------------------------------------------------------

INSERT INTO `admin` (`admin_id`, `username`, `password`, `full_name`) VALUES
(1, 'admin1', 'admin123', 'Michael Lero'),
(2, 'admin2', 'admin123', 'John Doe'),
(3, 'admin3', 'admin123', 'Aram Muncal'),
(4, 'admin4', 'adminpass1', 'James Bond'),
(5, 'admin5', 'adminpass2', 'Ken Kane'),
(6, 'admin6', 'adminpass3', 'Miranda Kerr'),
(7, 'admin7', 'adminpass4', 'Kim Mira');

-- --------------------------------------------------------
-- Data for table `atlog`
-- --------------------------------------------------------

INSERT INTO `atlog` (`atlog_id`, `emp_id`, `atlog_DATE`, `am_in`, `am_out`, `pm_in`, `pm_out`, `am_late`, `am_underTIME`, `pm_late`, `pm_underTIME`, `overtime`, `work_hour`, `status`) VALUES
(1, 14, '2023-07-11', '09:15:00', '12:45:00', '13:45:00', '17:45:00', 'YES', 'NO', 'NO', 'NO', '00:00:00', '07:30:00', 'Offline'),
(2, 15, '2023-07-14', '09:15:00', '12:45:00', '16:30:00', '20:45:00', 'NO', 'NO', 'YES', 'NO', '00:00:00', '04:15:00', 'Offline'),
-- Continue with other records in `atlog` table...

-- --------------------------------------------------------
-- Data for table `employee`
-- --------------------------------------------------------

INSERT INTO `employee` (`emp_id`, `password`, `first_name`, `middle_name`, `last_name`, `address`, `zip`, `contact_number`, `email_address`, `contract`, `shift`) VALUES
(1, 'password1', 'Daniela', 'M.', 'Cantillo', 'Labnig, Malinao, Albay', '2311', '09669517555', 'danielamarzan.cantillo@bicol-u.edu.ph', 'Part Time', 'Morning Shift'),
(2, 'password2', 'Misty Shaine', 'Sambajon', 'Niones', 'Daraga, Albay', '1234', '09562849189', 'mistyshainesambajon.niones@bicol-u.edu.ph', 'Part Time', 'Morning Shift'),
-- Continue with other records in `employee` table...
