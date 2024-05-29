<?php

include('config.php');
// Fetching state data
$connect = pdoConnection();

if((isset($_POST["date_effective"])) && (isset($_POST["department_id"])))
{
  $date_effective = date('Y-m-d', strtotime($_POST['date_effective']));
  $output = '';

  $statement = $connect->prepare("SELECT * FROM salary_advance WHERE YEAR(date_effective)= YEAR('".$date_effective."') AND MONTH(date_effective) = MONTH('".$date_effective."') AND department_id='".$_POST["department_id"]."'");  
  $statement->execute();
  $total_data = $statement->rowCount();
  if ($total_data > 0) {
    $mystatus='<span class="badge badge-danger">'.$total_data.'</span>';
    
  }else{
    $mystatus='<span class="badge badge-danger"></span>';
  }

  $output .=$mystatus;
  

  $statement = $connect->prepare("SELECT b.employee_no, p.position_abbreviation, c.surname, c.initial FROM salary_advance a INNER JOIN join_status b ON a.employee_id=b.join_id INNER JOIN employee c ON b.employee_id=c.employee_id INNER JOIN promotions d ON b.join_id = d.employee_id INNER JOIN (SELECT employee_id, MAX(id) maxid_pro FROM promotions GROUP BY employee_id) e ON d.employee_id = e.employee_id AND d.id = e.maxid_pro INNER JOIN position p ON d.position_id=p.position_id WHERE YEAR(a.date_effective)= YEAR('".$date_effective."') AND MONTH(a.date_effective) = MONTH('".$date_effective."') AND a.department_id='".$_POST["department_id"]."' ORDER BY a.id DESC LIMIT 1");  
  $statement->execute();
  $total_data = $statement->rowCount();
  $result = $statement->fetchAll();
  if ($total_data > 0) {
    foreach($result as $row)
    {

      $last_details='<span class="badge badge-success">'.$row['employee_no'].' '.$row['position_abbreviation'].' '.$row['surname'].' '.$row['initial'].'</span>';
    }
  }else{
    $last_details='<span class="badge badge-success"></span>';
  }

  $output .=$last_details;
}
echo $output;

?>