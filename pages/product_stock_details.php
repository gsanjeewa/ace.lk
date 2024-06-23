
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

$query_location = "SELECT location, type, id FROM inventory_location WHERE id='".$_GET['location_id']."'";
$statement = $connect->prepare($query_location);
$statement->execute();
$result_location = $statement->fetchAll();
foreach($result_location as $row_location)
{
 
}

$query_product = "SELECT product_name, id FROM inventory_product WHERE id='".$_GET['product_id']."'";
$statement = $connect->prepare($query_product);
$statement->execute();
$result_product = $statement->fetchAll();
foreach($result_product as $row_product)
{
 
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
                  <h3 class="card-title"><?php echo $row_location['location'].' - '.$row_product['product_name']; ?></h3>                       
                </div>
                  <!-- /.card-header -->
                <div class="card-body">  

                <?php
                $query_loc = 'SELECT * FROM inventory_location ORDER BY type ASC';
                $statement = $connect->prepare($query_loc);
                $statement->execute();
                $result_loc = $statement->fetchAll();

                $query = 'SELECT * FROM inventory_stock WHERE product_id= :product_id';
                if ($row_location['type'] == 1) {
                    $query .= ' AND location_id= :location_id';
                } elseif ($row_location['type'] == 2) {
                    $query .= ' AND sub_location_id= :location_id';
                }
                $query .= ' GROUP BY product_id, size, color, gender';

                $statement = $connect->prepare($query);
                $statement->execute([
                    ':product_id' => $_GET['product_id'],
                    ':location_id' => $_GET['location_id']
                ]);
                $result = $statement->fetchAll();
                ?>

                <table id="example2" class="table table-bordered table-striped table-sm">
                    <thead style="text-align: center;">
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sno = 1;
                        foreach ($result as $row) {
                            $total_qty = 0; // Initialize total quantity for each product variation
                            
                            // Fetch color
                            $query_color = "SELECT color FROM inventory_color WHERE id= :color_id";
                            $statement = $connect->prepare($query_color);
                            $statement->execute([':color_id' => $row['color']]);
                            $row_color = $statement->fetch();
                            $color = $row_color && $row_color['color'] != 'No' ? $row_color['color'] : '';

                            // Fetch gender
                            $query_gender = "SELECT gender FROM inventory_gender WHERE id= :gender_id";
                            $statement = $connect->prepare($query_gender);
                            $statement->execute([':gender_id' => $row['gender']]);
                            $row_gender = $statement->fetch();
                            $gender = $row_gender && $row_gender['gender'] != 'No' ? $row_gender['gender'] : '';

                            // Calculate quantity
                            $product_qty = $sub_loc_issue = $sub_loc_stock = $emp_qty = 0;

                            // Query for each status
                            $status_queries = [
                                'product_qty' => ['status' => 1, 'location_id' => true],
                                'sub_loc_issue' => ['status' => 2, 'location_id' => true],
                                'sub_loc_stock' => ['status' => 2, 'sub_location_id' => true],
                                'emp_qty' => ['status' => 4, 'location_id' => true]
                            ];

                            foreach ($status_queries as $var_name => $query_info) {
                                $query = "SELECT SUM(qty) AS total_qty FROM inventory_stock WHERE product_id = :product_id AND status = :status AND size = :size AND color = :color AND gender = :gender";
                                $params = [
                                    ':product_id' => $_GET['product_id'],
                                    ':status' => $query_info['status'],
                                    ':size' => $row['size'],
                                    ':color' => $row['color'],
                                    ':gender' => $row['gender']
                                ];
                                if (isset($query_info['location_id'])) {
                                    $query .= " AND location_id = :location_id";
                                    $params[':location_id'] = $_GET['location_id'];
                                }
                                if (isset($query_info['sub_location_id'])) {
                                    $query .= " AND sub_location_id = :sub_location_id";
                                    $params[':sub_location_id'] = $_GET['location_id'];
                                }
                                $statement = $connect->prepare($query);
                                $statement->execute($params);
                                ${$var_name} = $statement->fetchColumn();
                            }

                            $qty = (int)$product_qty + (int)$sub_loc_stock - (int)$sub_loc_issue - (int)$emp_qty;
                            $total_qty += $qty; // Accumulate the quantity for total

                            ?>
                            <tr>
                                <td><center><?php echo $sno; ?></center></td>
                                <td><?php echo $row['size'] . ' ' . $color . ' ' . $gender; ?></td>
                                <td><center><?php echo $qty > 0 ? $qty : ''; ?></center></td>
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

