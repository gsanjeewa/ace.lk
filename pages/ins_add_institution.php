<?php 
session_start();
include '../pages/config.php';
include '../inc/timezone.php';
require_once 'system_permissions.php'; 
$connect = pdoConnection();

if (checkPermissions($_SESSION["user_id"], 21) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}

if (isset($_POST['add_save'])){

  if (checkPermissions($_SESSION["user_id"], 21) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>'; 
    header('location:/institution_list/add_institution');   
    exit();
  }

  $department_name=  $_POST['department_name'];

  $statement = $connect->prepare("SELECT department_name FROM department WHERE department_name=:department_name");
  $statement->bindParam(':department_name', $department_name);

  $statement->execute();
  
  if($statement->rowCount()>0){
      $errMSG = '<div class="alert alert-dismissible alert-warning bg-gradient-warning text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>Already existing.</div>';
  }else{

    $data = array(
      ':department_name'          =>  strtoupper(trim($_POST['department_name'])),
      ':department_address'  =>  strtoupper(trim($_POST['department_address'])),
      ':department_location'  =>  strtoupper(trim($_POST['department_location'])),
      ':department_vat'  =>  strtoupper(trim($_POST['department_vat'])),
      ':sector_id'  =>  strtoupper($_POST['sector']),
    );

    $query = "
    INSERT INTO `department`(`sector_id`, `department_name`, `department_address`, `department_location`, `department_vat`)
    VALUES (:sector_id, :department_name, :department_address, :department_location, :department_vat)
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

  if (checkPermissions($_SESSION["user_id"], 22) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>'; 
    header('location:/institution_list/institution');   
    exit();
  }

  $data = array(
    ':department_name'          =>  strtoupper(trim($_POST['department_name'])),
    ':department_address'  =>  strtoupper(trim($_POST['department_address'])),
      ':department_location'  =>  strtoupper(trim($_POST['department_location'])),
      ':department_vat'  =>  strtoupper(trim($_POST['department_vat'])), 
      ':sector_id'  =>  strtoupper($_POST['sector']),
  );

  $query = "UPDATE `department` SET `sector_id`=:sector_id, `department_name`=:department_name, `department_address`=:department_address, `department_location`=:department_location, `department_vat`=:department_vat WHERE `department_id`=".$_GET['edit']."";
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    header('location:/institution_list/institution');            
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
            <h1 class="m-0 text-dark">Institution</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Institution</li>
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
              $query = 'SELECT * FROM department WHERE department_status = 0 AND department_id="'.$_GET['edit'].'"';

              $statement = $connect->prepare($query);
              $statement->execute();
              $total_data = $statement->rowCount();
              $result = $statement->fetchAll();
              if ($total_data>0) {
                foreach($result as $row)
                {
                  ?>
                  <form action="" id="add_department_form" method="post">
                    <div class="card card-danger">
                      <div class="card-header">
                        <h3 class="card-title">Edit Institution</h3>                
                      </div>
                        <!-- /.card-header -->
                      <div class="card-body">

                        <div class="form-group">
                        <label for="sector">Sector</label>
                        <select class="form-control select2" style="width: 100%;" id="sector" name="sector">
                          <option value="">Select Sector</option>
                          <?php
                          $query="SELECT * FROM sector ORDER BY sector ASC";
                          $statement = $connect->prepare($query);
                          $statement->execute();
                          $result = $statement->fetchAll();
                          foreach($result as $row_sector)
                          {
                            ?>
                            <option value="<?php echo $row_sector['id'];?>"<?php if ($row_sector['id']==$row['sector_id']){ echo "SELECTED";}?>><?php echo $row_sector['sector']; ?></option>
                            <?php
                          }
                          ?>
                        </select>                        
                      </div>

                        <div class="form-group">
                        <label for="">Institution</label>
                        <input type="text" class="form-control text-uppercase" id="" name="department_name" value="<?php echo $row['department_name']; ?>">
                      </div>

                      <div class="form-group">
                        <label for="department_address">Address</label>
                        <input type="text" class="form-control text-uppercase" id="department_address" name="department_address" value="<?php echo $row['department_address']; ?>">
                      </div>                

                      <div class="form-group">
                        <label for="department_location">Location</label>
                        <input type="text" class="form-control text-uppercase" id="department_location" name="department_location" value="<?php echo $row['department_location']; ?>">
                      </div>

                      <div class="form-group">
                        <label for="department_vat">VAT Register No</label>
                        <input type="text" class="form-control text-uppercase" id="department_vat" name="department_vat" value="<?php echo $row['department_vat']; ?>">
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
              <form action="" id="" method="post">
                <div class="card card-success">
                  <div class="card-header">
                    <h3 class="card-title">Add Institution</h3>                
                  </div>
                    <!-- /.card-header -->
                  <div class="card-body">

                    <div class="form-group">
                        <label for="sector">Sector</label>
                        <select class="form-control select2" style="width: 100%;" id="sector" name="sector">
                          <option value="">Select Sector</option>
                          <?php
                          $query="SELECT * FROM sector ORDER BY sector ASC";
                          $statement = $connect->prepare($query);
                          $statement->execute();
                          $result = $statement->fetchAll();
                          foreach($result as $row_sector)
                          {
                            ?>
                            <option value="<?php echo $row_sector['id'];?>"><?php echo $row_sector['sector']; ?></option>
                            <?php
                          }
                          ?>
                        </select>                        
                      </div>

                    <div class="form-group">
                    <label for="department_name">Institution</label>
                    <input type="text" class="form-control text-uppercase" id="department_name" name="department_name">
                  </div>

                  <div class="form-group">
                    <label for="department_address">Address</label>
                    <input type="text" class="form-control text-uppercase" id="department_address" name="department_address" >
                  </div>                

                  <div class="form-group">
                    <label for="department_location">Location</label>
                    <input type="text" class="form-control text-uppercase" id="department_location" name="department_location" >
                  </div>

                  <div class="form-group">
                    <label for="department_vat">VAT Register No</label>
                    <input type="text" class="form-control text-uppercase" id="department_vat" name="department_vat" >
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
  
  $('#add_department_form').validate({
    rules: {
      department_name: { required: true},
      department_abbreviation: {required: true}   
      
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