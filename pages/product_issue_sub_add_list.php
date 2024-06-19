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

$error = false;

if (isset($_POST['add_save'])){

  if (checkPermissions($_SESSION["user_id"], 68) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/inventory/issue_sub_loc');
    exit();
  }

  if ($_POST['aval_stock']<$_POST['qty'] ):
    $error = true;
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>More than available qty.</div>';
  endif;
  

  $statement = $connect->prepare("SELECT id FROM inventory_stock ORDER BY id DESC LIMIT 1");
    $statement->execute();
    $result = $statement->fetchAll();
    if ($statement->rowCount()>0) {        
      foreach($result as $row_id){
        $startpoint = $row_id['id'];        
      }
    }
    else{
      $startpoint = 0;
    }
    
    $sno = $startpoint + 1;


  if ($_POST['size']!='size') {
      $size=$_POST['size'];
  }else{
      $size='';
  }

  if ($_POST['color']!='color') {
      $color=$_POST['color'];
  }else{
      $color='';
  }

  if ($_POST['gender']!='gender') {
      $gender=$_POST['gender'];
  }else{
      $gender='';
  }

  $total=$_POST['qty']*$_POST['unit_price'];

  if(!$error):
    $data = array(
      ':id'         =>  $sno,
      ':loc_invoice_id' =>  $_GET['invoice_id'],
      ':product_id' =>  $_POST['product'],
      ':size'       =>  $size,
      ':color'      =>  $color,
      ':gender'     =>  $gender,
      ':qty'        =>  $_POST['qty'],
      ':unit_price' =>  $_POST['unit_price'],
      ':total'      =>  $total,
      ':status'     =>  2,
      ':location_id'=>  $_POST['location_id'],
      ':sub_location_id'=>  $_POST['sub_location_id'],
    );
  
    $query = "
    INSERT INTO inventory_stock(id, product_id, size, color, gender, location_id, sub_location_id, qty, unit_price, status, loc_invoice_id, total)
    VALUES (:id, :product_id, :size, :color, :gender, :location_id, :sub_location_id, :qty, :unit_price, :status, :loc_invoice_id, :total)
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
  endif;
}

if (isset($_POST['update_save'])){

  if (checkPermissions($_SESSION["user_id"], 68) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/inventory/issue_product');
    exit();
  }

//   $statement = $connect->prepare('SELECT * FROM inventory_issue WHERE employee_id="'.$_GET['edit'].'"' );
// $statement->execute(); 
// if(empty($statement->rowCount())){
//   $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Not Product or not select employee.</div>';
//   header('location:/inventory/issue_product');
//   exit();
// }
    
      $data = array(
        ':id'           =>  $_GET['invoice_id'],
        ':grand_total'  =>  $_POST['grand_total'],
        ':status'       =>  1,
      );

      $query = "
      UPDATE inventory_create_invoice_loc SET grand_total=:grand_total, status=:status WHERE id=:id    
      ";
        
      $statement = $connect->prepare($query);

      if($statement->execute($data))
      {
        $errMSG = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';
        header('location:/inventory/issue_sub_loc');
      }else{
        $errMSG = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
        header('location:/inventory/issue_sub_loc/'.$_GET['invoice_id'].'');
      }
    }  


if (isset($_POST['remove_product'])){

  if (checkPermissions($_SESSION["user_id"], 68) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/inventory/issue_product');
    exit();
  }

  $data = array(
      ':id'      =>  $_POST['delete_id'],     
  );

  $query = "DELETE FROM inventory_stock WHERE id=:id";
    
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
                    <h3 class="card-title">Issue to Employee</h3>                
                  </div>
                    <!-- /.card-header -->
                  <div class="card-body">
                  <?php 
                  if(isset($_GET['invoice_id']))
                  {
                    $query = 'SELECT a.location_id, a.sub_location_id, a.invoice_date, a.id, a.grand_total, a.status, b.location, c.location AS sub_location, a.invoice_no FROM inventory_create_invoice_loc a                     
                    INNER JOIN inventory_location b ON a.location_id=b.id
                    INNER JOIN inventory_location c ON a.sub_location_id=c.id
                    WHERE a.id="'.$_GET['invoice_id'].'"';

                    $statement = $connect->prepare($query);
                    $statement->execute();
                    $total_data = $statement->rowCount();
                    $result = $statement->fetchAll();
                    if ($total_data > 0){   
                      foreach($result as $row)
                      {
                        if (!empty($row['employee_no'])) {
                          $employee_epf=$row['employee_no'];
                        }else{
                          $employee_epf='';
                        }
                        ?>
                        <form action="" id="add_issue_form" method="post">
                  <div class="row">
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="location_id">Location</label>
                        <input type="hidden" name="location_id" id="location_id" class="form-control" value="<?php echo $row["location_id"]; ?>" readonly><input type="text" class="form-control" value="<?php echo $row["location"]; ?>" readonly>
                        
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="sub_location_id">Sub Location</label>
                        <input type="hidden" name="sub_location_id" id="sub_location_id" class="form-control" value="<?php echo $row["sub_location_id"]; ?>" readonly><input type="text" class="form-control" value="<?php echo $row["sub_location"]; ?>" readonly>
                        
                      </div>
                    </div>

                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="invoice_no">Invoice No</label>
                        <input type="text" class="form-control" id="invoice_no" name="invoice_no" value="<?php echo $row["invoice_no"]; ?>" readonly>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label for="invoice_date">Invoice Date</label>
                        <input type="text" class="form-control" id="invoice_date" name="invoice_date" value="<?php echo $row["invoice_date"]; ?>" readonly>
                      </div>
                    </div>                   
                  </div>

                  <?php 
                  if ($row['status']!=1) {
                    ?>
                    <div class="row">
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="product">Product</label>
                        <select class="form-control select2" style="width: 100%;" name="product" id="product">
                          <option value="">Select Product</option>
                          <?php
                          $query="SELECT * FROM inventory_product WHERE status=0 ORDER BY id ASC";
                          $statement = $connect->prepare($query);
                          $statement->execute();
                          $result = $statement->fetchAll();
                          foreach($result as $row)
                          {
                            ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $row['product_name']; ?></option>
                            <?php
                          }
                          ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="size">Size:</label>
                        <input type="text" name="size" id="size" class="form-control">
                      </div>
                    </div>

                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="color">Color:</label>
                        <select class="form-control select2" style="width: 100%;" id="color" name="color">
                          <option value="">Select color</option>
                          <?php
                          $query="SELECT * FROM inventory_color ORDER BY id ASC";
                          $statement = $connect->prepare($query);
                          $statement->execute();
                          $result = $statement->fetchAll();
                          foreach($result as $row)
                          {
                            ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $row['color']; ?></option>
                            <?php
                          }
                          ?>
                        </select>                        
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="gender">Gender:</label>
                        <select class="form-control select2" style="width: 100%;" id="gender" name="gender">
                          <option value="">Select Gender</option>
                          <?php
                          $query="SELECT * FROM inventory_gender ORDER BY id ASC";
                          $statement = $connect->prepare($query);
                          $statement->execute();
                          $result = $statement->fetchAll();
                          foreach($result as $row)
                          {
                            ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $row['gender']; ?></option>
                            <?php
                          }
                          ?>
                        </select>                        
                      </div>
                    </div>

                    <div class="col-md-1">
                      <div class="form-group">
                        <label for="aval_stock">Stock</label>
                        <input type="text" class="form-control" id="aval_stock" name="aval_stock" readonly>
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="unit_price">Unit Price</label>
                        <input type="text" class="form-control" id="unit_price" name="unit_price">
                      </div>
                    </div>

                    <div class="col-md-1">
                      <div class="form-group">
                        <label for="qty">Qty</label>
                        <input type="text" class="form-control" id="qty" name="qty">
                      </div>
                    </div>
                      
                  </div>
                    <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <button class="btn btn-sm btn-primary col-sm-3" name="add_save"><i class="fas fa-plus"></i> Add</button>
                      </div>
                    </div>
                  </div>
                    <?php
                  }
                  ?>
                
                  
                  </form>
                        <?php
                      }
                    }
                  }
                  ?> 

                  <div class="row">
                    <div class="col-md-12">
                      <label for="gceol">Product Details</label>
                      <table class="table table-bordered table-striped table-sm">
                        <thead style="text-align: center;">
                          <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 35%;">Product</th>
                            <th style="width: 20%;">Rate</th>
                            <th style="width: 10%;">Qty</th>
                            <th style="width: 20%;">Price</th>
                            <th style="width: 10%;">Action</th>
                          </tr>
                        </thead>
                        <tbody id="post_data">
                          <tr><td colspan="6" class="text-center">Select Employee</td></tr>
                      </tbody>
                        <tfoot>
                          <tr>
                            <th colspan="4" style="text-align: right;">Total</th>
                            <th style="text-align: right;"><b><span id="total_data_table">0.00</span></b></th>
                            <th></th>
                          </tr>
                        </tfoot>
                      </table>

                    </div>
                  </div>                  
                  
                </div>
                <!-- /.card-body -->

                <?php 
                  if ($row['status']!=1) {
                    ?>
                <div class="card-footer">
                  <form method="POST" action="">
              <input type="hidden" name="grand_total" id="grand_total">
              <button class="btn btn-sm btn-info col-sm-2 offset-md-5" name="update_save"><i class="fas fa-save"></i> Save</button>

            </form>
                  <!-- <button class="btn btn-sm btn-primary col-sm-2 offset-md-5" name="invoice_print">Print</button> -->
                  <!-- <button class="btn btn-sm btn-default col-sm-3" type="reset"> Cancel</button> -->
                </div>
                <?php
                  }
                  ?>

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

<script src="/plugins/bs-stepper/main.js"></script>

<script>

$(function () {

  $('#add_issue_form').validate({
    rules: {
      employee_id: { required: true},
      product: {required: true},
      unit_price: {required: true},
      qty: {required: true, number:true, lessThanEqual:"#aval_stock"}      
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

  $(document).ready(function(){

    load_data();
   
    setInterval(function(){
    load_data();       
  }, 2000);

    function load_data(invoice_id = '' , )
{
  var invoice_id = <?php echo $_GET['invoice_id']; ?>;
     
  $.ajax({
    url:"/fetch_issue",
    method:"POST",
    data:{invoice_id:invoice_id, request:3},
    dataType: 'json',

    success:function(response)
    {
      var html='';
      var serial_no = 1;
      
      if(response.data.length > 0)
      {
        for(var count = 0; count < response.data.length; count++)
        {
          html += '<tr>';
          html += '<td style="width:5%;"><center>'+serial_no+'</center></td>';
          html += '<td style="width:30%;">'+response.data[count].product_id+'</td>';
          html += '<td style="width:10%;"><center>'+response.data[count].unit_price+'</center></td>';
          html += '<td style="text-align:right; width:10%;"><center>'+response.data[count].qty+'</center></td>';
          html += '<td style="text-align:right; width:10%;"><center>'+response.data[count].total+'</center></td>';          
          html += '<td style="text-align:right; width:10%;"><center>'+response.data[count].action+'</center></td>';
          html += '</tr>'; 
          serial_no++;            
        }
      }
      else
      {
        html += '<tr><td colspan="9" class="text-center">No Data Found</td></tr>';
      }
      document.getElementById('post_data').innerHTML = html;
      // document.getElementById('total_data_table').innerHTML = response.total_data_table;
      document.getElementById('grand_total').value = response.total_data_table;
      var totalDataTable = document.getElementById('total_data_table');

      // Assuming response.total_data_table contains the number you want to format
      var numberToFormat = response.total_data_table;

      // Format the number
      var formattedNumber = parseFloat(numberToFormat).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'); // Formats to 2 decimal places with comma as thousand separator

      // Update the inner HTML of the span element with the formatted number
      totalDataTable.innerHTML = formattedNumber;

    }
  });  
}

    $('#employee_id').change(function(){
    var employee_id = $('#employee_id').val();
    load_data(employee_id);
    });
  });

  // $(document).on('change','#employee_id', function(){
  //   var employee_id=$(this).val();
  //   var location_id=$(this).val();
  //   if (employee_id == '' && location_id == ''){
  //     location.href = "/inventory/issue_product"; 
  //   }else{
  //     location.href = "/inventory/issue_product/"+employee_id+"/"+location_id;
  //   }  
  // });

  // ajax script for getting state data
 // $(document).on('change','#product', function(){
 //    var productID = $(this).val();
 //    if(productID){
 //      $.ajax({
 //        type:'POST',
 //        url:'/product_price',
 //        data:{'product_id':productID,'request':1},
 //        success:function(result){
 //            $('#unit_price').html(result);               
 //        }
 //      });

 //      $.ajax({
 //        type:'POST',
 //        url:'/backend',
 //        data:{'product_id':productID,'request':1},
 //        success:function(result){
 //            $('#size').html(result);
           
 //        }
 //      });
 //      $.ajax({
 //          type:'POST',
 //          url:'/backend',
 //          data:{'product_id':productID,'request':2},
 //          success:function(result){
 //              $('#color').html(result);
             
 //          }
 //      });
 //      $.ajax({
 //          type:'POST',
 //          url:'/backend',
 //          data:{'product_id':productID,'request':3},
 //          success:function(result){
 //              $('#gender').html(result);
             
 //          }
 //      });
         
 //    }else{
 //      $('#unit_price').html('<option value="">Select Price</option>');
 //      $('#size').html('<option value="">First Select Product</option>');
 //      $('#color').html('<option value="">First Select Product</option>');
 //      $('#gender').html('<option value="">First Select Product</option>');         
 //    }
 //  });  


 /*$(document).on('change','#product', function(){
    var productID = $(this).val();

    if(productID){
      $.ajax({
        type:'POST',
        url:'/product_price',
        data:{'product_id':productID,'request':2},
        success:function(result){
          document.getElementById('aval_stock').value = result; 
           
        }
      });
         
    }else{
      document.getElementById('aval_stock').value = '';          
    }
  }); */
</script>

<script>
  $(document).ready(function(){

    load_qty();

    function load_qty(product = '')
    {
      var product = $('#product').val();
      var size = $('#size').val();
      var color = $('#color').val();
      var gender = $('#gender').val();
      var location_id = $('#location_id').val();
      
      if (product) {
        $.ajax({
          url:"/product_script",
          method:"POST",
          data:{product:product,size:size,color:color,gender:gender,location_id:location_id,request:2},
          dataType: 'json',

          success:function(response)
          {
            document.getElementById('aval_stock').value = response;
          }
        });
      }else{
        document.getElementById('aval_stock').value = '';
      }
    }

    $('#product').change(function(){
    var product = $('#product').val();
    load_qty(product);
    });

    $('#size').change(function(){
    var size = $('#size').val();
    load_qty(size);
    });

    $('#color').change(function(){
    var color = $('#color').val();
    load_qty(color);
    });

    $('#gender').change(function(){
    var product = $('#gender').val();
    load_qty(gender);
    });
    
  });
</script>