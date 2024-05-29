<?php 
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();
require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 68) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}

if (isset($_POST['add_save'])){

  if (checkPermissions($_SESSION["user_id"], 68) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/inventory/issue_sub_loc');
    exit();
  }
  
  $data = array(
    ':invoice_no'   =>  $_POST['invoice_no'],
    ':location_id'  =>  $_POST['location_id'],
    ':sub_location_id'  =>  $_POST['sub_location_id'],
  );
 
  $query = "
  INSERT INTO inventory_create_invoice_loc(invoice_no, location_id, sub_location_id)
  VALUES (:invoice_no, :location_id, :sub_location_id)
  ";
          
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';    

  }else{
      $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
  }
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
          <div class="col-md-12">              
            <div class="card card-success">
              <div class="card-header">
                <h3 class="card-title">Issue to Sub Location</h3> 

                <button class="edit_data4 btn btn-sm bg-gradient-primary float-right" type="button" ><i class="fas fa-plus"></i> Create Invoice</button>              
              </div>
                <!-- /.card-header -->
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12">

                    <table id="emp_data" class="table table-bordered table-striped table-sm table-hover">                    
                      <thead style="text-align: center; width: 100%;">
                        <tr>
                          <th>#</th>
                          <th>Location</th>
                          <th>Sub Location</th>
                          <th>Invoice No</th>
                          <th>Total</th>
                          <th>Action</th>
                        </tr>  
                      </thead>
                      <tbody>
                      </tbody>
                    </table>

                  </div>
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->           
                   
          </div>          
        </div>
        <!-- /.row -->

      <!--  start  modal -->
    <div id="editData4" class="modal fade">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Create Invoice</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="info_update4">
            <?php @include("/create_invoice_loc");?>
          </div>
          <!-- <div class="modal-footer ">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          </div> -->
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->
    </div>
    <!--   end modal --> 

      </div><!-- /.container-fluid -->
    </section>    

<?php
include '../inc/footer.php';
?>

<script type="text/javascript">
  $(document).ready(function() {

    var dataTable = $('#emp_data').DataTable({
      "processing":true,
      "serverSide":true,
      "autoWidth": false,
      "scrollX": false,
      "order":[],
      "ajax":{
       url:"/issue_sub_loc_fetch",
       type:"POST"
      },
      "columnDefs":[
        {
          "targets":[0, 4],
          "orderable":false,
        },

        {
          "targets": [0, 3], // Replace 1 with the index of the column you want to center (zero-based index)
          "className": "text-center"
        },
        {
          "targets": [3], // Replace 1 with the index of the column you want to center (zero-based index)
          "className": "text-right"
        }
      ],
      // order: [[2, 'desc']],
      "drawCallback": function(settings) {
        var api = this.api();
        var startIndex = api.context[0]._iDisplayStart; // Get the index of the first row displayed on the current page
        api.column(0).nodes().each(function(cell, i) {
          cell.innerHTML = startIndex + i + 1; // Set the content of each cell in the serial number column
        });
      }

    });
 
 
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    });

    $(document).on('click','.edit_data4',function(){
      $("#editData4").modal({
          backdrop: 'static',
          keyboard: false
      });
      var edit_id4=$(this).attr('data-id');
      $.ajax({
        url:"/create_invoice_loc",
        type:"post",
        data:{edit_id4:edit_id4},
        success:function(data){
          $("#info_update4").html(data);
          $("#editData4").modal('show');
        }
      });
    });

  });
</script>
