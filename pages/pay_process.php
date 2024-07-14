<?php

//process.php
include 'config.php';
/*$connect = new PDO("mysql:host=localhost; dbname=testing", "root", "");*/
$connect = pdoConnection();
if(isset($_POST["payroll_id"]))
{
    
$delete_permision = array(
    ':payroll_id'      =>  $_POST['payroll_id'],     
  );

  $query = "
  DELETE FROM `payroll_items` WHERE `payroll_id`=:payroll_id;
  DELETE FROM `reports_income` WHERE `payroll_id`=:payroll_id;
  DELETE FROM `reports_position_pay` WHERE `payroll_id`=:payroll_id;
  DELETE FROM `reports_department` WHERE `payroll_id`=:payroll_id;
  ";
    
  $statement = $connect->prepare($query);

  $statement->execute($delete_permision);
  
 	$statement = $connect->prepare("SELECT date_from, date_to, type FROM payroll WHERE id ='".$_POST["payroll_id"]."'");
  	$statement->execute();
  	$result = $statement->fetchAll();
  	foreach($result as $pay):
  
	    $date_from=$pay['date_from'];
	    $date_to=$pay['date_to'];
	    $type=$pay['type'];
    
  	endforeach;

    $statement = $connect->prepare("SELECT id FROM payroll_items ORDER BY id DESC LIMIT 1");
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
	  $statement = $connect->prepare("SELECT DISTINCT employee_id FROM attendance WHERE start_date = '".$date_from."' AND end_date = '".$date_to."' AND (attendance_status=0 OR attendance_status=2)");
    $statement->execute();
    $result = $statement->fetchAll();
    foreach($result as $employee_id){
    	$employee_id = $employee_id['employee_id']; 

      $statement = $connect->prepare("
      WITH RankedAttendance AS (
    SELECT
        employee_id,
        department_id,
        sum(attendance.no_of_shifts) no_of_shifts,
        ROW_NUMBER() OVER (PARTITION BY employee_id ORDER BY no_of_shifts DESC) AS rnk
    FROM
        attendance
        WHERE start_date = '".$date_from."' AND end_date = '".$date_to."' AND (attendance_status=0 OR attendance_status=2) AND employee_id='".$employee_id."'
GROUP BY employee_id, department_id
ORDER BY employee_id, no_of_shifts DESC ,department_id
)
SELECT
    employee_id,
    department_id,
    no_of_shifts
FROM
    RankedAttendance
WHERE
    rnk = 1");
      $statement->execute();
    $result = $statement->fetchAll();
    foreach($result as $row_department_id){
      $department_id=$row_department_id['department_id'];
    }

      //-----------------Employee No------------------------//

      $statement = $connect->prepare("SELECT j.employee_id, j.employee_no, j.join_date, j.employee_status, c.position_id FROM join_status j
        INNER JOIN promotions c ON j.join_id=c.employee_id INNER JOIN (SELECT employee_id, MAX(id) maxid_pro FROM promotions GROUP BY employee_id) d ON c.employee_id = d.employee_id AND c.id = d.maxid_pro
        WHERE j.join_id='".$employee_id."'");
      $statement->execute();              

      $result = $statement->fetchAll();
      foreach($result as $row_employee_no)
      {
        $employee_no = $row_employee_no['employee_no'];
        $employee_id2 = $row_employee_no['employee_id'];
        $join_date = $row_employee_no['join_date'];
        $employee_status = $row_employee_no['employee_status'];
        $position_id = $row_employee_no['position_id'];
      }

      $statement = $connect->prepare("SELECT status FROM payroll_halt WHERE employee_id='".$employee_id."' AND payroll_id='".$_POST['payroll_id']."'");
      $statement->execute();              
      $result = $statement->fetchAll();
      if ($statement->rowCount()>0) {
        foreach($result as $halt_reason)
        {
          $halt_reason = $halt_reason['status'];
        }
      }else{
        $halt_reason = 0;
      }

      if ($employee_status == 0) {
        if ($halt_reason == 2) {        
          $status_payroll=2;
        }else{
          $status_payroll=0;
        }
      }elseif ($employee_status == 1) {
        $status_payroll=2;
      }elseif ($employee_status == 2) {
        if ($halt_reason == 2) {        
          $status_payroll=2;
        }else{
          $status_payroll=0;
        }
      }elseif ($employee_status == 3) {
        $status_payroll=2;
      }

      //-----------------Bank Details------------------------//

    	$statement = $connect->prepare("SELECT id FROM bank_details WHERE employee_id='".$employee_id2."' AND status=0 ORDER BY id DESC LIMIT 1");
      $statement->execute();
      $result = $statement->fetchAll();
      if ($statement->rowCount()>0) {
        foreach($result as $bank_id){
          	$bank_id = $bank_id['id'];
      	}
    	}else{
    		$bank_id='';
    	}

      //------------------shift details-----------------------//
    $statement = $connect->prepare("SELECT shifts FROM shifts_rate a INNER JOIN attendance b ON a.department_id = b.department_id WHERE b.start_date = '".$date_from."' AND b.end_date = '".$date_to."' AND b.employee_id='".$employee_id."' AND (b.attendance_status=0 OR b.attendance_status=2) AND a.status=0 ORDER BY a.id DESC LIMIT 1");
      $statement->execute();
      $result = $statement->fetchAll();
      
      if ($statement->rowCount()>0):
        foreach($result as $row_shifts):

          $dm_new=$row_shifts['shifts'];
        endforeach;
      
      else:

        $month= date('F', strtotime($pay['date_from']));                          
        $statement = $connect->prepare("SELECT shifts FROM shifts WHERE months = '".$month."'");
        $statement->execute();
        $result = $statement->fetchAll();
        foreach($result as $shifts):
          $dm_new = $shifts['shifts'];
        endforeach;



      endif;

    if($pay['type'] == 1){
      $dm = $dm_new;
    }else{
      $dm = $dm_new/2;
    }

      //-----------------12 hrs Shift details------------------------//

    	$statement = $connect->prepare("SELECT COALESCE(sum(a.no_of_shifts * b.position_payment),'0') AS total_amount, COALESCE(sum(a.no_of_shifts),'0') AS total_shifts FROM attendance a 
        INNER JOIN 
        (
            SELECT 
                department_id, 
                position_id, 
                MAX(position_pay_id) AS maxid 
            FROM 
                position_pay 
            GROUP BY 
                department_id, 
                position_id
        ) c 
    ON 
        a.department_id = c.department_id 
        AND a.position_id = c.position_id 
    INNER JOIN 
        position_pay b 
    ON 
        c.maxid = b.position_pay_id  
        WHERE a.start_date = '".$date_from."' AND a.end_date = '".$date_to."' AND a.employee_id='".$employee_id."' AND (a.attendance_status=0 OR a.attendance_status=2) AND a.shifts_type=1");
      $statement->execute();
      $result = $statement->fetchAll();
      foreach($result as $total_shifts){

        $total_amount_12=$total_shifts['total_amount'];
        $total_shifts_12=$total_shifts['total_shifts'];

      }
      //-----------------8 hrs Shift details------------------------//

      $statement = $connect->prepare("SELECT COALESCE(sum(a.no_of_shifts * b.position_payment),'0') AS total_amount, COALESCE(sum(a.no_of_shifts),'0') AS total_shifts FROM attendance a 
        INNER JOIN 
        (
            SELECT 
                department_id, 
                position_id, 
                MAX(position_pay_id) AS maxid 
            FROM 
                position_pay 
            GROUP BY 
                department_id, 
                position_id
        ) c 
    ON 
        a.department_id = c.department_id 
        AND a.position_id = c.position_id 
    INNER JOIN 
        position_pay b 
    ON 
        c.maxid = b.position_pay_id  
        WHERE a.start_date = '".$date_from."' AND a.end_date = '".$date_to."' AND a.employee_id='".$employee_id."' AND (a.attendance_status=0 OR a.attendance_status=2) AND a.shifts_type=2");
      $statement->execute();
      $result = $statement->fetchAll();
      foreach($result as $total_shifts_type){

        $total_amount_8=$total_shifts_type['total_amount'];
        $total_shifts_8=$total_shifts_type['total_shifts'];

      }

      $statement = $connect->prepare("SELECT COALESCE(sum(extra_ot_hrs),'0') AS total_extra FROM attendance WHERE start_date = '".$date_from."' AND end_date = '".$date_to."' AND employee_id='".$employee_id."' AND (attendance_status=0 OR attendance_status=2)");
      $statement->execute();
      $result = $statement->fetchAll();
      foreach($result as $total_extra){

        $total_extra=$total_extra['total_extra'];       

      }

      $statement = $connect->prepare("SELECT COALESCE(poya_day,'0') AS poya_days, COALESCE(m_day,'0') AS m_days, COALESCE(m_ot_hrs,'0') AS m_ot_hrs FROM attendance WHERE start_date = '".$date_from."' AND end_date = '".$date_to."' AND employee_id='".$employee_id."' AND (attendance_status=0 OR attendance_status=2)");
      $statement->execute();
      $result = $statement->fetchAll();
      foreach($result as $total_extra_o){       
        
      }

      if ($total_extra_o['poya_days'] > 0) {
          $poya_days=$total_extra_o['poya_days'];
        }else{
          $poya_days=0;
        }

        if ($total_extra_o['m_days'] > 0) {
          $m_days=$total_extra_o['m_days'];          
        }else{
          $m_days=0;          
        }

        if ($total_extra_o['m_ot_hrs'] > 0) {
          $m_ot_hrss=$total_extra_o['m_ot_hrs'];
        }else{
          $m_ot_hrss=0;
        }


      $attendance_id=array();
      $statement = $connect->prepare("SELECT a.id FROM attendance a JOIN position_pay b ON a.department_id=b.department_id AND a.position_id=b.position_id WHERE a.start_date = '".$date_from."' AND a.end_date = '".$date_to."' AND a.employee_id='".$employee_id."' AND (a.attendance_status=0 OR a.attendance_status=2)");
      $statement->execute();
      $result = $statement->fetchAll();
      foreach($result as $attendance_id_status){
        $attendance_id[]=$attendance_id_status['id'];
      }

      //-----------------Institution shift details------------------------//
		  
      $department = array();
	  	$statement = $connect->prepare("SELECT COALESCE(sum(no_of_shifts),'0') AS total_shifts, department_id, position_id FROM attendance WHERE start_date = '".$date_from."' AND end_date = '".$date_to."' AND employee_id='".$employee_id."' AND position_id!=0 GROUP BY department_id, position_id");
      $statement->execute();
      $result = $statement->fetchAll();
      foreach($result as $attendance){
      	
			 $department[]=array('d_id'=>$attendance['department_id'],"p_id"=>$attendance['position_id'],"t_shifts"=>$attendance['total_shifts']); 
		
      }
 	
      //-----------------Basic Salary Details------------------------//

   		$statement = $connect->prepare("SELECT basic_salary FROM salary WHERE employee_id='".$employee_id."' AND status=0 ORDER BY id DESC");
        	$statement->execute();
         	$result = $statement->fetchAll();      
        	foreach($result as $basic_salary){             

      		$basic_salary = $basic_salary['basic_salary'];
    		}
       
       if ($position_id != 14) {
       
      //-----------------Allowance Details------------------------//

      	$statement = $connect->prepare("SELECT COALESCE(sum(amount),'0') AS allow_amount FROM employee_allowances WHERE employee_id='".$employee_id."' AND (type='".$type."' OR (effective_date BETWEEN '".$date_from."' AND '".$date_to."')) AND allowances_id >= 6");
      	$statement->execute();
      	$result = $statement->fetchAll();
      	foreach($result as $emp_allowances){             
      		$allow_amount=$emp_allowances['allow_amount'];
    	}

    	$allowance = array();
    	$statement = $connect->prepare("SELECT * FROM employee_allowances WHERE employee_id='".$employee_id."' AND (type='".$type."' OR (effective_date BETWEEN '".$date_from."' AND '".$date_to."')) AND allowances_id >= 6");
      	$statement->execute();
      	$result = $statement->fetchAll();
      	foreach($result as $allowances){
      		$allowance[]=array('aid'=>$allowances['allowances_id'],"amount"=>$allowances['amount']); 
      	}

        //-----------------Service Allowance Details------------------------//

        $statement = $connect->prepare("SELECT COALESCE(sum(amount),'0') AS svc_amount FROM employee_allowances WHERE employee_id='".$employee_id."' AND (type='".$type."' OR (effective_date BETWEEN '".$date_from."' AND '".$date_to."')) AND allowances_id=1");
        $statement->execute();
        $result = $statement->fetchAll();
        foreach($result as $service_allowance){             
          $service_allowance=$service_allowance['svc_amount'];
      }

      //-----------------Shifts Allowance Details------------------------//

      $statement = $connect->prepare("SELECT COALESCE(sum(a.no_of_shifts * b.allowance),'0') AS total_amount FROM attendance a INNER JOIN shifts_allowance b ON a.department_id = b.department_id WHERE a.start_date = '".$date_from."' AND a.end_date = '".$date_to."' AND a.employee_id='".$employee_id."' AND (a.attendance_status=0 OR a.attendance_status=2) AND b.status=0");
      $statement->execute();
      $result = $statement->fetchAll();
      foreach($result as $total_shifts_allowance){

        $shifts_allowance_amount=$total_shifts_allowance['total_amount'];
        
      }

      //-----------------Shifts employee Allowance Details------------------------//

      $statement = $connect->prepare("SELECT COALESCE(sum(a.no_of_shifts * b.allowance),'0') AS total_amount FROM attendance a INNER JOIN shifts_emp_allowance b ON a.department_id = b.department_id AND a.position_id=b.position_id WHERE a.start_date = '".$date_from."' AND a.end_date = '".$date_to."' AND a.employee_id='".$employee_id."' AND (a.attendance_status=0 OR a.attendance_status=2) AND b.status=2 ORDER BY b.id DESC LIMIT 1");    

      $statement->execute();
      $result = $statement->fetchAll();
      foreach($result as $row_shifts_all_emp){

        $shifts_allowance_emp_amount=$row_shifts_all_emp['total_amount'];
        
      }

      $statement = $connect->prepare("SELECT allowance FROM shifts_emp_allowance WHERE employee_id='".$employee_id."' AND status=1 ORDER BY id DESC LIMIT 1");
      $statement->execute();
      $result = $statement->fetchAll();
      if ($statement->rowCount()>0){
        foreach($result as $row_shifts_any_emp){

          $shifts_any_emp_amount=$row_shifts_any_emp['allowance'];
          
        }
      }else{
        $shifts_any_emp_amount=0;
      }

      if (!empty($shifts_any_emp_amount)) {
          $total_shifts_any_emp_amount=(int)$shifts_any_emp_amount*(int)$total_shifts;
        }else{
          $total_shifts_any_emp_amount=0;
        }

      //-----------------Shifts Allowance Details------------------------//

      $statement = $connect->prepare("SELECT COALESCE(sum(a.no_of_shifts * b.allowance),'0') AS total_amount FROM attendance a INNER JOIN shifts_allowance_institute b ON a.department_id = b.department_id AND a.position_id=b.position_id WHERE a.start_date = '".$date_from."' AND a.end_date = '".$date_to."' AND a.employee_id='".$employee_id."' AND (a.attendance_status=0 OR a.attendance_status=2) AND b.status=0 AND b.total_shifts <= '".$total_shifts."' ORDER BY b.id DESC LIMIT 1");    

      $statement->execute();
      $result = $statement->fetchAll();
      foreach($result as $row_shifts_institute){

        $shifts_allowance_institute=$row_shifts_institute['total_amount'];
        
      }


      //-----------------Promotion Pay Details------------------------//

      $statement = $connect->prepare("SELECT promotion_pay FROM promotions WHERE employee_id='".$employee_id."' ORDER BY id DESC LIMIT 1");
        $statement->execute();
        $result = $statement->fetchAll();
        foreach($result as $promotion_pay){             
          $promotion_pay=$promotion_pay['promotion_pay'];
        }

        if (!empty($promotion_pay)) {
          $total_promotion_pay=(int)$promotion_pay*(int)$total_shifts;
        }else{
          $total_promotion_pay=0;
        }
        
        $total_service_allowance=(string)$total_promotion_pay+(string)$service_allowance+(string)$shifts_allowance_amount+(string)$shifts_allowance_emp_amount+(string)$total_shifts_any_emp_amount+(string)$shifts_allowance_institute;

      //-----------------Rewards Allowance Details------------------------//

        $statement = $connect->prepare("SELECT COALESCE(sum(amount),'0') AS allow_amount FROM employee_allowances WHERE employee_id='".$employee_id."' AND (type='".$type."' OR (effective_date BETWEEN '".$date_from."' AND '".$date_to."')) AND allowances_id=2");
        $statement->execute();
        $result = $statement->fetchAll();
        foreach($result as $rewards_allowance){             
          $rewards_allowance=$rewards_allowance['allow_amount'];
      }

      //-----------------Chairman Allowance Details------------------------//

        $statement = $connect->prepare("SELECT COALESCE(sum(amount),'0') AS allow_amount FROM employee_allowances WHERE employee_id='".$employee_id."' AND (type='".$type."' OR (effective_date BETWEEN '".$date_from."' AND '".$date_to."')) AND allowances_id=3");
        $statement->execute();
        $result = $statement->fetchAll();
        foreach($result as $chairman_allowance){             
          $chairman_allowance=$chairman_allowance['allow_amount'];
      }

      //-----------------Training and Be Allowance Details------------------------//

        $statement = $connect->prepare("SELECT COALESCE(sum(amount),'0') AS allow_amount FROM employee_allowances WHERE employee_id='".$employee_id."' AND (type='".$type."' OR (effective_date BETWEEN '".$date_from."' AND '".$date_to."')) AND allowances_id=4");
        $statement->execute();
        $result = $statement->fetchAll();
        foreach($result as $training_allowance){             
          $training_allowance=$training_allowance['allow_amount'];
      }

      //-----------------Pending Payments Details------------------------//

        $statement = $connect->prepare("SELECT COALESCE(sum(amount),'0') AS allow_amount FROM employee_allowances WHERE employee_id='".$employee_id."' AND (type='".$type."' OR (effective_date BETWEEN '".$date_from."' AND '".$date_to."')) AND allowances_id=5");
        $statement->execute();
        $result = $statement->fetchAll();
        foreach($result as $pending_allowance){             
          $pending_allowance=$pending_allowance['allow_amount'];
      }

        //-----------------Deduction Details------------------------//

      $statement = $connect->prepare("SELECT COALESCE(sum(amount),'0') AS ded_amount FROM employee_deductions WHERE employee_id='".$employee_id."' AND (type='".$type."' OR (effective_date BETWEEN '".$date_from."' AND '".$date_to."')) AND deduction_id >=5");
      $statement->execute();
      $result = $statement->fetchAll();
      foreach($result as $emp_deductions){             
      	$ded_amount=$emp_deductions['ded_amount'];    
  		}

  		$deduction = array();
      $ded_id = array();
  		$statement = $connect->prepare("SELECT * FROM employee_deductions WHERE employee_id='".$employee_id."' AND (type='".$type."' OR (effective_date BETWEEN '".$date_from."' AND '".$date_to."')) AND deduction_id >=5");
      $statement->execute();
      $result = $statement->fetchAll();
      foreach($result as $deductions){
      	$deduction[]=array('did'=>$deductions['deduction_id'],"amount"=>$deductions['amount']);
        $ded_id[]=$deductions['id'];
      }

      //-----------------Hostel Deductions Details------------------------//

      $statement = $connect->prepare("SELECT COALESCE(sum(amount),'0') AS ded_amount FROM employee_deductions WHERE employee_id='".$employee_id."' AND (type='".$type."' OR (effective_date BETWEEN '".$date_from."' AND '".$date_to."')) AND deduction_id=1 AND (status=0 OR status=1 OR status=2)");
      $statement->execute();
      $result = $statement->fetchAll();
      foreach($result as $hostel_deductions){             
        $hostel_deductions=$hostel_deductions['ded_amount'];    
      }

      $hostel_id=array();
        $statement = $connect->prepare("SELECT id FROM employee_deductions WHERE employee_id='".$employee_id."' AND (type='".$type."' OR (effective_date BETWEEN '".$date_from."' AND '".$date_to."')) AND deduction_id=1 AND (status=0 OR status=1 OR status=2)");
        $statement->execute();
        $result = $statement->fetchAll();        
        foreach($result as $hostel_deductions_id){             
          $hostel_id[]=$hostel_deductions_id['id'];            
        }

      //-----------------Fines Deductions Details------------------------//

      $statement = $connect->prepare("SELECT COALESCE(sum(amount),'0') AS ded_amount FROM employee_deductions WHERE employee_id='".$employee_id."' AND (type='".$type."' OR (effective_date BETWEEN '".$date_from."' AND '".$date_to."')) AND deduction_id=2 AND (status=0 OR status=1 OR status=2)");
      $statement->execute();
      $result = $statement->fetchAll();
      foreach($result as $fines_deductions){             
        $fines_deductions=$fines_deductions['ded_amount'];    
      }

      $fines_id=array();
        $statement = $connect->prepare("SELECT id FROM employee_deductions WHERE employee_id='".$employee_id."' AND (type='".$type."' OR (effective_date BETWEEN '".$date_from."' AND '".$date_to."')) AND deduction_id=2 AND (status=0 OR status=1 OR status=2)");
        $statement->execute();
        $result = $statement->fetchAll();        
        foreach($result as $fines_deductions_id){             
          $fines_id[]=$fines_deductions_id['id'];            
        }

      //-----------------Pending Deductions Details------------------------//

      $statement = $connect->prepare("SELECT COALESCE(sum(amount),'0') AS ded_amount FROM employee_deductions WHERE employee_id='".$employee_id."' AND (type='".$type."' OR (effective_date BETWEEN '".$date_from."' AND '".$date_to."')) AND deduction_id=3");
      $statement->execute();
      $result = $statement->fetchAll();
      foreach($result as $pending_deductions){             
        $pending_deductions=$pending_deductions['ded_amount'];    
      }

      $pending_id=array();
        $statement = $connect->prepare("SELECT id FROM employee_deductions WHERE employee_id='".$employee_id."' AND (type='".$type."' OR (effective_date BETWEEN '".$date_from."' AND '".$date_to."')) AND deduction_id=3 AND (status=0 OR status=1 OR status=2)");
        $statement->execute();
        $result = $statement->fetchAll();        
        foreach($result as $pending_deductions_id){             
          $pending_id[]=$pending_deductions_id['id'];            
        }


        //-----------------Loan Details------------------------//

        $statement = $connect->prepare("SELECT paid_amount, id, loan_id FROM loan_schedules WHERE employee_id='".$employee_id."' AND (date_due BETWEEN '".$date_from."' AND '".$date_to."') AND (status=0 OR status=1 OR status=2)");
        $statement->execute();
        $total_loan_schedules = $statement->rowCount();
        $result = $statement->fetchAll();
        if ($total_loan_schedules > 0) {
          foreach($result as $loan_deductions){             
            $paid_amount=$loan_deductions['paid_amount'];
            $loan_id=$loan_deductions['id'];
            $loan_list_id=$loan_deductions['loan_id'];
          }
        }else{
          $paid_amount=0;
          $loan_id=0;
          $loan_list_id=0;         
        }    

        $statement = $connect->prepare("SELECT loan_amount, id FROM loan_list WHERE employee_id='".$employee_id."' AND id='".$loan_list_id."'");
        $statement->execute();
        $total_loan_list = $statement->rowCount();
        $result = $statement->fetchAll();
        if ($total_loan_list > 0) {
          foreach($result as $loan){             
            $loan_amount=$loan['loan_amount'];          
          }
        }else{
          $loan_amount=0;
        }

        $statement = $connect->prepare("SELECT COALESCE(sum(paid_amount),'0') AS total_paid FROM loan_schedules WHERE employee_id='".$employee_id."' AND loan_id='".$loan_list_id."' AND status=1");
        $statement->execute();
        $total_data_loan_schedules = $statement->rowCount();
        $result = $statement->fetchAll();
        if ($total_data_loan_schedules > 0) {
        foreach($result as $loan_total){             
          $total_paid=$loan_total['total_paid'];          
        }
        }else{
          $total_paid=0;
        }

        $statement = $connect->prepare("SELECT paid_amount FROM loan_schedules WHERE employee_id='".$employee_id."' AND (date_due BETWEEN '".$date_from."' AND '".$date_to."') AND status=1");
        $statement->execute();
        $total_loan_schedules = $statement->rowCount();
        $result = $statement->fetchAll();
        if ($total_loan_schedules > 0) {
          foreach($result as $loan_deductions1){             
            $paid_amount1=$loan_deductions1['paid_amount'];
            
          }
        }else{
          $paid_amount1=0;
              
        }
      
        // $total=(string)$paid_amount+(string)$total_paid-(string)$paid_amount1;

        //-----------------Advance Details------------------------//

        // $statement = $connect->prepare("SELECT amount, id FROM salary_advance WHERE employee_id='".$employee_id."' AND (date_effective BETWEEN '".$date_from."' AND '".$date_to."') AND (status=2 OR status=1)");
        // $statement->execute();
        // $total_advance = $statement->rowCount();
        // $result = $statement->fetchAll();
        // if ($total_advance > 0) {
        //   foreach($result as $advance_deductions){             
        //     $advance_amount=$advance_deductions['amount']; 
        //     $advance_id=$advance_deductions['id'];   
        //   }
        // }else{
        //   $advance_amount=0;
        // } 

        $statement = $connect->prepare("SELECT COALESCE(sum(amount),'0') AS advance_amount FROM salary_advance WHERE employee_id='".$employee_id."' AND (date_effective BETWEEN '".$date_from."' AND '".$date_to."') AND (status=2 OR status=1)");
        $statement->execute();
        $result = $statement->fetchAll();        
        foreach($result as $advance_deductions){             
          $advance_amount=$advance_deductions['advance_amount'];            
        }
        
        $advance_id=array();
        $statement = $connect->prepare("SELECT id FROM salary_advance WHERE employee_id='".$employee_id."' AND (date_effective BETWEEN '".$date_from."' AND '".$date_to."') AND (status=2 OR status=1)");
        $statement->execute();
        $result = $statement->fetchAll();        
        foreach($result as $advance_deductions_id){             
          $advance_id[]=$advance_deductions_id['id'];            
        }

        //-----------------Ration Details------------------------//

        $statement = $connect->prepare("SELECT COALESCE(sum(amount),'0') AS ration_amount FROM ration_deduction WHERE employee_id='".$employee_id."' AND (date_effective BETWEEN '".$date_from."' AND '".$date_to."') AND (status=0 OR status=1 OR status=2)");
        $statement->execute();
        $result = $statement->fetchAll();        
        foreach($result as $ration_deductions){             
          $ration_amount=$ration_deductions['ration_amount'];            
        }
        
        $ration_id=array();
        $statement = $connect->prepare("SELECT id FROM ration_deduction WHERE employee_id='".$employee_id."' AND (date_effective BETWEEN '".$date_from."' AND '".$date_to."') AND (status=0 OR status=1 OR status=2)");
        $statement->execute();
        $result = $statement->fetchAll();        
        foreach($result as $ration_deductions_id){             
          $ration_id[]=$ration_deductions_id['id'];            
        }

        //-----------------Uniform Details------------------------//

        $statement = $connect->prepare("SELECT COALESCE(sum(amount),'0') AS uniform FROM inventory_deduction WHERE employee_id='".$employee_id."' AND (due_date BETWEEN '".$date_from."' AND '".$date_to."')");
        $statement->execute();
        $result = $statement->fetchAll();
        foreach($result as $inventory_deductions){             
          $inventory_amount=$inventory_deductions['uniform'];            
        } 
        
        $uniform_id=array();
        $statement = $connect->prepare("SELECT id FROM inventory_deduction WHERE employee_id='".$employee_id."' AND (due_date BETWEEN '".$date_from."' AND '".$date_to."')");
        $statement->execute();
        $result = $statement->fetchAll();        
        foreach($result as $uniform_deductions_id){             
          $uniform_id[]=$uniform_deductions_id['id'];            
        }

        //-----------------Death Donation------------------------//

        $statement = $connect->prepare("SELECT COALESCE(sum(amount),'0') AS ded_amount FROM employee_deductions WHERE employee_id='".$employee_id."' AND (type='".$type."' OR (effective_date BETWEEN '".$date_from."' AND '".$date_to."')) AND deduction_id=4 AND (status=0 OR status=1)");
        $statement->execute();
        $result = $statement->fetchAll();
        foreach($result as $death_deductions){             
          $death_amount=$death_deductions['ded_amount'];    
        }

        $death_id=array();
        $statement = $connect->prepare("SELECT id FROM employee_deductions WHERE employee_id='".$employee_id."' AND (type='".$type."' OR (effective_date BETWEEN '".$date_from."' AND '".$date_to."')) AND deduction_id=4 AND (status=0 OR status=1)");
        $statement->execute();
        $result = $statement->fetchAll();        
        foreach($result as $death_deductions_id){             
          $death_id[]=$death_deductions_id['id'];            
        }      

        }else{
        $allow_amount=0;
        $total_service_allowance=0;
        $rewards_allowance=0;
        $chairman_allowance=0;
        $training_allowance=0;
        $pending_allowance=0;
        $ded_amount=0;
        $hostel_deductions=0;
        $fines_deductions=0;
        $pending_deductions=0;
        $paid_amount=0;
        $advance_amount=0;
        $ration_amount=0;
        $inventory_amount=0;
        $death_amount=0;
      }
      //-----------------Position Details------------------------//

        $statement = $connect->prepare("SELECT position_id FROM promotions WHERE employee_id='".$employee_id."' ORDER BY id DESC LIMIT 1");
        $statement->execute();
        $total_position = $statement->rowCount();
        $result = $statement->fetchAll();
        if ($total_position > 0) {
          foreach($result as $position_id){             
            $position_id = $position_id['position_id'];
           
          }
        }else{
          $position_id ='';
        }

        //-----------------EPF Details------------------------//
       
        $statement = $connect->prepare("SELECT * FROM epf_excluded WHERE employee_id='".$employee_id."' AND status=0 AND ('".$date_from."' BETWEEN from_date AND to_date) ORDER BY id DESC LIMIT 1");
        $statement->execute();
        $result = $statement->fetchAll();
        if ($statement->rowCount() >0) {
             $epf ='';                   
        }else{
          $statement = $connect->prepare("SELECT epf FROM employee WHERE employee_id='".$employee_id2."'");
          $statement->execute();
          $result = $statement->fetchAll();
          foreach($result as $epf){             
          
            $epf = $epf['epf'];
          }
        }  
        
        //-----------------Poya Day Payment------------------------//
      
      $poya_day_payment=($basic_salary/26)*$poya_days*1.5;

      $poya_day_payment1=($basic_salary/26)*$poya_days*0.5;

      //-----------------M Day Payment------------------------//
      
      $m_payment=($basic_salary/26)*$m_days*2;

      $m_payment1=($basic_salary/26)*$m_days*1;

      //-----------------Over Time x (3)------------------------//
      
      $m_ot_payment=($basic_salary/200)*3*$m_ot_hrss;

      $total_shifts=(string)$total_shifts_12+(string)$total_shifts_8;

      $poya_minus=$total_shifts-$poya_days-$m_days;
        if ($poya_minus < $dm) {
          $working_salary=(($basic_salary/$dm)*$poya_minus)+$poya_day_payment+$m_payment;          
        }else{
          $working_salary=$basic_salary;
        }
        
        if ($total_shifts >= 10){
          if ($epf==1) {
          $epf_8=($working_salary/100)*8;
          $epf_12=($working_salary/100)*12;
          $etf_3=($working_salary/100)*3;
        }else{
          $epf_8=0;
          $epf_12=0;
          $etf_3=0;
        }
      }else{
        $epf_8=0;
        $epf_12=0;
        $etf_3=0;
      }
       
        //-----------------ot------------------------//
        $ot_hrs=($total_shifts_12-$m_days)*3;        
        $ot_amount=(($basic_salary/200)*1.5)*$ot_hrs;

        $extra_ot_amount=(($basic_salary/200)*1.5)*$total_extra;                      

        //-----------------Absent Details------------------------//

        if ($dm-$total_shifts > 0) {
          $absent_day=$dm-$total_shifts;
          $absent_amount=round(($basic_salary/$dm)*$absent_day, 2);
        }else{
          $absent_day=0;
          $absent_amount=0;
        }

        // if (($join_date >= $date_from) && ($join_date <=$date_to)) {
        //   $absent_day=0;
        //   $absent_amount=0;
        // }else{
        //   if ($dm-$total_shifts > 0) {
        //     $absent_day=$dm-$total_shifts;
        //     $absent_amount=round(($basic_salary/$dm)*$absent_day, 2);
        //   }else{
        //     $absent_day=0;
        //     $absent_amount=0;
        //   }
        // }   
        
        $total_amount=(string)$total_amount_12+(string)$total_amount_8;

        $total_allowance=(string)$extra_ot_amount+(string)$total_service_allowance+(string)$rewards_allowance+(string)$chairman_allowance+(string)$training_allowance+(string)$pending_allowance+(string)$allow_amount;
        
        if (($poya_days >0) OR ($m_days >0)) {
          $incentive=(string)$total_amount-(string)$basic_salary-(string)$ot_amount-(string)$m_ot_payment-(string)$poya_day_payment1-(string)$m_payment1+(string)$absent_amount;
          $gross=(string)$basic_salary+(string)$ot_amount+(string)$incentive+(string)$total_allowance+(string)$m_ot_payment+(string)$poya_day_payment1+(string)$m_payment1;
        }else{
          $incentive=(string)$total_amount-(string)$working_salary-(string)$ot_amount;
          $gross=(string)$basic_salary+(string)$ot_amount+(string)$incentive+(string)$total_allowance;
        }

        $level_one_deduction=(string)$epf_8+(string)$absent_amount+(string)$death_amount+(string)$advance_amount;

      	$total_deduction=(string)$hostel_deductions+(string)$fines_deductions+(string)$pending_deductions+(string)$epf_8+(string)$absent_amount+(string)$paid_amount+(string)$advance_amount+(string)$ration_amount+(string)$inventory_amount+(string)$death_amount+(string)$ded_amount;

      	$net=(string)$gross-(string)$total_deduction;

        $level_one_net=(string)$gross-(string)$level_one_deduction;

// Check Hostel and deduct
        if ($level_one_net >= $hostel_deductions) {
          $level_two_net=(string)$level_one_net-(string)$hostel_deductions;
          $hostel_status=1;
          $hostel_ded=$hostel_deductions;
          $level_two_deduction=(string)$level_one_deduction+(string)$hostel_deductions;
        }else{
          $level_two_net=(string)$level_one_net;
          $hostel_status=2;
          $hostel_ded=0;
          $level_two_deduction=(string)$level_one_deduction;
        }
// Check fines and deduct
        if ($level_two_net >= $fines_deductions) {
          $level_three_net=(string)$level_two_net-(string)$fines_deductions;
          $fines_status=1;
          $fines_ded=$fines_deductions;
          $level_three_deduction=(string)$level_two_deduction+(string)$fines_deductions;
        }else{
          $level_three_net=(string)$level_two_net;
          $fines_status=2;
          $fines_ded=0;
          $level_three_deduction=(string)$level_two_deduction;
        }
// Check ration and deduct
        if ($level_three_net >= $ration_amount) {
          $level_four_net=(string)$level_three_net-(string)$ration_amount;
          $ration_status=1;
          $ration_ded=$ration_amount;
          $level_four_deduction=(string)$level_three_deduction+(string)$ration_amount;
        }else{
          $level_four_net=(string)$level_three_net;
          $ration_status=2;
          $ration_ded=0;
          $level_four_deduction=(string)$level_three_deduction;
        }
// Check uniform and deduct
        if ($level_four_net >= $inventory_amount) {
          $level_five_net=(string)$level_four_net-(string)$inventory_amount;
          $inventory_status=1;
          $inventory_ded=$inventory_amount;
          $level_five_deduction=(string)$level_four_deduction+(string)$inventory_amount;
        }else{
          $level_five_net=(string)$level_four_net;
          $inventory_status=2;
          $inventory_ded=0;
          $level_five_deduction=(string)$level_four_deduction;
        }

// Check Loan and deduct

        if ($level_five_net >= $paid_amount) {
          $level_six_net=(string)$level_five_net-(string)$paid_amount;
          $loan_schedules_status=1;
          $loan_ded=$paid_amount;
          $loan_status=3;
          $level_six_deduction=(string)$level_five_deduction+(string)$paid_amount;
          $total=(string)$paid_amount+(string)$total_paid-(string)$paid_amount1;
        }else{
          $level_six_net=(string)$level_five_net;
          $loan_schedules_status=2;
          $loan_ded=0;
          $loan_status=2;
          $level_six_deduction=(string)$level_five_deduction;
          $total=(string)$total_paid-(string)$paid_amount1;
        }

// Check Pending deduct
        if ($level_six_net >= $pending_deductions) {
          $level_seven_net=(string)$level_six_net-(string)$pending_deductions;
          $pending_status=1;
          $pending_ded=$pending_deductions;
          $level_seven_deduction=(string)$level_six_deduction+(string)$pending_deductions;
        }else{
          $level_seven_net=(string)$level_six_net;
          $pending_status=2;
          $pending_ded=0;
          $level_seven_deduction=(string)$level_six_deduction;
        }

  // Check deduct
        if ($level_seven_net >= $ded_amount) {
          $level_eight_net=(string)$level_seven_net-(string)$ded_amount;
          $ded_status=1;
          $deductions=$deduction;
          $level_eight_deduction=(string)$level_seven_deduction+(string)$ded_amount;
        }else{
          $level_eight_net=(string)$level_seven_net;
          $ded_status=2;
          $deductions=array();
          $level_eight_deduction=(string)$level_seven_deduction;
        }

		$data = array(
      ':p_id'               => $sno ++,
  		':payroll_id'  	      => trim($_POST["payroll_id"]),
  		':employee_id'        => $employee_id,
  		':employee_no'        => $employee_no,
  		':position_id'        => $position_id,
			':bank_id'            => $bank_id,
  		':no_of_shift'        => $total_shifts,
      ':department_id'      => $department_id,
  		':department'         => json_encode($department),
  		':basic_salary'  	    => $basic_salary,
      ':basic_epf'          => $working_salary,
  		':ot_hrs'  	          => $ot_hrs,
  		':ot_amount'  	      => $ot_amount,
  		':sot_hrs'  	        => $total_extra,
	  	':sot_amount'  	      => $extra_ot_amount,
      ':poya_days'          => $poya_days,
      ':m_days'             => $m_days,
      ':m_ot_hrs'           => $m_ot_hrss,
      ':poya_day_payment'   => $poya_day_payment1,
      ':m_payment'          => $m_payment1,
      ':m_ot_payment'       => $m_ot_payment,
	  	':incentive'  	      => $incentive,
      ':service_allowance'  => $total_service_allowance,
      ':rewards'            => $rewards_allowance,
      ':chairman_allowance' => $chairman_allowance,
      ':training_be'        => $training_allowance,
      ':pending_payments'   => $pending_allowance,
      ':allowance_amount'  	=> $total_allowance,
	  	':allowances'  	      => json_encode($allowance),
	  	':gross'  	          => $gross,
	  	':absent_day'  	      => $absent_day,
	  	':absent_amount'  	  => $absent_amount,
	  	':employee_epf'  	    => $epf_8,
			':deduction_amount'  	=> $level_eight_deduction,
			':deductions'  	      => json_encode($deductions),
      ':hostel'             => $hostel_ded,
      ':fines'              => $fines_ded,
      ':pending_deductions' => $pending_ded,
			':employer_epf'  	    => $epf_12,
			':employer_etf'  	    => $etf_3,
			':net'  	            => $level_eight_net,
			':id'  	              => $_POST["payroll_id"],
			':status'  	          => 1,
      ':status_pay'         => $status_payroll,
      ':loan_amount'        => $loan_ded,
      ':inventory_amount'   => $inventory_ded,
      ':advance_amount'     => $advance_amount,
      ':ration_amount'      => $ration_ded,
      ':death_donation'     => $death_amount,	  		
	 	);

	 	$query = "
	 	INSERT INTO payroll_items 
	 	(id, payroll_id, employee_id, employee_no, position_id, bank_id, no_of_shift, department_id, department, basic_salary, basic_epf, ot_hrs, ot_amount, sot_hrs, sot_amount, poya_days, m_days, m_ot_hrs, poya_day_payment, m_payment, m_ot_payment, incentive, service_allowance, rewards, chairman_allowance, training_be, pending_payments, allowance_amount, allowances, gross, absent_day, absent_amount, employee_epf, deduction_amount, deductions, hostel, fines, pending_deductions, loan_amount, advance_amount, ration_amount, inventory_amount, death_donation, employer_epf, employer_etf, net, status) 
	 	VALUES (:p_id, :payroll_id, :employee_id, :employee_no, :position_id, :bank_id, :no_of_shift, :department_id, :department, :basic_salary, :basic_epf, :ot_hrs, :ot_amount, :sot_hrs, :sot_amount, :poya_days, :m_days, :m_ot_hrs, :poya_day_payment, :m_payment, :m_ot_payment, :incentive, :service_allowance, :rewards, :chairman_allowance, :training_be, :pending_payments, :allowance_amount, :allowances, :gross, :absent_day, :absent_amount, :employee_epf, :deduction_amount, :deductions, :hostel, :fines, :pending_deductions, :loan_amount, :advance_amount, :ration_amount, :inventory_amount, :death_donation, :employer_epf, :employer_etf, :net, :status_pay);
	 	UPDATE payroll SET status=:status WHERE id=:id;
    
	 	";

    if ($loan_amount == $total){
      $query .= "UPDATE loan_list SET status='".$loan_status."' WHERE id='".$loan_list_id."';";
    }

    if ($loan_id > 0){
      $query .= "UPDATE loan_schedules SET status='".$loan_schedules_status."' WHERE id='".$loan_id."';";
    }

	 	$statement = $connect->prepare($query);

	 	$statement->execute($data);
    
    //-----------------Attendance Details------------------------//

    for ($k = 0; $k < count($attendance_id); $k++) {  

      $data_attendance = array(
        ':id'     =>  $attendance_id[$k],
        ':status' =>  2,
      );

      $query_attendance = "
      UPDATE `attendance` SET `attendance_status`=:status WHERE `id`=:id    
      ";
        
      $statement = $connect->prepare($query_attendance);
      $statement->execute($data_attendance);
    }

    //-----------------salary_advance------------------------//

    for ($l = 0; $l < count($advance_id); $l++) {  

      $data_advance = array(
        ':id'     =>  $advance_id[$l],
        ':status' =>  1,
      );

      $query_advance = "
      UPDATE `salary_advance` SET `status`=:status WHERE `id`=:id    
      ";
        
      $statement = $connect->prepare($query_advance);
      $statement->execute($data_advance);
    }

    //-----------------Uniform Details------------------------//

    for ($j = 0; $j < count($uniform_id); $j++) {  

      $data_uniform = array(
        ':id'     =>  $uniform_id[$j],
        ':status' =>  $inventory_ded,
      );

      $query_uniform = "
      UPDATE `inventory_deduction` SET `status`=:status WHERE `id`=:id    
      ";
        
      $statement = $connect->prepare($query_uniform);
      $statement->execute($data_uniform);
    }
    //-----------------Ration Details------------------------//

    for ($l = 0; $l < count($ration_id); $l++) {  

      $data_ration = array(
        ':id'     =>  $ration_id[$l],
        ':status' =>  $ration_status,
      );

      $query_ration = "
      UPDATE `ration_deduction` SET `status`=:status WHERE `id`=:id    
      ";
        
      $statement = $connect->prepare($query_ration);
      $statement->execute($data_ration);
    }

    //-----------------Hostel Details------------------------//

    for ($m = 0; $m < count($hostel_id); $m++) {  

      $data_hostel = array(
        ':id'     =>  $hostel_id[$m],
        ':status' =>  $hostel_status,
      );

      $query_hostel = "
      UPDATE `employee_deductions` SET `status`=:status WHERE `id`=:id    
      ";
        
      $statement = $connect->prepare($query_hostel);
      $statement->execute($data_hostel);
    }

    //-----------------Fines Details------------------------//

    for ($n = 0; $n < count($fines_id); $n++) {  

      $data_fines = array(
        ':id'     =>  $fines_id[$n],
        ':status' =>  $fines_status,
      );

      $query_fines = "
      UPDATE `employee_deductions` SET `status`=:status WHERE `id`=:id    
      ";
        
      $statement = $connect->prepare($query_fines);
      $statement->execute($data_fines);
    }

    //-----------------Pending Details------------------------//

    for ($r = 0; $r < count($pending_id); $r++) {  

      $data_pending = array(
        ':id'     =>  $pending_id[$r],
        ':status' =>  $pending_status,
      );

      $query_pending = "
      UPDATE `employee_deductions` SET `status`=:status WHERE `id`=:id    
      ";
        
      $statement = $connect->prepare($query_pending);
      $statement->execute($data_pending);
    }

    //-----------------Deduction Details------------------------//

    for ($s = 0; $s < count($ded_id); $s++) {  

      $data_ded = array(
        ':id'     =>  $ded_id[$s],
        ':status' =>  $ded_status,
      );

      $query_ded = "
      UPDATE `employee_deductions` SET `status`=:status WHERE `id`=:id    
      ";
        
      $statement = $connect->prepare($query_ded);
      $statement->execute($data_ded);
    }

    //-----------------Death Details------------------------//

    for ($q = 0; $q < count($death_id); $q++) {  

      $data_death = array(
        ':id'     =>  $death_id[$q],
        ':status' =>  1,
      );

      $query_death = "
      UPDATE `employee_deductions` SET `status`=:status WHERE `id`=:id    
      ";
        
      $statement = $connect->prepare($query_death);
      $statement->execute($data_death);
    }


	}

//-----------------report income------------------------//
  //-----------------Attendance------------------------//

  $statement = $connect->prepare("SELECT id FROM reports_income ORDER BY id DESC LIMIT 1");
    $statement->execute();
    $result = $statement->fetchAll();
    if ($statement->rowCount()>0) {        
      foreach($result as $row_id_income){
        $startpoint1 = $row_id_income['id'];        
      }
    }
    else{
      $startpoint1 = 0;
    }
    
    $sno1 = $startpoint1 + 1;

    $statement = $connect->prepare("SELECT department_id FROM attendance WHERE start_date = '".$date_from."' AND end_date = '".$date_to."' AND (attendance_status=0 OR attendance_status=2) GROUP BY department_id");
    $statement->execute();
    $result = $statement->fetchAll();
    foreach($result as $department_id){
      $department_id = $department_id['department_id'];

      $statement = $connect->prepare("SELECT sector_id FROM department WHERE department_id='".$department_id."'");
      $statement->execute();
      $result = $statement->fetchAll();
      foreach($result as $sector_id){

        $sector_id=$sector_id['sector_id'];        
       
      }

      $statement = $connect->prepare("SELECT COALESCE(sum(a.no_of_shifts * b.payment),'0') AS total_invoice_amount, COALESCE(sum(a.no_of_shifts),'0') AS total_invoice_shifts FROM invoice a INNER JOIN invoice_rate b ON a.department_id = b.department_id AND a.position_id = b.position_id WHERE (a.date_effective BETWEEN '".$date_from."' AND '".$date_to."') AND a.department_id='".$department_id."'");
      $statement->execute();
      $result = $statement->fetchAll();
      foreach($result as $total_invoice_shifts){

        $total_invoice_amount=$total_invoice_shifts['total_invoice_amount'];
        $total_invoice_shifts=$total_invoice_shifts['total_invoice_shifts'];              
       
      }


      $statement = $connect->prepare("SELECT COALESCE(sum(a.no_of_shifts * b.position_payment),'0') AS total_amount, COALESCE(sum(a.no_of_shifts),'0') AS total_shifts FROM attendance a INNER JOIN position_pay b ON a.department_id = b.department_id AND a.position_id = b.position_id WHERE a.start_date = '".$date_from."' AND a.end_date = '".$date_to."' AND a.department_id='".$department_id."' AND (a.attendance_status=0 OR a.attendance_status=2)");
      $statement->execute();
      $result = $statement->fetchAll();
      foreach($result as $total_department_shifts){

        $total_department_amount=$total_department_shifts['total_amount'];
        $total_department_shifts=$total_department_shifts['total_shifts'];              
       
      }

      $epf_8=array();
      $epf_12=array();
      $etf_3=array();

      $statement = $connect->prepare("SELECT employee_id FROM attendance WHERE start_date = '".$date_from."' AND end_date = '".$date_to."' AND department_id='".$department_id."' AND (attendance_status=0 OR attendance_status=2)");
      $statement->execute();
      $result = $statement->fetchAll();
      foreach($result as $employee_id){
        $employee_id = $employee_id['employee_id'];

        $statement = $connect->prepare("SELECT COALESCE(sum(no_of_shifts),'0') AS emp_shifts FROM attendance WHERE start_date = '".$date_from."' AND end_date = '".$date_to."' AND employee_id ='".$employee_id."' AND department_id ='".$department_id."' AND (attendance_status=0 OR attendance_status=2)");
        $statement->execute();
        $result = $statement->fetchAll();
        foreach($result as $emp_shifts){

          $emp_shifts=$emp_shifts['emp_shifts'];        

        }

        //-----------------Basic Salary Details------------------------//

      $statement = $connect->prepare("SELECT basic_salary FROM salary WHERE employee_id='".$employee_id."' AND status=0 ORDER BY id DESC");
          $statement->execute();
          $result = $statement->fetchAll();      
          foreach($result as $basic_salary){             

          $basic_salary = $basic_salary['basic_salary'];
        }


        $statement = $connect->prepare("SELECT epf FROM employee WHERE employee_id='".$employee_id."'");
        $statement->execute();
        $result = $statement->fetchAll();
        foreach($result as $epf){             
          
          $epf = $epf['epf'];
        }
        
        $poya_minus_i=$emp_shifts-$poya_days-$m_days;

        if ($emp_shifts < $dm) {
          $working_salary=($basic_salary/$dm)*$emp_shifts;          
        }else{
          $working_salary=$basic_salary;
        }        
        
          if ($epf==1) {
          $epf_8[]=($working_salary/100)*8;
          $epf_12[]=($working_salary/100)*12;
          $etf_3[]=($working_salary/100)*3;
        }
    
      }

      $epf_8_sum=array_sum($epf_8);
      $epf_12_sum=array_sum($epf_12);
      $etf_3_sum=array_sum($etf_3);

      $data = array(
        ':id'    => $sno1 ++,
        ':payroll_id'   => trim($_POST["payroll_id"]),
        ':sector_id'    => $sector_id,
        ':department_id'    => $department_id,
        ':invoice_amount'    => $total_invoice_amount,
        ':gross_amount'    => $total_department_amount,
        ':employer_epf'    => $epf_12_sum,
        ':employer_etf'  =>  $etf_3_sum,
        ':employee_epf'   => $epf_8_sum,                
    );

    $query = "
    INSERT INTO reports_income(id, payroll_id, sector_id, department_id, invoice_amount, gross_amount, employer_epf, employer_etf, employee_epf)
    VALUES (:id, :payroll_id, :sector_id, :department_id, :invoice_amount, :gross_amount, :employer_epf, :employer_etf, :employee_epf)
    ";

    $statement = $connect->prepare($query);

    $statement->execute($data);   

}

//-----------------reports_position_pay------------------------//

  $statement = $connect->prepare("SELECT id FROM reports_position_pay ORDER BY id DESC LIMIT 1");
    $statement->execute();
    $result = $statement->fetchAll();
    if ($statement->rowCount()>0) {        
      foreach($result as $row_id_p){
        $startpoint2 = $row_id_p['id'];        
      }
    }
    else{
      $startpoint2 = 0;
    }
    
    $sno2 = $startpoint2 + 1;


    $statement = $connect->prepare("SELECT department_id FROM position_pay GROUP BY department_id");
    $statement->execute();
    $result = $statement->fetchAll();
    foreach($result as $row){             
      
      $statement = $connect->prepare("SELECT position_payment FROM position_pay WHERE department_id='".$row['department_id']."' AND position_id=1");
      $statement->execute();
      $result = $statement->fetchAll();
      if ($statement->rowCount()>0) {
        foreach($result as $cso){
          $cso=$cso['position_payment'];
        }
      }else{
        $cso='';
      }
      
      $statement = $connect->prepare("SELECT position_payment FROM position_pay WHERE department_id='".$row['department_id']."' AND position_id=2");
      $statement->execute();
      $result = $statement->fetchAll();
      if ($statement->rowCount()>0) {
        foreach($result as $oic){
          $oic=$oic['position_payment'];
        }
      }else{
        $oic='';
      }

      $statement = $connect->prepare("SELECT position_payment FROM position_pay WHERE department_id='".$row['department_id']."' AND position_id=3");
      $statement->execute();
      $result = $statement->fetchAll();
      if ($statement->rowCount()>0) {
        foreach($result as $sso){
          $sso=$sso['position_payment'];
        }
      }else{
        $sso='';
      }

      $statement = $connect->prepare("SELECT position_payment FROM position_pay WHERE department_id='".$row['department_id']."' AND position_id=4");
      $statement->execute();
      $result = $statement->fetchAll();
      if ($statement->rowCount()>0) {
        foreach($result as $loic){
          $loic=$loic['position_payment'];
        }
      }else{
        $loic='';
      }

      $statement = $connect->prepare("SELECT position_payment FROM position_pay WHERE department_id='".$row['department_id']."' AND position_id=5");
      $statement->execute();
      $result = $statement->fetchAll();
      if ($statement->rowCount()>0) {
        foreach($result as $jso){
          $jso=$jso['position_payment'];
        }
      }else{
        $jso='';
      }

      $statement = $connect->prepare("SELECT position_payment FROM position_pay WHERE department_id='".$row['department_id']."' AND position_id=6");
      $statement->execute();
      $result = $statement->fetchAll();
      if ($statement->rowCount()>0) {
        foreach($result as $lso){
          $lso=$lso['position_payment'];
        }
      }else{
        $lso='';
      }

      $statement = $connect->prepare("SELECT position_payment FROM position_pay WHERE department_id='".$row['department_id']."' AND position_id=7");
      $statement->execute();
      $result = $statement->fetchAll();
      if ($statement->rowCount()>0) {
        foreach($result as $lsso){
          $lsso=$lsso['position_payment'];
        }
      }else{
        $lsso='';
      }

      $statement = $connect->prepare("SELECT position_payment FROM position_pay WHERE department_id='".$row['department_id']."' AND position_id=8");
      $statement->execute();
      $result = $statement->fetchAll();
      if ($statement->rowCount()>0) {
        foreach($result as $asco){
          $asco=$asco['position_payment'];
        }
      }else{
        $asco='';
      }
   

    $data = array(
      ':id'    => $sno2 ++,
      ':payroll_id'   => trim($_POST["payroll_id"]),
      ':department_id'    => $row['department_id'],
      ':cso'    => $cso,
      ':oic'    => $oic,
      ':sso'    => $sso,
      ':loic'    => $loic,
      ':jso'    => $jso,
      ':lso'    => $lso,
      ':lsso'    => $lsso,
      ':asco'    => $asco,
                            
    );

    $query = "
    INSERT INTO reports_position_pay(id, payroll_id, department_id, cso, oic, sso, loic, jso, lso, lsso, asco)
    VALUES (:id, :payroll_id, :department_id, :cso, :oic, :sso, :loic, :jso, :lso, :lsso, :asco)
    ";

    $statement = $connect->prepare($query);

    $statement->execute($data);
  }

  //-----------------reports_department------------------------//

  $statement = $connect->prepare("SELECT id FROM reports_department ORDER BY id DESC LIMIT 1");
    $statement->execute();
    $result = $statement->fetchAll();
    if ($statement->rowCount()>0) {        
      foreach($result as $row_id_d){
        $startpoint3 = $row_id_d['id'];        
      }
    }
    else{
      $startpoint3 = 0;
    }
    
    $sno3 = $startpoint3 + 1;

    $statement = $connect->prepare("SELECT department_id, position_id FROM attendance 
      WHERE start_date = '".$date_from."' AND end_date = '".$date_to."' AND (attendance_status=0 OR attendance_status=2) GROUP BY department_id, position_id");
    $statement->execute();
    $result = $statement->fetchAll();
    foreach($result as $row){

    $statement = $connect->prepare("SELECT COALESCE(sum(a.no_of_shifts * b.payment),'0') AS total_invoice_amount, COALESCE(sum(a.no_of_shifts),'0') AS invoice_shift, b.payment FROM invoice a 
      LEFT JOIN invoice_rate b ON a.department_id = b.department_id AND a.position_id = b.position_id
      WHERE a.department_id='".$row['department_id']."' AND a.position_id='".$row['position_id']."' AND a.date_effective BETWEEN '".$date_from."' AND '".$date_to."'");
    $statement->execute();
    $result = $statement->fetchAll();
    foreach($result as $row_invoice):

    endforeach;

    $statement = $connect->prepare("SELECT COALESCE(sum(a.no_of_shifts * b.position_payment),'0') AS total_working_amount, COALESCE(sum(a.no_of_shifts),'0') AS working_shift, b.position_payment FROM attendance a 
      LEFT JOIN position_pay b ON a.department_id = b.department_id AND a.position_id = b.position_id
      WHERE a.department_id='".$row['department_id']."' AND a.position_id='".$row['position_id']."' AND a.start_date = '".$date_from."' AND a.end_date = '".$date_to."' AND (a.attendance_status=0 OR a.attendance_status=2)");
    $statement->execute();
    $result = $statement->fetchAll();
    foreach($result as $row_working):

    endforeach;

    $statement = $connect->prepare("SELECT no_of_shifts FROM to_be_applied WHERE department_id='".$row['department_id']."' AND position_id='".$row['position_id']."'");
    $statement->execute();
    $result = $statement->fetchAll();
    if ($statement->rowCount()>0) :
    
      foreach($result as $row_to_be):
        $to_be_applied_shifts=$row_to_be['no_of_shifts'];
      endforeach;
    else:
      $to_be_applied_shifts=0;
    endif;
    

    if ($row_invoice['payment']>0):
      $invoice_rate=$row_invoice['payment'];
    else:
      $invoice_rate=0;
    endif;

    if ($row_working['position_payment']>0):
      $working_rate=$row_working['position_payment'];
    else:
      $working_rate=0;
    endif;

    $data = array(
      ':id'    => $sno3 ++,
      ':payroll_id'           => trim($_POST["payroll_id"]),
      ':department_id'        => $row['department_id'],
      ':position_id'          => $row['position_id'],
      ':to_be_applied_shift'  => $to_be_applied_shifts,
      ':invoice_shift'        => $row_invoice['invoice_shift'],
      ':invoice_rate'         => $invoice_rate,
      ':invoice_total'        => $row_invoice['total_invoice_amount'],
      ':working_shift'        => $row_working['working_shift'],
      ':working_rate'         => $working_rate,
      ':working_total'        => $row_working['total_working_amount'],      
    );

    $query = "
    INSERT INTO reports_department(id, payroll_id, department_id, position_id, to_be_applied_shift, invoice_shift, invoice_rate, invoice_total, working_shift, working_rate, working_total)
    VALUES (:id, :payroll_id, :department_id, :position_id, :to_be_applied_shift, :invoice_shift, :invoice_rate, :invoice_total, :working_shift, :working_rate, :working_total)
    ";

    $statement = $connect->prepare($query);

    $statement->execute($data);
  }

 echo 'done';
 
}

?>
