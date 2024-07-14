<?php
session_start(); 
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();
require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 79) == "false") {

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
            <h1 class="m-0 text-dark">Payment</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Payment</li>
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
                  <h3 class="card-title">EPF</h3>                
                </div>
                  <!-- /.card-header -->
                <div class="card-body">

                  <div class="row">
                  <form method="GET">
                    <div class="col-md-12">
                    <div class="form-group">
                      <label for="effective_date" class="control-label">Month</label>
                      <div class="input-group date" id="reservationmonth" data-target-input="nearest">
                          <input type="text" name="effective_date" id="effective_date" class="form-control datetimepicker-input" data-target="#reservationmonth" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm" data-mask/>
                          <div class="input-group-append" data-target="#reservationmonth" data-toggle="datetimepicker">
                              <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                          </div>
                        </div>
                    </div>
                    </div>
                    <div class="col-md-12">
                      <button class="btn btn-sm btn-primary salary_excel">Submit</button>
                    </div>
                  </form>

                  </div>
                  <br>
                  <div class="row">
                    <div class="col-md-12">                  

                  <table id="example2" class="table table-bordered table-striped">
                    <thead style="text-align: center;">
                      <tr>
                        <th>NIC Number</th>
                        <th>Surname</th>
                        <th>initials</th>
                        <th>Member Number</th>
                        <th>Total Contribution</th>
                        <th>Employer’s Contribution</th>
                        <th>Member’s Contribution</th>
                        <th>Total Earnings</th>
                        <th>Member Status E=Extg. N=New V=Vacated</th>
                        <th>Zone</th>
                        <th>Employer Number</th>
                        <th>Contribution Period (YYYYMM)</th>
                        <th>Data Submission Number</th>
                        <th>No.of days worked</th>
                        <th>Occupation Classification Grade</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if(isset($_GET['effective_date']))
                      {
                        $effective_date=date('Y-m-d', strtotime($_GET['effective_date']));

                      $statement = $connect->prepare("SELECT a.employee_id, a.employee_epf, a.employer_epf, a.basic_epf, a.employee_no, b.date_from, b.date_to, a.no_of_shift, a.department_id FROM payroll_items a INNER JOIN payroll b ON a.payroll_id=b.id WHERE b.date_from='".$effective_date."' AND a.employee_epf > 0 ORDER BY a.id DESC");
                      $statement->execute();
                      $total_data = $statement->rowCount();

                      $result = $statement->fetchAll();

                      $startpoint =0;
                      $sno = $startpoint + 1;
                      foreach($result as $row)
                      {
                        $query_emp = 'SELECT e.nic_no, e.surname, e.initial, j.employee_no, j.join_date FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id WHERE j.join_id="'.$row['employee_id'].'" ORDER BY e.employee_id DESC';

                        $statement = $connect->prepare($query_emp);
                        $statement->execute();
                        $result = $statement->fetchAll();
                        foreach($result as $row_employee):
                          endforeach;

                          $statement = $connect->prepare("SELECT shifts FROM shifts_rate WHERE department_id = '".$row['department_id']."' AND status=0 ORDER BY id DESC LIMIT 1");
                          $statement->execute();
                          $result = $statement->fetchAll();
                          
                          if ($statement->rowCount()>0):
                            foreach($result as $row_shifts):

                              $dm_new=$row_shifts['shifts'];
                            endforeach;
                          
                          else:

                            $month= date('F', strtotime($_GET['effective_date']));                          
                            $statement = $connect->prepare("SELECT shifts FROM shifts WHERE months = '".$month."'");
                            $statement->execute();
                            $result = $statement->fetchAll();
                            foreach($result as $shifts):
                              $dm_new = $shifts['shifts'];
                            endforeach;

                          endif;
                          if ($row['no_of_shift'] >= $dm_new) {
                            $no_of_working=$dm_new;
                          }else{
                            $no_of_working=$row['no_of_shift'];
                          }


                          $total_epf=$row['employer_epf']+$row['employee_epf'];

                          if (($total_epf != '') AND ($row['employer_epf']) != '') {
                          
                        ?>
                        <tr>
                            <td style="text-align: left;"><?php echo $row_employee['nic_no']; ?></td>
                            <td style="text-align: left;"><?php echo $row_employee['surname']; ?></td>
                            <td style="text-align: left;"><?php echo $row_employee['initial']; ?></td>
                            <td style="text-align: right;"><?php echo $row['employee_no'];?></td>
                            <td style="text-align: right;"><?php echo $total_epf;?></td>
                            <td style="text-align: right;"><?php echo $row['employer_epf'];?></td>
                            
                            <td style="text-align: right;"><?php echo $row['employee_epf']; ?></td>
                            <td style="text-align: right;"><?php echo $row['basic_epf']; ?></td>
                            <td><center>
                              <?php if (($row_employee['join_date'] >= $row['date_from']) && ($row_employee['join_date'] <=$row['date_to'])) {
                            echo 'N';
                          }else{
                            echo 'E';
                          }
                          ?>
                        </center></td>
                            <td><center>B</center></td>
                            <td><center>45616</center></td>
                            <td><center><?php echo date('Ym', strtotime($row['date_from']));; ?></center></td>
                            <td><center>1</center></td>
                            <td><center><?php echo $no_of_working; ?></center></td>
                            <td><center></center></td>
                                                        
                        </tr>
                        <?php
                        $sno ++;
                      }
                      }
                    }
                      ?>
                    </tbody>
                  </table>
                  </div>
                </div>
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
      "searching": false,
      "ordering": false,
      "info": false,
      "autoWidth": false,
      "responsive": false,
      "scrollY": true,
      "scrollX": true,
      "buttons": ["excel"]
    }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');

    $('.salary_excel').click(function(){
      var $id=$(this).attr('data-id');
      location.href = "/payment/salary/"+$id;
    });

    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    });

  });
</script>