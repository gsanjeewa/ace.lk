
<?php
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();
require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 67) == "false") {

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
        <!-- Small boxes (Stat box) -->
       
          <div class="form-group" id="process" style="display:none;">
        <div class="progress">
       <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="">
       </div>
      </div>
       </div>
        
        
        <div class="row">
          
            <div class="col-xl-12 col-md-6 mb-4" id="success_message">
          
            </div>
          
        </div>
        <div class="row">          
          <div class="col-md-12">
            
              <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">Stock</h3>                       
                </div>
                  <!-- /.card-header -->
                <div class="card-body">  
                <?php
                  $query_loc = 'SELECT * FROM inventory_location ORDER BY type ASC';
                  $statement = $connect->prepare($query_loc);
                  $statement->execute();
                  $result_loc = $statement->fetchAll();

                  $query = 'SELECT * FROM inventory_product ORDER BY id ASC';
                  $statement = $connect->prepare($query);
                  $statement->execute();
                  $result = $statement->fetchAll();
                  ?>

                  <table id="example2" class="table table-bordered table-striped table-sm">
                      <thead style="text-align: center;">
                          <tr>
                              <th>#</th>
                              <th>Product</th>
                              <?php 
                              foreach ($result_loc as $row_loc) {
                                  ?>
                                  <th><?php echo $row_loc['location']; ?></th>
                                  <?php
                              }
                              ?>
                              <th>Total</th>
                          </tr>
                      </thead>
                      <tbody>
                          <?php
                          $sno = 1;
                          foreach ($result as $row) {
                              $total_qty = 0; // Initialize total quantity for each product
                              ?>
                              <tr>
                                  <td><center><?php echo $sno; ?></center></td>
                                  <td><center><?php echo $row['product_name']; ?></center></td>
                                  <?php 
                                  foreach ($result_loc as $row_loc) {
                                      $statement = $connect->prepare("SELECT sum(qty) AS total_qty FROM inventory_stock WHERE product_id = :product_id AND status = 1 AND location_id = :location_id");
                                      $statement->execute([
                                          ':product_id' => $row['id'],
                                          ':location_id' => $row_loc['id']
                                      ]);
                                      $product_qty = $statement->fetchColumn();

                                      $statement = $connect->prepare("SELECT sum(qty) AS total_qty FROM inventory_stock WHERE product_id = :product_id AND status = 2 AND location_id = :location_id");
                                      $statement->execute([
                                          ':product_id' => $row['id'],
                                          ':location_id' => $row_loc['id']
                                      ]);
                                      $sub_loc_issue = $statement->fetchColumn();

                                      $statement = $connect->prepare("SELECT sum(qty) AS total_qty FROM inventory_stock WHERE product_id = :product_id AND status = 2 AND sub_location_id = :sub_location_id");
                                      $statement->execute([
                                          ':product_id' => $row['id'],
                                          ':sub_location_id' => $row_loc['id']
                                      ]);
                                      $sub_loc_stock = $statement->fetchColumn();

                                      $statement = $connect->prepare("SELECT sum(qty) AS total_qty FROM inventory_stock WHERE product_id = :product_id AND status = 4 AND location_id = :location_id");
                                      $statement->execute([
                                          ':product_id' => $row['id'],
                                          ':location_id' => $row_loc['id']
                                      ]);
                                      $emp_qty = $statement->fetchColumn();

                                      $qty = (int)$product_qty + (int)$sub_loc_stock - (int)$sub_loc_issue - (int)$emp_qty;
                                      $total_qty += $qty; // Accumulate the quantity for total

                                      ?>
                                      <td><center><a href="/inventory/stock/<?php echo $row_loc['id']; ?>/<?php echo $row['id']; ?>"><?php echo $qty > 0 ? $qty : ''; ?></a></center></td>
                                      <?php
                                  }
                                  ?>
                                  <td><center><?php echo $total_qty > 0 ? $total_qty : ''; ?></center></td>

                              </tr>
                              <?php
                              $sno++;
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

 <?php
include '../inc/footer.php';
?>

<script>
 
 $(document).ready(function(){

  $('#example2').DataTable({
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });

  $('.view_payroll').click(function(){
      var $id=$(this).attr('data-id');
      location.href = "/payroll_list/payroll/"+$id;      
    });
  
  $('#sample_form').on('submit', function(event){
   event.preventDefault();   
    $.ajax({
     url:"/process",
     method:"POST",
     data:$(this).serialize(),
     beforeSend:function()
     {
      $('#calculate_payroll').attr('disabled', 'disabled');
      $('#process').css('display', 'block');
     },
     success:function(data)
     {
      var percentage = 0;

      var timer = setInterval(function(){
       percentage = percentage + 20;
       progress_bar_process(percentage, timer);
      }, 1000);
     }
    })
   
  });

  function progress_bar_process(percentage, timer)
  {
   $('.progress-bar').css('width', percentage + '%');
   if(percentage > 100)
   {
    clearInterval(timer);
    $('#sample_form')[0].reset();
    $('#process').css('display', 'none');
    $('.progress-bar').css('width', '0%');
    $('#calculate_payroll').attr('disabled', false);
    $('#success_message').html('<div class="alert alert-dismissible alert-success bg-gradient-success text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><span class="glyphicon glyphicon-info-sign"></span>Success.</div>');
    setTimeout(function(){
     $('#success_message').html('');
     location.reload();
    }, 5000);
   }
  }

  $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    });

 });
</script>

