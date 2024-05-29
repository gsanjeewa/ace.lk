<?php

//fetch.php

include "config.php";
$connect = pdoConnection();

$column = array('employee_no');
$output='';

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
 
  $statement = $connect->prepare('SELECT SUM(amount) AS total_paid FROM inventory_deduction WHERE employee_id="'.$row['employee_id'].'" AND  status=1');
  $statement->execute();
  $result = $statement->fetchAll();
  if ($statement->rowCount() > 0) :
    foreach($result as $row_paid):

      $total_paid = $row_paid['total_paid'];
    endforeach;
  else:
    $total_paid ='';
  endif;

    $statement = $connect->prepare('SELECT SUM(amount) AS total_ded FROM inventory_deduction WHERE employee_id="'.$row['employee_id'].'"');
    $statement->execute();
    $result = $statement->fetchAll();
    if ($statement->rowCount() > 0) :
      foreach($result as $row_ded):

        $total_ded = $row_ded['total_ded'];
      endforeach;
    else:
      $total_ded ='';
    endif;

      
      $statement = $connect->prepare('SELECT SUM(amount) AS total_rem FROM inventory_deduction WHERE employee_id="'.$row['employee_id'].'" AND status=0');
    $statement->execute();
    $result = $statement->fetchAll();
    if ($statement->rowCount() > 0) :
      foreach($result as $row_rem):

        $total_rem = $row_rem['total_rem'];
      endforeach;
    else:
      $total_rem ='';
    endif;

 $sub_array = array();
 $sub_array[] = $sno;
 $sub_array[] = $row['employee_no'];
 $sub_array[] = $row['position_abbreviation'];
 $sub_array[] = $row['surname'].' '.$row['initial'];
 $sub_array[] = number_format($total_ded,2); 
 $sub_array[] = number_format($total_paid,2); 
 $sub_array[] = number_format($total_rem,2); 
   
$sno ++;

 $total_amount = $total_amount + floatval($total_ded);
 $total_paid_amount = $total_paid_amount + floatval($total_paid);
 $total_remaining_amount = $total_remaining_amount + floatval($total_rem);
 
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