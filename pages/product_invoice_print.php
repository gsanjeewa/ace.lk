
<?php
session_start(); 
	error_reporting(0);
	require 'config.php';

	$connect = pdoConnection();
	require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 68) == "false") {

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
        size: A5;
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
    if(isset($_POST['invoice_id']))
    {	
    	$statement = $connect->prepare('SELECT * FROM address WHERE status=0 ORDER BY id DESC LIMIT 1');
          	$statement->execute();
          	$result = $statement->fetchAll();
          	foreach($result as $row_address)
          	{ 
          	}

          	$query .= "SELECT a.grand_total, a.status, a.invoice_no, a.invoice_date, f.location, j.employee_no, p.position_abbreviation, e.surname, e.initial FROM inventory_create_invoice a 
			INNER JOIN join_status j ON a.employee_id = j.join_id
			INNER JOIN promotions c ON a.employee_id=c.employee_id 
			INNER JOIN (SELECT employee_id, MAX(id) maxproid FROM promotions GROUP BY employee_id) d ON c.employee_id = d.employee_id AND c.id = d.maxproid 
			INNER JOIN position p ON c.position_id=p.position_id 
			INNER JOIN employee e ON j.employee_id=e.employee_id
			INNER JOIN inventory_location f ON a.location_id=f.id
			WHERE a.id='".$_POST['invoice_id']."'
			";
			$statement = $connect->prepare($query);
			$statement->execute();
          	$result = $statement->fetchAll();
          	foreach($result as $row)
          	{ 

          		if (!empty($row['employee_no'])) {
				  $employee_epf=$row['employee_no'];
				}else{
				  $employee_epf='';
				}
          	}

    	?>
	    	<div class="page">
	    		<div class="row">
	    			<div class="col-12 col-sm-12 col-md-12">
		            <div class="info-box">
		              <img src="/dist/img/logo.png" class="img-responsive" style="max-width: 150px; height: auto;" />

		              <div class="info-box-content">
		                	<h3><b><?php echo $row_address['name_eng']; ?></b></h3>
			    			<h4><?php echo $row_address['address_eng']; ?></h4>
			    			<h4 style="text-decoration: underline; text-align: center;"><b>INVOICE</b></h4>
		              </div>
		              <!-- /.info-box-content -->
		            </div>
		            <!-- /.info-box -->
		          </div> 
	    		</div>
	    		<div class="row">
	    			<div style="width: 60%; padding: 1px; height: 100px;">
	    				<h4>Bill To</h4>
	    				<b><?php echo $employee_epf.' '.$row['position_abbreviation'].' '.$row['surname'].' '.$row['initial']; ?></b>
	    			</div>
	    			<div style="width: 40%; padding: 1px;">
	    				<table>
	    					
			    			<tr>  
			                    <td width="40%">Invoice No</td>
			                    <td width="2%" align="center">:</td>
			                    <td width="58%"><b><?php echo $row['invoice_no']; ?></b></td>
			                </tr>
			                <tr>
			                    <td width="40%">Date</td>
			                    <td width="2%" align="center">:</td>
			                    <td width="58%"><b><?php echo $row['invoice_date']; ?></b></td>
			                </tr>
	    				
	    				</table>
	    			</div>

	    		</div>
	    		<div class="row">
	    			<div style="width: 100%; padding: 1px; min-height: 200px; max-height: 250px;">
					<?php

			    	$query = 'SELECT b.product_name, a.qty, a.unit_price, a.total, a.size, c.color, d.gender FROM inventory_stock a 
			    	INNER JOIN inventory_product b ON a.product_id = b.id 
			    	LEFT JOIN inventory_color c ON a.color=c.id
			    	LEFT JOIN inventory_gender d ON a.gender=d.id
			    	WHERE a.invoice_id ="'.$_POST['invoice_id'].'" ORDER BY a.id ASC';
			        
			        $statement = $connect->prepare($query);
			        $statement->execute();
			        $total_data = $statement->rowCount();
			        $result = $statement->fetchAll();
			        ?>
	    			<table class="table table-bordered" style="line-height: 5px;">
	    				<thead>
		    				<tr>
		    					<th style="width:5%">#</th>
		    					<th style="width:45%">Description</th>
		    					<th style="width:10%; text-align:center;">Qty</th>
		    					<th style="width:20%; text-align: right;">Unit Price</th>
		    					<th style="width:20%; text-align:right;">Amount</th>
		    				</tr>
	    				</thead>
	    				<tbody>
	    					
	    						<?php
	    						$startpoint =0;
                      			$sno = $startpoint + 1;
							    foreach($result as $row)
							    {

							    	if ($row['color']!='No') {
							    		$color=$row['color'];
							    	}else{
							    		$color='';
							    	}

							    	if ($row['gender']!='No') {
							    		$gender=$row['gender'];
							    	}else{
							    		$gender='';
							    	}

							    	?>
							    	<tr>
			                            <td><?php echo $sno; ?></td>
			                            <td><?php echo $row['product_name'].' '.$row['size'].' '.$color.' '.$gender; ?></td>
			                            <td style="text-align:center;"><?php echo $row['qty']; ?></td>
			                            <td style="text-align:right;"><?php echo number_format($row['unit_price'],2); ?></td>
			                            <td style="text-align:right;"><?php echo number_format($row['total'],2); ?></td>
			                        
	    					</tr>
	    					<?php
                        $sno ++;
                      }
                      ?>
	    				</tbody>
	    				<tfoot>
	    					<tr>
	    						<th colspan="4"><center>
	    							Total
	    						</center></th>
	    						<th style="text-align:right;">	    							
	    							<?php 

	    							$query = 'SELECT sum(a.total) AS sub_total FROM inventory_stock a INNER JOIN inventory_product b ON a.product_id = b.id WHERE a.invoice_id ="'.$_POST['invoice_id'].'" ORDER BY a.id ASC';
			        
							        $statement = $connect->prepare($query);
							        $statement->execute();
							        $total_data = $statement->rowCount();
							        $result = $statement->fetchAll();
							        foreach($result as $row_total)
											    {
											    	?>
											    	<span style="border-bottom: 1px solid; text-decoration: underline;"><b><?php echo number_format($row_total['sub_total'], 2);?></b></span>
							    	
							    <?php }

							    	?>	    							
	    						</th>
	    					</tr>
	    				</tfoot>

	    			</table>
	    		</div>
	    		
	    </div>
	    <div class="row">
	    	<div style="width: 50%; padding: 1px;">
	    		Received By:
	    	</div>
	    	<div style="width: 50%; padding: 1px;">
	    		Issued By:
	    	</div>
	    </div>
	    </div>
	    <?php

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