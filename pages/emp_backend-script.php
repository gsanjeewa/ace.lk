<?php

include('config.php');
// Fetching state data
$connect = pdoConnection();
if($_POST['request'] ==1){
$districts_id=!empty($_POST['districts_id'])?$_POST['districts_id']:'';
if(!empty($districts_id))
  {
  
  $query="SELECT ds_id, ds from ds WHERE dis_id='".$districts_id."'";
  $statement = $connect->prepare($query);
  $statement->execute();
  $total_data = $statement->rowCount();
  $result = $statement->fetchAll();
        
  if($total_data>0)
  {
      echo "<option value=''>Select DS</option>";
      foreach($result as $row)
      {
        echo "<option value='".$row['ds_id']."'>".$row['ds']."</option>";        
      }
   }else{
      echo "<option value=''>Select Districts</option>";
   }  
 }

}
   // Fetching city data
$ds_id=!empty($_POST['ds_id'])?$_POST['ds_id']:'';
if(!empty($ds_id))
  {
  $query="SELECT gn_id, gn from gn WHERE ds_id='".$ds_id."'";
  $statement = $connect->prepare($query);
  $statement->execute();
  $total_data = $statement->rowCount();
  $result = $statement->fetchAll();
        
  if($total_data>0)
  {
    echo "<option value=''>Select GN</option>";
    foreach($result as $row)
    {
      echo "<option value='".$row['gn_id']."'>".$row['gn']."</option>";
        
    }
  }  
}

if($_POST['request'] ==2){
$districts_id=!empty($_POST['districts_id'])?$_POST['districts_id']:'';
if(!empty($districts_id))
  {
  
  $query="SELECT police_id, police from police WHERE dis_id='".$districts_id."' ORDER BY police ASC";
  $statement = $connect->prepare($query);
  $statement->execute();
  $total_data = $statement->rowCount();
  $result = $statement->fetchAll();
        
  if($total_data>0)
  {
      echo "<option value=''>Select police</option>";
      foreach($result as $row)
      {
        echo "<option value='".$row['police_id']."'>".$row['police']."</option>";        
      }
   }else{
      echo "<option value=''>Select Districts</option>";
   }  
 }
}

if($_POST['request'] ==3){
$bank_name_id=!empty($_POST['bank_name_id'])?$_POST['bank_name_id']:'';
if(!empty($bank_name_id))
  {
  
  $query="SELECT id, branch_name, branch_no from bank_branch WHERE bank_name_id='".$bank_name_id."' ORDER BY branch_name ASC";
  $statement = $connect->prepare($query);
  $statement->execute();
  $total_data = $statement->rowCount();
  $result = $statement->fetchAll();
        
  if($total_data>0)
  {
      echo "<option value=''>Select Branch</option>";
      foreach($result as $row)
      {
        echo "<option value='".$row['id']."'>".$row['branch_name']." (".$row['branch_no'].")</option>";        
      }
   }else{
      echo "<option value=''>Select Branch</option>";
   }  
 }
}

?>