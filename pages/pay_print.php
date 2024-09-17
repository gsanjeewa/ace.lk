
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
	if((isset($_GET['view'])) && (isset($_GET['pay_id'])))
    {
    	$query = 'SELECT * FROM payroll_items a INNER JOIN payroll b ON a.payroll_id = b.id  WHERE a.payroll_id="'.$_GET['view'].'" AND a.id="'.$_GET['pay_id'].'"';
        
        $statement = $connect->prepare($query);
        $statement->execute();
        $total_data = $statement->rowCount();
        if ($total_data > 0) {
                
        $result = $statement->fetchAll();
        foreach($result as $row)
        {

        	$date_from=$row['date_from'];
			    $date_to=$row['date_to'];

        	   $year=date("Y", strtotime($date_from));

			    if (date("m", strtotime($date_from))=='01'):
			    	$month='ජනවාරි';
				elseif (date("m", strtotime($date_from))=='02'):
					$month='පෙබරවාරි';
				elseif (date("m", strtotime($date_from))=='03'):
					$month='මාර්තු';
				elseif (date("m", strtotime($date_from))=='04'):
					$month='අප්‍රේල්';
				elseif (date("m", strtotime($date_from))=='05'):
					$month='මැයි';
				elseif (date("m", strtotime($date_from))=='06'):
					$month='ජුනි';
				elseif (date("m", strtotime($date_from))=='07'):
					$month='ජුලි';
				elseif (date("m", strtotime($date_from))=='08'):
					$month='ආගෝස්තු';
				elseif (date("m", strtotime($date_from))=='09'):
					$month='සැප්තැම්බර්';
				elseif (date("m", strtotime($date_from))=='10'):
					$month='ඔක්තෝබර්';
				elseif (date("m", strtotime($date_from))=='11'):
					$month='නොවැම්බර්';
				elseif (date("m", strtotime($date_from))=='12'):
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

          		$query = 'SELECT department_name, department_location FROM department WHERE department_id="'.$row['department_id'].'"';
          	$statement = $connect->prepare($query);
          	$statement->execute();
          	$result = $statement->fetchAll();
          	foreach($result as $row_department)
          	{ 
          	}		     

          	$total_deduction=(string)$row['employee_epf']+(string)$row['absent_amount']+(string)$row['advance_amount']+(string)$row['inventory_amount']+(string)$row['ration_amount']+(string)$row['hostel']+(string)$row['fines']+(string)$row['death_donation']+(string)$row['pending_deductions'];    	
    	?>

	    <div class="page">
	    	<div class="row">

	    		<div style="width: 20%; text-align: right;">            
              		<img src="/dist/img/logo.png" class="img-responsive" style="display: block;margin-left: auto; margin-right: auto; max-width: 125px; height: auto;" />
				</div>
              <div style="width: 80%; text-align: center;">
            	<h3><b>ඒස් ෆ්‍රන්ට් ලයින් සෙකියුරිටි සොලුයුෂන්ස් (පුද්) සමාගම</b></h3>
    			<h4>නො:150/20, පළමු පටුමග, කුඹක්ගහදූව, පාර්ලිමේන්තුව පාර, කෝට්ටේ</h4>
    			<h4 style="text-decoration: underline; text-align: center;"><b>වැටුප් පත්‍රය</b></h4>
              </div>              
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
	                    <td width="63%"><b><?php echo $row['no_of_shift']?></b></td>
	                </tr>	                
	    		</table>    		
			</div>
			<div style="width: 50%;">
				<table>
	    			<tr>
	                <td width="25%">ගිණුම් අංකය</td>
	                <td width="2%" align="center">:</td>
	                    <td width="73%"><b><?php echo $account_no;?></b></td>
	                </tr>
	                <tr>
	                <td width="25%">බැංකුව</td>
	                <td width="2%" align="center">:</td>
	                    <td width="73%"><b><?php echo $bank_name;?></b></td>
	                </tr>
	                <tr>
	                <td width="25%">ශාඛාව</td>
	                <td width="2%" align="center">:</td>
	                    <td width="73%"><b><?php echo $branch_no.' '.$branch_name;?></b></td>
	                </tr>
	                
	    		</table>
			</div>
		</div>
		<div class="row">
			<div style="width: 100%;">
				<table>	    			
	                <tr>
	                <td width="28%">ආයතනය</td>
	                <td width="2%" align="center">:</td>
	                <td width="70%"><b><?php echo $row_department['department_name'].'-'.$row_department['department_location']?></b></td>
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
                        <td width="73%">මුලික වැටුප</td>
                        <td width="2%" align="center">:</td>
                        <td width="25%" align="right"><?php 
                        $basic=$row['basic_salary']-3500;
                        echo number_format($basic, 2)?></td> 
                    </tr>

                    <tr> 
                        <td width="73%">අයවැය නිදහස් දීමනා (I + II)</td>
                        <td width="2%" align="center">:</td>
                        <td width="25%" align="right"><?php echo number_format(3500, 2)?></td> 
                    </tr>

                    <tr>
                        <td width="73%">අතිකාල දිමනාව (පැය:<?php echo $row['ot_hrs']?>)</td>
                        <td width="2%" align="center">:</td>
                        <td width="25%" align="right"><?php echo number_format($row['ot_amount'], 2)?></td> 
                    </tr>
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
                    if ($row['m_ot_hrs'] > 0):
                    	
                    ?>
                    <tr>
                        <td width="70%">අතිකාල දිමනාව x 3 (පැය:<?php echo $row['m_ot_hrs']?>)</td>
                        <td width="2%" align="center">:</td>
                        <td width="28%" align="right"><?php echo number_format($row['m_ot_payment'], 2)?></td> 
                    </tr>
                    <?php
                	endif;                	
                    ?>
                    <tr> 
                        <td width="73%">දිරි දීමනා</td>
                        <td width="2%" align="center">:</td>
                        <td width="25%" align="right"><?php echo number_format($row['incentive'], 2)?></td> 
                    </tr>
                    <?php 
                    
                    if (($row['sot_hrs'] > 0) && ($row['sot_amount'] > 0)):
                    	?>
                    <tr>
                        <td width="73%">අමතර අතිකාල (පැය:<?php echo $row['sot_hrs']?>)</td>
                        <td width="2%" align="center">:</td>
                        <td width="25%" align="right"><?php echo number_format($row['sot_amount'], 2)?></td> 
                    </tr>

                    <?php
                	endif;

                	if ($row['service_allowance'] > 0):
                    	?>
                    <tr>
                        <td width="73%">සේවා දීමනා</td>
                        <td width="2%" align="center">:</td>
                        <td width="25%" align="right"><?php echo number_format($row['service_allowance'], 2)?></td> 
                    </tr>

                    <?php
                	endif;

                	if ($row['rewards'] > 0):
                    	?>
                    <tr>
                        <td width="73%">ප්‍රසාද දීමනා</td>
                        <td width="2%" align="center">:</td>
                        <td width="25%" align="right"><?php echo number_format($row['rewards'], 2)?></td> 
                    </tr>

                    <?php
                	endif;

                	if ($row['chairman_allowance'] > 0):
                    	?>
                    <tr>
                        <td width="73%">කළමනාකාර දීමනා</td>
                        <td width="2%" align="center">:</td>
                        <td width="25%" align="right"><?php echo number_format($row['chairman_allowance'], 2)?></td> 
                    </tr>

                    <?php
                	endif;
                	if ($row['training_be'] > 0):
                    	?>
                    <tr>
                        <td width="73%">හැසිරීමේ හා පුහුණුවීමේ දීමනා</td>
                        <td width="2%" align="center">:</td>
                        <td width="25%" align="right"><?php echo number_format($row['training_be'], 2)?></td> 
                    </tr>

                    <?php
                    endif;
                    $query = 'SELECT * FROM allowances';

	                $statement = $connect->prepare($query);
	                $statement->execute();
	                $total_data = $statement->rowCount();
	                $result = $statement->fetchAll();
	                foreach($result as $rows):
	                    $all_arr[$rows['allowances_id']] = $rows['allowances_si'];
	                endforeach;
	                
	                foreach(json_decode($row['allowances']) as $k => $val):
	                  ?>
	                <tr> 
                        <td width="73%"><?php echo $all_arr[$val->aid] ?></td>
                        <td width="2%" align="center">:</td>
                        <td width="25%" align="right"><?php echo number_format($val->amount, 2)?></td> 
                    </tr>
					<?php
	                endforeach;
                	
                	if ($row['pending_payments'] > 0):
                    	?>
                    <tr>
                        <td width="73%">හිඟ ගෙවීම්</td>
                        <td width="2%" align="center">:</td>
                        <td width="25%" align="right"><?php echo number_format($row['pending_payments'], 2)?></td> 
                    </tr>

                    <?php
                	endif;					                
					?>		                    
				</table>
			</div>
			<div style="border-top: 2px #000000 solid; border-left: 2px #000000 solid; border-right: 2px #000000 solid; width: 50%; padding: 1px;">
				<table class="table table-sm table-borderless" style="line-height: 15px;">									
					<tr> 
                        <td width="73%">සේ: අර්ථසාධක අරමුදල 8%</td>
                        <td width="2%" align="center">:</td>
                        <td width="25%" align="right">
                    	<?php
                    	echo number_format($row['employee_epf'], 2);
                        ?>                                	
                        </td> 
                    </tr>
	                <?php 
	                
	                if (($row['absent_day'] > 0) && ($row['absent_amount'] > 0)):
	                   	?>
	  					<tr>
	                        <td width="73%">වැටුප් රහිත දින:<?php echo $row['absent_day'] ?> සඳහා අඩු කිරීම්</td>
	                        <td width="2%" align="center">:</td>
	                        <td width="25%" align="right">
	                        	<?php 
	                        	
	                        	echo number_format($row['absent_amount'], 2);
	                        	
	                        ?></td> 
	                	</tr>
						<?php
	        		endif;

	                if ($row['loan_amount'] > 0):
	                   	?>
	  					<tr>
	                        <td width="73%">ණය අඩු කිරිම්</td>
	                        <td width="2%" align="center">:</td>
	                        <td width="25%" align="right"><?php echo number_format($row['loan_amount'], 2);?></td> 
	                	</tr>
						<?php
	        		endif;

	        		if ($row['advance_amount'] > 0):
	                   	?>
	  					<tr>
	                        <td width="73%">වැටුප් අත්තිකාරම්</td>
	                        <td width="2%" align="center">:</td>
	                        <td width="25%" align="right"><?php echo number_format($row['advance_amount'], 2);?></td> 
	                	</tr>
						<?php
	        		endif;

	        		if ($row['inventory_amount'] > 0):
	                   	?>
	  					<tr>
	                        <td width="73%">නිල ඇඳුම්</td>
	                        <td width="2%" align="center">:</td>
	                        <td width="25%" align="right"><?php echo number_format($row['inventory_amount'], 2);?></td> 
	                	</tr>
						<?php
	        		endif;

	        		if ($row['ration_amount'] > 0):
	                   	?>
	  					<tr>
	                        <td width="73%">ආහාර</td>
	                        <td width="2%" align="center">:</td>
	                        <td width="25%" align="right"><?php echo number_format($row['ration_amount'], 2);?></td> 
	                	</tr>
						<?php
	        		endif;

	        		if ($row['hostel'] > 0):
	                   	?>
	  					<tr>
	                        <td width="73%">නවාතැන්</td>
	                        <td width="2%" align="center">:</td>
	                        <td width="25%" align="right"><?php echo number_format($row['hostel'], 2);?></td> 
	                	</tr>
						<?php
	        		endif;

	        		if ($row['fines'] > 0):
	                   	?>
	  					<tr>
	                        <td width="73%">බැංකු ගාස්තු සහ දඬුවම්</td>
	                        <td width="2%" align="center">:</td>
	                        <td width="25%" align="right"><?php echo number_format($row['fines'], 2);?></td> 
	                	</tr>
						<?php
	        		endif;

	        		if ($row['death_donation'] > 0):
	                   	?>
	  					<tr>
	                        <td width="73%">මරණාධාර සඳහා අඩුකිරීම්</td>
	                        <td width="2%" align="center">:</td>
	                        <td width="25%" align="right"><?php echo number_format($row['death_donation'], 2);?></td> 
	                	</tr>
						<?php
	        		endif;
	        		$query = 'SELECT * FROM deduction';

	                $statement = $connect->prepare($query);
	                $statement->execute();
	                $total_data = $statement->rowCount();
	                $result = $statement->fetchAll();
	                foreach($result as $rows):
	                    $all_arr[$rows['deduction_id']] = $rows['deduction_si'];
	                endforeach;
	                
	                foreach(json_decode($row['deductions']) as $t => $val):
	                  ?>
	                <tr> 
                        <td width="73%"><?php echo $all_arr[$val->did] ?></td>
                        <td width="2%" align="center">:</td>
                        <td width="25%" align="right"><?php echo number_format($val->amount, 2)?></td> 
                    </tr>
					<?php
	                endforeach;

	        		if ($row['pending_deductions'] > 0):
	                   	?>
	  					<tr>
	                        <td width="73%">හිඟ අයකිරීම්</td>
	                        <td width="2%" align="center">:</td>
	                        <td width="25%" align="right"><?php echo number_format($row['pending_deductions'], 2);?></td> 
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
						<td width="73%"><b>දළ වැටුප</b></td>
                        <td width="2%" align="center">:</td>
                        <td width="25%" align="right"><span style="border-bottom: 1px solid; text-decoration: underline;"><b><?php echo number_format($row['gross'], 2);?></b></span></td>
					</tr>
					<tr> 
		                <td width="73%"><b>ශුද්ධ වැටුප</b></td>
		                <td width="2%" align="center">:</td>
		                <td width="25%" align="right"><span style="border-bottom: 1px solid; text-decoration: underline;"><b><?php echo number_format($row['net'], 2);?></b></span></td> 
		            </tr>
		        </table>
			</div>
			<div style="border: 2px #000000 solid; width: 50%; padding: 1px;">
				<table class="table table-sm table-borderless">
					<tr>
						<td width="73%"><b>අඩුකිරීම් වල එකතුව</b></td>
                        <td width="2%" align="center">:</td>
                        <td width="25%" align="right"><span style="border-bottom: 1px solid; text-decoration: underline;"><b><?php echo number_format($row['deduction_amount'], 2);?></b></span></td>
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
                        $basic_epf=$row['basic_epf'];
                        echo number_format($basic_epf, 2)?>
		                	</b></td> 
		            </tr>
		            <tr> 
		                <td width="73%">සේ: අර්ථසාධක අරමුදල 12%</td>
		                <td width="2%" align="center">:</td>
		                <td width="25%" align="right"><b>
		                	<?php 
                        	if ($row['employer_epf']>0) {
                        		echo number_format($row['employer_epf'], 2);
                        	}?>
		                	</b></td> 
		            </tr>
		            <tr> 
		                <td width="73%">සේ.නි.භාරකාර අරමුදල 3%</td>
		                <td width="2%" align="center">:</td>
		                <td width="25%" align="right"><b>
		                	<?php
                            	if ($row['employer_etf']>0) {
                            	 	echo number_format($row['employer_etf'], 2);
                            	 } ?>
		                	</b></td> 
		            </tr>
				</table>
			</div>
			<div style="width: 50%; padding: 1px;">
				
			</div>
		</div>

		<div class="row">
			<div style="width: 100%; max-height: 150px;min-height: 150px;">
				<?php
	            $query = 'SELECT * FROM department' ;
	            $statement = $connect->prepare($query);
	            $statement->execute();
	            $total_data = $statement->rowCount();
	            $result = $statement->fetchAll();
	            foreach($result as $rows_d):
	                $all_arr[$rows_d['department_id']] = $rows_d['department_name'].'-'.$rows_d['department_location'];
	            endforeach;

	            $query = 'SELECT * FROM position';

	            $statement = $connect->prepare($query);
	            $statement->execute();
	            $total_data = $statement->rowCount();
	            $result = $statement->fetchAll();
	            foreach($result as $rows_p):
	                $all_arr2[$rows_p['position_id']] = $rows_p['position_abbreviation'];
	            endforeach;

	            
            
	            foreach(json_decode($row['department']) as $m => $val):
	            	
	            	$query = 'SELECT * FROM position_pay WHERE position_id="'.$val->p_id.'" AND department_id='.$val->d_id.'';

		            $statement = $connect->prepare($query);
		            $statement->execute();
		            $total_data = $statement->rowCount();
		            $result = $statement->fetchAll();
		            foreach($result as $rows_pay):
		                
		            endforeach;

				?>					
	    		<li class='d-flex justify-content-between align-items-center'><?php echo $all_arr[$val->d_id].' - '.$all_arr2[$val->p_id].' ('.$val->t_shifts.'x'.$rows_pay['position_payment'].')';?><span class='badge badge-primary badge-pill'></span><span></span></li>
		    	    		
	                					
			    <?php
		        endforeach;
		        ?>
			</div>
		</div>
			<div class="row">

			<div style="width: 100%; max-height: 200px;min-height: 200px;">
				<h4 style="text-decoration: underline;"><b>මරණාධාර ගෙවිම්</b></h4>				
				<?php
		        /*$query = 'SELECT * FROM death_donation WHERE due_date BETWEEN "'.$date_from.'" AND "'.$date_to.'"';*/
	       
	            $statement = $connect->prepare("SELECT e.surname, e.initial, j.employee_no, d.relation, j.join_id FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id INNER JOIN death_donation d ON j.join_id = d.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid WHERE d.due_date BETWEEN '".$date_from."' AND '".$date_to."' ORDER BY e.employee_id DESC");
	            $statement->execute();
	            $total_data = $statement->rowCount();
	            $result = $statement->fetchAll();
	            foreach($result as $rows):

	            	$statement = $connect->prepare("SELECT b.position_abbreviation FROM promotions a INNER JOIN position b ON a.position_id = b.position_id INNER JOIN (SELECT employee_id, MAX(id) maxid FROM promotions GROUP BY employee_id) c ON a.employee_id = c.employee_id AND a.id = c.maxid WHERE a.employee_id ='".$rows['join_id']."'");
	            $statement->execute();
	            $total_data = $statement->rowCount();
	            $result = $statement->fetchAll();
	            foreach($result as $rows_pos):
	            	endforeach;

	            	$statement = $connect->prepare("SELECT b.department_name, b.department_location FROM payroll_items a INNER JOIN department b ON a.department_id = b.department_id WHERE a.employee_id ='".$rows['join_id']."'");
	            $statement->execute();
	            $total_data = $statement->rowCount();
	            $result = $statement->fetchAll();
	            foreach($result as $rows_loc):
	            	endforeach;

	                ?>
	                <li class='d-flex justify-content-between align-items-center'><?php echo $rows['employee_no'].' '.$rows_pos['position_abbreviation'].' '.$rows['surname'].' '.$rows['initial'].' - '.$rows['relation'].' - '.$rows_loc['department_name'].'-'.$rows_loc['department_location']; ?><span class='badge badge-primary badge-pill'></span><span></span></li>	
			    	
	                <?php
	            endforeach;

				?>
			</div>
		</div>
		<p>This is computer generated Payslip</p>
	    	<hr style="border-top: 3px dashed black;">
	    	
		    <div class="row">
		    	
	    		<div style="width: 40%; text-align: center;">
	    			<h3><b>ලදුපත</b></h3>
	    		</div>
	    		<div style="width: 30%; text-align: center;">
	    			<h3><b><?php echo $year.' '.$month; ?> මස</b></h3>
	    		</div>
	    		<div style="width: 30%; text-align: right;">
	    			<h3><b>Rs. <?php echo number_format($row['net'], 2);?></b></h3>
	    		</div>
	    	</div>
			<div class="row">
		    
		    <div style="text-align: justify; height: 100px;">
		    	<p>ඉහත සඳහන් වැටුප මාගේ ගිණුමට බැර වී ඇති අතර එය නිවැරදිව භාරගත් බවට සහතික කරමි. මෙම ලදුපත ප්‍රධාන කාර්යාලයට ආපසු යැවීමට කාරුණික වෙමි</p>
		    </div>

		    </div>

		    <div class="row">
		    	<div style="width: 50%; text-align: center;line-height: 3px; ">
		    		<p>------------------------------</p>
		    		<p>අත්සන</p>
		    		<p><?php echo $row['employee_no'].' '.$employee_name['surname'].' '.$employee_name['initial']?></p>
		    	</div>
		    	<div style="width: 50%; text-align: center; center;line-height: 5px;">
		    		<p>------------------------------</p>
		    		<p>දිනය</p>
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