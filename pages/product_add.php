<?php 
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();
require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 62) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}
$error = false;
if (isset($_POST['add_save'])){

if (checkPermissions($_SESSION["user_id"], 62) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/inventory/add_product');
    exit();
}
    $product=  $_POST['product'];

    $statement = $connect->prepare("SELECT product_name FROM inventory_product WHERE product_name=:product");
    $statement->bindParam(':product', $product);

    $statement->execute();
    
    if($statement->rowCount()>0){
      $error = true;
      $errMSG = '<div class="alert alert-dismissible alert-warning bg-gradient-warning text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>Already existing.</div>';
    }

    if (!$error) {

        $data = array(
          ':product_name' =>  $_POST['product'],          

        );
       
        $query = "
        INSERT INTO `inventory_product`(`product_name`) 
        VALUES (:product_name)
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

if (isset($_POST['update_save'])){

  if (checkPermissions($_SESSION["user_id"], 63) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/inventory/add_product/'.$_GET['edit'].'');
    exit();
  }

  $data = array(
      ':product_name'     =>  $_POST['product'],      

  );

  $query = "UPDATE `inventory_product` SET `product_name`=:product_name WHERE `id`=".$_GET['edit']."";
    
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    header('location:/inventory/product_list');            
  }else{
      $errMSG = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
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
          <div class="col-md-8">
            <?php 
            if(isset($_GET['edit']))
            {
              $query = 'SELECT * FROM inventory_product WHERE status=0 AND id="'.$_GET['edit'].'"';

              $statement = $connect->prepare($query);
              $statement->execute();
              $total_data = $statement->rowCount();
              $result = $statement->fetchAll();
              if ($total_data > 0){   
                foreach($result as $row)
                {
                  ?>
                  <form action="" id="add_product_form" method="post">
                    <div class="card card-danger">
                      <div class="card-header">
                        <h3 class="card-title">Edit Product</h3>                
                      </div>
                        <!-- /.card-header -->
                      <div class="card-body">
                        <div class="form-group">
                        <label for="product">Product</label>
                        <input type="text" class="form-control" id="product" name="product" value="<?php echo $row['product_name'] ; ?>">
                      </div>

                      
                      </div>
                      <!-- /.card-body -->

                      <div class="card-footer">
                        <button class="btn btn-sm btn-primary col-sm-3 offset-md-3" name="update_save"> Save</button>
                        <button class="btn btn-sm btn-default col-sm-3" type="reset"> Cancel</button>
                      </div>

                    </div>
                    <!-- /.card -->
                  </form>
                  <?php
                }
              }else{
                ?>
                <div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>This Cannot be found.</div>
                <?php
              }
            }else{
              ?>
              <form action="" id="add_product_form" method="post">
              <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">Add Product</h3>                
                </div>
                  <!-- /.card-header -->
                <div class="card-body">
                  <div class="form-group">
                  <label for="product">Product</label>
                  <input type="text" class="form-control" id="product" name="product">
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
              <?php
            }
            
            ?>

            
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
  
  $('#add_product_form').validate({
    rules: {
      product: { required: true}
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