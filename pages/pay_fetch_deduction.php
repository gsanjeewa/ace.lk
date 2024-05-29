<?php

include "config.php";
$connect = pdoConnection();
$payid = $_POST['payid'];

$query = 'SELECT * FROM payroll_items WHERE id="'.$payid.'"';

$statement = $connect->prepare($query);
$statement->execute();
$total_data = $statement->rowCount();

$result = $statement->fetchAll();
$html = '<div><ul class="list-group">';
foreach($result as $row)
{	

	if (!empty($row['absent_amount'])) {
		$html .= "<li class='d-flex justify-content-between align-items-center'>Absent (".$row['absent_day']."):<span class='badge badge-primary badge-pill'></span><span>".number_format($row['absent_amount'],2)."</span></li>";
	}

    if (!empty($row['employee_epf'])) {
		$html .= "<li class='d-flex justify-content-between align-items-center'>EPF 8%:<span class='badge badge-primary badge-pill'></span><span>".number_format($row['employee_epf'],2)."</span></li>";
	}
    
	if (!empty($row['loan_amount'])) {
		$html .= "<li class='d-flex justify-content-between align-items-center'>Loan:<span class='badge badge-primary badge-pill'></span><span>".number_format($row['loan_amount'],2)."</span></li>";
	}

	if (!empty($row['advance_amount'])) {
		$html .= "<li class='d-flex justify-content-between align-items-center'>Advance:<span class='badge badge-primary badge-pill'></span><span>".number_format($row['advance_amount'],2)."</span></li>";
	}

	if (!empty($row['ration_amount'])) {
		$html .= "<li class='d-flex justify-content-between align-items-center'>Ration:<span class='badge badge-primary badge-pill'></span><span>".number_format($row['ration_amount'],2)."</span></li>";
	}

	if (!empty($row['inventory_amount'])) {
		$html .= "<li class='d-flex justify-content-between align-items-center'>Uniforms:<span class='badge badge-primary badge-pill'></span><span>".number_format($row['inventory_amount'],2)."</span></li>";
	}

	if (!empty($row['death_donation'])) {
		$html .= "<li class='d-flex justify-content-between align-items-center'>Death:<span class='badge badge-primary badge-pill'></span><span>".number_format($row['death_donation'],2)."</span></li>";
	}

	if (!empty($row['hostel'])) {
		$html .= "<li class='d-flex justify-content-between align-items-center'>Hostel:<span class='badge badge-primary badge-pill'></span><span>".number_format($row['hostel'],2)."</span></li>";
	}

	if (!empty($row['fines'])) {
		$html .= "<li class='d-flex justify-content-between align-items-center'>Fines:<span class='badge badge-primary badge-pill'></span><span>".number_format($row['fines'],2)."</span></li>";
	}

	if (!empty($row['pending_deductions'])) {
		$html .= "<li class='d-flex justify-content-between align-items-center'>Pending Deductions:<span class='badge badge-primary badge-pill'></span><span>".number_format($row['pending_deductions'],2)."</span></li>";
	}

	$query = 'SELECT * FROM deduction';
    $statement = $connect->prepare($query);
    $statement->execute();
    $total_data = $statement->rowCount();
    $result = $statement->fetchAll();
    foreach($result as $rows){
        $all_arr[$rows['deduction_id']] = $rows['deduction_en'];
    }
    
    foreach(json_decode($row['deductions']) as $k => $val){
    	$html .= "<li class='d-flex justify-content-between align-items-center'>".$all_arr[$val->did].":<span class='badge badge-primary badge-pill'></span><span>".number_format($val->amount,2)."</span></li>";
    }    

}

$html .= '</ul></div>';

echo $html;