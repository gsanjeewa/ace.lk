
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
    header('location:/inventory/location');
    exit();
}

 
  $data = array(      
      ':location' =>  strtoupper(trim($_POST['location'])),
      ':type'      =>  2,            
  );
 
  $query = "
  INSERT INTO inventory_location(location, type) 
  VALUES (:location, :type)
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

if(isset($_POST['update_save']))
{
    if (checkPermissions($_SESSION["user_id"], 29) == "false") {

        $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>'; 
        header('location:/inventory/location');   
        exit();
    }

    $data = array(
        ':id'        =>  $_SESSION['editbid'],
        ':location' =>  strtoupper(trim($_POST['location'])),
    );
   
    $query = "UPDATE inventory_location SET location=:location WHERE `id`=:id";
            
    $statement = $connect->prepare($query);

    if($statement->execute($data))
    {
      $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';
      header('location:/inventory/location');   
        exit();            
    }else{
        $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
    }
}


include '../inc/header.php';


?>

<div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Inventory</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Inventory</li>
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
                  <h3 class="card-title">Location List</h3>
                  <button class="edit_data4 btn btn-sm bg-gradient-primary float-right" type="button" ><i class="fas fa-plus"></i> Add </button>                        
                </div>
                  <!-- /.card-header -->
                <div class="card-body"> 
                  
                  <?php
                  
                  $query = 'SELECT * FROM inventory_location ORDER BY id DESC';

                  $statement = $connect->prepare($query);
                  $statement->execute();
                  $total_data = $statement->rowCount();

                  $result = $statement->fetchAll();

                  ?>

                  <table id="example2" class="table table-bordered table-striped table-sm">
                    <thead style="text-align: center;">
                      <tr>
                        <th>#</th>                        
                        <th>Location</th>
                        <th>Type</th>
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
                            <td><?php echo $row['location'];?></td>
                            <td><center><?php if ($row['type']==1) {
                              echo 'Main Stores';
                            }elseif ($row['type']==2) {
                              echo 'Sub Stores';
                            }
                            ;?></center></td>
                            <td>
                              <center>
                                <form method="POST" id="sample_form">
                                  <input type="hidden" name="loc_id" value="<?php echo $row['id']?>">                                   
                                   <button class="edit_data4 btn btn-sm btn-outline-primary edit_payroll" data-id="<?php echo $row['id']?>" type="button" data-toggle="tooltip" data-placement="left" title="Edit Payroll"><i class="fa fa-edit"></i></button>                                  
                                  <button class="btn btn-sm btn-outline-danger" name="remove_payroll"><i class="fa fa-trash" data-toggle="tooltip" data-placement="left" title="Remove Payroll"></i></button>
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
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Location</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="info_update4">
            <?php @include("/location_add");?>
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
    url:"/location_add",
    type:"post",
    data:{edit_id4:edit_id4},
    success:function(data){
      $("#info_update4").html(data);
      $("#editData4").modal('show');
    }
  });
});
  
  $('#sample_form').on('submit', function(event){
   event.preventDefault();   
    $.ajax({
     url:"/d_process",
     method:"POST",
     data:$(this).serialize(),
     beforeSend:function()
     {
      $('#calculate_payroll').attr('disabled', 'disabled');
      $('#process').css('display', 'block');
     },
     success:function(data)
     {
      var percentage = 0;

      var timer = setInterval(function(){
       percentage = percentage + 20;
       progress_bar_process(percentage, timer);
      }, 1000);
     }
    })
   
  });

  function progress_bar_process(percentage, timer)
  {
   $('.progress-bar').css('width', percentage + '%');
   if(percentage > 100)
   {
    clearInterval(timer);
    $('#sample_form')[0].reset();
    $('#process').css('display', 'none');
    $('.progress-bar').css('width', '0%');
    $('#calculate_payroll').attr('disabled', false);
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

