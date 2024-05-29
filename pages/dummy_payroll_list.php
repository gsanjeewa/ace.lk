
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

$error=false;

if (isset($_POST['add_save'])){

  if (checkPermissions($_SESSION["user_id"], 29) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/payroll_list/add_payroll');
    exit();
}

  $date_from=  $_POST['start_date'];
  $date_to=  $_POST['end_date'];
  $statement = $connect->prepare("SELECT * FROM d_payroll WHERE date_from=:date_from AND date_to=:date_to");
  $statement->bindParam(':date_from', $date_from);
  $statement->bindParam(':date_to', $date_to);
  $statement->execute(); 
  if($statement->rowCount()>0){
    $error = true;
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-warning bg-gradient-warning text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>start_date & end_date Already existing.</div>';
  }
 
  $i= 1;
  while($i == 1){
    $ref_no=date('Y') .'-'. mt_rand(1,9999);
    $query = "SELECT * FROM payroll WHERE ref_no = '".$ref_no."'";
    $statement = $connect->prepare($query);
    $statement->execute();
    $total_data = $statement->rowCount();

    if($total_data <= 0){
      $i = 0;
    }
  }
  
  $data = array(
      ':ref_no'    =>  $ref_no,
      ':date_from' =>  $_POST['start_date'],
      ':date_to'   =>  $_POST['end_date'],
      ':type'      =>  $_POST['type_id'],            
  );
 
  $query = "
  INSERT INTO `d_payroll`(`ref_no`, `date_from`, `date_to`, `type`)
  VALUES (:ref_no, :date_from, :date_to, :type)
  ";
          
  $statement = $connect->prepare($query);

  if (!$error) {
    if($statement->execute($data))
    {
      $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';            
    }else{
        $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
    }
    
  }
}


include '../inc/header.php';


?>

<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Dummy</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dummy</li>
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
        <div class="row">          
          <div class="col-md-12">
            
              <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">Payroll List</h3>
                  <button class="edit_data4 btn btn-sm bg-gradient-primary float-right" type="button" ><i class="fas fa-plus"></i> Add Payroll</button>                        
                </div>
                  <!-- /.card-header -->
                <div class="card-body"> 
                  
                  <?php
                  
                  $query = 'SELECT * FROM d_payroll ORDER BY id DESC';

                  $statement = $connect->prepare($query);
                  $statement->execute();
                  $total_data = $statement->rowCount();

                  $result = $statement->fetchAll();

                  ?>

                  <table id="example2" class="table table-bordered table-striped table-sm">
                    <thead style="text-align: center;">
                      <tr>
                        <th>#</th>                        
                        <th>Ref No</th>
                        <th>Date Form</th>
                        <th>Date To</th>
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
                        ?>
                        <tr>
                            <td><center><?php echo $sno; ?></center></td>
                            <td><center><?php echo $row['ref_no'];?></center></td>
                            <td><center><?php echo $row['date_from'];?></center></td>
                            <td><center><?php echo $row['date_to'];?></center></td>
                            <td>
                              
                              <center>                                           
                                <?php if($row['status'] == 0): ?>
                                  <span class="badge badge-primary">New</span>
                                <?php else: ?>
                                  <span class="badge badge-success">Calculated</span>
                                <?php endif ?>
                              </center>
                            </td>

                            <td>
                              <center>
                                <form method="POST" id="sample_form_<?php echo $row['id']?>">
                                  <input type="hidden" name="payroll_id" value="<?php echo $row['id']?>">
                                <?php if($row['status'] == 0): ?>
                                  
                                  
                                   <button class="btn btn-sm btn-outline-success" name="calculate_payroll" id="calculate_payroll_<?php echo $row['id']?>" type="submit" data-toggle="tooltip" data-placement="top" title="Calculate"><i class="fas fa-calculator"></i> Calculate</button>
                                   <button class="edit_data4 btn btn-sm btn-outline-primary edit_payroll" data-id="<?php echo $row['id']?>" type="button" data-toggle="tooltip" data-placement="left" title="Edit Payroll"><i class="fa fa-edit"></i></button>                                  
                                  <button class="btn btn-sm btn-outline-danger" name="remove_payroll"><i class="fa fa-trash" data-toggle="tooltip" data-placement="left" title="Remove Payroll"></i></button>
                                  
                                <?php else: ?>
                                  
                                    
                                    <button class="btn btn-outline-primary btn-sm" name="calculate_payroll" id="calculate_payroll_<?php echo $row['id']?>" type="submit" data-toggle="tooltip" data-placement="top" title="Calculate"><i class="fas fa-calculator"></i> Re-Caclulate Payroll</button>
                               
                                    <a class="btn btn-sm btn-outline-warning" href="/dummy/d_payroll/<?php echo $row['id']?>" data-toggle="tooltip" data-placement="top" title="View Payroll"><i class="fa fa-eye"></i></a>
                                   
                                  
                                  
                                <?php endif ?>                                 
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

        <!--  start  modal -->
    <div id="editData4" class="modal fade">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Payroll</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="info_update4">
            <?php @include("/add_payroll");?>
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
      
      </div><!-- /.container-fluid -->
    </section>   

 <?php
include '../inc/footer.php';
?>

<script>
 
 $(document).ready(function(){

  $('#example2').DataTable({
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });


  $(document).on('click','.edit_data4',function(){
  $("#editData4").modal({
      backdrop: 'static',
      keyboard: false
  });
  var edit_id4=$(this).attr('data-id');
  $.ajax({
    url:"/add_payroll",
    type:"post",
    data:{edit_id4:edit_id4},
    success:function(data){
      $("#info_update4").html(data);
      $("#editData4").modal('show');
    }
  });
});
  
  $("[id^='sample_form_']").on('submit', function(event){
    var id = $(this).attr('id');
    id = id.replace("sample_form_",'');
   event.preventDefault();
    $.ajax({
     url:"/d_process",
     method:"POST",
     data:$(this).serialize(),
     beforeSend:function()
     {
      $('#calculate_payroll_'+id).attr('disabled', 'disabled');
      $('#process').css('display', 'block');
     },
     success:function(data)
     {
      var percentage = 0;

      var timer = setInterval(function(){
       percentage = percentage + 20;
       progress_bar_process(percentage, timer, id);
      }, 1000);
     }
    })
   
  });

  function progress_bar_process(percentage, timer, id)
  {
   $('.progress-bar').css('width', percentage + '%');
   if(percentage > 100)
   {
    clearInterval(timer);
    $('#sample_form_'+id)[0].reset();
    $('#process').css('display', 'none');
    $('.progress-bar').css('width', '0%');
    $('#calculate_payroll_'+id).attr('disabled', false);
    $('#success_message').html('<div class="alert alert-dismissible alert-success bg-gradient-success text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><span class="glyphicon glyphicon-info-sign"></span>Success.</div>');
    setTimeout(function(){
     $('#success_message').html('');
     location.reload();
    }, 2000);
   }
  }

  $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    });

 });
</script>

