<?php

/*include('database_connection.php');*/
include "config.php";
$connect = pdoConnection();

$column = array('employee_no');

$query = "
SELECT * FROM loan_list 
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
foreach($result as $row)
{

	 $query = 'SELECT e.employee_id, e.surname, e.initial, e.nic_no, j.employee_no, p.position_abbreviation FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid INNER JOIN position p ON e.position_id=p.position_id WHERE j.join_id="'.$row['employee_id'].'" ORDER BY e.employee_id DESC';

    $statement = $connect->prepare($query);
    $statement->execute();
    $total_data = $statement->rowCount();
    $result = $statement->fetchAll();
    foreach($result as $row_employee):                          
    endforeach;

$statement = $connect->prepare('SELECT c.position_abbreviation FROM promotions a INNER JOIN position c ON a.position_id=c.position_id INNER JOIN (SELECT employee_id, MAX(id) maxid FROM promotions GROUP BY employee_id) b ON a.employee_id = b.employee_id AND a.id = b.maxid WHERE a.employee_id="'.$row['employee_id'].'"');
    $statement->execute();
    $total_position = $statement->rowCount();
    $result = $statement->fetchAll();
    if ($total_position > 0) :
      foreach($result as $position_name):

        $position_id = $position_name['position_abbreviation'];
      endforeach;
    else:
      $position_id ='';
    endif;

    $statement = $connect->prepare('SELECT SUM(paid_amount) AS total_paid_amount, COUNT(paid_amount) AS last_installments FROM loan_schedules WHERE employee_id="'.$row['employee_id'].'" AND loan_id="'.$row['id'].'" AND status=1');
    $statement->execute();
    $total_paid = $statement->rowCount();
    $result = $statement->fetchAll();
    if ($total_paid > 0) :
      foreach($result as $row_paid):

        $total_paid_amount = $row_paid['total_paid_amount'];
        $last_installments = $row_paid['last_installments'];
      endforeach;
    else:
      $total_paid_amount ='';
      $last_installments = '';
    endif;

    $statement = $connect->prepare('SELECT SUM(paid_amount) AS total_remaining_amount, COUNT(paid_amount) AS total_remaining_count FROM loan_schedules WHERE employee_id="'.$row['employee_id'].'" AND loan_id="'.$row['id'].'" AND status=0');
    $statement->execute();
    $total_paid = $statement->rowCount();
    $result = $statement->fetchAll();
    if ($total_paid > 0) :
      foreach($result as $row_rem):

        $total_remaining_amount = $row_rem['total_remaining_amount'];
        $total_remaining_count = $row_rem['total_remaining_count'];

      endforeach;
    else:
      $total_remaining_amount ='';
      $total_remaining_count = '';
    endif;

    $last_month = date('Y-m', strtotime("-1 month"));

    $statement = $connect->prepare('SELECT paid_amount AS last_paid_amount FROM loan_schedules WHERE DATE_FORMAT(date_due,"%Y-%m") = "'.$last_month.'" AND employee_id="'.$row['employee_id'].'" AND loan_id="'.$row['id'].'" AND status=1 ORDER BY id DESC LIMIT 1');
    $statement->execute();
    $total_paid = $statement->rowCount();
    $result = $statement->fetchAll();
    if ($total_paid > 0) :
      foreach($result as $row_last_paid):

        $last_paid_amount = number_format($row_last_paid['last_paid_amount'],2);
      endforeach;
    else:
      $last_paid_amount ='';
    endif;


if ($row['loan_amount'] !=0) : $loan_amount = number_format($row['loan_amount'],2); else: $loan_amount =''; endif;
if ($row['loan_plan'] !=0) :$loan_plan = number_format($row['loan_plan']); else:$loan_plan = '';endif;

if ($total_paid_amount !=0) :$total_paid_amount= number_format($total_paid_amount,2); else:$total_paid_amount = '';endif;

if ($total_remaining_amount !=0) : $total_remaining_amount = number_format($total_remaining_amount,2);else:$total_remaining_amount='';endif;



 $sub_array = array();
 $sub_array[] = $sno;
 $sub_array[] = $row_employee['employee_no'];
 $sub_array[] = $position_id;
 $sub_array[] = $row_employee['surname'].' '.$row_employee['initial'];
 $sub_array[] = $loan_amount;
 $sub_array[] = $loan_plan;
 $sub_array[] = $total_paid_amount;
 $sub_array[] = $total_remaining_amount;
 $sub_array[] = $total_remaining_count;
 $sub_array[] = $last_paid_amount;
 $sub_array[] = $last_installments;
 $sno ++;

 $data[] = $sub_array;
}

function count_all_data($connect)
{
 $query = "SELECT * FROM loan_list";
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