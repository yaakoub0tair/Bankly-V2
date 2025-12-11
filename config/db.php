<?php
$DB_HOST = 'localhost';     
$DB_NAME = 'bankly_v2';     
$DB_USER = 'root';           
$DB_PASS = '';              


try {
    $dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4";

    $pdo = new PDO($dsn, $DB_USER, $DB_PASS);

   
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);   
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); 

} catch (PDOException $e) {
    
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}
