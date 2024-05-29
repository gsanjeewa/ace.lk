<?php
session_start(); 
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();
require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 66) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}
$error = false;
if (isset($_POST['add_save'])){

  if (checkPermissions($_SESSION["user_id"], 66) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/inventory/add_stock');
    exit();
}

    if (!$error) {

        $data = array(
          ':product_id' =>  $_POST['product'],
          ':qty' =>  $_POST['qty'],
          ':unit_price' =>  $_POST['unit_price'],
          ':purchase_date' =>  $_POST['purchase_date'],
        );
       
        $query = "
        INSERT INTO `inventory_stock`(`product_id`, `qty`, `unit_price`, `purchase_date`)
        VALUES (:product_id, :qty, :unit_price, :purchase_date);
        INSERT INTO `inventory_price`(`product_id`, `unit_price`, `create_date`)
        VALUES (:product_id, :unit_price, :purchase_date);
        ";
                
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
          <div class="col-md-4">
           
              <form action="" id="add_stock_form" method="post">
              <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">Add Stock</h3>                
                </div>
                  <!-- /.card-header -->
                <div class="card-body">
                  <div class="form-group">
                    <label for="product">Product</label>
                    <select class="form-control select2" style="width: 100%;" id="product" name="product">
                      <option value="">Select Product</option>
                      <?php
                      $query="SELECT * FROM inventory_product WHERE status=0 ORDER BY id ASC";
                      $statement = $connect->prepare($query);
                      $statement->execute();
                      $result = $statement->fetchAll();
                      foreach($result as $row_product)
                      {
                        ?>
                        <option value="<?php echo $row_product['id'];?>"><?php echo $row_product['product_name']; ?></option>
                        <?php
                      }
                      ?>
                    </select>   
                  </div>

                  <div class="form-group">
                    <label for="size">Size</label>
                    <select class="form-control select2" style="width: 100%;" id="size" name="size">
                      <option value="">Select size</option>                      
                    </select>   
                  </div>

                  <div class="form-group">
                    <label for="color">Color</label>
                    <select class="form-control select2" style="width: 100%;" id="color" name="color">
                      <option value="">Select Product</option>                      
                    </select>   
                  </div>

                  <div class="form-group">
                    <label for="purchase_date">Purchase Date</label>
                    <div class="input-group date" id="reservationjoindate" data-target-input="nearest">
                          <input type="text" name="purchase_date" id="purchase_date" class="form-control datetimepicker-input" data-target="#reservationjoindate" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm-dd" data-mask/>
                          <div class="input-group-append" data-target="#reservationjoindate" data-toggle="datetimepicker">
                              <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                          </div>
                        </div>
                  </div>

                  <div class="form-group">
                    <label for="unit_price">Unit Price</label>
                    <input type="text" class="form-control" id="unit_price" name="unit_price">
                  </div>

                  <div class="form-group">
                    <label for="qty">Qty</label>
                    <input type="text" class="form-control" id="qty" name="qty">
                  </div>


                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button class="btn btn-sm btn-primary col-sm-3 offset-md-3" name="add_save"> Save</button>
                  <button class="btn btn-sm btn-default col-sm-3" type="reset"> Cancel</button>
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

<script>
$(function () {
  
  $('#add_stock_form').validate({
    rules: {
      product: { required: true},
      unit_price: {required: true},
      qty: {required: true},
      purchase_date: {required: true,date:true}      
      
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