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
SELECT b.department_name, b.department_location, c.position_abbreviation, a.invoice_shift, a.invoice_rate, a.invoice_total, a.working_shift, a.working_rate, a.working_total FROM reports_department a 
  INNER JOIN department b ON a.department_id=b.department_id 
  INNER JOIN position c ON a.position_id=c.position_id 
  INNER JOIN payroll d ON a.payroll_id=d.id 
  WHERE DATE_FORMAT(d.date_from,'%Y-%m') = '".$effective_date."' ORDER BY b.department_name ASC

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
$total_shift = 0;
$total_invoice = 0;
$total_working = 0;
$total_working_amount = 0;
$total_short_amount = 0;
$total_short_invoice = 0;
$total_short_working = 0;

foreach($result as $row)
{
  if ($row['invoice_shift']-$row['working_shift']>0) {
    $invoice_short_shifts=(int)$row['invoice_shift']-(int)$row['working_shift'];
    $short_amount=$invoice_short_shifts*$row['invoice_rate'];
    $working_short_shifts='';
  }elseif ($row['working_shift']-$row['invoice_shift']>0){
    $working_short_shifts=(int)$row['working_shift']-(int)$row['invoice_shift'];
    $short_amount=$working_short_shifts*$row['working_rate'];
    $invoice_short_shifts='';
  }else{
    $short_amount=0;
    $working_short_shifts='';
    $invoice_short_shifts='';
  }

  if ($row['invoice_rate'] !=0) : $invoice_rate = number_format($row['invoice_rate'],2); else: $invoice_rate =''; endif;

  if ($row['invoice_shift'] !=0) : $invoice_shift = $row['invoice_shift']; else: $invoice_shift =''; endif;

  if ($row['working_shift'] !=0) : $working_shift = $row['working_shift']; else: $working_shift =''; endif;

  if ($row['invoice_total'] !=0) : $invoice_total = number_format($row['invoice_total'],2); else: $invoice_total =''; endif;

  if ($row['working_rate'] !=0) : $working_rate = number_format($row['working_rate'],2); else: $working_rate =''; endif;

  if ($row['working_total'] !=0) : $working_total = number_format($row['working_total'],2); else: $working_total =''; endif;

  if ($short_amount !=0) : $short_amount1 = number_format($short_amount,2); else: $short_amount1 =''; endif;
   
 $sub_array = array();
 $sub_array[] = $sno;
 $sub_array[] = $row['department_name'].'-'.$row['department_location'];
 $sub_array[] = $row['position_abbreviation'];
 $sub_array[] = $invoice_shift;
 $sub_array[] = $invoice_rate;
 $sub_array[] = $invoice_total; 
 $sub_array[] = $working_shift;
 $sub_array[] = $working_rate;
 $sub_array[] = $working_total;
 $sub_array[] = $invoice_short_shifts;
 $sub_array[] = $working_short_shifts;
 $sub_array[] = $short_amount1;
$sno ++;

 $total_shift = $total_shift + floatval($row['invoice_shift']);
 $total_invoice = $total_invoice + floatval($row['invoice_total']);
 $total_working = $total_working + floatval($row['working_shift']);
 $total_working_amount = $total_working_amount + floatval($row['working_total']);
 $total_short_amount = $total_short_amount + floatval($short_amount);
 $total_short_invoice = $total_short_invoice + floatval($invoice_short_shifts);
 $total_short_working = $total_short_working + floatval($working_short_shifts);
 
 $data[] = $sub_array;
}

$output = array(
 
 'data'    => $data,
 'total_shift'    => $total_shift,
 'total_invoice'    => number_format($total_invoice, 2), 
 'total_working'    => $total_working,
 'total_working_amount'    => number_format($total_working_amount, 2),
 'total_short_amount'    => number_format($total_short_amount, 2),
 'total_short_invoice'    => $total_short_invoice,
 'total_short_working'    => $total_short_working,

);
}
echo json_encode($output);

?>