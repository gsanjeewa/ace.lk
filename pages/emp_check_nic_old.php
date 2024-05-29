<?php 
/*session_start();*/
// Include the database config file 
include_once 'config.php'; 
$connect = pdoConnection();
$nic_old = $_REQUEST["nic_old"];
$query = "SELECT nic_no FROM employee WHERE nic_no = '".$nic_old."'";

$statement = $connect->prepare($query);

$statement->execute();

$result = $statement->fetchAll();

$count=$statement->rowCount();

if ($count > 0){
    echo 'false';
}else{
    
    echo 'true';
}
?>