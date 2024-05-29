<?php 
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();
require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 54) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}
if (isset($_POST['add_save'])){ 

  if (checkPermissions($_SESSION["user_id"], 54) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';  
    header('location:/loan/new_loan_req');  
    exit();
  }

  $ref_no=time();
  $start_date=date('Y-m-d', strtotime($_POST['date_effective']));

  $data = array(
    ':ref_no'           =>  $ref_no,  
    ':employee_id'      =>  $_POST['employee_id'],
    ':loan_plan'        =>  $_POST['loan_plan'],
    ':loan_amount'      =>  $_POST['loan_amount'], 
    ':monthly_ins'      =>  $_POST['monthly_ins'], 
    ':status'           =>  0,
    ':date_effective'   =>  $start_date,
    ':request_date'   =>  $_POST['date_issue'],
  );
 
  $query = "
  INSERT INTO `loan_list`(`ref_no`, `employee_id`, `loan_plan`, `loan_amount`, `monthly`, `status`, `request_date`, `date_effective`) 
  VALUES (:ref_no, :employee_id, :loan_plan, :loan_amount, :monthly_ins, :status, :request_date, :date_effective)  
  ";
          
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    $errMSG = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';            
  }else{
      $errMSG = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
  }
} 

if (isset($_POST['update_save'])){

  $data = array(
    ':employee_id'      =>  $_POST['employee_id'],
    ':loan_plan'        =>  $_POST['loan_plan'],
    ':loan_amount'      =>  $_POST['loan_amount'], 
    ':monthly_ins'      =>  $_POST['monthly_ins'], 
    ':date_effective'   =>  $_POST['date_effective'],      
  );

  $query = "UPDATE `loan_list` SET `employee_id`=:employee_id, `loan_plan`=:loan_plan, `loan_amount`=:loan_amount, `monthly`=:monthly_ins, `date_effective`=:date_effective WHERE `id`=".$_GET['edit']."";
    
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    header('location:/loan/loan_list');            
  }else{
      $errMSG = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
  }
    
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
            <?php 
            
            if(isset($_GET['edit']))
            {
              $query = 'SELECT * FROM loan_list WHERE status = 0 AND id="'.$_GET['edit'].'"';
              $statement = $connect->prepare($query);
              $statement->execute();
              $total_data = $statement->rowCount();              
              $result = $statement->fetchAll();
              if ($total_data > 0){                
                foreach($result as $row)
                {
                  ?>
                  <form action="" id="" method="post">
                <div class="card card-danger">
                  <div class="card-header">
                    <h3 class="card-title">Edit Employee Allowance</h3>                
                  </div>
                    <!-- /.card-header -->
                  <div class="card-body">
                    <div class="form-group">
                    <label for="">Employee</label>
                    <select class="form-control select2" style="width: 100%;" name="employee_id" id="employee_id">
                    <?php
                    $query="SELECT j.join_id, e.surname, e.initial, j.employee_no, p.position_abbreviation FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid INNER JOIN position p ON e.position_id=p.position_id ORDER BY e.employee_id DESC";
                    $statement = $connect->prepare($query);
                    $statement->execute();
                    $result = $statement->fetchAll();
                    foreach($result as $row_emp)
                    {
                      ?>
                      <option value="<?php echo $row_emp['join_id'];?>"<?php if ($row_emp['employee_id']==$row['employee_id']){ echo "selected";}?>><?php echo $row_emp['employee_no'].' '.$row_emp['position_abbreviation'].' '.$row_emp['surname'].' '.$row_emp['initial']; ?></option>
                      <?php
                    }
                    ?>
                  </select>
                  </div>

                  <div class="form-group">
                  <label for="loan_plan">Loan Plan</label>
                  <input type="text" class="form-control" id="loan_plan" name="loan_plan" onkeyup="getAmount(this.value)" value="<?php echo $row['loan_plan'] ; ?>">
                </div>

                  <div class="form-group">
                  <label for="date_effective" class="control-label">Effective Date</label>
                  <div class="input-group date" id="reservationdate" data-target-input="nearest">
                      <input type="text" name="date_effective" id="date_effective" class="form-control datetimepicker-input" data-target="#reservationdate" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm-dd" data-mask value="<?php echo $row['date_effective'] ; ?>" />
                      <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                          <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                      </div>
                    </div>
                </div>

                  <div class="form-group">
                  <label for="loan_amount">Loan Amount</label>
                  <input type="text" class="form-control" id="loan_amount" name="loan_amount" onkeyup="getAmount(this.value)" value="<?php echo $row['loan_amount'] ; ?>">
                </div>

                  <div class="form-group">
                  <label for="monthly_ins">Monthly Installment</label>
                  <input type="text" class="form-control" id="monthly_ins" name="monthly_ins" readonly value="<?php echo $row['monthly'] ; ?>">
                </div>


                  </div>


                  <!-- /.card-body -->

                  <div class="card-footer">
                    <button class="btn btn-sm btn-primary col-sm-3 offset-md-3" name="update_save"> Save</button>
                    <button class="btn btn-sm btn-default col-sm-3" type="reset"> Cancel</button>
                  </div>

                </div>
                <!-- /.card -->
              </form>
                  <?php
                }
              }else{
                ?>
                <div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>This Cannot be found.</div>
                <?php
              }
              
            }else{
              ?>
              <form action="" id="loan_req" method="post">
              <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">New Loan Request</h3>                
                </div>
                  <!-- /.card-header -->
                <div class="card-body">                  
                  <div class="form-group">
                  <label for="employee_id">Employee</label>
                  <select class="form-control select2" style="width: 100%;" name="employee_id" id="employee_id">
                    <option value="">Select Employee</option>
                    <?php
                    $query="SELECT j.join_id, e.surname, e.initial, j.employee_no, p.position_abbreviation FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid INNER JOIN position p ON e.position_id=p.position_id ORDER BY e.employee_id DESC";
                    $statement = $connect->prepare($query);
                    $statement->execute();
                    $result = $statement->fetchAll();
                    foreach($result as $row)
                    {
                      ?>
                      <option value="<?php echo $row['join_id']; ?>"><?php echo $row['employee_no'].' '.$row['position_abbreviation'].' '.$row['surname'].' '.$row['initial']; ?></option>
                      <?php
                    }
                    ?>
                  </select>
                </div>

                <div class="form-group">
                  <label for="loan_plan">Loan Plan</label>
                  <input type="text" class="form-control" id="loan_plan" name="loan_plan" onkeyup="getAmount(this.value)">
                </div>

                <div class="form-group">
                  <label for="date_issue" class="control-label">Issue Date</label>
                  <div class="input-group date" id="reservationstartdate" data-target-input="nearest">
                      <input type="text" name="date_issue" id="date_issue" class="form-control datetimepicker-input" data-target="#reservationstartdate" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm-dd" data-mask value="<?php echo date("Y-m-d"); ?>" />
                      <div class="input-group-append" data-target="#reservationstartdate" data-toggle="datetimepicker">
                          <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                      </div>
                    </div>
                </div>

                <div class="form-group">
                  <label for="date_effective" class="control-label">Start Deduct Date</label>
                  <div class="input-group date" id="reservationmonth" data-target-input="nearest">
                      <input type="text" name="date_effective" id="date_effective" class="form-control datetimepicker-input" data-target="#reservationmonth" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm" data-mask value="<?php echo date('Y-m', strtotime("-1 month")); ?>"/>
                      <div class="input-group-append" data-target="#reservationmonth" data-toggle="datetimepicker">
                          <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                      </div>
                    </div>
                </div>

                <div class="form-group">
                  <label for="loan_amount">Loan Amount</label>
                  <input type="text" class="form-control" id="loan_amount" name="loan_amount" onkeyup="getAmount(this.value)">
                </div>

                <div class="form-group">
                  <label for="monthly_ins">Monthly Installment</label>
                  <input type="text" class="form-control" id="monthly_ins" name="monthly_ins" readonly>
                </div>

                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button class="btn btn-sm btn-primary col-sm-3 offset-md-3" name="add_save"> Save</button>
                  <button class="btn btn-sm btn-default col-sm-3" type="reset"> Cancel</button>
                </div>

              </div>
              <!-- /.card -->
            </form>
              <?php
            }
            
            ?>

            
          </div>

          
        </div>
        <!-- /.row -->
       
      
      </div><!-- /.container-fluid -->
    </section>    

<?php
include '../inc/footer.php';
?>
<script>
function getAmount(value){
    var loan_plan = (Math.round($('#loan_plan').val()* 100) / 100).toFixed(2);
    var loan_amount = (Math.round($('#loan_amount').val()* 100) / 100).toFixed(2);
    var monthly_ins = parseFloat(loan_amount)/parseFloat(loan_plan);
    document.getElementById('monthly_ins').value = (Math.round(monthly_ins * 100) / 100).toFixed(2);
  }
</script>
<script type="text/javascript">
  $(function () {
  
  $('#loan_req').validate({
    rules: {
      employee_id: { required: true},
      loan_plan: {required: true, number:true},
      date_effective: {required: true, date:true},
      loan_amount: {required: true, number:true},        
      
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