
<?php
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();
require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 32) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}

if (isset($_POST['halt'])){
  $data = array(
    ':payroll_id' =>  $_POST['payroll_id'],
    ':employee_id' =>  $_POST['employee_id'],
    ':halt_reason' =>  $_POST['halt_reason'],
    ':status'   =>  2,                 
  );
 
  $query = "
  INSERT INTO payroll_halt(payroll_id, employee_id, reason, status) VALUES (:payroll_id, :employee_id, :halt_reason, :status);
  UPDATE payroll_items SET status=:status WHERE payroll_id=:payroll_id AND employee_id=:employee_id;
  ";
          
  $statement = $connect->prepare($query);
  if($statement->execute($data))
    {
      $errMSG = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';            
    }else{
        $errMSG = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
    }
}

if (isset($_POST['approved'])){
  $data = array(
      ':id' =>  $_POST['payroll_id'],
      ':status'   =>  1,                 
  );
 
  $query = "
  UPDATE payroll_items SET status=:status WHERE id=:id
  ";
          
  $statement = $connect->prepare($query);
  if($statement->execute($data))
    {
      $errMSG = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';            
    }else{
        $errMSG = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
    }
}

if (isset($_POST['re_approved'])){
  $data = array(
      ':id' =>  $_POST['payroll_id'],
      ':payroll_id' =>  $_GET['view'],
      ':employee_id' =>  $_POST['employee_id'],
      ':status'   =>  3,                 
  );
 
  $query = "
  UPDATE payroll_items SET status=:status WHERE id=:id;
  UPDATE payroll_halt SET status=:status WHERE payroll_id=:payroll_id AND employee_id=:employee_id;
  ";
          
  $statement = $connect->prepare($query);
  if($statement->execute($data))
    {
      $errMSG = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';            
    }else{
        $errMSG = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
    }
}

include '../inc/header.php';

?>

<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Payroll</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Payroll</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <?php
          if ( isset($errMSG) ) {
            ?>
            <div class="col-xl-12 col-md-6 mb-4">
              <?php echo $errMSG; ?>
            </div>
              <?php
          }
          if (isset($_SESSION["msg"])) {
          ?>
            <div class="col-xl-12 col-md-6 mb-4">
              <?php
              echo $_SESSION["msg"];
              unset($_SESSION["msg"]);
              ?>
            </div>
          <?php
          }
          
          ?>
        </div>
               
        <div class="row">          
          <div class="col-md-12">

            <div class="form-group" id="process" style="display:none;">
        <div class="progress">
       <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="">
       </div>
      </div>
       </div>
                
        <div class="row">
          
            <div class="col-xl-12 col-md-6 mb-4" id="success_message">
          
            </div>
          
        </div> 
        <?php 

            if(isset($_GET['view'])):
            
              $query = 'SELECT date_from, status FROM payroll WHERE id="'.$_GET['view'].'"';

              $statement = $connect->prepare($query);
              $statement->execute();
              $total_data = $statement->rowCount();
              $result = $statement->fetchAll();
              if ($total_data > 0):  
                foreach($result as $row_payroll):

                  endforeach;
                endif;
              endif;
                  ?>      
                    
              <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">Payroll List - <?php echo date("Y F", strtotime($row_payroll['date_from'])); ?></h3> 
                   <?php
                   if ($row_payroll['status']!=2){
                    if (checkPermissions($_SESSION["user_id"], 94) == "true") {
                      ?>
                      <form method="POST" id="re_calculate_form" >
                        <input type="hidden" name="payroll_id" value="<?php echo $_GET['view']; ?>">
                        <button class="btn btn-primary btn-sm btn-block col-md-2 float-right" name="calculate_payroll" id="calculate_payroll" type="submit" data-toggle="tooltip" data-placement="top" title="Calculate"><i class="fas fa-calculator"></i> Re-Caclulate Payroll</button>

                    </form>
                    <?php
                  }
                }
                  ?>
                </div>
                  <!-- /.card-header -->
                <div class="card-body"> 

                  <?php
                  if ($row_payroll['status']!=2){
                  if (checkPermissions($_SESSION["user_id"], 93) == "true") {
                    ?>

                    <form method="POST" id="sample_form" >
                    <input type="hidden" name="payroll_id" value="<?php echo $_GET['view']; ?>">
                    <button class="btn btn-sm btn-outline-success" name="all_approved" id="approved" type="submit" data-toggle="tooltip" data-placement="top" title="All Approved" ><i class="fa fa-check"></i> All Approved</button>

                  </form>
                  <?php
                  }}
                  ?>
                  
                  <br>
                  <?php
                  if(isset($_GET['view']))
                  {
                    $query = 'SELECT * FROM payroll_items WHERE payroll_id="'.$_GET['view'].'" ORDER BY id ASC';

                    $statement = $connect->prepare($query);
                    $statement->execute();
                    $total_data = $statement->rowCount();

                    $result = $statement->fetchAll();

                  ?>

                  <table id="example2" class="table table-bordered table-striped table-sm">
                    <thead style="text-align: center;">
                      <tr>
                        <th>#</th>                        
                        <!-- <th>EMP No</th> -->
                        <th>Name</th>
                        <!-- <th>Rank</th> -->
                        <th>Total Shifts</th>
                        <th>OT Hrs</th>
                        <th>Basic</th>
                        <th>For EPF</th>
                        <th>Over Time</th>
                        <th>Incentive</th>
                        <th>Allowances</th>
                        <th>Gross</th>
                        <!-- <th>EPF</th> -->
                        <!-- <th>No Pay Days</th>
                        <th>No Pay</th> -->
                        <th>Deductions</th>
                        <th>Net</th>
                        <th>EPF 12%</th>
                        <th>ETF 3%</th>
                        <th>Not Deducted</th>
                        <th>Status</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $startpoint =0;
                      $sno = $startpoint + 1;
                      foreach($result as $row)
                      {
                        $query = 'SELECT * FROM payroll WHERE id="'.$row['payroll_id'].'"';
                        $statement = $connect->prepare($query);
                        $statement->execute();
                        $result = $statement->fetchAll();
                        foreach($result as $date_from)
                        { 
                        }

                        $query = 'SELECT surname, initial FROM employee a INNER JOIN  join_status b ON a.employee_id=b.employee_id WHERE b.join_id="'.$row['employee_id'].'"';
                        $statement = $connect->prepare($query);
                        $statement->execute();
                        $result = $statement->fetchAll();
                        foreach($result as $employee_name)
                        { 
                        }

                        $query = 'SELECT position_abbreviation FROM position WHERE position_id="'.$row['position_id'].'"';
                        $statement = $connect->prepare($query);
                        $statement->execute();
                        $total_position = $statement->rowCount();
                        $result = $statement->fetchAll();
                        if ($total_position > 0) {
                          foreach($result as $position_name)
                          { 
                            $position_id = $position_name['position_abbreviation'];
                          }
                          }else{
                            $position_id ='';
                          }

                          $total_allowance=(string)$row['allowance_amount']+(string)$row['poya_day_payment']+(string)$row['m_ot_payment']+(string)$row['m_payment'];

                          // all deduction in employee_deductions table
                          $statement = $connect->prepare("SELECT COALESCE(sum(amount),'0') AS ded_amount FROM employee_deductions WHERE employee_id='".$row['employee_id']."' AND status = 2 AND YEAR(effective_date)= YEAR('".$date_from['date_from']."') AND MONTH(effective_date) = MONTH('".$date_from['date_from']."')");
                          $statement->execute();
                          $result = $statement->fetchAll();
                          foreach($result as $emp_deductions){             
                            $ded_amount=$emp_deductions['ded_amount'];    
                          }

                          $statement = $connect->prepare("SELECT paid_amount FROM loan_schedules WHERE employee_id='".$row['employee_id']."' AND status=2 AND YEAR(date_due)= YEAR('".$date_from['date_from']."') AND MONTH(date_due) = MONTH('".$date_from['date_from']."')");
                          $statement->execute();
                          $total_loan_schedules = $statement->rowCount();
                          $result = $statement->fetchAll();
                          if ($total_loan_schedules > 0) {
                            foreach($result as $loan_deductions){             
                              $paid_amount=$loan_deductions['paid_amount'];                              
                            }
                          }else{
                            $paid_amount=0;
                          }

                          $statement = $connect->prepare("SELECT COALESCE(sum(amount),'0') AS ration_amount FROM ration_deduction WHERE employee_id='".$row['employee_id']."' AND status=2 AND YEAR(date_effective)= YEAR('".$date_from['date_from']."') AND MONTH(date_effective) = MONTH('".$date_from['date_from']."')");
                          $statement->execute();
                          $result = $statement->fetchAll();        
                          foreach($result as $ration_deductions){             
                            $ration_amount=$ration_deductions['ration_amount'];            
                          }

                          $statement = $connect->prepare("SELECT COALESCE(sum(amount),'0') AS uniform FROM inventory_deduction WHERE employee_id='".$row['employee_id']."' AND status=2 AND YEAR(due_date)= YEAR('".$date_from['date_from']."') AND MONTH(due_date) = MONTH('".$date_from['date_from']."')");
                          $statement->execute();
                          $result = $statement->fetchAll();
                          foreach($result as $inventory_deductions){             
                            $inventory_amount=$inventory_deductions['uniform'];            
                          } 

                          $not_deduction=(string)$ded_amount+(string)$paid_amount+(string)$ration_amount+(string)$inventory_amount;

                        ?>
                        <tr>
                            <td><center><?php echo $sno; ?></td>
                            <td><center><?php echo $row['employee_no'].' '.$position_id.' '.$employee_name['surname'].' '.$employee_name['initial'];?></td>                            
                            <td><center>
							                   <a href="#" class="tooltipE3" id="pay_<?php echo $row['id']; ?>" title=""><?php if ($row['no_of_shift'] !='') : echo number_format($row['no_of_shift']); endif;?></a>
							             </center></td>
                            <td><center><?php echo $row['ot_hrs'];?></center></td>
                            <td><center><?php if ($row['basic_salary'] !=0) : echo number_format($row['basic_salary'],2); endif;?></center></td>
                            <td><center><?php if ($row['basic_epf'] !=0) : echo number_format($row['basic_epf'],2); endif;?></center></td>
                            <td><center><?php if ($row['ot_amount'] !=0) : echo number_format($row['ot_amount'],2); endif;?></center></td>
                            <td><center><?php if ($row['incentive'] !=0) : echo number_format($row['incentive'],2); endif;?></center></td>
                            <td><center>
                              <a href="#" class="tooltipE1" id="pay_<?php echo $row['id']; ?>" title=""><?php if ($total_allowance !=0) : echo number_format($total_allowance,2); endif;?></a>
                            </center></td>
                            <td><center><?php if ($row['gross'] !=0) : echo number_format($row['gross'],2); endif;?></center></td>
                            <!-- <td><center><?php if ($row['employee_epf']!=0) : echo number_format($row['employee_epf'],2); endif; ?></center></td> -->
                            <!-- <td><center><?php if ($row['absent_day'] !=0) : echo number_format($row['absent_day']); endif;?></center></td> -->
                            <!-- <td><center><?php if ($row['absent_amount'] !=0) :echo number_format($row['absent_amount'],2); endif;?></center></td> -->
                            <td><center>
                              <a href="#" class="tooltipE2" id="pay_<?php echo $row['id']; ?>" title=""><?php if ($row['deduction_amount'] !=0) : echo number_format($row['deduction_amount'],2); endif;?></a>
                              </center></td>
                            <td><center><?php if ($row['net'] !=0) : echo number_format($row['net'],2); endif;?></center></td>
                            <td><center><?php if ($row['employer_epf'] !=0) : echo number_format($row['employer_epf'],2); endif;?></center></td>
                            <td><center><?php if ($row['employer_etf'] !=0) : echo number_format($row['employer_etf'],2); endif;?></center></td>
                            <td><center><a href="#" class="tooltipE5" id="pay_<?php echo $row['employee_id']; ?>" title=""><?php if ($not_deduction !=0) : echo number_format($not_deduction,2); endif;?></a></center></td>
                            <td>
                              <center>
                                <?php if($row['status'] == 0): ?>
                                  <span class="badge badge-primary">New</span>
                                <?php elseif($row['status'] == 1): ?>
                                  <span class="badge badge-success">Approved</span>
                                <?php elseif($row['status'] == 2): ?>
                                  <span class="badge badge-danger tooltipE4" id="pay_<?php echo $row['employee_id']; ?>" title="" >Halt</span>
                                <?php elseif($row['status'] == 3): ?>
                                  <span class="badge badge-warning">Re-approved</span>
                                <?php elseif($row['status'] == 4): ?>
                                  <span class="badge badge-warning">Resignation</span>
                                <?php endif ?>
                              </center>
                            </td>

                            <td>
                              <center>
                              <form method="POST" class="employee_calculate_form">
                                <input type="hidden" name="payroll_id" value="<?php echo $_GET['view']; ?>">
                                <input type="hidden" name="payroll_item_id" value="<?php echo $row['id']?>">
                                <input type="hidden" name="employee_id" value="<?php echo $row['employee_id']?>">
                                <button class="btn btn-outline-primary btn-sm calculate_payroll_employee" name="calculate_payroll_employee" type="submit" data-toggle="tooltip" data-placement="top" title="Calculate"><i class="fas fa-calculator"></i></button>
                            </form>

                            
                                <form method="POST" id="" action="">
                                  <input type="hidden" name="payroll_id" value="<?php echo $row['id']?>">
                                  <input type="hidden" name="employee_id" value="<?php echo $row['employee_id']?>">
                                  <a class="btn btn-sm btn-outline-warning" name="view_payslip" id="view_payslip" href="/payroll_list/print/<?php echo $_GET['view'] ?>/<?php echo $row['id']?>" target="_blank" data-toggle="tooltip" data-placement="left" title="Payslip"><i class="fa fa-eye"></i></a>

                                  <?php 
                                  if (checkPermissions($_SESSION["user_id"], 93) == "true"):
                                    if($row['status'] == 0): ?>
                                    <button class="edit_data4 btn btn-sm btn-outline-danger halt float-right" data-id="<?php echo $row['employee_id']?>" type="button" data-toggle="tooltip" data-placement="left" title="Halt"><i class="fa fa-times"></i></button>
                                    <button class="btn btn-sm btn-outline-success" name="approved" id="approved" type="submit" data-toggle="tooltip" data-placement="left" title="Approved"><i class="fa fa-check"></i></button>   
                                    <?php elseif($row['status'] == 1): ?>
                                      <?php 
                                      if ($row_payroll['status']!=2){
                                      ?>
                                      <button class="edit_data4 btn btn-sm btn-outline-danger halt float-right" data-id="<?php echo $row['employee_id']?>" type="button" data-toggle="tooltip" data-placement="left" title="Halt"><i class="fa fa-times"></i></button>
                                      <?php } ?>
                                    <?php elseif($row['status'] == 2): ?>
                                      <?php 
                                      if ($row_payroll['status']!=2){
                                      ?>
                                     <button class="btn btn-sm btn-outline-primary" name="re_approved" id="are_approvedpproved" type="submit" data-toggle="tooltip" data-placement="left" title="Re Approved"><i class="fa fa-check"></i></button>
                                     <?php } ?>
                                    <?php elseif($row['status'] == 3): ?>
                                      
                                     <button class="edit_data4 btn btn-sm btn-outline-danger halt float-right" data-id="<?php echo $row['employee_id']?>" type="button" data-toggle="tooltip" data-placement="left" title="Halt"><i class="fa fa-times"></i></button>
                                                    
                                  <?php else: ?>

                                     
                                  <?php endif;
                                  endif; ?>
                                  
                                </form>                                  
                              </center>
                            </td>
                        </tr>
                        <?php
                        $sno ++;
                      }
                    }else{

                    }
                      ?>
                    
                    </tbody>
                  </table>
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->  
            
          </div>          
        </div>
        <!-- /.row -->
       
      
      </div><!-- /.container-fluid -->
    </section>
    <!--  start  modal -->
    <div id="editData4" class="modal fade">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Halt Reason</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="info_update4">
            <?php @include("/halt_reason");?>
          </div>
          <!-- <div class="modal-footer ">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          </div> -->
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->
    </div>
    <!--   end modal -->    

 <?php
include '../inc/footer.php';
?>

<script type="text/javascript">
  $(document).ready(function(){

      // Add tooltip
      $('.tooltipE1').tooltip({
          delay: 500,
          placement: "bottom",
          title: Allowances,
          html: true
      });

      $('.tooltipE2').tooltip({
          delay: 500,
          placement: "bottom",
          title: Deduction,
          html: true
      });
	  
	  $('.tooltipE3').tooltip({
          delay: 500,
          placement: "bottom",
          title: no_of_shift,
          html: true
      });

    $('.tooltipE4').tooltip({
          delay: 500,
          placement: "bottom",
          title: halt_reason,
          html: true
      });

    $('.tooltipE5').tooltip({
        delay: 500,
        placement: "bottom",
        title: not_deduction,
        html: true
    });

  });

	function no_of_shift(){
      var id = this.id;
      var split_id = id.split('_');
      var payid = split_id[1];

      var tooltipTextB = "";
      $.ajax({
          url: '/fetch_no_of_shifts',
          type: 'post',
          async: false,
          data: {payid:payid},
          success: function(response){
              tooltipTextB = response;
          }
      });
      return tooltipTextB;
  }
  
  function Allowances(){
      var id = this.id;
      var split_id = id.split('_');
      var payid = split_id[1];

      var tooltipTextA = "";
      $.ajax({
          url: '/fetch_allowance',
          type: 'post',
          async: false,
          data: {payid:payid},
          success: function(response){
              tooltipTextA = response;
          }
      });
      return tooltipTextA;
  }

  function Deduction(){
      var id = this.id;
      var split_id = id.split('_');
      var payid = split_id[1];

      var tooltipText = "";
      $.ajax({
          url: '/fetch_deduction',
          type: 'post',
          async: false,
          data: {payid:payid},
          success: function(response){
              tooltipText = response;
          }
      });
      return tooltipText;
  }

  function halt_reason(){
      var id = this.id;
      var split_id = id.split('_');
      var employee_id = split_id[1];
      var payroll_id = <?php echo $_GET['view']; ?>;

      var tooltipTextC = "";
      $.ajax({
          url: '/fetch_halt',
          type: 'post',
          async: false,
          data: {employee_id:employee_id, payroll_id:payroll_id},
          success: function(response){
              tooltipTextC = response;
          }
      });
      return tooltipTextC;
  }

  function not_deduction(){
      var id = this.id;
      var split_id = id.split('_');
      var employee_id = split_id[1];
      var payroll_id = <?php echo $_GET['view']; ?>;

      var tooltipTextD = "";
      $.ajax({
          url: '/fetch_not_deduction',
          type: 'post',
          async: false,
          data: {employee_id:employee_id, payroll_id:payroll_id},
          success: function(response){
              tooltipTextD = response;
          }
      });
      return tooltipTextD;
  }

</script>

<script>
 
 $(document).ready(function(){

$('#example2').DataTable({
  "paging": true,
  "lengthChange": true,
  "searching": true,
  "ordering": true,
  "info": true,
  "autoWidth": false,
  "responsive": false,
  "scrollX": true,
});

$('.view_payslip').click(function(){
  var $id = $(this).attr('data-id');
  location.href = "/payroll_list/payroll/pay_slip/" + $id;
});

function setupFormSubmission(formSelector, submitUrl, buttonSelector) {
    $(document).on('submit', formSelector, function(event) {
        event.preventDefault();
        var $form = $(this);
        $.ajax({
            url: submitUrl,
            method: "POST",
            data: $form.serialize(),
            beforeSend: function() {
                $form.find(buttonSelector).attr('disabled', 'disabled');
                $('#process').css('display', 'block');
            },
            success: function(data) {
                var percentage = 0;
                var timer = setInterval(function() {
                    percentage += 20;
                    progress_bar_process(percentage, timer, formSelector, buttonSelector);
                }, 1000);
            }
        });
    });
}




function progress_bar_process(percentage, timer, formSelector, buttonSelector) {
  $('.progress-bar').css('width', percentage + '%');
  if (percentage > 100) {
    clearInterval(timer);
    $(formSelector)[0].reset();
    $('#process').css('display', 'none');
    $('.progress-bar').css('width', '0%');
    $(buttonSelector).attr('disabled', false);
    $('#success_message').html('<div class="alert alert-dismissible alert-success bg-gradient-success text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><span class="glyphicon glyphicon-info-sign"></span>Success.</div>');
    setTimeout(function() {
      $('#success_message').html('');
      location.reload();
    }, 2000);
  }
}

setupFormSubmission('#sample_form', '/process_approved', '#approved');
setupFormSubmission('#re_calculate_form', '/process', '#calculate_payroll');
setupFormSubmission('.employee_calculate_form', '/process_employee', '.calculate_payroll_employee');

$(function () {
  $('[data-toggle="tooltip"]').tooltip()
});

$(document).on('click', '.edit_data4', function(){
  $("#editData4").modal({
    backdrop: 'static',
    keyboard: false
  });
  var edit_id4 = $(this).attr('data-id');
  var payroll_id = <?php echo $_GET['view']; ?>; // Use Laravel's output sanitization
  $.ajax({
    url: "/halt_reason",
    type: "POST",
    data: {edit_id4: edit_id4, payroll_id: payroll_id},
    success: function(data){
      $("#info_update4").html(data);
      $("#editData4").modal('show');
    }
  });
});
});

  </script>