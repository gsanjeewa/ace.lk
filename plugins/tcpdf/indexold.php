<?php
	include 'config.php';

	// Include the main TCPDF library (search for installation path).
	
	/*require_once('tcpdf.php');*/
	require_once('examples/tcpdf_include.php');


	// create new PDF document
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Nicole Asuni');
	$pdf->SetTitle('PDF file using TCPDF');

	// set default header data
	/*$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 006', PDF_HEADER_STRING);*/

	// set header and footer fonts
	/*$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));*/

	// remove default header/footer
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);
	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// set margins
	$pdf->SetMargins(10, 10, 10);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// set font
$pdf->setFont('freeserif', '', 12);

	// set image scale factor
	/*$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);*/

	// add a page
	$pdf->AddPage('P', 'A5');

	$html = '';

	$query = "SELECT * FROM payroll_items WHERE payroll_id = '2'";

	$statement = $connect->prepare($query);
    $statement->execute();
    $total_data = $statement->rowCount();
    $result = $statement->fetchAll();
    foreach($result as $row)
    {

		$html .= '<h2 align="center">ඒසේ ෆ්‍රන්ඩට් ලයින්ඩ ශ්‍සකියුරිටි ශ්‍සාලුයුෂන්ඩසේ (පුද්)සමාගම</h2>
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
// Print text using writeHTMLCell()
$pdf->writeHTML($html);

	 
	// $pdf->writeHTML($html, true, false, true, false, '');
	
        // add a page
	/*$pdf->AddPage();*/

	/*$html = '<h4>Second page</h4>';
	
	$pdf->writeHTML($html, true, false, true, false, '');*/

	// reset pointer to the last page
	/*$pdf->lastPage();*/
	//Close and output PDF document
	$pdf->Output('example.pdf', 'I');