<?php
include "config.php";
$connect = pdoConnection();

$query = '';
$output = array();
$query .= "SELECT e.employee_id, e.surname, e.initial, j.employee_no, e.nic_no, e.permanent_address, e.mobile_no, j.employee_status, p.position_abbreviation, j.join_id, j.join_date, j.location FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid INNER JOIN promotions c ON j.employee_id=c.employee_id INNER JOIN (SELECT employee_id, MAX(id) maxproid FROM promotions GROUP BY employee_id) d ON c.employee_id = d.employee_id AND c.id = d.maxproid INNER JOIN position p ON c.position_id=p.position_id ";
if(isset($_POST["search"]["value"]))
{
 $query .= 'WHERE j.employee_no LIKE "%'.$_POST["search"]["value"].'%" ';
 $query .= 'OR e.nic_no LIKE "%'.$_POST["search"]["value"].'%" ';
}
if(isset($_POST["order"]))
{
 $query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
}
else
{
 $query .= 'ORDER BY ABS(j.employee_no) DESC ';
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
	$statement = $connect->prepare('SELECT c.position_abbreviation FROM promotions a INNER JOIN position c ON a.position_id=c.position_id INNER JOIN (SELECT employee_id, MAX(id) maxid FROM promotions GROUP BY employee_id) b ON a.employee_id = b.employee_id AND a.id = b.maxid WHERE a.employee_id="'.$row['join_id'].'"');
	  $statement->execute();
	  $total_position = $statement->rowCount();
	  $result = $statement->fetchAll();
	  if ($total_position > 0) :
	    foreach($result as $position_name):

	      $position_id = $position_name['position_abbreviation'];
	    endforeach;
	  else:
	    $position_id ='';
	  endif;

	  if (!empty($row['employee_no'])) {
	      $employee_epf=$row['employee_no'];
	  }else{
	    $employee_epf='';
	  }

	  $statement = $connect->prepare('SELECT a.id, a.account_no, a.status, b.bank_name, b.bank_no, c.branch_name, c.branch_no FROM bank_details a INNER JOIN bank_name b ON a.bank_name=b.id INNER JOIN bank_branch c ON a.branch_name=c.id WHERE a.employee_id="'.$row['employee_id'].'"');
	  $statement->execute();
	  $total_bank = $statement->rowCount();
	  $result = $statement->fetchAll();
	  if ($total_bank > 0) :
	    foreach($result as $row_b):

	      $bank_name1 = $row_b['bank_name'].' ('.$row_b['bank_no'].')';
	      $branch_name1 = $row_b['branch_name'].' ('.str_pad($row_b['branch_no'], 3, "0", STR_PAD_LEFT).')';
	      $account_no1 =str_pad($row_b['account_no'], 12, "0", STR_PAD_LEFT);
	    endforeach;
	  else:
	    $bank_name1 ='';
	    $branch_name1 ='';
	    $account_no1 ='';
	  endif;

	  if($row['join_date']!='0000-00-00'):

	  $date1 = $row['join_date'];

	  $date2 = date('Y-m-d');

	  $diff = abs(strtotime($date2)-strtotime($date1));

	  $years = floor($diff / (365*60*60*24));

	  $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));

	  $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

	  endif;

	  $statement = $connect->prepare('SELECT basic_salary FROM salary a INNER JOIN (SELECT employee_id, MAX(id) maxid FROM salary GROUP BY employee_id) b ON a.employee_id = b.employee_id AND a.id = b.maxid WHERE a.employee_id="'.$row['join_id'].'"');
	  $statement->execute();
	  $total_basic = $statement->rowCount();
	  $result = $statement->fetchAll();
	  if ($total_basic > 0) :
	    foreach($result as $row_basic):

	      $basic_salary = number_format($row_basic['basic_salary']);
	    endforeach;
	  else:
	    $basic_salary ='';
	  endif; 

	  $statement = $connect->prepare('SELECT department_name, department_location FROM department  WHERE department_id="'.$row['location'].'"');
	  $statement->execute();
	  $total_loc = $statement->rowCount();
	  $result_loc = $statement->fetchAll();
	  if ($total_loc > 0) :
	    foreach($result_loc as $row_loc):

	      $location = $row_loc['department_name'].'-'.$row_loc['department_location'];
	    endforeach;
	  else:
	    $location ='';
	  endif;

	if($row['employee_status'] == 0): 
		$status='<span class="badge badge-success">Present</span>';
    elseif($row['employee_status'] == 1): 
      $status='<span class="badge badge-danger">Absent</span>';
    elseif($row['employee_status'] == 2): 
      $status='<span class="badge badge-warning">Re-Enlisted</span>';
    elseif($row['employee_status'] == 3): 
      $status='<span class="badge badge-warning">Resignation</span>';
    elseif($row['employee_status'] == 4):
      $status='<span class="badge badge-secondary">Disable</span>';
    endif;

 $sub_array = array();
 $sub_array[] ='';
 $sub_array[] = $employee_epf.' '.$position_id.' '.$row['surname'].' '.$row['initial'];
 $sub_array[] = $row["nic_no"];
 $sub_array[] = $row["nic_no"];
 $sub_array[] = $basic_salary;
 $sub_array[] = $location;
 $sub_array[] = '<dl><dt>'.$bank_name1.'</dt><dd>'.$branch_name1.'</dd><dd>'.$account_no1.'</dd></dl>';
 $sub_array[] = $row['permanent_address'];
 $sub_array[] = $row['mobile_no'];
 $sub_array[] = $status;
 $sub_array[] = '<center>

  <a href="/employee_list/employee/'.$row["employee_id"].'" class="btn btn-sm btn-outline-warning" data-toggle="tooltip" data-placement="left" title="View Profile"><i class="fa fa-eye"></i></a>
  
  <button class="edit_data4 btn btn-sm btn-outline-success" data-id="'.$row["employee_id"].'" type="button" data-toggle="tooltip" data-placement="top" title="Add Bank"><i class="fa fa-bank"></i></button>

  <button class="edit_promote btn btn-sm btn-outline-secondary" data-id="'.$row['join_id'].'" type="button" data-toggle="tooltip" data-placement="top" title="Promote"><i class="fa fa-plus"></i></button>

  <a href="/employee_list/add_employee/'.$row["employee_id"].'" class="btn btn-sm btn-outline-primary" data-toggle="tooltip" data-placement="left" title="Edit"><i class="fa fa-edit"></i></a>';

 $data[] = $sub_array;
}

function get_total_all_records($connect)
{
 $query = "SELECT * FROM employee";
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