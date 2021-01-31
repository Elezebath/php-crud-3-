<?php 
$server = "localhost";
$username = "root";
$password = "";
$dbname = "misc";
$message='';
$conn = new PDO("mysql:host=$server; dbname=$dbname", $username, $password);  
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
