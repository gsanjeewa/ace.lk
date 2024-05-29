<?php 
/*session_start();*/
// Include the database config file 
include_once 'config.php'; 
$connect = pdoConnection();
$position_id = $_REQUEST["position_id"];
$query = "SELECT position_name FROM position WHERE position_id = '".$position_id."' AND position_status=0 ";

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