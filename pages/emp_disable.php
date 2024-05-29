<?php
session_start(); 
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();
require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 1) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}

$error = false;
if (isset($_POST['save'])){

  if (checkPermissions($_SESSION["user_id"], 1) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions to Add Employee.</div>'; 
    header('location:/employee_list/employee');  
    exit();

}


$effective_date = date("Y-m", strtotime($_POST['effective_date']));
$next_month = date("Y-m", strtotime('+1 month', strtotime($_POST['effective_date'])));

if (!$error) {
    $data = array(
        ':effective_date' => $effective_date,
        ':next_month'     => $next_month,
        ':employee_status'=> 4,      
    );

    $query = '
    UPDATE join_status
    SET employee_status = :employee_status
    WHERE join_id NOT IN (
        SELECT employee_id
        FROM attendance
        WHERE DATE_FORMAT(start_date, "%Y-%m") = :effective_date
    )
    AND employee_status = 0
    AND DATE_FORMAT(join_date, "%Y-%m") < :next_month;
    ';

    $statement = $connect->prepare($query);

    if ($statement->execute($data)) {
        $rowsUpdated = $statement->rowCount();
        $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <span class="glyphicon glyphicon-info-sign"></span> Success. ' . $rowsUpdated . ' rows updated.</div>';
    } else {
        $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <i class="fas fa-fw fa-times"></i> Cannot Save.</div>';
    }
}


}


include '../inc/header.php';

?>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Employee</h1>
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
          <div class="col-md-6">
            
              <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">Disable</h3>                
                </div>
                  <!-- /.card-header -->
                <div class="card-body">

                  
                  <form method="POST" action="" enctype="multipart/form-data">
                    <div class="col-md-12">
                    <div class="form-group">
                      <label for="effective_date" class="control-label">Month</label>
                      <div class="input-group date" id="reservationmonth" data-target-input="nearest">
                          <input type="text" name="effective_date" id="effective_date" class="form-control datetimepicker-input" data-target="#reservationmonth" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm" data-mask value="<?php echo date("Y-m"); ?>"/>
                          <div class="input-group-append" data-target="#reservationmonth" data-toggle="datetimepicker">
                              <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                          </div>
                        </div>
                    </div>
                    
                    </div>
                    <div class="col-md-12">
                      <button class="btn btn-sm btn-primary" name="save"><i class="fas fa-save"></i> Save</button>
                    </div>
                  </form>

              
                  
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

    $('#add_deduction_form').validate({
    rules: {
      effective_date: { required: true},
      ins_id: {required: true}
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

    $('#example2').DataTable({
      "paging": false,
      "lengthChange": false,
      "searching": false,
      "ordering": false,
      "info": false,
      "autoWidth": false,
      "responsive": true,
      "scrollY": true,
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