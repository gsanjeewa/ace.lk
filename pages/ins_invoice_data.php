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
			SELECT b.position_abbreviation, a.no_of_shifts, a.payment, a.id
			FROM invoice a
			INNER JOIN position b ON a.position_id=b.position_id
			WHERE YEAR(a.date_effective)= YEAR('".$date_effective."') AND MONTH(a.date_effective) = MONTH('".$date_effective."') AND a.department_id='".$_POST['department_id']."'
			ORDER BY a.position_id ASC
			";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				$action='<a href="/institution_list/institution/invoice/'.$_POST['department_id'].'/'.$row['id'].'" class="btn btn-sm btn-outline-primary" data-toggle="tooltip" data-placement="left" title="Edit"><i class="fa fa-pen"></i></a>';
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