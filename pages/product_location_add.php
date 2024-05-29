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
        $eid=$_POST['edit_id4'];
        $sql2="SELECT * from inventory_location where id=:eid AND status=0";
        $query2 = $connect -> prepare($sql2);
        $query2-> bindParam(':eid', $eid, PDO::PARAM_STR);
        $query2->execute();
        $results=$query2->fetchAll(PDO::FETCH_OBJ);
        if($query2->rowCount() > 0)
        {
            foreach($results as $row)
            {
                $_SESSION['editbid']=$row->id;        
                ?>

                <form action="" method="post" enctype="multipart/form-data" id="formattendance">
                   
                  <div class="form-group">
                    <label for="no_of_shifts">Location</label>
                    <input type="text" name="location" id="location" class="form-control text-uppercase" value="<?php echo $row->location; ?>" />                        
                  </div> 

                  <button type="submit" name="update_save" class="btn btn-primary btn-fw mr-2" style="float: left;">Update</button>
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </form>
                <?php 
            }
        }
    }else{
        ?> 

    <form action="" method="post" enctype="multipart/form-data" id="formattendance">
                           
          <div class="form-group">
            <label for="location" class="control-label">Location</label>
            <input type="text" name="location" id="location" class="form-control text-uppercase" />                            
          </div>
                      
      

       
        <button type="submit" name="add_save" class="btn btn-primary btn-fw mr-2" style="float: left;">Add</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
    </form>
<?php
} 
?>
</div>

<script>
$(function () {
  
  $('#formattendance').validate({
    rules: {      
      location: {required: true}             
    },

    messages: {      
      
      employee_no: {
        remote: 'Employee No Already existing!'
      },

      nic_new: {
        remote: 'NIC No Already existing!'
      }, 

      nic_old: {
        remote: 'NIC No Already existing!'
      }, 
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
 

$('[data-mask]').inputmask()
$('.select2').select2()
$('#reservationstartdate').datetimepicker({
  format: 'YYYY-MM-DD'
});
$('#reservationenddate').datetimepicker({
    format: 'YYYY-MM-DD'
  });
});

</script>