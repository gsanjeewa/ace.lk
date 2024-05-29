<?php

include 'config.php';
$connect = pdoConnection();
//process_data.php
$request = $_POST['request'];   // request
if($request == 1){
	$output = array();
	if(isset($_POST["department_id"]) && $_POST["department_id"] != '')
	{
		$date_effective = date('Y-m-d', strtotime($_POST['date_effective']));
					
			$query = "
			SELECT b.position_abbreviation, a.no_of_shifts, a.id
			FROM to_be_applied a
			INNER JOIN position b ON a.position_id=b.position_id
			WHERE a.department_id='".$_POST['department_id']."'
			ORDER BY a.position_id ASC
			";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				$action='<div class="btn-group"><form action="" method="POST"><input type="hidden" name="att_id" value="'.$row['id'].'"><button class="btn btn-sm btn-outline-danger" name="remove_attendance"  data-toggle="tooltip" data-placement="top" title="Delete" type="submit"><i class="fa fa-trash"></i></button></form></div>';
				$output[] = array(
					'position_name'	 =>	 $row['position_abbreviation'],
					'no_of_shifts'	 =>	 $row['no_of_shifts'],
					'action'	 =>	 $action,
				);			
			}

			
		

	}
	echo json_encode($output);
}

?>