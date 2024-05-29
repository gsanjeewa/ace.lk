<?php
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
require_once 'system_permissions.php';
$connect = pdoConnection();

if ((checkPermissions($_SESSION["user_id"], 87) == "false") OR (checkPermissions($_SESSION["user_id"], 88) == "false")) {

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
        $sql2="SELECT * from shifts_emp_allowance where id=:eid";
        $query2 = $connect -> prepare($sql2);
        $query2-> bindParam(':eid', $eid, PDO::PARAM_STR);
        $query2->execute();
        $results=$query2->fetchAll(PDO::FETCH_OBJ);
        if($query2->rowCount() > 0)
        {
            foreach($results as $row)
            {
                $_SESSION['editbid']=$row->id;
                $query="SELECT j.join_id, e.surname, e.initial, j.employee_no, p.position_abbreviation FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid INNER JOIN position p ON e.position_id=p.position_id WHERE j.join_id='".$row->employee_id."'";
                                $statement = $connect->prepare($query);
                                $statement->execute();
                                $result = $statement->fetchAll();
                                foreach($result as $row_emp)
                                {
                                  $employee_name = $row_emp['employee_no'].' '.$row_emp['position_abbreviation'].' '.$row_emp['surname'].' '.$row_emp['initial'];                                  
                                }

                ?>

                <form action="" class="form-sample" method="post" enctype="multipart/form-data" id="add_bank_form">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label class="col-sm-12 pl-0 pr-0">Employee</label>
                            <div class="col-sm-12 pl-0 pr-0">
                                <input type="text" name="" value="<?php echo $employee_name; ?>" class="form-control" readonly> 
                              
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group clearfix col-md-12">
                            <div class="icheck-primary d-inline">
                              <input type="radio" id="any_shifts" name="shifts_selection" value="1" <?php if ($row->department_id==0) { echo "checked";} ?>>
                              <label for="any_shifts">Any Shifts
                              </label>
                            </div>
                            <div class="icheck-primary d-inline">
                              <input type="radio" id="only_one" name="shifts_selection" value="2" <?php if ($row->department_id!=0) { echo "checked";} ?>>
                              <label for="only_one">Only one
                              </label>
                            </div>                                       
                          </div>

                    </div>

                    <div class="row">
                        <div <?php if ($row->department_id!=0) { echo 'style="display: block"';}else{ echo 'style="display: none"';} ?> id="only_one_field" class="col-md-12">
                          <div class="form-group">
                            <label class="col-sm-12 pl-0 pr-0">Position</label>
                            <div class="col-sm-12 pl-0 pr-0">
                                <select class="form-control select2" style="width: 100%;" name="position_id" id="position_id">
                                    <option value="">Select Position</option>
                                    <?php
                                    $query="SELECT * FROM position ORDER BY priority ASC";
                                    $statement = $connect->prepare($query);
                                    $statement->execute();
                                    $result = $statement->fetchAll();
                                    foreach($result as $row_position)
                                    {
                                      ?>
                                      <option value="<?php echo $row_position['position_id']; ?>"<?php if ($row_position['position_id']==$row->position_id){ echo "SELECTED";}?>><?php echo $row_position['position_abbreviation']; ?></option>
                                      <?php
                                    }
                                    ?>
                                </select>                                
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-12 pl-0 pr-0">Institution Name</label>
                            <div class="col-sm-12 pl-0 pr-0">
                                <select class="form-control select2" style="width: 100%;" name="department_id" id="department_id">
                                    <option value="">Select Institution</option>
                                    <?php
                                    $query="SELECT * FROM department ORDER BY department_id";
                                    $statement = $connect->prepare($query);
                                    $statement->execute();
                                    $result = $statement->fetchAll();
                                    foreach($result as $row_department)
                                    {
                                      ?>
                                      <option value="<?php echo $row_department['department_id']; ?>"<?php if ($row_department['department_id']==$row->department_id){ echo "SELECTED";}?>><?php echo $row_department['department_name'].'-'.$row_department['department_location']; ?></option>
                                      <?php
                                    }
                                    ?>
                                </select>                                
                            </div>
                        </div>

                        </div>
                        
                    </div>
                                        
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label class="col-sm-12 pl-0 pr-0">Shifts Allowance</label>
                            <div class="col-sm-12 pl-0 pr-0">
                                <input type="text" name="allowance" value="<?php  echo $row->allowance;?>" class="form-control" id="allowance">
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
                    <label class="col-sm-12 pl-0 pr-0">Employee</label>
                    <div class="col-sm-12 pl-0 pr-0">
                        <select class="form-control select2" style="width: 100%;" name="employee_id" id="employee_id">
                            <option value="">Select Employee</option>
                        <?php
                        $query="SELECT j.join_id, e.surname, e.initial, j.employee_no, p.position_abbreviation FROM employee e INNER JOIN join_status j ON e.employee_id = j.employee_id INNER JOIN (SELECT employee_id, MAX(join_id) maxid FROM join_status GROUP BY employee_id) b ON j.employee_id = b.employee_id AND j.join_id = b.maxid INNER JOIN position p ON e.position_id=p.position_id ORDER BY e.employee_id DESC";
                        $statement = $connect->prepare($query);
                        $statement->execute();
                        $result = $statement->fetchAll();
                        foreach($result as $row_emp)
                        {
                          ?>
                          <option value="<?php echo $row_emp['join_id'];?>"><?php echo $row_emp['employee_no'].' '.$row_emp['position_abbreviation'].' '.$row_emp['surname'].' '.$row_emp['initial']; ?></option>
                          <?php
                        }
                        ?>
                      </select>                               
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group clearfix col-md-12">
                    <div class="icheck-primary d-inline">
                      <input type="radio" id="any_shifts" name="shifts_selection" value="1" checked>
                      <label for="any_shifts">Any Shifts
                      </label>
                    </div>
                    <div class="icheck-primary d-inline">
                      <input type="radio" id="only_one" name="shifts_selection" value="2">
                      <label for="only_one">Only one
                      </label>
                    </div>                                       
                  </div>

            </div>

            <div class="row">
                <div style="display: none" id="only_one_field" class="col-md-12">
                  <div class="form-group">
                    <label class="col-sm-12 pl-0 pr-0">Position</label>
                    <div class="col-sm-12 pl-0 pr-0">
                        <select class="form-control select2" style="width: 100%;" name="position_id" id="position_id">
                            <option value="">Select Position</option>
                            <?php
                            $query="SELECT * FROM position ORDER BY priority ASC";
                            $statement = $connect->prepare($query);
                            $statement->execute();
                            $result = $statement->fetchAll();
                            foreach($result as $row_position)
                            {
                              ?>
                              <option value="<?php echo $row_position['position_id']; ?>"><?php echo $row_position['position_abbreviation']; ?></option>
                              <?php
                            }
                            ?>
                        </select>                                
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-12 pl-0 pr-0">Institution Name</label>
                    <div class="col-sm-12 pl-0 pr-0">
                        <select class="form-control select2" style="width: 100%;" name="department_id" id="department_id">
                            <option value="">Select Institution</option>
                            <?php
                            $query="SELECT * FROM department ORDER BY department_id";
                            $statement = $connect->prepare($query);
                            $statement->execute();
                            $result = $statement->fetchAll();
                            foreach($result as $row_department)
                            {
                              ?>
                              <option value="<?php echo $row_department['department_id']; ?>"><?php echo $row_department['department_name'].'-'.$row_department['department_location']; ?></option>
                              <?php
                            }
                            ?>
                        </select>                                
                    </div>
                </div>

                </div>
                
            </div>
            
            <div class="row">
                <div class="form-group col-md-12">
                    <label class="col-sm-12 pl-0 pr-0">Shifts Allowance</label>
                    <div class="col-sm-12 pl-0 pr-0">
                        <input type="text" name="allowance" class="form-control" id="allowance">
                    </div>
                </div>
            </div>
            
            <button type="submit" name="add_new" class="btn btn-primary btn-fw mr-2" style="float: left;">Add</button>
        </form>

        <?php
    }
         ?>

</div>

<script type="text/javascript">

  $(function () {
    $("input[name='shifts_selection']").click(function () {
      $("#position_id select, #department_id select").val('empty');      
      if ($("#only_one").is(":checked")) {
          $("#only_one_field").show();
          $('#position_id').attr('required','');          
          $('#position_id').attr('data-error', 'This field is required.');
          $('#position_id').val(null).trigger('change.select2');
          $('#department_id').attr('required','');          
          $('#department_id').attr('data-error', 'This field is required.');
          $('#department_id').val(null).trigger('change.select2');            
      } else {
          $("#only_one_field").hide();
          $('#position_id').removeAttr('required');         
          $('#position_id').removeAttr('data-error');
          $('#position_id').val(null).trigger('change.select2');
          $('#department_id').removeAttr('required');         
          $('#department_id').removeAttr('data-error');
          $('#department_id').val(null).trigger('change.select2');          
      }
        
    }); 
      
  });
    
</script>

<script>
$(function () {
  
  $('#add_bank_form').validate({
    rules: {
      allowance: { required: true, number: true},
      employee_id: { required: true},      
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

  $('.select2').select2()

});
</script>