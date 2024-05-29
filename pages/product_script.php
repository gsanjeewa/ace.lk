<?php

include('config.php');
// Fetching state data
$connect = pdoConnection();

if($_POST['request'] == 2){

if((isset($_POST["product"]) && !empty($_POST["product"])) && (isset($_POST["size"]) && !empty($_POST["size"]) && $_POST["size"]!='size') && (isset($_POST["color"]) && !empty($_POST["color"]) && $_POST["color"]!='color') && (isset($_POST["gender"]) && !empty($_POST["gender"]) && $_POST["gender"]!='gender'))
{
  $query = "SELECT sum(qty) AS total_qty FROM inventory_stock WHERE status=1 AND product_id='".$_POST["product"]."' AND size='".$_POST["size"]."' AND color='".$_POST["color"]."' AND gender='".$_POST["gender"]."' AND location_id='".$_POST["location_id"]."'";
    
  $statement = $connect->prepare($query);
  $statement->execute();
  $result = $statement->fetchAll();
  foreach($result as $product_qty){             
    $product_qty=$product_qty['total_qty'];
  }

  $query = "SELECT sum(qty) AS total_qty FROM inventory_stock WHERE status=2 AND product_id='".$_POST["product"]."' AND size='".$_POST["size"]."' AND color='".$_POST["color"]."' AND gender='".$_POST["gender"]."' AND location_id='".$_POST["location_id"]."'";
    
  $statement = $connect->prepare($query);
  $statement->execute();
  $result = $statement->fetchAll();
  foreach($result as $product_issue_qty){             
    $product_sub_issue_qty=$product_issue_qty['total_qty'];
  }

  $query = "SELECT sum(qty) AS total_qty FROM inventory_stock WHERE status=4 AND product_id='".$_POST["product"]."' AND size='".$_POST["size"]."' AND color='".$_POST["color"]."' AND gender='".$_POST["gender"]."' AND location_id='".$_POST["location_id"]."'";
    
  $statement = $connect->prepare($query);
  $statement->execute();
  $result = $statement->fetchAll();
  foreach($result as $product_issue_emp_qty){             
    $product_issue_emp_qty=$product_issue_emp_qty['total_qty'];
  }

  $query = "SELECT sum(qty) AS total_qty FROM inventory_stock WHERE status=2 AND product_id='".$_POST["product"]."' AND size='".$_POST["size"]."' AND color='".$_POST["color"]."' AND gender='".$_POST["gender"]."' AND sub_location_id='".$_POST["location_id"]."'";
    
  $statement = $connect->prepare($query);
  $statement->execute();
  $result = $statement->fetchAll();
  foreach($result as $product_sub_stock){             
    $product_sub_stock=$product_sub_stock['total_qty'];
  }

  $output=(int)$product_qty+(int)$product_sub_stock-(int)$product_sub_issue_qty-(int)$product_issue_emp_qty;
    
 }

 echo json_encode($output);

}

?>