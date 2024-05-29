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
  SELECT e.initial, e.surname, j.employee_no, a.amount, p.position_abbreviation, f.department_name, e.employee_id, f.department_location, a.status 
    FROM salary_advance a
    INNER JOIN join_status j ON a.employee_id = j.join_id
    INNER JOIN employee e ON j.employee_id = e.employee_id
    INNER JOIN promotions c ON j.join_id = c.employee_id    
    INNER JOIN (SELECT employee_id, MAX(id) maxid_pro FROM promotions GROUP BY employee_id) d ON c.employee_id = d.employee_id AND c.id = d.maxid_pro
    INNER JOIN position p ON c.position_id = p.position_id
    LEFT JOIN department f ON a.department_id = f.department_id    
    WHERE (a.status=2 OR a.status=1) AND DATE_FORMAT(a.date_effective,'%Y-%m') = '".$effective_date."' ORDER BY f.department_name ASC, cast(j.employee_no as int) ASC
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

foreach($result as $row)
{
  if ($row['status']==1) {
    $status1='Paid';
  }elseif($row['status']==2) {
    $status1='To be Paid';
  }
   
    $statement = $connect->prepare("SELECT * FROM bank_details WHERE employee_id='".$row['employee_id']."' AND status = 0");
    $statement->execute();
    $total_bank = $statement->rowCount();
    $sub_array = array();
    if (!empty($total_bank)) :
      
      $status ='Deposit Bank';
      $sub_array[] = $sno;
   $sub_array[] = $row['department_name'].'-'.$row['department_location'];
   $sub_array[] = $row['employee_no'];
   $sub_array[] = $row['position_abbreviation'];
   $sub_array[] = $row['surname'].' '.$row['initial'];
   $sub_array[] = number_format($row['amount'],2); 
   $sub_array[] = $status1; 
$sno ++;

 $total_amount = $total_amount + floatval($row['amount']);
 
 $data[] = $sub_array;
 endif;
}

$output = array(
 
 'data'    => $data,
 'total_amount'    => number_format($total_amount, 2), 
);
}
echo json_encode($output);


?>