
<?php
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();

require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 95) == "false") {

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
            <h1 class="m-0 text-dark">Report</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Report</li>
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
          <div class="container box">
   <h3 align="center">Invoice</h3>
   <br />
   <div class="row">
    <div class="col-md-4"></div>
    <div class="col-md-4">
      <form method="POST" target="_blank" id="add_deduction_form" action="/reports/payroll_print_depatment">
     <div class="form-group">
      <div class="input-group date" id="reservationmonth" data-target-input="nearest">
        <input type="text" name="effective_date" id="effective_date" class="form-control datetimepicker-input" data-target="#reservationmonth" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm" data-mask value="<?php echo date("Y-m"); ?>" required/>
        <div class="input-group-append" data-target="#reservationmonth" data-toggle="datetimepicker">
            <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
        </div>
      </div>      
     </div>
     

     <div class="form-group" align="center">
      <button type="button" name="filter" id="filter" class="btn btn-info"> <i class="fas fa-filter"> Filter</i></button>
      <button class="btn btn-primary"><i class="fas fa-print"> Print</i></button>
     </div>
     </form>
   
    </div>
    <div class="col-md-4"></div>
   </div>
   <div class="table-responsive">
    <table id="customer_data" class="table table-bordered table-striped">
     <thead>
      <tr>
        <th>#</th>                        
        <th>Sector</th>
        <th>Institute</th>
        <th>Invoice Amount</th>
        <th>Gross Payment</th>
        <th>EPF 12%</th>
        <th>ETF 3%</th>
        <th>EPF 8%</th>
      </tr>
     </thead>
    </table>
    <br />
    <br />
    <br />
   </div>
  </div>         
        </div>
        <!-- /.row -->
       
      
      </div><!-- /.container-fluid -->
    </section>   

 <?php
include '../inc/footer.php';
?>
<script type="text/javascript" language="javascript" >
 $(document).ready(function(){
  
  fill_datatable();
  
  function fill_datatable(effective_date = '')
  {
   var dataTable = $('#customer_data').DataTable({
    "processing" : true,
    "serverSide" : true,
    "lengthChange": false,
    "searching" : false,
    "paging": false,
    "info": false,
    "autoWidth": false,
    "responsive": true,
    "scrollX": true,
    "ajax" : {
     url:"/reports_invoice_fetch",
     type:"POST",
     data:{
      effective_date:effective_date
     }
    },
    dom: 'Bfrtip',
    buttons: [
    {
        extend:'excelHtml5',
        title:'Invoice_'+$('#effective_date').val(),
        footer:true
      }
   ],   
   });
  }
  
  $('#filter').click(function(){
   var effective_date = $('#effective_date').val();
   
   if(effective_date != '')
   {
    $('#customer_data').DataTable().destroy();
    fill_datatable(effective_date);
   }
   else
   {
    alert('Select Both filter option');
    $('#customer_data').DataTable().destroy();
    fill_datatable();
   }
  });  
  
 });
 
</script>

