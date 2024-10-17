<?php

include 'config.php';

$connect = pdoConnection();
if((isset($_POST["effective_date"])) && (isset($_POST["ins_id"]))):
  $effective_date = date("Y-m", strtotime($_POST['effective_date']));
  $start_date = date("Y-m-d", strtotime($_POST['effective_date']));

$delete_permision = array(
    ':department_id' =>  $_POST['ins_id'],
    ':start_date'    =>  $start_date,
  );

  $query = "
  DELETE FROM d_attendance WHERE department_id=:department_id AND start_date=:start_date
  ";
    
  $statement = $connect->prepare($query);

  $statement->execute($delete_permision);

  $statement = $connect->prepare("SELECT id FROM d_attendance ORDER BY id DESC LIMIT 1");
    $statement->execute();
    $result = $statement->fetchAll();
    if ($statement->rowCount()>0) {        
      foreach($result as $row_id){
        $startpoint = $row_id['id'];        
      }
    }
    else{
      $startpoint = 0;
    }
    
    $sno = $startpoint + 1;

    //-----------------Attendance------------------------//

    $total_array=array();
      $statement = $connect->prepare("SELECT a.employee_id, a.position_id, a.start_date, a.end_date, a.poya_day, a.m_day, a.m_ot_hrs, a.extra_ot_hrs, COALESCE(sum(a.no_of_shifts),'0') AS total_shifts FROM attendance a INNER JOIN d_department_merge b ON a.department_id=b.department_id WHERE b.merge_id='".$_POST["ins_id"]."' AND DATE_FORMAT(a.start_date,'%Y-%m') = '".$effective_date."' GROUP BY a.employee_id");
      $statement->execute();
      $result = $statement->fetchAll();

      if ($statement->rowCount()):
            
        foreach($result as $row):

          $total_array[]=array('employee_id'=>$row['employee_id'],"position_id"=>$row['position_id'],"start_date"=>$row['start_date'],"end_date"=>$row['end_date'],"poya_day"=>$row['poya_day'],"m_day"=>$row['m_day'],"m_ot_hrs"=>$row['m_ot_hrs'],"extra_ot_hrs"=>$row['extra_ot_hrs'],"total_shifts"=>$row['total_shifts']);
          
        endforeach;

      else:

        $statement = $connect->prepare("SELECT employee_id, department_id, position_id, start_date, end_date, poya_day, m_day, m_ot_hrs, extra_ot_hrs, COALESCE(sum(no_of_shifts),'0') AS total_shifts FROM attendance           
          WHERE DATE_FORMAT(start_date,'%Y-%m') = '".$effective_date."' AND department_id = '".$_POST["ins_id"]."' GROUP BY employee_id");
        $statement->execute();
        $result = $statement->fetchAll();

        foreach($result as $row):

          $total_array[]=array('employee_id'=>$row['employee_id'],"position_id"=>$row['position_id'],"start_date"=>$row['start_date'],"end_date"=>$row['end_date'],"poya_day"=>$row['poya_day'],"m_day"=>$row['m_day'],"m_ot_hrs"=>$row['m_ot_hrs'],"extra_ot_hrs"=>$row['extra_ot_hrs'],"total_shifts"=>$row['total_shifts']);

        endforeach;

      endif;

      foreach($total_array as $row):

        //------------------shift details-----------------------//
        $statement = $connect->prepare("SELECT a.shifts FROM d_shifts_rate_max a INNER JOIN attendance b ON a.department_id = b.department_id AND a.position_id = b.position_id WHERE b.start_date = '".$row['start_date']."' AND b.end_date = '".$row['end_date']."' AND a.department_id = '".$_POST["ins_id"]."' AND a.status=0 ORDER BY a.id DESC LIMIT 1");
        $statement->execute();
        $result = $statement->fetchAll();
        
        if ($statement->rowCount()>0):
          foreach($result as $row_shifts):
            $dm_new=$row_shifts['shifts'];
          endforeach;
        
        else:

          $month= date('F', strtotime($_POST['effective_date']));                          
          $statement = $connect->prepare("SELECT shifts FROM shifts WHERE months = '".$month."'");
          $statement->execute();
          $result = $statement->fetchAll();
          foreach($result as $shifts):
            $dm_new = $shifts['shifts'];
          endforeach;

        endif;

        if (!empty($_POST['total_shifts'])) {
          $total_shifts=$row['total_shifts'];
        }else{
          if ($row['total_shifts'] >= $dm_new) :
            $total_shifts=$dm_new;
          else:
            $total_shifts=$row['total_shifts'];
          endif;
        }
        
        if (!empty($_POST['extra_ot'])) {
          $extra_ot=$row['extra_ot_hrs'];
        }else{
          $extra_ot='';
        }

		$data = array(
      ':id'           => $sno ++,
      ':employee_id'  => $row['employee_id'],
  		':department_id'=> $_POST["ins_id"],
  		':position_id'  => $row['position_id'],
  		':start_date'   => $row['start_date'],
  		':end_date'     => $row['end_date'],
      ':no_of_shifts' => $total_shifts,
			':poya_day'     => $row['poya_day'],
  		':m_day'        => $row['m_day'],
      ':m_ot_hrs'     => $row['m_ot_hrs'],
      ':extra_ot_hrs' => $extra_ot,
	 	);

	 	$query = "
	 	INSERT INTO d_attendance(id, employee_id, department_id, position_id, start_date, end_date, no_of_shifts, poya_day, m_day, m_ot_hrs, extra_ot_hrs) 
	 	VALUES (:id, :employee_id, :department_id, :position_id, :start_date, :end_date, :no_of_shifts, :poya_day, :m_day, :m_ot_hrs, :extra_ot_hrs)    
	 	";    

	 	$statement = $connect->prepare($query);

	 	$statement->execute($data);  

	endforeach;
 echo 'done';
endif;

?>
