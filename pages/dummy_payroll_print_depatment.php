
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
	if(isset($_POST['effective_date'], $_POST['filter_institution']) && $_POST['effective_date'] != '' && $_POST['filter_institution'] != '')
    {
    	$effective_date = date("Y-m-d", strtotime($_POST['effective_date']));

    	echo 'Month of :'.date("F Y", strtotime($_POST['effective_date']));
    	?>
    	<br>
    	<?php
    	$query_department = 'SELECT department_name, department_location FROM department
    	WHERE department_id="'.$_POST['filter_institution'].'"';
    	         
        $statement = $connect->prepare($query_department);
        $statement->execute();
        $total_data = $statement->rowCount();
        $result = $statement->fetchAll();
        foreach($result as $row_department):
        	echo $row_department['department_name'].' - '.$row_department['department_location'];
        endforeach;
 
        // $statement = $connect->prepare('SELECT * FROM d_half_days
    	// WHERE department_id="'.$_POST['filter_institution'].'"');
        // $statement->execute();
        // if ($statement->rowCount()>0):

		// endif;
        

    	$query = 'SELECT * FROM d_payroll_items a 
    	INNER JOIN d_payroll b ON a.payroll_id = b.id 
    	WHERE b.date_from="'.$effective_date.'" AND a.department="'.$_POST['filter_institution'].'" AND a.status=0 ORDER BY a.employee_no ASC';
    	         
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
			        <th>Basic Salary</th>
			        <th>BRA Allowance I</th>
			        <th>BRA Allowance II</th>
			        <th>Total Working Days</th>
			        <th>Normal Working Days</th>
			        <th>Normal OT Hrs</th>
			        <th>Normal Day Earning</th>
			        <th>Paid Leave Days</th>
			        <th>Payment for leave days</th>
			        <th>Over Time x (1.5)</th>
			        
			        	<th>Half Days</th>
				        <th>Half day OT hrs</th>
				       
			        	<th>Poya Days</th>
				        <th>Mercantile Days</th>
				        <th>Mercantile OT Hrs</th>				        
				        <th>Poya Day Payment</th>
			        	<th>Mercantile Payment</th>
			        	<th>Over Time x (3)</th>			        	
			        	
			        	<th>Poya Days</th>
				        <th>Mercantile Days</th>
				        <th>Mercantile OT Hrs</th>				        
				        <th>Poya Day Payment</th>
			        	<th>Mercantile Payment</th>
			        	<th>Over Time x (3)</th>
			        	<th>Half Days</th>
				        <th>Half day OT hrs</th>
			        	
			        
			        <th>Performance Incentive</th>
			        <th>For EPF</th>
			        <th>Gross Salary</th>
			        <th>Employee EPF (8%)</th>
			        <th>No Pay Days</th>
			        <th>No Pay</th>
			        <th>Salary Advance</th>
			        <th>Ration</th>
			        <th>Hostel</th>
			        <th>Fines</th>
			        <th>Total Deductions</th>
			        <th>Net Salary</th>
			        <th>Employer EPF (12%)</th>
			        <th>Employer ETF (3%)</th>  
				</tr>
			</thead>
        <?php
        if ($total_data > 0) {
                
        
        $startpoint =0;
        $sno = $startpoint + 1;
        foreach($result as $row)
        {
        	$query = 'SELECT * FROM d_payroll WHERE id="'.$row['payroll_id'].'"';
		    $statement = $connect->prepare($query);
		    $statement->execute();
		    $result = $statement->fetchAll();
		    foreach($result as $date_from)
		    { 
		    }

		    $query = 'SELECT a.surname, a.initial FROM employee a INNER JOIN join_status b ON a.employee_id=b.employee_id WHERE b.join_id="'.$row['employee_id'].'"';
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


		    $statement = $connect->prepare("SELECT a.account_no, b.bank_name FROM bank_details a INNER JOIN bank_name b ON a.bank_name=b.id WHERE a.id='".$row['bank_id']."'");
		    $statement->execute();
		    $total_bank = $statement->rowCount();
		    $result = $statement->fetchAll();
		    if ($total_bank > 0) :
		        foreach($result as $row_bank):
		            $bank_name = $row_bank['bank_name'];
		        	$account_no=str_pad($row_bank['account_no'], 12, "0", STR_PAD_LEFT);
		        endforeach;
		    else:
		        $bank_name = '';
		    	$account_no = '';
		    endif;

			$basic_salary1=$row['basic_salary']-3500;

			$brai=number_format(2500,2);
			$braii=number_format(1000,2);

			$query = 'SELECT sum(no_of_shift) AS total_shift, sum(gross) AS total_gross, sum(employee_epf) AS total_employee_epf, sum(total_deductions) AS total_deduction_amount, sum(net_salary) AS total_net, sum(employer_epf) AS total_employer_epf, sum(employer_etf) AS total_employer_etf, sum(salary_advance) AS total_advance, sum(hostel) AS total_hostel, sum(fines) AS total_fines, sum(ration) AS total_ration FROM d_payroll_items a INNER JOIN d_payroll b ON a.payroll_id = b.id WHERE b.date_from="'.$effective_date.'" AND a.department="'.$_POST['filter_institution'].'" AND a.status=0';
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
				<td style="text-align: right;"><?php  echo number_format($basic_salary1,2);?></td>
				<td style="text-align: right;"><?php echo $brai; ?></td>
				<td style="text-align: right;"><?php echo $braii; ?></td>
				<td style="text-align: center;"><?php if ($row['no_of_shift'] !=0) : echo number_format($row['no_of_shift']); else: ; endif;?></td>
				<td style="text-align: center;"><?php if ($row['n_working_days'] !=0) :echo number_format($row['n_working_days']); else:;endif;?></td>				
				<td style="text-align: center;"><?php if ($row['ot_hrs'] !=0) : echo number_format($row['ot_hrs'],2); else:; endif;?></td>
				<td style="text-align: right;"><?php if ($row['n_day_earning'] !=0) : echo number_format($row['n_day_earning'],2); else: ; endif;?></td>		
				<td style="text-align: right;"><?php if ($row['p_leave_days'] !=0) : echo number_format($row['p_leave_days'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['p_leave_day_payment'] !=0) : echo number_format($row['p_leave_day_payment'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['ot_payment'] !=0) : echo number_format($row['ot_payment'],2); else: ; endif;?></td>

				
			        	<td style="text-align: right;"><?php if ($row['h_days'] !=0) : echo number_format($row['h_days'],2); else: ; endif;?></td>
						<td style="text-align: right;"><?php if ($row['h_ot_hrs'] !=0) : echo number_format($row['h_ot_hrs'],2); else: ; endif;?></td>
			        	
			        	<td style="text-align: right;"><?php if ($row['poya_days'] !=0) : echo number_format($row['poya_days'],2); else:;endif;?></td>
						<td style="text-align: right;"><?php if ($row['m_days'] !=0) : echo number_format($row['m_days'],2); else: endif;?></td>
						<td style="text-align: right;"><?php if ($row['m_ot_hrs'] !=0) : echo number_format($row['m_ot_hrs'],2); else: endif;?></td>			
						<td style="text-align: center;"><?php if ($row['poya_day_payment'] !=0) : echo number_format($row['poya_day_payment'],2); else: ; endif;?></td>
						<td style="text-align: right;"><?php if ($row['m_payment'] !=0) : echo number_format($row['m_payment'],2); else: ; endif;?></td>
						<td style="text-align: right;"><?php if ($row['ot_t_payment'] !=0) : echo number_format($row['ot_t_payment'],2); else: ; endif;?></td>
			        	
			        	<td style="text-align: right;"><?php if ($row['poya_days'] !=0) : echo number_format($row['poya_days'],2); else:;endif;?></td>
						<td style="text-align: right;"><?php if ($row['m_days'] !=0) : echo number_format($row['m_days'],2); else: endif;?></td>
						<td style="text-align: right;"><?php if ($row['m_ot_hrs'] !=0) : echo number_format($row['m_ot_hrs'],2); else: endif;?></td>			
						<td style="text-align: center;"><?php if ($row['poya_day_payment'] !=0) : echo number_format($row['poya_day_payment'],2); else: ; endif;?></td>
						<td style="text-align: right;"><?php if ($row['m_payment'] !=0) : echo number_format($row['m_payment'],2); else: ; endif;?></td>
						<td style="text-align: right;"><?php if ($row['ot_t_payment'] !=0) : echo number_format($row['ot_t_payment'],2); else: ; endif;?></td>
						<td style="text-align: right;"><?php if ($row['h_days'] !=0) : echo number_format($row['h_days'],2); else: ; endif;?></td>
						<td style="text-align: right;"><?php if ($row['h_ot_hrs'] !=0) : echo number_format($row['h_ot_hrs'],2); else: ; endif;?></td>
			        	

				<td style="text-align: right;"><?php if ($row['incentive'] !=0) : echo number_format($row['incentive'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['for_epf'] !=0) : echo number_format($row['for_epf'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['gross'] !=0) : echo number_format($row['gross'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['employee_epf'] !=0) : echo number_format($row['employee_epf'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['no_pay_days'] !=0) : echo number_format($row['no_pay_days'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['no_pay'] !=0) : echo number_format($row['no_pay'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['salary_advance'] !=0) : echo number_format($row['salary_advance'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['ration'] !=0) : echo number_format($row['ration'],2); else: ; endif;?></td>				
				<td style="text-align: right;"><?php if ($row['hostel'] !=0) : echo number_format($row['hostel'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['fines'] !=0) : echo number_format($row['fines'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['total_deductions'] !=0) : echo number_format($row['total_deductions'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['net_salary'] !=0) : echo number_format($row['net_salary'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['employer_epf'] !=0) : echo number_format($row['employer_epf'],2); else: ; endif;?></td>
				<td style="text-align: right;"><?php if ($row['employer_etf'] !=0) : echo number_format($row['employer_etf'],2); else: ; endif;?></td>
			</tr>
	<?php
$sno ++;
}
?>
	<tr>
		<th colspan="7" style="text-align: center;">Total</th>
		<th style="text-align: center;"><?php if ($total['total_shift'] !=0) : echo $total['total_shift']; else: ; endif;?></th>
		<th colspan="4"></th>
		<?php 
        if ($row_shifts['shifts']==1):
        	
        elseif ($row_shifts['shifts']==2):
        	?>
        	<th colspan="2"></th>
        	<?php
        elseif ($row_shifts['shifts']==3):
        	?>
        	<th colspan="6"></th>
        	<?php
        elseif ($row_shifts['shifts']==4):
        	?>
        	<th colspan="8"></th>
        	<?php
        elseif ($row_shifts['shifts']==5):
    	
    	endif;
        ?>

		<th colspan="4"></th>
		<th style="text-align: right;"><?php if ($total['total_gross'] !=0) : echo number_format($total['total_gross'],2); else: ; endif;?></th>
		<th style="text-align: right;"><?php if ($total['total_employee_epf'] !=0) : echo number_format($total['total_employee_epf'],2); else: ; endif;?></th>
		<th colspan="2"></th>
		<th style="text-align: right;"><?php if ($total['total_advance'] !=0) : echo number_format($total['total_advance'],2); else: ; endif;?></th>
		<th style="text-align: right;"><?php if ($total['total_ration'] !=0) : echo number_format($total['total_ration'],2); else: ; endif;?></th>
		<th style="text-align: right;"><?php if ($total['total_hostel'] !=0) : echo number_format($total['total_hostel'],2); else: ; endif;?></th>
		<th style="text-align: right;"><?php if ($total['total_fines'] !=0) : echo number_format($total['total_fines'],2); else: ; endif;?></th>
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