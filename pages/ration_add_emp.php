<?php
session_start(); 
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();
require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 50) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}
if (isset($_POST['add_save'])){
  if (checkPermissions($_SESSION["user_id"], 50) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/ration/add_emp_ration');
    exit();
  }


  $ration_date=date('Y-m-d', strtotime($_POST['ration_date']));

  $statement = $connect->prepare("SELECT join_id FROM join_status WHERE employee_no = '".$_POST['employee_id']."'
      ");
  $statement->execute();
  $total_data = $statement->rowCount();
  $result = $statement->fetchAll();
  foreach($result as $row)
  {
    $employee_id=$row['join_id'];
  }

  $data = array(
      ':employee_id'   =>  $employee_id,
      ':ration_date'  =>  $ration_date,
      ':amount'          =>  $_POST['amount'],          
  );
 
  $query = "
  INSERT INTO `ration_deduction`(`employee_id`, `amount`, `date_effective`)
  VALUES (:employee_id, :amount, :ration_date)
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

include '../inc/header.php';
?>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Ration</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Ration</li>
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
            
              <form action="" id="loan_req" method="post">
              <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">Add Ration to Employee</h3>                
                </div>
                  <!-- /.card-header -->
                <div class="card-body">

                  <div class="form-group">
                  <label for="employee_id">Service No </label>
                  <input type="text" class="form-control" id="employee_id" name="employee_id" autofocus autocomplete="off"> 
                  <span id="employee_name" class="text-success"></span>
                </div>

                <!-- 
                  <div class="form-group">
                  <label for="employee_id">Employee</label>
                  <select class="form-control select2" style="width: 100%;" name="employee_id" id="employee_id">
                    <option value="">Select Employee</option>
                    <?php
                    $query="SELECT j.join_id, e.surname, e.initial, j.employee_no FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid WHERE j.employee_status=0 OR j.employee_status=2 ORDER BY e.employee_id DESC";
                    $statement = $connect->prepare($query);
                    $statement->execute();
                    $result = $statement->fetchAll();
                    foreach($result as $row)
                    {
                      ?>
                      <option value="<?php echo $row['join_id']; ?>"><?php echo $row['employee_no'].' '.$row['surname'].' '.$row['initial']; ?></option>
                      <?php
                    }
                    ?>
                  </select>
                </div> -->

                <!-- <div class="form-group">
                  <label for="supplier_name">Supplier Name</label>
                  <select class="form-control select2" style="width: 100%;" name="supplier_name" id="supplier_name">
                    <option value="">Select Supplier</option>
                    <?php
                    $query="SELECT * FROM ration_supplier_list WHERE status=0 ORDER BY id DESC";
                    $statement = $connect->prepare($query);
                    $statement->execute();
                    $result = $statement->fetchAll();
                    foreach($result as $row)
                    {
                      ?>
                      <option value="<?php echo $row['id']; ?>"><?php echo $row['supplier_name']; ?></option>
                      <?php
                    }
                    ?>
                  </select>
                </div>  -->  
                <?php 
                $datestring=date('Y-m-d', strtotime("first day of last month"));
                $dt=date_create($datestring);
                ?>             
               
                <div class="form-group" id="dfield">
                  <label for="ration_date" class="control-label">Ration Date</label>
                  <div class="input-group date" id="reservationmonth" data-target-input="nearest">
                      <input type="text" name="ration_date" id="ration_date" class="form-control datetimepicker-input" data-target="#reservationmonth" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm" data-mask value="<?php echo $dt->format('Y-m'); ?>"/>
                      <div class="input-group-append" data-target="#reservationmonth" data-toggle="datetimepicker" >
                          <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                      </div>
                    </div>
                </div>

                <div class="form-group">
                  <label for="amount">Amount</label>
                  <input type="text" class="form-control" id="amount" name="amount">
                </div>

                </div>
                <!-- /.card-body -->

                <dir class="card-footer">
                  <button class="btn btn-sm btn-primary col-sm-3 offset-md-3" name="add_save"> Save</button>
                  <button class="btn btn-sm btn-default col-sm-3" type="reset"> Cancel</button>
                </dir>

              </div>
              <!-- /.card -->
            </form>
          </div>

        </div>
        <!-- /.row -->
       
      
      </div><!-- /.container-fluid -->
    </section>    

<?php
include '../inc/footer.php';
?>
<script type="text/javascript">
  $(function () {
  
  $('#loan_req').validate({
    rules: {
      employee_id: { required: true,
      remote: {
          url: "/check_employee_id",
          type: "post"
          }
        
      },
      date_effective: {required: true, date:true},
      amount: {required: true, number:true},        
      
    },
    
    messages: {  
    
      employee_id: {
        remote: 'Wrong Employee No!'
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

load_data();

function load_data(query = '')
{
  var query = $('#employee_id').val();
      $.ajax({
        url:"/employee_no2",
        method:"POST",
        data:{query:query,request:1},
        dataType: 'json',

        success:function(response)
        {
          var len = response.length;
          
          var name_with_initial='';
          
          if(len > 0){
              var name_with_initial = response[0]['name_with_initial'];
          }
          document.getElementById('employee_name').innerHTML = name_with_initial;
        }
      });

      
}
$('#employee_id').keyup(function(){
     var query = $('#employee_id').val();
       load_data(1, query);
     });


</script>