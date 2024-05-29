<?php 
/*session_start();*/
// Include the database config file 
include_once 'config.php'; 
$connect = pdoConnection();
$department_id = $_REQUEST["department_id"];
$query = "SELECT department_id FROM department WHERE department_status = 0 AND department_id = '".$department_id."'";

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