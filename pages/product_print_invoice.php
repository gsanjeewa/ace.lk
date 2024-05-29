<?php
session_start();
include('config.php');
$connect = pdoConnection();
define("DOMPDF_ENABLE_REMOTE", false);

if(!empty($_GET['invoice_id']) && $_GET['invoice_id']) {
	// echo $_GET['invoice_id'];
	
	$query="
    SELECT c.surname, c.initial, b.employee_no, f.position_abbreviation FROM inventory_stock a
    INNER JOIN join_status b ON a.employee_id=b.join_id
    INNER JOIN employee c ON b.employee_id=c.employee_id
    INNER JOIN promotions d ON a.employee_id=d.employee_id 
    INNER JOIN (SELECT employee_id, MAX(id) maxproid FROM promotions GROUP BY employee_id) e ON d.employee_id = e.employee_id AND d.id = e.maxproid 
    INNER JOIN position f ON d.position_id=f.position_id
    WHERE a.ref_no='".$_GET['invoice_id']."' AND a.status=4
    GROUP BY a.employee_id    
    ";
    $statement = $connect->prepare($query);
    $statement->execute();
    $total_data = $statement->rowCount();
    $result = $statement->fetchAll();

    foreach ($result as $row_employee){
		if (!empty($row_employee['employee_no'])) {
          	$employee_epf=$row_employee['employee_no'];
      	}else{
        	$employee_epf='';
      	}

      	$employee_name=$employee_epf.' '.$row_employee['position_abbreviation'].' '.$row_employee['surname'].' '.$row_employee['initial'];
    }

	$query="
    SELECT ref_no, employee_id, COALESCE(sum(total),'0') AS total FROM inventory_stock
    WHERE ref_no='".$_GET['invoice_id']."' AND status=4
    ORDER BY id ASC
    ";
    $statement = $connect->prepare($query);
    $statement->execute();
    $total_data = $statement->rowCount();
    $result = $statement->fetchAll();
    


}

$output = '';
$output .= '
<div style="display: table; clear: both;">
  <div style="width: 30%;">
  
  </div>
  <div style="width: 70%;">
    <h3><b>ACE FRONT LINE SECURITY SOLUTIONS (PVT) LTD</b></h3>
    <h4>No:150/20, 1st Lane, Kumbukgahaduwa, Parliment Road, Kotte</h4>
    <h4 style="text-decoration: underline; text-align: center;"><b>Invoice</b></h4>
  </div>


</div>

<table width="100%" border="1" cellpadding="5" cellspacing="0">
	<tr>
	<td colspan="2" align="center" style="font-size:18px"><b>Invoice</b></td>
	</tr>
	<tr>
	<td colspan="2">
	<table width="100%" cellpadding="5">
	<tr>
	<td width="65%">
	<b>'.$employee_name.'</b><br />	
	</td>
	<td width="35%">         
	Invoice No. : '.$_GET['invoice_id'].'<br />
	Invoice Date : <br />
	</td>
	</tr>
	</table>
	<br />
	<table width="100%" border="1" cellpadding="5" cellspacing="0">
	<tr>
	<th align="left" width="5%">#</th>
	<th align="left" width="55%">Product</th>	
	<th align="left" width="10%">Quantity</th>
	<th align="left" width="10%">Price</th>
	<th align="left" width="20%">Actual Amt.</th> 
	</tr>';
$count = 0;   
foreach($result as $invoiceItem){
	$count++;
	$output .= '
	<tr>
	<td align="left">'.$count.'</td>
	<td align="left"></td>
	<td align="left"></td>
	<td align="left"></td>
	<td align="left"></td>   
	</tr>';
}
$output .= '
	<tr>
	<td align="right" colspan="4"><b>Sub Total</b></td>
	<td align="left"><b></b></td>
	</tr>
	';
$output .= '
	</table>
	</td>
	<div class="row">
	<div style="width: 40%; padding: 1px;">
	    Received By:
	</div>
	<div style="width: 40%; padding: 1px;">
		Issued By:
	</div>
	</div>
	</tr>
	</table>';
// create pdf of invoice	
$invoiceFileName = 'Invoice-'.$_GET['invoice_id'].'.pdf';
require_once '../plugins/dompdf/src/Autoloader.php';
Dompdf\Autoloader::register();
use Dompdf\Dompdf;
$dompdf = new Dompdf();
$dompdf->loadHtml(html_entity_decode($output));
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream($invoiceFileName, array("Attachment" => false));
?>   
   