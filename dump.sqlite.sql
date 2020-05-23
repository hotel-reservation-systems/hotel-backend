-- TABLE
CREATE TABLE chains (id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER, name VARCHAR);
CREATE TABLE hotels (id INTEGER PRIMARY KEY AUTOINCREMENT, chain_id INTEGER, name VARCHAR, grid_i INTEGER, grid_j INTEGER, created DATETIME, user_agent TEXT, ip_address TEXT);
CREATE TABLE reservations (id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER, room_id INTEGER, date_from DATETIME, date_to DATETIME, created DATETIME, user_agent TEXT, ip_address TEXT);
CREATE TABLE reservations_canceled (id INTEGER PRIMARY KEY AUTOINCREMENT, reservation_id INTEGER, user_id INTEGER, created DATETIME, user_agent TEXT, ip_address TEXT);
CREATE TABLE rooms (id INTEGER PRIMARY KEY AUTOINCREMENT, hotel_id INTEGER, name VARCHAR, description TEXT, rate VARCHAR, price INTEGER, created DATETIME, user_agent TEXT, ip_address TEXT);
CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT, username VARCHAR, password VARCHAR, role VARCHAR, created DATETIME, user_agent TEXT, ip_address TEXT);

-- INDEX

-- TRIGGER

-- VIEW
