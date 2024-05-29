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

if (isset($_POST['invoice_btn'])){

  if (checkPermissions($_SESSION["user_id"], 68) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/inventory/issue_sub_loc');
    exit();
  }

  // if ($_POST['size']!='0') {
  //     $size=$_POST['size'];
  // }else{
  //     $size='';
  // }

  // if ($_POST['color']!='1') {
  //     $color=$_POST['color'];
  // }else{
  //     $color='';
  // }

  // if ($_POST['gender']!='1') {
  //     $gender=$_POST['gender'];
  // }else{
  //     $gender='';
  // }

  for ($i = 0; $i < count($_POST['product']); $i++) {

  $data = array(
    ':employee_id'=>  $_POST['employee_id'],
    ':product_id' =>  $_POST['product'][$i],
    ':size'       =>  $_POST['size'][$i],
    ':color'      =>  $_POST['color'][$i],
    ':gender'     =>  $_POST['gender'][$i],
    ':qty'        =>  $_POST['quantity'][$i],
    ':unit_price' =>  $_POST['price'][$i],
    ':total'      =>  $_POST['total'][$i],
    ':status'     =>  4,
    ':location_id'=>  $_POST['location_id'],
    ':ref_no'     =>  $_POST['ref_no'],
  );
 
  $query = "
  INSERT INTO inventory_stock(product_id, size, color, gender, location_id, qty, unit_price, status, ref_no, employee_id, total)
  VALUES (:product_id, :size, :color, :gender, :location_id, :qty, :unit_price, :status, :ref_no, :employee_id, :total)
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
}

if (isset($_POST['update_save'])){

  if (checkPermissions($_SESSION["user_id"], 68) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/inventory/issue_product');
    exit();
  }

  $statement = $connect->prepare('SELECT * FROM inventory_issue WHERE employee_id="'.$_GET['edit'].'"' );
$statement->execute(); 
if(empty($statement->rowCount())){
  $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Not Product or not select employee.</div>';
  header('location:/inventory/issue_product');
  exit();
}
 
    for ($i = 0; $i <= count($_POST['row_id']); $i++) {  

      $data = array(
        ':id'     =>  $_POST['row_id'][$i],
        ':status' =>  1,
      );

      $query = "
      UPDATE `inventory_issue` SET `status`=:status WHERE `id`=:id    
      ";
        
      $statement = $connect->prepare($query);

      if($statement->execute($data))
      {
        $errMSG = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';
        header('location:/inventory/issue_product/monthly/'.$_POST['employee_id'].'/'.$_POST['ref_no_table'].'');
      }else{
        $errMSG = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
        header('location:/inventory/issue_product');
      }
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
$query="SELECT id, color FROM inventory_color ORDER BY id ASC";
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
                      $ref_no=date('Y').'-'.str_pad($expNum[1]+1, 5, "0", STR_PAD_LEFT);
                    }else{
                      $ref_no=date('Y').'-'.str_pad(1, 5, "0", STR_PAD_LEFT);
                    }
                  }
                }
                else{
                  $ref_no=date('Y').'-'.str_pad(1, 5, "0", STR_PAD_LEFT);
                }

          ?>
        </div>
        <div class="row">          
          <div class="col-md-12">
            <div class="card card-success">
              <div class="card-header">
                <h3 class="card-title">Issue to Employee</h3>
                <a href="/inventory/issue_product" class="edit_data4 btn btn-sm bg-gradient-primary float-right">Back</a>               
              </div>
                <!-- /.card-header -->
              <div class="card-body">
                <div class="row">
                  <form action="" id="invoice-form" method="post" class="invoice-form" role="form" novalidate=""> 
                  <div class="load-animate animated fadeInUp">
                        
                    <div class="row">
                      <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                        <div class="form-group">
                          <label for="location_id">Location</label>
                          <select class="form-control select2 location_id" style="width: 100%;" name="location_id" id="location_id">
                            <option value="">Select Location</option>
                            <?php
                            $query="SELECT * FROM inventory_location ORDER BY location ASC";
                            $statement = $connect->prepare($query);
                            $statement->execute();
                            $result = $statement->fetchAll();
                            foreach($result as $row)
                            {
                              ?>
                              <option value="<?php echo $row['id']; ?>" <?php if ($row['id']==$_GET['loc_id']){ echo "SELECTED";} ?>><?php echo $row['location']; ?></option>
                              <?php
                            }
                            ?>
                          </select>
                        </div>
                      </div>          
                      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                        <div class="form-group">
                          <label for="employee_id">Employee</label>
                          <select class="form-control select2 employee_id" style="width: 100%;" name="employee_id" id="employee_id">
                            <option value="">Select Employee</option>
                            <?php
                            $query="SELECT j.join_id, e.surname, e.initial, j.employee_no, p.position_abbreviation FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid INNER JOIN position p ON e.position_id=p.position_id WHERE j.employee_status=0 OR j.employee_status=2 ORDER BY e.employee_id DESC";
                            $statement = $connect->prepare($query);
                            $statement->execute();
                            $result = $statement->fetchAll();
                            foreach($result as $row)
                            {
                              ?>
                              <option value="<?php echo $row['join_id']; ?>" <?php if ($row['join_id']==$_GET['add']){ echo "SELECTED";} ?>><?php echo $row['employee_no'].' '.$row['surname'].' '.$row['initial']; ?></option>
                              <?php
                            }
                            ?>
                          </select>
                        </div>          
                      </div>

                      <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                        <div class="form-group">
                          <label for="ref_no">Invoice No</label>
                          <input type="text" class="form-control" id="ref_no" name="ref_no" value="<?php echo $ref_no; ?>" readonly>
                        </div>
                      </div>

                      <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 pull-right">
                        <div class="form-group">
                          <label for="purchase_date">Invoice Date</label>
                          <div class="input-group date" id="reservationjoindate" data-target-input="nearest">
                            <input type="text" name="purchase_date" id="purchase_date" class="form-control datetimepicker-input" data-target="#reservationjoindate" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm-dd" data-mask value="<?php echo date('Y-m-d'); ?>" />
                            <div class="input-group-append" data-target="#reservationjoindate" data-toggle="datetimepicker">
                              <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                            </div>
                          </div>
                        </div>
                      </div>    
                    </div>

                    <div class="row">
                      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 resposive">
                        <table class="table table-bordered table-hover table-sm" id="invoiceItem"> 
                          <tr style="text-align:center;">
                            <th width="2%"><input id="checkAll" class="formcontrol" type="checkbox"></th>
                            <th width="14%">Product</th>
                            <th width="12%">Size</th>
                            <th width="12%">Color</th>
                            <th width="12%">Gender</th>
                            <th width="12%">Stock</th>
                            <th width="12%">Quantity</th>
                            <th width="12%">Price</th>                
                            <th width="12%">Total</th>
                          </tr>             
                          <tr>
                            <td><input class="itemRow" type="checkbox"></td>
                            <td>
                              <div class="form-group">                        
                                <select class="form-control select2" style="width: 100%;" name="product[]" id="product_1">
                                  <option value="">Select Product</option>
                                  <?php echo $product; ?>
                                </select>
                              </div>
                            </td>
                            <td>
                              <div class="form-group">
                                <input type="text" name="size[]" id="size_1" class="form-control" autocomplete="off">
                              </div>
                            </td>
                            <td>
                              <div class="form-group">
                                <select class="form-control select2" style="width: 100%;" id="color_1" name="color[]">                          
                                  <?php echo $color; ?>                          
                                </select>                        
                              </div>
                            </td>
                            <td>
                              <div class="form-group">
                                <select class="form-control select2" style="width: 100%;" id="gender_1" name="gender[]">                          
                                  <?php echo $gender; ?>  
                                </select>                        
                              </div>
                            </td>
                            <td>
                              <div class="form-group">
                              
                                <input type="text" class="form-control" id="aval_stock_1" name="aval_stock[]" readonly>
                              </div>

                            </td>
                            <td>
                              <input type="text" name="quantity[]" id="quantity_1" class="form-control quantity" autocomplete="off">
                            </td>

                            <td>
                              <input type="text" name="price[]" id="price_1" class="form-control price" autocomplete="off">
                            </td>
                            <td>
                              <input type="text" name="total[]" id="total_1" class="form-control total" autocomplete="off" readonly>
                            </td>
                          </tr>           
                        </table>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                        <button class="btn btn-danger delete" id="removeRows" type="button">- Delete</button>
                        <button class="btn btn-success" id="addRows" type="button">+ Add More</button>
                      </div>
                    </div>
                    <div class="row"> 
                      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                      </div>
                      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                        <span class="form-inline">
                          <div class="form-group">
                            <label>Subtotal: &nbsp;</label>
                            <div class="input-group">
                              <div class="input-group-addon currency"></div>
                              <input value="" type="text" class="form-control" name="subTotal" id="subTotal" placeholder="Subtotal" readonly>
                            </div>
                          </div>
                          
                        </span>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="form-group">            
                          <input data-loading-text="Saving Invoice..." type="submit" name="invoice_btn" value="Save Invoice" class="btn btn-success submit_btn invoice-save-btm offset-md-5">           
                        </div>
                      </div>
                    </div>
                    <div class="clearfix"></div>            
                  </div>
                </form> 
              </div>
                 
              </div>
              <!-- /.card-body -->

              <!-- <div class="card-footer">                  
                  
                </div> -->

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

  $('#invoice-form').validate({
    rules: {
      employee_id: { required: true},
      location_id: { required: true},
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
  $(document).on('click', '#checkAll', function() {           
    $(".itemRow").prop("checked", this.checked);
  }); 
  $(document).on('click', '.itemRow', function() {    
    if ($('.itemRow:checked').length == $('.itemRow').length) {
      $('#checkAll').prop('checked', true);
    } else {
      $('#checkAll').prop('checked', false);
    }
  });  
  var count = $(".itemRow").length;

  $(document).on('click', '#addRows', function() { 
    count++;
    var htmlRows = '';
    htmlRows += '<tr>';
    htmlRows += '<td><input class="itemRow" type="checkbox"></td>';          
    htmlRows += '<td><select class="form-control select2" style="width: 100%;" name="product[]" id="product_'+count+'"><option value="">Select Product</option><?php echo $product; ?></select></td>';          
    htmlRows += '<td><input type="text" name="size[]" id="size_'+count+'" class="form-control" autocomplete="off"></td>';
    htmlRows += '<td><select class="form-control select2" style="width: 100%;" id="color_'+count+'" name="color[]"><?php echo $color; ?></select></td>';
    htmlRows += '<td><select class="form-control select2" style="width: 100%;" id="gender_'+count+'" name="gender[]"><?php echo $gender; ?>                    </select></td>';
    htmlRows += '<td><input type="text" class="form-control" id="aval_stock_'+count+'" name="aval_stock[]" readonly></td>';
    htmlRows += '<td><input type="text" name="quantity[]" id="quantity_'+count+'" class="form-control quantity" autocomplete="off"></td>'; 
     htmlRows += '<td><input type="text" name="price[]" id="price_'+count+'" class="form-control price" autocomplete="off"></td>';              
    htmlRows += '<td><input type="text" name="total[]" id="total_'+count+'" class="form-control total" autocomplete="off" readonly></td>';          
    htmlRows += '</tr>';
    $('#invoiceItem').append(htmlRows);

    $('.select2').select2()

  }); 
  $(document).on('click', '#removeRows', function(){
    $(".itemRow:checked").each(function() {
      $(this).closest('tr').remove();
    });
    $('#checkAll').prop('checked', false);
    calculateTotal();
  });   
  $(document).on('blur', "[id^=quantity_]", function(){
    calculateTotal();
  }); 
  $(document).on('blur', "[id^=price_]", function(){
    calculateTotal();
  }); 
  $(document).on('blur', "#taxRate", function(){    
    calculateTotal();
  }); 
  $(document).on('blur', "#amountPaid", function(){
    var amountPaid = $(this).val();
    var totalAftertax = $('#totalAftertax').val();  
    if(amountPaid && totalAftertax) {
      totalAftertax = totalAftertax-amountPaid;     
      $('#amountDue').val(totalAftertax);
    } else {
      $('#amountDue').val(totalAftertax);
    } 
  }); 
  $(document).on('click', '.deleteInvoice', function(){
    var id = $(this).attr("id");
    if(confirm("Are you sure you want to remove this?")){
      $.ajax({
        url:"action.php",
        method:"POST",
        dataType: "json",
        data:{id:id, action:'delete_invoice'},        
        success:function(response) {
          if(response.status == 1) {
            $('#'+id).closest("tr").remove();
          }
        }
      });
    } else {
      return false;
    }
  });
}); 
function calculateTotal(){
  var totalAmount = 0; 
  $("[id^='price_']").each(function() {
    var id = $(this).attr('id');
    id = id.replace("price_",'');
    var price = $('#price_'+id).val();
    var quantity  = $('#quantity_'+id).val();
    if(!quantity) {
      quantity = 1;
    }
    var total = price*quantity;
    $('#total_'+id).val(parseFloat(total));
    totalAmount += total;     
  });
  $('#subTotal').val(parseFloat(totalAmount));  
  var taxRate = $("#taxRate").val();
  var subTotal = $('#subTotal').val();  
  if(subTotal) {
    var taxAmount = subTotal*taxRate/100;
    $('#taxAmount').val(taxAmount);
    subTotal = parseFloat(subTotal)+parseFloat(taxAmount);
    $('#totalAftertax').val(subTotal);    
    var amountPaid = $('#amountPaid').val();
    var totalAftertax = $('#totalAftertax').val();  
    if(amountPaid && totalAftertax) {
      totalAftertax = totalAftertax-amountPaid;     
      $('#amountDue').val(totalAftertax);
    } else {    
      $('#amountDue').val(subTotal);
    }
  }
}

</script>

<script>
  $(document).ready(function(){
   
    function load_qty(productId) {
    var product = $('#' + productId).val();
    var size = $('#size_' + productId).val();
    var color = $('#color_' + productId).val();
    var gender = $('#gender_' + productId).val();
    var avalStockId = 'aval_stock_' + productId.split('_')[1]; // Extract the number from productId

    if (product) {
        $.ajax({
            url: "/product_script",
            method: "POST",
            data: { product: product, size: size, color: color, gender: gender, request: 2 },
            dataType: 'json',
            success: function(response) {
                $('#' + avalStockId).val(response);
            }
        });
    } else {
        $('#' + avalStockId).val('');
    }
}

$('[id^="product_"]').change(function() {
    var productId = $(this).attr('id');
    load_qty(productId);
});

$('[id^="size_"]').change(function() {
    var productId = $(this).attr('id').split('_')[1];
    load_qty('size_' + productId);
});

$('[id^="color_"]').change(function() {
    var productId = $(this).attr('id').split('_')[1];
    load_qty('color_' + productId);
});

$('[id^="gender_"]').change(function() {
    var productId = $(this).attr('id').split('_')[1];
    load_qty('gender_' + productId);
});
    
  });
</script>