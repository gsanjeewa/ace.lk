<?php

include "config.php";
$connect = pdoConnection();

if (isset($_POST['effective_date']) && $_POST['effective_date'] != '') {
    $effective_date = date("Y-m-d", strtotime($_POST['effective_date']));
    
    // Fetch all department details
    $query = "
        SELECT c.department_name, c.department_location, a.department_id
        FROM reports_department a
        INNER JOIN payroll b ON a.payroll_id = b.id
        INNER JOIN department c ON a.department_id = c.department_id
        WHERE b.date_from = :effective_date
        GROUP BY a.department_id
        ORDER BY c.department_name ASC
    ";

    $statement = $connect->prepare($query);
    $statement->execute([':effective_date' => $effective_date]);
    $departments = $statement->fetchAll(PDO::FETCH_ASSOC);

    $data = [];
    $sno = 1;

    foreach ($departments as $dept) {
        $department_id = $dept['department_id'];

        // Fetch position details for each department
        $query_positions = "
            SELECT a.position_id, a.invoice_rate, a.to_be_applied_shift, a.working_shift, a.working_rate
            FROM reports_department a
            INNER JOIN payroll b ON a.payroll_id = b.id
            WHERE b.date_from = :effective_date AND a.department_id = :department_id
        ";

        $statement = $connect->prepare($query_positions);
        $statement->execute([':effective_date' => $effective_date, ':department_id' => $department_id]);
        $positions = $statement->fetchAll(PDO::FETCH_ASSOC);

        // Initialize variables for each position with default values
        $positions_data = array_fill(1, 8, [
            'to_be' => 0, 'working' => 0, 'shortfall' => 0, 'invoice' => 0, 'shortfall_amount' => 0,
            'actual_invoice' => 0, 'invoice_amount' => 0, 'working_rate' => 0
        ]);

        foreach ($positions as $pos) {
            $position_id = $pos['position_id'];
            $to_be = $pos['to_be_applied_shift'] ?: 0;
            $working = $pos['working_shift'] ?: 0;
            $invoice_rate = $pos['invoice_rate'] ?: 0;
            $working_rate = $pos['working_rate'] ?: 0;
            $actual_invoice = $to_be * $invoice_rate;
            $invoice_amount = $working * $invoice_rate;
            $shortfall = $to_be - $working;

            $positions_data[$position_id] = [
                'to_be' => $to_be,
                'working' => $working,
                'shortfall' => $shortfall,
                'invoice' => $shortfall > 0 ? $invoice_rate : 0,
                'actual_invoice' => $actual_invoice,
                'invoice_amount' => $invoice_amount,
                'shortfall_amount' => $shortfall > 0 ? $shortfall * $invoice_rate : 0,
                'working_rate' => $working_rate,
            ];
        }

        $total_actual_invoice = array_sum(array_column($positions_data, 'actual_invoice'));
        $total_invoice_amount = array_sum(array_column($positions_data, 'invoice_amount'));
        $total_shortfall_amount = array_sum(array_column($positions_data, 'shortfall_amount'));

        $data[] = [
            $sno,
            $dept['department_name'] . '-' . $dept['department_location'],
            $positions_data[1]['to_be'],
            $positions_data[2]['to_be'],
            $positions_data[3]['to_be'],
            $positions_data[4]['to_be'],
            $positions_data[5]['to_be'],
            $positions_data[6]['to_be'],
            $positions_data[7]['to_be'],
            $positions_data[8]['to_be'],
            $positions_data[1]['working'],
            $positions_data[2]['working'],
            $positions_data[3]['working'],
            $positions_data[4]['working'],
            $positions_data[5]['working'],
            $positions_data[6]['working'],
            $positions_data[7]['working'],
            $positions_data[8]['working'],
            $positions_data[1]['shortfall'],
            $positions_data[2]['shortfall'],
            $positions_data[3]['shortfall'],
            $positions_data[4]['shortfall'],
            $positions_data[5]['shortfall'],
            $positions_data[6]['shortfall'],
            $positions_data[7]['shortfall'],
            $positions_data[8]['shortfall'],
            $positions_data[1]['invoice'],
            $positions_data[2]['invoice'],
            $positions_data[3]['invoice'],
            $positions_data[4]['invoice'],
            $positions_data[5]['invoice'],
            $positions_data[6]['invoice'],
            $positions_data[7]['invoice'],
            $positions_data[8]['invoice'],
            number_format($total_actual_invoice, 2),
            number_format($total_invoice_amount, 2),
            number_format($total_shortfall_amount, 2),
            $positions_data[1]['working_rate'],
            $positions_data[2]['working_rate'],
            $positions_data[3]['working_rate'],
            $positions_data[4]['working_rate'],
            $positions_data[5]['working_rate'],
            $positions_data[6]['working_rate'],
            $positions_data[7]['working_rate'],
            $positions_data[8]['working_rate'],
        ];

        $sno++;
    }

    $output = ['data' => $data];
    echo json_encode($output);
}
?>
