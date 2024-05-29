<?php 
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();
require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 46) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}

$error = false;
if (isset($_POST['add_save'])){

  if (checkPermissions($_SESSION["user_id"], 46) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/ration/add_supplier');
    exit();
  }

    $supplier_name=  $_POST['supplier_name'];

    $statement = $connect->prepare("SELECT supplier_name FROM ration_supplier_list WHERE supplier_name=:supplier_name");
    $statement->bindParam(':supplier_name', $supplier_name);

    $statement->execute();
    
    if($statement->rowCount()>0){
      $error = true;
      $errMSG = '<div class="alert alert-dismissible alert-warning bg-gradient-warning text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>Already existing.</div>';
    }

    if (!$error) {

        $data = array(
          ':supplier_name' =>  strtoupper(trim($_POST['supplier_name'])),          
          ':bank_name'          =>  $_POST['bank_name'],
          ':branch_name'        =>  $_POST['bank_branch'],
          ':account_no'         =>  $_POST['account_no'],
        );
       
        $query = "
        INSERT INTO `ration_supplier_list`(`supplier_name`, `bank_id`, `branch_id`, `bank_account`) 
        VALUES (:supplier_name, :bank_name, :branch_name, :account_no)
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

  if (checkPermissions($_SESSION["user_id"], 47) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/ration/add_supplier/'.$_GET['edit'].'');
    exit();
  }

  $data = array(
    ':supplier_name'  =>  strtoupper(trim($_POST['supplier_name'])),
    ':bank_id'          =>  $_POST['bank_name'],
    ':branch_id'        =>  $_POST['bank_branch'],
    ':bank_account'         =>  $_POST['account_no'],
    ':status'  =>  $_POST['status'],
  );

  $query = "UPDATE `ration_supplier_list` SET `supplier_name`=:supplier_name, `bank_id`=:bank_id, `branch_id`=:branch_id, `bank_account`=:bank_account, `status`=:status WHERE `id`=".$_GET['edit']."";
    
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    header('location:/ration/supplier_list');            
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
            <h1 class="m-0 text-dark">Ration</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Ration</li>
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
              $query = 'SELECT * FROM ration_supplier_list WHERE id="'.$_GET['edit'].'"';

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
                        <h3 class="card-title">Edit Supplier</h3>                
                      </div>
                        <!-- /.card-header -->
                      <div class="card-body">
                        <div class="form-group">
                          <label for="supplier_name">Supplier Name</label>
                          <input type="text" class="form-control" id="supplier_name" name="supplier_name" value="<?php echo $row['supplier_name'] ; ?>">
                        </div>
                        
                        <div class="form-group">
                          <label for="bank_name">Bank Name</label>
                          <select class="form-control select2" style="width: 100%;" id="bank_name" name="bank_name">
                            <option value="">Select Bank</option>
                            <?php
                            $query="SELECT * FROM bank_name ORDER BY bank_name ASC";
                            $statement = $connect->prepare($query);
                            $statement->execute();
                            $result = $statement->fetchAll();
                            foreach($result as $row_bank)
                            {
                              ?>
                              <option value="<?php echo $row_bank['id'];?>"<?php if ($row_bank['id']==$row['bank_id']){ echo "SELECTED";}?>><?php echo $row_bank['bank_name'].' ('.$row_bank['bank_no'].')'; ?></option>
                              <?php
                            }
                            ?>
                          </select>                        
                        </div>
                        
                        <div class="form-group">
                          <label for="bank_branch">Branch</label>
                          <select class="form-control select2" style="width: 100%;" id="bank_branch" name="bank_branch">
                            <option value="">Select Branch</option> 
                            <?php
                            $query="SELECT * FROM bank_branch ORDER BY branch_name ASC";
                            $statement = $connect->prepare($query);
                            $statement->execute();
                            $result = $statement->fetchAll();
                            foreach($result as $row_branch)
                            {
                              ?>
                              <option value="<?php echo $row_branch['id'];?>"<?php if ($row_branch['id']==$row['branch_id']){ echo "SELECTED";}?>><?php echo $row_branch['branch_name'].' ('.$row_branch['branch_no'].')'; ?></option>
                              <?php
                            }
                            ?>

                          </select>                        
                        </div>
                      
                        <div class="form-group">
                          <label for="account_no">Account no</label>
                          <input type="text" class="form-control" id="account_no" name="account_no" value="<?php echo $row['bank_account'] ; ?>">
                        </div>

                        <div class="form-group">
                          <label for="status">Status</label>
                          <select class="form-control" id="status" name="status" >
                            <option value="">Select Status</option>
                            <option value="0"<?php if ($row['status']==0){ echo "SELECTED";}?>>Supply</option>
                            <option value="1"<?php if ($row['status']==1){ echo "SELECTED"; } ?>>Not Supply</option>
                          </select>                          
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
                  <h3 class="card-title">Add Supplier</h3>                
                </div>
                  <!-- /.card-header -->
                <div class="card-body">
                  <div class="form-group">
                  <label for="supplier_name">Supplier Name</label>
                  <input type="text" class="form-control text-uppercase" id="supplier_name" name="supplier_name">
                </div>
                
                <div class="form-group">
                  <label for="bank_name">Bank Name</label>
                  <select class="form-control select2" style="width: 100%;" id="bank_name" name="bank_name">
                    <option value="">Select Bank</option>
                    <?php
                    $query="SELECT * FROM bank_name ORDER BY bank_name ASC";
                    $statement = $connect->prepare($query);
                    $statement->execute();
                    $result = $statement->fetchAll();
                    foreach($result as $row_bank)
                    {
                      ?>
                      <option value="<?php echo $row_bank['id'];?>"><?php echo $row_bank['bank_name'].' ('.$row_bank['bank_no'].')'; ?></option>
                      <?php
                    }
                    ?>
                  </select>                        
                </div>

                
                <div class="form-group">
                  <label for="bank_branch">Branch</label>
                  <select class="form-control select2" style="width: 100%;" id="bank_branch" name="bank_branch">
                    <option value="">Select Branch</option>                          
                  </select>                        
                </div>
              
                <div class="form-group">
                  <label for="account_no">Account no</label>
                  <input type="text" class="form-control" id="account_no" name="account_no">
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
      supplier_name: { required: true}
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

$(document).on('change','#bank_name', function(){
      var bank_nameID = $(this).val();
      if(bank_nameID){
          $.ajax({
              type:'POST',
              url:'/backend-script',
              data:{'bank_name_id':bank_nameID,'request':3},
              success:function(result){
                  $('#bank_branch').html(result);
                 
              }
          });          
      }else{
          $('#bank_branch').html('<option value="">First Select Bank</option>');          
      }
  });

</script>