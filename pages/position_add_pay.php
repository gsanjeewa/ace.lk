<?php
session_start(); 
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();
require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 37) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}

$error = false;

if (isset($_POST['add_save'])){

if (checkPermissions($_SESSION["user_id"], 37) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/position_list/add_position_pay');
    exit();
}
  $department_id =  $_POST['department_id'];
  $position_id =  $_POST['position_id'];
  $position_payment =  $_POST['position_payment'];
  $statement = $connect->prepare("SELECT department_id, position_id FROM position_pay WHERE department_id=:department_id AND position_id=:position_id AND position_payment=:position_payment");
  $statement->bindParam(':department_id', $department_id);
  $statement->bindParam(':position_id', $position_id);
  $statement->bindParam(':position_payment', $position_payment);

  $statement->execute();
  
  if($statement->rowCount()>0){
    $error = true;
      $errMSG = '<div class="alert alert-dismissible alert-warning bg-gradient-warning text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>Already existing.</div>';
  }

   if (!$error) {

      $data = array(
          ':department_id'       =>  $_POST['department_id'],
          ':position_id'         =>  $_POST['position_id'],
          ':position_payment'    =>  $_POST['position_payment'],          
      );
     
      $query = "
      INSERT INTO `position_pay`(`department_id`, `position_id`, `position_payment`) 
      VALUES (:department_id, :position_id, :position_payment)
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

  if (checkPermissions($_SESSION["user_id"], 38) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/position_list/add_position_pay/'.$_GET['edit'].'');
    exit();
  }

  $data = array(

      ':department_id'       =>  $_POST['department_id'],
      ':position_id'         =>  $_POST['position_id'],
      ':position_payment'    =>  $_POST['position_payment'],      
      
  );

  $query = "UPDATE `position_pay` SET `department_id`=:department_id, `position_id`=:position_id, `position_payment`=:position_payment WHERE `position_pay_id`=".$_GET['edit']."";
    
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
     $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Success.</div>';
    header('location:/position_list/position_pay');            
  }else{
      $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
  }
    
}

include '../inc/header.php';
?>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Position Pay</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Position Pay</li>
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
              $query = 'SELECT * FROM position_pay WHERE position_pay_status = 0 AND position_pay_id="'.$_GET['edit'].'"';
              $statement = $connect->prepare($query);
              $statement->execute();
              $total_data = $statement->rowCount();              
              $result = $statement->fetchAll();
              if ($total_data > 0){                
                foreach($result as $row_payment)
                {
                  ?>
                  <form action="" id="add_position_form" method="post">
                <div class="card card-danger">
                  <div class="card-header">
                    <h3 class="card-title">Edit Position Pay</h3>                
                  </div>
                    <!-- /.card-header -->
                  <div class="card-body">
                    <div class="form-group">
                    <label for="">Institution Name</label>
                    <select class="form-control select2" style="width: 100%;" name="department_id" id="department_id">
                    <?php
                    $query="SELECT * FROM department ORDER BY department_id";
                    $statement = $connect->prepare($query);
                    $statement->execute();
                    $result = $statement->fetchAll();
                    foreach($result as $row)
                    {
                      ?>
                      <option value="<?php echo $row['department_id']; ?>" <?php if ($row_payment['department_id']==$row['department_id']){ echo "SELECTED";}?>><?php echo $row['department_name'].'-'.$row['department_location']; ?></option>
                      <?php
                    }
                    ?>
                  </select>
                  </div>

                  <div class="form-group">
                  <label for="">Position Name</label>
                  <div class="row">
                  <?php
                      $query="SELECT position_id, position_abbreviation FROM position ORDER BY position_id";
                      $statement = $connect->prepare($query);
                      $statement->execute();
                      $result = $statement->fetchAll();
                      foreach($result as $row_position)
                      {
                        ?><div class="col-md-3">
                        <div class="form-group clearfix">
                            <div class="icheck-success d-inline">
                              <input type="radio" id="radioPrimary<?php echo $row_position['position_id']; ?>" name="position_id" value="<?php echo $row_position['position_id']; ?>" <?php if ($row_position['position_id']==$row_payment['position_id']) { echo "checked";} ?>>
                              <label for="radioPrimary<?php echo $row_position['position_id']; ?>"><?php echo $row_position['position_abbreviation']; ?>
                              </label>
                            </div>
                          </div>     
                          </div>                           
                        <?php
                      }
                      ?>  
                      </div>              
                </div>

                  <div class="form-group">
                    <label for="">Position Payment</label>
                    <input type="text" class="form-control" id="" name="position_payment" value="<?php echo $row_payment['position_payment'] ; ?>">
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
              <form action="" id="add_position_form" method="post">
              <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">Add Position Pay</h3>                
                </div>
                  <!-- /.card-header -->
                <div class="card-body">
                  <div class="form-group">
                  <label for="department_id">Institution Name</label>
                  <select class="form-control select2" style="width: 100%;" name="department_id" id="department_id">
                    <option value="">Select Institution</option>
                    <?php
                    $query="SELECT * FROM department ORDER BY department_id";
                    $statement = $connect->prepare($query);
                    $statement->execute();
                    $result = $statement->fetchAll();
                    foreach($result as $row)
                    {
                      ?>
                      <option value="<?php echo $row['department_id']; ?>"><?php echo $row['department_name'].'-'.$row['department_location']; ?></option>
                      <?php
                    }
                    ?>
                  </select>
                </div>

                <div class="form-group">
                  <label for="position_id">Position Name</label>
                  <div class="row">
                  <?php
                      $query="SELECT position_id, position_abbreviation FROM position ORDER BY position_id";
                      $statement = $connect->prepare($query);
                      $statement->execute();
                      $result = $statement->fetchAll();
                      foreach($result as $row)
                      {
                        ?><div class="col-md-3">
                        <div class="form-group clearfix">
                            <div class="icheck-success d-inline">
                              <input type="radio" id="radioPrimary<?php echo $row['position_id']; ?>" name="position_id" value="<?php echo $row['position_id']; ?>">
                              <label for="radioPrimary<?php echo $row['position_id']; ?>"><?php echo $row['position_abbreviation']; ?>
                              </label>
                            </div>
                          </div>     
                          </div>                           
                        <?php
                      }
                      ?>
                    </div>
                </div>

                <div class="form-group">
                  <label for="position_payment">Payment</label>
                  <input type="text" class="form-control" id="position_payment" name="position_payment">
                </div>

                <!-- <div class="form-group">
                        <label for="no_of_shifts">Shifts</label>
                        <input type="text" class="form-control" id="no_of_shifts" name="no_of_shifts">
                      </div> --> 

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
  
  $('#add_position_form').validate({
    rules: {
      department_id: { required: true},
      position_id: {required: true},
      position_payment:{required: true, number:true}
      
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