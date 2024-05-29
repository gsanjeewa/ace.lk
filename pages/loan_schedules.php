<?php
session_start(); 
include '../pages/config.php';
include '../inc/timezone.php';
$connect = pdoConnection();
require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 17) == "false") {

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
            <h1 class="m-0 text-dark">Loan</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Loan</li>
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
          if ( isset($_SESSION["errMSG"]) ) {
            ?>
            <div class="col-xl-12 col-md-6 mb-4">
              <?php echo $_SESSION["errMSG"]; ?>
            </div>
              <?php
          }

          ?>
        </div>
        <div class="row">          
          <div class="col-md-12">
            
              <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">Loan Schedules</h3>                
                </div>
                  <!-- /.card-header -->
                <div class="card-body">  

                  <?php

                  $query = 'SELECT * FROM loan_schedules WHERE loan_id="'.$_GET['edit'].'" ORDER BY id DESC';

                  $statement = $connect->prepare($query);
                  $statement->execute();
                  $total_data = $statement->rowCount();

                  $result = $statement->fetchAll();

                  ?>

                  <table id="example2" class="table table-bordered table-striped">
                    <thead style="text-align: center;">
                      <tr>
                        <th>#</th>                        
                        <th>Monthly Installment</th>
                        <th>date_effective</th>
                        <th>Status</th>                        
                        <!--<th>Action</th>-->
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $startpoint =0;
                      $sno = $startpoint + 1;
                      foreach($result as $row)
                      {
                        $query = 'SELECT e.surname, e.initial, e.nic_no, j.employee_no FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid INNER JOIN position p ON e.position_id=p.position_id WHERE j.join_id="'.$row['employee_id'].'" ORDER BY e.employee_id DESC';

                        $statement = $connect->prepare($query);
                        $statement->execute();
                        $total_data = $statement->rowCount();
                        $result = $statement->fetchAll();
                        foreach($result as $row_employee):                          
                        endforeach;
                                      
                        if ($row['status'] == 0) {
                          $status='<span class="right badge badge-warning">Pending</span>';
                        }elseif ($row['status'] == 1) {
                          $status='<span class="right badge badge-success">Paid</span>';
                        }else{
                          $status='<span class="right badge badge-secondary">Unidentified</span>';
                        }
                       
                        ?>
                        <tr>
                            <td><?php echo $sno; ?></td>
                            <td><?php echo number_format($row['paid_amount']); ?></td>
                            <td ><?php echo $row['date_due'];?></td>
                            <td><center><?php echo $status;?></center></td>
                            <!--<td>
                              <center>
                                <form action="" method="post" >
                                  <input type="hidden" name="loan_id" value="<?php echo $row['id'];?>">
                                <?php 
                                if ($row['status'] == 0) {
                                  ?>
                                  <button class="btn btn-sm btn-outline-success approved" name="approved" data-toggle="tooltip" data-placement="top" title="Approved"><i class="fa fa-check"></i></button>

                          <?php
                        }
                        ?>
                        </form>
                              </center>
                            </td>-->
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

    $('.edit_loan').click(function(){
        var $id=$(this).attr('data-id');
        location.href = "/loan/new_loan_req/"+$id;
        
      });

      $('.view_loan').click(function(){
        var $id=$(this).attr('data-id');
        location.href = "/loan/loan_list/"+$id;
        
      });  

      $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    });           

    });
  </script>