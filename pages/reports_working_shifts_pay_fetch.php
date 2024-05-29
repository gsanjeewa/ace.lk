<?php

/*include('database_connection.php');*/
include "config.php";
$connect = pdoConnection();

$column = array('');

$output='';
if(isset($_POST['effective_date'], $_POST['filter_institution']) && $_POST['effective_date'] != '' && $_POST['filter_institution'] != '' )
{
  $effective_date = date("Y-m-d", strtotime($_POST['effective_date']));
  $institution = implode("', '", $_POST['filter_institution']);
  if ($institution == 'all') {
  $query = "
  SELECT c.department_name, c.department_location, a.department_id
    FROM reports_department a
    INNER JOIN payroll b ON a.payroll_id = b.id
    INNER JOIN department c ON a.department_id = c.department_id
    WHERE b.date_from = '".$effective_date."' GROUP BY a.department_id ORDER BY c.department_name ASC
  "; 

  $statement = $connect->prepare($query);
  $statement->execute();
  $result = $statement->fetchAll();
  $data = array();
  $startpoint =0;
  $sno = $startpoint + 1;
  foreach($result as $row)
  {

    $query_cso = "
          SELECT a.invoice_rate, a.working_rate, a.working_shift, a.working_total
    FROM reports_department a
    INNER JOIN payroll b ON a.payroll_id = b.id
    INNER JOIN department c ON a.department_id = c.department_id
    WHERE b.date_from = '".$effective_date."' AND a.position_id=1 AND a.department_id='".$row['department_id']."' ORDER BY c.department_name ASC
    "; 
    
    $statement = $connect->prepare($query_cso);
    $statement->execute();
    $result_cso = $statement->fetchAll();
    if ($statement->rowCount()>0) {
      foreach($result_cso as $row_cso)
      {
        if ($row_cso['working_shift'] !='') : $cso_invoice = $row_cso['working_shift']; else: $cso_invoice =''; endif;
        if ($row_cso['working_rate'] !='') : $cso_working = number_format($row_cso['working_rate'], 2); else: $cso_working =''; endif;
        if ($row_cso['working_total'] !='') : $cso_total = $row_cso['working_total']; else: $cso_total =''; endif;
      }
    }else{
      $cso_invoice ='';
      $cso_working ='';
      $cso_total = '';
    }

    $query_oic = "
          SELECT a.invoice_rate, a.working_rate, a.working_shift, a.working_total
    FROM reports_department a
    INNER JOIN payroll b ON a.payroll_id = b.id
    INNER JOIN department c ON a.department_id = c.department_id
    WHERE b.date_from = '".$effective_date."' AND a.position_id=2 AND a.department_id='".$row['department_id']."' ORDER BY c.department_name ASC
    "; 
    
    $statement = $connect->prepare($query_oic);
    $statement->execute();
    $result_oic = $statement->fetchAll();
    if ($statement->rowCount()>0) {
      foreach($result_oic as $row_oic)
      {
        if ($row_oic['working_shift'] !='') : $oic_invoice = $row_oic['working_shift']; else: $oic_invoice =''; endif;
        if ($row_oic['working_rate'] !='') : $oic_working = number_format($row_oic['working_rate'], 2); else: $oic_working =''; endif;
        if ($row_oic['working_total'] !='') : $oic_total = $row_oic['working_total']; else: $oic_total =''; endif;
      }
    }else{
      $oic_invoice ='';
      $oic_working ='';
      $oic_total ='';
    }
    
   $query_sso = "
          SELECT a.invoice_rate, a.working_rate, a.working_shift, a.working_total
    FROM reports_department a
    INNER JOIN payroll b ON a.payroll_id = b.id
    INNER JOIN department c ON a.department_id = c.department_id
    WHERE b.date_from = '".$effective_date."' AND a.position_id=3 AND a.department_id='".$row['department_id']."' ORDER BY c.department_name ASC
    "; 
    
    $statement = $connect->prepare($query_sso);
    $statement->execute();
    $result_sso = $statement->fetchAll();
    if ($statement->rowCount()>0) {
      foreach($result_sso as $row_sso)
      {
        if ($row_sso['working_shift'] !='') : $sso_invoice = $row_sso['working_shift']; else: $sso_invoice =''; endif;
        if ($row_sso['working_rate'] !='') : $sso_working = number_format($row_sso['working_rate'], 2); else: $sso_working =''; endif;
        if ($row_sso['working_total'] !='') : $sso_total = $row_sso['working_total']; else: $sso_total =''; endif;
      }
    }else{
      $sso_invoice ='';
      $sso_working ='';
      $sso_total ='';
    }

    $query_loio = "
          SELECT a.invoice_rate, a.working_rate, a.working_shift, a.working_total
    FROM reports_department a
    INNER JOIN payroll b ON a.payroll_id = b.id
    INNER JOIN department c ON a.department_id = c.department_id
    WHERE b.date_from = '".$effective_date."' AND a.position_id=4 AND a.department_id='".$row['department_id']."' ORDER BY c.department_name ASC
    "; 
    
    $statement = $connect->prepare($query_loio);
    $statement->execute();
    $result_loio = $statement->fetchAll();
    if ($statement->rowCount()>0) {
      foreach($result_loio as $row_loio)
      {
        if ($row_loio['working_shift'] !='') : $loio_invoice = $row_loio['working_shift']; else: $loio_invoice =''; endif;
        if ($row_loio['working_rate'] !='') : $loio_working = number_format($row_loio['working_rate'], 2); else: $loio_working =''; endif;
        if ($row_loio['working_total'] !='') : $loio_total = $row_loio['working_total']; else: $loio_total =''; endif;
      }
    }else{
      $loio_invoice ='';
      $loio_working ='';
      $loio_total ='';
    }

    $query_jso = "
          SELECT a.invoice_rate, a.working_rate, a.working_shift, a.working_total
    FROM reports_department a
    INNER JOIN payroll b ON a.payroll_id = b.id
    INNER JOIN department c ON a.department_id = c.department_id
    WHERE b.date_from = '".$effective_date."' AND a.position_id=5 AND a.department_id='".$row['department_id']."' ORDER BY c.department_name ASC
    "; 
    
    $statement = $connect->prepare($query_jso);
    $statement->execute();
    $result_jso = $statement->fetchAll();
    if ($statement->rowCount()>0) {
      foreach($result_jso as $row_jso)
      {
        if ($row_jso['working_shift'] !='') : $jso_invoice = $row_jso['working_shift']; else: $jso_invoice =''; endif;
        if ($row_jso['working_rate'] !='') : $jso_working = number_format($row_jso['working_rate'], 2); else: $jso_working =''; endif;
        if ($row_jso['working_total'] !='') : $jso_total = $row_jso['working_total']; else: $jso_total =''; endif;
      }
    }else{
      $jso_invoice ='';
      $jso_working ='';
      $jso_total ='';
    }

    $query_lso = "
          SELECT a.invoice_rate, a.working_rate, a.working_shift, a.working_total
    FROM reports_department a
    INNER JOIN payroll b ON a.payroll_id = b.id
    INNER JOIN department c ON a.department_id = c.department_id
    WHERE b.date_from = '".$effective_date."' AND a.position_id=6 AND a.department_id='".$row['department_id']."' ORDER BY c.department_name ASC
    "; 
    
    $statement = $connect->prepare($query_lso);
    $statement->execute();
    $result_lso = $statement->fetchAll();
    if ($statement->rowCount()>0) {
      foreach($result_lso as $row_lso)
      {
        if ($row_lso['working_shift'] !='') : $lso_invoice = $row_lso['working_shift']; else: $lso_invoice =''; endif;
        if ($row_lso['working_rate'] !='') : $lso_working = number_format($row_lso['working_rate'], 2); else: $lso_working =''; endif;
        if ($row_lso['working_total'] !='') : $lso_total = $row_lso['working_total']; else: $lso_total =''; endif;
      }
    }else{
      $lso_invoice ='';
      $lso_working ='';
      $lso_total ='';
    }

    $query_lsso = "
          SELECT a.invoice_rate, a.working_rate, a.working_shift, a.working_total
    FROM reports_department a
    INNER JOIN payroll b ON a.payroll_id = b.id
    INNER JOIN department c ON a.department_id = c.department_id
    WHERE b.date_from = '".$effective_date."' AND a.position_id=7 AND a.department_id='".$row['department_id']."' ORDER BY c.department_name ASC
    "; 
    
    $statement = $connect->prepare($query_lsso);
    $statement->execute();
    $result_lsso = $statement->fetchAll();
    if ($statement->rowCount()>0) {
      foreach($result_lsso as $row_lsso)
      {
        if ($row_lsso['working_shift'] !='') : $lsso_invoice = $row_lsso['working_shift']; else: $lsso_invoice =''; endif;
        if ($row_lsso['working_rate'] !='') : $lsso_working = number_format($row_lsso['working_rate'], 2); else: $lsso_working =''; endif;
        if ($row_lsso['working_total'] !='') : $lsso_total = $row_lsso['working_total']; else: $lsso_total =''; endif;
      }
    }else{
      $lsso_invoice ='';
      $lsso_working ='';
      $lsso_total ='';
    }

    $query_acso = "
          SELECT a.invoice_rate, a.working_rate, a.working_shift, a.working_total
    FROM reports_department a
    INNER JOIN payroll b ON a.payroll_id = b.id
    INNER JOIN department c ON a.department_id = c.department_id
    WHERE b.date_from = '".$effective_date."' AND a.position_id=8 AND a.department_id='".$row['department_id']."' ORDER BY c.department_name ASC
    "; 
    
    $statement = $connect->prepare($query_acso);
    $statement->execute();
    $result_acso = $statement->fetchAll();
    if ($statement->rowCount()>0) {
      foreach($result_acso as $row_acso)
      {
        if ($row_acso['working_shift'] !='') : $acso_invoice = $row_acso['working_shift']; else: $acso_invoice =''; endif;
        if ($row_acso['working_rate'] !='') : $acso_working = number_format($row_acso['working_rate'], 2); else: $acso_working =''; endif;
        if ($row_acso['working_total'] !='') : $acso_total = $row_acso['working_total']; else: $acso_total =''; endif;
      }
    }else{
      $acso_invoice ='';
      $acso_working ='';
      $acso_total ='';
    }

    $total=(int)$acso_total+(int)$lsso_total+(int)$lso_total+(int)$jso_total+(int)$loio_total+(int)$sso_total+(int)$oic_total+(int)$cso_total;



     $sub_array = array();
     $sub_array[] = $sno;
     $sub_array[] = $row['department_name'].'-'.$row['department_location'];
     $sub_array[] = $cso_invoice;
     $sub_array[] = $oic_invoice;
     $sub_array[] = $sso_invoice;
     $sub_array[] = $loio_invoice; 
     $sub_array[] = $jso_invoice;
     $sub_array[] = $lso_invoice;
     $sub_array[] = $lsso_invoice;
     $sub_array[] = $acso_invoice;
     $sub_array[] = $cso_working;
     $sub_array[] = $oic_working;
     $sub_array[] = $sso_working;
     $sub_array[] = $loio_working;
     $sub_array[] = $jso_working;
     $sub_array[] = $lso_working;
     $sub_array[] = $lsso_working;
     $sub_array[] = $acso_working;
     $sub_array[] = number_format($total, 2);
     $sno ++;

     $data[] = $sub_array;
  }

}else{
  // $query = "
  // SELECT c.department_name, c.department_location, a.cso, a.oic, a.sso, a.loic, a.jso, a.lso, a.lsso, a.asco
  //   FROM reports_position_pay a
  //   INNER JOIN payroll b ON a.payroll_id = b.id
  //   INNER JOIN department c ON a.department_id = c.department_id
  //   WHERE b.date_from = '".$effective_date."' AND a.department_id IN ('$institution') ORDER BY c.department_name ASC
  // "; 


  $query = "
  SELECT c.department_name, c.department_location, a.department_id
    FROM reports_department a
    INNER JOIN payroll b ON a.payroll_id = b.id
    INNER JOIN department c ON a.department_id = c.department_id
    WHERE b.date_from = '".$effective_date."' AND a.department_id IN ('$institution') GROUP BY a.department_id ORDER BY c.department_name ASC
  "; 

  $statement = $connect->prepare($query);
  $statement->execute();
  $result = $statement->fetchAll();
  $data = array();
  $startpoint =0;
  $sno = $startpoint + 1;
  foreach($result as $row)
  {

    $query_cso = "
          SELECT a.invoice_rate, a.working_rate, a.working_shift, a.working_total
    FROM reports_department a
    INNER JOIN payroll b ON a.payroll_id = b.id
    INNER JOIN department c ON a.department_id = c.department_id
    WHERE b.date_from = '".$effective_date."' AND a.position_id=1 AND a.department_id='".$row['department_id']."' ORDER BY c.department_name ASC
    "; 
    
    $statement = $connect->prepare($query_cso);
    $statement->execute();
    $result_cso = $statement->fetchAll();
    if ($statement->rowCount()>0) {
      foreach($result_cso as $row_cso)
      {
        if ($row_cso['working_shift'] !='') : $cso_invoice = $row_cso['working_shift']; else: $cso_invoice =''; endif;
        if ($row_cso['working_rate'] !='') : $cso_working = number_format($row_cso['working_rate'], 2); else: $cso_working =''; endif;
        if ($row_cso['working_total'] !='') : $cso_total = $row_cso['working_total']; else: $cso_total =''; endif;
      }
    }else{
      $cso_invoice ='';
      $cso_working ='';
      $cso_total = '';
    }

    $query_oic = "
          SELECT a.invoice_rate, a.working_rate, a.working_shift, a.working_total
    FROM reports_department a
    INNER JOIN payroll b ON a.payroll_id = b.id
    INNER JOIN department c ON a.department_id = c.department_id
    WHERE b.date_from = '".$effective_date."' AND a.position_id=2 AND a.department_id='".$row['department_id']."' ORDER BY c.department_name ASC
    "; 
    
    $statement = $connect->prepare($query_oic);
    $statement->execute();
    $result_oic = $statement->fetchAll();
    if ($statement->rowCount()>0) {
      foreach($result_oic as $row_oic)
      {
        if ($row_oic['working_shift'] !='') : $oic_invoice = $row_oic['working_shift']; else: $oic_invoice =''; endif;
        if ($row_oic['working_rate'] !='') : $oic_working = number_format($row_oic['working_rate'], 2); else: $oic_working =''; endif;
        if ($row_oic['working_total'] !='') : $oic_total = $row_oic['working_total']; else: $oic_total =''; endif;
      }
    }else{
      $oic_invoice ='';
      $oic_working ='';
      $oic_total ='';
    }
    
   $query_sso = "
          SELECT a.invoice_rate, a.working_rate, a.working_shift, a.working_total
    FROM reports_department a
    INNER JOIN payroll b ON a.payroll_id = b.id
    INNER JOIN department c ON a.department_id = c.department_id
    WHERE b.date_from = '".$effective_date."' AND a.position_id=3 AND a.department_id='".$row['department_id']."' ORDER BY c.department_name ASC
    "; 
    
    $statement = $connect->prepare($query_sso);
    $statement->execute();
    $result_sso = $statement->fetchAll();
    if ($statement->rowCount()>0) {
      foreach($result_sso as $row_sso)
      {
        if ($row_sso['working_shift'] !='') : $sso_invoice = $row_sso['working_shift']; else: $sso_invoice =''; endif;
        if ($row_sso['working_rate'] !='') : $sso_working = number_format($row_sso['working_rate'], 2); else: $sso_working =''; endif;
        if ($row_sso['working_total'] !='') : $sso_total = $row_sso['working_total']; else: $sso_total =''; endif;
      }
    }else{
      $sso_invoice ='';
      $sso_working ='';
      $sso_total ='';
    }

    $query_loio = "
          SELECT a.invoice_rate, a.working_rate, a.working_shift, a.working_total
    FROM reports_department a
    INNER JOIN payroll b ON a.payroll_id = b.id
    INNER JOIN department c ON a.department_id = c.department_id
    WHERE b.date_from = '".$effective_date."' AND a.position_id=4 AND a.department_id='".$row['department_id']."' ORDER BY c.department_name ASC
    "; 
    
    $statement = $connect->prepare($query_loio);
    $statement->execute();
    $result_loio = $statement->fetchAll();
    if ($statement->rowCount()>0) {
      foreach($result_loio as $row_loio)
      {
        if ($row_loio['working_shift'] !='') : $loio_invoice = $row_loio['working_shift']; else: $loio_invoice =''; endif;
        if ($row_loio['working_rate'] !='') : $loio_working = number_format($row_loio['working_rate'], 2); else: $loio_working =''; endif;
        if ($row_loio['working_total'] !='') : $loio_total = $row_loio['working_total']; else: $loio_total =''; endif;
      }
    }else{
      $loio_invoice ='';
      $loio_working ='';
      $loio_total ='';
    }

    $query_jso = "
          SELECT a.invoice_rate, a.working_rate, a.working_shift, a.working_total
    FROM reports_department a
    INNER JOIN payroll b ON a.payroll_id = b.id
    INNER JOIN department c ON a.department_id = c.department_id
    WHERE b.date_from = '".$effective_date."' AND a.position_id=5 AND a.department_id='".$row['department_id']."' ORDER BY c.department_name ASC
    "; 
    
    $statement = $connect->prepare($query_jso);
    $statement->execute();
    $result_jso = $statement->fetchAll();
    if ($statement->rowCount()>0) {
      foreach($result_jso as $row_jso)
      {
        if ($row_jso['working_shift'] !='') : $jso_invoice = $row_jso['working_shift']; else: $jso_invoice =''; endif;
        if ($row_jso['working_rate'] !='') : $jso_working = number_format($row_jso['working_rate'], 2); else: $jso_working =''; endif;
        if ($row_jso['working_total'] !='') : $jso_total = $row_jso['working_total']; else: $jso_total =''; endif;
      }
    }else{
      $jso_invoice ='';
      $jso_working ='';
      $jso_total ='';
    }

    $query_lso = "
          SELECT a.invoice_rate, a.working_rate, a.working_shift, a.working_total
    FROM reports_department a
    INNER JOIN payroll b ON a.payroll_id = b.id
    INNER JOIN department c ON a.department_id = c.department_id
    WHERE b.date_from = '".$effective_date."' AND a.position_id=6 AND a.department_id='".$row['department_id']."' ORDER BY c.department_name ASC
    "; 
    
    $statement = $connect->prepare($query_lso);
    $statement->execute();
    $result_lso = $statement->fetchAll();
    if ($statement->rowCount()>0) {
      foreach($result_lso as $row_lso)
      {
        if ($row_lso['working_shift'] !='') : $lso_invoice = $row_lso['working_shift']; else: $lso_invoice =''; endif;
        if ($row_lso['working_rate'] !='') : $lso_working = number_format($row_lso['working_rate'], 2); else: $lso_working =''; endif;
        if ($row_lso['working_total'] !='') : $lso_total = $row_lso['working_total']; else: $lso_total =''; endif;
      }
    }else{
      $lso_invoice ='';
      $lso_working ='';
      $lso_total ='';
    }

    $query_lsso = "
          SELECT a.invoice_rate, a.working_rate, a.working_shift, a.working_total
    FROM reports_department a
    INNER JOIN payroll b ON a.payroll_id = b.id
    INNER JOIN department c ON a.department_id = c.department_id
    WHERE b.date_from = '".$effective_date."' AND a.position_id=7 AND a.department_id='".$row['department_id']."' ORDER BY c.department_name ASC
    "; 
    
    $statement = $connect->prepare($query_lsso);
    $statement->execute();
    $result_lsso = $statement->fetchAll();
    if ($statement->rowCount()>0) {
      foreach($result_lsso as $row_lsso)
      {
        if ($row_lsso['working_shift'] !='') : $lsso_invoice = $row_lsso['working_shift']; else: $lsso_invoice =''; endif;
        if ($row_lsso['working_rate'] !='') : $lsso_working = number_format($row_lsso['working_rate'], 2); else: $lsso_working =''; endif;
        if ($row_lsso['working_total'] !='') : $lsso_total = $row_lsso['working_total']; else: $lsso_total =''; endif;
      }
    }else{
      $lsso_invoice ='';
      $lsso_working ='';
      $lsso_total ='';
    }

    $query_acso = "
          SELECT a.invoice_rate, a.working_rate, a.working_shift, a.working_total
    FROM reports_department a
    INNER JOIN payroll b ON a.payroll_id = b.id
    INNER JOIN department c ON a.department_id = c.department_id
    WHERE b.date_from = '".$effective_date."' AND a.position_id=8 AND a.department_id='".$row['department_id']."' ORDER BY c.department_name ASC
    "; 
    
    $statement = $connect->prepare($query_acso);
    $statement->execute();
    $result_acso = $statement->fetchAll();
    if ($statement->rowCount()>0) {
      foreach($result_acso as $row_acso)
      {
        if ($row_acso['working_shift'] !='') : $acso_invoice = $row_acso['working_shift']; else: $acso_invoice =''; endif;
        if ($row_acso['working_rate'] !='') : $acso_working = number_format($row_acso['working_rate'], 2); else: $acso_working =''; endif;
        if ($row_acso['working_total'] !='') : $acso_total = $row_acso['working_total']; else: $acso_total =''; endif;
      }
    }else{
      $acso_invoice ='';
      $acso_working ='';
      $acso_total ='';
    }

    $total=(int)$acso_total+(int)$lsso_total+(int)$lso_total+(int)$jso_total+(int)$loio_total+(int)$sso_total+(int)$oic_total+(int)$cso_total;

     $sub_array = array();
     $sub_array[] = $sno;
     $sub_array[] = $row['department_name'].'-'.$row['department_location'];
     $sub_array[] = $cso_invoice;
     $sub_array[] = $oic_invoice;
     $sub_array[] = $sso_invoice;
     $sub_array[] = $loio_invoice; 
     $sub_array[] = $jso_invoice;
     $sub_array[] = $lso_invoice;
     $sub_array[] = $lsso_invoice;
     $sub_array[] = $acso_invoice;
     $sub_array[] = $cso_working;
     $sub_array[] = $oic_working;
     $sub_array[] = $sso_working;
     $sub_array[] = $loio_working;
     $sub_array[] = $jso_working;
     $sub_array[] = $lso_working;
     $sub_array[] = $lsso_working;
     $sub_array[] = $acso_working;
     $sub_array[] = number_format($total, 2); 
     $sno ++;

     $data[] = $sub_array;
  }

}



$output = array(
 "data"       =>  $data
);

}

echo json_encode($output);
?>