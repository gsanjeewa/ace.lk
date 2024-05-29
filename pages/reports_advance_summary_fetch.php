<?php

//fetch.php

include "config.php";
$connect = pdoConnection();

$column = array('');
$output='';
if(isset($_POST['effective_date']) && $_POST['effective_date'] != '')
{
  $effective_date = date("Y-m", strtotime($_POST['effective_date']));

$query = "
SELECT COUNT(a.department_id) AS total_count, SUM(a.amount) AS total_amount, b.department_name, b.department_location 
    FROM salary_advance a
    INNER JOIN department b ON a.department_id=b.department_id
    WHERE (a.status=1 OR a.status=2) AND DATE_FORMAT(a.date_effective,'%Y-%m') = '".$effective_date."' GROUP BY a.department_id ORDER BY b.department_name ASC
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
$total_amount = 0; 
$total_emp = 0;
foreach($result as $row)
{
  $sub_array = array();
 $sub_array[] = $sno;
 $sub_array[] = $row['department_name'].'-'.$row['department_location'];
 $sub_array[] = $row['total_count']; 
 $sub_array[] = number_format($row['total_amount'],2);

$sno ++;

 $total_amount = $total_amount + floatval($row['total_amount']);
 $total_emp = $total_emp + floatval($row['total_count']);
 
 $data[] = $sub_array;
}

$output = array(
 
 'data'    => $data,
 'total_amount'    => number_format($total_amount, 2),
 'total_emp'    => $total_emp, 
);
}
echo json_encode($output);


?>