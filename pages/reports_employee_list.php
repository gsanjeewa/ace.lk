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
              <li class="breadcrumb-item active">Employee</li>
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
                  
                  $query="SELECT e.employee_id, e.surname, e.initial, j.employee_no, e.nic_no, e.permanent_address, e.mobile_no, j.employee_status, j.location, p.position_abbreviation, j.join_id, j.join_date FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid INNER JOIN promotions c ON j.employee_id=c.employee_id INNER JOIN position p ON c.position_id=p.position_id ORDER BY ABS(j.employee_no) DESC";

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
                        <th>No</th>
                        <th>Rank</th>
                        <th>Initial</th>              
                        <th>Name</th>
                        <th>NIC No</th>
                        <th>Basic Salary</th>
                        <th>Date of Join</th>
                        <th>Location</th>
                        <th>Bank Account</th>
                        <th>Status</th>                        
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $startpoint =0;
                      $sno = $startpoint + 1;
                      foreach($result as $row)
                      {
                        $statement = $connect->prepare('SELECT c.position_abbreviation FROM promotions a INNER JOIN position c ON a.position_id=c.position_id INNER JOIN (SELECT employee_id, MAX(id) maxid FROM promotions GROUP BY employee_id) b ON a.employee_id = b.employee_id AND a.id = b.maxid WHERE a.employee_id="'.$row['join_id'].'"');
                          $statement->execute();
                          $total_position = $statement->rowCount();
                          $result = $statement->fetchAll();
                          if ($total_position > 0) :
                            foreach($result as $position_name):
                  
                              $position_id = $position_name['position_abbreviation'];
                            endforeach;
                          else:
                            $position_id ='';
                          endif;
                          
                        $statement = $connect->prepare('SELECT a.basic_salary FROM salary a INNER JOIN (SELECT employee_id, MAX(id) maxid FROM salary GROUP BY employee_id) b ON a.employee_id = b.employee_id AND a.id = b.maxid WHERE a.employee_id="'.$row['join_id'].'"');
                        $statement->execute();
                        $result = $statement->fetchAll();

                        foreach($result as $row_inc)
                        { 
                        }

                        $statement = $connect->prepare('SELECT department_name, department_location FROM department WHERE department_id="'.$row['location'].'"');
                        $statement->execute();
                        $total_loc = $statement->rowCount();
                        $result = $statement->fetchAll();
                        if ($total_loc>0) {                                                 
                          foreach($result as $row_loc)
                          {
                            $location=$row_loc['department_name'].'-'.$row_loc['department_location'];
                          }
                        }else{
                          $location='';
                        }
                          if (!empty($row['employee_no'])) {
                              $employee_epf=$row['employee_no'];
                          }else{
                            $employee_epf='';
                          }
                          
                          if($row['join_date']!='0000-00-00'):

                          $date1 = $row['join_date'];

                          $date2 = '2023-09-29';

                          $diff = abs(strtotime($date2)-strtotime($date1));

                          $years = floor($diff / (365*60*60*24));

                          $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));

                          $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

                          endif;

                          $statement = $connect->prepare('SELECT a.id, a.account_no, a.status, b.bank_name, b.bank_no, c.branch_name, c.branch_no FROM bank_details a INNER JOIN bank_name b ON a.bank_name=b.id INNER JOIN bank_branch c ON a.branch_name=c.id WHERE a.employee_id="'.$row['employee_id'].'"');
                          $statement->execute();
                          $total_bank = $statement->rowCount();
                          $result = $statement->fetchAll();
                          if ($total_bank > 0) :
                            foreach($result as $row_b):
                  
                              $bank_name1 = $row_b['bank_name'].' ('.$row_b['bank_no'].')';
                              $branch_name1 = $row_b['branch_name'].' ('.str_pad($row_b['branch_no'], 3, "0", STR_PAD_LEFT).')';
                              $account_no1 =str_pad($row_b['account_no'], 12, "0", STR_PAD_LEFT);
                            endforeach;
                          else:
                            $bank_name1 ='';
                            $branch_name1 ='';
                            $account_no1 ='';
                          endif;

                        ?>
                        <tr>
                            <td><?php echo $sno; ?></td>
                            <td style="text-align: left;"><?php echo $employee_epf;?></td>
                            <td style="text-align: left;"><?php echo $position_id;?></td>
                            <td style="text-align: left;"><?php echo $row['initial'];?></td>
                            <td style="text-align: left;"><?php echo $row['surname'];?></td>
                            <td><?php echo $row['nic_no'];?></td>
                            <td style="text-align: right;"><?php echo number_format($row_inc['basic_salary']);?></td>
                            <td><center>
                        <?php if($row['join_date']!='0000-00-00'): echo $row['join_date']; endif;

                          ?>
                          </center></td>
                            <td><?php echo $location;?></td>
                            <td><?php echo $account_no1;?></td>
                           
                            <td>
                              <center>
                                <?php if($row['employee_status'] == 0): ?>
                                  <span class="badge badge-success">Present</span>
                                <?php elseif($row['employee_status'] == 1): ?>
                                  <span class="badge badge-danger">Absent</span>
                                <?php elseif($row['employee_status'] == 2): ?>
                                  <span class="badge badge-warning">Re-Enlisted</span>
                                <?php elseif($row['employee_status'] == 3): ?>
                                  <span class="badge badge-warning">Resignation</span>
                                <?php elseif($row['employee_status'] == 4): ?>
                                  <span class="badge badge-secondary">Disable</span>
                                <?php endif ?>
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