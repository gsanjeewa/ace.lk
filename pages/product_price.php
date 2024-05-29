<?php

include('config.php');
// Fetching state data
$connect = pdoConnection();
if($_POST['request'] ==1){
$product_id=!empty($_POST['product_id'])?$_POST['product_id']:'';
if(!empty($product_id))
  {
  
  $query="SELECT unit_price, id from inventory_price WHERE product_id='".$product_id."' ORDER BY id DESC";
  $statement = $connect->prepare($query);
  $statement->execute();
  $total_data = $statement->rowCount();
  $result = $statement->fetchAll();
        
  if($total_data>0)
  {
      echo "<option value=''>Select Price</option>";
      foreach($result as $row)
      {
        echo "<option value='".$row['unit_price']."'>".$row['unit_price']."</option>";        
      }
   }else{
      echo "<option value=''>Select Price</option>";
   }  
 }

}

if($_POST['request'] ==2){
$product=!empty($_POST['product'])?$_POST['product']:'';
$size=!empty($_POST['size'])?$_POST['size']:'';
$color=!empty($_POST['color'])?$_POST['color']:'';
$gender=!empty($_POST['gender'])?$_POST['gender']:'';
if(isset($_POST["product"]) && !empty($_POST["product"]))
{
  $query = "SELECT sum(qty) AS total_qty FROM inventory_stock WHERE status=1 AND product_id='".$_POST["product"]."'";
  
  if (!empty($size)) {
    $query .= "AND size='".$size."'";
  }

  if (!empty($color)) {
    $query .= "AND color='".$color."'";
  }

  if (!empty($gender)) {

    $query .= "AND gender='".$gender."'";
  }

  $statement = $connect->prepare($query);
  $statement->execute();
  $result = $statement->fetchAll();
  foreach($result as $product_qty){             
    $product_qty=$product_qty['total_qty'];
  }

  $output=(int)$product_qty;
    
 }

 echo json_encode($output);

}

?>