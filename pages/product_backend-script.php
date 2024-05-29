<?php

include('config.php');
// Fetching state data
$connect = pdoConnection();
if($_POST['request'] ==1){
$product_id=!empty($_POST['product_id'])?$_POST['product_id']:'';
if(!empty($product_id))
  {
  
  $query="SELECT id, size from inventory_size WHERE product_id='".$product_id."' ORDER BY size ASC";
  $statement = $connect->prepare($query);
  $statement->execute();
  $total_data = $statement->rowCount();
  $result = $statement->fetchAll();
        
  if($total_data>0)
  {
      // echo "<option value=''>Select Size</option>";
      foreach($result as $row)
      {
        echo "<option value='".$row['id']."'>".$row['size']."</option>";        
      }
   }else{
      echo "<option value='size'>No Size</option>";
   }  
 }

}
   // Fetching city data
/*$ds_id=!empty($_POST['ds_id'])?$_POST['ds_id']:'';
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
}*/

if($_POST['request'] ==2){
$product_id=!empty($_POST['product_id'])?$_POST['product_id']:'';
if(!empty($product_id))
  {
  
  $query="SELECT id, color from inventory_color WHERE product_id='".$product_id."' ORDER BY color ASC";
  $statement = $connect->prepare($query);
  $statement->execute();
  $total_data = $statement->rowCount();
  $result = $statement->fetchAll();
        
  if($total_data>0)
  {
      echo "<option value=''>Select Color</option>";
      foreach($result as $row)
      {
        echo "<option value='".$row['id']."'>".$row['color']."</option>";        
      }
   }else{
      echo "<option value='color'>No Color</option>";
   }  
 }
}

if($_POST['request'] ==3){
$product_id=!empty($_POST['product_id'])?$_POST['product_id']:'';
if(!empty($product_id))
  {
  
  $query="SELECT id, gender from inventory_gender WHERE product_id='".$product_id."' ORDER BY gender ASC";
  $statement = $connect->prepare($query);
  $statement->execute();
  $total_data = $statement->rowCount();
  $result = $statement->fetchAll();
        
  if($total_data>0)
  {
      echo "<option value=''>Select Gender</option>";
      foreach($result as $row)
      {
        echo "<option value='".$row['id']."'>".$row['gender']."</option>";        
      }
   }else{
      echo "<option value='gender'>No Gender</option>";
   }  
 }
}

?>