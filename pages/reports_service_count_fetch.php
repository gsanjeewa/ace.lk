<?php

/*include('database_connection.php');*/
include "config.php";
$connect = pdoConnection();

$column = array('');

$output = '';

if(isset($_POST['effective_date']) && $_POST['effective_date'] != '')
{
	
$query = "
WITH sevicecount AS (SELECT count(employee_no) AS year_count, Floor(datediff('".$_POST['effective_date']."', join_date)/365) as svc FROM join_status WHERE employee_status=0 GROUP BY svc) SELECT year_count, svc FROM sevicecount WHERE svc > 0; 
";

$query1 = '';

if($_POST["length"] != -1)
{
 $query1 = 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}

$statement = $connect->prepare($query);

$statement->execute();

$number_filter_row = $statement->rowCount();

$statement = $connect->prepare($query . $query1);

$statement->execute();

$result = $statement->fetchAll();

$data = array();
$startpoint =0;
$sno = $startpoint + 1;
$join_id=array();

foreach($result as $row)
{
	$effective_date = date("Ymd", strtotime($_POST['effective_date']));
 	$sub_array = array();
 	$sub_array[] = $sno;
 	$sub_array[] = $row['svc'];
 	$sub_array[] = '<a href="/reports/misc/svc_count/'.$row['svc'].'/'.$effective_date.'">'.$row['year_count'].'</a>';
   
 	$sno ++; 

 	$data[] = $sub_array;
}

$output = array(
 	"data"       =>  $data 
);
}
echo json_encode($output);

?>