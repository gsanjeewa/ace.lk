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
    if(isset($_POST['edit_id4']))
    {
          $_SESSION['empid']=$_POST['edit_id4'];        
                ?>

                <form action="" method="post" enctype="multipart/form-data" id="add_bank_form">
                  <div class="form-group">
                <label for="holder_name">Holder Name:</label>
                <input type="text" class="form-control text-uppercase" id="holder_name" name="holder_name">                        
              </div> 

            <div class="form-group">
                <label for="bank_name">Bank Name:</label>
                <select class="form-control select2" style="width: 100%;" id="bank_name" name="bank_name">
                  <option value="">Select Bank</option>
                  <?php
                  $query="SELECT * FROM bank_name ORDER BY bank_no ASC";
                  $statement = $connect->prepare($query);
                  $statement->execute();
                  $result = $statement->fetchAll();
                  foreach($result as $row_bank)
                  {
                    ?>
                    <option value="<?php echo $row_bank['id'];?>"><?php echo $row_bank['bank_name'].' ('.$row_bank['bank_no'].')'; ?></option>
                    <?php
                  }
                  ?>
                </select>                        
              </div>

            <div class="form-group">
                <label for="bank_branch">Branch (ශාඛාව):</label>
                <select class="form-control select2" style="width: 100%;" id="bank_branch" name="bank_branch">
                  <option value="">Select Branch</option>                          
                </select>                        
              </div>              
              <div class="form-group">
                <label for="account_no">Account No:</label>
                <input type="text" class="form-control" id="account_no" name="account_no">
              </div>
                   
                    <button type="submit" name="add_new" class="btn btn-primary btn-fw mr-2" style="float: left;">Save</button>
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
      bank_name: { required: true},
      bank_branch: { required: true},
      account_no: { required: true}
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