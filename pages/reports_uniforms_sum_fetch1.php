<?php

/*include('database_connection.php');*/
include "config.php";
$connect = pdoConnection();

$column = array('');

$query = "
SELECT e.initial, e.surname, j.employee_no, p.position_abbreviation, a.employee_id
    FROM inventory_deduction a
    INNER JOIN join_status j ON a.employee_id = j.join_id
    INNER JOIN employee e ON j.employee_id = e.employee_id
    INNER JOIN promotions c ON j.join_id = c.employee_id    
    INNER JOIN (SELECT employee_id, MAX(id) maxid_pro FROM promotions GROUP BY employee_id) d ON c.employee_id = d.employee_id AND c.id = d.maxid_pro
    INNER JOIN position p ON c.position_id = p.position_id    
    GROUP BY a.employee_id ORDER BY cast(j.employee_no as int) ASC
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
$total_paid_amount = 0;
$total_remaining_amount = 0; 
foreach($result as $row)
{

	 $query_sum = 'SELECT sum(amount) AS total_ded FROM inventory_deduction WHERE employee_id="'.$row['employee_id'].'"';
    $statement = $connect->prepare($query_sum);
    $statement->execute();
    $result_sum = $statement->fetchAll();
    foreach($result_sum as $row_sum)
    { 
    }

    $query_paid = 'SELECT sum(amount) AS total_paid FROM inventory_deduction WHERE employee_id="'.$row['employee_id'].'" AND status=1';
    $statement = $connect->prepare($query_paid);
    $statement->execute();
    $result_paid = $statement->fetchAll();
    foreach($result_paid as $row_paid)
    { 
    }

    $remaining=(string)$row_sum['total_ded']-(string)$row_paid['total_paid'];

 $sub_array = array();
 $sub_array[] = $sno;
 $sub_array[] = $row['employee_no'];
 $sub_array[] = $row['position_abbreviation'];
 $sub_array[] = $row['surname'].' '.$row['initial'];
 $sub_array[] = number_format($row_sum['total_ded'],2); 
 $sub_array[] = number_format($row_paid['total_paid'],2);
 $sub_array[] = number_format($remaining,2);
 $sno ++;

$total_amount = $total_amount + floatval($row_sum['total_ded']);
 $total_paid_amount = $total_paid_amount + floatval($row_paid['total_paid']);
 $total_remaining_amount = $total_remaining_amount + floatval($remaining);
 
 $data[] = $sub_array;
}


$output = array(
 'data'    => $data,
 'total_amount'    => number_format($total_amount, 2),
 'total_paid_amount'    => number_format($total_paid_amount, 2), 
 'total_remaining_amount'    => number_format($total_remaining_amount, 2), 
);

echo json_encode($output);

?>