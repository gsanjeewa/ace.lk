<?php 
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
require_once 'system_permissions.php';
$connect = pdoConnection();

if (checkPermissions($_SESSION["user_id"], 3) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions to View Employee.</div>';
    header('location:/dashbpard');
    exit();

}

include '../inc/header.php';


?>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Report</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Uniforms</li>
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
            
              <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">Employee List</h3>                
                </div>
                  <!-- /.card-header -->
                <div class="card-body">  
                  <?php
                  
                  $query="SELECT e.initial, e.surname, j.employee_no, p.position_abbreviation, a.employee_id
    FROM inventory_deduction a
    INNER JOIN join_status j ON a.employee_id = j.join_id
    INNER JOIN employee e ON j.employee_id = e.employee_id
    INNER JOIN promotions c ON j.join_id = c.employee_id    
    INNER JOIN (SELECT employee_id, MAX(id) maxid_pro FROM promotions GROUP BY employee_id) d ON c.employee_id = d.employee_id AND c.id = d.maxid_pro
    INNER JOIN position p ON c.position_id = p.position_id    
    GROUP BY a.employee_id ORDER BY cast(j.employee_no as int) ASC";

                  /*$query = 'SELECT * FROM employee ORDER BY employee_id ASC';*/

                  $statement = $connect->prepare($query);
                  $statement->execute();
                  $total_data = $statement->rowCount();
                  $result = $statement->fetchAll();

                  ?>

                  <table id="example2" class="table table-bordered table-striped">
                    <thead style="text-align: center; width: 100%;">
                      <tr>
                        <th>#</th>                        
                        <th>Employee No</th>
                        <th>Rank</th>
                        <th>Name</th>
                        <th>Total Deduction</th>
                        <th>Paid Amount</th>
                        <th>Remaining Amount</th>                       
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $startpoint =0;
                      $sno = $startpoint + 1;
                      foreach($result as $row)
                      {
                        
                        $statement = $connect->prepare('SELECT SUM(amount) AS total_paid FROM inventory_deduction WHERE employee_id="'.$row['employee_id'].'" AND  status=1');
                      $statement->execute();
                      $result = $statement->fetchAll();
                      if ($statement->rowCount() > 0) :
                        foreach($result as $row_paid):

                          $total_paid = $row_paid['total_paid'];
                        endforeach;
                      else:
                        $total_paid ='';
                      endif;

                      $statement = $connect->prepare('SELECT SUM(amount) AS total_ded FROM inventory_deduction WHERE employee_id="'.$row['employee_id'].'"');
                      $statement->execute();
                      $result = $statement->fetchAll();
                      if ($statement->rowCount() > 0) :
                        foreach($result as $row_ded):

                          $total_ded = $row_ded['total_ded'];
                        endforeach;
                      else:
                        $total_ded ='';
                      endif;

                        
                        $statement = $connect->prepare('SELECT SUM(amount) AS total_rem FROM inventory_deduction WHERE employee_id="'.$row['employee_id'].'" AND status=0');
                      $statement->execute();
                      $result = $statement->fetchAll();
                      if ($statement->rowCount() > 0) :
                        foreach($result as $row_rem):

                          $total_rem = $row_rem['total_rem'];
                        endforeach;
                      else:
                        $total_rem ='';
                      endif;

                        ?>
                        <tr>
                            <td><?php echo $sno; ?></td>
                            <td style="text-align: left;"><?php echo $row['employee_no'];?></td>
                            <td style="text-align: left;"><?php echo $row['position_abbreviation'];?></td>
                            <td style="text-align: left;"><?php echo $row['surname'].' '.$row['initial'];?></td>
                            <td style="text-align: right;"><?php echo number_format($total_ded,2);?></td>                            
                            <td style="text-align: right;"><?php echo number_format($total_paid,2);?></td>                           
                            <td style="text-align: right;"><?php echo number_format($total_rem,2);?></td>                           
                            
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
      "scrollX": false,
    "buttons": ["excel"]
    }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');

 
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    });

  });
</script>