<?php

/*include('database_connection.php');*/
include "config.php";
$connect = pdoConnection();

$column = array('employee_no');
$output ='';
if(isset($_POST['effective_date'], $_POST['filter_institution']) && $_POST['effective_date'] != '' && $_POST['filter_institution'] != '')
{
  $effective_date = date("Y-m-d", strtotime($_POST['effective_date']));
$query = '
SELECT * FROM d_payroll_items a INNER JOIN d_payroll b ON a.payroll_id = b.id WHERE b.date_from = "'.$effective_date.'" AND a.department = "'.$_POST['filter_institution'].'" AND a.status=0 ORDER BY a.department ASC, a.employee_id ASC
';

$query1 = '';

if($_POST["length"] != -1)
{
 $query1 = 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}

$statement = $connect->prepare($query);

$statement->execute();

$number_filter_row = $statement->rowCount();

$statement = $connect->prepare($query . $query1);

$statement->execute();

$result = $statement->fetchAll();

$data = array();
$startpoint =0;
$sno = $startpoint + 1;
foreach($result as $row)
{

	 $query = 'SELECT * FROM d_payroll WHERE id="'.$row['payroll_id'].'"';
    $statement = $connect->prepare($query);
    $statement->execute();
    $result = $statement->fetchAll();
    foreach($result as $date_from)
    { 
    }

    $query = 'SELECT a.surname, a.initial FROM employee a INNER JOIN join_status b ON a.employee_id=b.employee_id WHERE b.join_id="'.$row['employee_id'].'"';
    $statement = $connect->prepare($query);
    $statement->execute();
    $result = $statement->fetchAll();
    foreach($result as $employee_name)
    { 
    }

    $query = 'SELECT position_abbreviation FROM position WHERE position_id="'.$row['position_id'].'"';
    $statement = $connect->prepare($query);
    $statement->execute();
    $total_position = $statement->rowCount();
    $result = $statement->fetchAll();
    if ($total_position > 0) {
      foreach($result as $position_name)
      { 
        $position_id = $position_name['position_abbreviation'];
      }
      }else{
        $position_id ='';
      }


      $statement = $connect->prepare("SELECT a.account_no, b.bank_name FROM bank_details a INNER JOIN bank_name b ON a.bank_name=b.id WHERE a.id='".$row['bank_id']."'");
      $statement->execute();
      $total_bank = $statement->rowCount();
      $result = $statement->fetchAll();
        if ($total_bank > 0) :
          foreach($result as $row_bank):
            $bank_name = $row_bank['bank_name'];
        $account_no=str_pad($row_bank['account_no'], 12, "0", STR_PAD_LEFT);
          endforeach;
      else:
        $bank_name = '';
      $account_no = '';
      endif;

$basic_salary1=$row['basic_salary']-3500;

if ($basic_salary1 !=0) : $basic_salary1 = number_format($basic_salary1,2);else:$basic_salary1='';endif;

if ($row['no_of_shift'] !=0) : $no_of_shift = number_format($row['no_of_shift']); else: $no_of_shift =''; endif;

if ($row['n_working_days'] !=0) :$n_working_days = number_format($row['n_working_days']); else:$n_working_days = '';endif;

if ($row['ot_hrs'] !=0) : $ot_hrs = number_format($row['ot_hrs'],2); else:$ot_hrs =''; endif;

if ($row['poya_days'] !=0) : $poya_days = number_format($row['poya_days'],2); else: $poya_days='';endif;

if ($row['m_days'] !=0) : $m_days = number_format($row['m_days'],2); else: $m_days='';endif;

if ($row['m_ot_hrs'] !=0) : $m_ot_hrs = number_format($row['m_ot_hrs'],2); else: $m_ot_hrs='';endif;

 if ($row['h_days'] !=0) : $h_days = number_format($row['h_days'],2);else: $h_days=''; endif;

 if ($row['h_ot_hrs'] !=0) : $h_ot_hrs = number_format($row['h_ot_hrs'],2); else:$h_ot_hrs=''; endif;

 if ($row['p_leave_days'] !=0) : $p_leave_days = number_format($row['p_leave_days'],2); else:$p_leave_days=''; endif;

 if ($row['basic_salary'] !=0) : $basic_salary_t = number_format($row['basic_salary'],2); else:$basic_salary_t=''; endif;

 if ($row['n_day_earning'] !=0) : $n_day_earning = number_format($row['n_day_earning'],2);else: $n_day_earning=''; endif;

 if ($row['poya_day_payment']!=0) : $poya_day_payment = number_format($row['poya_day_payment'],2);else: $poya_day_payment=''; endif; 

 if ($row['m_payment'] !=0) : $m_payment = number_format($row['m_payment']); else: $m_payment=''; endif;

 if ($row['p_leave_day_payment'] !=0) :$p_leave_day_payment = number_format($row['p_leave_day_payment'],2); else: $p_leave_day_payment=''; endif;

 if ($row['ot_payment'] !=0) :$ot_payment = number_format($row['ot_payment'],2); else: $ot_payment=''; endif;

 if ($row['ot_t_payment'] !=0) :$ot_t_payment = number_format($row['ot_t_payment'],2); else: $ot_t_payment=''; endif;

  if ($row['incentive'] !=0) :$incentive = number_format($row['incentive'],2); else: $incentive=''; endif;

 if ($row['for_epf'] !=0) :$for_epf = number_format($row['for_epf'],2); else: $for_epf=''; endif;

 if ($row['arrears_payment'] !=0) :$arrears_payment = number_format($row['arrears_payment'],2); else: $arrears_payment=''; endif;

 if ($row['gross'] !=0) :$gross = number_format($row['gross'],2); else: $gross=''; endif;

 if ($row['employee_epf'] !=0) :$employee_epf = number_format($row['employee_epf'],2); else: $employee_epf=''; endif;
 
 if ($row['no_pay_days'] !=0) :$no_pay_days = number_format($row['no_pay_days'],2); else: $no_pay_days=''; endif;

 if ($row['no_pay'] !=0) :$no_pay = number_format($row['no_pay'],2); else: $no_pay=''; endif;

 if ($row['ration'] !=0) : $ration = number_format($row['ration'],2); else: $ration=''; endif;

 if ($row['salary_advance'] !=0) : $salary_advance = number_format($row['salary_advance'],2); else: $salary_advance=''; endif;

 if ($row['hostel'] !=0) : $hostel = number_format($row['hostel'],2); else: $hostel=''; endif;

 if ($row['fines'] !=0) : $fines = number_format($row['fines'],2); else: $fines=''; endif;

 if ($row['net_salary'] !=0) : $net_salary = number_format($row['net_salary'],2); else: $net_salary=''; endif;

 if ($row['total_deductions'] !=0) : $total_deductions = number_format($row['total_deductions'],2); else: $total_deductions=''; endif;

 if ($row['employer_epf'] !=0) : $employer_epf = number_format($row['employer_epf'],2); else:$employer_epf=''; endif;

 if ($row['employer_etf'] !=0) : $employer_etf = number_format($row['employer_etf'],2); else: $employer_etf=''; endif;

$brai=number_format(2500,2);
$braii=number_format(1000,2);


 $sub_array = array();
 $sub_array[] = $sno;
 $sub_array[] = $row['employee_no'];
 $sub_array[] = $employee_name['surname'].' '.$employee_name['initial'];
 $sub_array[] = $position_id;
 $sub_array[] = $basic_salary1;
 $sub_array[] = $brai;
 $sub_array[] = $braii;
 $sub_array[] = $no_of_shift;
 $sub_array[] = $n_working_days;
 $sub_array[] = $ot_hrs;
 $sub_array[] = $poya_days;
 $sub_array[] = $m_days;
 $sub_array[] = $m_ot_hrs;
 $sub_array[] = $h_days;
 $sub_array[] = $h_ot_hrs;
 $sub_array[] = $p_leave_days;
 $sub_array[] = $basic_salary_t;
 $sub_array[] = $n_day_earning;
 $sub_array[] = $poya_day_payment;
 $sub_array[] = $m_payment;
 $sub_array[] = $p_leave_day_payment;
 $sub_array[] = $ot_payment;
 $sub_array[] = $ot_t_payment;
 $sub_array[] = $incentive;
 $sub_array[] = $for_epf;
 $sub_array[] = $arrears_payment;
 $sub_array[] = $gross;
 $sub_array[] = $employee_epf;
 $sub_array[] = $no_pay_days;
 $sub_array[] = $no_pay;
 $sub_array[] = $salary_advance;
 $sub_array[] = $ration;
 $sub_array[] = $hostel;
 $sub_array[] = $fines;
 $sub_array[] = $total_deductions;
 $sub_array[] = $net_salary;
 $sub_array[] = $employer_epf;
 $sub_array[] = $employer_etf;
  
 $sno ++;

 $data[] = $sub_array;
}

function count_all_data($connect)
{
 $query = 'SELECT * FROM d_payroll_items a INNER JOIN d_payroll b ON a.payroll_id = b.id';
 
 if(isset($_POST['effective_date'], $_POST['filter_institution']) && $_POST['effective_date'] != '' && $_POST['filter_institution'] != '')
{
  $effective_date = date("Y-m-d", strtotime($_POST['effective_date']));

 $query .= '
 WHERE b.date_from = "'.$effective_date.'" AND a.department = "'.$_POST['filter_institution'].'" 
 ';
}

 $query .= ' AND a.status=0';

 $statement = $connect->prepare($query);
 $statement->execute();
 return $statement->rowCount();
}

$output = array(
 "draw"       =>  intval($_POST["draw"]),
 "recordsTotal"   =>  count_all_data($connect),
 "recordsFiltered"  =>  $number_filter_row,
 "data"       =>  $data
);
}
echo json_encode($output);

?>