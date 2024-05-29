<?php
session_start(); 
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();
require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 81) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}
if (isset($_POST['add_save'])){

  if (checkPermissions($_SESSION["user_id"], 81) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/position_list/add_position');
    exit();
  }

  $position_id=  $_POST['position_id']; 
  $statement = $connect->prepare('SELECT id FROM increment_rate WHERE position_id="'.$position_id.'" ORDER BY id DESC LIMIT 1' );
  $statement->execute();
  $total_data = $statement->rowCount();              
  $result = $statement->fetchAll();
  if ($total_data > 0){                
    foreach($result as $row)
    {
      $id=$row['id'];
    }
  }else{
    $id='';
  }

        $data = array(
            ':position_id'     =>  $_POST['position_id'],
            ':increment_rate'  =>  $_POST['increment_rate'],
            ':status'           =>  1,
            ':id'             =>  $id,
        );
       
        $query = "
        INSERT INTO `increment_rate`(`position_id`, `rate`) 
        VALUES (:position_id, :increment_rate);
        UPDATE increment_rate SET status=:status WHERE id=:id;
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

/*if (isset($_POST['update_save'])){

  if (checkPermissions($_SESSION["user_id"], 34) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/position_list/add_position/'.$_GET['edit'].'');
    exit();
  }

  $data = array(
      ':position_name'          =>  $_POST['position_name'],
      ':position_abbreviation'  =>  $_POST['position_abbreviation'],      
  );

  $query = "UPDATE `position` SET `position_name`=:position_name, `position_abbreviation`=:position_abbreviation WHERE `position_id`=".$_GET['edit']."";
    
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    header('location:/position_list/position');            
  }else{
      $errMSG = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
  }
    
}*/


include '../inc/header.php';
?>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Position</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Position</li>
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
              $query = 'SELECT * FROM increment_rate WHERE status = 0 AND id="'.$_GET['edit'].'"';
              $statement = $connect->prepare($query);
              $statement->execute();
              $total_data = $statement->rowCount();              
              $result = $statement->fetchAll();
              if ($total_data > 0){                
                foreach($result as $row)
                {
                  ?>
                  <form action="" id="add_increment_form" method="post">
                <div class="card card-danger">
                  <div class="card-header">
                    <h3 class="card-title">Edit Increment Rate</h3>                
                  </div>
                    <!-- /.card-header -->
                  <div class="card-body">
                    <div class="form-group">
                      <label for="position_id">Position Name</label>
                      <select class="form-control select2" style="width: 100%;" name="position_id" id="position_id">
                        <?php
                        $query="SELECT * FROM position ORDER BY position_id";
                        $statement = $connect->prepare($query);
                        $statement->execute();
                        $result = $statement->fetchAll();
                        foreach($result as $row_position)
                        {
                          ?>
                          <option value="<?php echo $row_position['position_id']; if ($row['position_id']==$row_position['position_id']){ echo "selected";} ?>"><?php echo $row['position_abbreviation']; ?></option>
                          <?php
                        }
                        ?>
                      </select>
                    </div>

                  <div class="form-group">
                    <label for="increment_rate">Increment Rate</label>
                    <input type="text" class="form-control" id="increment_rate" name="increment_rate" value="<?php echo $row['rate'] ; ?>">
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
              <form action="" id="add_increment_form" method="post">
              <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">Add Increment Rate</h3>                
                </div>
                  <!-- /.card-header -->
                <div class="card-body">
                  <div class="form-group">
                      <label for="position_id">Position Name</label>
                      <select class="form-control select2" style="width: 100%;" name="position_id" id="position_id">
                        <option value="">Select Position</option>
                        <?php
                        $query="SELECT * FROM position ORDER BY position_id";
                        $statement = $connect->prepare($query);
                        $statement->execute();
                        $result = $statement->fetchAll();
                        foreach($result as $row)
                        {
                          ?>
                          <option value="<?php echo $row['position_id']; ?>"><?php echo $row['position_abbreviation']; ?></option>
                          <?php
                        }
                        ?>
                      </select>
                    </div>

                  <div class="form-group">
                    <label for="increment_rate">Increment Rate</label>
                    <input type="text" class="form-control" id="increment_rate" name="increment_rate" >
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
  
  $('#add_increment_form').validate({
    rules: {
      position_id: { required: true},
      increment_rate: {required: true, number:true}      
      
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