<?php
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
require_once 'system_permissions.php';
$connect = pdoConnection();

if (checkPermissions($_SESSION["user_id"], 45) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>'; 
    header('location:/users/role');   
    exit();
}

?>
<form action="" id="" method="post">
<div class="modal-body" style="max-height: 400px; overflow-y: auto;">
    
    <div class="row">

    <?php
    if (isset($_POST['edit_per_id'])) {
      $eid = $_POST['edit_per_id'];
      
      // Fetch permissions linked to the role
      $query = "SELECT permission_id, role_id, ref_id FROM system_permission_to_roles WHERE role_id=:eid";
      $statement = $connect->prepare($query);
      $statement->bindParam(':eid', $eid, PDO::PARAM_STR);
      $statement->execute();
      $result = $statement->fetchAll();
      $checked_arr = array();
      $check_id = array();
      foreach ($result as $row_per) {
          $checked_arr[] = $row_per['permission_id'];
          $check_id[] = $row_per['ref_id'];
      }
  
      // Fetch all permissions
      $query = "SELECT * FROM system_permissions WHERE status=0 ORDER BY permission_group ASC";
      $statement = $connect->prepare($query);
      $statement->execute();
      $result = $statement->fetchAll();
  
      // Organize permissions by group
      $permissions_by_group = array();
      foreach ($result as $row_perm) {
          $permissions_by_group[$row_perm['permission_group']][] = $row_perm;
      }
  
      // Generate HTML for each permission group
      foreach ($permissions_by_group as $group => $permissions) {
        ?>
        
          <div class="col-md-12"><h4><b><?php
          echo $group?></b></h4></div>
       <?php
          foreach ($permissions as $permission) {
              $per_id = $permission['permission_id'];
              $per_name = $permission['permission_name'];
              $checked = in_array($per_id, $checked_arr);
              $ref_id = $checked ? $check_id[array_search($per_id, $checked_arr)] : "";
              ?>
              <input type="hidden" name="ref_id[]" value="<?php echo $ref_id; ?>">
              <div class="col-md-3">
                  <div class="form-group clearfix">
                      <div class="icheck-primary d-inline">
                          <input type="checkbox" id="permissions<?php echo $per_id; ?>" name="permissions[]" value="<?php echo $per_id; ?>" <?php echo $checked ? 'checked' : ''; ?>>
                          <label for="permissions<?php echo $per_id; ?>"><?php echo $per_name; ?></label>
                      </div>
                  </div>
              </div>
              <?php
          }
          ?>        
          
          <?php
      }
  }
  
    ?>
          <input type="hidden" name="role_id" value="<?php echo $_POST['edit_per_id']; ?>">
        

        
</div>
</div>
<div class="modal-footer ">
<button type="submit" name="update_save" class="btn btn-primary btn-fw mr-2" style="float: left;">Update</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          </div>

</form>


<script>
$(function () {
  
  $('#add_bank_form').validate({
    rules: {
      role_name: { required: true}      
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

});
</script>