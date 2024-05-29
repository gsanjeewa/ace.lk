
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
		<!-- <link rel="stylesheet" href="/dist/css/adminlte.min.css"> -->
		<!-- <link rel="stylesheet" href="/dist/css/custom.css"> -->
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
        width: 210mm;
        min-height: 297mm;
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
        size: portrait;        
        margin: 0;
    }
    @media print {
        html, body {
            width: 210mm;
            height: 297mm;

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
    .row {
	  display: -ms-flexbox;
	  display: flex;
	  -ms-flex-wrap: wrap;
	  flex-wrap: wrap;
	  margin-right: -7.5px;
	  margin-left: -7.5px;
	}
    div{
    	border-color: red;
    }
	</style>
	</head>
<body>
	
<div class="book">
	<div class="page">
		

	<?php
	$statement = $connect->prepare('SELECT * FROM address WHERE status=0 ORDER BY id DESC LIMIT 1');
          	$statement->execute();
          	$result = $statement->fetchAll();
          	foreach($result as $row_address)
          	{ 
          	}

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
    	
    	<div class="row" style="max-height: 20px;min-height: 20px;">
	    	<div style="float: left; width: 100%; text-align: left; line-height: 0%;">
	        	
	        	<h3 style="text-align: center; font-weight: bold; text-decoration: underline;font-size: 16px;">ආහාර සඳහා මුදල් අය කිරීම හා මුදල් ගෙවීම</h3>
	        </div>
        </div>

        <div class="row" style="max-height: 40px;min-height: 40px;">	    		
              <div style="width: 30%; float: left;">
            	<p style="font-size: 16px;">මාසය: <?php echo $month; ?></p>            	
              </div>
              <div style="width: 70%; float: right;">
            	<p style="font-size: 16px;">ස්ථානය: <?php echo $row_dep['department_name'].' - '.$row_dep['department_location']; ?></p>            	
              </div>              
          </div>         	
                
        <div class="row" style="max-height: 650px;min-height: 650px;">
        	<div style="width: 100%; float: left;">
		        <?php
		    	
		    	$query = '
		    	SELECT e.surname, e.initial, t.position_abbreviation, j.employee_no 
		    	FROM salary_advance a 		    	
		    	INNER JOIN join_status j ON a.employee_id = j.join_id
				INNER JOIN employee e ON j.employee_id = e.employee_id
				INNER JOIN promotions c ON j.join_id=c.employee_id
				INNER JOIN position t ON c.position_id=t.position_id
				INNER JOIN (SELECT employee_id, MAX(id) maxid_pro FROM promotions GROUP BY employee_id) d ON c.employee_id = d.employee_id AND c.id = d.maxid_pro
		    	WHERE a.date_effective ="'.$effective_date2.'" AND a.status=1 AND a.department_id="'.$_POST['ins_id'].'" ORDER BY j.employee_no ASC';
		        
		        $statement = $connect->prepare($query);
		        $statement->execute();
		        $total_data = $statement->rowCount();
		        $result = $statement->fetchAll();
				
		        ?>

		        <table class="table" style="border: 1px solid black; border-collapse:collapse;">
					<thead>
						<tr style="text-align:center; font-weight: bold;">
							<th style="border: 1px solid black; width: 5%;">#</th>
							<th style="border: 1px solid black; width: 10%;">අංකය</th>
							<th style="border: 1px solid black; width: 5%;">නිලය</th>
							<th style="border: 1px solid black; width: 40%;">නම</th>
							<th style="border: 1px solid black; width: 15%;">සේවය කරන ස්ථානය</th>
							<th style="border: 1px solid black; width: 10%;">ආහාර සඳහා වු මුදල</th>
							<th style="border: 1px solid black; width: 15%;">සේවකයාගේ අත්සන</th>
						</tr>
					</thead>
					<tbody>
		        <?php
		        if ($total_data > 0) {
			        $startpoint =0;
			        $sno = $startpoint + 1;

			        foreach($result as $row)
			        {
			        	?>		
					
						<tr style="height: 15px;">
							<td style="border: 1px solid black;"><center><?php echo $sno; ?></center></td>
							<td style="border: 1px solid black;"><?php echo $row['employee_no'];?></td>
							<td style="border: 1px solid black;"><?php echo $row['position_abbreviation'];?></td>
							<td style="border: 1px solid black;"><?php echo $row['surname'].' '.$row['initial'];?></td>
							<td style="border: 1px solid black;"></td>
							<td style="border: 1px solid black;"></td>
							<td style="border: 1px solid black;"></td>
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
					<td style="border: 1px solid black;"><center><?php echo $sno; ?></center></td>
					<td style="border: 1px solid black;"></td>
					<td style="border: 1px solid black;"></td>
					<td style="border: 1px solid black;"></td>
					<td style="border: 1px solid black;"></td>
					<td style="border: 1px solid black;"></td>
					<td style="border: 1px solid black;"></td>						
				</tr>

				<tr>
					<td style="border: 1px solid black;"><center><?php echo $sno+1; ?></center></td>
					<td style="border: 1px solid black;"></td>
					<td style="border: 1px solid black;"></td>
					<td style="border: 1px solid black;"></td>
					<td style="border: 1px solid black;"></td>
					<td style="border: 1px solid black;"></td>
					<td style="border: 1px solid black;"></td>						
				</tr>

				<tr>
					<td style="border: 1px solid black;"><center><?php echo $sno+2; ?></center></td>
					<td style="border: 1px solid black;"></td>
					<td style="border: 1px solid black;"></td>
					<td style="border: 1px solid black;"></td>
					<td style="border: 1px solid black;"></td>
					<td style="border: 1px solid black;"></td>
					<td style="border: 1px solid black;"></td>						
				</tr>

				<tr>
					<td style="border: 1px solid black;"><center><?php echo $sno+3; ?></center></td>
					<td style="border: 1px solid black;"></td>
					<td style="border: 1px solid black;"></td>
					<td style="border: 1px solid black;"></td>
					<td style="border: 1px solid black;"></td>
					<td style="border: 1px solid black;"></td>
					<td style="border: 1px solid black;"></td>						
				</tr>

				<tr>
					<td style="border: 1px solid black;"><center><?php echo $sno+4; ?></center></td>
					<td style="border: 1px solid black;"></td>
					<td style="border: 1px solid black;"></td>
					<td style="border: 1px solid black;"></td>
					<td style="border: 1px solid black;"></td>
					<td style="border: 1px solid black;"></td>
					<td style="border: 1px solid black;"></td>						
				</tr>

				</tbody>
				<tfoot>
					<tr>
						<th colspan="5" style="text-align:right;">
							ආහාර සඳහා මුළු මුදල
						</th>
						<th></th>
						<th></th>
					</tr>
					<tr>
						<th colspan="5" style="text-align:right;">
							අත්තිකාරම් සඳහා ගෙවූ මුදල
						</th>
						<th></th>
						<th></th>
					</tr>
					<tr>
						<th colspan="5" style="text-align:right;">
							ගෙවීමට ඇති ඉතිරි මුදල
						</th>
						<th></th>
						<th></th>
					</tr>
				</tfoot>
				</table>
			</div>
		<?php
		}
		?>
	</div>
	
	<div class="row" style="max-height: 60px;min-height: 60px;">
		<div style="width: 100%; text-align: right;line-height: 3px; float: left; ">
		</div>
		<div style="width: 100%; text-align: right;line-height:15px; float: right; ">
	    		<table style="border: 1px solid black; border-collapse:collapse; width: 100%;" >

	    			<tr style="height: 15px;">
	    				<td style="border: 1px solid black; width: 50px;">ආහාර සඳහා මුළු මුදල</td>
	    				<td style="border: 1px solid black; width: 50px;"></td>	    				
	    			</tr>
	    			<tr>
	    				<td style="border: 1px solid black; width: 50px;">අත්තිකාරම් සඳහා ගෙවූ මුදල</td>
	    				<td style="border: 1px solid black; width: 50px;"></td>
	    				
	    			</tr>
	    			<tr>
	    				<td style="border: 1px solid black; width: 50px;">ගෙවීමට ඇති ඉතිරි මුදල</td>
	    				<td style="border: 1px solid black; width: 50px;"></td>
	    				
	    			</tr>
	    		</table>
	    	</div>
	</div>
	<div class="row">
		<h3 style="text-align: left; font-weight: bold; text-decoration: underline;font-size: 16px;">ආහාර සැපයුම් කරුගේ විස්තර</h3>
	</div>
	<div class="row" style="max-height: 120px;min-height: 120px;">
		
		<div style="width: 100%; text-align: left;line-height: 15px; float: left; ">
		<table class="table" style="border: 1px solid black; border-collapse:collapse;">
			<thead>
				<tr>
					<th style="border: 1px solid black; width: 40%;">නම</th>
					<th style="border: 1px solid black; width: 60%;"></th>
				</tr>
				<tr>
					<th style="border: 1px solid black; width: 40%;">බැංකු ගිණුම් අංකය</th>
					<th style="border: 1px solid black; width: 60%;"></th>
				</tr>
				<tr>
					<th style="border: 1px solid black; width: 40%;">බැංකු ශාඛාව</th>
					<th style="border: 1px solid black; width: 60%;"></th>
				</tr>

				<tr>
					<th style="border: 1px solid black; width: 40%;">දුරකථන අංකය</th>
					<th style="border: 1px solid black; width: 60%;"> </th>
				</tr>

				<tr>
					<th style="border: 1px solid black; width: 40%;">අත්සන</th>
					<th style="border: 1px solid black; width: 60%;"></th>
				</tr>

			</thead>
			<tbody>
			</tbody>
		</table>
				</div>
	</div>
		<div class="row" style="max-height: 60px;min-height: 60px;">
	    	<div style="width: 33%; text-align: center;line-height: 3px; float: left; ">
	    		<p>------------------------------</p>
	    		<p>ප්‍රධාන ආරක්ෂක නිළධාරී/</p>
	    		<p>ස්ථාන භාර නිළධාරි</p>
	    		<p></p>
	    	</div>
	    	<div style="width: 33%; text-align: center;line-height: 3px; float: center; ">
	    		<p>------------------------------</p>
	    		<p>සංචාරක නිලධාරී</p>
	    		<p></p>
	    	</div>
	    	<div style="width: 34%; text-align: center; center;line-height: 3px; float: right;">
	    		<p>------------------------------</p>
	    		<p>ප්‍රදේශිය නිළධාරිගේ</p>
	    		<p></p>
	    	</div>
	    </div>
		<div class="row" style="max-height: 10px;min-height: 10px;">
			
				<p style="max-height: 40px;min-height: 40px;">(දැනට සකස් කරන ලද ආහාර ලේඛණ තව දුරටත් ගොනු කර තබා ගැනීමට කටයුතු කරන්න)</p>
	    	
	    	
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