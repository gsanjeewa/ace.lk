<?php
include "config.php";
$connect = pdoConnection();

$query = '';
$output = array();
$query .= "SELECT a.id, a.grand_total, a.status, f.location, a.invoice_no, a.employee_id, j.employee_no, p.position_abbreviation, e.surname, e.initial FROM inventory_create_invoice a 
INNER JOIN join_status j ON a.employee_id = j.join_id
INNER JOIN promotions c ON a.employee_id=c.employee_id 
INNER JOIN (SELECT employee_id, MAX(id) maxproid FROM promotions GROUP BY employee_id) d ON c.employee_id = d.employee_id AND c.id = d.maxproid 
INNER JOIN position p ON c.position_id=p.position_id 
INNER JOIN employee e ON j.employee_id=e.employee_id
INNER JOIN inventory_location f ON a.location_id=f.id
";

if(isset($_POST["search"]["value"]))
{
 	$query .= 'WHERE (j.employee_no LIKE "%'.$_POST["search"]["value"].'%" OR e.surname LIKE "%'.$_POST["search"]["value"].'%" OR e.initial LIKE "%'.$_POST["search"]["value"].'%" OR a.invoice_no LIKE "%'.$_POST["search"]["value"].'%" OR f.location LIKE "%'.$_POST["search"]["value"].'%") '; 
}

if(isset($_POST["order"])) {
    $column_index = $_POST['order']['0']['column'];
    $column_name = $_POST['columns'][$column_index]['data'];
    $sort_direction = $_POST['order']['0']['dir'];
    $query .= 'ORDER BY ' . $column_name . ' ' . $sort_direction . ' ';
} else {
    $query .= 'ORDER BY id DESC ';
}

if($_POST["length"] != -1)
{
 	$query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}

$statement = $connect->prepare($query);
$statement->execute();
$result = $statement->fetchAll();
$data = array();
$filtered_rows = $statement->rowCount();
foreach($result as $row)
{
	if (!empty($row['employee_no'])) {
	  $employee_epf=$row['employee_no'];
	}else{
	  $employee_epf='';
	}

	if ($row["status"]==1) {
		$action ='<form method="POST" target="_blank" action="/inventory/invoice_print"><input type="hidden" name="invoice_id" value="'.$row["id"].'"><button class="btn btn-xs btn-outline-primary float-right"><i class="fas fa-print"></i></button></form><a href="/inventory/deduction/'.$row["id"].'" class="btn btn-xs btn-outline-warning" data-toggle="tooltip" data-placement="left" title="Deduction"><i class="fa fa-plus"></i></a>';
		
	}elseif($row["status"]==1){
		$action='<form method="POST" target="_blank" action="/inventory/invoice_print"><input type="hidden" name="invoice_id" value="'.$row["id"].'"><button class="btn btn-xs btn-outline-primary float-right"><i class="fas fa-print"></i></button></form>';
	}else{
		$action='<a href="/inventory/issue_product/'.$row["id"].'" class="btn btn-xs btn-outline-success" data-toggle="tooltip" data-placement="left" title="Add List"><i class="fa fa-plus"></i></a>';
	}


	$sub_array = array();
	$sub_array[] ='';
	$sub_array[] = $employee_epf.' '.$row['position_abbreviation'].' '.$row['surname'].' '.$row['initial'];
	$sub_array[] = $row["location"];
	$sub_array[] = $row["invoice_no"];
	$sub_array[] = $row["grand_total"];	 
	$sub_array[] = '<center><div class="btn-group">'.$action.'</div></center>';

 $data[] = $sub_array;
}

function get_total_all_records($connect)
{
 $query = "SELECT * FROM inventory_create_invoice";
 $statement = $connect->prepare($query);
 $statement->execute();
 return $statement->rowCount();
}

$output = array(
 "draw"    => intval($_POST["draw"]),
 "recordsTotal"  =>  $filtered_rows, 
 "recordsFiltered" => get_total_all_records($connect),
 "data"    => $data
);
echo json_encode($output);
?>