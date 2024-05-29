<?php

//process.php
include 'config.php';
/*$connect = new PDO("mysql:host=localhost; dbname=testing", "root", "");*/
$connect = pdoConnection();
if((isset($_POST["effective_date"])) && (isset($_POST["ins_id"]))):
  $effective_date = date("Y-m", strtotime($_POST['effective_date']));

    //-----------------Attendance------------------------//
	  $statement = $connect->prepare("SELECT employee_id, department_id, position_id, start_date, end_date, poya_day, m_day, m_ot_hrs, COALESCE(sum(no_of_shifts),'0') AS total_shifts FROM attendance WHERE DATE_FORMAT(start_date,'%Y-%m') = '".$effective_date."' AND department_id = '".$_POST["ins_id"]."' GROUP BY employee_id");
    $statement->execute();
    $result = $statement->fetchAll();
    foreach($result as $row): 

    	$month= date('F', strtotime($_POST['effective_date']));                          
      $statement = $connect->prepare("SELECT shifts FROM shifts WHERE months = '".$month."'");
      $statement->execute();
      $result = $statement->fetchAll();
      foreach($result as $shifts):
        $dm_new = $shifts['shifts'];
      endforeach;

      $statement = $connect->prepare("SELECT a.position_id FROM promotions a INNER JOIN (SELECT employee_id, MAX(id) maxid_pro FROM promotions GROUP BY employee_id) b ON a.employee_id = b.employee_id AND a.id = b.maxid_pro WHERE a.employee_id='".$row['employee_id']."'");
      $statement->execute();
      $result = $statement->fetchAll();
      foreach($result as $row_position):
        
      endforeach;

      if ($row['total_shifts'] >= $dm_new) :
        $total_shifts=$dm_new;
      else:
        $total_shifts=$row['total_shifts'];
      endif;

		$data = array(
      ':employee_id'  => $row['employee_id'],
  		':department_id'=> $row['department_id'],
  		':position_id'  => $row_position['position_id'],
  		':start_date'   => $row['start_date'],
  		':end_date'     => $row['end_date'],
      ':no_of_shifts' => $total_shifts,
			':poya_day'     => $row['poya_day'],
  		':m_day'        => $row['m_day'],
      ':m_ot_hrs'     => $row['m_ot_hrs'],  		  		 		
	 	);

	 	$query = "
	 	INSERT INTO d_attendance(employee_id, department_id, position_id, start_date, end_date, no_of_shifts, poya_day, m_day, m_ot_hrs) 
	 	VALUES (:employee_id, :department_id, :position_id, :start_date, :end_date, :no_of_shifts, :poya_day, :m_day, :m_ot_hrs)    
	 	";    

	 	$statement = $connect->prepare($query);

	 	$statement->execute($data);
	endforeach;

 echo 'done';
 
endif;

?>
