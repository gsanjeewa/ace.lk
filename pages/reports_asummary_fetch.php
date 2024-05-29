<?php

//fetch.php

include "config.php";
$connect = pdoConnection();

$column = array('');
$output='';
$data = array();


$query ="SELECT COALESCE(sum(a.amount),'0') AS total, b.allowances_en, COUNT(a.employee_id) AS total_count FROM employee_allowances a
INNER JOIN allowances b ON a.allowances_id = b.allowances_id ";
if(isset($_POST['effective_date']) && $_POST['effective_date'] != '')
{
  $effective_date = date("Y-m", strtotime($_POST['effective_date']));
  $query .="WHERE DATE_FORMAT(a.effective_date,'%Y-%m') = '".$effective_date."' ";
}

$query .="GROUP BY a.allowances_id";

$statement = $connect->prepare($query);
$statement->execute();
$result = $statement->fetchAll();

$startpoint =0;
$sno = $startpoint + 1;
$total_paid = 0; 

foreach($result as $row)
{ 

  $sub_array=array();
  $sub_array[] = $sno;
  $sub_array[] = $row['allowances_en'];
  $sub_array[] = $row['total_count'];
  $sub_array[] = number_format($row['total'], 2);

  $sno ++;

  $total_paid = $total_paid + floatval($row['total']);
  $data[] = $sub_array;

} 

$output = array( 
 'data'    => $data,
 'total_paid'    => number_format($total_paid, 2), 
);

echo json_encode($output);


?>