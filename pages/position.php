<?php 
session_start();
include '../pages/config.php';
$connect = pdoConnection();
require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 35) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}
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
            <div class="card card-outline card-danger">
              <div class="card-header">
                <h3 class="card-title">Position</h3>                
              </div>
                <!-- /.card-header -->
              <div class="card-body">
                <?php                

                $query = 'SELECT * FROM position WHERE position_status = 0 ORDER BY position_id ASC';

                $statement = $connect->prepare($query);
                $statement->execute();
                $total_data = $statement->rowCount();
                $result = $statement->fetchAll();

                ?>
                <table class="table table-sm table-hover">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Name</th>
                      <th>Abbreviation</th>
                      <th>Priority</th>
                      <th>Action</th>
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
                        <td><?php echo $row['position_name']; ?></td>
                        <td><?php echo $row['position_abbreviation']; ?></td>
                        <td><?php echo $row['priority']; ?></td>
                        <td>
                          <center>
                            <button class="btn btn-sm btn-outline-primary edit_position" data-id="<?php echo $row['position_id'];?>" type="button"><i class="fa fa-edit"></i></button>
                    <button class="btn btn-sm btn-outline-danger remove_department" data-id="<?php echo $row['position_id'];?>" type="button"><i class="fa fa-trash"></i></button>                    
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
<?php
include '../inc/footer.php';
?>
<script type="text/javascript">
    $(document).ready(function() {

      $('.edit_position').click(function(){
        var $id=$(this).attr('data-id');
        location.href = "/position_list/add_position/"+$id;
        
      });      
    });
  </script>