<?php
session_start(); 
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();
require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 92) == "false") {

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
            <h1 class="m-0 text-dark">Inventory</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Inventory</li>
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
                  <h3 class="card-title">Invoice Re Print</h3>                
                </div>
                  <!-- /.card-header -->
                <div class="card-body">

                  
                  <form method="POST" target="_blank" id="add_deduction_form" action="/inventory/re_print/invoice_print">
                    <div class="col-md-12">
                    <div class="form-group">
                        <label for="invoice_no">Invoice No</label>
                        <input type="text" class="form-control" id="invoice_no" name="invoice_no">
                      </div>
                    
                    </div>
                    <div class="col-md-12">
                      <button class="btn btn-sm btn-primary"><i class="fas fa-print"> Print</i></button>
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