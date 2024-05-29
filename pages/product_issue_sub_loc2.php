<?php 
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();
require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 68) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}

$error = false;

if (isset($_POST['invoice_btn'])){

  if (checkPermissions($_SESSION["user_id"], 68) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/inventory/issue_sub_loc');
    exit();
  }

  if ($_POST['size']!='0') {
      $size=$_POST['size'];
  }else{
      $size='';
  }

  if ($_POST['color']!='1') {
      $color=$_POST['color'];
  }else{
      $color='';
  }

  if ($_POST['gender']!='1') {
      $gender=$_POST['gender'];
  }else{
      $gender='';
  }

  for ($i = 0; $i < count($_POST['product']); $i++) {

  $data = array(
    ':employee_id'=>  $_POST['employee_id'],
    ':product_id' =>  $_POST['product'][$i],
    ':size'       =>  $size[$i],
    ':color'      =>  $color[$i],
    ':gender'     =>  $gender[$i],
    ':qty'        =>  $_POST['quantity'][$i],
    ':unit_price' =>  $_POST['price'][$i],
    ':total'      =>  $_POST['total'][$i],
    ':status'     =>  4,
    ':location_id'=>  $_POST['location_id'],
    ':ref_no'     =>  $_POST['ref_no'],
  );
 
  $query = "
  INSERT INTO inventory_stock(product_id, size, color, gender, location_id, qty, unit_price, status, ref_no, employee_id, total)
  VALUES (:product_id, :size, :color, :gender, :location_id, :qty, :unit_price, :status, :ref_no, :employee_id, :total)
  ";
          
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';    

  }else{
      $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
  }
}
}

if (isset($_POST['update_save'])){

  if (checkPermissions($_SESSION["user_id"], 68) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/inventory/issue_product');
    exit();
  }

  $statement = $connect->prepare('SELECT * FROM inventory_issue WHERE employee_id="'.$_GET['edit'].'"' );
$statement->execute(); 
if(empty($statement->rowCount())){
  $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Not Product or not select employee.</div>';
  header('location:/inventory/issue_product');
  exit();
}
 
    for ($i = 0; $i <= count($_POST['row_id']); $i++) {  

      $data = array(
        ':id'     =>  $_POST['row_id'][$i],
        ':status' =>  1,
      );

      $query = "
      UPDATE `inventory_issue` SET `status`=:status WHERE `id`=:id    
      ";
        
      $statement = $connect->prepare($query);

      if($statement->execute($data))
      {
        $errMSG = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';
        header('location:/inventory/issue_product/monthly/'.$_POST['employee_id'].'/'.$_POST['ref_no_table'].'');
      }else{
        $errMSG = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
        header('location:/inventory/issue_product');
      }
    }  
}

if (isset($_POST['remove_product'])){

  if (checkPermissions($_SESSION["user_id"], 68) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/inventory/issue_product');
    exit();
  }

  $data = array(
      ':id'      =>  $_POST['delete_id'],     
  );

  $query = "DELETE FROM inventory_stock WHERE id=:id";
    
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    $errMSG = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';            
  }else{
      $errMSG = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
  }
}

$product = '';
$query="SELECT * FROM inventory_product WHERE status=0 ORDER BY id ASC";
$statement = $connect->prepare($query);
$statement->execute();
$result = $statement->fetchAll();
foreach($result as $row)
{
 $product .= '<option value="'.$row['id'].'">'.$row['product_name'].'</option>';
}

$color = '';
$query="SELECT id, color FROM inventory_color ORDER BY id ASC";
$statement = $connect->prepare($query);
$statement->execute();
$result = $statement->fetchAll();
foreach($result as $row_color)
{
 $color .= '<option value="'.$row_color['id'].'">'.$row_color['color'].'</option>';
}

$gender = '';
$query="SELECT id, gender FROM inventory_gender ORDER BY id ASC";
$statement = $connect->prepare($query);
$statement->execute();
$result = $statement->fetchAll();
foreach($result as $row_gender)
{
 $gender .= '<option value="'.$row_gender['id'].'">'.$row_gender['gender'].'</option>';
}

              
include '../inc/header.php';
?>

    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Inventory</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Inventory</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <?php
          if ( isset($errMSG) ) {
            ?>
            <div class="col-xl-12 col-md-6 mb-4">
              <?php echo $errMSG; ?>
            </div>
              <?php
          }
          if (isset($_SESSION["msg"])) {
          ?>
            <div class="col-xl-12 col-md-6 mb-4">
              <?php
              echo $_SESSION["msg"];
              unset($_SESSION["msg"]);
              ?>
            </div>
          <?php
          }  
         
          ?>
        </div>

        
        <div class="row">          
          <div class="col-md-12">
                          
              
                <div class="card card-success">
                  <div class="card-header">
                    <h3 class="card-title">Issue to Sub Location</h3> 

                    <a href="/inventory/issue_sub_loc/create_sub" class="edit_data4 btn btn-sm bg-gradient-primary float-right">Create Invoice</a>               
                  </div>
                    <!-- /.card-header -->
                  <div class="card-body">
                  <div class="row">
                    <div class="col-md-12">

                    <?php
                  
                  $query="
                  SELECT ref_no, employee_id, COALESCE(sum(total),'0') AS total FROM inventory_stock
                  WHERE status=4
                  GROUP BY ref_no
                  ORDER BY id DESC
                  ";

                  $statement = $connect->prepare($query);
                  $statement->execute();
                  $total_data = $statement->rowCount();
                  $result = $statement->fetchAll();

                  ?>

                    <table id="emp_data" class="table table-bordered table-striped table-sm table-hover">                    
                      <thead style="text-align: center; width: 100%;">
                        <tr>
                          <th>#</th>
                          <th>Name</th>
                          <th>Invoice No</th>
                          <th>Total</th>
                          <th>Action</th>
                        </tr>  
                      </thead>
                      <tbody>
                        <?php
                      $startpoint =0;
                      $sno = $startpoint + 1;
                      foreach($result as $row)
                      {
                        $query = 'SELECT e.employee_id, e.surname, e.initial, j.employee_no FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid INNER JOIN position p ON e.position_id=p.position_id WHERE j.join_id="'.$row['employee_id'].'" ORDER BY e.employee_id DESC';

                        $statement = $connect->prepare($query);
                        $statement->execute();
                        $total_data = $statement->rowCount();
                        $result = $statement->fetchAll();
                        foreach($result as $row_employee):
                          endforeach;

                        $statement = $connect->prepare('SELECT c.position_abbreviation FROM promotions a INNER JOIN position c ON a.position_id=c.position_id INNER JOIN (SELECT employee_id, MAX(id) maxid FROM promotions GROUP BY employee_id) b ON a.employee_id = b.employee_id AND a.id = b.maxid WHERE a.employee_id="'.$row['employee_id'].'"');
                        $statement->execute();
                        $total_position = $statement->rowCount();
                        $result = $statement->fetchAll();
                        if ($total_position > 0) :
                          foreach($result as $position_name):
                          
                            $position_id = $position_name['position_abbreviation'];
                          endforeach;
                          else:
                            $position_id ='';
                          endif;                
                        ?>
                        <tr>
                          <td><center><?php echo $sno; ?></center></td>
                          <td style="text-align: left;"><?php echo $row_employee['employee_no'].' '.$position_id.' '.$row_employee['surname'].' '.$row_employee['initial']; ?></td>
                          <td><center><?php echo $row['ref_no'];?></center></td>
                          <td style="text-align: right;"><?php echo number_format($row['total'], 2); ?></td>
                          <td><center>
                            <form method="POST" target="_blank" id="add_deduction_form" action="/inventory/invoice_print">
                        
                        <input type="hidden" class="form-control" id="invoice_no" name="invoice_no" value="<?php echo $row['ref_no'];?>">
                    
                      <button class="btn btn-sm btn-outline-primary" data-toggle="tooltip" title="Print Invoice"><i class="fas fa-print"></i></form>

                            <!-- <a href="/employee_list/employee/<?php echo $row['employee_id']?>" class="btn btn-sm btn-outline-warning" data-toggle="tooltip" data-placement="left" title="View Profile"><i class="fa fa-eye"></i></a> -->
                            </td>
                        </tr>
                      <?php }
                      ?>
                      </tbody>
                    </table>

                  </div>
                </div>
                <!-- /.card-body -->
<!-- 
                <div class="card-footer">                  

                  
                </div> -->

              </div>
              <!-- /.card -->
            
                   
          </div>          
        </div>
        <!-- /.row -->

      
      </div><!-- /.container-fluid -->
    </section>    

<?php
include '../inc/footer.php';
?>

<script type="text/javascript">
  $(document).ready(function() {

    var dataTable = $('#emp_data').DataTable({
        "processing":true,
        "serverSide":true,
        "autoWidth": false,
        "scrollX": false,
        "order":[],
        "ajax":{
         url:"/employee_fetch",
         type:"POST"
        },
        "columnDefs":[
         {
          "targets":[0, 3, 4],
          "orderable":false,
         },
        ],

       });
 
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    });

  });
</script>
