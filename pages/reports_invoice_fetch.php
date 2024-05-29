<?php

/*include('database_connection.php');*/
include "config.php";
$connect = pdoConnection();

$column = array('employee_no');

$output = '';

if(isset($_POST['effective_date']) && $_POST['effective_date'] != '')
{
	$effective_date = date("Y-m-d", strtotime($_POST['effective_date']));
$query = "
SELECT * FROM reports_income a 
INNER JOIN payroll b ON a.payroll_id = b.id 
INNER JOIN department c ON a.department_id = c.department_id
INNER JOIN sector d ON a.sector_id = d.id WHERE b.date_from = '".$effective_date."' ORDER BY d.sector ASC, a.department_id ASC
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
	
if ($row['invoice_amount'] !='') : $invoice_amount = number_format($row['invoice_amount'],2); else: $invoice_amount =''; endif;
if ($row['gross_amount'] !=0) :$gross_amount = number_format($row['gross_amount'],2); else:$gross_amount = '';endif;

 if ($row['employer_epf'] !=0) : $employer_epf = number_format($row['employer_epf'],2); else:$employer_epf=''; endif;

 if ($row['employer_etf'] !=0) : $employer_etf = number_format($row['employer_etf'],2); else: $employer_etf=''; endif;

 if ($row['employee_epf']!=0) : $employee_epf = number_format($row['employee_epf'],2);else: $employee_epf=''; endif; 


 $sub_array = array();
 $sub_array[] = $sno;
 $sub_array[] = $row['sector'];
 $sub_array[] = $row['department_name'].'-'.$row['department_location'];
 $sub_array[] = $invoice_amount;
 $sub_array[] = $gross_amount;
 $sub_array[] = $employer_epf;
 $sub_array[] = $employer_etf;
 $sub_array[] = $employee_epf;
  
 $sno ++;

 $data[] = $sub_array;
}

function count_all_data($connect)
{
 $query = "SELECT * FROM payroll_items WHERE status=1 OR status=3";
 $statement = $connect->prepare($query);
 $statement->execute();
 return $statement->rowCount();
}

$output = array(
 
 "recordsTotal"   =>  count_all_data($connect),
 "recordsFiltered"  =>  $number_filter_row,
 "data"       =>  $data
);
}
echo json_encode($output);

?>