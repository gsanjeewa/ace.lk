
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
            <h1 class="m-0 text-dark">Resignation</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Resignation</li>
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
         
                    
              <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">Resignation List </h3>                    
                </div>
                  <!-- /.card-header -->
                <div class="card-body"> 

                  
                  <?php
                  
                    $query = 'SELECT * FROM resignation ORDER BY id DESC';

                    $statement = $connect->prepare($query);
                    $statement->execute();
                    $total_data = $statement->rowCount();

                    $result = $statement->fetchAll();

                  ?>

                  <table id="example2" class="table table-bordered table-striped table-sm">
                    <thead style="text-align: center;">
                      <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Last Payroll</th>                        
                        <th>Gross</th>                        
                        <th>Deductions</th>
                        <th>Net</th>                        
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $startpoint =0;
                      $sno = $startpoint + 1;
                      foreach($result as $row)
                      {                        

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
                         
                        ?>
                        <tr>
                            <td><center><?php echo $sno; ?></center></td>
                            <td><?php echo $row['employee_no'].' '.$position_id.' '.$employee_name['surname'].' '.$employee_name['initial'];?></td>                            
                            <td><center><?php if ($row['last_month_pay'] !=0) : echo number_format($row['last_month_pay'],2); endif;?></center></td>
                            <td><center><?php if ($row['gross_income'] !=0) : echo number_format($row['gross_income'],2); endif;?></center></td>                            
                            <td><center>
                              <a href="#" class="tooltipE2" id="pay_<?php echo $row['id']; ?>" title=""><?php if ($row['total_deduction'] !=0) : echo number_format($row['total_deduction'],2); endif;?></a>
                              </center></td>
                            <td><center><?php if ($row['net_amount'] !=0) : echo number_format($row['net_amount'],2); endif;?></center></td>
                            

                            <td>
                              <center>
                                <form method="POST" id="" action="">
                                  <input type="hidden" name="payroll_id" value="<?php echo $row['id']?>">
                                  <input type="hidden" name="employee_id" value="<?php echo $row['employee_id']?>">
                                  <a class="btn btn-sm btn-outline-warning" name="view_payslip" id="view_payslip" href="/employee_list/resignation/print/<?php echo $row['id']?>" target="_blank" data-toggle="tooltip" data-placement="left" title="Payslip"><i class="fa fa-eye"></i></a>
                                                              
                                </form>                                  
                              </center>
                            </td>
                        </tr>
                        <?php
                        $sno ++;
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


  $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    });

 });
</script>
