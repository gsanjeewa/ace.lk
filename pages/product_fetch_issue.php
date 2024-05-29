<?php
error_reporting(0);

include('config.php');
$connect = pdoConnection();
/*session_start();*/
$request = $_POST['request'];   // request
if($request == 1){

  $output = array();

  if(isset($_POST["location_id"]) && $_POST["location_id"] != '')
  { 
    
    $query = '
    SELECT * FROM inventory_stock WHERE location_id="'.$_POST['location_id'].'" AND status = 2 ORDER BY id DESC
      ';

    $statement = $connect->prepare($query);
    $statement->execute();
    $total_data = $statement->rowCount();
    $result = $statement->fetchAll();

    if($total_data > 0)
    {
      foreach($result as $row)
      {

        $statement = $connect->prepare("SELECT product_name FROM inventory_product WHERE id='".$row['product_id']."'");
            $statement->execute();
            $result = $statement->fetchAll();
            foreach($result as $row_product_name){             
              $product_name=$row_product_name['product_name'];
        }

        $statement = $connect->prepare("SELECT sum(total) AS grand_total FROM inventory_stock WHERE location_id='".$_POST['location_id']."' AND status = 2");
            $statement->execute();
            $result = $statement->fetchAll();
            foreach($result as $emp_allowances){             
              $grand_total=$emp_allowances['grand_total'];
        }

        $statement = $connect->prepare("SELECT size FROM inventory_size WHERE id='".$row['size']."'");
        $statement->execute();
        $result = $statement->fetchAll();
        if ($statement->rowCount() > 0) {
          
          foreach($result as $row_size){             
            $size=$row_size['size'];
          }
        }else{
          $size='';
        }

        $statement = $connect->prepare("SELECT color FROM inventory_color WHERE id='".$row['color']."'");
        $statement->execute();
        $result = $statement->fetchAll();
        if ($statement->rowCount() > 0) {
          
          foreach($result as $row_color){             
            $color=$row_color['color'];
          }
        }else{
          $color='';
        }

        $statement = $connect->prepare("SELECT gender FROM inventory_gender WHERE id='".$row['gender']."'");
        $statement->execute();
        $result = $statement->fetchAll();
        if ($statement->rowCount() > 0) {
          
          foreach($result as $row_gender){             
            $gender=$row_gender['gender'];
          }
        }else{
          $gender='';
        }

        $product = $product_name.' '.$size.' '.$color.' '.$gender;
        $id[]=$row['id'];
        $action='<form action="" method="POST"><input type="hidden" name="delete_id" value="'.$row['id'].'"><button class="btn btn-sm btn-outline-danger" name="remove_product"  data-toggle="tooltip" data-placement="top" title="Delete" type="submit"><i class="fa fa-trash"></i></button></form>';
          
        $data[] = array(
          'id'          => $row['id'],
          'product_id'  => $product,
          'unit_price'  => number_format($row['unit_price'],2),
          'qty'         => $row['qty'],        
          'total'       => number_format($row['total'],2),
          'action'      => $action        
        );
 
      }
  
    }    

  }
$output = array(
    'data'        =>  $data,
    'total_data_table'    =>  number_format($grand_total,2),
    'id'    =>  $id

  );
 
  echo json_encode($output);
}

if($request == 2){

  $output = array();

  if(isset($_POST["invoice_id"]) && $_POST["invoice_id"] != '')
  { 
    
    $query = '
    SELECT * FROM inventory_stock WHERE invoice_id="'.$_POST['invoice_id'].'" AND status = 4 ORDER BY id DESC
      ';

    $statement = $connect->prepare($query);
    $statement->execute();
    $total_data = $statement->rowCount();
    $result = $statement->fetchAll();

    if($total_data > 0)
    {
      foreach($result as $row)
      {

        $statement = $connect->prepare("SELECT product_name FROM inventory_product WHERE id='".$row['product_id']."'");
            $statement->execute();
            $result = $statement->fetchAll();
            foreach($result as $row_product_name){             
              $product_name=$row_product_name['product_name'];
        }

        $statement = $connect->prepare("SELECT sum(total) AS grand_total FROM inventory_stock WHERE invoice_id='".$_POST['invoice_id']."' AND status = 4");
            $statement->execute();
            $result = $statement->fetchAll();
            foreach($result as $emp_allowances){             
              $grand_total=$emp_allowances['grand_total'];
        }

        $statement = $connect->prepare("SELECT size FROM inventory_size WHERE id='".$row['size']."'");
        $statement->execute();
        $result = $statement->fetchAll();
        if ($statement->rowCount() > 0) {
          
          foreach($result as $row_size){             
            $size=$row_size['size'];
          }
        }else{
          $size='';
        }

        $statement = $connect->prepare("SELECT color FROM inventory_color WHERE id='".$row['color']."'");
        $statement->execute();
        $result = $statement->fetchAll();
        if ($statement->rowCount() > 0) {
          
          foreach($result as $row_color){             
            $color=$row_color['color'];
          }
        }else{
          $color='';
        }

        $statement = $connect->prepare("SELECT gender FROM inventory_gender WHERE id='".$row['gender']."'");
        $statement->execute();
        $result = $statement->fetchAll();
        if ($statement->rowCount() > 0) {
          
          foreach($result as $row_gender){             
            $gender=$row_gender['gender'];
          }
        }else{
          $gender='';
        }

        $statement = $connect->prepare("SELECT status FROM inventory_create_invoice WHERE id='".$_POST['invoice_id']."'");
        $statement->execute();
        $result = $statement->fetchAll();
        if ($statement->rowCount() > 0) {
          
          foreach($result as $row_invoice){             
            if ($row_invoice['status']!=1) {
              $action='<form action="" method="POST"><input type="hidden" name="delete_id" value="'.$row['id'].'"><button class="btn btn-sm btn-outline-danger" name="remove_product"  data-toggle="tooltip" data-placement="top" title="Delete" type="submit"><i class="fa fa-trash"></i></button></form>';  
            }else{
              $action='';
            }
          }
        }else{
          $action='';
        }

        $product = $product_name.' '.$size.' '.$color.' '.$gender;
        $id[]=$row['id'];
        
        
          
        $data[] = array(
          'id'          => $row['id'],
          'product_id'  => $product,
          'unit_price'  => number_format($row['unit_price'],2),
          'qty'         => $row['qty'],        
          'total'       => number_format($row['total'],2),
          'action'      => $action        
        );
 
      }
  
    }    

  }
$output = array(
    'data'        =>  $data,
    'total_data_table'    =>  $grand_total,
    'id'    =>  $id

  );
 
  echo json_encode($output);
}

if($request == 3){

  $output = array();

  if(isset($_POST["invoice_id"]) && $_POST["invoice_id"] != '')
  { 
    
    $query = '
    SELECT * FROM inventory_stock WHERE loc_invoice_id="'.$_POST['invoice_id'].'" AND status = 2 ORDER BY id DESC
      ';

    $statement = $connect->prepare($query);
    $statement->execute();
    $total_data = $statement->rowCount();
    $result = $statement->fetchAll();

    if($total_data > 0)
    {
      foreach($result as $row)
      {

        $statement = $connect->prepare("SELECT product_name FROM inventory_product WHERE id='".$row['product_id']."'");
            $statement->execute();
            $result = $statement->fetchAll();
            foreach($result as $row_product_name){             
              $product_name=$row_product_name['product_name'];
        }

        $statement = $connect->prepare("SELECT sum(total) AS grand_total FROM inventory_stock WHERE loc_invoice_id='".$_POST['invoice_id']."' AND status = 2");
            $statement->execute();
            $result = $statement->fetchAll();
            foreach($result as $emp_allowances){             
              $grand_total=$emp_allowances['grand_total'];
        }

        $statement = $connect->prepare("SELECT size FROM inventory_size WHERE id='".$row['size']."'");
        $statement->execute();
        $result = $statement->fetchAll();
        if ($statement->rowCount() > 0) {
          
          foreach($result as $row_size){             
            $size=$row_size['size'];
          }
        }else{
          $size='';
        }

        $statement = $connect->prepare("SELECT color FROM inventory_color WHERE id='".$row['color']."'");
        $statement->execute();
        $result = $statement->fetchAll();
        if ($statement->rowCount() > 0) {
          
          foreach($result as $row_color){             
            $color=$row_color['color'];
          }
        }else{
          $color='';
        }

        $statement = $connect->prepare("SELECT gender FROM inventory_gender WHERE id='".$row['gender']."'");
        $statement->execute();
        $result = $statement->fetchAll();
        if ($statement->rowCount() > 0) {
          
          foreach($result as $row_gender){             
            $gender=$row_gender['gender'];
          }
        }else{
          $gender='';
        }

        $statement = $connect->prepare("SELECT status FROM inventory_create_invoice_loc WHERE id='".$_POST['invoice_id']."'");
        $statement->execute();
        $result = $statement->fetchAll();
        if ($statement->rowCount() > 0) {
          
          foreach($result as $row_invoice){             
            if ($row_invoice['status']!=1) {
              $action='<form action="" method="POST"><input type="hidden" name="delete_id" value="'.$row['id'].'"><button class="btn btn-sm btn-outline-danger" name="remove_product"  data-toggle="tooltip" data-placement="top" title="Delete" type="submit"><i class="fa fa-trash"></i></button></form>';  
            }else{
              $action='';
            }
          }
        }else{
          $action='';
        }

        $product = $product_name.' '.$size.' '.$color.' '.$gender;
        $id[]=$row['id'];
        
        
          
        $data[] = array(
          'id'          => $row['id'],
          'product_id'  => $product,
          'unit_price'  => number_format($row['unit_price'],2),
          'qty'         => $row['qty'],        
          'total'       => number_format($row['total'],2),
          'action'      => $action        
        );
 
      }
  
    }    

  }
$output = array(
    'data'        =>  $data,
    'total_data_table'    =>  $grand_total,
    'id'    =>  $id

  );
 
  echo json_encode($output);
}

?>