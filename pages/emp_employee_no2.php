<?php

include 'config.php';
$connect = pdoConnection();
//process_data.php
$request = $_POST['request'];   // request
if($request == 1){
	if(isset($_POST["query"]))
	{
		$output = array();

		if($_POST["query"] != '')
		{
			
			$query = "
			SELECT e.initial, e.surname, p.position_abbreviation, j.employee_no, j.employee_status, j.location FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid INNER JOIN promotions c ON j.join_id=c.employee_id INNER JOIN (SELECT employee_id, MAX(id) maxid_pro FROM promotions GROUP BY employee_id) d ON c.employee_id = d.employee_id AND c.id = d.maxid_pro INNER JOIN position p ON c.position_id=p.position_id WHERE j.employee_no='".$_POST["query"]."' AND j.employee_status != 4 ORDER BY j.join_id DESC LIMIT 1		
			";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				$query = "
			SELECT department_name, department_location FROM department WHERE department_id='".$row['location']."'	
			";

			$statement = $connect->prepare($query);
			$statement->execute();
			$result = $statement->fetchAll();
			if ($statement->rowCount() > 0) :
					foreach($result as $row_l):
						$loc=' ('.trim($row_l['department_name']).'-'.trim($row_l['department_location']).')';
					endforeach;
				else:
					$loc='';
				endif;
				if($row['employee_status'] == 0):
                  $status='<span class="badge badge-success">Present</span>';
                elseif($row['employee_status'] == 1):
                  $status='<span class="badge badge-danger">Absent</span>';
                elseif($row['employee_status'] == 2):
                  $status='<span class="badge badge-warning">Re-Enlisted</span>';
                elseif($row['employee_status'] == 3):
                  $status='<span class="badge badge-warning">Resignation</span>';
                endif;
				$output[] = array(
					'name_with_initial'	 =>	 $row['position_abbreviation'].' '.$row['surname'].' '.$row['initial'].'-'.$status.' '.$loc
				);			
			}

		}	
		echo json_encode($output);

	}
}

if($request == 2){
	if(isset($_POST["query"]))
	{
		$output = array();

		if($_POST["query"] != '')
		{
			
			$query = "
			SELECT * 
			FROM department 
			WHERE department_id = '".$_POST["query"]."' AND department_status = 0
			ORDER BY department_id DESC
			";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				$output[] = array(
					'department_name'	 =>	 $row['department_name']				
				);			
			}

		}	
		echo json_encode($output);

	}
}

if($request == 3){
	if(isset($_POST["query"]))
	{
		$output = array();

		if($_POST["query"] != '')
		{
			
			$query = "
			SELECT * 
			FROM position 
			WHERE position_id = '".$_POST["query"]."' AND position_status = 0
			ORDER BY position_id DESC
			";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				$output[] = array(
					'position_id'	 =>	 $row['position_name']				
				);			
			}

		}	
		echo json_encode($output);

	}
}

?>