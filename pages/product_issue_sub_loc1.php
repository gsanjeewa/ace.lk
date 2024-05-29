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

  $data = array(
    ':id'         =>  $sno,
    ':product_id' =>  $_POST['product'],
    ':size'       =>  $size,
    ':color'      =>  $color,
    ':gender'     =>  $gender,
    ':qty'        =>  $_POST['qty'],
    ':unit_price' =>  $_POST['unit_price'],
    ':total'      =>  $total,
    ':status'     =>  2,
    ':location_id'=>  $_POST['location_id'],
  );
 
  $query = "
  INSERT INTO inventory_stock(id, product_id, size, color, gender, location_id, qty, unit_price, status, total)
  VALUES (:id, :product_id, :size, :color, :gender, :location_id, :qty, :unit_price, :status, :total)
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

if (isset($_POST['update_save'])){

  if (checkPermissions($_SESSION["user_id"], 68) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/inventory/issue_sub_loc');
    exit();
  }

  $store_row_id= explode(",", $_POST['store_row_id']);
 
    for ($i = 0; $i <= count($store_row_id); $i++) {  

      $data = array(
        ':id'     =>  $store_row_id[$i],
        ':ref_no'     =>  $_POST['ref_no'],
        ':status' =>  3,
      );

      $query = "
      UPDATE inventory_stock SET status=:status, ref_no=:ref_no WHERE id=:id    
      ";
        
      $statement = $connect->prepare($query);

      if($statement->execute($data))
      {
        $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';
        header('location:/inventory/issue_sub_loc');
      }else{
        $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
        header('location:/inventory/issue_sub_loc');
      }
    }  
}

if (isset($_POST['remove_product'])){

  if (checkPermissions($_SESSION["user_id"], 68) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/inventory/issue_sub_loc');
    exit();
  }

  $data = array(
      ':id'      =>  $_POST['delete_id'],     
  );

  $query = "DELETE FROM inventory_stock WHERE id=:id";
    
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

$product = '';
$query="SELECT * FROM inventory_product WHERE status=0 ORDER BY id ASC";
$statement = $connect->prepare($query);
$statement->execute();
$result = $statement->fetchAll();
foreach($result as $row)
{
 $product .= '<option value="'.$row['id'].'">'.$row['product_name'].'</option>';
}

$color = '';
$query="SELECT * FROM inventory_color ORDER BY id ASC";
$statement = $connect->prepare($query);
$statement->execute();
$result = $statement->fetchAll();
foreach($result as $row_color)
{
 $color .= '<option value="'.$row_color['id'].'">'.$row_color['color'].'</option>';
}

$gender = '';
$query="SELECT id, gender FROM inventory_gender ORDER BY id ASC";
$statement = $connect->prepare($query);
$statement->execute();
$result = $statement->fetchAll();
foreach($result as $row_gender)
{
 $gender .= '<option value="'.$row_gender['id'].'">'.$row_gender['gender'].'</option>';
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

          $statement = $connect->prepare("SELECT ref_no FROM inventory_stock ORDER BY ref_no DESC LIMIT 1");
                $statement->execute();
                $result = $statement->fetchAll();
                if ($statement->rowCount()>0) {
                  foreach($result as $invoice_no){
                    $expNum = explode('-', $invoice_no['ref_no']);             
                      
                    if ($expNum[0]==date('Y')) {
                      $ref_no=date('Y').'-'.str_pad($expNum[1]+1, 4, "0", STR_PAD_LEFT);
                    }else{
                      $ref_no=date('Y').'-'.str_pad(1, 4, "0", STR_PAD_LEFT);
                    }
                  }
                }
                else{
                  $ref_no=date('Y').'-'.str_pad(1, 4, "0", STR_PAD_LEFT);
                }
   
// $arr=array ('I','am','simple','boy!');
// echo implode(", ",$arr);

/*$str="I, am, simple, boy!";
print_r(explode(",",$str));*/

          ?>
        </div>
        <div class="row">          
          <div class="col-md-12">                          
              
                <div class="card card-success">
                  <div class="card-header">
                    <h3 class="card-title">Issue to Sub Loc</h3>                
                  </div>
                    <!-- /.card-header -->
                  <div class="card-body">                                      
                    <form action="" id="add_issue_form" method="post">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="location_id">Sub Location</label>
                        <select class="form-control select2 location_id" style="width: 100%;" name="location_id" id="location_id">
                          <option value="">Select Location</option>
                          <?php
                          $query="SELECT * FROM inventory_location WHERE type=2 ORDER BY location ASC";
                          $statement = $connect->prepare($query);
                          $statement->execute();
                          $result = $statement->fetchAll();
                          foreach($result as $row)
                          {
                            ?>
                            <option value="<?php echo $row['id']; ?>" <?php if ($row['id']==$_GET['add']){ echo "SELECTED";} ?>><?php echo $row['location']; ?></option>
                            <?php
                          }
                          ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="ref_no">Invoice No</label>
                        <input type="text" class="form-control" id="ref_no" name="ref_no" value="<?php echo $ref_no; ?>" readonly>
                      </div>
                    </div>                    
                  </div>
                
                  <div class="row">
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="product">Product</label>
                        <select class="form-control select2" style="width: 100%;" name="product" id="product">
                          <option value="">Select Product</option>
                          <?php echo $product; ?>
                          <!-- <?php
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
                          ?> -->
                        </select>
                      </div>
                    </div>

                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="size">Size:</label>
                        <input type="text" name="size" id="size" class="form-control" autocomplete="off">                        
                      </div>
                    </div>

                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="color">Color:</label>
                        <select class="form-control select2" style="width: 100%;" id="color" name="color">
                          <?php echo $color; ?>
                        </select>                        
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="form-group">
                        <label for="gender">Gender:</label>
                        <select class="form-control select2" style="width: 100%;" id="gender" name="gender">
                          <?php echo $gender; ?>
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
                        <select class="form-control select2" style="width: 100%;" name="unit_price" id="unit_price">
                          <option value="">Select Price</option>                          
                        </select>
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
                        <button class="btn btn-sm btn-primary col-sm-3" name="add_save"> Add</button>
                      </div>
                    </div>
                  </div>
                  </form>
                  

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
                          <tr><td colspan="6" class="text-center">Select Location</td></tr>
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

                <div class="card-footer">
                  <form method="POST" action="" >
              <input type="text" name="store_row_id" id="store_row_id">
              <input type="text" id="ref_no" name="ref_no" value="<?php echo $ref_no; ?>">
              <button class="btn btn-sm btn-primary col-sm-2 offset-md-5" name="update_save"><i class="fas fa-save"> Save</i></button>

            </form>

                  <!-- <button class="btn btn-sm btn-primary col-sm-2 offset-md-5" name="invoice_print">Print</button> -->
                  <!-- <button class="btn btn-sm btn-default col-sm-3" type="reset"> Cancel</button> -->
                </div>

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

    function load_data(location_id = '' , )
{
  var location_id = $('#location_id').val();
     
  $.ajax({
    url:"/fetch_issue",
    method:"POST",
    data:{location_id:location_id, request:1},
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
      document.getElementById('total_data_table').innerHTML = response.total_data_table;
      document.getElementById('store_row_id').value = response.id;
            
    }
  });  
}

    $('#location_id').change(function(){
    var location_id = $('#location_id').val();
    load_data(location_id);
    });    
    

  });


  $(document).on('change','#location_id', function(){
    var location_id=$(this).val();        
    if (location_id == ''){
      location.href = "/inventory/issue_sub_loc"; 
    }else{
      location.href = "/inventory/issue_sub_loc/"+location_id;
    }  
  });


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
      
      if (product) {
        $.ajax({
          url:"/product_script",
          method:"POST",
          data:{product:product,size:size,color:color,gender:gender,request:2},
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

    $('#size').keyup(function(){
    var size = $('#size').val();
    load_qty(size);
    });

    $('#color').change(function(){
    var color = $('#color').val();
    load_qty(color);
    });

    $('#gender').change(function(){
    var gender = $('#gender').val();
    load_qty(gender);
    });
    
  });
</script>