<?php
include "config.php";
$connect = pdoConnection();

$query = '';
$output = array();
$query .= "SELECT * FROM employee ";
if(isset($_POST["search"]["value"]))
{
 $query .= 'WHERE surname LIKE "%'.$_POST["search"]["value"].'%" ';
 $query .= 'OR nic_no LIKE "%'.$_POST["search"]["value"].'%" ';
}
if(isset($_POST["order"]))
{
 $query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
}
else
{
 $query .= 'ORDER BY employee_id DESC ';
}
if($_POST["length"] != -1)
{
 $query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}
$statement = $connect->prepare($query);
$statement->execute();
$result = $statement->fetchAll();
$data = array();
$filtered_rows = $statement->rowCount();
foreach($result as $row)
{
 
 $sub_array = array();
 $sub_array[] = $row["surname"];
 $sub_array[] = $row["nic_no"];
 $sub_array[] = $row["nic_no"];
 $sub_array[] = $row["nic_no"];
 $sub_array[] = $row["nic_no"];
 $sub_array[] = $row["nic_no"];
 $sub_array[] = $row["nic_no"];
 $sub_array[] = $row["nic_no"];
 $sub_array[] = $row["nic_no"];
 $sub_array[] = '<button type="button" name="update" id="'.$row["employee_id"].'" class="btn btn-warning btn-xs update">Update</button>';
 $sub_array[] = '<button type="button" name="delete" id="'.$row["employee_id"].'" class="btn btn-danger btn-xs delete">Delete</button>';
 $data[] = $sub_array;
}
$output = array(
 "draw"    => intval($_POST["draw"]),
 "recordsTotal"  =>  $filtered_rows, 
 "data"    => $data
);
echo json_encode($output);
?>