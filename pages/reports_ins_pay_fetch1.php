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
  SELECT b.department_name, b.department_location, c.position_abbreviation, a.invoice_shift, a.invoice_rate, a.invoice_total, a.working_shift, a.working_rate, a.working_total FROM reports_department a 
  INNER JOIN department b ON a.department_id=b.department_id 
  INNER JOIN position c ON a.position_id=c.position_id 
  INNER JOIN payroll d ON a.payroll_id=d.id 
  WHERE DATE_FORMAT(d.date_from,'%Y-%m') = '".$effective_date."' ORDER BY b.department_name ASC
  "; 

$statement = $connect->prepare($query);

$statement->execute();

$number_filter_row = $statement->rowCount();

$result = $statement->fetchAll();

$data = array();
$startpoint =0;
$sno = $startpoint + 1;

$total_shift = 0;
$total_invoice = 0;
$total_working = 0;
$total_working_amount = 0;
$total_short_amount = 0;


foreach($result as $row)
{

  if ($row['invoice_shift']-$row['working_shift']>0) {
    $short_shifts=(int)$row['invoice_shift']-(int)$row['working_shift'];
    $short_amount=$short_shifts*$row['invoice_rate'];
  }elseif ($row['working_shift']-$row['invoice_shift']>0){
    $short_shifts=(int)$row['working_shift']-(int)$row['invoice_shift'];
    $short_amount=$short_shifts*$row['working_rate'];
  }else{
    $short_amount=0;
    $short_shifts=0;
  }

 
 $sub_array = array();
 $sub_array[] = $sno;
 $sub_array[] = $row['department_name'].'-'.$row['department_location'];
 $sub_array[] = $row['position_abbreviation'];
 $sub_array[] = $row['invoice_shift'];
 $sub_array[] = number_format($row['invoice_rate'],2);
 $sub_array[] = number_format($row['invoice_total'],2); 
 $sub_array[] = $row['working_shift'];
 $sub_array[] = number_format($row['working_rate'],2);
 $sub_array[] = number_format($row['working_total'],2);
 $sub_array[] = $short_shifts;
 $sub_array[] = number_format($short_amount,2);
  
 $sno ++;

 $total_shift = $total_shift + floatval($row['invoice_shift']);
 $total_invoice = $total_invoice + floatval($row['invoice_total']);
 $total_working = $total_working + floatval($row['working_shift']);
 $total_working_amount = $total_working_amount + floatval($row['working_total']);
 $total_short_amount = $total_short_amount + floatval($short_amount);

 $data[] = $sub_array;
}

$output = array(
 "data"       =>  $data,
 'total_shift'    => $total_shift,
 'total_invoice'    => number_format($total_invoice, 2), 
 'total_working'    => $total_working,
 'total_working_amount'    => number_format($total_working_amount, 2),
 'total_short_amount'    => number_format($total_short_amount, 2), 
);

}

echo json_encode($output);
?>