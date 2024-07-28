
<?php
session_start(); 
	require 'config.php';
	$connect = pdoConnection();
	require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 92) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}
$statement = $connect->prepare('SELECT * FROM address WHERE status=0 ORDER BY id DESC LIMIT 1');
$statement->execute();
$result = $statement->fetchAll();
foreach($result as $row_address)
{ 
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
	if(isset($_POST['effective_date']))
    {
    	$effective_date = date("Y-m-d", strtotime($_POST['effective_date']));

    	echo 'Month of :'.date("F Y", strtotime($_POST['effective_date']));

    	if ((isset($_POST['filter_institution'])) && (!empty($_POST['filter_institution'])))  {
    		
    	
    	$statement = $connect->prepare('SELECT * FROM department WHERE department_id="'.$_POST['filter_institution'].'"');
        $statement->execute();
        $total_data = $statement->rowCount();
        $result = $statement->fetchAll();
        foreach($result as $row_dep)
        {
        	?>
        	<h5 style="text-align: left; font-weight: bold; text-decoration: underline;"><?php echo $row_dep['department_name'].' - '.$row_dep['department_location']; ?></h5>
        	<?php
        }
    }
    	
    	$query = 'SELECT * FROM payroll_items a INNER JOIN payroll b ON a.payroll_id = b.id WHERE b.date_from="'.$effective_date.'" AND (a.status=1 OR a.status=3)';
    	
    	if ((isset($_POST['filter_institution'])) && (!empty($_POST['filter_institution'])))  {
    		$query .= ' AND a.department_id="'.$_POST['filter_institution'].'"';
    	}

    	$query .= ' ORDER BY a.employee_no ASC, a.department_id ASC';
        
        $statement = $connect->prepare($query);
        $statement->execute();
        $total_data = $statement->rowCount();
        $result = $statement->fetchAll();

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
        <?php
        if ($total_data > 0) {
                
        
        $startpoint =0;
        $sno = $startpoint + 1;
        foreach($result as $row)
        {
        	$query = 'SELECT surname, initial FROM employee WHERE employee_id="'.$row['employee_id'].'"';
          	$statement = $connect->prepare($query);
          	$statement->execute();
          	$result = $statement->fetchAll();
          	foreach($result as $employee_name)
          	{ 
          	}
	        $query = 'SELECT position_abbreviation FROM position WHERE position_id="'.$row['position_id'].'"';
		    $statement = $connect->prepare($query);
		    $statement->execute();
		    $total_position = $statement->rowCount();
		    $result = $statement->fetchAll();
		    if ($total_position > 0) {
		      foreach($result as $position_name)
		      { 
		        $position_id = $position_name['position_abbreviation'];
		      }
		      }else{
		        $position_id ='';
		      }

		      /*$total_deduction=(string)$row['employee_epf']+(string)$row['absent_amount']+(string)$row['advance_amount']+(string)$row['inventory_amount']+(string)$row['ration_amount']+(string)$row['hostel']+(string)$row['fines']+(string)$row['death_donation']+(string)$row['pending_deductions'];*/

		      $query = 'SELECT sum(no_of_shift) AS total_shift, sum(gross) AS total_gross, sum(employee_epf) AS total_employee_epf, sum(deduction_amount) AS total_deduction_amount, sum(net) AS total_net, sum(employer_epf) AS total_employer_epf, sum(employer_etf) AS total_employer_etf FROM payroll_items a INNER JOIN payroll b ON a.payroll_id = b.id INNER JOIN (SELECT c.employee_id, c.department_id FROM attendance c INNER JOIN (SELECT employee_id, MAX(no_of_shifts) maxid FROM attendance GROUP BY employee_id) d ON c.employee_id = d.employee_id AND c.no_of_shifts = d.maxid) e ON a.employee_id=e.employee_id WHERE b.date_from="'.$effective_date.'" AND (a.status=1 OR a.status=3)';

		      if ((isset($_POST['filter_institution'])) && (!empty($_POST['filter_institution'])))  {
    		$query .= ' AND e.department_id="'.$_POST['filter_institution'].'"';
    	}

          	$statement = $connect->prepare($query);
          	$statement->execute();
          	$result = $statement->fetchAll();
          	foreach($result as $total)
          	{ 
          	}

		?>
	
		<tbody>
			<tr>
				<td><center><?php echo $sno; ?></center></td>
				<td><?php if ($row['employee_no'] !=0) : echo $row['employee_no']; else: ; endif;?></td>
				<td><?php echo $employee_name['surname'].' '.$employee_name['initial'];?></td>
				<td><center><?php echo $position_id;?></center></td>
				<td><center><?php if ($row['no_of_shift'] !='') : echo $row['no_of_shift']; else: ; endif;?></td>
				<td><center><?php if ($row['ot_hrs'] !=0) : echo $row['ot_hrs']; else: ; endif;?></center></td>
				<td><center><?php if ($row['sot_hrs'] !=0) : echo $row['sot_hrs']; else: ; endif;?></center></td>
				<td style="text-align: right;"><?php if ($row['basic_salary'] !=0) : echo number_format($row['basic_salary'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['basic_epf'] !=0) : echo number_format($row['basic_epf'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['ot_amount'] !=0) : echo number_format($row['ot_amount'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['incentive'] !=0) : echo number_format($row['incentive'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['sot_amount'] !=0) : echo number_format($row['sot_amount'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['service_allowance'] !=0) : echo number_format($row['service_allowance'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['rewards'] !=0) : echo number_format($row['rewards'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['chairman_allowance'] !=0) : echo number_format($row['chairman_allowance'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['training_be'] !=0) : echo number_format($row['training_be'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['pending_payments'] !=0) : echo number_format($row['pending_payments'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['gross'] !=0) : echo number_format($row['gross'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['employee_epf'] !=0) : echo number_format($row['employee_epf'],2); else: ; endif;?></td>
				<td style="text-align: center;"><?php if ($row['absent_day'] !=0) : echo $row['absent_day']; else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['absent_amount'] !=0) : echo number_format($row['absent_amount'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['advance_amount'] !=0) : echo number_format($row['advance_amount'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['inventory_amount'] !=0) : echo number_format($row['inventory_amount'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['ration_amount'] !=0) : echo number_format($row['ration_amount'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['hostel'] !=0) : echo number_format($row['hostel'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['fines'] !=0) : echo number_format($row['fines'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['death_donation'] !=0) : echo number_format($row['death_donation'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['pending_deductions'] !=0) : echo number_format($row['pending_deductions'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['deduction_amount'] !=0) : echo number_format($row['deduction_amount'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['net'] !=0) : echo number_format($row['net'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['employer_epf'] !=0) : echo number_format($row['employer_epf'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['employer_etf'] !=0) : echo number_format($row['employer_etf'],2); else: ; endif;?></td>
			</tr>
		
		
	
	<?php
$sno ++;
}
?>

	<tr>
		<th colspan="4" style="text-align: center;">Total</th>
		<th style="text-align: center;"><?php if ($total['total_shift'] !=0) : echo $total['total_shift']; else: ; endif;?></th>
		<th colspan="12"></th>
		<th style="text-align: right;"><?php if ($total['total_gross'] !=0) : echo number_format($total['total_gross'],2); else: ; endif;?></th>
		<th style="text-align: right;"><?php if ($total['total_employee_epf'] !=0) : echo number_format($total['total_employee_epf'],2); else: ; endif;?></th>
		<th colspan="9"></th>
		<th style="text-align: right;"><?php if ($total['total_deduction_amount'] !=0) : echo number_format($total['total_deduction_amount'],2); else: ; endif;?></th>
		<th style="text-align: right;"><?php if ($total['total_net'] !=0) : echo number_format($total['total_net'],2); else: ; endif;?></th>
		<th style="text-align: right;"><?php if ($total['total_employer_epf'] !=0) : echo number_format($total['total_employer_epf'],2); else: ; endif;?></th>
		<th style="text-align: right;"><?php if ($total['total_employer_etf'] !=0) : echo number_format($total['total_employer_etf'],2); else: ; endif;?></th>
	</tr>
	
</tbody>
<?php
} 


?>
</table>
<?php
}
	?>
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