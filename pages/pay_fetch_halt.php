<?php

include "config.php";
$connect = pdoConnection();
$employee_id = $_POST['employee_id'];
$payroll_id = $_POST['payroll_id'];

$query = 'SELECT * FROM payroll_halt WHERE payroll_id="'.$payroll_id.'" AND employee_id="'.$employee_id.'" AND status=2';

$statement = $connect->prepare($query);
$statement->execute();
$total_data = $statement->rowCount();

$result = $statement->fetchAll();
$html = '<div><ul class="list-group">';
if ($total_data >0) {
	
	foreach($result as $row)
	{	

		$html .= "<li class='d-flex justify-content-between align-items-center'>".$row['reason']."</li>";

	}
}else{
	$html .= "<li class='d-flex justify-content-between align-items-center'>Absent</li>";
}

$html .= '</ul></div>';

echo $html;