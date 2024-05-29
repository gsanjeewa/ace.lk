
<?php
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();

require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 104) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}


include '../inc/header.php';

?>
<style type="text/css">
  th.dt-center, td.dt-center { text-align: center; }
  th.dt-left, td.dt-left { text-align: left; }
  th.dt-right, td.dt-right { text-align: right; }
</style>

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
   <h3 align="center">Institution Payment</h3>
   <br />
   <div class="row">
    <div class="col-md-4"></div>
    <div class="col-md-4">
      
     <div class="form-group">
      <div class="input-group date" id="reservationmonth" data-target-input="nearest">
        <input type="text" name="effective_date" id="effective_date" class="form-control datetimepicker-input" data-target="#reservationmonth" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm" data-mask value="<?php echo date("Y-m"); ?>" required/>
        <div class="input-group-append" data-target="#reservationmonth" data-toggle="datetimepicker">
            <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
        </div>
      </div>
      
     </div>
     
     <div class="form-group" align="center">
      <button type="button" name="filter" id="filter" class="btn btn-info">Filter</button>
     </div>
   
    </div>
    <div class="col-md-4"></div>
   </div>
   <div class="table-responsive">
    <table id="customer_data" class="table table-bordered table-striped table-sm">
     <thead>
      <tr style="text-align: center;">
        <th>#</th>                        
        <th>Institutaion</th>
        <th>Position Name</th>
        <th>Total No of Shift</th>
        <th>Payment for Invoice</th>
        <th>Total</th>
        <th>Total no of Working Shifts</th>
        <th>Payment for Employee</th>
        <th>Total</th>
        <th>Short Shift</th>
        <th>Short Amount</th>
      </tr>
     </thead>
     <tbody>
     </tbody>
     <tfoot>
      <tr>
        <th></th>
        <th>Total</th>
        <th id="total_shift"></th>
        <th></th>
        <th id="total_invoice"></th>
        <th id="total_working"></th>
        <th></th>
        <th id="total_working_amount"></th>
        <th></th>
        <th id="total_short_amount"></th>
      </tr>
     </tfoot>
     
    </table>   
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
    "searching" : false,
    "lengthChange": false,
    "paging": false,
    "info": false,
    "order" : [],
    "autoWidth": false,
    "responsive": true,
    "scrollX": true,
    "ajax" : {
     url:"/ins_pay_fetch",
     type:"POST",
     data:{
      effective_date:effective_date      
     }
    },
    drawCallback:function(settings)
    {
     $('#total_shift').html(settings.json.total_shift);
     $('#total_invoice').html(settings.json.total_invoice);
     $('#total_working').html(settings.json.total_working); 
     $('#total_working_amount').html(settings.json.total_working_amount);
     $('#total_short_amount').html(settings.json.total_short_amount);       
    },      
    dom: 'lBfrtip',
    buttons: [
    'excel', 'pdf'
   ],
    /*"columnDefs": [
        {"className": "dt-center", "targets": "_all"},
        {"className": "dt-right", "targets": [4]},
      ]*/
   
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