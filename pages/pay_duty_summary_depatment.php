
<?php
session_start(); 
	require 'config.php';
	include '../inc/timezone.php'; 
	$connect = pdoConnection();
	require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 92) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}

?>

<!DOCTYPE html>

<html lang="en">
	<head>
		<meta charset="UTF-8">
		<!-- <link rel="stylesheet" href="/dist/css/adminlte.min.css">
		<link rel="stylesheet" href="/dist/css/custom.css"> -->
		<style>	
		 body {
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
        background-color: #FAFAFA;
        font: 10pt "Times New Roman";

    }
    * {
        box-sizing: border-box;
        -moz-box-sizing: border-box;
    }
    .rotated-text{
    	-webkit-transform:rotate(-90deg);
    	-ms-transform:rotate(-90deg);
    	transform:rotate(-90deg);
    	text-align: center;    		
    }

    .leftbox {
            float: left;            
            width: 25%;
            bottom: 0;            
        }
 
        .middlebox {
            float: left;           
            width: 50%;            
            text-align: center;
        }
 
        .rightbox {
            float: right;           
            width: 25%;            
        }

    .page {
        width: 297mm;
        min-height: 210mm;
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
        size: landscape;        
        margin: 0;
    }
    @media print {
        html, body {
            width: 297mm;
            height: 210mm;

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
		

	<?php
	if((isset($_POST['effective_date'])) && (isset($_POST['ins_id'])))
    {
    	$effective_date = date("Y-m-d", strtotime($_POST['effective_date']));
    	$effective_date2 = date("Y-m-d", strtotime($_POST['effective_date']." -1 month"));
    	$end_date = date("Y-m-d", strtotime($effective_date."first day of next month"));
    	$year=date("Y", strtotime($_POST['effective_date']));
    	$month=date("F", strtotime($_POST['effective_date']));
    	$statement = $connect->prepare('SELECT * FROM department WHERE department_id="'.$_POST['ins_id'].'"');
        $statement->execute();
        $total_data = $statement->rowCount();
        $result = $statement->fetchAll();
        foreach($result as $row_dep)
        {

        }
    	?>
    	<div>
	    	<div style="width: 100%; text-align: center; line-height: 0%;">
	        	<h3 style="text-align: center; font-weight: bold; text-decoration: underline;">ACE FRONT LINE SECURITY SOLUTIONS (PVT) LTD</h3>
	        	<h3 style="text-align: center; font-weight: bold; text-decoration: underline;">DUTY SUMMARY</h3>
	        </div>
        </div>
        <div >
        	<div style="float: left; width: 50%; line-height: 1%;">
        		<p>Month: <?php echo $month; ?></p>
        		<p>Year: <?php echo $year; ?></p>
        	</div>
        	<div style="float: right; width: 50%; line-height: 1%;">
        		<p>Location Code:</p>
        		<p>Location: <?php echo $row_dep['department_name'].' - '.$row_dep['department_location']; ?></p>
        	</div>
        </div>        	
        
        <!-- <h4 style="text-align: center; font-weight: bold; text-decoration: underline;"><?php echo $year; ?></h4> -->
        <div class="row">
        	<div style="width: 100%;">
		        <?php
		    	
		    	$query = '
		    	SELECT c.employee_id, a.position_id, a.employee_no 
		    	FROM payroll_items a 
		    	INNER JOIN payroll b ON a.payroll_id = b.id 
		    	INNER JOIN join_status c ON a.employee_id=c.join_id
		    	INNER JOIN position d ON a.position_id=d.position_id
		    	WHERE b.date_from ="'.$effective_date2.'" AND (a.status=1 OR a.status=3) AND a.department_id="'.$_POST['ins_id'].'" ORDER BY d.priority ASC, a.employee_no ASC';
		        
		        $statement = $connect->prepare($query);
		        $statement->execute();
		        $total_data = $statement->rowCount();
		        $result = $statement->fetchAll();		        
				     
		        ?>

		        <table class="table" style="border: 2px solid black; border-collapse:collapse;">
					<thead>
						<tr style="text-align:center; font-weight: bold;">
							<th style="border: 2px solid black; width: 15px;">#</th>
							<th style="border: 2px solid black;">SVC NO</th>
							<th style="border: 2px solid black;">RANK</th>
							<th style="border: 2px solid black;">NAME</th>
							<th style="border: 2px solid black;"></th>
							<?php
							// Step 1: Setting the Start and End Dates
							$start_date = date_create($effective_date);
							$end_date = date_create($end_date);

							// Step 2: Defining the Date Interval
							$interval = new DateInterval('P1D');

							// Step 3: Creating the Date Range
							$date_range = new DatePeriod($start_date, $interval, $end_date);

							// Step 4: Looping Through the Date Range
							foreach ($date_range as $date) {
								?>
								<th style="border: 2px solid black;"><?php  echo $date->format('d'); ?></th>
								<?php				   
							}
							?>
							<th style="border: 2px solid black;">SHIFTS</th>
							<th style="border: 2px solid black;">TOTAL SHIFTS</th>
							<?php 							
					        
					        $statement = $connect->prepare('
					    	SELECT b.position_abbreviation FROM position_pay a 
					    	INNER JOIN position b ON a.position_id = b.position_id 
					    	WHERE a.department_id="'.$_POST['ins_id'].'" ORDER BY b.position_id ASC');
					        $statement->execute();					        
					        $result_position = $statement->fetchAll();
					        foreach($result_position as $row_position)
		        			{
		        				?>
								<th style="border: 2px solid black;"><?php  echo $row_position['position_abbreviation']; ?></th>
								<?php
		        			}
					        ?>							
							<th style="border: 2px solid black;">OT TOT</th>
						</tr>
					</thead>
					<tbody>
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
						?>		
					
						<tr style="height: 15px;">
							<td rowspan="3" style="border: 2px solid black;"><center><?php echo $sno; ?></center></td>
							<td rowspan="3" style="border: 2px solid black;"><?php echo $row['employee_no'];?></td>
							<td rowspan="3" style="border: 2px solid black;"><?php echo $position_id;?></td>
							<td rowspan="3" style="border: 2px solid black;"><?php echo $employee_name['surname'].' '.$employee_name['initial'];?></td>
							<td style="border: 2px solid black;">D</td>
							<?php
							foreach ($date_range as $date) {
								?>
								<td style="border: 2px solid black;"></td>
								<?php				   
							}
							?>
							<td style="border: 2px solid black;"></td>
							<td rowspan="2" style="border: 2px solid black;"></td>
							<?php
							foreach ($result_position as $row_position) {
								?>
								<td rowspan="3" style="border: 2px solid black;"></td>
								<?php				   
							}
							?>
							<td rowspan="3" style="border: 2px solid black;"></td>
						</tr>							
						<tr>
							<td style="border: 2px solid black;">N</td>
							<?php
							foreach ($date_range as $date) {
								?>
								<td style="border: 2px solid black;"></td>
								<?php				   
							}
							?>
							<td style="border: 2px solid black;"></td>
						</tr>
						<tr>
							<td style="border: 2px solid black;">OT</td>
							<?php
							foreach ($date_range as $date) {
								?>
								<td style="border: 2px solid black;"></td>
								<?php				   
							}
							?>
							<td style="border: 2px solid black;"></td>
							<td style="border: 2px solid black;"></td>
						</tr>
		
					<?php
				$sno ++;
				}

				}else{
					$startpoint=0;
					$sno = $startpoint + 1;
				}

				?>
				<tr>
							<td rowspan="3" style="border: 2px solid black;"><center><?php echo $sno; ?></center></td>
							<td rowspan="3" style="border: 2px solid black;"></td>
							<td rowspan="3" style="border: 2px solid black;"></td>
							<td rowspan="3" style="border: 2px solid black;"></td>
							<td style="border: 2px solid black;">D</td>
							<?php
							foreach ($date_range as $date) {
								?>
								<td style="border: 2px solid black;"></td>
								<?php				   
							}
							?>
							<td style="border: 2px solid black;"></td>
							<td rowspan="2" style="border: 2px solid black;"></td>
							<?php
							foreach ($result_position as $row_position) {
								?>
								<td rowspan="3" style="border: 2px solid black;"></td>
								<?php				   
							}
							?>
							<td rowspan="3" style="border: 2px solid black;"></td>
						</tr>							
						<tr>
							<td style="border: 2px solid black;">N</td>
							<?php
							foreach ($date_range as $date) {
								?>
								<td style="border: 2px solid black;"></td>
								<?php				   
							}
							?>
							<td style="border: 2px solid black;"></td>
						</tr>
						<tr>
							<td style="border: 2px solid black;">OT</td>
							<?php
							foreach ($date_range as $date) {
								?>
								<td style="border: 2px solid black;"></td>
								<?php				   
							}
							?>
							<td style="border: 2px solid black;"></td>
							<td style="border: 2px solid black;"></td>
						</tr>
						<tr>
							<td rowspan="3" style="border: 2px solid black;"><center><?php echo $sno+1; ?></center></td>
							<td rowspan="3" style="border: 2px solid black;"></td>
							<td rowspan="3" style="border: 2px solid black;"></td>
							<td rowspan="3" style="border: 2px solid black;"></td>
							<td style="border: 2px solid black;">D</td>
							<?php
							foreach ($date_range as $date) {
								?>
								<td style="border: 2px solid black;"></td>
								<?php				   
							}
							?>
							<td style="border: 2px solid black;"></td>
							<td rowspan="2" style="border: 2px solid black;"></td>
							<?php
							foreach ($result_position as $row_position) {
								?>
								<td rowspan="3" style="border: 2px solid black;"></td>
								<?php				   
							}
							?>
							<td rowspan="3" style="border: 2px solid black;"></td>
						</tr>							
						<tr>
							<td style="border: 2px solid black;">N</td>
							<?php
							foreach ($date_range as $date) {
								?>
								<td style="border: 2px solid black;"></td>
								<?php				   
							}
							?>
							<td style="border: 2px solid black;"></td>
						</tr>
						<tr>
							<td style="border: 2px solid black;">OT</td>
							<?php
							foreach ($date_range as $date) {
								?>
								<td style="border: 2px solid black;"></td>
								<?php				   
							}
							?>
							<td style="border: 2px solid black;"></td>
							<td style="border: 2px solid black;"></td>
						</tr>
						<tr>
							<td rowspan="3" style="border: 2px solid black;"><center><?php echo $sno+2; ?></center></td>
							<td rowspan="3" style="border: 2px solid black;"></td>
							<td rowspan="3" style="border: 2px solid black;"></td>
							<td rowspan="3" style="border: 2px solid black;"></td>
							<td style="border: 2px solid black;">D</td>
							<?php
							foreach ($date_range as $date) {
								?>
								<td style="border: 2px solid black;"></td>
								<?php				   
							}
							?>
							<td style="border: 2px solid black;"></td>
							<td rowspan="2" style="border: 2px solid black;"></td>
							<?php
							foreach ($result_position as $row_position) {
								?>
								<td rowspan="3" style="border: 2px solid black;"></td>
								<?php				   
							}
							?>
							<td rowspan="3" style="border: 2px solid black;"></td>
						</tr>							
						<tr>
							<td style="border: 2px solid black;">N</td>
							<?php
							foreach ($date_range as $date) {
								?>
								<td style="border: 2px solid black;"></td>
								<?php				   
							}
							?>
							<td style="border: 2px solid black;"></td>
						</tr>
						<tr>
							<td style="border: 2px solid black;">OT</td>
							<?php
							foreach ($date_range as $date) {
								?>
								<td style="border: 2px solid black;"></td>
								<?php				   
							}
							?>
							<td style="border: 2px solid black;"></td>
							<td style="border: 2px solid black;"></td>
						</tr>
						<tr>
							<td rowspan="3" style="border: 2px solid black;"><center><?php echo $sno+3; ?></center></td>
							<td rowspan="3" style="border: 2px solid black;"></td>
							<td rowspan="3" style="border: 2px solid black;"></td>
							<td rowspan="3" style="border: 2px solid black;"></td>
							<td style="border: 2px solid black;">D</td>
							<?php
							foreach ($date_range as $date) {
								?>
								<td style="border: 2px solid black;"></td>
								<?php				   
							}
							?>
							<td style="border: 2px solid black;"></td>
							<td rowspan="2" style="border: 2px solid black;"></td>
							<?php
							foreach ($result_position as $row_position) {
								?>
								<td rowspan="3" style="border: 2px solid black;"></td>
								<?php				   
							}
							?>
							<td rowspan="3" style="border: 2px solid black;"></td>
						</tr>							
						<tr>
							<td style="border: 2px solid black;">N</td>
							<?php
							foreach ($date_range as $date) {
								?>
								<td style="border: 2px solid black;"></td>
								<?php				   
							}
							?>
							<td style="border: 2px solid black;"></td>
						</tr>
						<tr>
							<td style="border: 2px solid black;">OT</td>
							<?php
							foreach ($date_range as $date) {
								?>
								<td style="border: 2px solid black;"></td>
								<?php				   
							}
							?>
							<td style="border: 2px solid black;"></td>
							<td style="border: 2px solid black;"></td>
						</tr>
						<tr>
							<td rowspan="3" style="border: 2px solid black;"><center><?php echo $sno+4; ?></center></td>
							<td rowspan="3" style="border: 2px solid black;"></td>
							<td rowspan="3" style="border: 2px solid black;"></td>
							<td rowspan="3" style="border: 2px solid black;"></td>
							<td style="border: 2px solid black;">D</td>
							<?php
							foreach ($date_range as $date) {
								?>
								<td style="border: 2px solid black;"></td>
								<?php				   
							}
							?>
							<td style="border: 2px solid black;"></td>
							<td rowspan="2" style="border: 2px solid black;"></td>
							<?php
							foreach ($result_position as $row_position) {
								?>
								<td rowspan="3" style="border: 2px solid black;"></td>
								<?php				   
							}
							?>
							<td rowspan="3" style="border: 2px solid black;"></td>
						</tr>							
						<tr>
							<td style="border: 2px solid black;">N</td>
							<?php
							foreach ($date_range as $date) {
								?>
								<td style="border: 2px solid black;"></td>
								<?php				   
							}
							?>
							<td style="border: 2px solid black;"></td>
						</tr>
						<tr>
							<td style="border: 2px solid black;">OT</td>
							<?php
							foreach ($date_range as $date) {
								?>
								<td style="border: 2px solid black;"></td>
								<?php				   
							}
							?>
							<td style="border: 2px solid black;"></td>
							<td style="border: 2px solid black;"></td>
						</tr>
				<tr>
					<td colspan="5" style="border: 2px solid black;">PER DAY SHIFTS</td>
							<?php
							foreach ($date_range as $date) {
								?>
								<td style="border: 2px solid black;"></td>
								<?php				   
							}
							?>
							<td style="border: 2px solid black;"></td>
							<td rowspan="2" style="border: 2px solid black;"></td>
							<?php
							foreach ($result_position as $row_position) {
								?>
								<td rowspan="2" style="border: 2px solid black;"></td>
								<?php				   
							}
							?>
							<td rowspan="2" style="border: 2px solid black;"></td>
						</tr>
						<tr>
							<td colspan="5" style="border: 2px solid black;">TOTAL SHIFTS PER MONTH</td>
							<?php
							foreach ($date_range as $date) {
								?>
								<td style="border: 2px solid black;"></td>
								<?php				   
							}
							?>
							<td style="border: 2px solid black;"></td>							
						</tr>

				</tbody>
				</table>
			</div>
		<?php
		}
		?>
	</div>
	
	<br>
	<div class="row">
		<div class="leftbox">
			<p>OFFICER INCHARGE:.....................................</p>
		</div>

		<div class="middlebox">
			<p>SENIOR SECURITY OFICER:.....................................</p>
		</div>

		<div class="rightbox">
			<p>VISITING OFFICER:.....................................</p>
		</div>
		
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