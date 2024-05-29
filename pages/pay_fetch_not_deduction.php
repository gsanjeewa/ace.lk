<?php

include "config.php";
$connect = pdoConnection();
$employee_id = $_POST['employee_id'];
$payroll_id = $_POST['payroll_id'];

$query = 'SELECT * FROM payroll WHERE id="'.$payroll_id.'"';

$statement = $connect->prepare($query);
$statement->execute();
$total_data = $statement->rowCount();

$result = $statement->fetchAll();
$html = '<div><ul class="list-group">';
foreach($result as $row)
{	
    $statement = $connect->prepare("SELECT paid_amount FROM loan_schedules WHERE employee_id='".$employee_id."' AND status=2 AND YEAR(date_due)= YEAR('".$row['date_from']."') AND MONTH(date_due) = MONTH('".$row['date_from']."')");
    $statement->execute();
    $total_loan_schedules = $statement->rowCount();
    $result = $statement->fetchAll();
    if ($total_loan_schedules > 0) {
    foreach($result as $loan_deductions){             
        $paid_amount=$loan_deductions['paid_amount'];                              
    }
    }else{
    $paid_amount=0;
    }

    $statement = $connect->prepare("SELECT COALESCE(sum(amount),'0') AS ration_amount FROM ration_deduction WHERE employee_id='".$employee_id."' AND status=2 AND YEAR(date_effective)= YEAR('".$row['date_from']."') AND MONTH(date_effective) = MONTH('".$row['date_from']."')");
    $statement->execute();
    $result = $statement->fetchAll();        
    foreach($result as $ration_deductions){             
    $ration_amount=$ration_deductions['ration_amount'];            
    }

    $statement = $connect->prepare("SELECT COALESCE(sum(amount),'0') AS uniform FROM inventory_deduction WHERE employee_id='".$employee_id."' AND status=2 AND YEAR(due_date)= YEAR('".$row['date_from']."') AND MONTH(due_date) = MONTH('".$row['date_from']."')");
    $statement->execute();
    $result = $statement->fetchAll();
    foreach($result as $inventory_deductions){             
    $inventory_amount=$inventory_deductions['uniform'];            
    } 

	
	if (!empty($paid_amount)) {
		$html .= "<li class='d-flex justify-content-between align-items-center'>Loan:<span class='badge badge-primary badge-pill'></span><span>".number_format($paid_amount,2)."</span></li>";
	}

	if (!empty($ration_amount)) {
		$html .= "<li class='d-flex justify-content-between align-items-center'>Ration:<span class='badge badge-primary badge-pill'></span><span>".number_format($ration_amount,2)."</span></li>";
	}

	if (!empty($inventory_amount)) {
		$html .= "<li class='d-flex justify-content-between align-items-center'>Uniforms:<span class='badge badge-primary badge-pill'></span><span>".number_format($inventory_amount,2)."</span></li>";
	}

	$query = 'SELECT * FROM deduction';
    $statement = $connect->prepare($query);
    $statement->execute();
    $total_data = $statement->rowCount();
    $result = $statement->fetchAll();
    $all_arr = array(); // Initialize $all_arr array
    foreach ($result as $rows) {
        $all_arr[$rows['deduction_id']] = $rows['deduction_en'];
    }

    $deductions = array();
    $statement = $connect->prepare("SELECT * FROM employee_deductions WHERE employee_id=:employee_id AND status = 2 AND YEAR(effective_date)= YEAR(:date_from) AND MONTH(effective_date) = MONTH(:date_from)");
    $statement->bindParam(':employee_id', $employee_id);
    $statement->bindParam(':date_from', $row['date_from']);
    $statement->execute();
    $result = $statement->fetchAll();
    foreach ($result as $row) {
        $deductions[] = array('did' => $row['deduction_id'], "amount" => $row['amount']);
    }


    foreach ($deductions as $k => $val) {
        $html .= "<li class='d-flex justify-content-between align-items-center'>".$all_arr[$val['did']].":<span class='badge badge-primary badge-pill'></span><span>".number_format($val['amount'], 2)."</span></li>";
    } 

}

$html .= '</ul></div>';

echo $html;