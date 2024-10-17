<?php

//process.php
include 'config.php';
/*$connect = new PDO("mysql:host=localhost; dbname=testing", "root", "");*/
$connect = pdoConnection();
if(isset($_POST["payroll_id"]))
{
  
  // Start building the permission array with payroll_id
  $delete_permision = array(
    ':payroll_id' => $_POST['payroll_id']
  );

  // Initialize query
  $query = "DELETE FROM `d_payroll_items` WHERE `payroll_id` = :payroll_id";

  // If filter_institution is provided, add department filter
  if (isset($_POST["filter_institution"])) {
      $query .= ' AND `department` = :department';
      // Add department to the permission array
      $delete_permision[':department'] = $_POST['filter_institution'];
  }

  // Prepare and execute the query
  $statement = $connect->prepare($query);
  $statement->execute($delete_permision);

  $statement = $connect->prepare("SELECT date_from, date_to, type FROM d_payroll WHERE id ='".$_POST["payroll_id"]."'");
    $statement->execute();
    $result = $statement->fetchAll();
    foreach($result as $pay):
  
      $date_from=$pay['date_from'];
      $date_to=$pay['date_to'];
      $type=$pay['type'];
    
    endforeach;

    $statement = $connect->prepare("SELECT id FROM d_payroll_items ORDER BY id DESC LIMIT 1");
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

    $attendance_id=array();
    //-----------------Attendance------------------------//
    $query = "SELECT a.id, a.employee_id, COALESCE((a.no_of_shifts * b.position_payment),'0') AS total_amount, COALESCE(a.no_of_shifts,'0') AS total_shifts, COALESCE(a.poya_day,'0') AS poya_days, COALESCE(a.m_day,'0') AS m_days, COALESCE(a.m_ot_hrs,'0') AS m_ot_hrss, COALESCE(a.total_ot_hrs,'0') AS total_ot_hrss, COALESCE(a.extra_ot_hrs,'0') AS extra_ot_hrs, a.department_id FROM d_attendance a 
      INNER JOIN d_position_pay b ON a.department_id = b.department_id AND a.position_id = b.position_id 
      WHERE a.start_date = '".$date_from."' AND a.end_date = '".$date_to."' AND (a.attendance_status=0 OR a.attendance_status=2)";

    if (isset($_POST["filter_institution"])) {
      $query .= " AND a.department_id = '".$_POST["filter_institution"]."'";
    }

    $statement = $connect->prepare($query);


    $statement->execute();
    $result = $statement->fetchAll();
    foreach($result as $rows){
      $employee_id = $rows['employee_id'];
      $total_shifts=$rows['total_shifts'];
      $total_amount=$rows['total_amount'];
      $attendance_id[]=$rows['id'];     
    
      //-----------------Employee No------------------------//

      $statement = $connect->prepare("SELECT employee_id, employee_no, join_date FROM join_status WHERE join_id='".$employee_id."'");
      $statement->execute();              

      $result = $statement->fetchAll();
      foreach($result as $row_employee_no)
      {
        $employee_no = $row_employee_no['employee_no'];
        $employee_id2 = $row_employee_no['employee_id'];
        $join_date = $row_employee_no['join_date'];
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

      //-----------------Basic Salary Details------------------------//

      $statement = $connect->prepare("SELECT basic_salary FROM salary WHERE employee_id='".$employee_id."' AND status=0 ORDER BY id DESC");
          $statement->execute();
          $result = $statement->fetchAll();      
          foreach($result as $basic_salary){             

          $basic_salary = $basic_salary['basic_salary'];
        }

      //-----------------arrears payment------------------------//

        $statement = $connect->prepare("SELECT COALESCE(sum(amount),'0') AS svc_amount FROM d_employee_allowances WHERE employee_id='".$employee_id."' AND (type='".$type."' OR (effective_date BETWEEN '".$date_from."' AND '".$date_to."')) AND allowances_id=1");
        $statement->execute();
        $result = $statement->fetchAll();
        foreach($result as $arrears_payment){             
          $arrears_payment=$arrears_payment['svc_amount'];
      }

      //------------------shift details-----------------------//
      $statement = $connect->prepare("SELECT shifts FROM d_shifts_rate_max a INNER JOIN d_attendance b ON a.department_id = b.department_id AND a.position_id = b.position_id WHERE b.start_date = '".$date_from."' AND b.end_date = '".$date_to."' AND b.employee_id='".$employee_id."' AND (b.attendance_status=0 OR b.attendance_status=2) AND a.status=0 ORDER BY a.id DESC LIMIT 1");
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

      //-----------------Half Days------------------------//

      $statement = $connect->prepare("SELECT * FROM d_half_days a INNER JOIN d_attendance b ON a.department_id = b.department_id AND a.position_id = b.position_id WHERE b.start_date = '".$date_from."' AND b.end_date = '".$date_to."' AND b.employee_id='".$employee_id."' AND (b.attendance_status=0 OR b.attendance_status=2) AND a.status=0 ORDER BY a.id DESC LIMIT 1");
      $statement->execute();
      $result = $statement->fetchAll();
      
      if ($statement->rowCount()>0):
        
        if ($total_shifts >=26):
          $h_days=4;
        elseif ($total_shifts >=20 && $total_shifts<=25):
          $h_days=3;
        elseif ($total_shifts >=13 && $total_shifts<=19):
          $h_days=2;
        elseif ($total_shifts >=6 && $total_shifts<=12):
          $h_days=1; 
        else:
          $h_days=0;
        endif;
      
      else:

        $h_days=0;

      endif;

      $h_ot_hrs=$h_days*6;

      $h_days_payment=($basic_salary/200)*1.5*($h_ot_hrs);

      if ($rows['m_days'] > 0) {
        $m_days=$rows['m_days'];
      }else{
        $m_days=0;
      }

      if ($rows['poya_days'] > 0) {
        $poya_days=$rows['poya_days'];
      }else{
        $poya_days=0;
      }

      if ($rows['m_ot_hrss'] > 0) {
        $m_ot_hrss=$rows['m_ot_hrss'];
      }else{
        $m_ot_hrss=0;
      }

      //-----------------Poya Day Payment------------------------//
      
      $poya_day_payment=($basic_salary/26)*$poya_days*1.5;

      //-----------------M Day Payment------------------------//
      
      $m_payment=($basic_salary/26)*$m_days*2;
      
      //-----------------Over Time x (3)------------------------//
      
      $ot_t_payment=($basic_salary/200)*3*$m_ot_hrss;

      if ($dm_new-$total_shifts > 0) {
        $absent_day=$dm_new-$total_shifts;
        $absent_amount=round(($basic_salary/$dm_new)*$absent_day, 2);
      }else{
        $absent_day=0;
        $absent_amount=0;
      }

      $n_working_days=(int)$total_shifts-(int)$m_days-(int)$poya_days;

      if ($n_working_days < $dm_new) {
        $n_day_earning=round(($basic_salary/$dm_new)*$n_working_days, 2);
      }else{
        $n_day_earning=$basic_salary;
      }
      //-----------------Shifts Type------------------------//

      $statement = $connect->prepare("SELECT shifts_type FROM d_shifts_type a INNER JOIN d_attendance b ON a.department_id = b.department_id AND a.position_id = b.position_id WHERE b.start_date = '".$date_from."' AND b.end_date = '".$date_to."' AND b.employee_id='".$employee_id."' AND (b.attendance_status=0 OR b.attendance_status=2) AND a.status=0 ORDER BY a.id DESC LIMIT 1");
      $statement->execute();
      $result = $statement->fetchAll();
      
      if ($statement->rowCount()>0):
        
        foreach($result as $row_shifts_type):

          $shifts_type = $row_shifts_type['shifts_type'];

          if ($shifts_type == 12):
            $normal_ot_hrs= $total_shifts*3 - $m_ot_hrss;
            $normal_ot_payment=round(($basic_salary/200)*1.5*($normal_ot_hrs), 2);

          elseif ($shifts_type == 8):
            $normal_ot_hrs=0;
            $normal_ot_payment=0;

          endif;
        
      endforeach;    
      
      else:
        $normal_ot_hrs=0;
        $normal_ot_payment=0;

      endif;

      //-----------------Over Time x (1.5)------------------------//
      $total_ot_hrss = $h_ot_hrs+$normal_ot_hrs;
      $ot_payment = $normal_ot_payment+$h_days_payment;

              
        if ($rows['department_id'] > 0) {
          $department_id=$rows['department_id'];
        }else{
          $department_id=0;
        }
   
        //-----------------EPF Details------------------------//

        $statement = $connect->prepare("SELECT epf FROM employee WHERE employee_id='".$employee_id."'");
        $statement->execute();
        $result = $statement->fetchAll();
        foreach($result as $epf){             
          
          $epf = $epf['epf'];
        }        

        $for_epf=(string)$n_day_earning+(string)$poya_day_payment+(string)$m_payment;

        if ($epf==1) {
          $epf_8=round(($for_epf/100)*8, 2);
          $epf_12=round(($for_epf/100)*12, 2);
          $etf_3=round(($for_epf/100)*3, 2);
        }else{
          $epf_8=0;
          $epf_12=0;
          $etf_3=0;
        }

        //-----------------Extra OT------------------------//

        if ($rows['extra_ot_hrs'] > 0) {
          $extra_ot_hrs=$rows['extra_ot_hrs'];
        }else{
          $extra_ot_hrs=0;
        }

        $extra_ot_payment=round(($basic_salary/200)*1.5*$extra_ot_hrs,2);

        //-----------------Advance Details------------------------//

        $statement = $connect->prepare("SELECT amount, id FROM salary_advance WHERE employee_id='".$employee_id."' AND (date_effective BETWEEN '".$date_from."' AND '".$date_to."') AND (status=2 OR status=1)");
        $statement->execute();
        $total_advance = $statement->rowCount();
        $result = $statement->fetchAll();
        if ($total_advance > 0) {
          foreach($result as $advance_deductions){             
            $advance_amount=$advance_deductions['amount'];             
          }
        }else{
          $advance_amount=0;
        } 

        //-----------------Ration Details------------------------//

        $statement = $connect->prepare("SELECT COALESCE(sum(amount),'0') AS ration_amount FROM ration_deduction WHERE employee_id='".$employee_id."' AND (date_effective BETWEEN '".$date_from."' AND '".$date_to."') AND (status=0 OR status=1) AND department_id='".$rows['department_id']."'");
        $statement->execute();
        $result = $statement->fetchAll();        
        foreach($result as $ration_deductions){             
          $ration_amount=$ration_deductions['ration_amount'];            
        }

        $incentive=(string)$total_amount-(string)$n_day_earning-(string)$poya_day_payment-(string)$m_payment-(string)$ot_payment-(string)$ot_t_payment;  

        $gross=(string)$n_day_earning+(string)$poya_day_payment+(string)$m_payment+(string)$ot_payment+(string)$ot_t_payment+(string)$incentive+(string)$absent_amount+(string)$extra_ot_payment+(string)$arrears_payment;

        $total_deduction=(string)$epf_8+(string)$absent_amount+(string)$advance_amount+(string)$ration_amount;

        $net=(string)$gross-(string)$total_deduction;

        if ($net > 0) {
          $total_deduction1=(string)$epf_8+(string)$absent_amount+(string)$advance_amount+(string)$ration_amount;
          $advance_amount1=$advance_amount;

        }else{
          $total_deduction1=(string)$epf_8+(string)$absent_amount+(string)$ration_amount;
          $advance_amount1=0;
        }

        $net1=(string)$gross-(string)$total_deduction1;

    $data = array(
      ':p_id'    => $sno ++,
      ':payroll_id'         => trim($_POST["payroll_id"]),
      ':employee_id'        => $employee_id,
      ':employee_no'        => $employee_no,
      ':position_id'        => $position_id,
      ':bank_id'            => $bank_id,
      ':department'         => $department_id,
      ':basic_salary'       => $basic_salary, 
      ':no_of_shift'        => $total_shifts,
      ':n_working_days'     => $n_working_days,   
      ':ot_hrs'             => $total_ot_hrss,
      ':poya_days'          => $poya_days,
      ':m_days'             => $m_days,
      ':m_ot_hrs'           => $m_ot_hrss,
      ':h_days'             => $h_days,
      ':h_ot_hrs'           => $h_ot_hrs,
      ':p_leave_days'       => 0,
      ':n_day_earning'      => $n_day_earning,
      ':poya_day_payment'   => $poya_day_payment,
      ':m_payment'          => $m_payment,
      ':p_leave_day_payment'=> 0,
      ':ot_payment'         => $ot_payment,
      ':ot_t_payment'       => $ot_t_payment,
      ':extra_ot_hrs'       => $extra_ot_hrs,
      ':extra_ot_payment'   => $extra_ot_payment,
      ':incentive'          => $incentive,
      ':for_epf'            => $for_epf,
      ':arrears_payment'    => $arrears_payment,
      ':gross'              => $gross,
      ':no_pay_days'        => $absent_day,
      ':no_pay'             => $absent_amount,
      ':employee_epf'       => $epf_8,
      ':salary_advance'     => $advance_amount1,
      ':ration'             => $ration_amount,
      ':hostel'             => 0,
      ':fines'              => 0,
      ':total_deductions'   => $total_deduction1,
      ':net_salary'         => $net1,
      ':employer_epf'       => $epf_12,
      ':employer_etf'       => $etf_3,
      ':id'                 => $_POST["payroll_id"],
      ':status'             => 1,
    );

    $query = "
    INSERT INTO d_payroll_items(id, payroll_id, employee_id, employee_no, position_id, bank_id, department, basic_salary, no_of_shift, n_working_days, ot_hrs, poya_days, m_days, m_ot_hrs, h_days, h_ot_hrs, p_leave_days, n_day_earning, poya_day_payment, m_payment, p_leave_day_payment, ot_payment, ot_t_payment, extra_ot_hrs, extra_ot_payment, incentive, for_epf, arrears_payment, gross, no_pay_days, no_pay, employee_epf, salary_advance, ration, hostel, fines, total_deductions, net_salary, employer_epf, employer_etf)
    VALUES (:p_id, :payroll_id, :employee_id, :employee_no, :position_id, :bank_id, :department, :basic_salary, :no_of_shift, :n_working_days, :ot_hrs, :poya_days, :m_days, :m_ot_hrs, :h_days, :h_ot_hrs, :p_leave_days, :n_day_earning, :poya_day_payment, :m_payment, :p_leave_day_payment, :ot_payment, :ot_t_payment, :extra_ot_hrs, :extra_ot_payment, :incentive, :for_epf, :arrears_payment, :gross, :no_pay_days, :no_pay, :employee_epf, :salary_advance, :ration, :hostel, :fines, :total_deductions, :net_salary, :employer_epf, :employer_etf);
    UPDATE d_payroll SET status=:status WHERE id=:id;
    ";
  
    $statement = $connect->prepare($query);

    $statement->execute($data);

    //-----------------Attendance Details------------------------//

    for ($k = 0; $k < count($attendance_id); $k++) {  

      $data_attendance = array(
        ':id'     =>  $attendance_id[$k],
        ':status' =>  2,
      );

      $query_attendance = "
      UPDATE `d_attendance` SET `attendance_status`=:status WHERE `id`=:id    
      ";
        
      $statement = $connect->prepare($query_attendance);
      $statement->execute($data_attendance);
    }

  }
 echo 'done';
 
}

?>
