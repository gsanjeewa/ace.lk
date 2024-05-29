<?php
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
require_once 'system_permissions.php';
$connect = pdoConnection();

if (checkPermissions($_SESSION["user_id"], 84) == "false") {

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
        $sql2="SELECT * from bank_name where id=:eid";
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

                <form action="" class="form-sample" method="post" enctype="multipart/form-data" id="add_bank_form">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label class="col-sm-12 pl-0 pr-0">Bank Name</label>
                            <div class="col-sm-12 pl-0 pr-0">
                                <input type="text" name="bank_name" id="bank_name" class="form-control text-uppercase" value="<?php  echo $row->bank_name;?>" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label class="col-sm-12 pl-0 pr-0">Bank Code</label>
                            <div class="col-sm-12 pl-0 pr-0">
                                <input type="text" name="bank_code" value="<?php  echo $row->bank_no;?>" class="form-control">
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
                        <div class="form-group col-md-12">
                            <label class="col-sm-12 pl-0 pr-0">Bank Name</label>
                            <div class="col-sm-12 pl-0 pr-0">
                                <input type="text" name="bank_name" id="bank_name" class="form-control text-uppercase"  />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label class="col-sm-12 pl-0 pr-0">Bank Code</label>
                            <div class="col-sm-12 pl-0 pr-0">
                                <input type="text" name="bank_code" id="bank_code" class="form-control">
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
      bank_name: { required: true},
      bank_code: {required: true, number: true}           
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