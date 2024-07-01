<?php

//fetch.php

include "config.php";
$connect = pdoConnection();

$column = array('employee_id', 'gross', 'deduction_amount', 'net');
$output='';
if(isset($_POST['effective_date']) && $_POST['effective_date'] != '')
{
  $effective_date = date("Y-m-d", strtotime($_POST['effective_date']));

$query = '
SELECT * FROM payroll_items a INNER JOIN payroll b ON a.payroll_id = b.id WHERE b.date_from = "'.$effective_date.'" AND (a.status=1 OR a.status=3) AND employee_epf > 0 ORDER BY a.employee_id ASC

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
$total_epf_8 = 0;
$total_epf_12 = 0;
$total_etf = 0;

foreach($result as $row)
{
	$query = 'SELECT * FROM payroll WHERE id="'.$row['payroll_id'].'"';
    $statement = $connect->prepare($query);
    $statement->execute();
    $result = $statement->fetchAll();
    foreach($result as $date_from)
    { 
    }
    
    $query = 'SELECT a.surname, a.initial FROM employee a
    INNER JOIN join_status b ON a.employee_id=b.employee_id WHERE b.join_id="'.$row['employee_id'].'"';
    $statement = $connect->prepare($query);
    $statement->execute();
    $result = $statement->fetchAll();
    foreach($result as $employee_name)
    { 
    }

    $query = 'SELECT position_abbreviation FROM position WHERE position_id="'.$row['position_id'].'"';
    $statement = $connect->prepare($query);
    $statement->execute();
    $total_position = $statement->rowCount();
    $result = $statement->fetchAll();
    if ($total_position > 0) {
      foreach($result as $position_name)
      { 
        $position_id = $position_name['position_abbreviation'];
      }
      }else{
        $position_id ='';
      }

if ($row['basic_epf'] !=0) : $basic_epf = number_format($row['basic_epf'],2);else:$basic_epf='';endif;

if ($row['employee_epf']!=0) : $employee_epf = number_format($row['employee_epf'],2);else: $employee_epf=''; endif; 

if ($row['employer_epf'] !=0) : $employer_epf = number_format($row['employer_epf'],2); else:$employer_epf=''; endif;

if ($row['employer_etf'] !=0) : $employer_etf = number_format($row['employer_etf'],2); else: $employer_etf=''; endif;


  $sub_array = array();
  $sub_array[] = $sno;
  $sub_array[] = $row['employee_no'];
  $sub_array[] = $position_id;
  $sub_array[] = $employee_name['surname'].' '.$employee_name['initial']; 
  $sub_array[] = $basic_epf; 
  $sub_array[] = $employee_epf; 
  $sub_array[] = $employer_epf;
  $sub_array[] = $employer_etf;
  $sno ++;

 
 $total_epf_8 = $total_epf_8 + floatval($row['employee_epf']); 
 $total_epf_12 = $total_epf_12 + floatval($row['employer_epf']);
 $total_etf = $total_etf + floatval($row['employer_etf']);
 $data[] = $sub_array;
}

$output = array(
 'data'    => $data, 
 'total_epf_8'    => number_format($total_epf_8, 2), 
 'total_epf_12'    => number_format($total_epf_12, 2),
 'total_etf'    => number_format($total_etf, 2)
);
}
echo json_encode($output);


?>