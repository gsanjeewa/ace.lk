<?php

/*include('database_connection.php');*/
include "config.php";
$connect = pdoConnection();

$column = array('');

$output='';
if(isset($_POST['effective_date']) && $_POST['effective_date'] != '' )
{
  $effective_date = date("Y-m-d", strtotime($_POST['effective_date']));
  $query = "
  SELECT c.department_name, c.department_location, a.cso, a.oic, a.sso, a.loic, a.jso, a.lso, a.lsso, a.asco
    FROM reports_position_pay a
    INNER JOIN payroll b ON a.payroll_id = b.id
    INNER JOIN department c ON a.department_id = c.department_id
    WHERE b.date_from = '".$effective_date."' ORDER BY c.department_name ASC
  "; 

$statement = $connect->prepare($query);

$statement->execute();

$number_filter_row = $statement->rowCount();

$result = $statement->fetchAll();

$data = array();
$startpoint =0;
$sno = $startpoint + 1;
foreach($result as $row)
{
   if ($row['cso'] != '') : $cso=number_format($row['cso'],2); else: $cso=''; endif;
   if ($row['oic'] != '') : $oic=number_format($row['oic'],2); else: $oic=''; endif;
   if ($row['sso'] != '') : $sso=number_format($row['sso'],2); else: $sso=''; endif;
   if ($row['loic'] != '') : $loic=number_format($row['loic'],2); else: $loic=''; endif;
   if ($row['jso'] != '') : $jso=number_format($row['jso'],2); else: $jso=''; endif;
   if ($row['lso'] != '') : $lso=number_format($row['lso'],2); else: $lso=''; endif;
   if ($row['lsso'] != '') : $lsso=number_format($row['lsso'],2); else: $lsso=''; endif;
   if ($row['asco'] != '') : $asco=number_format($row['asco'],2); else: $asco=''; endif;

 $sub_array = array();
 $sub_array[] = $sno;
 $sub_array[] = $row['department_name'].'-'.$row['department_location'];
 $sub_array[] = $cso;
 $sub_array[] = $oic;
 $sub_array[] = $sso;
 $sub_array[] = $loic; 
 $sub_array[] = $jso;
 $sub_array[] = $lso;
 $sub_array[] = $lsso;
 $sub_array[] = $asco;
  
 $sno ++;

 $data[] = $sub_array;
  }

$output = array(
 "data"       =>  $data
);

}

echo json_encode($output);
?>