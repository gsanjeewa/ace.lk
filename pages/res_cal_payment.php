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
    header('location:/inventory/issue_product');
    exit();
  }

  $total=$_POST['qty']*$_POST['unit_price'];

  $data = array(
    ':employee_id'=>  $_POST['employee_id'],
    ':ref_no'=>  $_POST['ref_no'],
    ':product_id' =>  $_POST['product'],
    ':qty'        =>  $_POST['qty'],
    ':unit_price' =>  $_POST['unit_price'],
    ':total' =>  $total,
  );
 
  $query = "
  INSERT INTO `inventory_issue`(`ref_no`,`employee_id`, `product_id`, `qty`, `unit_price`, `total`)  
  VALUES (:ref_no, :employee_id, :product_id, :qty, :unit_price, :total)
  ";
          
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

if (isset($_POST['update_save'])){

  if (checkPermissions($_SESSION["user_id"], 68) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
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

  $query = "DELETE FROM `inventory_issue` WHERE `id`=:id";
    
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
            <h1 class="m-0 text-dark">Resignation</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Resignation</li>
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
                          
              <form id="add_issue_form" method="post">
                <div class="card card-success">
                  <div class="card-header">
                    <h3 class="card-title">Calculate</h3>                
                  </div>
                    <!-- /.card-header -->
                  <div class="card-body">                                      

                  <div class="row">
                    <div class="col-md-6">
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label for="employee_id">Employee</label>
                            <select class="form-control select2" style="width: 100%;" name="employee_id" id="employee_id">
                              <option value="">Select Employee</option>
                              <?php
                              $query="SELECT e.initial, e.surname, p.position_abbreviation, j.employee_no, j.join_id FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid INNER JOIN promotions c ON j.join_id=c.employee_id INNER JOIN (SELECT employee_id, MAX(id) maxid_pro FROM promotions GROUP BY employee_id) d ON c.employee_id = d.employee_id AND c.id = d.maxid_pro INNER JOIN position p ON c.position_id=p.position_id WHERE j.employee_status NOT BETWEEN 3 AND 4 ORDER BY j.employee_no DESC";
                              $statement = $connect->prepare($query);
                              $statement->execute();
                              $result = $statement->fetchAll();
                              foreach($result as $row)
                              {
                                ?>
                                <option value="<?php echo $row['join_id']; ?>"><?php echo $row['employee_no'].' '.$row['position_abbreviation'].' '.$row['surname'].' '.$row['initial']; ?></option>
                                <?php
                              }
                              ?>
                            </select>
                          </div>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label for="last_payroll">Last Payroll</label>
                            <input type="text" class="form-control" id="last_payroll" name="last_payroll" onblur="getAmount(this.value)" readonly>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-group">
                            <label for="deduction">Deduction</label>
                            <input type="text" class="form-control" id="deduction" name="deduction" onkeyup="getAmount(this.value)" readonly>
                          </div>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label for="doc_chargers">Document Chargers</label>
                            <input type="text" class="form-control" id="doc_chargers" name="doc_chargers" onkeyup="getAmount(this.value)">
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-group">
                            <label for="total_deduction">Total Deduction</label>
                            <input type="text" class="form-control" id="total_deduction" name="total_deduction" readonly>
                          </div>
                        </div>

                      </div>

                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label for="paid_amount">Paid Amount</label>
                            <input type="text" class="form-control" id="paid_amount" name="paid_amount" onkeyup="getAmount(this.value)">
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-group">
                            <label for="reson">Reson</label>
                            <input type="text" class="form-control" id="reson" name="reson">
                          </div>
                        </div>
                      </div>
                      
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label for="net">Net</label>
                            <input type="text" class="form-control" id="net" name="net" readonly>
                          </div>
                        </div>
                      </div>

                      

                      <!-- <div class="form-group">
                        <label for="gross">Gross</label>
                        <input type="text" class="form-control" id="gross" name="gross" onkeyup="getAmount(this.value)" readonly>
                      </div> -->

                    </div>
                  
                    <div class="col-md-6">
                      <!-- <label for="gceol">Product Details</label> -->
                      <table class="table table-bordered table-striped table-sm">
                        <thead style="text-align: center;">
                          <tr>
                            <!-- <th style="width: 5%;">#</th>
                            <th style="width: 10%;">Last Payroll</th>
                            <th style="width: 10%;">Gross</th> -->
                            <th style="width: 30%;">Deduction</th>
                            <!-- <th style="width: 10%;">Total Deduction</th>
                            <th style="width: 10%;">Net</th> -->
                          </tr>
                        </thead>
                        <tbody id="post_data">  
                        <tr><td colspan="10" class="text-center">Select Employee</td></tr>
                      </tbody>                        
                      </table>

                    </div>
                  </div>               
                  
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <div class="form-group">
                    <button class="btn btn-sm btn-outline-success" name="calculate_payroll" id="calculate_payroll" type="submit" data-toggle="tooltip" data-placement="top" title="Calculate"><i class="fas fa-calculator"></i> Calculate</button>
                  </div>
                 <!-- <button class="btn btn-sm btn-primary col-sm-2 offset-md-5" name="update_save"> Issue</button> -->
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

$(function () {

  $('#add_issue_form').validate({
    rules: {
      employee_id: { required: true},
      doc_chargers: { required: true, number:true},         
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
  
  function load_data(query = '', )
{
  var query = $('#employee_id').val();
   
  $.ajax({
    url:"/fetch_cal_pay",
    method:"POST",
    data:{query:query},
    dataType: 'json',

    success:function(response)
    {
      var html='';
      var serial_no = 1;
      var last_payroll='';
      var deduction='';
      if(response.length > 0)
      {
        var last_payroll = response[0]['last_payroll'];
        var deduction = response[0]['total_deduction'];
        
        for(var count = 0; count < response.length; count++)
        {
          html += '<tr>';
          /*html += '<td><center>'+serial_no+'</button></td>';
          html += '<td style="text-align: right;">'+response[count].last_payroll+'</td>';
          html += '<td style="text-align: right;">'+response[count].gross+'</td>';*/
          html += '<td style="text-align: right;">'+response[count].ded_details+'</td>';
          /*html += '<td style="text-align: right;">'+response[count].total_deduction+'</td>';
          html += '<td style="text-align: right;">'+response[count].net+'</td>';
          html += '</tr>';
          serial_no++; */           
        }
      }
      else
      {
        html += '<tr><td colspan="6" class="text-center">Select Employee</td></tr>';
      }
      document.getElementById('post_data').innerHTML = html;
      document.getElementById('last_payroll').value = (Math.round(last_payroll * 100) / 100).toFixed(2);
      document.getElementById('deduction').value = (Math.round(deduction * 100) / 100).toFixed(2);
    }
  });  
}
    

     $('#employee_id').change(function(){
     var query = $('#employee_id').val();
       load_data(1, query);
     });
    

    $('#add_issue_form').on('submit', function(event){
   event.preventDefault();   
    $.ajax({
     url:"/cal_process",
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
    $('#add_issue_form')[0].reset();
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

  function getAmount(value){    
    var doc_chargers = (Math.round($('#doc_chargers').val()* 100) / 100).toFixed(2);
    var paid_amount = (Math.round($('#paid_amount').val()* 100) / 100).toFixed(2);
    var last_payroll = (Math.round($('#last_payroll').val()* 100) / 100).toFixed(2);
    var deduction = (Math.round($('#deduction').val()* 100) / 100).toFixed(2);
    var total_deduction = parseFloat(doc_chargers)+parseFloat(deduction);
    var net = parseFloat(last_payroll)-parseFloat(total_deduction)+parseFloat(paid_amount);

    document.getElementById('total_deduction').value = (Math.round(total_deduction * 100) / 100).toFixed(2);
    document.getElementById('net').value = (Math.round(net * 100) / 100).toFixed(2);
  }

</script>