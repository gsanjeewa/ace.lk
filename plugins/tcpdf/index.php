<?php
	include 'config.php';

	$payroll_id = 2;    
   /* $range = $_POST['payroll_id'];
	$ex = explode(' - ', $range);
	$from = date('Y-m-d', strtotime($ex[0]));
	$to = date('Y-m-d', strtotime($ex[1]));

	$sql = "SELECT *, SUM(amount) as total_amount FROM deductions";
    $query = $conn->query($sql);
   	$drow = $query->fetch_assoc();
    $deduction = $drow['total_amount'];

	$from_title = date('M d, Y', strtotime($ex[0]));
	$to_title = date('M d, Y', strtotime($ex[1]));*/
    header('Content-type: text/html; charset=UTF-8') ;//chrome

	require_once('tcpdf.php');  
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);  
    $pdf->SetTitle('Payslip');  
    /*$pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);  */
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));  
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
    //$pdf->SetDefaultMonospacedFont('helvetica');  
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
    $pdf->SetMargins(PDF_MARGIN_LEFT, '10', PDF_MARGIN_RIGHT);  
    $pdf->setPrintHeader(false);  
    $pdf->setPrintFooter(false);  
    $pdf->SetAutoPageBreak(TRUE, 10);  
    $pdf->setFontSubsetting(true);
    $pdf->SetFont('freeserif', '', 12);
    $pdf->AddPage(); 
    $contents = '';

	$query = "SELECT * FROM payroll_items WHERE payroll_id = '".$payroll_id."'";

	$statement = $connect->prepare($query);
    $statement->execute();
    $total_data = $statement->rowCount();
    $result = $statement->fetchAll();
    foreach($result as $row)
    {
	
                      
      	

		$contents .= '
			<h2 align="center">ගිහාන් සංජීව</h2>
			<h4 align="center"></h4>
			<table cellspacing="0" cellpadding="3">  
    	       	<tr>  
            		<td width="25%" align="right">Employee Name: </td>
                 	<td width="25%"><b>'.$row['employee_id']." ".$row['employee_no'].'</b></td>
				 	<td width="25%" align="right">Rate per Hour: </td>
                 	<td width="25%" align="right">'.number_format($row['ot_amount'], 2).'</td>
    	    	</tr>
    	    	<tr>
    	    		<td width="25%" align="right">Employee ID: </td>
				 	<td width="25%">'.$row['employee_id'].'</td>   
				 	<td width="25%" align="right">Total Hours: </td>
				 	<td width="25%" align="right">'.number_format($row['basic_salary'], 2).'</td> 
    	    	</tr>
    	    	<tr> 
    	    		<td></td> 
    	    		<td></td>
				 	<td width="25%" align="right"><b>Gross Pay: </b></td>
				 	<td width="25%" align="right"><b>'.number_format($row['incentive'], 2).'</b></td> 
    	    	</tr>
    	    	<tr> 
    	    		<td></td> 
    	    		<td></td>
				 	<td width="25%" align="right">Deduction: </td>
				 	<td width="25%" align="right">'.number_format($row['allowance_amount'], 2).'</td> 
    	    	</tr>
    	    	<tr> 
    	    		<td></td> 
    	    		<td></td>
				 	<td width="25%" align="right">Cash Advance: </td>
				 	<td width="25%" align="right">'.number_format($row['gross'], 2).'</td> 
    	    	</tr>
    	    	<tr> 
    	    		<td></td> 
    	    		<td></td>
				 	<td width="25%" align="right"><b>Total Deduction:</b></td>
				 	<td width="25%" align="right"><b>'.number_format($row['deduction_amount'], 2).'</b></td> 
    	    	</tr>
    	    	<tr> 
    	    		<td></td> 
    	    		<td></td>
				 	<td width="25%" align="right"><b>Net Pay:</b></td>
				 	<td width="25%" align="right"><b>'.number_format($row['net'], 2).'</b></td> 
    	    	</tr>
    	    </table>
    	    <br><hr>
		';
	}
    $pdf->writeHTML($contents);  
    $pdf->Output('payslip.pdf', 'I');

?>