<?php

//process.php
include 'config.php';
/*$connect = new PDO("mysql:host=localhost; dbname=testing", "root", "");*/
$connect = pdoConnection();
if(isset($_POST["employee_id"]))
{
  $data_deduction = array();
  $statement = $connect->prepare("SELECT employee_id, employee_no FROM join_status WHERE join_id='".$_POST["employee_id"]."'");
  $statement->execute();
  $result = $statement->fetchAll();
  foreach($result as $row_employee){
    $employee_no = $row_employee['employee_no'];
    $employee_id = $row_employee['employee_id'];    
  }
//-----------------Bank Details------------------------//

      $statement = $connect->prepare("SELECT id FROM bank_details WHERE employee_id='".$employee_id."' AND status=0 ORDER BY id DESC LIMIT 1");
      $statement->execute();
      $result = $statement->fetchAll();
      if ($statement->rowCount()>0) {
        foreach($result as $bank_id){
            $bank_id = $bank_id['id'];
        }
      }else{
        $bank_id='';
      }

  //-----------------Payroll Details------------------------//
  $month_pay=array();
  $month_pay_id=array();
 	$statement = $connect->prepare("SELECT net, id, payroll_id FROM payroll_items WHERE employee_id ='".$_POST["employee_id"]."' AND status=2");
	$statement->execute();
  $result = $statement->fetchAll();
  foreach($result as $payroll){  
    $month_pay[] = $payroll['net'];
    $month_pay_id[] = $payroll['id'];
    $payroll_id = $payroll['payroll_id'];
	}

  $month_sum=array_sum($month_pay);
    
  //-----------------Loan Details------------------------//
  $loan_id=array();
  $paid_amount=array();
  $sub_array = array();
  $statement = $connect->prepare("SELECT paid_amount, id, loan_id FROM loan_schedules WHERE employee_id='".$_POST["employee_id"]."' AND status=0");
  $statement->execute();
  $total_loan_schedules = $statement->rowCount();
  $result = $statement->fetchAll();
  
    foreach($result as $loan_deductions){             
      $paid_amount[]=$loan_deductions['paid_amount'];
      $loan_id[]=$loan_deductions['id'];
      $loan_list_id=$loan_deductions['loan_id'];
      
    }

  if (array_sum($paid_amount) != 0) {
    $sub_array[]=array('did'=>'Loan Deduction',"amount"=>array_sum($paid_amount));
  }
    
  $loan_sum=array_sum($paid_amount);
    
  //-----------------Advance Details------------------------//

  $statement = $connect->prepare("SELECT amount, id FROM salary_advance WHERE employee_id='".$_POST["employee_id"]."' AND status=0");
  $statement->execute();
  $total_advance = $statement->rowCount();
  $result = $statement->fetchAll();
  if ($total_advance > 0) {
    foreach($result as $advance_deductions){             
      $advance_amount=$advance_deductions['amount']; 
      $advance_id=$advance_deductions['id']; 
      $sub_array[]=array('did'=>'Advance Deduction',"amount"=>$advance_deductions['amount']);  
    }
  }else{
    $advance_amount='';
  } 

  //-----------------Inventory Details------------------------//
  $inventory_id=array();
  $inventory_amount=array();
  $statement = $connect->prepare("SELECT amount, id FROM inventory_deduction WHERE employee_id='".$_POST["employee_id"]."' AND status = 0");
  $statement->execute();
  $total_inventory = $statement->rowCount();
  $result = $statement->fetchAll();
  foreach($result as $inventory_deductions){             
    $inventory_amount[]=$inventory_deductions['amount'];
    $inventory_id[]=$inventory_deductions['id']; 
       
  } 

  if (array_sum($inventory_amount) != 0) {
    $sub_array[]=array('did'=>'Uniforms',"amount"=>array_sum($inventory_amount));
  }
  
  $inventory_sum=array_sum($inventory_amount);
  
  //-----------------Ration Details------------------------//
  $ration_id=array();
  $ration_amount=array();
  $statement = $connect->prepare("SELECT amount, id FROM ration_deduction WHERE employee_id='".$_POST["employee_id"]."' AND status = 0");
  $statement->execute();
  $total_ration = $statement->rowCount();
  $result = $statement->fetchAll();
  
    foreach($result as $ration_deductions){             
      $ration_amount[]=$ration_deductions['amount'];
      $ration_id[]=$ration_deductions['id'];
      
    }

  if (array_sum($ration_amount) != 0) {
    $sub_array[]=array('did'=>'Ration',"amount"=>array_sum($ration_amount));
  }


  $ration_sum=array_sum($ration_amount);


  //-----------------Deduction Details------------------------//
  $emp_deduction=array();
  $emp_deduction_id=array();
  $statement = $connect->prepare("SELECT COALESCE(sum(a.amount),'0') AS total, b.deduction_en, a.id FROM employee_deductions a INNER JOIN deduction b ON a.deduction_id=b.deduction_id WHERE a.employee_id='".$_POST["employee_id"]."' AND a.status = 0 AND a.deduction_id!=4 GROUP BY a.deduction_id");
  $statement->execute();
  $result = $statement->fetchAll();
  foreach($result as $deductions){ 
    $emp_deduction[]=$deductions['total'];
    $emp_deduction_id[]=$deductions['id'];
    $sub_array[]=array('did'=>$deductions['deduction_en'],"amount"=>$deductions['total']);
    
  }

  $emp_deduction_sum=array_sum($emp_deduction);
$sub_array[]=array('did'=>'Document Chargers',"amount"=>$_POST['doc_chargers']);
$paid_array=array();
$paid_array[]=array('reson'=>$_POST['reson'],"amount"=>$_POST['paid_amount']);

       	$gross=(float)$month_sum;
      	$total_deduction=(float)$loan_sum+(float)$advance_amount+(float)$inventory_sum+(float)$ration_sum+(float)$emp_deduction_sum+(float)$_POST['doc_chargers'];

      	$net=(float)$gross-(float)$total_deduction-(float)$_POST['paid_amount'];
        
		$data = array(
  		':employee_id'  	  => $_POST["employee_id"],
  		':employee_no'  	  => $employee_no,
  		':last_month_pay'   => $month_sum,
      ':gross_income'     => $gross,
			':status'  	        => 1,
      ':loan_deduction'      => json_encode($sub_array),
      ':inventory_deduction' => $payroll_id,
      ':advance_deduction'   => $bank_id,
      ':ration_deduction'   => json_encode($paid_array),
      ':total_deduction'  => $total_deduction,
      ':net_amount'       => $net,
	 	);

	 	$query = "
	 	INSERT INTO `resignation`(`employee_id`, `employee_no`, `last_month_pay`, `gross_income`, `advance_deduction`, `loan_deduction`, `inventory_deduction`, `ration_deduction`, `total_deduction`, `net_amount`, `status`)
	 	VALUES (:employee_id, :employee_no, :last_month_pay, :gross_income, :advance_deduction, :loan_deduction, :inventory_deduction, :ration_deduction, :total_deduction, :net_amount, :status);
    UPDATE join_status SET employee_status=3, resignation_date=CURDATE() WHERE join_id='".$_POST["employee_id"]."';   
	 	"; 

    if ($advance_id > 0){
      $query .= "UPDATE salary_advance SET status=1 WHERE id='".$advance_id."';";
    }

	 	$statement = $connect->prepare($query);

	 	$statement->execute($data);

    //-----------------Inventory Details------------------------//

    for ($i = 0; $i <= count($inventory_id); $i++) {  

      $data_inventory = array(
        ':id'     =>  $inventory_id[$i],
        ':status' =>  1,
      );

      $query_inventory = "
      UPDATE `inventory_deduction` SET `status`=:status WHERE `id`=:id    
      ";
        
      $statement = $connect->prepare($query_inventory);
      $statement->execute($data_inventory);
    }

    //-----------------Payroll Details------------------------//

    for ($j = 0; $j <= count($month_pay_id); $j++) {  

      $data_payroll = array(
        ':id'     =>  $month_pay_id[$j],
        ':status' =>  4,
      );

      $query_payroll = "
      UPDATE `payroll_items` SET `status`=:status WHERE `id`=:id    
      ";
        
      $statement = $connect->prepare($query_payroll);
      $statement->execute($data_payroll);
    }
  
    //-----------------Loan Details------------------------//

    for ($k = 0; $k < count($loan_id); $k++) {  

      $data_loan = array(
        ':id'     =>  $loan_id[$k],
        ':status' =>  1,
      );

      $query_loan = "
      UPDATE `loan_schedules` SET `status`=:status WHERE `id`=:id    
      ";
        
      $statement = $connect->prepare($query_loan);
      $statement->execute($data_loan);
    }

    //-----------------Ration Details------------------------//

    for ($l = 0; $l < count($ration_id); $l++) {  

      $data_ration = array(
        ':id'     =>  $ration_id[$l],
        ':status' =>  1,
      );

      $query_ration = "
      UPDATE `ration_deduction` SET `status`=:status WHERE `id`=:id    
      ";
        
      $statement = $connect->prepare($query_ration);
      $statement->execute($data_ration);
    }

    //-----------------Deduction Details------------------------//

    for ($n = 0; $n < count($emp_deduction_id); $n++) {  

      $data_ded = array(
        ':id'     =>  $emp_deduction_id[$n],
        ':status' =>  1,
      );

      $query_ded = "
      UPDATE `employee_deductions` SET `status`=:status WHERE `id`=:id    
      ";
        
      $statement = $connect->prepare($query_ded);
      $statement->execute($data_ded);
    }




	}
 echo 'done';
 


?>