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

    <form action="" method="post" enctype="multipart/form-data" id="add_emp_form">
        <div class="row">
          <div class="col-md-4">                        
            <label for="nic_no">1. NIC No (ජා: හැ: අංකය):<span class="text-danger">*</span></label>
            <div class="form-group clearfix">
            <div class="icheck-primary d-inline">
              <input type="radio" id="nic_no_new" name="nic_no_selection" value="new" checked>
              <label for="nic_no_new">New NIC
              </label>
            </div>
            <div class="icheck-primary d-inline">
              <input type="radio" id="nic_no_old" name="nic_no_selection" value="Old">
              <label for="nic_no_old">Old NIC
              </label>
            </div>
            <div class="icheck-primary d-inline">
              <input type="radio" id="no_nic" name="nic_no_selection" value="no">
              <label for="no_nic">No NIC
              </label>
            </div>                     
          </div>
          <div class="form-group">
            <div class="form-group" id="nic_no_new_field">
              <input type="text" class="form-control" id="nic_new" name="nic_new" autocomplete="off" data-inputmask='"mask": "999999999999"' data-mask autofocus>
            </div>

            <div class="form-group" style="display: none" id="nic_no_old_field">
              <input type="text" class="form-control text-uppercase" id="nic_old" name="nic_old" autocomplete="off" data-inputmask='"mask": "999999999*"' data-mask autofocus>
            </div>

            <!-- <div class="form-group" style="display: none" id="nic_no_old_field">
              <input type="text" class="form-control" id="nic_old" name="nic_old" data-inputmask='"mask": "999999999 V"' data-mask autocomplete="off">
            </div>  -->                         
              
            </div>
          </div>                      

          <div class="col-md-3">
            <div class="form-group">
          <label for="employee_no">Employee No:<span class="text-danger">*</span></label>
          <div class="form-group clearfix">
            <div class="icheck-primary d-inline">
              <input type="radio" id="emp_no" name="emp_no_selection" value="emp" checked>
              <label for="emp_no">Emp No
              </label>
            </div>
            <div class="icheck-primary d-inline">
              <input type="radio" id="temp_no" name="emp_no_selection" value="temp">
              <label for="temp_no">Temp No
              </label>
            </div>                                             
          </div>

          
        </div>

        <?php 
          $query_no="SELECT employee_no FROM join_status WHERE employee_no REGEXP '^-?[0-9]+$' ORDER BY ABS(employee_no) DESC LIMIT 1";
          $statement = $connect->prepare($query_no);
          $statement->execute();
          $result = $statement->fetchAll();
          foreach($result as $row_no)
          {
            $new_employee_no=$row_no['employee_no']+1;                      
          }
          ?>

          

        <div class="form-group" id="emp_no_field">
              <input type="text" class="form-control" id="employee_no" name="employee_no" value="<?php echo $new_employee_no; ?>">
            </div>
            <div class="form-group" id="temp_no_field" style="display: none" >
              <input type="text" class="form-control text-uppercase" id="temporary_no" name="temporary_no">
            </div>
          </div>          

          <div class="col-md-5">
            <div class="form-group">
              <label for="">Rank<span class="text-danger">*</span></label>
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
          </div>

        </div>

        <div class="row">                     

          <div class="col-md-4">
            <div class="form-group">
              <label for="surname">Surname (වාසගම):<span class="text-danger">*</span></label>
              <input type="text" class="form-control text-uppercase" id="surname" name="surname">
            </div>
          </div>                    

          <div class="col-md-2">
            <div class="form-group">
              <label for="initial">Initials (මුලකරු):<span class="text-danger">*</span></label>
              <input type="text" class="form-control text-uppercase" id="initial" name="initial">
            </div>
          </div>
        
        <div class="col-md-3">
          <div class="form-group">
            <label for="join_date">Join Date:</label>
            <div class="input-group date" id="reservationjoindate" data-target-input="nearest">
              <input type="text" name="join_date" id="join_date" class="form-control datetimepicker-input" data-target="#reservationjoindate" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy-mm-dd" data-mask value="<?php echo date("Y-m-d"); ?>"/>
              <div class="input-group-append" data-target="#reservationjoindate" data-toggle="datetimepicker">
                  <div class="input-group-text"><i class="fa fa-calendar-alt"></i></div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-md-3">
          <div class="form-group">
            <label for="basic_salary">Basic Salary (මුලික වැටුප):</label>
            <input type="text" class="form-control" id="basic_salary" name="basic_salary">
          </div>
        </div>
        </div>
          <div class="row">              
            <div class="col-md-6">
          <div class="form-group">
            <label for="department_id">Location (රාජකාරි ස්ථානය):</label>
            <select class="form-control select2" style="width: 100%;" name="department_id" id="department_id">
              <option value="">Select Location</option>
              <?php
              $query="SELECT * FROM department ORDER BY department_id";
              $statement = $connect->prepare($query);
              $statement->execute();
              $result = $statement->fetchAll();
              foreach($result as $row)
              {
                ?>
                <option value="<?php echo $row['department_id']; ?>"><?php echo $row['department_name']; ?></option>
                <?php
              }
              ?>
            </select>
          </div>
        </div>
          </div>

       
      
       
        <button type="submit" name="add_emp" class="btn btn-primary btn-fw mr-2" style="float: left;">Add</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
    </form>

</div>

<script>
$(function () {
  
  $('#add_emp_form').validate({
    rules: {     
      surname: {required: true},
      initial: {required: true},
      nic_new: {required: true, 
        remote: {
          url: "/check_nic_no",
          type: "post"
          }},
      nic_old: {required: true,
      remote: {
          url: "/check_nic_old",
          type: "post"
          }
        },
      position_id: {required: true},      
      employee_no: {required: true, 
        remote: {
          url: "/check_employee_no",
          type: "post"
          }
        }      
      
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


  $("input[name='nic_no_selection']").click(function () {
      if ($("#nic_no_new").is(":checked")) {
          $("#nic_no_new_field").show();
          $('#nic_new').attr('required','');
          $('#nic_new').attr('focus', true);
          $('#nic_new').attr('data-error', 'This field is required.');
          $('#nic_new').val('');            
      } else {
          $("#nic_no_new_field").hide();
          $('#nic_new').removeAttr('required');
          $('#nic_new').removeAttr('data-error');
          $('#nic_new').removeAttr('focus');
          $('#nic_new').val('')
      }
      if ($("#nic_no_old").is(":checked")) {
          $("#nic_no_old_field").show();
          $('#nic_old').attr('required','');
          $('#nic_old').attr('focus', true);
          $('#nic_old').attr('data-error', 'This field is required.');
          $('#nic_old').val('');            
      } else {
          $("#nic_no_old_field").hide();
          $('#nic_old').removeAttr('required');
          $('#nic_old').removeAttr('focus');
          $('#nic_old').removeAttr('data-error');
          $('#nic_old').val('');          
      }
        
    });

    $("input[name='emp_no_selection']").click(function () {
      if ($("#emp_no").is(":checked")) {
          $("#emp_no_field").show();
          $('#employee_no').attr('required','');
          $('#employee_no').attr('focus', true);
          $('#employee_no').attr('data-error', 'This field is required.');
          $('#employee_no').val('');
      } else {
          $("#emp_no_field").hide();
          $('#employee_no').removeAttr('required');
          $('#employee_no').removeAttr('data-error');
          $('#employee_no').removeAttr('focus');
          $('#employee_no').val('');
      }
      if ($("#temp_no").is(":checked")) {
          $("#temp_no_field").show();
          $('#temporary_no').attr('required','');
          $('#temporary_no').attr('focus', true);
          $('#temporary_no').attr('data-error', 'This field is required.');
          $('#temporary_no').val('');            
      } else {
          $("#temp_no_field").hide();
          $('#temporary_no').removeAttr('required');
          $('#temporary_no').removeAttr('focus');
          $('#temporary_no').removeAttr('data-error');
          $('#temporary_no').val('');          
      }
        
    });

$('[data-mask]').inputmask()
$('.select2').select2()
$('#reservationjoindate').datetimepicker({
        format: 'YYYY-MM-DD'
    });
});

</script>