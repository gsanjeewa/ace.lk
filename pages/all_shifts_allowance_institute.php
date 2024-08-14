<?php 
session_start();
include '../pages/config.php';
include '../inc/timezone.php';
require_once 'system_permissions.php';
$connect = pdoConnection();

if (checkPermissions($_SESSION["user_id"], 85) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}

$error = false;

if (isset($_POST['add_new'])) {
  if (checkPermissions($_SESSION["user_id"], 83) == "false") {
      $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>'; 
      header('location:/allowance_list/shifts_allowance_institute');   
      exit();
  }

  if (!$error) {
      // Start a transaction
      $connect->beginTransaction();

      try {
          $query = "
              INSERT INTO shifts_allowance_institute (department_id, position_id, allowance, total_shifts)
              VALUES (:department_id, :position_id, :allowance, :total_shifts)
          ";
          $statement = $connect->prepare($query);

          // Initialize counters
          $insertCount = 0;
          $duplicateCount = 0;

          // Iterate through the arrays and insert each row
          for ($i = 0; $i < count($_POST['position_id']); $i++) {
              // Check for duplicates
              $checkQuery = "
                  SELECT COUNT(*) 
                  FROM shifts_allowance_institute 
                  WHERE department_id = :department_id AND position_id = :position_id
              ";
              $checkStatement = $connect->prepare($checkQuery);
              $checkStatement->execute(array(
                  ':department_id' => $_POST['department_id'],
                  ':position_id' => $_POST['position_id'][$i]
              ));
              $count = $checkStatement->fetchColumn();

              if ($count == 0) {
                  // Insert only if no duplicate exists
                  $statement->execute(array(
                      ':department_id' => $_POST['department_id'],
                      ':position_id' => $_POST['position_id'][$i],
                      ':allowance' => $_POST['allowance'],
                      ':total_shifts' => $_POST['total_shifts']
                  ));
                  $insertCount++;
              } else {
                  $duplicateCount++;
              }
          }

          // Commit the transaction
          $connect->commit();

          $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <span class="glyphicon glyphicon-info-sign"></span> Success. ' . $insertCount . ' rows inserted. ' . $duplicateCount . ' duplicates found.</div>'; 
      } catch (Exception $e) {
          // Rollback the transaction if something went wrong
          $connect->rollBack();
          $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <i class="fas fa-fw fa-times"></i>Cannot Save. Error: ' . $e->getMessage() . '</div>';
      }
  }
}


if(isset($_POST['insert']))
{
    if (checkPermissions($_SESSION["user_id"], 84) == "false") {

        $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>'; 
        header('location:/allowance_list/shifts_allowance_institute');   
        exit();
    }

    $data = array(
        ':id'         =>  $_SESSION['editbid'],
        ':department_id'  =>  $_POST['department_id'],
        ':position_id'  =>  $_POST['position_id'],
        ':allowance'  =>  $_POST['allowance'],
        ':total_shifts' => $_POST['total_shifts']
    );
   
    $query = "UPDATE `shifts_allowance_institute` SET `department_id`=:department_id, `position_id`=:position_id, `allowance`=:allowance, `total_shifts`=:total_shifts WHERE `id`=:id";
            
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

if (isset($_POST['shift_disable'])){

  if (checkPermissions($_SESSION["user_id"], 84) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/allowance_list/shifts_allowance_institute');
    exit();
}

  $data = array(
    ':id'      =>  $_POST['shift_id'],
    ':status'  => 1,      
  );

  $query = "UPDATE `shifts_allowance_institute` SET `status`=:status WHERE `id`=:id";
    
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';
    header('location:/allowance_list/shifts_allowance_institute');            
  }else{
      $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
  }
    
}  

if (isset($_POST['shift_enable'])){

  if (checkPermissions($_SESSION["user_id"], 84) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/allowance_list/shifts_allowance_institute');
    exit();
}

  $data = array(
    ':id'      =>  $_POST['shift_id'],
    ':status'  => 0,      
  );

  $query = "UPDATE `shifts_allowance_institute` SET `status`=:status WHERE `id`=:id";
    
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';
    header('location:/allowance_list/shifts_allowance_institute');            
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
            <h1 class="m-0 text-dark">Allowance</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Allowance</li>
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
          if ( isset($_SESSION["msg"]) ) {
            ?>
            <div class="col-xl-12 col-md-6 mb-4">
              <?php echo $_SESSION["msg"];
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
                  <h3 class="card-title">Shifts Institute</h3>
                  <button class="edit_data4 btn btn-sm bg-gradient-primary float-right" type="button" ><i class="fas fa-plus"></i> Add</button>                
                </div>
                  <!-- /.card-header -->
                <div class="card-body">  

                  <?php

                  $query = 'SELECT b.department_name, b.department_location, a.allowance, a.id, a.status, c.position_abbreviation, a.total_shifts FROM shifts_allowance_institute a INNER JOIN department b ON a.department_id=b.department_id INNER JOIN position c ON a.position_id=c.position_id ORDER BY b.department_name ASC';

                  $statement = $connect->prepare($query);
                  $statement->execute();
                  $total_data = $statement->rowCount();

                  $result = $statement->fetchAll();

                  ?>

                  <table id="example2" class="table table-bordered table-sm table-striped">
                    <thead style="text-align: center;">
                      <tr>
                        <th>#</th>                        
                        <th>Institution</th>
                        <th>Position</th>
                        <th>Allowance</th>
                        <th>Total Shifts</th>
                        <th>Status</th>
                        <th>Action</th>                                                  
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $startpoint =0;
                      $sno = $startpoint + 1;
                      foreach($result as $row)
                      {
                        if($row['status'] == 0): 
                        $status='<span class="badge badge-success">Active</span>';
                       elseif($row['status'] == 1): 
                        $status='<span class="badge badge-danger">Deactive</span>';
                      
                       endif;                    
                        ?>
                        <tr>
                            <td><center><?php echo $sno; ?></center></td>
                            <td><?php echo $row['department_name'].'-'.$row['department_location']; ?></td>
                            <td><?php echo $row['position_abbreviation']; ?></td>
                            <td><center><?php echo $row['allowance']; ?></center></td>
                            <td><center><?php echo $row['total_shifts']; ?></center></td>
                            <td><center><?php echo $status; ?></center></td>
                            <td>
                              <center>
                                <form action="" method="POST">
                              <input type="hidden" name="shift_id" value="<?php echo $row['id']?>">
                                
                                <?php if($row['status'] == 0): ?>
                                  <button class="edit_data4 btn btn-sm btn-outline-primary" data-id="<?php echo $row['id'];?>" type="button" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></button>
                                  <button class="btn btn-sm btn-outline-danger" type="submit" data-toggle="tooltip" data-placement="top" title="Disable" name="shift_disable"><i class="fas fa-toggle-on"></i></button>

                                <?php elseif($row['status'] == 1): ?>

                                  <button class="btn btn-sm btn-outline-success" type="submit" data-toggle="tooltip" data-placement="top" title="Enable" name="shift_enable"><i class="fas fa-toggle-off"></i></button>
                               <?php endif;  ?>
                              
                            </form>
                              </center>
                            </td>
                        </tr>
                        <?php
                        $sno ++;
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->
          </div>          
        </div>
        <!-- /.row -->
       
      
      </div><!-- /.container-fluid -->
    </section> 

    <!--  start  modal -->
    <div id="editData4" class="modal fade">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Shifts Institute</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="info_update4">
            <?php @include("/edit_shifts_allowance_institute");?>
          </div>
          <div class="modal-footer ">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->
    </div>
    <!--   end modal -->       

<?php
include '../inc/footer.php';
?>

<script type="text/javascript">
    $(document).ready(function() {

      $('#example2').DataTable({
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    }); 

    
      $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    });           

    });
  </script>

  <script type="text/javascript">
    $(document).ready(function(){
      $(document).on('click','.edit_data4',function(){
        $("#editData4").modal({
          backdrop: 'static',
          keyboard: false
        });
        var edit_id4=$(this).attr('data-id');
        $.ajax({
          url:"/edit_shifts_allowance_institute",
          type:"post",
          data:{edit_id4:edit_id4},
          success:function(data){
            $("#info_update4").html(data);
            $("#editData4").modal('show');
          }
        });
      });
    });
  </script>
