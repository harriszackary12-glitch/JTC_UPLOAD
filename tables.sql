-- Database schema 
-- if no database exists, create it
CREATE DATABASE IF NOT EXISTS rolsa;
USE rolsa;

create table rolsa_users (
    pk_user_id int primary key auto_increment not null,
    username varchar(30) unique not null,
    email varchar(100) unique not null,
    password_hash varchar(255) not null,
    postcode varchar(32) not null,
    created_at datetime not null default current_timestamp(),
    updated_at datetime not null default current_timestamp() on update current_timestamp()
);

create table rolsa_products (
    pk_product_id int primary key auto_increment not null,
    product_name varchar(128) not null,
    category enum('Solar', 'EV', 'Smart Home') not null,
    product_description text not null,
    average_carbon_saving float not null,
    in_stock boolean default true
);

insert into rolsa_products (product_name, category, product_description, average_carbon_saving, in_stock
) values ("Solar Panels", 1, "A Solar Panel is a device made of photovoltaic cells that converts sunlight into electrical current.", 10.0, true
), ("Electric Vehicle (EV) charging station", 2, "A charging station for an Electric Vehicle", 25.0, true
), ("Smart Home Management System", 3, "A centralised platform that connects, controls, and tracks energy usage from lighting, heating, and appliances in the home", 15.0, true
);

create table rolsa_energy_logs (
    pk_log_id bigint primary key auto_increment,
    fk_user_id int not null,
    log_timestamp datetime not null default current_timestamp(),
    generation_kw float not null,
    consumption_kw float not null,
    grid_import_export float not null
);

create table rolsa_bookings (
    pk_booking_id int primary key auto_increment not null,
    fk_user_id int not null,
    fk_product_id int not null,
    booking_type enum('Consultation', 'Installation') not null,
    scheduled_date datetime not null,
    booking_status enum('Pending', 'Confirmed', 'Completed', 'Cancelled') default 'Pending',
    created_at datetime not null default current_timestamp()
);

create table rolsa_carbon_profiles (
    pk_profile_id int primary key not null auto_increment,
    fk_user_id int not null,
    base_footprint float not null,
    current_footprint float not null,
    updated_at datetime not null default current_timestamp() on update current_timestamp()
);