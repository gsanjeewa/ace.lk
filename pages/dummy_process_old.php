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

  $query = "DELETE FROM `d_payroll_items` WHERE `payroll_id`=:payroll_id";
    
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
    $statement = $connect->prepare("SELECT a.id, a.employee_id, COALESCE((a.no_of_shifts * b.position_payment),'0') AS total_amount, COALESCE(a.no_of_shifts,'0') AS total_shifts, COALESCE(a.poya_day,'0') AS poya_days, COALESCE(a.m_day,'0') AS m_days, COALESCE(a.m_ot_hrs,'0') AS m_ot_hrss, COALESCE(a.total_ot_hrs,'0') AS total_ot_hrss, a.department_id FROM d_attendance a INNER JOIN d_position_pay b ON a.department_id = b.department_id AND a.position_id = b.position_id WHERE a.start_date = '".$date_from."' AND a.end_date = '".$date_to."' AND (a.attendance_status=0 OR a.attendance_status=2)");
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

      //------------------shift details-----------------------//
      $statement = $connect->prepare("SELECT shifts FROM d_shifts_rate a INNER JOIN d_attendance b ON a.department_id = b.department_id WHERE b.start_date = '".$date_from."' AND b.end_date = '".$date_to."' AND b.employee_id='".$employee_id."' AND (b.attendance_status=0 OR b.attendance_status=2) AND a.status=0 ORDER BY a.id DESC LIMIT 1");
      $statement->execute();
      $result = $statement->fetchAll();
      
      if ($statement->rowCount()>0):
        foreach($result as $row_shifts):

          $dm_new=$row_shifts['shifts'];
        endforeach;
      $h_days=0; 
      else:
        //-----------------Half Days------------------------//

      if ($total_shifts >=26):
        $h_days=4;
      elseif ($total_shifts >=20 && $total_shifts<=25):
        $h_days=3;
      elseif ($total_shifts >=13 && $total_shifts<=19):
        $h_days=2;
      elseif ($total_shifts >=6 && $total_shifts<=12):
        $h_days=1;      
      endif;

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
        
        if ($rows['poya_days'] > 0) {
          $poya_days=$rows['poya_days'];
        }else{
          $poya_days=0;
        }

        if ($rows['m_days'] > 0) {
          $m_days=$rows['m_days'];
        }else{
          $m_days=0;
        }

        if ($rows['m_ot_hrss'] > 0) {
          $m_ot_hrss=$rows['m_ot_hrss'];
        }else{
          $m_ot_hrss=0;
        }

        $ot_shifts=(string)$total_shifts-(string)$h_days-(string)$m_days;
        //-----------------Half Days OT hrs------------------------//

        $h_ot_hrs=$h_days*6;

        if ($rows['total_ot_hrss'] > 0) {
          $total_ot_hrss=$rows['total_ot_hrss']-$h_ot_hrs-$m_ot_hrss;
        }else{
          $total_ot_hrss=$ot_shifts*3;
        }

        if ($rows['department_id'] > 0) {
          $department_id=$rows['department_id'];
        }else{
          $department_id=0;
        }

      //-----------------Nomal OT hrs------------------------//

      $ot_hrs=(string)$total_ot_hrss+(string)$h_ot_hrs;

      //-----------------Normal Working Days------------------------//

      $n_working_days=(string)$total_shifts-(string)$poya_days-(string)$m_days;

      //-----------------Normal Day Earning------------------------//

      if ($n_working_days < $dm) {
        $n_day_earning=($basic_salary/$dm)*$n_working_days;
      }else{
        $n_day_earning=$basic_salary;
      }
    
      //-----------------Poya Day Payment------------------------//
      
      $poya_day_payment=($basic_salary/$dm)*$poya_days*1.5;

      //-----------------M Day Payment------------------------//
      
      $m_payment=($basic_salary/$dm)*$m_days*2;

      //-----------------Over Time x (1.5)------------------------//
      
      $ot_payment=($basic_salary/200)*1.5*($total_ot_hrss+$h_ot_hrs);

      //-----------------Over Time x (3)------------------------//
      
      $ot_t_payment=($basic_salary/200)*3*$m_ot_hrss;      

      //-----------------Absent Details------------------------//

      if ($dm-$total_shifts > 0) {
            $absent_day=$dm-$total_shifts;
            $absent_amount=round(($basic_salary/$dm)*$absent_day, 2);
          }else{
            $absent_day=0;
            $absent_amount=0;
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
          $epf_8=($for_epf/100)*8;
          $epf_12=($for_epf/100)*12;
          $etf_3=($for_epf/100)*3;
        }else{
          $epf_8=0;
          $epf_12=0;
          $etf_3=0;
        }

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

        $statement = $connect->prepare("SELECT COALESCE(sum(amount),'0') AS ration_amount FROM ration_deduction WHERE employee_id='".$employee_id."' AND (date_effective BETWEEN '".$date_from."' AND '".$date_to."') AND (status=0 OR status=1)");
        $statement->execute();
        $result = $statement->fetchAll();        
        foreach($result as $ration_deductions){             
          $ration_amount=$ration_deductions['ration_amount'];            
        }

        $incentive=(string)$total_amount-(string)$n_day_earning-(string)$poya_day_payment-(string)$m_payment-(string)$ot_payment-(string)$ot_t_payment;  

        $gross=(string)$n_day_earning+(string)$poya_day_payment+(string)$m_payment+(string)$ot_payment+(string)$ot_t_payment+(string)$incentive+(string)$absent_amount;

        $total_deduction=(string)$epf_8+(string)$absent_amount+(string)$advance_amount+(string)$ration_amount;

        $net=(string)$gross-(string)$total_deduction;

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
      ':incentive'          => $incentive,
      ':for_epf'            => $for_epf,
      ':gross'              => $gross,
      ':no_pay_days'        => $absent_day,
      ':no_pay'             => $absent_amount,
      ':employee_epf'       => $epf_8,
      ':salary_advance'     => $advance_amount,
      ':ration'             => $ration_amount,
      ':hostel'             => 0,
      ':fines'              => 0,
      ':total_deductions'   => $total_deduction,
      ':net_salary'         => $net,
      ':employer_epf'       => $epf_12,
      ':employer_etf'       => $etf_3,
      ':id'                 => $_POST["payroll_id"],
      ':status'             => 1,
    );

    $query = "
    INSERT INTO d_payroll_items(id, payroll_id, employee_id, employee_no, position_id, bank_id, department, basic_salary, no_of_shift, n_working_days, ot_hrs, poya_days, m_days, m_ot_hrs, h_days, h_ot_hrs, p_leave_days, n_day_earning, poya_day_payment, m_payment, p_leave_day_payment, ot_payment, ot_t_payment, incentive, for_epf, gross, no_pay_days, no_pay, employee_epf, salary_advance, ration, hostel, fines, total_deductions, net_salary, employer_epf, employer_etf)
    VALUES (:p_id, :payroll_id, :employee_id, :employee_no, :position_id, :bank_id, :department, :basic_salary, :no_of_shift, :n_working_days, :ot_hrs, :poya_days, :m_days, :m_ot_hrs, :h_days, :h_ot_hrs, :p_leave_days, :n_day_earning, :poya_day_payment, :m_payment, :p_leave_day_payment, :ot_payment, :ot_t_payment, :incentive, :for_epf, :gross, :no_pay_days, :no_pay, :employee_epf, :salary_advance, :ration, :hostel, :fines, :total_deductions, :net_salary, :employer_epf, :employer_etf);
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
