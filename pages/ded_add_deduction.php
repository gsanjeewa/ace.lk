<?php 
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();

require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 13) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}

if (isset($_POST['add_save'])){

  if (checkPermissions($_SESSION["user_id"], 13) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/deduction_list/add_deduction');    
    exit();
}

  $deduction_en=  $_POST['deduction_en'];

  $statement = $connect->prepare("SELECT deduction_en FROM deduction WHERE deduction_en=:deduction_en");
  $statement->bindParam(':deduction_en', $deduction_en);

  $statement->execute();
  
  if($statement->rowCount()>0){
    $errMSG = '<div class="alert alert-dismissible alert-warning bg-gradient-warning text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>Already existing.</div>';
  }else{

    $data = array(
        ':deduction_en'     =>  $_POST['deduction_en'],
        ':deduction_si'     =>  $_POST['deduction_si'],
        ':deduction_status' =>  0,
        ':deduction_create_date' =>  date("Y-m-d h:i:s"),

    );
    
    $query = "
    INSERT INTO `deduction`(`deduction_en`, `deduction_si`, `deduction_status`, `deduction_create_date`) 
    VALUES (:deduction_en, :deduction_si, :deduction_status, :deduction_create_date)
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

  if (checkPermissions($_SESSION["user_id"], 14) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/deduction_list/deduction');    
    exit();
}

  $data = array(
    ':deduction_en'     =>  $_POST['deduction_en'],
    ':deduction_si'     =>  $_POST['deduction_si'],
  );

  $query = "UPDATE `deduction` SET `deduction_en`=:deduction_en, `deduction_si`=:deduction_si WHERE `deduction_id`=".$_GET['edit']."";
    
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    header('location:/deduction_list/deduction');            
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
            <h1 class="m-0 text-dark">Deduction</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Deduction</li>
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
              $query = 'SELECT * FROM deduction WHERE deduction_status=0 AND deduction_id="'.$_GET['edit'].'"';

              $statement = $connect->prepare($query);
              $statement->execute();
              $total_data = $statement->rowCount();
              $result = $statement->fetchAll();
              if ($total_data > 0) {
                foreach($result as $row)
                {
                  ?>
                  <form action="" id="add_deduction_form" method="post">
                    <div class="card card-danger">
                      <div class="card-header">
                        <h3 class="card-title">Edit Deduction</h3>                
                      </div>
                        <!-- /.card-header -->
                      <div class="card-body">
                        <div class="form-group">
                        <label for="">Deduction English</label>
                        <input type="text" class="form-control" id="" name="deduction_en" value="<?php echo $row['deduction_en']; ?>">
                      </div>

                      <div class="form-group">
                        <label for="">Deduction Sinhala</label>
                        <input type="text" class="form-control" id="" name="deduction_si" value="<?php echo $row['deduction_si']; ?>">
                      </div>

                      </div>
                      <!-- /.card-body -->

                      <dir class="card-footer">
                        <button class="btn btn-sm btn-primary col-sm-3 offset-md-3" name="update_save"> Save</button>
                        <button class="btn btn-sm btn-default col-sm-3" type="reset"> Cancel</button>
                      </dir>

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
              <form action="" id="add_deduction_form" method="post">
                  <div class="card card-success">
                    <div class="card-header">
                      <h3 class="card-title">Add Deduction</h3>                
                    </div>
                      <!-- /.card-header -->
                    <div class="card-body">
                      <div class="form-group">
                      <label for="">Deduction English</label>
                      <input type="text" class="form-control" id="" name="deduction_en">
                    </div>

                    <div class="form-group">
                      <label for="">Deduction Sinhala</label>
                      <input type="text" class="form-control" id="" name="deduction_si" >
                    </div>

                    </div>
                    <!-- /.card-body -->

                    <dir class="card-footer">
                      <button class="btn btn-sm btn-primary col-sm-3 offset-md-3" name="add_save"> Save</button>
                      <button class="btn btn-sm btn-default col-sm-3" type="reset"> Cancel</button>
                    </dir>

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
  
  $('#add_deduction_form').validate({
    rules: {
      deduction_en: { required: true},
      deduction_si: {required: true}
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