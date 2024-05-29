<?php 
session_start();
include '../pages/config.php';
include '../inc/timezone.php';
require_once 'system_permissions.php';
$connect = pdoConnection();

if (checkPermissions($_SESSION["user_id"], 102) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}

$error = false;

if(isset($_POST['add_new']))
{
  if (checkPermissions($_SESSION["user_id"], 100) == "false") {
    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>'; 
    header('location:/settings/gn_division_list');   
    exit();
  }

  $gn=  strtoupper(trim($_POST['gn_division']));
  $statement = $connect->prepare("SELECT gn FROM gn WHERE gn=:gn");
  $statement->bindParam(':gn', $gn);
  $statement->execute(); 
  if($statement->rowCount()>0){
    $error = true;
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-warning bg-gradient-warning text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>Police Station Already existing.</div>';
  }

  if (!$error) {
    $data = array(
        ':gn'     =>  $gn,
        ':ds_id'     =>  $_POST['ds'],        
    );
   
    $query = "
    INSERT INTO `gn`(`ds_id`, `gn`) VALUES (:ds_id, :gn)
    ";
            
    $statement = $connect->prepare($query);

    if($statement->execute($data))
    {
      $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <span class="glyphicon glyphicon-info-sign"></span>Success.</div>'; 
      header('location:/settings/gn_division_list');   
        exit();           
    }else{
        $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
    }
  }
}


if(isset($_POST['update_save']))
{
    if (checkPermissions($_SESSION["user_id"], 101) == "false") {

        $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>'; 
        header('location:/settings/gn_division_list');   
        exit();
    }

    $data = array(
        ':id'        =>  $_SESSION['editbid'],
        ':gn'    =>  strtoupper(trim($_POST['gn_division'])),
        ':ds_id'    =>  $_POST['ds'],
    );
   
    $query = "UPDATE `gn` SET `gn`=:gn, `ds_id`=:ds_id WHERE `gn_id`=:id";
            
    $statement = $connect->prepare($query);

    if($statement->execute($data))
    {
      $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';
      header('location:/settings/gn_division_list');   
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
            <h1 class="m-0 text-dark">Settings</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Settings</li>
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
          if ( isset($_SESSION["msg"]) ) {
            ?>
            <div class="col-xl-12 col-md-6 mb-4">
              <?php echo $_SESSION["msg"];
              unset($_SESSION["msg"]);
               ?>
            </div>
              <?php
          }                  
        
          ?>
        </div>
        <div class="row">          
          <div class="col-md-12">
            
              <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">GN Division List</h3>
                  <button class="edit_data4 btn btn-sm bg-gradient-primary float-right" type="button" ><i class="fas fa-plus"></i> Add</button>                
                </div>
                  <!-- /.card-header -->
                <div class="card-body">  

                  <?php

                  $query = 'SELECT * FROM gn g INNER JOIN ds d ON g.ds_id=d.ds_id INNER JOIN districts s ON d.dis_id=s.dis_id ORDER BY s.districts ASC, d.ds ASC, g.gn ASC';

                  $statement = $connect->prepare($query);
                  $statement->execute();
                  $total_data = $statement->rowCount();

                  $result = $statement->fetchAll();

                  ?>

                  <table id="example2" class="table table-bordered table-sm table-striped">
                    <thead style="text-align: center;">
                      <tr>
                        <th>#</th>                        
                        <th>Distric</th>
                        <th>DS Division</th>
                        <th>GN Division</th>
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
                            <td><?php echo $row['districts']; ?></td>
                            <td><?php echo $row['ds'];?></td>
                            <td><?php echo $row['gn'];?></td>
                            <td>
                              <center>
                                <button class="edit_data4 btn btn-sm btn-outline-primary" data-id="<?php echo $row['gn_id'];?>" type="button" data-toggle="tooltip" data-placement="top" title="Edit" data-backdrop="static" data-keyboard="false"><i class="fa fa-edit"></i></button>
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

    <!--  start  modal -->
    <div id="editData4" class="modal fade">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">GN Division</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="info_update4">
            <?php @include("/gn_division_edit");?>
          </div>
          <div class="modal-footer ">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          </div>
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
    $(document).ready(function() {      

      $('#example2').DataTable({
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    }); 

    /*$('.edit_loan').click(function(){
        var $id=$(this).attr('data-id');
        location.href = "/loan/new_loan_req/"+$id;
        
      }); 
*/
      $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    });           

    });
  </script>

  <script type="text/javascript">

    $(document).ready(function(){
      $(document).on('click','.edit_data4',function(){
        $("#editData4").modal({
            backdrop: 'static',
            keyboard: false
        });
        var edit_id4=$(this).attr('data-id');
        $.ajax({
          url:"/gn_division_edit",
          type:"post",
          data:{edit_id4:edit_id4},
          success:function(data){
            $("#info_update4").html(data);
            $("#editData4").modal('show');
          }
        });
      });
    });
  </script>
