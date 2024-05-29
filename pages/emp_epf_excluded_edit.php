<?php
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
require_once 'system_permissions.php';
$connect = pdoConnection();

if (checkPermissions($_SESSION["user_id"], 1) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>'; 
    header('location:/settings/bank_list');   
    exit();
}

?>

<div class="card-body">
    <?php
    if(isset($_POST['edit_epf_id']))
    {
          $_SESSION['empid']=$_POST['edit_epf_id'];        
                ?>

                <form action="" method="post" enctype="multipart/form-data" id="add_bank_form">
            
              <div class="form-group">
                <label for="from_date">From Date:</label>
                <div class="input-group date" id="from_date" data-target-input="nearest">
                  <input type="text" name="from_date" id="from_date" class="form-control datetimepicker-input" data-target="#from_date" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm" data-mask value="<?php echo date("Y-m"); ?>" />
                  <div class="input-group-append" data-target="#from_date" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                  </div>
                </div>                       
              </div>

              <div class="form-group">
                <label for="to_date">To Date:</label>
                <div class="input-group date" id="to_date" data-target-input="nearest">
                  <input type="text" name="to_date" id="to_date" class="form-control datetimepicker-input" data-target="#to_date" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm" data-mask value="<?php echo date("Y-m"); ?>" />
                  <div class="input-group-append" data-target="#to_date" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                  </div>
                </div>                       
              </div>

              
                   
                    <button type="submit" name="add_epf" class="btn btn-primary btn-fw mr-2" style="float: left;">Save</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </form>
                <?php 
            
    }else{
      ?>

                <form action="" method="post" enctype="multipart/form-data" id="add_bank_form">

                  <div class="form-group">
                  <label for="employee_id">Employee</label>
                  <select class="form-control select2" style="width: 100%;" name="employee_id" id="employee_id">
                    <option value="">Select Employee</option>
                    <?php
                    $query="SELECT j.join_id, e.surname, e.initial, j.employee_no, p.position_abbreviation FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid INNER JOIN position p ON e.position_id=p.position_id ORDER BY e.employee_id DESC";
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
                
            
              <div class="form-group">
                <label for="from_date">From Date:</label>
                <div class="input-group date" id="from_date" data-target-input="nearest">
                  <input type="text" name="from_date" id="from_date" class="form-control datetimepicker-input" data-target="#from_date" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm" data-mask value="<?php echo date("Y-m"); ?>" />
                  <div class="input-group-append" data-target="#from_date" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                  </div>
                </div>                       
              </div>

              <div class="form-group">
                <label for="to_date">To Date:</label>
                <div class="input-group date" id="to_date" data-target-input="nearest">
                  <input type="text" name="to_date" id="to_date" class="form-control datetimepicker-input" data-target="#to_date" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm" data-mask value="<?php echo date("Y-m"); ?>" />
                  <div class="input-group-append" data-target="#to_date" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                  </div>
                </div>                       
              </div>

              
                   
                    <button type="submit" name="add_epf" class="btn btn-primary btn-fw mr-2" style="float: left;">Save</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </form>
                <?php 
    }
         ?>

</div>

<script>
$(function () {
  
  $('#add_bank_form').validate({
    rules: {
      from_date: { required: true, date:true},
      to_date: { required: true, date:true}      
    },

    messages: {      
      
       
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

    $('#from_date').datetimepicker({
        format: 'YYYY-MM'
    });

    $('#to_date').datetimepicker({
        format: 'YYYY-MM'
    });

    $('.select2').select2()
   
   });

</script>