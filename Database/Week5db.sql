CREATE DATABASE IF NOT EXISTS `testdb`;
USE `testdb`;

CREATE TABLE IF NOT EXISTS `teachers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `teachers` (`username`, `password`) VALUES ('admin', 'password123');

CREATE TABLE IF NOT EXISTS `students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admission_number` varchar(50) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `class_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `adm_unique` (`admission_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `students` (`admission_number`, `full_name`, `class_name`) VALUES
('ADM-001', 'Hikaru Nakamura', 'Form 3'),
('ADM-007', 'Garry Kasparov', 'Advanced Tactics'),
('ADM-012', 'Magnus Carlsen', 'Form 1');

COMMIT;