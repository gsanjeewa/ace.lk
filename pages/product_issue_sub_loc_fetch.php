<?php
include "config.php";
$connect = pdoConnection();

$query = '';
$output = array();
$query .= "SELECT a.id, a.grand_total, a.status, b.location, c.location AS sub_location, a.invoice_no FROM inventory_create_invoice_loc a 
INNER JOIN inventory_location b ON a.location_id=b.id
INNER JOIN inventory_location c ON a.sub_location_id=c.id
";

if(isset($_POST["search"]["value"]))
{
 	$query .= 'WHERE (a.invoice_no LIKE "%'.$_POST["search"]["value"].'%" OR b.location LIKE "%'.$_POST["search"]["value"].'%" OR c.location LIKE "%'.$_POST["search"]["value"].'%") '; 
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
		$action='<form method="POST" target="_blank" action="/inventory/invoice_sub_print"><input type="hidden" name="invoice_id" value="'.$row["id"].'"><button class="btn btn-xs btn-outline-primary float-right"><i class="fas fa-print"></i></button></form>';
	}else{
		$action='<a href="/inventory/issue_sub_loc/'.$row["id"].'" class="btn btn-xs btn-outline-success" data-toggle="tooltip" data-placement="left" title="Add List"><i class="fa fa-plus"></i></a>';
	}
	if ($row['grand_total'] !='') : $grand_total = number_format($row['grand_total'],2); else: $grand_total='';endif;

	$sub_array = array();
	$sub_array[] ='';	
	$sub_array[] = $row["location"];
	$sub_array[] = $row["sub_location"];
	$sub_array[] = $row["invoice_no"];
	$sub_array[] = $grand_total;
	$sub_array[] = '<center><div class="btn-group">'.$action.'</div></center>';

 $data[] = $sub_array;
}

function get_total_all_records($connect)
{
 $query = "SELECT * FROM inventory_create_invoice_loc";
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