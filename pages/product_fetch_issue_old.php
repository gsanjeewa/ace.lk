<?php
error_reporting(0);

include('config.php');
$connect = pdoConnection();
/*session_start();*/

if($_POST['query'] != '')
 {
  $data = array();
  
 $query = '
SELECT * FROM inventory_stock WHERE employee_id="'.$_POST['query'].'" AND status = 3 AND emp_status=0 ORDER BY id DESC
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

    $statement = $connect->prepare("SELECT sum(total) AS grand_total FROM inventory_stock WHERE employee_id='".$_POST['query']."' AND status = 3");
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

      $ref_no=$row['ref_no'];

      $product = $product_name.' '.$size.' '.$color.' '.$gender;

      $action='<input type="text" class="form-control" name="delete_id" value="'.$row['id'].'"><button class="btn btn-sm btn-outline-danger" name="remove_product" type="submit"><i class="fa fa-trash"></i></button>';
          
    $data[] = array(
        'id'     =>  $row['id'],
        'product_id'     =>  $product,
        'unit_price'   =>  number_format($row['unit_price'],2),
        'qty'      =>  $row['qty'],        
        'total'   =>  number_format($row['total'],2),
        'action'         => $action        
      );
   
}
    
  }
  else{
    $statement = $connect->prepare("SELECT ref_no FROM inventory_stock ORDER BY ref_no DESC LIMIT 1");
        $statement->execute();
        $result = $statement->fetchAll();
        if ($statement->rowCount()>0) {
                
          foreach($result as $invoice_no){
          $expNum = explode('-', $invoice_no['ref_no']);             
            
            if ($expNum[0]==date('Y')) {
              $ref_no=date('Y').'-'.str_pad($expNum[1]+1, 4, "0", STR_PAD_LEFT);
            }else{
              $ref_no=date('Y').'-'.str_pad(1, 4, "0", STR_PAD_LEFT);
            }

        }
      }
      else{
        $ref_no=date('Y').'-'.str_pad(1, 4, "0", STR_PAD_LEFT);
      }

    
  }

 }

 $output = array(
    'data'        =>  $data,
    'total_data_table'    =>  number_format($grand_total,2),
    'total_data'    =>  $grand_total,
    'ref_no'    =>  $ref_no,
    'employee_id1' => $_POST['query']
  );

echo json_encode($output);

?>