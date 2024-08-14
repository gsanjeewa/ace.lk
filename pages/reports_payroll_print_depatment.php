<?php
session_start(); 
require 'config.php';
$connect = pdoConnection();
require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 92) == "false") {
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}

$statement = $connect->prepare('SELECT * FROM address WHERE status=0 ORDER BY id DESC LIMIT 1');
$statement->execute();
$result = $statement->fetchAll();
foreach($result as $row_address) { 
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="/dist/css/custom.css">
    <style>	
		 body {
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
        background-color: #FAFAFA;
        font: 16pt "Times New Roman";
    }
    * {
        box-sizing: border-box;
        -moz-box-sizing: border-box;
    }
    .rotated-text{
    	-webkit-transform:rotate(-90deg);
    	-ms-transform:rotate(-90deg);
    	transform:rotate(-90deg);
    	height: 50px;
    	text-align: right;
    }
    .page {
        width: 14in;
        min-height: 8.5in;
        padding: 10mm;
        margin: 10mm auto;
        border: 5px #000000 solid;
        border-radius: 5px;
        background: white;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }
    .subpage {
        padding: 1cm;
        border: 2px #000000 solid;
        height: 257mm;
        outline: 2cm #FFEAEA solid;
    }

    table, td, th{
    	border: 2px #000000 solid;
    	border-collapse: collapse;
    }
    
    @page {
        size: 14in 8.5in;
        margin: 0;
    }
    @media print {
        html, body {
            width: 14in;
            height: 8.5in;        
        }
        .page {
            margin: 0;
            border: initial;
            border-radius: initial;
            width: initial;
            min-height: initial;
            box-shadow: initial;
            background: initial;
            page-break-after: always;
        }

		table {
			page-break-inside: auto;
		}
		tr {
			page-break-inside: avoid;
			page-break-after: auto;
		}
		thead {
			display: table-header-group;
		}
		tfoot {
			display: table-footer-group;
		}
    }
	</style>
</head>
<body>
    
<div class="book">
    <div class="page">
        <div class="center">
        <h2 style="text-align: left; font-weight: bold; text-decoration: underline;"><?php echo $row_address['name_eng']; ?></h2>
        <h4 style="text-align: left; font-weight: bold; text-decoration: underline;"><?php echo $row_address['address_eng']; ?></h4>
        </div>
    <?php
    if(isset($_POST['effective_date'])) {
        $effective_date = date("Y-m-d", strtotime($_POST['effective_date']));
        echo 'Month of :'.date("F Y", strtotime($_POST['effective_date']));
        
        $query = 'SELECT * FROM payroll_items a INNER JOIN payroll b ON a.payroll_id = b.id WHERE b.date_from="'.$effective_date.'" AND (a.status=1 OR a.status=3)';
        $query .= ' ORDER BY a.employee_no ASC, a.department_id ASC';
        
        $statement = $connect->prepare($query);
        $statement->execute();
        $total_data = $statement->rowCount();
        $result = $statement->fetchAll();

        $totals = [
            'basic_salary' => 0,
            'basic_epf' => 0,
            'ot_amount' => 0,
            'incentive' => 0,
            'sot_amount' => 0,
            'service_allowance' => 0,
            'rewards' => 0,
            'chairman_allowance' => 0,
            'training_be' => 0,
            'pending_payments' => 0,
            'gross' => 0,
            'employee_epf' => 0,
            'absent_amount' => 0,
            'advance_amount' => 0,
            'inventory_amount' => 0,
            'ration_amount' => 0,
            'hostel' => 0,
            'fines' => 0,
            'death_donation' => 0,
            'pending_deductions' => 0,
            'deduction_amount' => 0,
            'net' => 0,
            'employer_epf' => 0,
            'employer_etf' => 0,
        ];
    ?>
        <table class="table table-sm">
            <thead>
                <tr style="text-align:center;">
                    <th>#</th>                        
                    <th>EMP No</th>
                    <th>Name</th>
                    <th>Rank</th>
                    <th>Total Shifts</th>
                    <th>OT Hrs</th>
                    <th>Ex OT Hrs</th>
                    <th>Basic</th>
                    <th>For EPF</th>
                    <th>Over Time</th>
                    <th>Incentive</th>
                    <th>Extra OT</th>
                    <th>Service Allowance</th>
                    <th>Rewards</th>
                    <th>Chairman Allowance</th>
                    <th>Training and Be</th>
                    <th>Pending Payments</th>
                    <th>Gross</th>
                    <th>EPF 8%</th>
                    <th>No Pay Days</th>
                    <th>No Pay</th>
                    <th>Salary Advance</th>
                    <th>Uniforms</th>
                    <th>Ration</th>
                    <th>Hostel</th>
                    <th>Fines</th>
                    <th>Death Donation</th>
                    <th>Pending Deductions</th>
                    <th>Total Deductions</th>
                    <th>Net</th>
                    <th>EPF 12%</th>
                    <th>ETF 3%</th>    
                </tr>
            </thead>
            <tbody>
        <?php
        if ($total_data > 0) {
            $sno = 1;
            foreach($result as $row) {
                $query = 'SELECT surname, initial FROM employee WHERE employee_id="'.$row['employee_id'].'"';
                $statement = $connect->prepare($query);
                $statement->execute();
                $result = $statement->fetchAll();
                foreach($result as $employee_name) { 
                }
                $query = 'SELECT position_abbreviation FROM position WHERE position_id="'.$row['position_id'].'"';
                $statement = $connect->prepare($query);
                $statement->execute();
                $total_position = $statement->rowCount();
                $result = $statement->fetchAll();
                if ($total_position > 0) {
                    foreach($result as $position_name) { 
                        $position_id = $position_name['position_abbreviation'];
                    }
                } else {
                    $position_id ='';
                }

                // Sum up the totals
                $totals['basic_salary'] += $row['basic_salary'];
                $totals['basic_epf'] += $row['basic_epf'];
                $totals['ot_amount'] += $row['ot_amount'];
                $totals['incentive'] += $row['incentive'];
                $totals['sot_amount'] += $row['sot_amount'];
                $totals['service_allowance'] += $row['service_allowance'];
                $totals['rewards'] += $row['rewards'];
                $totals['chairman_allowance'] += $row['chairman_allowance'];
                $totals['training_be'] += $row['training_be'];
                $totals['pending_payments'] += $row['pending_payments'];
                $totals['gross'] += $row['gross'];
                $totals['employee_epf'] += $row['employee_epf'];
                $totals['absent_amount'] += $row['absent_amount'];
                $totals['advance_amount'] += $row['advance_amount'];
                $totals['inventory_amount'] += $row['inventory_amount'];
                $totals['ration_amount'] += $row['ration_amount'];
                $totals['hostel'] += $row['hostel'];
                $totals['fines'] += $row['fines'];
                $totals['death_donation'] += $row['death_donation'];
                $totals['pending_deductions'] += $row['pending_deductions'];
                $totals['deduction_amount'] += $row['deduction_amount'];
                $totals['net'] += $row['net'];
                $totals['employer_epf'] += $row['employer_epf'];
                $totals['employer_etf'] += $row['employer_etf'];

                echo '<tr>';
                echo '<td>'.$sno.'</td>';
                echo '<td>'.$row['employee_no'].'</td>';
                echo '<td>'.$employee_name['surname'].' '.$employee_name['initial'].'</td>';
                echo '<td>'.$position_id.'</td>';
                echo '<td>'.$row['no_of_shift'].'</td>';
                echo '<td>'.$row['ot_hrs'].'</td>';
                echo '<td>'.$row['sot_hrs'].'</td>';
                echo '<td>'.number_format($row['basic_salary'],2).'</td>';
                echo '<td>'.number_format($row['basic_epf'],2).'</td>';
                echo '<td>'.number_format($row['ot_amount'],2).'</td>';
                echo '<td>'.number_format($row['incentive'],2).'</td>';
                echo '<td>'.number_format($row['sot_amount'],2).'</td>';
                echo '<td>'.number_format($row['service_allowance'],2).'</td>';
                echo '<td>'.number_format($row['rewards'],2).'</td>';
                echo '<td>'.number_format($row['chairman_allowance'],2).'</td>';
                echo '<td>'.number_format($row['training_be'],2).'</td>';
                echo '<td>'.number_format($row['pending_payments'],2).'</td>';
                echo '<td>'.number_format($row['gross'],2).'</td>';
                echo '<td>'.number_format($row['employee_epf'],2).'</td>';
                echo '<td>'.$row['absent_day'].'</td>';
                echo '<td>'.number_format($row['absent_amount'],2).'</td>';
                echo '<td>'.number_format($row['advance_amount'],2).'</td>';
                echo '<td>'.number_format($row['inventory_amount'],2).'</td>';
                echo '<td>'.number_format($row['ration_amount'],2).'</td>';
                echo '<td>'.number_format($row['hostel'],2).'</td>';
                echo '<td>'.number_format($row['fines'],2).'</td>';
                echo '<td>'.number_format($row['death_donation'],2).'</td>';
                echo '<td>'.number_format($row['pending_deductions'],2).'</td>';
                echo '<td>'.number_format($row['deduction_amount'],2).'</td>';
                echo '<td>'.number_format($row['net'],2).'</td>';
                echo '<td>'.number_format($row['employer_epf'],2).'</td>';
                echo '<td>'.number_format($row['employer_etf'],2).'</td>';
                echo '</tr>';

                $sno++;
            }
        } 
        ?>
            </tbody>
            <tfoot>
                <tr style="font-weight:bold;">
                    <td colspan="7" style="text-align:right;">Totals:</td>
                    <td><?php echo number_format($totals['basic_salary'], 2); ?></td>
                    <td><?php echo number_format($totals['basic_epf'], 2); ?></td>
                    <td><?php echo number_format($totals['ot_amount'], 2); ?></td>
                    <td><?php echo number_format($totals['incentive'], 2); ?></td>
                    <td><?php echo number_format($totals['sot_amount'], 2); ?></td>
                    <td><?php echo number_format($totals['service_allowance'], 2); ?></td>
                    <td><?php echo number_format($totals['rewards'], 2); ?></td>
                    <td><?php echo number_format($totals['chairman_allowance'], 2); ?></td>
                    <td><?php echo number_format($totals['training_be'], 2); ?></td>
                    <td><?php echo number_format($totals['pending_payments'], 2); ?></td>
                    <td><?php echo number_format($totals['gross'], 2); ?></td>
                    <td><?php echo number_format($totals['employee_epf'], 2); ?></td>
                    <td></td>
                    <td><?php echo number_format($totals['absent_amount'], 2); ?></td>
                    <td><?php echo number_format($totals['advance_amount'], 2); ?></td>
                    <td><?php echo number_format($totals['inventory_amount'], 2); ?></td>
                    <td><?php echo number_format($totals['ration_amount'], 2); ?></td>
                    <td><?php echo number_format($totals['hostel'], 2); ?></td>
                    <td><?php echo number_format($totals['fines'], 2); ?></td>
                    <td><?php echo number_format($totals['death_donation'], 2); ?></td>
                    <td><?php echo number_format($totals['pending_deductions'], 2); ?></td>
                    <td><?php echo number_format($totals['deduction_amount'], 2); ?></td>
                    <td><?php echo number_format($totals['net'], 2); ?></td>
                    <td><?php echo number_format($totals['employer_epf'], 2); ?></td>
                    <td><?php echo number_format($totals['employer_etf'], 2); ?></td>
                </tr>
            </tfoot>
        </table>
    <?php } ?>
    </div>
</div>
</body>
<script type="text/javascript">
	function PrintPage() {
		window.print();
	}
	document.loaded = function(){
		
	}
	window.addEventListener('DOMContentLoaded', (event) => {
   		PrintPage()
		setTimeout(function(){ window.close() },750)
	});
</script>
</html>
