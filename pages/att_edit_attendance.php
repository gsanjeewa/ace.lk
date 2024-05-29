<?php
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
require_once 'system_permissions.php';
$connect = pdoConnection();

if (checkPermissions($_SESSION["user_id"], 26) == "false") {

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
        $sql2="SELECT * from attendance where id=:eid";
        $query2 = $connect -> prepare($sql2);
        $query2-> bindParam(':eid', $eid, PDO::PARAM_STR);
        $query2->execute();
        $results=$query2->fetchAll(PDO::FETCH_OBJ);
        if($query2->rowCount() > 0)
        {
            foreach($results as $row)
            {
                $_SESSION['attendanceeditbid']=$row->id;        
                ?>

                <form action="" class="form-sample" method="post" enctype="multipart/form-data" id="add_bank_form">
                    
                    <div class="form-group">
                            <label for="">Position Name</label>
                          </div>
                          <div class="row">
                       
                           <?php
                              $query="SELECT * FROM position ORDER BY position_id";
                              $statement = $connect->prepare($query);
                              $statement->execute();
                              $result = $statement->fetchAll();
                              foreach($result as $row_position_id)
                              {
                                ?><div class="col-md-3">
                                <div class="form-group clearfix">
                                    <div class="icheck-success d-inline">
                                      <input type="radio" id="radioPrimary<?php echo $row_position_id['position_id']; ?>" name="position_id" value="<?php echo $row_position_id['position_id']; ?>" <?php if($row->position_id==$row_position_id['position_id']) {echo "checked";}?>>
                                      <label for="radioPrimary<?php echo $row_position_id['position_id']; ?>"><?php echo $row_position_id['position_abbreviation']; ?>
                                      </label>
                                    </div>
                                  </div>     
                                  </div>
                                  
                                <?php
                              }
                              ?>
                          </div>
                          <div class="form-group">
                        <label for="no_of_shifts">No of Shifts</label>
                        <input type="text" class="form-control" id="no_of_shifts" name="no_of_shifts" autocomplete="off" value="<?php echo $row->no_of_shifts;?>">
                      </div>

                      <div class="form-group">
                        <label for="extra_ot_hrs">Extra OT Hrs</label>
                        <input type="text" class="form-control" id="extra_ot_hrs" name="extra_ot_hrs" autocomplete="off" value="<?php echo $row->extra_ot_hrs;?>">
                      </div>
                    
                    <button type="submit" name="update" class="btn btn-primary btn-fw mr-2" style="float: left;">Update</button>
                </form>
                <?php 
            }
        }
    }
         ?>

</div>

<script>
$(function () {
  
  $('#add_bank_form').validate({
    rules: {
      no_of_shifts: { required: true}      
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