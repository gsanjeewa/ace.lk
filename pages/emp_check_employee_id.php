<?php 
/*session_start();*/
// Include the database config file 
include_once 'config.php'; 
$connect = pdoConnection();


/*$employee_no = $_REQUEST["employee_id"];*/

$query = "
            SELECT j.join_id FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid 
            ";

if (isset($_REQUEST["employee_id"])) {
    $query .= "WHERE j.employee_no = '".$_REQUEST["employee_id"]."'";
}

if (isset($_REQUEST["nic_new1"])) {
    $query .= "WHERE e.nic_no = '".$_REQUEST["nic_new1"]."'";
}

if (isset($_REQUEST["nic_old1"])) {
    $query .= "WHERE e.nic_no = '".$_REQUEST["nic_old1"]."'";
}

$query .= "AND (j.employee_status BETWEEN 0 AND 2)";

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