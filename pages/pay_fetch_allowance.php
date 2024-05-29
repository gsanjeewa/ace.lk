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


    if (!empty($row['sot_amount'])) {
        $html .= "<li class='d-flex justify-content-between align-items-center'>Extra OT Hrs (".$row['sot_hrs']."):<span class='badge badge-primary badge-pill'></span><span>".number_format($row['sot_amount'],2)."</span></li>";
    }

    if (!empty($row['service_allowance'])) {
        $html .= "<li class='d-flex justify-content-between align-items-center'>Service Allowance :<span class='badge badge-primary badge-pill'></span><span>".number_format($row['service_allowance'],2)."</span></li>";
    }

    if (!empty($row['rewards'])) {
        $html .= "<li class='d-flex justify-content-between align-items-center'>Rewards :<span class='badge badge-primary badge-pill'></span><span>".number_format($row['rewards'],2)."</span></li>";
    }

    if (!empty($row['chairman_allowance'])) {
        $html .= "<li class='d-flex justify-content-between align-items-center'>Chairman Allowance :<span class='badge badge-primary badge-pill'></span><span>".number_format($row['chairman_allowance'],2)."</span></li>";
    }

    if (!empty($row['training_be'])) {
        $html .= "<li class='d-flex justify-content-between align-items-center'>Training be :<span class='badge badge-primary badge-pill'></span><span>".number_format($row['training_be'],2)."</span></li>";
    }

    if (!empty($row['pending_payments'])) {
        $html .= "<li class='d-flex justify-content-between align-items-center'>Pending Payments :<span class='badge badge-primary badge-pill'></span><span>".number_format($row['pending_payments'],2)."</span></li>";
    }

	$query = 'SELECT * FROM allowances';

    $statement = $connect->prepare($query);
    $statement->execute();
    $total_data = $statement->rowCount();
    $result = $statement->fetchAll();
    foreach($result as $rows){
        $all_arr[$rows['allowances_id']] = $rows['allowances_en'];
    }
    
    foreach(json_decode($row['allowances']) as $k => $val){
    	$html .= "<li class='d-flex justify-content-between align-items-center'>".$all_arr[$val->aid].":<span class='badge badge-primary badge-pill'></span><span>".number_format($val->amount,2)."</span></li>";
    }

    if (!empty($row['poya_days'])) {
        $html .= "<li class='d-flex justify-content-between align-items-center'>Poya Day (".$row['poya_days']."):<span class='badge badge-primary badge-pill'></span><span>".number_format($row['poya_day_payment'],2)."</span></li>";
    }

    if (!empty($row['m_days'])) {
        $html .= "<li class='d-flex justify-content-between align-items-center'>Mercantile Day (".$row['m_days']."):<span class='badge badge-primary badge-pill'></span><span>".number_format($row['m_payment'],2)."</span></li>";
    }

    if (!empty($row['m_ot_hrs'])) {
        $html .= "<li class='d-flex justify-content-between align-items-center'>Mercantile OT Hrs (".$row['m_ot_hrs']."):<span class='badge badge-primary badge-pill'></span><span>".number_format($row['m_ot_payment'],2)."</span></li>";
    }
}

$html .= '</ul></div>';

echo $html;