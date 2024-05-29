<?php
error_reporting(0);

include('config.php');
$connect = pdoConnection();
/*session_start();*/

if($_POST['query'] != '')
 {
  $data = array();
  
 $query = '
SELECT a.id, a.unit_price, a.qty, a.total, b.product_name, a.ref_no FROM inventory_issue a INNER JOIN inventory_product b ON a.product_id=b.id WHERE a.employee_id="'.$_POST['query'].'" AND a.status = 0 ORDER BY a.id DESC
';

$statement = $connect->prepare($query);
$statement->execute();
$total_data = $statement->rowCount();
$result = $statement->fetchAll();

if($total_data > 0)
{
  foreach($result as $row)
  {
    $statement = $connect->prepare("SELECT sum(total) AS grand_total FROM inventory_issue WHERE employee_id='".$_POST['query']."' AND status = 0");
        $statement->execute();
        $result = $statement->fetchAll();
        foreach($result as $emp_allowances){             
          $grand_total=$emp_allowances['grand_total'];
      }

      $ref_no=$row['ref_no'];
          
    $data[] = array(
        'id'     =>  $row['id'],
        'product_id'     =>  $row['product_name'],
        'unit_price'   =>  number_format($row['unit_price'],2),
        'qty'      =>  $row['qty'],        
        'total'   =>  number_format($row['total'],2)        
      );
   
}
    
  }
  else{
    $statement = $connect->prepare("SELECT ref_no FROM inventory_issue ORDER BY ref_no DESC LIMIT 1");
        $statement->execute();
        $result = $statement->fetchAll();
        foreach($result as $invoice_no){
        $expNum = explode('-', $invoice_no['ref_no']);             
          
          if ($expNum[0]==date('Y')) {
            $ref_no=date('Y').'-'.str_pad($expNum[1]+1, 4, "0", STR_PAD_LEFT);
          }else{
            $ref_no=date('Y').'-'.str_pad(1, 4, "0", STR_PAD_LEFT);
          }

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