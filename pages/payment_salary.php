<?php
session_start(); 
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();
require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 73) == "false") {

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
                  <h3 class="card-title">Salary</h3>                
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

                  <table id="example2" class="table table-bordered table-striped table-sm">
                    <thead style="text-align: center;">
                      <tr>
                        <th>#</th>                        
                        <th>Employee No</th>
                        <th></th>
                        <th>Bank No</th>
                        <th>Branch No</th>
                        <th>Bank Account</th>
                        <th>Name</th>
                        <th></th>
                        <th></th>
                        <th></th>                        
                        <th></th>
                        <th>Salary</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php

                      if(isset($_GET['effective_date']))
                      {
                        $effective_date=date('Y-m-d', strtotime($_GET['effective_date']));                          
                        $statement = $connect->prepare("SELECT a.employee_no, a.bank_id, a.employee_id, a.net FROM payroll_items a INNER JOIN payroll b ON a.payroll_id=b.id WHERE a.status=1 AND b.date_from='".$effective_date."' ORDER BY a.bank_id ASC, cast(a.employee_no as int) ASC");
                        $statement->execute();
                        $total_data = $statement->rowCount();

                        $result = $statement->fetchAll();

                        $startpoint =0;
                        $sno = $startpoint + 1;
                        foreach($result as $row)
                        {
                          $query_emp = 'SELECT e.employee_id, e.surname, e.initial, j.employee_no FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid WHERE j.join_id="'.$row['employee_id'].'" ORDER BY e.employee_id DESC';

                          $statement = $connect->prepare($query_emp);
                          $statement->execute();
                          $result = $statement->fetchAll();
                          foreach($result as $row_employee):
                          endforeach;                         

                          $statement = $connect->prepare("SELECT a.account_no, b.bank_name, b.bank_no, c.branch_name, c.branch_no, a.holder_name FROM bank_details a INNER JOIN bank_name b ON a.bank_name=b.id INNER JOIN bank_branch c ON a.branch_name=c.id WHERE a.id='".$row['bank_id']."'");
                          $statement->execute();
                          $total_bank = $statement->rowCount();
                          $result = $statement->fetchAll();
                          if ($total_bank > 0) :
                          foreach($result as $row_b):
                            $bank_no = $row_b['bank_no'];
                              $branch_no = str_pad($row_b['branch_no'], 3, "0", STR_PAD_LEFT);
                              $account_no1 =str_pad($row_b['account_no'], 12, "0", STR_PAD_LEFT);
                              $holder_name = str_replace(' ', '', $row_employee['initial']).' '.$row_employee['surname'];
                          endforeach;
                          else:

                            $bank_no ='';
                            $branch_no ='';
                            $account_no1 =''; 
                            $holder_name =str_replace(' ', '', $row_employee['initial']).' '.$row_employee['surname'];
                          endif;


                          $with_decimal=round($row['net'],2);
                          $remove_decimal=$with_decimal*100;
                          if ($row_b['bank_no']==7010) {
                            $no_code=52;
                          }else{
                            $no_code=23;
                          }
                          if ($row['net']>0) {
                            
                          

                        ?>
                        <tr>
                            <td><center><?php echo $sno; ?></center></td>
                            <td><center><?php echo $row['employee_no']; ?></center></td>
                            <td><center>0000</center></td>
                            <td><center><?php echo $bank_no;?></center></td>
                            <td><center><?php echo $branch_no;?></center></td>
                            <td><center><?php echo $account_no1;?></center></td>
                            <td style="text-align: left;"><?php echo $holder_name; ?></td>
                            <td><?php echo $no_code; ?></td>
                            <td>00</td>
                            <td>0</td>
                            <td>000000</td>
                            <td style="text-align: right;"><?php echo $remove_decimal;?></td>
                              <td>SLR</td>
                              <td>7010</td>
                              <td>612</td>
                              <td>000079289055</td>
                              <td>ACE FRONT LINE</td>
                              <td>SALARY PAYMENT</td>
                              <td><?php echo strtoupper(date('Y F', strtotime($_GET['effective_date']))); ?></td>
                              <td></td>                                                        
                              <td>000000</td>
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
      "info": true,
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