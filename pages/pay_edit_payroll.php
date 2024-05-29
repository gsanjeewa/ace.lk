<?php
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
require_once 'system_permissions.php';
$connect = pdoConnection();

if (checkPermissions($_SESSION["user_id"], 45) == "false") {

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
        $sql2="SELECT * from roles where role_id=:eid";
        $query2 = $connect -> prepare($sql2);
        $query2-> bindParam(':eid', $eid, PDO::PARAM_STR);
        $query2->execute();
        $results=$query2->fetchAll(PDO::FETCH_OBJ);
        if($query2->rowCount() > 0)
        {
            foreach($results as $row)
            {
                $_SESSION['editbid']=$row->role_id;        
                ?>

                <form action="" class="form-sample" method="post" enctype="multipart/form-data" id="add_bank_form">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label class="col-sm-12 pl-0 pr-0">Role Name</label>
                            <div class="col-sm-12 pl-0 pr-0">
                                <input type="text" name="role_name" id="role_name" class="form-control text-uppercase" value="<?php  echo $row->role_name;?>" />
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" name="insert" class="btn btn-primary btn-fw mr-2" style="float: left;">Update</button>
                </form>
                <?php 
            }
        }
    }else{
        ?>
        <form action="" method="post" enctype="multipart/form-data" id="add_bank_form">
                    <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="start_date" class="control-label">Start Date</label>
                        <div class="input-group date" id="reservationstartdate" data-target-input="nearest">
                            <input type="text" name="start_date" id="start_date" class="form-control datetimepicker-input" data-target="#reservationstartdate" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm-dd" data-mask/>
                            <div class="input-group-append" data-target="#reservationstartdate" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                            </div>
                          </div>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="end_date" class="control-label">End Date</label>
                        <div class="input-group date" id="reservationenddate" data-target-input="nearest">
                            <input type="text" name="end_date" id="end_date" class="form-control datetimepicker-input" data-target="#reservationenddate" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm-dd" data-mask/>
                            <div class="input-group-append" data-target="#reservationenddate" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
                            </div>
                          </div>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="no_of_shifts">Payroll Type</label>
                        <select class="form-control select2" style="width: 100%;" name="type_id" id="type_id">
                          <option value="1">Monthly</option>
                          <option value="2">Semi-Monthly</option>                          
                        </select>
                      </div> 
                    </div>
                  </div>  
                   
                   
                    <button type="submit" name="add_new" class="btn btn-primary btn-fw mr-2" style="float: left;">Add</button>
                </form>

        <?php
    }
         ?>

</div>

<script>
$(function () {
  
  $('#add_bank_form').validate({
    rules: {
      start_date: {required: true},
      end_date: {required: true}     
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

<script>
  $(function () {
    //Initialize Select2 Elements
    

    $('#reservationstartdate').datetimepicker({
        format: 'YYYY-MM-DD'
    });

     

    $('#reservationenddate').datetimepicker({
        format: 'YYYY-MM-DD'
    });   
    


  })
</script>