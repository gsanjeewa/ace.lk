<?php
include 'config.php';

$connect = pdoConnection();

if (isset($_POST["employee_id"]) && isset($_POST["resignation_date"]) && isset($_POST["doc_chargers"])) {
    $employee_id = $_POST["employee_id"];
    $resignation_date = $_POST["resignation_date"];
    $doc_chargers = $_POST['doc_chargers'];
    $paid_amount = isset($_POST['paid_amount']) ? $_POST['paid_amount'] : 0;
    $reson = isset($_POST['reson']) ? $_POST['reson'] : '';

    // Fetch employee details
    $statement = $connect->prepare("SELECT employee_id, employee_no FROM join_status WHERE join_id = :employee_id");
    $statement->execute([':employee_id' => $employee_id]);
    $employee = $statement->fetch();

    if (!$employee) {
        echo 'Employee not found';
        exit;
    }

    $employee_no = $employee['employee_no'];
    
    // Fetch bank details
    $statement = $connect->prepare("SELECT id FROM bank_details WHERE employee_id = :employee_id AND status = 0 ORDER BY id DESC LIMIT 1");
    $statement->execute([':employee_id' => $employee_id]);
    $bank = $statement->fetch();
    $bank_id = $bank ? $bank['id'] : '';

    // Fetch payroll details
    $statement = $connect->prepare("SELECT net, id, payroll_id FROM payroll_items WHERE employee_id = :employee_id AND status = 2");
    $statement->execute([':employee_id' => $employee_id]);
    $payrolls = $statement->fetchAll();
    
    $month_pay = array_column($payrolls, 'net');
    $month_pay_id = array_column($payrolls, 'id');
    $payroll_id = array_column($payrolls, 'payroll_id')[0];
    $month_sum = array_sum($month_pay);

    // Fetch loan details
    $statement = $connect->prepare("SELECT paid_amount, id, loan_id FROM loan_schedules WHERE employee_id = :employee_id AND status = 0");
    $statement->execute([':employee_id' => $employee_id]);
    $loans = $statement->fetchAll();
    
    $loan_sum = array_sum(array_column($loans, 'paid_amount'));
    $loan_ids = array_column($loans, 'id');

    // Fetch advance details
    $statement = $connect->prepare("SELECT amount, id FROM salary_advance WHERE employee_id = :employee_id AND status = 0");
    $statement->execute([':employee_id' => $employee_id]);
    $advances = $statement->fetchAll();
    
    $advance_sum = array_sum(array_column($advances, 'amount'));
    $advance_ids = array_column($advances, 'id');

    // Fetch inventory details
    $statement = $connect->prepare("SELECT amount, id FROM inventory_deduction WHERE employee_id = :employee_id AND status = 0");
    $statement->execute([':employee_id' => $employee_id]);
    $inventories = $statement->fetchAll();
    
    $inventory_sum = array_sum(array_column($inventories, 'amount'));
    $inventory_ids = array_column($inventories, 'id');

    // Fetch ration details
    $statement = $connect->prepare("SELECT amount, id FROM ration_deduction WHERE employee_id = :employee_id AND status = 0");
    $statement->execute([':employee_id' => $employee_id]);
    $rations = $statement->fetchAll();
    
    $ration_sum = array_sum(array_column($rations, 'amount'));
    $ration_ids = array_column($rations, 'id');

    // Fetch other deductions
    $statement = $connect->prepare("SELECT COALESCE(SUM(a.amount), '0') AS total, b.deduction_en, a.id FROM employee_deductions a INNER JOIN deduction b ON a.deduction_id = b.deduction_id WHERE a.employee_id = :employee_id AND a.status = 0 AND a.deduction_id != 4 GROUP BY a.deduction_id");
    $statement->execute([':employee_id' => $employee_id]);
    $deductions = $statement->fetchAll();
    
    $deduction_sum = array_sum(array_column($deductions, 'total'));
    $deduction_ids = array_column($deductions, 'id');

    // Prepare data for insertion
    $sub_array = [
        ['did' => 'Loan Deduction', "amount" => $loan_sum],
        ['did' => 'Advance Deduction', "amount" => $advance_sum],
        ['did' => 'Uniforms', "amount" => $inventory_sum],
        ['did' => 'Ration', "amount" => $ration_sum],
        ...array_map(fn($ded) => ['did' => $ded['deduction_en'], "amount" => $ded['total']], $deductions),
        ['did' => 'Document Chargers', "amount" => $doc_chargers]
    ];

    $paid_array = [
        ['reson' => $reson, "amount" => $paid_amount]
    ];

    $gross = (float)$month_sum;
    $total_deduction = (float)$loan_sum + (float)$advance_sum + (float)$inventory_sum + (float)$ration_sum + (float)$deduction_sum + (float)$doc_chargers;
    $net = (float)$gross - (float)$total_deduction + (float)$paid_amount;

    // Insert data into resignation table
    $data = [
        ':employee_id' => $employee_id,
        ':employee_no' => $employee_no,
        ':last_month_pay' => $month_sum,
        ':gross_income' => $gross,
        ':advance_deduction' => $advance_ids,
        ':loan_deduction' => json_encode($sub_array),
        ':inventory_deduction' => $payroll_id,
        ':ration_deduction' => json_encode($paid_array),
        ':total_deduction' => $total_deduction,
        ':net_amount' => $net,
        ':resignation_date' => $resignation_date,
        ':status' => 1
    ];

    $query = "
        INSERT INTO resignation (employee_id, employee_no, last_month_pay, gross_income, advance_deduction, loan_deduction, inventory_deduction, ration_deduction, total_deduction, net_amount, status)
        VALUES (:employee_id, :employee_no, :last_month_pay, :gross_income, :advance_deduction, :loan_deduction, :inventory_deduction, :ration_deduction, :total_deduction, :net_amount, :status);
        UPDATE join_status SET employee_status = 3, resignation_date = :resignation_date WHERE join_id = :employee_id;
    ";

    if (!empty($advance_ids)) {
        $query .= "UPDATE salary_advance SET status = 1 WHERE id IN (" . implode(',', $advance_ids) . ");";
    }

    $statement = $connect->prepare($query);
    $statement->execute($data);

    // Update statuses for other records
    $updates = [
        'inventory_deduction' => $inventory_ids,
        'payroll_items' => $month_pay_id,
        'loan_schedules' => $loan_ids,
        'ration_deduction' => $ration_ids,
        'employee_deductions' => $deduction_ids
    ];

    foreach ($updates as $table => $ids) {
        foreach ($ids as $id) {
            $statement = $connect->prepare("UPDATE $table SET status = 1 WHERE id = :id");
            $statement->execute([':id' => $id]);
        }
    }

    echo 'done';
}
?>