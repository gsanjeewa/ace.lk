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
    if(isset($_POST['edit_pro_id']))
    {
          $_SESSION['empid']=$_POST['edit_pro_id'];        
                ?>

                <form action="" method="post" enctype="multipart/form-data" id="add_bank_form">
            <div class="form-group">
                <label for="bank_name">Promoted Rank:</label>
                <select class="form-control select2" style="width: 100%;" name="position_id" id="position_id">
                  <option value="">Select Rank</option>
                  <?php
                  $query="SELECT * FROM position ORDER BY position_id";
                  $statement = $connect->prepare($query);
                  $statement->execute();
                  $result = $statement->fetchAll();
                  foreach($result as $row)
                  {
                    ?>
                    <option value="<?php echo $row['position_id']; ?>"><?php echo $row['position_abbreviation']; ?></option>
                    <?php
                  }
                  ?>
                </select>                      
              </div>
              <div class="form-group">
                <label for="bank_branch">Promoted Date:</label>
                <div class="input-group date" id="reservationdate" data-target-input="nearest">
                  <input type="text" name="promoted_date" id="promoted_date" class="form-control datetimepicker-input" data-target="#reservationdate" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm-dd" data-mask value="<?php echo date("Y-m-d"); ?>" />
                  <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                  </div>
                </div>                       
              </div>

              <div class="form-group">
                <label for="promotion_pay">Promotion Pay:</label>
                <input type="text" name="promotion_pay" id="promotion_pay" class="form-control">
              </div>

              <div class="form-group">
                <label for="basic_salary">Basic Salary:</label>
                <input type="text" class="form-control" id="basic_salary" name="basic_salary">
              </div>
                   
                    <button type="submit" name="add_promote" class="btn btn-primary btn-fw mr-2" style="float: left;">Save</button>
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
      position_id: { required: true},
      promoted_date: { required: true, date:true}      
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

    $('#reservationdate').datetimepicker({
        format: 'YYYY-MM-DD'
    });

    $('.select2').select2()
  // ajax script for getting state data
   $(document).on('change','#bank_name', function(){
      var bank_nameID = $(this).val();
      if(bank_nameID){
          $.ajax({
              type:'POST',
              url:'/backend-script',
              data:{'bank_name_id':bank_nameID,'request':3},
              success:function(result){
                  $('#bank_branch').html(result);
                 
              }
          });          
      }else{
          $('#bank_branch').html('<option value="">First Select Bank</option>');          
      }
  }); 
   });

</script>