<?php

include 'config.php';
$connect = pdoConnection();
//process_data.php
$request = $_POST['request'];   // request
if($request == 1){
	$output = array();
	if((isset($_POST["query"])) OR (isset($_POST["query_new_nic"])) OR (isset($_POST["query_nic_old"])))
	{
		if($_POST["query"] != '')
		{
			$query = "SELECT e.initial, e.surname, p.position_abbreviation, j.employee_no, j.employee_status, j.location FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid INNER JOIN promotions c ON j.join_id=c.employee_id INNER JOIN (SELECT employee_id, MAX(id) maxid_pro FROM promotions GROUP BY employee_id) d ON c.employee_id = d.employee_id AND c.id = d.maxid_pro INNER JOIN position p ON c.position_id=p.position_id WHERE j.employee_no='".$_POST["query"]."' AND (j.employee_status BETWEEN 0 AND 2)
			";
		}

		if($_POST["query_new_nic"] != '')
		{
			$query = " SELECT e.initial, e.surname, p.position_abbreviation, j.employee_no, j.employee_status, j.location FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid INNER JOIN promotions c ON j.join_id=c.employee_id INNER JOIN (SELECT employee_id, MAX(id) maxid_pro FROM promotions GROUP BY employee_id) d ON c.employee_id = d.employee_id AND c.id = d.maxid_pro INNER JOIN position p ON c.position_id=p.position_id WHERE e.nic_no='".$_POST["query_new_nic"]."' AND (j.employee_status BETWEEN 0 AND 2)		
			";
		}

		if($_POST["query_nic_old"] != '')
		{
			$query = "SELECT e.initial, e.surname, p.position_abbreviation, j.employee_no, j.employee_status, j.location FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid INNER JOIN promotions c ON j.join_id=c.employee_id INNER JOIN (SELECT employee_id, MAX(id) maxid_pro FROM promotions GROUP BY employee_id) d ON c.employee_id = d.employee_id AND c.id = d.maxid_pro INNER JOIN position p ON c.position_id=p.position_id WHERE e.nic_no='".$_POST["query_nic_old"]."' AND (j.employee_status BETWEEN 0 AND 2)		
			";
		}

		$query .=" ORDER BY j.join_id DESC LIMIT 1";
			

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
					'name_with_initial'	 =>	 $row['employee_no'].' '.$row['position_abbreviation'].' '.$row['surname'].' '.$row['initial'].'-'.$status.' '.$loc
				);			
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

if($request == 4){
	$output_allowance = array();
	if(isset($_POST["query"]) && !empty($_POST["query"]))
	{			
		$start_date=date('Y-m-d', strtotime($_POST['query_start_date']));
		$end_date=date('Y-m-t', strtotime($start_date));
			$query = "
			SELECT p.allowances_en, c.amount 
			FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id
			INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid 
			INNER JOIN employee_allowances c ON j.join_id=c.employee_id 
			INNER JOIN allowances p ON c.allowances_id=p.allowances_id WHERE (c.effective_date BETWEEN '".$start_date."' AND '".$end_date."') AND j.employee_no='".$_POST["query"]."'
			";
		
			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				$output_allowance[] = array(
					'allowances_en'	=>	 $row['allowances_en'],
					'amount'	 	=>	 number_format($row['amount'], 2)				
				);			
			}

			
		

	}
	
	if(isset($_POST["query_new_nic"]) && !empty($_POST["query_new_nic"]))
	{		

		
			$query = "
			SELECT p.allowances_en, c.amount 
			FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id
			INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid 
			INNER JOIN employee_allowances c ON j.join_id=c.employee_id 
			INNER JOIN allowances p ON c.allowances_id=p.allowances_id WHERE (c.effective_date BETWEEN '".$start_date."' AND '".$end_date."') AND e.nic_no='".$_POST["query_new_nic"]."'
			";
		

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				$output_allowance[] = array(
					'allowances_en'	=>	 $row['allowances_en'],
					'amount'	 	=>	 number_format($row['amount'], 2)				
				);			
			}

	}

	if(isset($_POST["query_nic_old"]) && !empty($_POST["query_nic_old"]))
	{		

		
			$query = "
			SELECT p.allowances_en, c.amount 
			FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id
			INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid 
			INNER JOIN employee_allowances c ON j.join_id=c.employee_id 
			INNER JOIN allowances p ON c.allowances_id=p.allowances_id WHERE (c.effective_date BETWEEN '".$start_date."' AND '".$end_date."') AND e.nic_no='".$_POST["query_nic_old"]."'
			";
		

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				$output_allowance[] = array(
					'allowances_en'	=>	 $row['allowances_en'],
					'amount'	 	=>	 number_format($row['amount'], 2)				
				);			
			}


	}

	if(isset($_POST["query_emp_name_id"]) && !empty($_POST["query_emp_name_id"]))
	{		
		
			$query = "
			SELECT p.allowances_en, c.amount 
			FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id
			INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid 
			INNER JOIN employee_allowances c ON j.join_id=c.employee_id 
			INNER JOIN allowances p ON c.allowances_id=p.allowances_id WHERE (c.effective_date BETWEEN '".$start_date."' AND '".$end_date."') AND j.join_id='".$_POST["query_emp_name_id"]."'
			";
		

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				$output_allowance[] = array(
					'allowances_en'	=>	 $row['allowances_en'],
					'amount'	 	=>	 number_format($row['amount'], 2)				
				);			
			}


	}

	echo json_encode($output_allowance);
}

if($request == 5){
	$output_deduction = array();
	if(isset($_POST["query"]) && ($_POST["query"] != ''))
	{
			$start_date=date('Y-m-d', strtotime($_POST['query_start_date']));
		$end_date=date('Y-m-t', strtotime($start_date));		
			$query = "
			SELECT p.deduction_en, c.amount 
			FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id
			INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid 
			INNER JOIN employee_deductions c ON j.join_id=c.employee_id 
			INNER JOIN deduction p ON c.deduction_id=p.deduction_id WHERE (c.effective_date BETWEEN '".$start_date."' AND '".$end_date."') AND j.employee_no='".$_POST["query"]."'		
			";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				$output_deduction[] = array(
					'deduction_en'	=>	 $row['deduction_en'],
					'amount'	 	=>	 number_format($row['amount'], 2)			
				);			
			}


			$query = "
			SELECT c.amount 
			FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id
			INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid 
			INNER JOIN inventory_deduction c ON j.join_id=c.employee_id 
			WHERE (c.due_date BETWEEN '".$start_date."' AND '".$end_date."') AND j.employee_no='".$_POST["query"]."'	
			";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				$output_deduction[] = array(
					'deduction_en'	=>	 'Uniforms',
					'amount'	 	=>	 number_format($row['amount'], 2)			
				);			
			}

			$query = "
			SELECT c.paid_amount 
			FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id
			INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid 
			INNER JOIN loan_schedules c ON j.join_id=c.employee_id 
			WHERE (c.date_due BETWEEN '".$start_date."' AND '".$end_date."') AND j.employee_no='".$_POST["query"]."'
			";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				$output_deduction[] = array(
					'deduction_en'	=>	 'Loan',
					'amount'	 	=>	 number_format($row['paid_amount'], 2)			
				);			
			}

			$query = "
			SELECT c.amount 
			FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id
			INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid 
			INNER JOIN salary_advance c ON j.join_id=c.employee_id 
			WHERE (c.date_effective BETWEEN '".$start_date."' AND '".$end_date."') AND j.employee_no='".$_POST["query"]."'
			";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				$output_deduction[] = array(
					'deduction_en'	=>	 'Salary Advance',
					'amount'	 	=>	 number_format($row['amount'], 2)			
				);			
			}

			$query = "
			SELECT c.amount 
			FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id
			INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid 
			INNER JOIN ration_deduction c ON j.join_id=c.employee_id 
			WHERE (c.date_effective BETWEEN '".$start_date."' AND '".$end_date."') AND j.employee_no='".$_POST["query"]."'
			";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				$output_deduction[] = array(
					'deduction_en'	=>	 'Ration',
					'amount'	 	=>	 number_format($row['amount'], 2)			
				);			
			}

		}

	if(isset($_POST["query_new_nic"]) && ($_POST["query_new_nic"] != ''))
	{
					
			$query = "
			SELECT p.deduction_en, c.amount 
			FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id
			INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid 
			INNER JOIN employee_deductions c ON j.join_id=c.employee_id 
			INNER JOIN deduction p ON c.deduction_id=p.deduction_id WHERE (c.effective_date BETWEEN '".$start_date."' AND '".$end_date."') AND e.nic_no='".$_POST["query_new_nic"]."'		
			";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				$output_deduction[] = array(
					'deduction_en'	=>	 $row['deduction_en'],
					'amount'	 	=>	 number_format($row['amount'], 2)			
				);			
			}


			$query = "
			SELECT c.amount 
			FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id
			INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid 
			INNER JOIN inventory_deduction c ON j.join_id=c.employee_id 
			WHERE (c.due_date BETWEEN '".$start_date."' AND '".$end_date."') AND e.nic_no='".$_POST["query_new_nic"]."'	
			";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				$output_deduction[] = array(
					'deduction_en'	=>	 'Uniforms',
					'amount'	 	=>	 number_format($row['amount'], 2)			
				);			
			}

			$query = "
			SELECT c.paid_amount 
			FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id
			INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid 
			INNER JOIN loan_schedules c ON j.join_id=c.employee_id 
			WHERE (c.date_due BETWEEN '".$start_date."' AND '".$end_date."') AND e.nic_no='".$_POST["query_new_nic"]."'
			";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				$output_deduction[] = array(
					'deduction_en'	=>	 'Loan',
					'amount'	 	=>	 number_format($row['paid_amount'], 2)			
				);			
			}

			$query = "
			SELECT c.amount 
			FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id
			INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid 
			INNER JOIN salary_advance c ON j.join_id=c.employee_id 
			WHERE (c.date_effective BETWEEN '".$start_date."' AND '".$end_date."') AND e.nic_no='".$_POST["query_new_nic"]."'
			";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				$output_deduction[] = array(
					'deduction_en'	=>	 'Salary Advance',
					'amount'	 	=>	 number_format($row['amount'], 2)			
				);			
			}

			$query = "
			SELECT c.amount 
			FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id
			INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid 
			INNER JOIN ration_deduction c ON j.join_id=c.employee_id 
			WHERE (c.date_effective BETWEEN '".$start_date."' AND '".$end_date."') AND e.nic_no='".$_POST["query_new_nic"]."'
			";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				$output_deduction[] = array(
					'deduction_en'	=>	 'Ration',
					'amount'	 	=>	 number_format($row['amount'], 2)			
				);			
			}

		}

		if(isset($_POST["query_nic_old"]) && ($_POST["query_nic_old"] != ''))
		{
					
			$query = "
			SELECT p.deduction_en, c.amount 
			FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id
			INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid 
			INNER JOIN employee_deductions c ON j.join_id=c.employee_id 
			INNER JOIN deduction p ON c.deduction_id=p.deduction_id WHERE (c.effective_date BETWEEN '".$start_date."' AND '".$end_date."') AND e.nic_no='".$_POST["query_nic_old"]."'		
			";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				$output_deduction[] = array(
					'deduction_en'	=>	 $row['deduction_en'],
					'amount'	 	=>	 number_format($row['amount'], 2)			
				);			
			}


			$query = "
			SELECT c.amount 
			FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id
			INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid 
			INNER JOIN inventory_deduction c ON j.join_id=c.employee_id 
			WHERE (c.due_date BETWEEN '".$start_date."' AND '".$end_date."') AND e.nic_no='".$_POST["query_nic_old"]."'	
			";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				$output_deduction[] = array(
					'deduction_en'	=>	 'Uniforms',
					'amount'	 	=>	 number_format($row['amount'], 2)			
				);			
			}

			$query = "
			SELECT c.paid_amount 
			FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id
			INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid 
			INNER JOIN loan_schedules c ON j.join_id=c.employee_id 
			WHERE (c.date_due BETWEEN '".$start_date."' AND '".$end_date."') AND e.nic_no='".$_POST["query_nic_old"]."'
			";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				$output_deduction[] = array(
					'deduction_en'	=>	 'Loan',
					'amount'	 	=>	 number_format($row['paid_amount'], 2)			
				);			
			}

			$query = "
			SELECT c.amount 
			FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id
			INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid 
			INNER JOIN salary_advance c ON j.join_id=c.employee_id 
			WHERE (c.date_effective BETWEEN '".$start_date."' AND '".$end_date."') AND e.nic_no='".$_POST["query_nic_old"]."'
			";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				$output_deduction[] = array(
					'deduction_en'	=>	 'Salary Advance',
					'amount'	 	=>	 number_format($row['amount'], 2)			
				);			
			}

			$query = "
			SELECT c.amount 
			FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id
			INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid 
			INNER JOIN ration_deduction c ON j.join_id=c.employee_id 
			WHERE (c.date_effective BETWEEN '".$start_date."' AND '".$end_date."') AND e.nic_no='".$_POST["query_nic_old"]."'
			";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				$output_deduction[] = array(
					'deduction_en'	=>	 'Ration',
					'amount'	 	=>	 number_format($row['amount'], 2)			
				);			
			}

		}

		if(isset($_POST["query_emp_name_id"]) && ($_POST["query_emp_name_id"] != ''))
		{
					
			$query = "
			SELECT p.deduction_en, c.amount 
			FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id
			INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid 
			INNER JOIN employee_deductions c ON j.join_id=c.employee_id 
			INNER JOIN deduction p ON c.deduction_id=p.deduction_id WHERE (c.effective_date BETWEEN '".$start_date."' AND '".$end_date."') AND j.join_id='".$_POST["query_emp_name_id"]."'		
			";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				$output_deduction[] = array(
					'deduction_en'	=>	 $row['deduction_en'],
					'amount'	 	=>	 number_format($row['amount'], 2)			
				);			
			}


			$query = "
			SELECT c.amount 
			FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id
			INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid 
			INNER JOIN inventory_deduction c ON j.join_id=c.employee_id 
			WHERE (c.due_date BETWEEN '".$start_date."' AND '".$end_date."') AND j.join_id='".$_POST["query_emp_name_id"]."'	
			";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				$output_deduction[] = array(
					'deduction_en'	=>	 'Uniforms',
					'amount'	 	=>	 number_format($row['amount'], 2)			
				);			
			}

			$query = "
			SELECT c.paid_amount 
			FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id
			INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid 
			INNER JOIN loan_schedules c ON j.join_id=c.employee_id 
			WHERE (c.date_due BETWEEN '".$start_date."' AND '".$end_date."') AND j.join_id='".$_POST["query_emp_name_id"]."'
			";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				$output_deduction[] = array(
					'deduction_en'	=>	 'Loan',
					'amount'	 	=>	 number_format($row['paid_amount'], 2)			
				);			
			}

			$query = "
			SELECT c.amount 
			FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id
			INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid 
			INNER JOIN salary_advance c ON j.join_id=c.employee_id 
			WHERE (c.date_effective BETWEEN '".$start_date."' AND '".$end_date."') AND j.join_id='".$_POST["query_emp_name_id"]."'
			";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				$output_deduction[] = array(
					'deduction_en'	=>	 'Salary Advance',
					'amount'	 	=>	 number_format($row['amount'], 2)			
				);			
			}

			$query = "
			SELECT c.amount 
			FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id
			INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid 
			INNER JOIN ration_deduction c ON j.join_id=c.employee_id 
			WHERE (c.date_effective BETWEEN '".$start_date."' AND '".$end_date."') AND j.join_id='".$_POST["query_emp_name_id"]."'
			";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				$output_deduction[] = array(
					'deduction_en'	=>	 'Ration',
					'amount'	 	=>	 number_format($row['amount'], 2)			
				);			
			}

		}	
		echo json_encode($output_deduction);

	}

if($request == 6){
	$output = array();
	if(isset($_POST["query"]) && ($_POST["query"] != ''))
	{
			$query_start_date = date('Y-m-d', strtotime($_POST['query_start_date']));		
			$query = "
			SELECT d.department_name, p.position_abbreviation, a.no_of_shifts 
			FROM attendance a 			
			INNER JOIN join_status j ON a.employee_id = j.join_id 
			INNER JOIN employee g ON j.employee_id = g.employee_id
			INNER JOIN position p ON a.position_id=p.position_id
			INNER JOIN department d ON a.department_id=d.department_id
			WHERE YEAR(a.start_date)= YEAR('".$query_start_date."') AND MONTH(a.start_date) = MONTH('".$query_start_date."') AND j.employee_no='".$_POST["query"]."' ORDER BY a.department_id ASC
			";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				$output[] = array(
					'department_name'			=>	 $row['department_name'],
					'position_abbreviation'	 	=>	 $row['position_abbreviation'],
					'no_of_shifts'	 			=>	 $row['no_of_shifts'],		
				);			
			}
			
		}
	
		echo json_encode($output);

	}

if($request == 7){
	$output = array();
	if(isset($_POST["department_id"]) && $_POST["department_id"] != '')
	{	
		$start_date = date('Y-m-d', strtotime($_POST['start_date']));
		$query = "
		SELECT e.initial, e.surname, p.position_abbreviation, j.employee_no, a.no_of_shifts, c.position_id, a.extra_ot_hrs, t.position_abbreviation AS position, a.attendance_status, a.id, a.poya_day, a.m_day, a.m_ot_hrs
		FROM attendance a
		INNER JOIN join_status j ON a.employee_id = j.join_id
		INNER JOIN employee e ON j.employee_id = e.employee_id
		INNER JOIN position p ON a.position_id=p.position_id
		INNER JOIN promotions c ON j.join_id=c.employee_id 
		INNER JOIN position t ON c.position_id=t.position_id
		INNER JOIN (SELECT employee_id, MAX(id) maxid_pro FROM promotions GROUP BY employee_id) d ON c.employee_id = d.employee_id AND c.id = d.maxid_pro
		WHERE YEAR(a.start_date)= YEAR('".$start_date."') AND MONTH(a.start_date) = MONTH('".$start_date."') AND a.department_id='".$_POST["department_id"]."' ORDER BY a.id DESC
			";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{	
				
				if ($row['attendance_status']==0) {
					$action='<form action="" method="POST"><input type="hidden" name="att_id" value="'.$row['id'].'"><button class="btn btn-sm btn-outline-danger" name="remove_attendance"  data-toggle="tooltip" data-placement="top" title="Delete" type="submit"><i class="fa fa-trash"></i></button></form>';
				}else{
					$action='<form action="" method="POST"><input type="hidden" name="att_id" value="'.$row['id'].'"><button class="btn btn-sm btn-outline-danger" name="remove_attendance"  data-toggle="tooltip" data-placement="top" title="Delete" type="submit"><i class="fa fa-trash"></i></button></form>';
				}
				$output[] = array(
					'emp_name'	 	 =>	$row['employee_no'].' '.$row['position'].' '.$row['initial'].' '.$row['surname'],
					'position_name'	 =>	$row['position_abbreviation'],
					'no_of_shifts'	 =>	$row['no_of_shifts'],
					'extra_ot_hrs'	 =>	$row['extra_ot_hrs'],
					'm_day'	 				 =>	$row['m_day'],
					'm_ot_hrs'	 		 =>	$row['m_ot_hrs'],
					'poya_day'	 		 =>	$row['poya_day'],			
					'action'	 			 =>	$action,
				);			
			}		

	}
	echo json_encode($output);
}

if($request == 8){
	$output = '';
	if(isset($_POST["department_id"]) && $_POST["department_id"] != '')
	{	
		$start_date = date('Y-m-d', strtotime($_POST['start_date']));
		
		$query = "
		SELECT p.position_abbreviation, a.no_of_shifts, a.position_id
		FROM invoice a
		INNER JOIN position p ON a.position_id=p.position_id
		WHERE YEAR(a.date_effective)= YEAR('".$start_date."') AND MONTH(a.date_effective) = MONTH('".$start_date."') AND a.department_id='".$_POST["department_id"]."' ORDER BY a.position_id ASC
			";
		$statement = $connect->prepare($query);
		$statement->execute();
		$total_data = $statement->rowCount();
		$result = $statement->fetchAll();
		foreach($result as $row_invoice)
		{
			
			$statement = $connect->prepare("
			SELECT sum(no_of_shifts) AS total_shifts
			FROM attendance
			WHERE YEAR(start_date)= YEAR('".$start_date."') AND MONTH(start_date) = MONTH('".$start_date."') AND department_id='".$_POST["department_id"]."' AND position_id='".$row_invoice['position_id']."'
				");
			$statement->execute();
			$result = $statement->fetchAll();
			if ($statement->rowCount()>0) {  
			    foreach($result as $row_att_shifts)
			    {
			      $total_shifts=$row_att_shifts['total_shifts'];
			    }
			}else{
			    $total_shifts=0;
			}

			$shifts_diff=(int)$row_invoice['no_of_shifts']-(int)$total_shifts;

			$output .= '<span class="badge badge-primary">'.$row_invoice['position_abbreviation'].' - '.$shifts_diff.'</span>';		
		}

	}
	echo $output;
}

if($request == 9){
	$output = array();
	$data=array();
	if(isset($_POST["department_id"]) && $_POST["department_id"] != '')
	{	
		$start_date = date('Y-m-d', strtotime($_POST['start_date']));
		$query = "
		SELECT e.initial, e.surname, j.employee_no, a.amount, p.position_abbreviation, a.id, a.status 
		FROM employee_deductions a
		INNER JOIN join_status j ON a.employee_id = j.join_id
		INNER JOIN employee e ON j.employee_id = e.employee_id
		INNER JOIN position p ON e.position_id=p.position_id
		INNER JOIN promotions c ON j.join_id=c.employee_id 		
		INNER JOIN (SELECT employee_id, MAX(id) maxid_pro FROM promotions GROUP BY employee_id) d ON c.employee_id = d.employee_id AND c.id = d.maxid_pro
		WHERE YEAR(a.effective_date)= YEAR('".$start_date."') AND MONTH(a.effective_date) = MONTH('".$start_date."') AND a.department_id='".$_POST["department_id"]."' AND a.deduction_id=2 ORDER BY a.id DESC
			";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{	
				if ($row['status']==0) {
					$action='<form action="" method="POST"><input type="hidden" name="att_id" value="'.$row['id'].'"><button class="btn btn-sm btn-outline-danger" name="remove_attendance"  data-toggle="tooltip" data-placement="top" title="Delete" type="submit"><i class="fa fa-trash"></i></button></form>';
				}else{
					$action='<span class="badge badge-success">Paid</span>';
				}

				$data[] = array(
					'emp_name'	 	 =>	$row['employee_no'].' '.$row['position_abbreviation'].' '.$row['initial'].' '.$row['surname'],					
					'amount'	 =>	number_format($row['amount'],2),
					'action'	 =>	$action,
				);			
			}	

			
			$statement = $connect->prepare("SELECT sum(amount) AS grand_total FROM employee_deductions WHERE YEAR(effective_date)= YEAR('".$start_date."') AND MONTH(effective_date) = MONTH('".$start_date."') AND department_id='".$_POST["department_id"]."' AND deduction_id=2");
		        $statement->execute();
		        $result = $statement->fetchAll();
		        foreach($result as $row_total){             
		          $grand_total=$row_total['grand_total'];
		      }		

	}

	$output = array(
    'data'        =>  $data,
    'total_data_table'    =>  number_format($grand_total,2)
  );

	echo json_encode($output);
}

if($request == 10){
	$data = array();
	if(isset($_POST["department_id"]) && $_POST["department_id"] != '')
	{	
		$start_date = date('Y-m-d', strtotime($_POST['start_date']));
		$query = "
		SELECT e.initial, e.surname, j.employee_no, a.amount, p.position_abbreviation, a.status, a.id, a.supplier_id 
		FROM ration_deduction a
		INNER JOIN join_status j ON a.employee_id = j.join_id
		INNER JOIN employee e ON j.employee_id = e.employee_id
		INNER JOIN position p ON e.position_id=p.position_id
		INNER JOIN promotions c ON j.join_id=c.employee_id 		
		INNER JOIN (SELECT employee_id, MAX(id) maxid_pro FROM promotions GROUP BY employee_id) d ON c.employee_id = d.employee_id AND c.id = d.maxid_pro
		WHERE YEAR(a.date_effective)= YEAR('".$start_date."') AND MONTH(a.date_effective) = MONTH('".$start_date."') AND a.department_id='".$_POST["department_id"]."'
			";
if(isset($_POST["supplier_name"]) && $_POST["supplier_name"] != '')
	{
		$query .= " AND a.supplier_id='".$_POST["supplier_name"]."'";
	}
	$query .= " ORDER BY a.id DESC";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{	
				$statement = $connect->prepare("SELECT supplier_name FROM ration_supplier_list WHERE id='".$row['supplier_id']."'");

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			if ($statement->rowCount() > 0) {
				foreach($result as $row_sup)
				{
					$sup_name=$row_sup['supplier_name'];
				}	
			}else{
				$sup_name='';
			}
			
				if ($row['status']==0) {
					$action='<form action="" method="POST"><input type="hidden" name="att_id" value="'.$row['id'].'"><button class="btn btn-sm btn-outline-danger" name="remove_attendance"  data-toggle="tooltip" data-placement="top" title="Delete" type="submit"><i class="fa fa-trash"></i></button></form>';
				}else{
					$action='<span class="badge badge-success">Paid</span>';
				}

				$data[] = array(
					'emp_name'	 	 =>	$row['employee_no'].' '.$row['position_abbreviation'].' '.$row['initial'].' '.$row['surname'],
					'sup_name'	 	 =>	$sup_name,					
					'amount'	 =>	number_format($row['amount'],2),
					'action'	 =>	$action,					
				);			
			}

			$query_s="SELECT sum(amount) AS grand_total FROM ration_deduction WHERE YEAR(date_effective)= YEAR('".$start_date."') AND MONTH(date_effective) = MONTH('".$start_date."') AND department_id='".$_POST["department_id"]."'";
			
			if(isset($_POST["supplier_name"]) && $_POST["supplier_name"] != '')
			{
				$query_s .= " AND supplier_id='".$_POST["supplier_name"]."'";
			}
			$statement = $connect->prepare($query_s);
		        $statement->execute();
		        $result = $statement->fetchAll();
		        foreach($result as $row_total){             
		          $grand_total=$row_total['grand_total'];
		      }	

	}

	$output = array(
    'data'        =>  $data,
    'total_data_table'    =>  number_format($grand_total,2)
  );

	echo json_encode($output);
}

if($request == 11){
	$output = array();
	$data=array();
	if(isset($_POST["department_id"]) && $_POST["department_id"] != '')
	{	
		$start_date = date('Y-m-d', strtotime($_POST['start_date']));
		$query = "
		SELECT e.initial, e.surname, j.employee_no, a.amount, p.position_abbreviation, a.id, a.status 
		FROM employee_deductions a
		INNER JOIN join_status j ON a.employee_id = j.join_id
		INNER JOIN employee e ON j.employee_id = e.employee_id
		INNER JOIN position p ON e.position_id=p.position_id
		INNER JOIN promotions c ON j.join_id=c.employee_id 		
		INNER JOIN (SELECT employee_id, MAX(id) maxid_pro FROM promotions GROUP BY employee_id) d ON c.employee_id = d.employee_id AND c.id = d.maxid_pro
		WHERE YEAR(a.effective_date)= YEAR('".$start_date."') AND MONTH(a.effective_date) = MONTH('".$start_date."') AND a.department_id='".$_POST["department_id"]."' AND a.deduction_id=1 ORDER BY a.id DESC
			";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				if ($row['status']==0) {
					$action='<form action="" method="POST"><input type="hidden" name="att_id" value="'.$row['id'].'"><button class="btn btn-sm btn-outline-danger" name="remove_attendance"  data-toggle="tooltip" data-placement="top" title="Delete" type="submit"><i class="fa fa-trash"></i></button></form>';
				}else{
					$action='<span class="badge badge-success">Paid</span>';
				}

				$data[] = array(
					'emp_name'	 	 =>	$row['employee_no'].' '.$row['position_abbreviation'].' '.$row['initial'].' '.$row['surname'],					
					'amount'	 =>	number_format($row['amount'],2),
					'action'	 =>	$action,
				);			
			}

			$statement = $connect->prepare("SELECT sum(amount) AS grand_total FROM employee_deductions WHERE YEAR(effective_date)= YEAR('".$start_date."') AND MONTH(effective_date) = MONTH('".$start_date."') AND department_id='".$_POST["department_id"]."' AND deduction_id=1");
		        $statement->execute();
		        $result = $statement->fetchAll();
		        foreach($result as $row_total){             
		          $grand_total=$row_total['grand_total'];
		      }			

	}
	$output = array(
    'data'        =>  $data,
    'total_data_table'    =>  number_format($grand_total,2)
  );

	echo json_encode($output);
}

if($request == 12){
	$output = array();
	if(isset($_POST["department_id"]) && $_POST["department_id"] != '')
	{	
		$start_date = date('Y-m-d', strtotime($_POST['start_date']));
		$query = "
		SELECT e.initial, e.surname, j.employee_no, c.position_id, a.extra_ot_hrs, t.position_abbreviation AS position
		FROM attendance a
		INNER JOIN join_status j ON a.employee_id = j.join_id
		INNER JOIN employee e ON j.employee_id = e.employee_id
		INNER JOIN promotions c ON j.join_id=c.employee_id 
		INNER JOIN position t ON c.position_id=t.position_id
		INNER JOIN (SELECT employee_id, MAX(id) maxid_pro FROM promotions GROUP BY employee_id) d ON c.employee_id = d.employee_id AND c.id = d.maxid_pro
		WHERE YEAR(a.start_date)= YEAR('".$start_date."') AND MONTH(a.start_date) = MONTH('".$start_date."') AND a.department_id='".$_POST["department_id"]."' AND a.no_of_shifts = 0 ORDER BY a.id DESC

			";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{				
				$output[] = array(
					'emp_name'	 	 =>	$row['employee_no'].' '.$row['position'].' '.$row['initial'].' '.$row['surname'],
					'extra_ot_hrs'	 =>	$row['extra_ot_hrs'],
				);			
			}		

	}
	echo json_encode($output);
}

if($request == 13){
	$output = '';
	if((isset($_POST["department_id"]) && $_POST["department_id"] != '') && (isset($_POST["position_id"]) && $_POST["position_id"] != ''))
	{	
		$start_date = date('Y-m-d', strtotime($_POST['start_date']));
		
		$query = "
		SELECT p.position_abbreviation, a.no_of_shifts, a.position_id
		FROM invoice a
		INNER JOIN position p ON a.position_id=p.position_id
		WHERE YEAR(a.date_effective)= YEAR('".$start_date."') AND MONTH(a.date_effective) = MONTH('".$start_date."') AND a.department_id='".$_POST["department_id"]."' AND a.position_id='".$_POST["position_id"]."' ORDER BY a.position_id ASC
			";
		$statement = $connect->prepare($query);
		$statement->execute();
		$total_data = $statement->rowCount();
		$result = $statement->fetchAll();
		foreach($result as $row_invoice)
		{
			
			$statement = $connect->prepare("
			SELECT sum(no_of_shifts) AS total_shifts
			FROM attendance
			WHERE YEAR(start_date)= YEAR('".$start_date."') AND MONTH(start_date) = MONTH('".$start_date."') AND department_id='".$_POST["department_id"]."' AND position_id='".$row_invoice['position_id']."'
				");
			$statement->execute();
			$result = $statement->fetchAll();
			if ($statement->rowCount()>0) {  
			    foreach($result as $row_att_shifts)
			    {
			      $total_shifts=$row_att_shifts['total_shifts'];
			    }
			}else{
			    $total_shifts=0;
			}

			$shifts_diff=(int)$row_invoice['no_of_shifts']-(int)$total_shifts;

			if ($shifts_diff > 0) {
				$output = '<button class="btn btn-sm btn-primary col-sm-3 offset-md-3" name="add_save"> Save</button>';	
			}else{
				$output = '<button class="btn btn-sm btn-danger col-sm-3 offset-md-3" name="spl_save"> Save</button>';	
			}
				
		}

	}else{
		$output = '<button class="btn btn-sm btn-primary col-sm-3 offset-md-3" name="add_save"> Save</button>';
	}
	echo $output;
}

if($request == 14){
	$output = array();
	$data=array();
	if((isset($_POST["department_id"]) && $_POST["department_id"] != '') && (isset($_POST["start_date"]) && $_POST["start_date"] != ''))
	{	
		$start_date = date('Y-m-d', strtotime($_POST['start_date']));
		$query = "
		SELECT e.initial, e.surname, j.employee_no, a.amount, p.position_abbreviation, a.id, a.status 
		FROM salary_advance a
		INNER JOIN join_status j ON a.employee_id = j.join_id
		INNER JOIN employee e ON j.employee_id = e.employee_id
		INNER JOIN promotions c ON j.join_id = c.employee_id 		
		INNER JOIN (SELECT employee_id, MAX(id) maxid_pro FROM promotions GROUP BY employee_id) d ON c.employee_id = d.employee_id AND c.id = d.maxid_pro
		INNER JOIN position p ON c.position_id=p.position_id
		WHERE YEAR(a.date_effective)= YEAR('".$start_date."') AND MONTH(a.date_effective) = MONTH('".$start_date."') AND a.department_id='".$_POST["department_id"]."' ORDER BY a.id DESC
			";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				if ($row['status'] == 0) {
	                $status='<span class="right badge badge-primary">Request</span>';
	                $action='<a href="/institution_list/institution/salary_advance/'.$_POST["department_id"].'/'.$row['id'].'" class="btn btn-sm btn-outline-primary" data-toggle="tooltip" data-placement="left" title="Edit"><i class="fa fa-edit"></i></a><button class="btn btn-sm btn-outline-danger not_approved" name="not_approved" data-toggle="tooltip" data-placement="top" title="Not Approved"><i class="fa fa-times"></i></button>
                                  <button class="btn btn-sm btn-outline-success approved" name="approved" data-toggle="tooltip" data-placement="top" title="Approved"><i class="fa fa-check"></i></button><form action="" method="POST"><input type="hidden" name="att_id" value="'.$row['id'].'"><button class="btn btn-sm btn-outline-danger" name="remove_advance"  data-toggle="tooltip" data-placement="top" title="Delete" type="submit"><i class="fa fa-trash"></i></button></form>';

	              }elseif ($row['status'] == 1) {
	                $status='<span class="right badge badge-success">Paid</span>';
	                $action='<form action="" method="POST"><input type="hidden" name="att_id" value="'.$row['id'].'"><button class="btn btn-sm btn-outline-danger" name="remove_advance"  data-toggle="tooltip" data-placement="top" title="Delete" type="submit"><i class="fa fa-trash"></i></button><button class="btn btn-sm btn-outline-danger halt" name="halt" data-toggle="tooltip" data-placement="top" title="Halt"><i class="fa fa-times"></i></button></form>';
	              }elseif ($row['status'] == 2) {
	                $status='<span class="right badge badge-warning">Released</span>';
	                $action='<button class="btn btn-sm btn-outline-danger not_approved" name="not_approved" data-toggle="tooltip" data-placement="top" title="Not Approved"><i class="fa fa-times"></i></button><form action="" method="POST"><input type="hidden" name="att_id" value="'.$row['id'].'"><button class="btn btn-sm btn-outline-danger" name="remove_advance"  data-toggle="tooltip" data-placement="top" title="Delete" type="submit"><i class="fa fa-trash"></i></button></form>';
	              }elseif ($row['status'] == 3) {
	                $status='<span class="right badge badge-danger">Not Approved</span>';
	                $action='<button class="btn btn-sm btn-outline-success approved" name="approved" data-toggle="tooltip" data-placement="top" title="Approved"><i class="fa fa-check"></i></button><form action="" method="POST"><input type="hidden" name="att_id" value="'.$row['id'].'"><button class="btn btn-sm btn-outline-danger" name="remove_advance"  data-toggle="tooltip" data-placement="top" title="Delete" type="submit"><i class="fa fa-trash"></i></button></form>';
	              }elseif ($row['status'] == 4) {
	                $status='<span class="right badge badge-danger">Halt</span>';
	                $action='';
	              }else{
	                $status='<span class="right badge badge-secondary">Unidentified</span>';
	                $action='<form action="" method="POST"><input type="hidden" name="att_id" value="'.$row['id'].'"><button class="btn btn-sm btn-outline-danger" name="remove_advance"  data-toggle="tooltip" data-placement="top" title="Delete" type="submit"><i class="fa fa-trash"></i></button></form>';
	              } 

				$data[] = array(
					'emp_name'	 	 =>	$row['employee_no'].' '.$row['position_abbreviation'].' '.$row['initial'].' '.$row['surname'],					
					'amount'	 =>	number_format($row['amount'],2),
					'status'	 =>	$status,
					'action'	 =>	$action,
					'id'	 =>	$row['id'],
				);
						
			}	

			$statement = $connect->prepare("SELECT sum(amount) AS grand_total FROM salary_advance WHERE YEAR(date_effective)= YEAR('".$start_date."') AND MONTH(date_effective) = MONTH('".$start_date."') AND department_id='".$_POST["department_id"]."'");
		        $statement->execute();
		        $result = $statement->fetchAll();
		        foreach($result as $row_total){             
		          $grand_total=$row_total['grand_total'];
		      }	

	}
	$output = array(
    'data'        =>  $data,
    'total_data_table'    =>  number_format($grand_total,2)
  );

	echo json_encode($output);
}

if($request == 15){
	$output = array();
	if(isset($_POST["department_id"]) && $_POST["department_id"] != '')
	{	
		$start_date = date('Y-m-d', strtotime($_POST['start_date']));
		$query = "
		SELECT e.initial, e.surname, p.position_abbreviation, j.employee_no, a.no_of_shifts, a.poya_day, a.m_day, a.m_ot_hrs, a.total_ot_hrs, a.extra_ot_hrs, a.id, c.position_id, t.position_abbreviation AS position
		FROM d_attendance a
		INNER JOIN join_status j ON a.employee_id = j.join_id
		INNER JOIN employee e ON j.employee_id = e.employee_id
		INNER JOIN position p ON a.position_id=p.position_id
		INNER JOIN promotions c ON j.join_id=c.employee_id 
		INNER JOIN position t ON c.position_id=t.position_id
		INNER JOIN (SELECT employee_id, MAX(id) maxid_pro FROM promotions GROUP BY employee_id) d ON c.employee_id = d.employee_id AND c.id = d.maxid_pro
		WHERE YEAR(a.start_date)= YEAR('".$start_date."') AND MONTH(a.start_date) = MONTH('".$start_date."') AND a.department_id='".$_POST["department_id"]."' ORDER BY a.id DESC

			";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				
					$action='<form action="" method="POST"><input type="hidden" name="att_id" value="'.$row['id'].'"><button class="btn btn-sm btn-outline-danger" name="remove_attendance"  data-toggle="tooltip" data-placement="top" title="Delete" type="submit"><i class="fa fa-trash"></i></button></form>';
				
				$output[] = array(
					'emp_name'	 	 =>	$row['employee_no'].' '.$row['position'].' '.$row['initial'].' '.$row['surname'],
					'position_name'	=>	$row['position_abbreviation'],
					'no_of_shifts'	=>	$row['no_of_shifts'],
					'total_ot_hrs'	=>	$row['total_ot_hrs'],
					'poya_day'	 	=>	$row['poya_day'],
					'm_day'	 		=>	$row['m_day'],
					'm_ot_hrs'	 	=>	$row['m_ot_hrs'],
					'extra_ot_hrs'	 	=>	$row['extra_ot_hrs'],
					'action'	 			 =>	$action,
				);			
			}		

	}
	echo json_encode($output);
}

if($request == 16){
	$output_allowance = array();
	if(isset($_POST["query"]) && !empty($_POST["query"]))
	{			
		
		$query = "
		SELECT c.due_date, c.amount 
		FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id
		INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid 
		INNER JOIN inventory_deduction c ON j.join_id=c.employee_id 
		WHERE status=0 AND j.employee_no='".$_POST["query"]."'
		";
	
		$statement = $connect->prepare($query);

		$statement->execute();

		$total_data = $statement->rowCount();

		$result = $statement->fetchAll();
		
		foreach($result as $row)
		{
			$output_allowance[] = array(
				'due_date'	=>	 date('Y F', strtotime($row['due_date'])),
				'amount'	 	=>	 number_format($row['amount'], 2)				
			);			
		}
		

	}
	
	if(isset($_POST["query_new_nic"]) && !empty($_POST["query_new_nic"]))
	{		

		$query = "
		SELECT c.due_date, c.amount 
		FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id
		INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid 
		INNER JOIN inventory_deduction c ON j.join_id=c.employee_id 
		WHERE status=0 AND e.nic_no='".$_POST["query_new_nic"]."'
		";
			
		$statement = $connect->prepare($query);

		$statement->execute();

		$total_data = $statement->rowCount();

		$result = $statement->fetchAll();
		
		foreach($result as $row)
		{
			$output_allowance[] = array(
				'due_date'	=>	 date('Y F', strtotime($row['due_date'])),
				'amount'	 	=>	 number_format($row['amount'], 2)				
			);			
		}

	}

	if(isset($_POST["query_nic_old"]) && !empty($_POST["query_nic_old"]))
	{		

		$query = "
		SELECT c.due_date, c.amount 
		FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id
		INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid 
		INNER JOIN inventory_deduction c ON j.join_id=c.employee_id 
		WHERE status=0 AND e.nic_no='".$_POST["query_nic_old"]."'
		";
		
		$statement = $connect->prepare($query);

		$statement->execute();

		$total_data = $statement->rowCount();

		$result = $statement->fetchAll();
		
		foreach($result as $row)
		{
			$output_allowance[] = array(
				'due_date'	=>	 date('Y F', strtotime($row['due_date'])),
				'amount'	 	=>	 number_format($row['amount'], 2)				
			);			
		}


	}

	if(isset($_POST["query_emp_name_id"]) && !empty($_POST["query_emp_name_id"]))
	{		
		
		$query = "
		SELECT c.due_date, c.amount 
		FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id
		INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid 
		INNER JOIN inventory_deduction c ON j.join_id=c.employee_id 
		WHERE status=0 AND j.join_id='".$_POST["query_emp_name_id"]."'
		";
			
		$statement = $connect->prepare($query);

		$statement->execute();

		$total_data = $statement->rowCount();

		$result = $statement->fetchAll();
		
		foreach($result as $row)
		{
			$output_allowance[] = array(
				'allowances_en'	=>	 $row['allowances_en'],
				'amount'	 	=>	 number_format($row['amount'], 2)				
			);			
		}


	}

	echo json_encode($output_allowance);
}

if($request == 17){
	$output = array();
		
		$query = "
		SELECT e.initial, e.surname, j.employee_no, a.due_date, a.amount, a.employee_id, c.position_id, t.position_abbreviation
		FROM inventory_deduction a
		INNER JOIN join_status j ON a.employee_id = j.join_id
		INNER JOIN employee e ON j.employee_id = e.employee_id
		INNER JOIN promotions c ON j.join_id=c.employee_id 
		INNER JOIN position t ON c.position_id=t.position_id
		INNER JOIN (SELECT employee_id, MAX(id) maxid_pro FROM promotions GROUP BY employee_id) d ON c.employee_id = d.employee_id AND c.id = d.maxid_pro
		WHERE a.status=2 ORDER BY a.id DESC

			";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{	
				$ded='<ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item"><b>'.$row['due_date'].'</b> <a class="float-right">'.$row['amount'].'</a></li></ul>';

				$output[] = array(
					'emp_name'	 	 =>	$row['employee_no'].' '.$row['position_abbreviation'].' '.$row['initial'].' '.$row['surname'],
					'due_date'	 =>	date('Y F', strtotime($row['due_date'])),
					'amount'	 =>	$row['amount'],
				);
							
			}		

	
	echo json_encode($output);
}

if($request == 18){
	$output = array();
	if(isset($_POST["start_date"]) && $_POST["start_date"] != '')
	{	
		$start_date = date('Y-m-d', strtotime($_POST['start_date']));
		$query = "
		SELECT e.initial, e.surname, j.employee_no, a.amount, p.position_abbreviation 
		FROM employee_deductions a
		INNER JOIN join_status j ON a.employee_id = j.join_id
		INNER JOIN employee e ON j.employee_id = e.employee_id
		INNER JOIN position p ON e.position_id=p.position_id
		INNER JOIN promotions c ON j.join_id=c.employee_id 		
		INNER JOIN (SELECT employee_id, MAX(id) maxid_pro FROM promotions GROUP BY employee_id) d ON c.employee_id = d.employee_id AND c.id = d.maxid_pro
		WHERE YEAR(a.effective_date)= YEAR('".$start_date."') AND MONTH(a.effective_date) = MONTH('".$start_date."') AND deduction_id=4 ORDER BY a.id DESC
			";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{				
				$output[] = array(
					'emp_name'	 	 =>	$row['employee_no'].' '.$row['position_abbreviation'].' '.$row['initial'].' '.$row['surname'],					
					'amount'	 =>	$row['amount'],
				);			
			}		

	}
	echo json_encode($output);
}

if($request == 19){
	$output = '';
	if(isset($_POST["start_date"]) && $_POST["start_date"] != '')
	{	
		$start_date = date('Y-m-d', strtotime($_POST['start_date']));
		
		$query = "
		SELECT COUNT(employee_id) AS total_count FROM (SELECT employee_id FROM attendance WHERE YEAR(start_date)= YEAR('".$start_date."') AND MONTH(start_date) = MONTH('".$start_date."') GROUP BY employee_id Having SUM(no_of_shifts) >= 4) indebted
			";
		$statement = $connect->prepare($query);
		$statement->execute();
		$total_data = $statement->rowCount();
		$result = $statement->fetchAll();
		foreach($result as $total_count)
		{
			
			$output = '<span class="badge badge-primary">Total Employee - '.$total_count['total_count'].'</span>';		
		}

	}
	echo $output;
}

if($request == 20){
	$output = '';
	
	$output = array();
	if(isset($_POST["query"]) && $_POST["query"] != '') 
	{
		
			$query = "SELECT department_name, department_location FROM department WHERE department_id='".$_POST["query"]."' 		
			";
		

		$query .=" ORDER BY department_id DESC LIMIT 1";
			

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				
				$output[] = array(
					'department_name'	 =>	 $row['department_name'].' '.$row['department_location']
				);			
			}
			
		echo json_encode($output);

	}
	
}

if($request == 21){

$effective_date = date("Y-m-d", strtotime($_POST['effective_date']));

	$statement = $connect->prepare("SELECT * FROM salary_advance WHERE date_effective='".$effective_date."'");	
	$statement->execute();
	$statesList = $statement->fetchAll();

	$response = array();
	foreach($statesList as $state){
		$response[] = array(
				"id" => $state['department_id'],
				"sub_category" => $state['department_name']
			);
	}

	echo json_encode($response);
	exit;
}

if($request == 22){
	$output = '';
	
	$output = array();
	if(isset($_POST["query"]) && $_POST["query"] != '') 
	{
		
			$query = "SELECT deduction_en FROM deduction WHERE deduction_id='".$_POST["query"]."' 		
			";
	

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				
				$output[] = array(
					'deduction_name'	 =>	 $row['deduction_en']
				);			
			}
			
		echo json_encode($output);

	}
	
}

if($request == 23){
	$output = '';
	
	$output = array();
	if(isset($_POST["query"]) && $_POST["query"] != '') 
	{
		
			$query = "SELECT allowances_en FROM allowances WHERE allowances_id='".$_POST["query"]."' 		
			";	

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{
				
				$output[] = array(
					'allowances_name'	 =>	 $row['allowances_en'],
				);			
			}
			
		echo json_encode($output);

	}
	
}

if($request == 24){
	$output = array();
	if(isset($_POST["effective_date"]) && $_POST["effective_date"] != '')
	{	
		$effective_date = date('Y-m-d', strtotime($_POST['effective_date']));
		$query = "
		SELECT j.employee_no, e.initial, e.surname, b.allowances_en, a.amount, a.id, t.position_abbreviation
		FROM employee_allowances a
		INNER JOIN allowances b ON a.allowances_id = b.allowances_id
		INNER JOIN join_status j ON a.employee_id = j.join_id
		INNER JOIN employee e ON j.employee_id = e.employee_id
		INNER JOIN promotions c ON j.join_id=c.employee_id
		INNER JOIN position t ON c.position_id=t.position_id
		INNER JOIN (SELECT employee_id, MAX(id) maxid_pro FROM promotions GROUP BY employee_id) d ON c.employee_id = d.employee_id AND c.id = d.maxid_pro
		WHERE YEAR(a.effective_date)= YEAR('".$effective_date."') AND MONTH(a.effective_date) = MONTH('".$effective_date."')
			";
		if(isset($_POST["allowances_id"]) && $_POST["allowances_id"] != '')
		{
			$query .= " AND a.allowances_id='".$_POST["allowances_id"]."'";
		}
		$query .= " ORDER BY a.id DESC";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{	
				$output[] = array(
					'emp_name'	 	 =>	$row['employee_no'].' '.$row['position_abbreviation'].' '.$row['initial'].' '.$row['surname'],
					'allowance_name'	 =>	$row['allowances_en'],
					'amount'	 =>	$row['amount'],								
					'action'	 			 =>	'<form action="" method="POST"><input type="hidden" name="allowances_id" value="'.$row['id'].'"><button class="btn btn-sm btn-outline-danger" name="remove_allowances"  data-toggle="tooltip" data-placement="top" title="Delete" type="submit"><i class="fa fa-trash"></i></button></form>',
				);			
			}		

	}
	echo json_encode($output);
}

if($request == 25){
	$output = array();
	if(isset($_POST["effective_date"]) && $_POST["effective_date"] != '')
	{	
		$effective_date = date('Y-m-d', strtotime($_POST['effective_date']));
		$query = "
		SELECT j.employee_no, e.initial, e.surname, b.deduction_en, a.amount, a.id, t.position_abbreviation
		FROM employee_deductions a
		INNER JOIN deduction b ON a.deduction_id = b.deduction_id
		INNER JOIN join_status j ON a.employee_id = j.join_id
		INNER JOIN employee e ON j.employee_id = e.employee_id
		INNER JOIN promotions c ON j.join_id=c.employee_id
		INNER JOIN position t ON c.position_id=t.position_id
		INNER JOIN (SELECT employee_id, MAX(id) maxid_pro FROM promotions GROUP BY employee_id) d ON c.employee_id = d.employee_id AND c.id = d.maxid_pro
		WHERE YEAR(a.effective_date)= YEAR('".$effective_date."') AND MONTH(a.effective_date) = MONTH('".$effective_date."')
			";
		if(isset($_POST["deduction_id"]) && $_POST["deduction_id"] != '')
		{
			$query .= " AND a.deduction_id='".$_POST["deduction_id"]."'";
		}
		$query .= " ORDER BY a.id DESC";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{	
				$output[] = array(
					'emp_name'	 	 =>	$row['employee_no'].' '.$row['position_abbreviation'].' '.$row['initial'].' '.$row['surname'],
					'deduction_name'	 =>	$row['deduction_en'],
					'amount'	 =>	$row['amount'],								
					'action'	 			 =>	'<form action="" method="POST"><input type="hidden" name="deduction_id" value="'.$row['id'].'"><button class="btn btn-sm btn-outline-danger" name="remove_deduction"  data-toggle="tooltip" data-placement="top" title="Delete" type="submit"><i class="fa fa-trash"></i></button></form>',
				);			
			}		

	}
	echo json_encode($output);
}

if($request == 26){
	$output = array();
	if(isset($_POST["effective_date"]) && $_POST["effective_date"] != '')
	{	
		$effective_date = date('Y-m-d', strtotime($_POST['effective_date']));
		$query = "
		SELECT j.employee_no, e.initial, e.surname, b.allowances_en, a.amount, a.id, t.position_abbreviation
		FROM d_employee_allowances a
		INNER JOIN d_allowances b ON a.allowances_id = b.allowances_id
		INNER JOIN join_status j ON a.employee_id = j.join_id
		INNER JOIN employee e ON j.employee_id = e.employee_id
		INNER JOIN promotions c ON j.join_id=c.employee_id
		INNER JOIN position t ON c.position_id=t.position_id
		INNER JOIN (SELECT employee_id, MAX(id) maxid_pro FROM promotions GROUP BY employee_id) d ON c.employee_id = d.employee_id AND c.id = d.maxid_pro
		WHERE YEAR(a.effective_date)= YEAR('".$effective_date."') AND MONTH(a.effective_date) = MONTH('".$effective_date."')
			";
		if(isset($_POST["allowances_id"]) && $_POST["allowances_id"] != '')
		{
			$query .= " AND a.allowances_id='".$_POST["allowances_id"]."'";
		}
		$query .= " ORDER BY a.id DESC";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{	
				$output[] = array(
					'emp_name'	 	 =>	$row['employee_no'].' '.$row['position_abbreviation'].' '.$row['initial'].' '.$row['surname'],
					'allowance_name'	 =>	$row['allowances_en'],
					'amount'	 =>	$row['amount'],								
					'action'	 			 =>	'<form action="" method="POST"><input type="hidden" name="allowances_id" value="'.$row['id'].'"><button class="btn btn-sm btn-outline-danger" name="remove_allowances"  data-toggle="tooltip" data-placement="top" title="Delete" type="submit"><i class="fa fa-trash"></i></button></form>',
				);			
			}		

	}
	echo json_encode($output);
}

if($request == 27){
	$output_allowance = array();
	if(isset($_POST["employee_id"]) && !empty($_POST["employee_id"]))
	{			
		
		$query = "
		SELECT c.due_date, c.amount 
		FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id
		INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid 
		INNER JOIN inventory_deduction c ON j.join_id=c.employee_id 
		WHERE status=0 AND j.join_id='".$_POST["employee_id"]."'
		";
	
		$statement = $connect->prepare($query);

		$statement->execute();

		$total_data = $statement->rowCount();

		$result = $statement->fetchAll();
		
		foreach($result as $row)
		{
			$output_allowance[] = array(
				'due_date'	=>	 date('Y F', strtotime($row['due_date'])),
				'amount'	 	=>	 number_format($row['amount'], 2)				
			);			
		}
		

	}

	echo json_encode($output_allowance);
}

if($request == 28){
	$output = array();
	
	if((isset($_POST["employee_id"]) && $_POST["employee_id"] != '') && (isset($_POST["invoice_id"]) && $_POST["invoice_id"] != ''))
	{	
		$query = "
		SELECT e.initial, e.surname, j.employee_no, a.due_date, a.amount, a.employee_id, c.position_id, t.position_abbreviation
		FROM inventory_deduction a
		INNER JOIN join_status j ON a.employee_id = j.join_id
		INNER JOIN employee e ON j.employee_id = e.employee_id
		INNER JOIN promotions c ON j.join_id=c.employee_id 
		INNER JOIN position t ON c.position_id=t.position_id
		INNER JOIN (SELECT employee_id, MAX(id) maxid_pro FROM promotions GROUP BY employee_id) d ON c.employee_id = d.employee_id AND c.id = d.maxid_pro
		WHERE a.status=0 AND a.invoice_id='".$_POST["invoice_id"]."' AND a.employee_id='".$_POST["employee_id"]."' ORDER BY a.id DESC

			";

			$statement = $connect->prepare($query);

			$statement->execute();

			$total_data = $statement->rowCount();

			$result = $statement->fetchAll();
			
			foreach($result as $row)
			{	
				$ded='<ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item"><b>'.$row['due_date'].'</b> <a class="float-right">'.$row['amount'].'</a></li></ul>';

				$output[] = array(
					'emp_name'	 	 =>	$row['employee_no'].' '.$row['position_abbreviation'].' '.$row['initial'].' '.$row['surname'],
					'due_date'	 =>	date('Y F', strtotime($row['due_date'])),
					'amount'	 =>	$row['amount'],
				);
							
			}		
}
	
	echo json_encode($output);
}


?>