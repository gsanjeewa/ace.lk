
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
	if((isset($_POST['effective_date'])) && (isset($_POST['ins_id'])))
    {
    	$effective_date = date("Y-m-d", strtotime($_POST['effective_date']));
    	
    	$query = 'SELECT * FROM d_payroll_items a INNER JOIN d_payroll b ON a.payroll_id = b.id WHERE b.date_from="'.$effective_date.'" AND a.status=0 AND a.department="'.$_POST['ins_id'].'" ORDER BY a.department ASC, a.employee_id ASC';
        
        $statement = $connect->prepare($query);
        $statement->execute();
        $total_data = $statement->rowCount();
        if ($total_data > 0) {
                
        $result = $statement->fetchAll();
        foreach($result as $row)
        {

        	$date_from=$row['date_from'];
			    $date_to=$row['date_to'];

        	   $year=date("Y", strtotime($_POST['effective_date']));

			    if (date("m", strtotime($_POST['effective_date']))=='01'):
			    	$month='ජනවාරි';
				elseif (date("m", strtotime($_POST['effective_date']))=='02'):
					$month='පෙබරවාරි';
				elseif (date("m", strtotime($_POST['effective_date']))=='03'):
					$month='මාර්තු';
				elseif (date("m", strtotime($_POST['effective_date']))=='04'):
					$month='අප්‍රේල්';
				elseif (date("m", strtotime($_POST['effective_date']))=='05'):
					$month='මැයි';
				elseif (date("m", strtotime($_POST['effective_date']))=='06'):
					$month='ජුනි';
				elseif (date("m", strtotime($_POST['effective_date']))=='07'):
					$month='ජුලි';
				elseif (date("m", strtotime($_POST['effective_date']))=='08'):
					$month='ආගෝස්තු';
				elseif (date("m", strtotime($_POST['effective_date']))=='09'):
					$month='සැප්තැම්බර්';
				elseif (date("m", strtotime($_POST['effective_date']))=='10'):
					$month='ඔක්තෝබර්';
				elseif (date("m", strtotime($_POST['effective_date']))=='11'):
					$month='නොවැම්බර්';
				elseif (date("m", strtotime($_POST['effective_date']))=='12'):
					$month='දෙසැම්බර්';
				endif;

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

		      $statement = $connect->prepare("SELECT a.account_no, b.bank_name, c.branch_no, c.branch_name FROM bank_details a INNER JOIN bank_name b ON a.bank_name=b.id INNER JOIN bank_branch c ON a.branch_name=c.id WHERE a.id='".$row['bank_id']."'");
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

          		$query = 'SELECT department_name, department_location FROM department WHERE department_id="'.$row['department'].'"';
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

	    		<div style="width: 20%; text-align: right;">
            
              <img src="/dist/img/logo.png" class="img-responsive" style="display: block;margin-left: auto; margin-right: auto; max-width: 125px; height: auto;" />
			</div>  
              <div style="width: 80%; text-align: center;">
                	<h3><b><?php echo $row_address['name_sin']; ?></b></h3>
    				<h4><?php echo $row_address['address_sin']; ?></h4>
	    			<h4 style="text-decoration: underline; text-align: center;"><b>වැටුප් පත්‍රය</b></h4>
              </div>
              <!-- /.info-box-content -->
                          	    		
	    	</div>
	    	<div class="row">
	    	<div style="width: 50%;">
	    		<table>
	    			<tr>  
	                    <td width="30%">මාසය</td>
	                    <td width="2%" align="center">:</td>
	                    <td width="68%"><b><?php echo $year.' '.$month; ?> මස</b></td>
	                </tr>
	                <tr>
	                    <td width="30%">සාමාජික අංකය</td>
	                    <td width="2%" align="center">:</td>
	                    <td width="68%"><b><?php echo $row['employee_no'] ?></b></td>
	                </tr>
	                <tr>
	                    <td width="30%">නම</td>
	                    <td width="2%" align="center">:</td>
	                    <td width="68%"><b><?php echo $employee_name['surname'].' '.$employee_name['initial']?></b></td>                               
	                </tr>
	                <tr>
	                <td width="30%">නිලය</td>
	                <td width="2%" align="center">:</td>
	                    <td width="68%"><b><?php echo $position_id;?></b></td>
	                </tr>
	                
	                <tr>
	                <td width="30%">මුලු වැඩ මුර</td>
	                <td width="2%" align="center">:</td>
	                    <td width="68%"><b><?php echo $row['no_of_shift']?></b></td>
	                </tr>	                
	    		</table>    		
			</div>
			<div style="width: 50%;">
				<table>
	    			
	                <tr>
	                <td width="30%">ගිණුම් අංකය</td>
	                <td width="2%" align="center">:</td>
	                    <td width="68%"><b><?php echo $account_no;?></b></td>
	                </tr>
	                <tr>
	                <td width="30%">බැංකුව</td>
	                <td width="2%" align="center">:</td>
	                    <td width="68%"><b><?php echo $bank_name;?></b></td>
	                </tr>
	                <tr>
	                <td width="30%">ශාඛාව</td>
	                <td width="2%" align="center">:</td>
	                    <td width="68%"><b><?php echo $branch_no.' '.$branch_name;?></b></td>
	                </tr>
	                
	    		</table> 
			</div>
		</div>
		<div class="row">
			<div style="width: 100%;">
	    		<table>	    			
	                <tr>
	                <td width="30%">ආයතනය</td>
	                <td width="2%" align="center">:</td>
	                <td width="68%"><b><?php echo $row_department['department_name'].'-'.$row_department['department_location']?></b></td>
	                </tr>
	    		</table>    		
			</div>
		</div>

		<div class="row">
			<div style="border-top: 2px #000000 solid; border-left: 2px #000000 solid; width: 50%; text-align: center;">
				<h4><b>ඉපැයීම්</b></h4>
			</div>
			<div style="border-top: 2px #000000 solid; border-left: 2px #000000 solid; border-right: 2px #000000 solid; width: 50%; text-align: center;">
				<h4><b>අඩුකිරීම්</b></h4>
			</div>
		</div>
		<div class="row">
			<div style="border-top: 2px #000000 solid; border-left: 2px #000000 solid; width: 50%; min-height: 250px; max-height: 300px; padding: 1px;">
				<table class="table table-sm table-borderless" style="line-height: 15px;">
				
						<tr>
							
				                        <td width="70%">මුලික වැටුප</td>
				                        <td width="2%" align="center">:</td>
				                        <td width="28%" align="right"><?php 
				                        $basic=$row['basic_salary']-3500;
				                        echo number_format($basic, 2)?></td> 
				                    </tr>

				                    <tr> 
				                        <td width="70%">අයවැය නිදහස් දීමනා (I)</td>
				                        <td width="2%" align="center">:</td>
				                        <td width="28%" align="right"><?php echo number_format(2500, 2)?></td> 
				                    </tr>

				                    <tr> 
				                        <td width="70%">අයවැය නිදහස් දීමනා (II)</td>
				                        <td width="2%" align="center">:</td>
				                        <td width="28%" align="right"><?php echo number_format(1000, 2)?></td> 
				                    </tr>

				                    <!-- <tr>
				                        <td width="70%">සාමාන්‍ය වැඩ කල දින <?php echo $row['n_working_days']?> සඳහා ගෙවීම්</td>
				                        <td width="2%" align="center">:</td>
				                        <td width="28%" align="right"><?php echo number_format($row['n_day_earning'], 2)?></td> 
				                    </tr> -->
				                    <?php
				                    if ($row['poya_days'] > 0):
				                    	
				                    ?>
				                    <tr>
				                        <td width="70%">පෝය නිවාඩු  දින <?php echo $row['poya_days']?> සඳහා ගෙවීම්</td>
				                        <td width="2%" align="center">:</td>
				                        <td width="28%" align="right"><?php echo number_format($row['poya_day_payment'], 2)?></td> 
				                    </tr>
				                     <?php
				                	endif;
				                	if ($row['m_days'] > 0):
				                    ?>
				                    <tr>
				                        <td width="70%">වෙළඳ නිවාඩු දින <?php echo $row['m_days']?> සඳහා ගෙවීම්</td>
				                        <td width="2%" align="center">:</td>
				                        <td width="28%" align="right"><?php echo number_format($row['m_payment'], 2)?></td> 
				                    </tr>
				                    <?php
				                    endif;
				                    if ($row['p_leave_days'] > 0):
				                    	
				                    ?>
				                    <tr>
				                        <td width="70%">නිවාඩු දින <?php echo $row['p_leave_days']?> සඳහා ගෙවීම්</td>
				                        <td width="2%" align="center">:</td>
				                        <td width="28%" align="right"><?php echo number_format($row['p_leave_day_payment'], 2)?></td> 
				                    </tr>
				                    <?php
				                    endif;
				                   				                    	
				                    ?>
				                    <tr>
				                        <td width="70%">අතිකාල දිමනාව x 1.5 (පැය:<?php echo $row['ot_hrs']+$row['h_ot_hrs'];?>)</td>
				                        <td width="2%" align="center">:</td>
				                        <td width="28%" align="right"><?php echo number_format($row['ot_payment'], 2)?></td> 
				                    </tr>
				                    <?php
				                    
				                    if ($row['m_ot_hrs'] > 0):
				                    	
				                    ?>
				                    <tr>
				                        <td width="70%">අතිකාල දිමනාව x 3 (පැය:<?php echo $row['m_ot_hrs']?>)</td>
				                        <td width="2%" align="center">:</td>
				                        <td width="28%" align="right"><?php echo number_format($row['ot_t_payment'], 2)?></td> 
				                    </tr>
				                    <?php
				                	endif;                	
				                    ?>
				                    <tr> 
				                        <td width="70%">දිරි දීමනා</td>
				                        <td width="2%" align="center">:</td>
				                        <td width="28%" align="right"><?php echo number_format($row['incentive'], 2)?></td> 
				                    </tr>
				                    <?php
				                    if (($row['extra_ot_hrs'] > 0) && ($row['extra_ot_payment'] > 0)):
				                    	?>
				                    <tr>
				                        <td width="73%">අමතර අතිකාල (පැය:<?php echo $row['extra_ot_hrs']?>)</td>
				                        <td width="2%" align="center">:</td>
				                        <td width="25%" align="right"><?php echo number_format($row['extra_ot_payment'], 2)?></td> 
				                    </tr>

				                    <?php
				                	endif;
				                	if ($row['arrears_payment'] > 0):
				                    	?>
				                    <tr>
				                        <td width="73%">හිඟ ගෙවීම්</td>
				                        <td width="2%" align="center">:</td>
				                        <td width="25%" align="right"><?php echo number_format($row['arrears_payment'], 2)?></td> 
				                    </tr>

				                    <?php
				                	endif;
				                	?>
				                    	                    
								</table>
							</div>
								<div style="border-top: 2px #000000 solid; border-left: 2px #000000 solid; border-right: 2px #000000 solid; width: 50%; padding: 1px;">
									<table class="table table-sm table-borderless" style="line-height: 15px;">
										<tr> 
					                        <td width="70%">සේ: අර්ථසාධක අරමුදල 8%</td>
					                        <td width="2%" align="center">:</td>
					                        <td width="28%" align="right">
				                        	<?php
				                        	echo number_format($row['employee_epf'], 2);
			                                ?>                                	
				                            </td> 
					                    </tr>
				                    <?php 
				                    
		                            if (($row['no_pay_days'] > 0) && ($row['no_pay'] > 0)):
		                               	?>
		              					<tr>
			                                <td width="70%">වැටුප් රහිත දින:<?php echo $row['no_pay_days'] ?> සඳහා අඩු කිරීම්</td>
			                                <td width="2%" align="center">:</td>
			                                <td width="28%" align="right">
			                                	<?php 
			                                	
			                                	echo number_format($row['no_pay'], 2);
			                                	
			                                ?></td> 
		                            	</tr>
										<?php
		                    		endif;		                            

		                    		if ($row['salary_advance'] > 0):
		                               	?>
		              					<tr>
			                                <td width="70%">වැටුප් අත්තතිකරම්</td>
			                                <td width="2%" align="center">:</td>
			                                <td width="28%" align="right"><?php echo number_format($row['salary_advance'], 2);?></td> 
		                            	</tr>
										<?php
		                    		endif;

		                    		if ($row['ration'] > 0):
		                               	?>
		              					<tr>
			                                <td width="70%">ආහාර</td>
			                                <td width="2%" align="center">:</td>
			                                <td width="28%" align="right"><?php echo number_format($row['ration'], 2);?></td> 
		                            	</tr>
										<?php
		                    		endif;

		                    		if ($row['hostel'] > 0):
		                               	?>
		              					<tr>
			                                <td width="70%">නවාතැන්</td>
			                                <td width="2%" align="center">:</td>
			                                <td width="28%" align="right"><?php echo number_format($row['hostel'], 2);?></td> 
		                            	</tr>
										<?php
		                    		endif;

		                    		if ($row['fines'] > 0):
		                               	?>
		              					<tr>
			                                <td width="70%">බැංකු ගාස්තු සහ දඬුවම්</td>
			                                <td width="2%" align="center">:</td>
			                                <td width="28%" align="right"><?php echo number_format($row['fines'], 2);?></td> 
		                            	</tr>
										<?php
		                    		endif;                   		
		                    				                    		
									?>
								</table>
							</div>
						</div>
						<div class="row">
			<div style="border-top: 2px #000000 solid; border-bottom: 2px #000000 solid; border-left: 2px #000000 solid; width: 50%; padding: 1px;">
				<table class="table table-sm table-borderless">
									<tr>
										<td width="70%"><b>දළ වැටුප</b></td>
		                                <td width="2%" align="center">:</td>
		                                <td width="28%" align="right"><span style="border-bottom: 1px solid; text-decoration: underline;"><b><?php echo number_format($row['gross'], 2);?></b></span></td>
									</tr>
									<tr> 
						                <td width="70%"><b>ශුද්ධ වැටුප</b></td>
						                <td width="2%" align="center">:</td>
						                <td width="28%" align="right"><span style="border-bottom: 1px solid; text-decoration: underline;"><b><?php echo number_format($row['net_salary'], 2);?></b></span></td> 
						            </tr>
						            </table>
			</div>
			<div style="border: 2px #000000 solid; width: 50%; padding: 1px;">
				<table class="table table-sm table-borderless">
					<tr>
						<td width="70%"><b>අඩුකිරීම් වල එකතුව</b></td>
	                    <td width="2%" align="center">:</td>
	                    <td width="28%" align="right"><span style="border-bottom: 1px solid; text-decoration: underline;"><b><?php echo number_format($row['total_deductions'], 2);?></b></span></td>
					</tr>
				</table>
			</div>
		</div>
			<div class="row">
			<div style="border-right: 2px #000000 solid; border-bottom: 2px #000000 solid; border-left: 2px #000000 solid; width: 50%; padding: 1px;">
				<table class="table table-sm table-borderless" style="line-height: 15px;">
					<tr> 
		                <td width="73%">සේ: අ: අ: අදාල මුදල</td>
		                <td width="2%" align="center">:</td>
		                <td width="25%" align="right"><b>
		                	<?php 
                        $for_epf=$row['for_epf'];
                        echo number_format($for_epf, 2)?>
		                	</b></td> 
		            </tr>
		            <tr> 
		                <td width="70%">සේ. අර්ථසාධක අරමුදල 12%</td>
		                <td width="2%" align="center">:</td>
		                <td width="28%" align="right"><b>
		                	<?php 
                        	if ($row['employer_epf']>0) {
                        		echo number_format($row['employer_epf'], 2);
                        	}?>
		                	</b></td> 
		            </tr>
		            <tr> 
		                <td width="70%">සේ.නි.භාරකාර අරමුදල 3%</td>
		                <td width="2%" align="center">:</td>
		                <td width="28%" align="right"><b>
		                	<?php
                                	if ($row['employer_etf']>0) {
                                	 	echo number_format($row['employer_etf'], 2);
                                	 } ?>
		                	</b></td> 
		            </tr>
								</table>
							</div>
						</div>

						<p>This is computer generated Payslip</p>
						<br>
						<br>
						<br>
						<br>

						<div class="row">

							<div style="width: 50%; text-align: left;line-height: 3px; max-height: 200px; ">
		    		<p>------------------------------</p>
		    		<p>අත්සන</p>
		    		<p><?php echo $row['employee_no'].' '.$employee_name['surname'].' '.$employee_name['initial']?></p>
		    	</div>
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