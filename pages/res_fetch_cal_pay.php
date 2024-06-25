<?php
error_reporting(0);
include('config.php');
$connect = pdoConnection();

if(isset($_POST["query"]) && $_POST['query'] != '') {
    $output = array();
    $data = array();
    $employee_id = $_POST["query"];
    
    // Function to execute and fetch results from the database
    function fetch_sum($connect, $query, $employee_id) {
        $statement = $connect->prepare($query);
        $statement->execute([':employee_id' => $employee_id]);
        return $statement->fetchColumn();
    }

    // Fetch last payroll
    $last_payroll = fetch_sum($connect, "SELECT COALESCE(sum(net), '0') AS total FROM payroll_items WHERE employee_id = :employee_id AND status = 2", $employee_id);

    // Fetch loan deductions
    $loan = fetch_sum($connect, "SELECT COALESCE(sum(paid_amount), '0') AS total FROM loan_schedules WHERE employee_id = :employee_id AND status = 0", $employee_id);
    $deduction = !empty($loan) ? "<li class='d-flex justify-content-between align-items-center'>Loan:<span class='badge badge-primary badge-pill'></span><span>".number_format($loan, 2)."</span></li>" : "";

    // Fetch salary advance deductions
    $advance = fetch_sum($connect, "SELECT COALESCE(sum(amount), '0') AS total FROM salary_advance WHERE employee_id = :employee_id AND status = 2", $employee_id);
    $deduction .= !empty($advance) ? "<li class='d-flex justify-content-between align-items-center'>Advance:<span class='badge badge-primary badge-pill'></span><span>".number_format($advance, 2)."</span></li>" : "";

    // Fetch inventory deductions
    $equipment = fetch_sum($connect, "SELECT COALESCE(sum(amount), '0') AS total FROM inventory_deduction WHERE employee_id = :employee_id AND status = 0", $employee_id);
    $deduction .= !empty($equipment) ? "<li class='d-flex justify-content-between align-items-center'>Uniforms:<span class='badge badge-primary badge-pill'></span><span>".number_format($equipment, 2)."</span></li>" : "";

    // Fetch ration deductions
    $ration = fetch_sum($connect, "SELECT COALESCE(sum(amount), '0') AS total FROM ration_deduction WHERE employee_id = :employee_id AND status = 0", $employee_id);
    $deduction .= !empty($ration) ? "<li class='d-flex justify-content-between align-items-center'>Ration:<span class='badge badge-primary badge-pill'></span><span>".number_format($ration, 2)."</span></li>" : "";

    // Fetch other employee deductions
    $statement = $connect->prepare("SELECT COALESCE(sum(a.amount), '0') AS total, b.deduction_en FROM employee_deductions a INNER JOIN deduction b ON a.deduction_id = b.deduction_id WHERE a.employee_id = :employee_id AND (a.status = 0 OR a.status = 2) AND a.deduction_id != 4 GROUP BY a.deduction_id");
    $statement->execute([':employee_id' => $employee_id]);
    $emp_deductions = $statement->fetchAll();

    $emp_deduction = array();
    foreach ($emp_deductions as $deductions) {
        $emp_deduction[] = $deductions['total'];
        if (!empty($deductions['total'])) {
            $deduction .= "<li class='d-flex justify-content-between align-items-center'>".$deductions['deduction_en'].":<span class='badge badge-primary badge-pill'></span><span>".number_format($deductions['total'], 2)."</span></li>";
        }
    }

    $total_deduction = $loan + $advance + $equipment + $ration + array_sum($emp_deduction);
    $net = $last_payroll - $total_deduction;

    $output[] = array(
        'last_payroll'    =>  $last_payroll,
        'gross'           =>  $last_payroll,
        'ded_details'     =>  $deduction,
        'total_deduction' =>  $total_deduction,
        'net'             =>  number_format($net, 2),
    );

    echo json_encode($output);
}
?>
