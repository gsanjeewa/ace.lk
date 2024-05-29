<?php

/*include('database_connection.php');*/
include "config.php";
$connect = pdoConnection();

$column = array('');

$output='';
if(isset($_POST['effective_date']) && $_POST['effective_date'] != '' )
{
  $effective_date = date("Y-m", strtotime($_POST['effective_date']));
  $query = "
  SELECT sum(a.amount) AS total_amount, b.department_name, b.department_location, c.supplier_name  
    FROM ration_deduction a
    INNER JOIN department b ON a.department_id = b.department_id
    INNER JOIN ration_supplier_list c ON a.supplier_id = c.id
    WHERE DATE_FORMAT(a.date_effective,'%Y-%m') = '".$effective_date."' GROUP BY a.supplier_id ORDER BY b.department_name ASC
  "; 

$statement = $connect->prepare($query);

$statement->execute();

$number_filter_row = $statement->rowCount();

$result = $statement->fetchAll();

$data = array();
$startpoint =0;
$sno = $startpoint + 1;
foreach($result as $row)
{
   
 $sub_array = array();
 $sub_array[] = $sno;
 $sub_array[] = $row['department_name'].'-'.$row['department_location'];
 $sub_array[] = $row['supplier_name'];
 $sub_array[] = number_format($row['total_amount'],2); 
  
 $sno ++;

 $data[] = $sub_array;
  }

$output = array(
 "data"       =>  $data
);

}

echo json_encode($output);
?>