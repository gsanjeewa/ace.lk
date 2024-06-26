<?php 
session_start();
include '../pages/config.php';
include '../inc/timezone.php';
require_once 'system_permissions.php'; 
$connect = pdoConnection();

if (checkPermissions($_SESSION["user_id"], 23) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}

if (isset($_POST['department_disable'])){

  if (checkPermissions($_SESSION["user_id"], 24) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/institution_list/institution');
    exit();
}

  $data = array(
    ':id'      =>  $_POST['deduction_id']
       
  );

  $query = "UPDATE `department` SET `department_status`=1 WHERE `department_id`=:id";
    
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>
    <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';
               
  }else{
      $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
  }
    
}


if (isset($_POST['department_enable'])){

  if (checkPermissions($_SESSION["user_id"], 24) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/institution_list/institution');
    exit();
}

  $data = array(
    ':id'      =>  $_POST['deduction_id']
       
  );

  $query = "UPDATE `department` SET `department_status`=0 WHERE `department_id`=:id";
    
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>
    <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';
               
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
          <div class="col-md-12">
            <div class="card card-outline card-danger">
              <div class="card-header">
                <h3 class="card-title">Institution</h3>                
              </div>
                <!-- /.card-header -->
              <div class="card-body">
                <?php
                $query = 'SELECT a.sector_id, b.sector FROM department a INNER JOIN sector b ON a.sector_id=b.id GROUP BY a.sector_id ORDER BY b.sector';               

                $statement = $connect->prepare($query);
                $statement->execute();
                $total_data = $statement->rowCount();
                $result = $statement->fetchAll();

                ?>
                <table class="table table-sm table-hover" id="example2">
                  <thead>
                    <tr>
                      <th><center>#</center></th>
                      <th><center>Sector</center></th>
                      <th><center>Institution</center></th>                      
                    </tr>                    
                  </thead>
                  <tbody>
                    <?php
                    $startpoint =0;
                    $sno = $startpoint + 1;
                    foreach($result as $row)
                    {
                      

                      ?>
                      <tr>
                        <td><?php echo $sno; ?></td>
                        <td><?php echo $row['sector']; ?></td>
                        <td>
                          <table>
                            <?php 
                            if (checkPermissions($_SESSION["user_id"], 24) == "true") {
                              $query_p = 'SELECT * FROM department WHERE sector_id="'.$row['sector_id'].'" AND (department_status = 0 OR department_status = 1) ORDER BY department_status ASC, department_name ASC';
                            }else{
                              $query_p = 'SELECT * FROM department WHERE sector_id="'.$row['sector_id'].'" AND department_status = 0 ORDER BY department_status ASC, department_name ASC';
                            }
                            
                            $statement = $connect->prepare($query_p);
                            $statement->execute();
                            $total_data = $statement->rowCount();
                            $result = $statement->fetchAll();

                            foreach($result as $row_p):
                              ?>

                            <tr>
                              <td style="width:50%;">
                                <?php echo $row_p['department_name'].' - '.$row_p['department_location']; ?>
                              </td>
                              <td style="width:50%;">
                          <center>
                            <div class="btn-group">
                            <form action="" method="POST" enctype="multipart/form-data">
                              <input type="hidden" name="deduction_id" value="<?php echo $row_p['department_id']; ?>">

                              <?php if ($row_p['department_status']==0): ?>
                                <a class="btn btn-xs btn-outline-primary" href="/institution_list/add_institution/<?php echo $row_p['department_id'];?>" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-edit"></i></a>
                                 <?php if (checkPermissions($_SESSION["user_id"], 24) == "true") {
                                  ?>
                                <button class="btn btn-xs btn-outline-danger" type="submit" data-toggle="tooltip" data-placement="top" title="Disable" name="department_disable"><i class="fas fa-toggle-on"></i></button>
                                <?php
                                }
                                ?>
                                <a class="btn btn-xs btn-outline-warning" href="/institution_list/institution/<?php echo $row_p['department_id']?>" data-toggle="tooltip" data-placement="top" title="Mark Attendance"><i class="fa fa-plus"></i></a>

                                <a class="btn btn-xs btn-outline-secondary" href="/institution_list/institution/salary_advance/<?php echo $row_p['department_id']?>" data-toggle="tooltip" data-placement="top" title="Salary Advance"><i class="fas fa-money-bill-alt"></i></a>
                                
                                <a class="btn btn-xs btn-outline-info" href="/institution_list/institution/invoice/<?php echo $row_p['department_id']?>" data-toggle="tooltip" data-placement="top" title="Invoice"><i class="fas fa-file-invoice-dollar"></i></a>
                                
                                <a class="btn btn-xs btn-outline-danger" href="/institution_list/institution/hostel/<?php echo $row_p['department_id']?>" data-toggle="tooltip" data-placement="top" title="Hostel"><i class="fas fa-hotel"></i></a>

                                <a class="btn btn-xs btn-outline-warning" href="/institution_list/institution/ration/<?php echo $row_p['department_id']?>" data-toggle="tooltip" data-placement="top" title="Ration"><i class="fas fa-bread-slice"></i></a>
                                
                                <a class="btn btn-xs btn-outline-primary" href="/institution_list/institution/fines/<?php echo $row_p['department_id']?>" data-toggle="tooltip" data-placement="top" title="Fines"><i class="fas fa-ban"></i></a>

                                <a class="btn btn-xs btn-outline-warning" href="/institution_list/institution/extra_ot/<?php echo $row_p['department_id']?>" data-toggle="tooltip" data-placement="top" title="Extra OT"><i class="fa fa-plus"></i></a>

                                <a class="btn btn-xs btn-outline-danger" href="/institution_list/institution/d_attendance/<?php echo $row_p['department_id']?>" data-toggle="tooltip" data-placement="top" title="Dummy Attendance"><i class="fa fa-plus"></i></a>

                                <a class="btn btn-xs btn-outline-secondary" href="/institution_list/institution/to_be_applied/<?php echo $row_p['department_id']?>" data-toggle="tooltip" data-placement="top" title="To be applied"><i class="fa fa-plus"></i></a>

                                <?php else:?>
                                  <?php if (checkPermissions($_SESSION["user_id"], 24) == "true") {
                                  ?>
                                  <button class="btn btn-xs btn-outline-success" type="submit" data-toggle="tooltip" data-placement="top" title="Enable" name="department_enable"><i class="fas fa-toggle-off"></i></button>
                                  <?php
                                  }
                                  ?>
                              <?php endif ?>                        
                             </form>
                           </div>
                          </center>
                          
                  </td>
                            </tr>
                            <?php
                  
                  
                  endforeach;
                        ?>
                          </table>
                          
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
<?php
include '../inc/footer.php';
?>
<script type="text/javascript">
    $(document).ready(function() {

      $('#example2').DataTable({
      "paging": false,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": false,
      "scrollX": true,
    });

      $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    });

    });
  </script>