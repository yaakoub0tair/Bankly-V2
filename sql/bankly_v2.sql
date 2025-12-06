CREATE DATABASE IF NOT EXISTS bankly_v2;
USE  bankly_v2;
CREATE TABLE IF NOT EXISTS clients(
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE ,
    cin VARCHAR(50) UNIQUE NOT NULL,
    telephone VARCHAR(50) UNIQUE ,
    adress text,
    created_at datetime DEFAULT CURRENT_TIMESTAMP
);


