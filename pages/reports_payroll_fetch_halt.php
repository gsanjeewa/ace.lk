<?php

//fetch.php

include "config.php";
$connect = pdoConnection();

$column = array('employee_id', 'gross', 'deduction_amount', 'net');
$output='';
if(isset($_POST['effective_date']) && $_POST['effective_date'] != '')
{
  $effective_date = date("Y-m-d", strtotime($_POST['effective_date']));

$query = '
SELECT * FROM payroll_items a INNER JOIN payroll b ON a.payroll_id = b.id WHERE b.date_from = "'.$effective_date.'" AND (a.status=2) ORDER BY a.department_id ASC, a.employee_id ASC

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
$total_shifts = 0;
 $total_svc = 0;
 $total_rewards = 0;
 $total_chairman = 0;
 $total_gross = 0;
 $total_epf_8 = 0;
 $total_advance = 0;
 $total_uniforms = 0;
 $total_ration = 0;
 $total_hostel = 0;
 $total_fines = 0;
 $total_death = 0;
 $total_net = 0;
 $total_epf_12 = 0;
 $total_etf = 0;

foreach($result as $row)
{
	$query = 'SELECT * FROM payroll WHERE id="'.$row['payroll_id'].'"';
    $statement = $connect->prepare($query);
    $statement->execute();
    $result = $statement->fetchAll();
    foreach($result as $date_from)
    { 
    }
    
    $query = 'SELECT a.surname, a.initial FROM employee a
    INNER JOIN join_status b ON a.employee_id=b.employee_id WHERE b.join_id="'.$row['employee_id'].'"';
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

    if ($row['no_of_shift'] !='') : $no_of_shift = number_format($row['no_of_shift']); else: $no_of_shift =''; endif;
if ($row['ot_hrs'] !=0) :$ot_hrs = number_format($row['ot_hrs']); else:$ot_hrs = '';endif;

if ($row['sot_hrs'] !=0) :$sot_hrs = number_format($row['sot_hrs']); else:$sot_hrs = '';endif;

if ($row['basic_salary'] !=0) : $basic_salary = number_format($row['basic_salary'],2);else:$basic_salary='';endif;

if ($row['basic_epf'] !=0) : $basic_epf = number_format($row['basic_epf'],2);else:$basic_epf='';endif;

if ($row['ot_amount'] !=0) : $ot_amount = number_format($row['ot_amount'],2); else:$ot_amount =''; endif;

if ($row['incentive'] !=0) : $incentive = number_format($row['incentive'],2); else: $incentive='';endif;

if ($row['sot_amount'] !=0) : $sot_amount = number_format($row['sot_amount'],2); else: $sot_amount='';endif;

if ($row['service_allowance'] !=0) : $service_allowance = number_format($row['service_allowance'],2); else: $service_allowance='';endif;

 if ($row['rewards'] !=0) : $rewards = number_format($row['rewards'],2); else: $rewards=''; endif;

 if ($row['chairman_allowance'] !=0) : $chairman_allowance = number_format($row['chairman_allowance'],2);else: $chairman_allowance=''; endif;

 if ($row['training_be'] !=0) : $training_be = number_format($row['training_be'],2); else:$training_be=''; endif;

 if ($row['pending_payments'] !=0) : $pending_payments = number_format($row['pending_payments'],2); else:$pending_payments=''; endif;

 if ($row['gross'] !=0) : $gross = number_format($row['gross'],2);else: $gross=''; endif;

 if ($row['employee_epf']!=0) : $employee_epf = number_format($row['employee_epf'],2);else: $employee_epf=''; endif; 

 if ($row['absent_day'] !=0) : $absent_day = number_format($row['absent_day']); else: $absent_day=''; endif;

 if ($row['absent_amount'] !=0) :$absent_amount = number_format($row['absent_amount'],2); else: $absent_amount=''; endif;

 if ($row['advance_amount'] !=0) :$advance_amount = number_format($row['advance_amount'],2); else: $advance_amount=''; endif;

 if ($row['inventory_amount'] !=0) :$inventory_amount = number_format($row['inventory_amount'],2); else: $inventory_amount=''; endif;

 if ($row['ration_amount'] !=0) :$ration_amount = number_format($row['ration_amount'],2); else: $ration_amount=''; endif;

 if ($row['hostel'] !=0) :$hostel = number_format($row['hostel'],2); else: $hostel=''; endif;

 if ($row['fines'] !=0) :$fines = number_format($row['fines'],2); else: $fines=''; endif;

 if ($row['death_donation'] !=0) :$death_donation = number_format($row['death_donation'],2); else: $death_donation=''; endif;

 if ($row['pending_deductions'] !=0) :$pending_deductions = number_format($row['pending_deductions'],2); else: $pending_deductions=''; endif;

 if ($row['deduction_amount'] !=0) :$deduction_amount = number_format($row['deduction_amount'],2); else: $deduction_amount=''; endif;

 if ($row['net'] !=0) : $net = number_format($row['net'],2); else: $net=''; endif;

 if ($row['employer_epf'] !=0) : $employer_epf = number_format($row['employer_epf'],2); else:$employer_epf=''; endif;

 if ($row['employer_etf'] !=0) : $employer_etf = number_format($row['employer_etf'],2); else: $employer_etf=''; endif;


 $sub_array = array();
 $sub_array[] = $sno;
 $sub_array[] = $row['employee_no'];
 $sub_array[] = $employee_name['surname'].' '.$employee_name['initial'];
 $sub_array[] = $position_id;
 $sub_array[] = $bank_name;
 $sub_array[] = $account_no;
 $sub_array[] = $no_of_shift;
 $sub_array[] = $ot_hrs;
 $sub_array[] = $sot_hrs;
 $sub_array[] = $basic_salary;
 $sub_array[] = $basic_epf;
 $sub_array[] = $ot_amount;
 $sub_array[] = $incentive;
 $sub_array[] = $sot_amount;
 $sub_array[] = $service_allowance;
 $sub_array[] = $rewards;
 $sub_array[] = $chairman_allowance;
 $sub_array[] = $training_be;
 $sub_array[] = $pending_payments;
 $sub_array[] = $gross;
 $sub_array[] = $employee_epf;
 $sub_array[] = $absent_day;
 $sub_array[] = $absent_amount;
 $sub_array[] = $advance_amount;
 $sub_array[] = $inventory_amount;
 $sub_array[] = $ration_amount;
 $sub_array[] = $hostel;
 $sub_array[] = $fines;
 $sub_array[] = $death_donation;
 $sub_array[] = $pending_deductions;
 $sub_array[] = $deduction_amount;
 $sub_array[] = $net;
 $sub_array[] = $employer_epf;
 $sub_array[] = $employer_etf;
$sno ++;

 $total_shifts = $total_shifts + floatval($no_of_shift);
 $total_svc = $total_svc + floatval($row['service_allowance']);
 $total_rewards = $total_rewards + floatval($row['rewards']);
 $total_chairman = $total_chairman + floatval($row['chairman_allowance']);
 $total_gross = $total_gross + floatval($row['gross']);
 $total_epf_8 = $total_epf_8 + floatval($row['employee_epf']);
 $total_advance = $total_advance + floatval($row['advance_amount']);
 $total_uniforms = $total_uniforms + floatval($row['inventory_amount']);
 $total_ration = $total_ration + floatval($row['ration_amount']);
 $total_hostel = $total_hostel + floatval($row['hostel']);
 $total_fines = $total_fines + floatval($row['fines']);
 $total_death = $total_death + floatval($row['death_donation']);
 $total_net = $total_net + floatval($row['net']);
 $total_epf_12 = $total_epf_12 + floatval($row['employer_epf']);
 $total_etf = $total_etf + floatval($row['employer_etf']);
 $data[] = $sub_array;
}

function count_all_data($connect)
{
 $query = "SELECT * FROM payroll_items";
 $statement = $connect->prepare($query);
 $statement->execute();
 return $statement->rowCount();
}

$output = array(
 'draw'    => intval($_POST["draw"]),
 'recordsTotal'  => count_all_data($connect),
 'recordsFiltered' => $number_filter_row,
 'data'    => $data,
 'total_shifts'    => $total_shifts,
 'total_svc'    => number_format($total_svc, 2),
 'total_rewards'    => number_format($total_rewards, 2),
 'total_chairman'    => number_format($total_chairman, 2),
 'total_gross'    => number_format($total_gross, 2),
 'total_epf_8'    => number_format($total_epf_8, 2),
 'total_advance'    => number_format($total_advance, 2),
 'total_uniforms'    => number_format($total_uniforms, 2),
 'total_ration'    => number_format($total_ration, 2),
 'total_hostel'    => number_format($total_hostel, 2),
 'total_fines'    => number_format($total_fines, 2),
 'total_death'    => number_format($total_death, 2),
 'total_net'    => number_format($total_net, 2),
 'total_epf_12'    => number_format($total_epf_12, 2),
 'total_etf'    => number_format($total_etf, 2)
);
}
echo json_encode($output);


?>