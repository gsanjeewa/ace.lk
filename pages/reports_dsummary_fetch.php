<?php

//fetch.php

include "config.php";
$connect = pdoConnection();

$column = array('');
$output='';
$data = array();
$sub_array=array();

###################### Loan Details ########################

$query_loan_paid ="SELECT COALESCE(sum(paid_amount),'0') AS total_loan FROM loan_schedules WHERE status=1 ";
if(isset($_POST['effective_date']) && $_POST['effective_date'] != '')
{
  $effective_date = date("Y-m", strtotime($_POST['effective_date']));
  $query_loan_paid .="AND DATE_FORMAT(date_due,'%Y-%m') = '".$effective_date."' ";
}

$statement = $connect->prepare($query_loan_paid);
$statement->execute();
$result = $statement->fetchAll();
foreach($result as $row_loan_paid)
{
}

$query_loan_to_paid ="SELECT COALESCE(sum(paid_amount),'0') AS total_loan FROM loan_schedules WHERE status=0 ";
if(isset($_POST['effective_date']) && $_POST['effective_date'] != '')
{
  $effective_date = date("Y-m", strtotime($_POST['effective_date']));
  $query_loan_to_paid .="AND DATE_FORMAT(date_due,'%Y-%m') = '".$effective_date."' ";
}
$statement = $connect->prepare($query_loan_to_paid);
$statement->execute();
$result = $statement->fetchAll();
foreach($result as $row_loan_to_paid)
{
}

$sub_array[]=array('did'=>'Loan Deduction',"paid"=>$row_loan_paid['total_loan'],"not_paid"=>$row_loan_to_paid['total_loan']);
 
###################### Uniforms Details ########################

 $query_uniforms_paid ="SELECT COALESCE(sum(amount),'0') AS total_uniforms
  FROM inventory_deduction WHERE status=1 ";
  if(isset($_POST['effective_date']) && $_POST['effective_date'] != '')
{
  $effective_date = date("Y-m", strtotime($_POST['effective_date']));
  $query_uniforms_paid .="AND DATE_FORMAT(due_date,'%Y-%m') = '".$effective_date."' ";
}
$statement = $connect->prepare($query_uniforms_paid);
$statement->execute();
$result = $statement->fetchAll();
foreach($result as $row_uniforms_paid)
{
}

$query_uniforms_to_paid ="SELECT COALESCE(sum(amount),'0') AS total_uniforms
  FROM inventory_deduction WHERE status=0 ";
  if(isset($_POST['effective_date']) && $_POST['effective_date'] != '')
{
  $effective_date = date("Y-m", strtotime($_POST['effective_date']));
  $query_uniforms_to_paid .="AND DATE_FORMAT(due_date,'%Y-%m') = '".$effective_date."' ";
}
$statement = $connect->prepare($query_uniforms_to_paid);
$statement->execute();
$result = $statement->fetchAll();
foreach($result as $row_uniforms_to_paid)
{
}

$sub_array[]=array('did'=>'Uniforms Deduction',"paid"=>$row_uniforms_paid['total_uniforms'],"not_paid"=>$row_uniforms_to_paid['total_uniforms']);

###################### Advance Details ########################

 $query_advance_paid ="SELECT COALESCE(sum(amount),'0') AS total_advance
  FROM salary_advance WHERE status=1 ";
  if(isset($_POST['effective_date']) && $_POST['effective_date'] != '')
{
  $effective_date = date("Y-m", strtotime($_POST['effective_date']));
  $query_advance_paid .="AND DATE_FORMAT(date_effective,'%Y-%m') = '".$effective_date."' ";
}
$statement = $connect->prepare($query_advance_paid);
$statement->execute();
$result = $statement->fetchAll();
foreach($result as $row_advance_paid)
{
}

$query_advance_to_paid ="SELECT COALESCE(sum(amount),'0') AS total_advance
  FROM salary_advance WHERE status=2 ";
  if(isset($_POST['effective_date']) && $_POST['effective_date'] != '')
{
  $effective_date = date("Y-m", strtotime($_POST['effective_date']));
  $query_advance_to_paid .="AND DATE_FORMAT(date_effective,'%Y-%m') = '".$effective_date."' ";
}
$statement = $connect->prepare($query_advance_to_paid);
$statement->execute();
$result = $statement->fetchAll();
foreach($result as $row_advance_to_paid)
{
}

$sub_array[]=array('did'=>'Advance Deduction',"paid"=>$row_advance_paid['total_advance'],"not_paid"=>$row_advance_to_paid['total_advance']);

###################### Hostel Details ########################

 $query_hostel_paid ="SELECT COALESCE(sum(amount),'0') AS total_hostel
  FROM employee_deductions WHERE deduction_id=1 AND status=1 ";
  if(isset($_POST['effective_date']) && $_POST['effective_date'] != '')
{
  $effective_date = date("Y-m", strtotime($_POST['effective_date']));
  $query_hostel_paid .="AND DATE_FORMAT(effective_date,'%Y-%m') = '".$effective_date."' ";
}
$statement = $connect->prepare($query_hostel_paid);
$statement->execute();
$result = $statement->fetchAll();
foreach($result as $row_hostel_paid)
{
}

$query_hostel_to_paid ="SELECT COALESCE(sum(amount),'0') AS total_hostel
  FROM employee_deductions WHERE deduction_id=1 AND status=0 ";
  if(isset($_POST['effective_date']) && $_POST['effective_date'] != '')
{
  $effective_date = date("Y-m", strtotime($_POST['effective_date']));
  $query_hostel_to_paid .="AND DATE_FORMAT(effective_date,'%Y-%m') = '".$effective_date."' ";
}
$statement = $connect->prepare($query_hostel_to_paid);
$statement->execute();
$result = $statement->fetchAll();
foreach($result as $row_hostel_to_paid)
{
}

$sub_array[]=array('did'=>'Hostel Deduction',"paid"=>$row_hostel_paid['total_hostel'],"not_paid"=>$row_hostel_to_paid['total_hostel']);


###################### Ration Details ########################

 $query_ration_paid ="SELECT COALESCE(sum(amount),'0') AS total_ration
  FROM ration_deduction WHERE status=1 ";
  if(isset($_POST['effective_date']) && $_POST['effective_date'] != '')
{
  $effective_date = date("Y-m", strtotime($_POST['effective_date']));
  $query_ration_paid .="AND DATE_FORMAT(date_effective,'%Y-%m') = '".$effective_date."' ";
}
$statement = $connect->prepare($query_ration_paid);
$statement->execute();
$result = $statement->fetchAll();
foreach($result as $row_ration_paid)
{
}

$query_ration_to_paid ="SELECT COALESCE(sum(amount),'0') AS total_ration
  FROM ration_deduction WHERE status=0 ";
  if(isset($_POST['effective_date']) && $_POST['effective_date'] != '')
{
  $effective_date = date("Y-m", strtotime($_POST['effective_date']));
  $query_ration_to_paid .="AND DATE_FORMAT(date_effective,'%Y-%m') = '".$effective_date."' ";
}
$statement = $connect->prepare($query_ration_to_paid);
$statement->execute();
$result = $statement->fetchAll();
foreach($result as $row_ration_to_paid)
{
}

$sub_array[]=array('did'=>'Ration Deduction',"paid"=>$row_ration_paid['total_ration'],"not_paid"=>$row_ration_to_paid['total_ration']);

###################### Fines Details ########################

 $query_fines_paid ="SELECT COALESCE(sum(amount),'0') AS total_fines
  FROM employee_deductions WHERE deduction_id=2 AND status=1 ";
  if(isset($_POST['effective_date']) && $_POST['effective_date'] != '')
{
  $effective_date = date("Y-m", strtotime($_POST['effective_date']));
  $query_fines_paid .="AND DATE_FORMAT(effective_date,'%Y-%m') = '".$effective_date."' ";
}
$statement = $connect->prepare($query_fines_paid);
$statement->execute();
$result = $statement->fetchAll();
foreach($result as $row_fines_paid)
{
}

$query_fines_to_paid ="SELECT COALESCE(sum(amount),'0') AS total_fines
  FROM employee_deductions WHERE deduction_id=2 AND status=0 ";
  if(isset($_POST['effective_date']) && $_POST['effective_date'] != '')
{
  $effective_date = date("Y-m", strtotime($_POST['effective_date']));
  $query_fines_to_paid .="AND DATE_FORMAT(effective_date,'%Y-%m') = '".$effective_date."' ";
}
$statement = $connect->prepare($query_fines_to_paid);
$statement->execute();
$result = $statement->fetchAll();
foreach($result as $row_fines_to_paid)
{
}

$sub_array[]=array('did'=>'Fines Deduction',"paid"=>$row_fines_paid['total_fines'],"not_paid"=>$row_fines_to_paid['total_fines']);


#################################################################
$startpoint =0;
$sno = $startpoint + 1;
$total_paid = 0; 
$total_not_paid = 0; 
foreach($sub_array as $k)
{
  $sub_array2 = array();
  $sub_array2[] = $sno;
  $sub_array2[] =$k['did'];
  $sub_array2[] =number_format($k['paid'], 2);
  $sub_array2[] =number_format($k['not_paid'], 2);

  $sno ++;
  $total_paid = $total_paid + floatval($k['paid']);
  $total_not_paid = $total_not_paid + floatval($k['not_paid']);
  $data[] = $sub_array2;
}


$output = array(
 
 'data'    => $data,
 'total_paid'    => number_format($total_paid, 2),
 'total_not_paid'    => number_format($total_not_paid, 2), 
);

echo json_encode($output);


?>