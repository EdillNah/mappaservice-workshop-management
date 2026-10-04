create database mappaservice_db;

create table users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(10) NOT NULL,
  role ENUM('owner', 'kasir') NOT NULL
);

insert into users (name, username, password, role)
values('Owner MappaService', 'owner', '12345', 'owner'),
('Kasir MappaService', 'kasir', '12345', 'kasir');
