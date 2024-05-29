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

$statement = $connect->prepare('SELECT * FROM inventory_issue WHERE ref_no="'.$_GET['ref_no'].'" AND employee_id="'.$_GET['emp_id'].'"' );
$statement->execute(); 
if(empty($statement->rowCount())){
  $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Not Product</div>';
  header('location:/inventory/issue_product/'.$_GET['emp_id'].'');
  exit();
}

$error = false;

if (isset($_POST['update_save'])){

  if (checkPermissions($_SESSION["user_id"], 68) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/inventory/issue_product');
    exit();
  }

  for($i= 1; $i <= $_POST['loan_plan']; $i++){
    $date = date("Y-m-d",strtotime($_POST['date_effective']." +".$i." months"));

    $data = array(
      ':employee_id'  =>  $_GET['emp_id'],
      ':paid_amount'  =>  $_POST['monthly_ins'],
      ':due_date'     =>  $date,      
    );

    $query = "
    INSERT INTO `inventory_deduction`(`employee_id`, `due_date`, `amount`) VALUES (:employee_id, :due_date, :paid_amount)   
    ";
      
    $statement = $connect->prepare($query);

    if($statement->execute($data))
    {
      $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';
      header('location:/inventory/issue_product');             
    }else{
      $errMSG = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
      header('location:/inventory/issue_product');
    }
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
                          
              <form action="" id="add_issue_form" method="post">
                <div class="card card-success">
                  <div class="card-header">
                    <h3 class="card-title">Monthly Installment</h3>                
                  </div>
                    <!-- /.card-header -->
                  <div class="card-body">                                      
                  
                  <div class="row">
                    <div class="col-md-12"> 

                    <?php

                    $query = '
                    SELECT SUM(total) AS total FROM inventory_issue WHERE ref_no="'.$_GET['ref_no'].'" 
                    ';

                    $statement = $connect->prepare($query);
                    $statement->execute();
                    $total_data = $statement->rowCount();
                    $result = $statement->fetchAll();

                    if($total_data > 0)
                    {
                      foreach($result as $row)
                      {

                      }
                    }
                    ?>                   

                      <div class="row">
                        <div class="col-md-2">
                          <div class="form-group">
                            <label for="total_amount">Total Amount</label>
                            <input type="text" class="form-control" id="total_amount" name="total_amount" onkeyup="getAmount(this.value)" value="<?php echo $row['total']; ?>" readonly>
                          </div>
                        </div>

                        <div class="col-md-2">
                          <div class="form-group">
                            <label for="loan_plan">Plan</label>
                            <input type="text" class="form-control" id="loan_plan" name="loan_plan" onkeyup="getAmount(this.value)">
                          </div>
                        </div>

                        <div class="col-md-3">
                          <div class="form-group">
                            <label for="date_effective" class="control-label">Effective Date</label>
                            <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                <input type="text" name="date_effective" id="date_effective" class="form-control datetimepicker-input" data-target="#reservationdate" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm-dd" data-mask value="<?php echo $row['date_effective'] ; ?>" />
                                <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                                </div>
                              </div>
                          </div>
                        </div>

                        <div class="col-md-2">
                          <div class="form-group">
                            <label for="monthly_ins">Monthly Installment</label>
                            <input type="text" class="form-control" id="monthly_ins" name="monthly_ins" readonly>
                          </div>
                        </div>

                      </div>

                  

                    </div>
                  </div>
                  
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button class="btn btn-sm btn-primary col-sm-2 offset-md-5" name="update_save"> Save</button>
                  <!-- <button class="btn btn-sm btn-default col-sm-3" type="reset"> Cancel</button> -->
                </div>

              </div>
              <!-- /.card -->
            </form>
                   
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
function getAmount(value){
    var loan_plan = $('#loan_plan').val();
    var total_amount = $('#total_amount').val();
    $('#monthly_ins').val(total_amount/loan_plan);
 
    
  
  }
</script>

<script>

$(function () {

  $('#add_issue_form').validate({
    rules: {
      employee_id: { required: true},
      product: {required: true},
      unit_price: {required: true},
      loan_plan: {required: true},
      row_id: {required: true},
      date_effective: {required: true, date:true},
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
    
    function load_data(page, query = '')
    {
      var query = $('#employee_id').val();
      var form_data = new FormData();

      form_data.append('query', query);
      var ajax_request = new XMLHttpRequest();

      ajax_request.open('POST', '/fetch_issue');

      ajax_request.send(form_data);

      ajax_request.onreadystatechange = function()
      {
        if(ajax_request.readyState == 4 && ajax_request.status == 200)
        {
          var response = JSON.parse(ajax_request.responseText);

          var html = '';

          var serial_no = 1;

          if(response.data.length > 0)
          {
            for(var count = 0; count < response.data.length; count++)
            {
              html += '<tr>';
              html += '<td><center>'+serial_no+'</button></td>';
              html += '<td>'+response.data[count].product_id+'</td>';
              html += '<td style="text-align: right;">'+response.data[count].unit_price+'</td>';
              html += '<td><center>'+response.data[count].qty+'</button></td>';
              html += '<td style="text-align: right;">'+response.data[count].total+'</td>';
              html += '<td><center><input type="hidden" class="form-control" name="delete_id" value="'+response.data[count].id+'"><button class="btn btn-sm btn-outline-danger" name="remove_product" type="submit"><i class="fa fa-trash"></i></button><input type="hidden" class="form-control" id="row_id" name="row_id[]" value="'+response.data[count].id+'"></center></td>';          
              html += '</tr>';
              serial_no++;
            }
          }
          else
          {
            html += '<tr><td colspan="6" class="text-center">Add Product</td></tr>';
          }

          document.getElementById('post_data').innerHTML = html;
          document.getElementById('total_data_table').innerHTML = response.total_data_table;
          document.getElementById('total_amount').value = response.total_data;
          document.getElementById('employee_id1').value = response.employee_id1;
        }
      }
    }

     $('#employee_id').change(function(){
     var query = $('#employee_id').val();
       load_data(query);
     });     
    

  });


  $(document).on('change','#employee_id', function(){
    var employee_id=$(this).val();        
    if (employee_id == ''){
      location.href = "/inventory/issue_product"; 
    }else{
      location.href = "/inventory/issue_product/"+employee_id;
    }  
  });


  // ajax script for getting state data
 $(document).on('change','#product', function(){
    var productID = $(this).val();
    if(productID){
      $.ajax({
        type:'POST',
        url:'/product_price',
        data:{'product_id':productID,'request':1},
        success:function(result){
            $('#unit_price').html(result);               
        }
      });
         
    }else{
      $('#unit_price').html('<option value="">Select Price</option>');          
    }
  });  


 $(document).on('change','#product', function(){
    var productID = $(this).val();
    if(productID){
      $.ajax({
        type:'POST',
        url:'/product_price',
        data:{'product_id':productID,'request':2},
        success:function(result){
          /*$('#total_data1').html(result); */

          document.getElementById('aval_stock').value = result; 
           
        }
      });
         
    }else{
      document.getElementById('aval_stock').value = '';          
    }
  }); 

</script>