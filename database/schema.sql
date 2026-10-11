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

create table pelanggan (
	pelanggan_id int auto_increment primary key,
	nama varchar(100) not null,
	no_telpon varchar(20) not null,
	alamat text
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
	tanggal_masuk datetime default current_timestamp(),

	foreign key (kendaraan_id)
		references kendaraan(kendaraan_id)
);

create table mekanik (
	mekanik_id int auto_increment primary key,
	nama varchar(100) not null,
	no_telpon varchar(20),
	spesialisasi varchar(100),
	status enum('tersedia','sibuk') default 'tersedia'
);

insert into mekanik (nama, no_telpon, spesialisasi, status) values
('Andi', '081234567890','Mesin', 'tersedia'),
('Budi', '081234567891','Kelistrikan', 'tersedia'),
('Rizal', '081234567892','Servis Umum', 'sibuk');

create table sparepart (
	sparepart_id int auto_increment primary key,
	nama varchar(100) not null,
	harga decimal(12,2) not null default 0,
	stok int not null default 0
);

insert into sparepart (nama, harga, stok) values
('Oli Mesin', 60000, 20),
('Busi', 25000, 15),
('Filter Udara', 45000, 10);

alter table servis 
add column mekanik_id int null after kendaraan_id,
add column diagnosa text null after keluhan,
add column status enum(
    'menunggu',
    'diperiksa',
    'menunggu_konfirmasi',
    'disetujui',
    'diproses',
    'selesai',
    'diambil'
) default 'menunggu' after diagnosa;

alter table servis 
add constraint fk_servis_mekanik 
foreign key (mekanik_id) references mekanik(mekanik_id) 
on delete set null
on update cascade;

create table detail_servis (
    detail_servis_id int auto_increment primary key,
    servis_id int not null,
    sparepart_id int not null,
    jumlah int not null,
    harga decimal(12,2) not null,
    subtotal decimal(12,2) not null,

    foreign key (servis_id)
        references servis(servis_id),

    foreign key (sparepart_id)
        references sparepart(sparepart_id)
);

create table transaksi (
    transaksi_id int auto_increment primary key,
    servis_id int not null,
    total decimal(12,2) not null,
    metode_pembayaran enum('cash', 'qris') not null,
    jumlah_bayar decimal(12,2) not null,
    kembalian decimal(12,2) not null default 0,
    status_pembayaran enum('belum_bayar', 'lunas') default 'belum_bayar',
    tanggal_transaksi datetime default current_timestamp(),

   	foreign key (servis_id)
        references servis(servis_id)
);

alter table servis
add column biaya_jasa decimal(12,2) not null default 5000;

create table transaksi (
    transaksi_id int auto_increment primary key,
    servis_id int not null,
    total decimal(12,2) not null,
    metode_pembayaran enum(
        'cash',
        'qris'
    ) not null,
    jumlah_bayar decimal(12,2) not null,
    kembalian decimal(12,2) not null default 0,
    status_pembayaran enum(
        'belum_bayar',
        'lunas'
    ) default 'belum_bayar',
    tanggal_transaksi datetime default current_timestamp(),

    foreign key (servis_id)
        references servis(servis_id)
);

create table restock (
    restock_id int auto_increment primary key,
    tanggal datetime default current_timestamp(),
    total_biaya decimal(12,2) not null default 0,
    keterangan varchar(255) null,
    user_id int null,
    foreign key (user_id) references users(user_id) on delete set null
);

create table detail_restock (
    detail_restock_id int auto_increment primary key,
    restock_id int not null,
    sparepart_id int not null,
    jumlah int not null,
    harga_beli decimal(12,2) not null default 0,
    subtotal decimal(12,2) not null default 0,
    foreign key (restock_id) references restock(restock_id) on delete cascade,
    foreign key (sparepart_id) references sparepart(sparepart_id) on delete cascade
);
