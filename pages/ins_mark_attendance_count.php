<?php

include('config.php');
// Fetching state data
$connect = pdoConnection();

$request = $_POST['request'];

if($request == 1){

if((isset($_POST["start_date"])) && (isset($_POST["department_id"])))
{
  $start_date = date('Y-m-d', strtotime($_POST['start_date']));
  $output = '';

  $statement = $connect->prepare("SELECT COUNT(*) AS emp_count FROM attendance WHERE YEAR(start_date) = YEAR('".$start_date."') AND MONTH(start_date) = MONTH('".$start_date."') AND department_id = '".$_POST["department_id"]."' GROUP BY employee_id") ;  
  $statement->execute();
  $total_data = $statement->rowCount();
  if ($total_data > 0) 
    {
      $mystatus='<span class="badge badge-warning font-weight-bold">Emp count - '.$total_data.'</span>';
      $output .=$mystatus;
    }


    $statement = $connect->prepare("SELECT sum(no_of_shifts) AS total_count, sum(extra_ot_hrs) AS total_extra FROM attendance WHERE YEAR(start_date) = YEAR('".$start_date."') AND MONTH(start_date) = MONTH('".$start_date."') AND department_id = '".$_POST["department_id"]."'") ;  
  $statement->execute();
  $total_data = $statement->rowCount();
  $result = $statement->fetchAll();
  
    foreach($result as $row_total)
    {
      $mystatus='<span class="badge badge-primary">Total - '.$row_total['total_count'].'</span><span class="badge badge-secondary">Extra Total - '.$row_total['total_extra'].'</span>';
      $output .=$mystatus;
    }

    $statement = $connect->prepare("SELECT p.position_abbreviation, sum(a.no_of_shifts) AS total_count FROM attendance a INNER JOIN position p ON a.position_id=p.position_id WHERE YEAR(a.start_date) = YEAR('".$start_date."') AND MONTH(a.start_date) = MONTH('".$start_date."') AND a.department_id = '".$_POST["department_id"]."' GROUP BY a.position_id") ;  
  $statement->execute();
  $total_data = $statement->rowCount();
  $result = $statement->fetchAll();
  
    foreach($result as $row_position)
    {
      $mystatus='<span class="badge badge-danger">'.$row_position['position_abbreviation'].'-'.$row_position['total_count'].'</span>';
      $output .=$mystatus;
    }
    
  // $statement = $connect->prepare("SELECT b.employee_no, p.position_abbreviation, c.surname, c.initial, a.no_of_shifts, a.position_id FROM attendance a 
  //   INNER JOIN join_status b ON a.employee_id=b.join_id 
  //   INNER JOIN employee c ON b.employee_id=c.employee_id INNER JOIN promotions d ON b.join_id = d.employee_id 
  //   INNER JOIN (SELECT employee_id, MAX(id) maxid_pro FROM promotions GROUP BY employee_id) e ON d.employee_id = e.employee_id AND d.id = e.maxid_pro 
  //   INNER JOIN position p ON d.position_id=p.position_id 
  //   WHERE YEAR(a.start_date)= YEAR('".$start_date."') AND MONTH(a.start_date) = MONTH('".$start_date."') AND a.department_id='".$_POST["department_id"]."' AND a.position_id!=0 ORDER BY a.id DESC LIMIT 1");  
  // $statement->execute();
  // $total_data = $statement->rowCount();
  // $result = $statement->fetchAll();
  // if ($total_data > 0) {
  //   foreach($result as $row)
  //   {

  //     $statement = $connect->prepare("SELECT position_abbreviation FROM position WHERE position_id = '".$row['position_id']."'");  
  //     $statement->execute();
  //     $total_data = $statement->rowCount();
  //     $result_p = $statement->fetchAll();
  //     if ($total_data >0) {
        
  //     foreach($result_p as $row_p)
  //       {
  //         $pos=$row_p['position_abbreviation'];
  //       }
  //     }else{
  //       $pos='';
  //     }

  //     $last_details='<span class="badge badge-success">'.$row['employee_no'].' '.$row['position_abbreviation'].' '.$row['surname'].' '.$row['initial'].' - '.$pos.'('.$row['no_of_shifts'].')</span>';
  //   }
  // }else{
  //   $last_details='<span class="badge badge-success"></span>';
  // }

  // $output .=$last_details;
}
echo $output;
}

if($request == 2){

if((isset($_POST["start_date"])) && (isset($_POST["department_id"])))
{
  $start_date = date('Y-m-d', strtotime($_POST['start_date']));
  $output = '';
    
  $statement = $connect->prepare("SELECT b.employee_no, p.position_abbreviation, c.surname, c.initial, a.no_of_shifts, a.position_id, a.employee_id FROM attendance a 
    INNER JOIN join_status b ON a.employee_id=b.join_id 
    INNER JOIN employee c ON b.employee_id=c.employee_id INNER JOIN promotions d ON b.join_id = d.employee_id 
    INNER JOIN (SELECT employee_id, MAX(id) maxid_pro FROM promotions GROUP BY employee_id) e ON d.employee_id = e.employee_id AND d.id = e.maxid_pro 
    INNER JOIN position p ON d.position_id=p.position_id 
    WHERE YEAR(a.start_date)= YEAR('".$start_date."') AND MONTH(a.start_date) = MONTH('".$start_date."') AND a.department_id='".$_POST["department_id"]."' AND a.position_id!=0 ORDER BY a.id DESC LIMIT 1");  
    $statement->execute();
    $total_data = $statement->rowCount();
    $result = $statement->fetchAll();
    if ($total_data > 0) {
      foreach($result as $row)
      {
        $last_details='<span >'.$row['employee_no'].' '.$row['position_abbreviation'].' '.$row['surname'].' '.$row['initial'].'</span>';

        $last_details .='<table class="table table-sm table-bordered" style="text-align:center;"><thead><tr><th>Position</th><th>Shifts</th></tr></thead><tbody>';

        $statement = $connect->prepare("SELECT b.position_abbreviation, a.no_of_shifts FROM attendance a 
        INNER JOIN position b ON a.position_id=b.position_id 
        WHERE YEAR(a.start_date)= YEAR('".$start_date."') AND MONTH(a.start_date) = MONTH('".$start_date."') AND a.department_id='".$_POST["department_id"]."' AND a.employee_id='".$row["employee_id"]."' AND a.position_id!=0 ORDER BY a.id DESC");  
        $statement->execute();
        $total_data = $statement->rowCount();
        $result = $statement->fetchAll();
        if ($total_data > 0) {
          foreach($result as $row_p)
          {
            $last_details .='<tr><td>'.$row_p['position_abbreviation'].'</td><td>'.$row_p['no_of_shifts'].'</td></tr>';
          }

        }else{
          $last_details .='<tr><td colspan="2">No data</td></tr></tbody>';
        }

        $statement = $connect->prepare("SELECT SUM(no_of_shifts) AS total_shifts FROM attendance 
        WHERE YEAR(start_date)= YEAR('".$start_date."') AND MONTH(start_date) = MONTH('".$start_date."') AND department_id='".$_POST["department_id"]."' AND employee_id='".$row["employee_id"]."' AND position_id!=0");  
        $statement->execute();
        $total_data = $statement->rowCount();
        $result = $statement->fetchAll();
        if ($total_data > 0) {
          foreach($result as $row_pt)
          {
            $last_details .='<tfoot><tr><td>Total</td><td>'.$row_pt['total_shifts'].'</td></tr></tfoot></table>';
          }

        }else{
          $last_details .='<tr><td colspan="2">No data</td></tr></tbody></table>';
        }



      // $statement = $connect->prepare("SELECT position_abbreviation FROM position WHERE position_id = '".$row['position_id']."'");  
      // $statement->execute();
      // $total_data = $statement->rowCount();
      // $result_p = $statement->fetchAll();
      // if ($total_data >0) {
        
      // foreach($result_p as $row_p)
      //   {
      //     $pos=$row_p['position_abbreviation'];
      //   }
      // }else{
      //   $pos='';
      // }

      
    }
  }else{
    $last_details='<span class="badge badge-success"></span>';
  }

  $output .=$last_details;
}
echo $output;
}
?>