<?php
error_reporting(0);

include('config.php');
$connect = pdoConnection();
/*session_start();*/

if(isset($_POST["query"]) && $_POST['query'] != '')
 {
  $output = array();
  $data = array();
  
  //-----------------Monthly Pay------------------------//

  $statement = $connect->prepare("SELECT COALESCE(sum(net),'0') AS total FROM payroll_items WHERE employee_id ='".$_POST["query"]."' AND status=2");
  $statement->execute();
  $result = $statement->fetchAll();
    foreach($result as $payroll){  
      $last_payroll =$payroll['total'];    
    }
  
  $deduction="";
  //-----------------Loan Details------------------------//

  $statement = $connect->prepare("SELECT COALESCE(sum(paid_amount),'0') AS total FROM loan_schedules WHERE employee_id='".$_POST["query"]."' AND status = 0");
  $statement->execute();
  $total_loan_schedules = $statement->rowCount();
  $result = $statement->fetchAll();
  
  foreach($result as $loan_deductions){   
    $loan=$loan_deductions['total']; 
    if (!empty($loan_deductions['total'])) {  
    $deduction .="<li class='d-flex justify-content-between align-items-center'>Loan:<span class='badge badge-primary badge-pill'></span><span>".number_format($loan_deductions['total'],2)."</span></li>";}
  }

  //-----------------Advance Details------------------------//

  $statement = $connect->prepare("SELECT COALESCE(sum(amount),'0') AS total FROM salary_advance WHERE employee_id='".$_POST["query"]."' AND status=2");
  $statement->execute();
  $result = $statement->fetchAll();
  foreach($result as $advance_deductions){
  
    $advance=$advance_deductions['total'];
    if (!empty($advance_deductions['total'])) {
    $deduction .="<li class='d-flex justify-content-between align-items-center'>Advance:<span class='badge badge-primary badge-pill'></span><span>".number_format($advance_deductions['total'],2)."</span></li>";}      
  }
  
  //-----------------Inventory Details------------------------//

  $statement = $connect->prepare("SELECT COALESCE(sum(amount),'0') AS total FROM inventory_deduction WHERE employee_id = '".$_POST["query"]."' AND status = 0");
  $statement->execute();
  $result = $statement->fetchAll();
  foreach($result as $inventory_deductions){

    $equipment=$inventory_deductions['total']; 
    if (!empty($inventory_deductions['total'])) {
    $deduction .="<li class='d-flex justify-content-between align-items-center'>Uniforms:<span class='badge badge-primary badge-pill'></span><span>".number_format($inventory_deductions['total'],2)."</span></li>"; }  
  } 
    
  //-----------------Ration Details------------------------//

  $statement = $connect->prepare("SELECT COALESCE(sum(amount),'0') AS total FROM ration_deduction WHERE employee_id='".$_POST["query"]."' AND status = 0");
  $statement->execute();
  $result = $statement->fetchAll();
  foreach($result as $ration_deductions){ 
    $ration=$ration_deductions['total'];
    if (!empty($ration_deductions['total'])) {
    $deduction .="<li class='d-flex justify-content-between align-items-center'>Ration:<span class='badge badge-primary badge-pill'></span><span>".number_format($ration_deductions['total'],2)."</span></li>";}
  }

  //-----------------Deduction Details------------------------//
  $emp_deduction=array();
  $statement = $connect->prepare("SELECT COALESCE(sum(a.amount),'0') AS total, b.deduction_en FROM employee_deductions a INNER JOIN deduction b ON a.deduction_id=b.deduction_id WHERE a.employee_id='".$_POST["query"]."' AND a.status = 0 AND a.deduction_id!=4 GROUP BY a.deduction_id");
  $statement->execute();
  $result = $statement->fetchAll();
  foreach($result as $deductions){ 
    $emp_deduction[]=$deductions['total'];
    if (!empty($deductions['total'])) {
     $deduction .="<li class='d-flex justify-content-between align-items-center'>".$deductions['deduction_en'].":<span class='badge badge-primary badge-pill'></span><span>".number_format($deductions['total'],2)."</span></li>";}
  }


  $gross=(string)$last_payroll;
  $total_deduction=(string)$loan+(string)$advance+(string)$equipment+(string)$ration+array_sum($emp_deduction);
  $net=(string)$gross-(string)$total_deduction;  
    
      $data[] = array(
        'last_payroll'    =>  number_format($last_payroll,2),
        'gross'           =>  number_format($gross,2),
        'loan'            =>  $deduction,
        'total_deduction' =>  number_format($total_deduction,2),
        'net'             =>  number_format($net,2),
      );    
 }

 $output = array(
    'data'        =>  $data,    
  );

echo json_encode($output);

?>