<?php

try {
    $servername = "localhost";
    $username = "root";
    $password = "Govind@13";
    $dbname = "invennico_new";
    $conn = mysqli_connect($servername, $username, $password);
    if (!$conn) {
        echo "Connection to MySQL server failed<br>";
        die();
    }
    $sql_create_db = "CREATE DATABASE IF NOT EXISTS $dbname";
    if (!mysqli_query($conn, $sql_create_db)) {
        echo "Error creating database: " . mysqli_error($conn);
        die();
    }
    mysqli_select_db($conn, $dbname);
    $sql_create_table = "CREATE TABLE IF NOT EXISTS user (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        firstname VARCHAR(100) NOT NULL,
        lastname VARCHAR(100) NOT NULL,
        email VARCHAR(200) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        address VARCHAR(250) NOT NULL,
        dob date NOT NULL,
        password VARCHAR(200) NOT NULL,
        role VARCHAR(10) NULL,
        status VARCHAR(10) NULL)";

    if (!mysqli_query($conn, $sql_create_table)) {
        echo "Error creating table: " . mysqli_error($conn);
        die();
    }
} catch (Exception $ex) {
    echo "Exception caught: " . $ex->getMessage();
}
?>
