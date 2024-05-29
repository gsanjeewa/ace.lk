<?php

/*include('database_connection.php');*/
include "config.php";
$connect = pdoConnection();

$column = array('department_name');

$query = '
SELECT * FROM department a 
INNER JOIN attendance b ON a.department_id=b.department_id WHERE a.department_status = 0
';

if(isset($_POST['effective_date']) && $_POST['effective_date'] != '')
{
	$effective_date = date("Y-m-d", strtotime($_POST['effective_date']));

 $query .= '
 AND b.start_date = "'.$effective_date.'" 
 ';
}
 
$query .= '
 ORDER BY a.department_name ASC
';

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
foreach($result as $row)
{
	
 $sub_array = array();
 $sub_array[] = $sno;
 $sub_array[] = $row['department_name'].'-'.$row['department_location'];
 
  
 $sno ++;

 $data[] = $sub_array;
}

function count_all_data($connect)
{
 $query = "SELECT * FROM department WHERE department_status=0";
 $statement = $connect->prepare($query);
 $statement->execute();
 return $statement->rowCount();
}

$output = array(
 "draw"       =>  intval($_POST["draw"]),
 "recordsTotal"   =>  count_all_data($connect),
 "recordsFiltered"  =>  $number_filter_row,
 "data"       =>  $data
);

echo json_encode($output);

?>