<?php
session_start(); 
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();

require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 1) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions to Add Employee.</div>';
    header('location:/employee_list/employee');
    exit();

}

$error = false;

if (isset($_POST['add_save'])){

  if (checkPermissions($_SESSION["user_id"], 1) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions to Add Employee.</div>'; 
    header('location:/employee_list/add_employee');  
    exit();

}
$statement = $connect->prepare("SELECT id FROM inventory_stock ORDER BY id DESC LIMIT 1");
    $statement->execute();
    $result = $statement->fetchAll();
    if ($statement->rowCount()>0) {        
      foreach($result as $row_id){
        $startpoint = $row_id['id'];        
      }
    }
    else{
      $startpoint = 0;
    }
    
    $sno = $startpoint + 1;
 
   if (!$error) {

    $data = array(
      ':id'         =>  $sno,      
      ':product_id'     =>  $_POST['product'],
      ':size'           =>  $_POST['size'],
      ':color'          =>  $_POST['color'],
      ':gender'         =>  $_POST['gender'],
      ':location_id'    =>  1,
      ':qty'            =>  trim($_POST['qty']),
      ':unit_price'     =>  trim($_POST['unit_price']),      
      ':purchase_date'  =>  $_POST['purchase_date'],
      ':status'         =>  1,              
    );
   
    $query = "
    INSERT INTO inventory_stock(id, product_id, size, color, gender, location_id, qty, unit_price, purchase_date, status)
    VALUES (:id, :product_id, :size, :color, :gender, :location_id, :qty, :unit_price, :purchase_date, :status);
    INSERT INTO inventory_price(product_id, unit_price, create_date)
    VALUES (:product_id, :unit_price, :purchase_date);
    ";
    
    $statement = $connect->prepare($query);

    if($statement->execute($data))
    {        
      $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>
      <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';            
    }else{
      $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
    }
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
            
              <form action="" id="add_emp_form" method="post">
                <div class="card card-success">
                  <div class="card-header">
                    <h3 class="card-title">Add Stock</h3>                
                  </div>
                    <!-- /.card-header -->
                  <div class="card-body">
                                        
                    <div class="row">
                      <div class="col-md-3">
                      <div class="form-group">
                        <label for="product">Product:</label>
                        <select class="form-control select2" style="width: 100%;" id="product" name="product">
                          <option value="">Select Product</option>
                          <?php echo $product; ?>
                        </select>                        
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="size">Size:</label>
                        <input type="text" name="size" id="size" class="form-control" autocomplete="off">                                              
                      </div>
                    </div>

                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="color">Color:</label>
                        <select class="form-control select2" style="width: 100%;" id="color" name="color">
                          <option value="">Select Color</option>
                          <?php echo $color; ?>                       
                        </select>                        
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="gender">Gender:</label>
                        <select class="form-control select2" style="width: 100%;" id="gender" name="gender">
                          <option value="">Select Gender</option>
                          <?php echo $gender; ?> 
                        </select>                        
                      </div>
                    </div>

                  </div>
                                      
                  <div class="row">                   

                    
                  </div>       

                  <div class="row">
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="purchase_date">Purchase Date</label>
                        <div class="input-group date" id="reservationjoindate" data-target-input="nearest">
                          <input type="text" name="purchase_date" id="purchase_date" class="form-control datetimepicker-input" data-target="#reservationjoindate" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm-dd" data-mask/>
                          <div class="input-group-append" data-target="#reservationjoindate" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="unit_price">Unit Price</label>
                        <input type="text" class="form-control" id="unit_price" name="unit_price">
                      </div>
                    </div>

                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="qty">Qty</label>
                        <input type="text" class="form-control" id="qty" name="qty">
                      </div>
                    </div>
                  </div>                  
                  
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button class="btn btn-sm btn-primary col-sm-3 offset-md-3" type="submit" name="add_save"><i class="fas fa-save"></i> Save</button>                  
                </div>

              </div>
              <!-- /.card -->
            </form>                        
          </div>          
        </div>
        <!-- /.row -->

      
      </div><!-- /.container-fluid -->
    </section>    

<?php
include '../inc/footer.php';
?>

<script src="/plugins/bs-stepper/main.js"></script>
<script>
$(function () {
  
  $('#add_emp_form').validate({
    rules: {     
      product: {required: true},
      size: {required: true},
      color: {required: true},
      gender: {required: true},
      purchase_date: {required: true, date:true},      
      unit_price: {required: true, number:true},
      qty: {required: true, number:true}
    },

    messages: {
    },
    errorElement: 'span',
    errorPlacement: function (error, element) {
      error.addClass('invalid-feedback');
      element.closest('.form-group').append(error);
    },
    highlight: function (element, errorClass, validClass) {
      $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass('is-invalid');
    }
  });

});
</script>

<!-- <script type="text/javascript">
  // ajax script for getting state data
   $(document).on('change','#product', function(){
      var productID = $(this).val();
      if(productID){
          $.ajax({
              type:'POST',
              url:'/backend',
              data:{'product_id':productID,'request':1},
              success:function(result){
                  $('#size').html(result);
                 
              }
          });
          $.ajax({
              type:'POST',
              url:'/backend',
              data:{'product_id':productID,'request':2},
              success:function(result){
                  $('#color').html(result);
                 
              }
          });
          $.ajax({
              type:'POST',
              url:'/backend',
              data:{'product_id':productID,'request':3},
              success:function(result){
                  $('#gender').html(result);
                 
              }
          });

      }else{
          $('#size').html('<option value="">First Select Product</option>');
          $('#color').html('<option value="">First Select Product</option>');
          $('#gender').html('<option value="">First Select Product</option>'); 
      }
  });
 
</script> -->