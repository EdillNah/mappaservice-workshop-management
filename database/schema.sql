create database mappaservice_db;

create table pengguna (
	id_pengguna int auto_increment primary key,
	nama varchar(100) not null,
	nama_pengguna varchar(50) not null unique,
	password varchar(20) not null,
	peran enum('owner','kasir') not null
);

insert into pengguna (nama, nama_pengguna, password, peran)
values('Owner MappaService', 'owner', '12345', 'owner'),
('Kasir MappaService', 'kasir', '12345', 'kasir');
