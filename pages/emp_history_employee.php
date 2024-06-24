<?php 
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
require_once 'system_permissions.php';
$connect = pdoConnection();

if (checkPermissions($_SESSION["user_id"], 3) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}

$query = 'SELECT * FROM employee WHERE employee_id="'.$_GET['employee_id'].'"';

$statement = $connect->prepare($query);
$statement->execute();
$total_data = $statement->rowCount();
$result = $statement->fetchAll();
if ($total_data > 0){   
  foreach($result as $row):                   

  endforeach; 
}

include '../inc/header.php';

?>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark"><?php echo $row['surname'].' '.$row['initial']; ?></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Employee</li>
              <li class="breadcrumb-item active">History</li>
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
            <div class="col-md-6">
          <div class="card card-success">
              <div class="card-header">
                <h3 class="card-title">Increment</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                  </button>
                </div>
                <!-- /.card-tools -->
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <?php 
                $query = 'SELECT * FROM salary WHERE employee_id="'.$_GET['join_id'].'" ORDER BY id DESC';

                $statement = $connect->prepare($query);
                $statement->execute();
                $total_data = $statement->rowCount();

                $result = $statement->fetchAll();
                ?>

                <table id="example1" class="table table-bordered table-sm table-striped" >
                  <thead>
                    <tr style="text-align: center;">
                      <th>#</th>
                      <th>Salary</th>
                      <th>Increment Date</th>                      
                    </tr>
                  </thead>
                  <tbody>
                    <?php                     
                      $startpoint =0;
                      $sno = $startpoint + 1;
                      foreach($result as $row_inc)
                      {                        
                    ?>

                    <tr>
                      <td><center><?php echo $sno; ?></center></td>
                      <td style="text-align: right;"><?php echo number_format($row_inc['basic_salary']);?></td>
                      <td><center><?php echo date('Y-m', strtotime($row_inc['increment_date']));?></center></td>
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
          <div class="col-md-6">
            <div class="card card-secondary">
              <div class="card-header">
                <h3 class="card-title">ETF / EPF</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                  </button>
                </div>
                <!-- /.card-tools -->
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <?php 
                $query = 'SELECT * FROM payroll_items WHERE employee_id="'.$_GET['join_id'].'" ORDER BY id ASC';

                $statement = $connect->prepare($query);
                $statement->execute();
                $total_data = $statement->rowCount();

                $result = $statement->fetchAll();
                ?>

                <table id="example2" class="table table-bordered table-sm table-striped" >
                  <thead>
                    <tr style="text-align: center;">
                      <th>#</th>
                      <th>Month</th>
                      <th>EPF (8%)</th>
                      <th>EPF (12%)</th>
                      <th>ETF (3%)</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php                     
                      $startpoint =0;
                      $sno = $startpoint + 1;
                      foreach($result as $row_epf)
                      {
                        $payroll_id=$row_epf['payroll_id'];

                        $query = 'SELECT * FROM payroll WHERE id="'.$payroll_id.'" ORDER BY id ASC';

                        $statement = $connect->prepare($query);
                        $statement->execute();
                        $result = $statement->fetchAll();
                        foreach($result as $row_month)
                        {
                          $month=date('Y F', strtotime($row_month['date_from']));
                        }
                    ?>

                    <tr>
                      <td><center><?php echo $sno; ?></center></td>
                      <td><?php echo $month;?></td>
                      <td style="text-align: right;"><?php if ($row_epf['employee_epf'] >0){ echo number_format($row_epf['employee_epf']);}?></td>
                      <td style="text-align: right;"><?php if ($row_epf['employer_epf'] >0){ echo number_format($row_epf['employer_epf']);}?></td>
                      <td style="text-align: right;"><?php if ($row_epf['employer_etf'] >0) {echo number_format($row_epf['employer_etf']);}?></td>
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
          <div class="col-md-6">
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Death Donation</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                  </button>
                </div>
                <!-- /.card-tools -->
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <?php 
                $query = 'SELECT * FROM death_donation WHERE employee_id="'.$_GET['join_id'].'" ORDER BY id DESC';

                $statement = $connect->prepare($query);
                $statement->execute();
                $total_data = $statement->rowCount();

                $result = $statement->fetchAll();
                ?>

                <table id="example_death" class="table table-bordered table-sm table-striped" >
                  <thead>
                    <tr style="text-align: center;">
                      <th>#</th>
                      <th>Relation</th>
                      <th>Amount</th>
                      <th>Death Date</th>                      
                    </tr>
                  </thead>
                  <tbody>
                    <?php                     
                      $startpoint =0;
                      $sno = $startpoint + 1;
                      foreach($result as $row_death)
                      {
                        
                    ?>

                    <tr>
                      <td><center><?php echo $sno; ?></center></td>
                      <td><?php echo $row_death['relation'];?></td>
                      <td style="text-align: right;"><?php echo number_format($row_death['amount']);?></td>
                      <td><center><?php echo $row_death['due_date'];?></center></td>
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
          <!-- /.col -->

          <div class="col-md-6">
            <div class="card card-danger">
              <div class="card-header">
                <h3 class="card-title">Advance</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                  </button>
                </div>
                <!-- /.card-tools -->
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <?php 
                $query = 'SELECT * FROM salary_advance WHERE employee_id="'.$_GET['join_id'].'" ORDER BY id DESC';

                $statement = $connect->prepare($query);
                $statement->execute();
                $total_data = $statement->rowCount();

                $result = $statement->fetchAll();
                ?>

                <table id="example3" class="table table-bordered table-sm table-striped" >
                  <thead>
                    <tr style="text-align: center;">
                      <th>#</th>
                      <th>Amount</th>
                      <th>Date Effective</th>
                      <th>Status</th>                      
                    </tr>
                  </thead>
                  <tbody>
                    <?php                     
                      $startpoint =0;
                      $sno = $startpoint + 1;
                      foreach($result as $row_advance)
                      {
                        
                    ?>

                    <tr>
                      <td><center><?php echo $sno; ?></center></td>
                      <td style="text-align: right;"><?php echo number_format($row_advance['amount']);?></td>
                      <td><center><?php echo $row_advance['date_effective'];?></center></td>
                      <td><center>
                        <?php if($row_advance['status'] == 0): ?>
                          <span class="badge badge-warning">to be paid by</span>
                        <?php elseif($row_advance['status'] == 1): ?>
                          <span class="badge badge-success">Salary deduct</span>
                        <?php elseif($row_advance['status'] == 2): ?>
                          <span class="badge badge-primary">approved</span>
                        <?php elseif($row_advance['status'] == 3): ?>
                          <span class="badge badge-danger">not approved</span>
                        <?php endif ?>
                      </center></td>
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
          <div class="col-md-6">
            <div class="card card-warning">
              <div class="card-header">
                <h3 class="card-title">Loan Details</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                  </button>
                </div>
                <!-- /.card-tools -->
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <?php 
                $query = 'SELECT * FROM loan_list WHERE employee_id="'.$_GET['join_id'].'" ORDER BY id DESC';

                $statement = $connect->prepare($query);
                $statement->execute();
                $total_data = $statement->rowCount();

                $result = $statement->fetchAll();
                ?>

                <table id="example4" class="table table-bordered table-sm table-striped" >
                  <thead>
                    <tr style="text-align: center;">
                      <th>#</th>
                      <th>Loan Amount</th>
                      <th>Salary Deduct</th>
                      <th>Status</th>
                      <th>Loan Close Date</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php                     
                      $startpoint =0;
                      $sno = $startpoint + 1;
                      foreach($result as $row_loan)
                      {
                        $query = 'SELECT sum(paid_amount) AS deduct_amount FROM loan_schedules WHERE loan_id="'.$row_loan['id'].'" AND employee_id="'.$_GET['join_id'].'" AND status=1';

                        $statement = $connect->prepare($query);
                        $statement->execute();
                        $result = $statement->fetchAll();
                        foreach($result as $row_ded)
                        {
                          
                        }

                        $query = 'SELECT date_due FROM loan_schedules WHERE loan_id="'.$row_loan['id'].'" AND employee_id="'.$_GET['join_id'].'" ORDER BY id DESC LIMIT 1';

                        $statement = $connect->prepare($query);
                        $statement->execute();
                        $result = $statement->fetchAll();
                        foreach($result as $row_close)
                        {
                          
                        }
                    ?>

                    <tr>
                      <td><center><?php echo $sno; ?></center></td>
                      <td style="text-align: right;"><?php echo number_format($row_loan['loan_amount']);?></td>
                      <td style="text-align: right;"><?php echo number_format($row_ded['deduct_amount']);?></td>
                      <td><?php ?></td>
                      <td><center><?php echo $row_close['date_due']; ?></center></td>
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
          <div class="col-md-6">
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Equipment</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                  </button>
                </div>
                <!-- /.card-tools -->
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <?php 
                $query = 'SELECT * FROM inventory_issue WHERE employee_id="'.$_GET['join_id'].'" AND status = 1 ORDER BY id DESC';

                $statement = $connect->prepare($query);
                $statement->execute();
                $total_data = $statement->rowCount();

                $result = $statement->fetchAll();
                ?>

                <table id="example6" class="table table-bordered table-sm table-striped" >
                  <thead>
                    <tr style="text-align: center;">
                      <th>#</th>
                      <th>Product</th>
                      <th>Qty</th>
                      <th>Price</th>
                      <!-- <th>Loan Close Date</th> -->
                    </tr>
                  </thead>
                  <tbody>
                    <?php                     
                      $startpoint =0;
                      $sno = $startpoint + 1;
                      foreach($result as $row_eqpt)
                      {
                        $query = 'SELECT * FROM inventory_product WHERE id="'.$row_eqpt['product_id'].'" ';

                        $statement = $connect->prepare($query);
                        $statement->execute();
                        $result = $statement->fetchAll();
                        foreach($result as $row_product)
                        {
                          
                        }
                       
                    ?>

                    <tr>
                      <td><center><?php echo $sno; ?></center></td>
                      <td><?php echo $row_product['product_name'];?></td>
                      <td><center><?php echo $row_eqpt['qty'];?></center></td>
                      <td style="text-align: right;"><?php echo number_format($row_eqpt['total']);?></td>
                      
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
          <!-- /.col -->          
           
        
          <div class="col-md-6">
            <div class="card card-info">
              <div class="card-header">
                <h3 class="card-title">Pay Details</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                  </button>
                </div>
                <!-- /.card-tools -->
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <?php 
                $query = 'SELECT * FROM payroll_items WHERE employee_id="'.$_GET['join_id'].'" ORDER BY id DESC';

                $statement = $connect->prepare($query);
                $statement->execute();
                $total_data = $statement->rowCount();

                $result = $statement->fetchAll();
                ?>

                <table id="example5" class="table table-bordered table-sm table-striped" >
                  <thead>
                    <tr style="text-align: center;">
                      <th>#</th>
                      <th>Month</th>
                      <th>Net Salary</th>
                      <th>Status</th>                      
                    </tr>
                  </thead>
                  <tbody>
                    <?php                     
                      $startpoint =0;
                      $sno = $startpoint + 1;
                      foreach($result as $row_pay)
                      {
                        $payroll_id=$row_pay['payroll_id'];

                        $query = 'SELECT * FROM payroll WHERE id="'.$payroll_id.'" ORDER BY id ASC';

                        $statement = $connect->prepare($query);
                        $statement->execute();
                        $result = $statement->fetchAll();
                        foreach($result as $row_month)
                        {
                          $month=date('Y F', strtotime($row_month['date_from']));
                        }
                    ?>

                    <tr>
                      <td><center><?php echo $sno; ?></center></td>
                      <td><?php echo $month;?></td>
                      <td style="text-align: right;"><?php echo number_format($row_pay['net']);?></td>
                      <td><center>
                        <?php if($row_pay['status'] == 0): ?>
                          <span class="badge badge-warning">Calculated</span>
                        <?php elseif($row_pay['status'] == 1): ?>
                          <span class="badge badge-success">Approved</span>
                        <?php elseif($row_pay['status'] == 2): ?>
                          <span class="badge badge-danger">Halt</span>
                        <?php elseif($row_pay['status'] == 3): ?>
                          <span class="badge badge-primary">Re-approved</span>
                        <?php endif ?>
                      </center></td>                      
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
          <div class="col-md-6">

            <div class="card card-secondary">
              <div class="card-header">
                <h3 class="card-title">Bank Details</h3>                

              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <?php 
                $statement = $connect->prepare("SELECT a.id, a.account_no, a.status, b.bank_name, b.bank_no, c.branch_name, c.branch_no FROM bank_details a INNER JOIN bank_name b ON a.bank_name=b.id INNER JOIN bank_branch c ON a.branch_name=c.id WHERE a.employee_id='".$_GET['employee_id']."'");
                $statement->execute();
                $total_bank = $statement->rowCount();
                $result = $statement->fetchAll();
                ?>

                <table id="example_bank" class="table table-bordered table-sm table-striped">
                  <thead>
                    <tr style="text-align: center;">
                      <th>#</th>
                      <th>Bank Name</th>
                      <th>Account No</th>
                      <th>Status</th>                      
                    </tr>
                  </thead>
                  <tbody>
                    <?php                     
                      $startpoint =0;
                      $sno = $startpoint + 1;
                      foreach($result as $row_b)
                      {                        
                        
                    ?>

                    <tr>
                      <td><center><?php echo $sno; ?></center></td>
                      <td>
                        <dl>
                          <dt><?php echo $row_b['bank_name'].' ('.$row_b['bank_no'].')'; ?></dt>
                          <dd><?php echo $row_b['branch_name'].' ('.$row_b['branch_no'].')'; ?></dd>
                        </dl>
                      </td>
                      <td><?php echo str_pad($row_b['account_no'], 12, "0", STR_PAD_LEFT);?></td>
                      <td><center>
                        <form action="" method="POST">
                            <input type="hidden" name="bank_id" value="<?php echo $row_b['id'];?>">
                        <?php if($row_b['status'] == 0): ?>                          
                                                  
                          <span class="badge badge-success">Enabled</span>
                        <?php elseif($row_b['status'] == 1): ?>                          
                          
                          <span class="badge badge-danger">Disabled</span>
                        <?php endif ?>
                        </form>
                      </center></td>                      
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

<!-- promotion -->
<div class="modal fade" id="promoteModal" tabindex="-1" role="dialog" aria-labelledby="promoteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <form action="" method="POST" id="add_promote_form">
        <div class="modal-content">
          <div class="modal-body">
            <div class="col-md-2"></div>
            <div class="col-md-8">
              <div class="form-group">
                <label for="bank_name">Promoted Rank:</label>
                <select class="form-control select2" style="width: 100%;" name="position_id" id="position_id">
                  <option value="">Select Rank</option>
                  <?php
                  $query="SELECT * FROM position ORDER BY position_id";
                  $statement = $connect->prepare($query);
                  $statement->execute();
                  $result = $statement->fetchAll();
                  foreach($result as $row)
                  {
                    ?>
                    <option value="<?php echo $row['position_id']; ?>"><?php echo $row['position_abbreviation']; ?></option>
                    <?php
                  }
                  ?>
                </select>                      
              </div>
              <div class="form-group">
                <label for="bank_branch">Promoted Date:</label>
                <div class="input-group date" id="reservationdate" data-target-input="nearest">
                  <input type="text" name="promoted_date" id="promoted_date" class="form-control datetimepicker-input" data-target="#reservationdate" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm-dd" data-mask value="<?php echo date("Y-m-d"); ?>" />
                  <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                  </div>
                </div>                       
              </div>

              <div class="form-group">
                <label for="promotion_pay">Promotion Pay:</label>
                <input type="text" name="promotion_pay" id="promotion_pay" class="form-control">
              </div>            
              

            </div>
          </div>
          <div style="clear:both;"></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal"> Close</button>
            <button name="add_promote" class="btn btn-primary"> Save</button>
          </div>
        </div>
      </form>
    </div>
  </div>  
   

<?php
include '../inc/footer.php';
?>

<script src="/plugins/bs-stepper/main.js"></script>
<script>
 
 $(document).ready(function(){

  $('#example1').DataTable({
    "paging": true,
    "lengthChange": false,
    "searching": false,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": false,    
  });

  $('#example2').DataTable({
    "paging": true,
    "lengthChange": false,
    "searching": false,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": false,
    
  });

  $('#example3').DataTable({
    "paging": true,
    "lengthChange": false,
    "searching": false,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": false,
    
  });

  $('#example4').DataTable({
    "paging": true,
    "lengthChange": false,
    "searching": false,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": false,
    
  });

  $('#example5').DataTable({
    "paging": true,
    "lengthChange": false,
    "searching": false,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": false,
    
  });

  $('#example6').DataTable({
    "paging": true,
    "lengthChange": false,
    "searching": false,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": false,
    
  });

  $('#example_bank').DataTable({
    "paging": true,
    "lengthChange": false,
    "searching": false,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": false,
    
  });
  $('#example_death').DataTable({
    "paging": true,
    "lengthChange": false,
    "searching": false,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": false,
    
  });

  $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    });

 });
</script>

<script>
$(function () {
  
  $('#add_join_form').validate({
    rules: {
      create_date: { required: true, date:true},
      employee_no: {required: true, number:true},
      position_id: {required: true},
      basic_salary: {required: true}     
    },

    messages: {      
      
      employee_no: {
        remote: 'Employee No Already existing!'
      },

      nic_new: {
        remote: 'NIC No Already existing!'
      }, 

      nic_old: {
        remote: 'NIC No Already existing!'
      }, 
    },
    errorElement: 'span',
    errorPlacement: function (error, element) {
      error.addClass('invalid-feedback');
      element.closest('.form-group').append(error);
    },
    highlight: function (element, errorClass, validClass) {
      $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass('is-invalid');
    }
  });

});
</script>

<script type="text/javascript">
  // ajax script for getting state data
  
   $(document).on('change','#bank_name', function(){
      var bank_nameID = $(this).val();
      if(bank_nameID){
          $.ajax({
              type:'POST',
              url:'/backend-script',
              data:{'bank_name_id':bank_nameID,'request':3},
              success:function(result){
                  $('#bank_branch').html(result);
                 
              }
          });          
      }else{
          $('#bank_branch').html('<option value="">First Select Bank</option>');          
      }
  });

   $(document).on('click','.edit_data4',function(){
        $("#editData4").modal({
            backdrop: 'static',
            keyboard: false
        });
        var edit_id4=$(this).attr('data-id');
        $.ajax({
          url:"/bank_edit",
          type:"post",
          data:{edit_id4:edit_id4},
          success:function(data){
            $("#info_update4").html(data);
            $("#editData4").modal('show');
          }
        });
      });

   $(document).on('click','.edit_pro',function(){
        $("#promoteModal").modal({
            backdrop: 'static',
            keyboard: false
        });        
      });

</script>