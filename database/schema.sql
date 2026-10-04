create database mappaservice_db;

create table users (
  user_id int auto_increment primary key,
  name varchar(100) not null,
  username varchar(50) not null unique,
  password varchar(10) not null,
  role enum('owner', 'kasir') not null
);

insert into users (name, username, password, role)
values('Owner MappaService', 'owner', '12345', 'owner'),
('Kasir MappaService', 'kasir', '12345', 'kasir');

CREATE TABLE pelanggan (
  pelanggan_id int auto_increment primary key,
  nama varchar(100) not null,
  no_telepon varchar(20),
  alamat TEXT
);

create table kendaraan (
  kendaraan_id int auto_increment primary key,
  pelanggan_id int not null,
  nomor_polisi varchar(20) not null,
  merk varchar(50),
  model varchar(50),
  tahun year,

  foreign key (pelanggan_id)
  references pelanggan(pelanggan_id)
);

create table servis (
    servis_id int auto_increment primary key,
    kendaraan_id int not null,
    keluhan text not null,
    tanggal_masuk datatime default current_timestamp,

    foreign key (kendaraan_id)
    references kendaraan(kendaraan_id)
);
