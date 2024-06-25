
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

?>

<!DOCTYPE html>

<html lang="en">
	<head>
		<meta charset="UTF-8">
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
    .page {
        width: 210mm;
        min-height: 297mm;
        max-height: 297mm;
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
        size: A4;
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
	</style>
	</head>
<body>
	<div class="book">
    <!-- <div class="page">
        <div class="subpage">Page 1/3</div>    
    </div>
    <div class="page">
        <div class="subpage">Page 2/3</div>    
    </div>

    <div class="page">
        <div class="subpage">Page 3/3</div>    
    </div> -->
    <?php
	if(isset($_GET['print'])) 
    {
    	$query = 'SELECT * FROM resignation WHERE id="'.$_GET['print'].'"';
        
        $statement = $connect->prepare($query);
        $statement->execute();
        $total_data = $statement->rowCount();
        if ($total_data > 0) {
                
        $result = $statement->fetchAll();
        foreach($result as $row)
        {

        	$query = 'SELECT e.initial, e.surname, p.position_abbreviation, j.employee_no, j.join_id FROM employee e 
        	INNER JOIN join_status j ON e.employee_id = j.employee_id 
        	INNER JOIN promotions c ON j.join_id=c.employee_id 
        	INNER JOIN (SELECT employee_id, MAX(id) maxid_pro FROM promotions GROUP BY employee_id) d ON c.employee_id = d.employee_id AND c.id = d.maxid_pro 
        	INNER JOIN position p ON c.position_id=p.position_id 
        	WHERE j.join_id="'.$row['employee_id'].'"';
          	$statement = $connect->prepare($query);
          	$statement->execute();
          	$result = $statement->fetchAll();
          	foreach($result as $employee_name)
          	{ 
          	}	        

		      $statement = $connect->prepare("SELECT a.account_no, b.bank_name, c.branch_no, c.branch_name FROM bank_details a INNER JOIN bank_name b ON a.bank_name=b.id INNER JOIN bank_branch c ON a.branch_name=c.id WHERE a.id='".$row['advance_deduction']."'");
              $statement->execute();
              $total_bank = $statement->rowCount();
              $result = $statement->fetchAll();
              	if ($total_bank > 0) :
	              	foreach($result as $row_bank):
	              		$bank_name = $row_bank['bank_name'];
	        			$account_no=str_pad($row_bank['account_no'], 12, "0", STR_PAD_LEFT);
	        			$branch_no=str_pad($row_bank['branch_no'], 3, "0", STR_PAD_LEFT);
	        			$branch_name=$row_bank['branch_name'];
	              	endforeach;
          		else:
          			$bank_name = '';
        			$account_no = '';
        			$branch_no='';
	        		$branch_name='';
          		endif;	

          		$query = 'SELECT department_name, department_location FROM payroll_items a INNER JOIN department b ON a.department_id=b.department_id WHERE a.employee_id="'.$row['employee_id'].'" ORDER BY id DESC LIMIT 1';
          	$statement = $connect->prepare($query);
          	$statement->execute();
          	$result = $statement->fetchAll();
          	foreach($result as $row_department)
          	{ 
          	}	     

			$statement = $connect->prepare('SELECT * FROM address WHERE status=0 ORDER BY id DESC LIMIT 1');
          	$statement->execute();
          	$result = $statement->fetchAll();
          	foreach($result as $row_address)
          	{ 
          	}
          	   	
    	?>

	    <div class="page">
	    	<div class="row">	    		
              <div style="width: 100%; ">
              	<p>-----------------------------------------</p>
              	<p>-----------------------------------------</p>
              	<p>-----------------------------------------</p>
              	<p>-----------------------------------------</p>
              	
            	<p><?php echo $row_address['name_sin']; ?><br><?php echo $row_address['address_sin']; ?></p>
            	<h4 style="text-decoration: underline; text-align: center;"><b>නිශ්කාෂන පත්‍රිකාව</b></h4>  			 			
              </div>              
          </div> 

          <div class="row">	    		
              <div style="width: 100%; ">
            	<p>නම <?php echo $row['employee_no'].' '.$employee_name['surname'].' '.$employee_name['initial']?> ස්ථානය <?php echo $row_department['department_name'].' '.$row_department['department_location']; ?> මෙම  ආයතනයේ සේවාය කර ඔබ දැනට සේවයෙන් ඉල්ලා අස්වීම / නිනොනො සිටින අයෙකු බව තිරණය කර ඇත. ඒ අනුව ඔබගේ නිශ්කාෂන සහතිකය පහත සඳහන් පරිදි වේ</p>
              </div>              
          </div>     
	    		    	
	    	<div class="row">
	    	<div style="width: 100%;">
	    		<table>
	    			<tr >
	    				<th width="70%" align="center">විස්තරය</th>
	    				<th width="30%"align="center">මුදල</th>
	    			</tr>
	    			<tr>
	    				
	                    <td>අත්හිටවන ලද වැටුප</td>	                    
	                    <td><?php echo number_format($row['last_month_pay'],2); ?></td>
	                </tr>
	                <tr>
	                	<?php
	                   foreach(json_decode($row['loan_deduction']) as $k => $val):
	                  ?>
	                <tr> 
                        <td><?php echo $val->did; ?></td>
                        
                        <td align="right"><?php echo number_format($val->amount, 2)?></td> 
                    </tr>
					<?php
	                endforeach;
	                  ?>
	                </tr>
	                <tr>
	                    <td >අඩුකිරීම් වල එකතුව</td>
	                    
	                    <td  align="right"><b><?php echo number_format($row['total_deduction'],2);?></b></td>                               
	                </tr>
					
					<tr>
	                	<?php
	                   foreach(json_decode($row['ration_deduction']) as $t => $val):
	                  ?>
	                <tr> 
                        <td ><?php echo $val->reson; ?></td>
                        
                        <td  align="right"><?php echo number_format($val->amount, 2)?></td> 
                    </tr>
					<?php
	                endforeach;
	                  ?>
	                </tr>
	                
	                <tr>
	                <td >ගෙවිමට ඇති මුදල</td>
	                
	                    <td ><b><?php echo number_format($row['net_amount'],2)?></b></td>
	                </tr>	                
	    		</table>    		
			</div>
			
		</div>
		
		<br>
			<div class="row">
		    
		    <div style="text-align: justify; height: 100px;">
		    	<p>නිවැරදි බවට</p>

		    </div>

		    </div>

		    <div class="row">
		    	<div style="width: 50%; text-align: center;line-height: 3px; ">
		    		<p>------------------------------</p>
		    		<p>ආරක්‍ෂක නිලධාරි / නිළධාරීනීගේ අත්සන</p>
		    		<p></p>
		    	</div>
		    	<div style="width: 50%; text-align: center; center;line-height: 25px;">
		    		<p>------------------------------</p>
		    		<p>සහතික කරන නිලධාරිගේ අත්සන <br>කලාප භාර නිලධාරි <br>(නිලමුද්‍රාව තබන්න)</p>
		    	</div>
		    </div>


		</div>


		<?php
			}
		}
		else{
			?>
			<div class="page">
				Data not found!
			</div>
			<?php
			
		}
    }

    ?>

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