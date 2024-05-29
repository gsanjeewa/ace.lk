<?php 
session_start();
include '../pages/config.php';
include '../inc/timezone.php';
$connect = pdoConnection();
require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 80) == "false") {

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
        <!-- Small boxes (Stat box) -->
        <div class="row">          
          <div class="col-md-8">
            <div class="card card-outline card-danger">
              <div class="card-header">
                <h3 class="card-title">Increment Rate</h3>                
              </div>
                <!-- /.card-header -->
              <div class="card-body">
                <?php
                $query = 'SELECT b.position_abbreviation, a.rate, a.status FROM increment_rate a INNER JOIN position b ON a.position_id=b.position_id ORDER BY a.position_id';

                $statement = $connect->prepare($query);
                $statement->execute();
                $total_data = $statement->rowCount();
                $result = $statement->fetchAll();

                ?>
                <table class="table table-sm table-hover">
                  <thead>
                    <tr style="text-align: center;">
                      <th>#</th>
                      <th>Position Name</th>
                      <th>Rate</th>
                      <th>Status</th>
                      <th>Date</th>
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
                        <td><?php echo $row['position_abbreviation']; ?></td>
                        <td style="text-align: right;"><?php echo number_format($row['rate'], 2); ?></td>
                        <td>
                          <center>
                            <?php if($row['status'] == 0): ?>
                              <span class="badge badge-success">Eligible</span>
                            <?php elseif($row['status'] == 1): ?>
                              <span class="badge badge-danger">Not Elegible</span>
                            <?php endif ?>
                          </center>
                        </td>
                        <td>
                          <?php
                          echo date('Y-m-d', $row['create_date']);
                          ?>
                        </td>
                        <td>
                          <center>
                            <?php if($row['status'] == 0): ?>
                              <button class="btn btn-sm btn-outline-primary edit_position" data-id="<?php echo $row['id']?>" type="button"><i class="fa fa-edit"></i></button>
                            <?php endif ?>


                            
                    <button class="btn btn-sm btn-outline-danger remove_department" data-id="<?php echo $row['position_pay_id']?>" type="button"><i class="fa fa-trash"></i></button>                    
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
        location.href = "/position_list/add_position_pay/"+$id;
        
      });      
    });
  </script>