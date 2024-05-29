<?php 
session_start();
include '../pages/config.php';
include '../inc/timezone.php';
$connect = pdoConnection();
require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 39) == "false") {

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
                <h3 class="card-title">Position Payment</h3>                
              </div>
                <!-- /.card-header -->
              <div class="card-body">
                <?php
                /*$query = 'SELECT position_pay.position_pay_id, position_pay.position_payment, department.department_name, position.position_abbreviation FROM position_pay INNER JOIN department ON position_pay.department_id=department.department_id INNER JOIN position ON position_pay.position_id=position.position_id ORDER BY position_pay.position_pay_id';*/
                $query = 'SELECT a.position_payment, b.department_name, a.department_id, b.department_location FROM position_pay a INNER JOIN department b ON a.department_id=b.department_id WHERE b.department_status=0 GROUP BY a.department_id ORDER BY a.department_id';
                $statement = $connect->prepare($query);
                $statement->execute();
                $total_data = $statement->rowCount();
                $result = $statement->fetchAll();

                ?>
                <table class="table table-sm table-hover" id="example2">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Institution Name</th>
                      <th>Position Payment</th>                      
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
                        <td><?php echo $row['department_name'].'-'.$row['department_location']; ?></td>
                        <td>
                          <table>
                            <?php 
                       $query = 'SELECT a.position_pay_id, b.position_abbreviation, a.position_payment FROM position_pay a INNER JOIN position b ON a.position_id=b.position_id WHERE a.department_id="'.$row['department_id'].'"';

                        $statement = $connect->prepare($query);
                $statement->execute();
                $total_data = $statement->rowCount();
                $result = $statement->fetchAll();
                
                foreach($result as $row_p):
                  ?>
                  
                    <tr>
                      <td><?php echo $row_p['position_abbreviation'];?></td>
                      <td><?php echo number_format($row_p['position_payment']);?></td>
                      <td><center>
                        <form action="" method="POST">  
                        <input type="hidden" name="position_pay_id" value="<?php echo $row_p['position_pay_id']?>">

                            <a href="/position_list/add_position_pay/<?php echo $row_p['position_pay_id']?>" class="btn btn-sm btn-outline-primary"><i class="fa fa-edit"></i></a>
                    <button class="btn btn-sm btn-outline-danger remove_department" data-id="<?php echo $row_p['position_pay_id']?>" type="button"><i class="fa fa-trash"></i></button>                    
                          </center>
                                                   
                    <!-- <button class="btn btn-sm btn-outline-danger" name="remove_deduction" type="submit"><i class="fa fa-trash"></i></button> -->
                    </form>
                          
                        </td>
                    </tr>
                  
                  <?php
                  
                  
                  endforeach;
                        ?></table>

                        
                        
                                                    
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
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
      });

      $('.edit_position').click(function(){
        var $id=$(this).attr('data-id');
        location.href = "/position_list/add_position_pay/"+$id;
        
      });      
    });
  </script>