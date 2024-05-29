<?php

include('config.php');
// Fetching state data
$connect = pdoConnection();
if($_POST['request'] ==1){
$merge_id=!empty($_POST['merge_id'])?$_POST['merge_id']:'';
if(!empty($merge_id))
{
  $merge_department=array();
  $query="SELECT department_id from d_department_merge WHERE merge_id='".$merge_id."'";
  $statement = $connect->prepare($query);
  $statement->execute();
  $total_data = $statement->rowCount();
  $result = $statement->fetchAll();
  foreach($result as $row_merge)
  {
    $merge_department[]=$row_merge['department_id'];
  }

  $query="SELECT department_id, department_name, department_location FROM department ORDER BY department_name ASC";
  $statement = $connect->prepare($query);
  $statement->execute();
  $total_data = $statement->rowCount();
  $result = $statement->fetchAll();
        
  if($total_data>0)
  {          
      foreach($result as $row)
      {
        echo "<option value='".$row['department_id']."'";

        if (array_search($row['department_id'], $merge_department) !== false){ 
          echo "SELECTED";
        }

        echo ">".$row['department_name'].'-'.$row['department_location']."</option>";        
      }
   }else{
      echo "<option value=''>Select Districts</option>";
   }  
 }

}

?>