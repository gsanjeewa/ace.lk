<?php 
session_start();
// Include the database config file 
include_once 'config.php'; 
$connect = pdoConnection();
$current_password = md5($_REQUEST["current_password"]);
$query = "SELECT * FROM system_users WHERE password = '".$current_password."' AND user_id='".$_SESSION['user_id']."'";

$statement = $connect->prepare($query);

$statement->execute();

$result = $statement->fetchAll();

$count=$statement->rowCount();

if ($count > 0){
    echo 'true';
}else{
    
    echo 'false';
}
?>