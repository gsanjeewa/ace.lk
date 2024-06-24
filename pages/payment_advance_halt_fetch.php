  <?php

//fetch.php

include "config.php";
$connect = pdoConnection();

$column = array('');
$output='';
if(isset($_POST['effective_date'], $_POST['filter_institution']) && $_POST['effective_date'] != '' && $_POST['filter_institution'] != '')
{
  $effective_date = date("Y-m", strtotime($_POST['effective_date']));
  $institution = implode("', '", $_POST['filter_institution']);
  if ($institution == 'all') {
    $query = "
    SELECT b.employee_no, c.surname, c.initial, a.amount, b.join_id, h.bank_no, h.branch_no, h.account_no, h.holder_name FROM salary_advance a 
    INNER JOIN join_status b ON a.employee_id=b.join_id 
    INNER JOIN employee c ON b.employee_id=c.employee_id 
    LEFT JOIN (SELECT d.account_no, e.bank_name, e.bank_no, f.branch_name, f.branch_no, d.employee_id, d.holder_name FROM bank_details d INNER JOIN bank_name e ON d.bank_name=e.id 
      INNER JOIN bank_branch f ON d.branch_name=f.id 
      INNER JOIN (SELECT employee_id, MAX(id) maxid FROM bank_details GROUP BY employee_id) g ON d.employee_id = g.employee_id AND d.id = g.maxid WHERE d.status=0) h ON c.employee_id=h.employee_id 
    WHERE a.status=3 AND DATE_FORMAT(a.date_effective,'%Y-%m') = '".$effective_date."' ORDER BY h.bank_no ASC, cast(b.employee_no as int) ASC
";
  }else{

  
$query = "
SELECT b.employee_no, c.surname, c.initial, a.amount, b.join_id, h.bank_no, h.branch_no, h.account_no, h.holder_name FROM salary_advance a 
    INNER JOIN join_status b ON a.employee_id=b.join_id 
    INNER JOIN employee c ON b.employee_id=c.employee_id 
    LEFT JOIN (SELECT d.account_no, e.bank_name, e.bank_no, f.branch_name, f.branch_no, d.employee_id, d.holder_name FROM bank_details d INNER JOIN bank_name e ON d.bank_name=e.id 
      INNER JOIN bank_branch f ON d.branch_name=f.id 
      INNER JOIN (SELECT employee_id, MAX(id) maxid FROM bank_details GROUP BY employee_id) g ON d.employee_id = g.employee_id AND d.id = g.maxid WHERE d.status=0) h ON c.employee_id=h.employee_id
    WHERE a.status=3 AND DATE_FORMAT(a.date_effective,'%Y-%m') = '".$effective_date."' AND a.department_id IN ('$institution') ORDER BY h.bank_no ASC, cast(b.employee_no as int) ASC
";
}

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
$total_amount = 0; 

foreach($result as $row)
{
  if (!empty($row['bank_no'])):
    $bank_no = $row['bank_no'];
  else:
    $bank_no ='';
  endif;

  if (!empty($row['holder_name'])):
    $holder_name = $row['holder_name'];
  else:
    $holder_name =str_replace(' ', '', $row['initial']).' '.$row['surname'];
  endif;


  if (!empty($row['branch_no'])):
    $branch_no = str_pad($row['branch_no'], 3, "0", STR_PAD_LEFT);
  else:
    $branch_no ='';
  endif;

  if (!empty($row['account_no'])):
    $account_no1 =str_pad($row['account_no'], 12, "0", STR_PAD_LEFT);
  else:
    $account_no1 ='';
  endif;

  
  $with_decimal=round($row['amount'],2);
  $remove_decimal=$with_decimal*100;
  if ($row['bank_no']==7010) {
    $no_code=52;
  }else{
    $no_code=23;
  }
  $date_pay=strtoupper(date('Y F', strtotime($_POST['effective_date'])));
  $sub_array = array();
  $sub_array[] = $sno;
  $sub_array[] = $row['employee_no'];
  $sub_array[] = '0000';
  $sub_array[] = $bank_no;
  $sub_array[] = $branch_no; 
  $sub_array[] = $account_no1;
  $sub_array[] = $holder_name;
  $sub_array[] = $no_code;
  $sub_array[] = '00';
  $sub_array[] = '0';
  $sub_array[] = '000000';
  $sub_array[] = $remove_decimal;
  $sub_array[] = 'SLR';
  $sub_array[] = '7010';
  $sub_array[] = '612';
  $sub_array[] = '000079289055';
  $sub_array[] = 'ACE FRONT LINE';
  $sub_array[] = 'ADVANCE PAYMENT';
  $sub_array[] = $date_pay;
  $sub_array[] = '';
  $sub_array[] = '000000';

  $sno ++;

 $total_amount = $total_amount + floatval($remove_decimal);
 
 $data[] = $sub_array;
}

$output = array(
 
 'data'    => $data,
 'total_amount'    => $total_amount
);
}
echo json_encode($output);


?>