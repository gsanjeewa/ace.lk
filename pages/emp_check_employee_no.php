<?php 
/*session_start();*/
// Include the database config file 
include_once 'config.php'; 
$connect = pdoConnection();
$employee_no = $_REQUEST["employee_no"];
$query = "SELECT employee_no FROM join_status WHERE employee_no = '".$employee_no."'  ";

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