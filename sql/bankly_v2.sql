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
CREATE TABLE IF NOT EXISTS users(
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
   password VARCHAR(255) NOT NULL,
   role ENUM('admin', 'agent') NOT NULL,
   created_at datetime DEFAULT CURRENT_TIMESTAMP
); 
CREATE TABLE IF NOT EXISTS accounts(
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    account_number VARCHAR(50) UNIQUE NOT NULL,
    type ENUM('courant', 'epargne') NOT NULL,
    status ENUM('actif', 'suspendu','ferme') NOT NULL,
    balance DECIMAL(15,2) NOT NULL DEFAULT 0.0,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS transactions(
    id INT AUTO_INCREMENT PRIMARY KEY,
    account_id INT NOT NULL,
    user_id INT DEFAULT NULL,
    type ENUM('depot', 'retrait') NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    balance_before DECIMAL(15,2) NOT NULL,
    balance_after DECIMAL(15,2) NOT NULL,
    description text,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (account_id) REFERENCES accounts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL

    );



