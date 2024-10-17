<?php
session_start(); 
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();
require_once 'system_permissions.php';

if (checkPermissions($_SESSION["user_id"], 92) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';
    header('location:/dashboard');
    exit();
}


if (isset($_POST['add_save'])){

  if (checkPermissions($_SESSION["user_id"], 92) == "false") {

    $_SESSION["msg"] ='<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>You do not have permissions.</div>';  
    header('location:/dashboard');  
    exit();
  }

  $array1=array();
  $array2=array();
  $query = 'SELECT department_id FROM d_department_merge WHERE merge_id = "'.$_POST['merge_id'].'"';
  $statement = $connect->prepare($query);
  $statement->execute();
  $total_data = $statement->rowCount();
  $result = $statement->fetchAll();
  foreach($result as $row)
  {
    $array1[]=$row['department_id'];
  }
 
  $array2=$_POST['department_id'];
  
  $diff_insert=array_diff($array2, $array1);

  $diff_delete=array_diff($array1, $array2); 

  $query_insert = "
  INSERT INTO d_department_merge(merge_id, department_id)
  VALUES (:merge_id, :department_id)  
  ";
          
  $statement = $connect->prepare($query_insert);

foreach($diff_insert as $d) {
  if($statement->execute(array(':merge_id' => $_POST['merge_id'], ':department_id' => $d)))
  {
    $_SESSION["msg"] = '<div class="alert alert-dismissible alert-success bg-gradient-success text-white">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <span class="glyphicon glyphicon-info-sign"></span>Success.</div>';            
  }else{
      $_SESSION["msg"] = '<div class="alert alert-dismissible alert-danger bg-gradient-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button><i class="fas fa-fw fa-times"></i>Can not Save.</div>';
  }
}

$query_delete = "
  DELETE FROM `d_department_merge` WHERE `merge_id`=:merge_id AND `department_id`=:department_id
  ";
          
  $statement = $connect->prepare($query_delete);

foreach($diff_delete as $k) {
  if($statement->execute(array(':merge_id' => $_POST['merge_id'], ':department_id' => $k)))
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
        <div class="row">          
          <div class="col-md-6">
            
              <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">Institute Merge</h3>                
                </div>
                  <!-- /.card-header -->
                <div class="card-body">

                  
                  <form method="POST" id="add_deduction_form" action="">
                    <div class="col-md-12">
                    <div class="form-group">
                      <label for="merge_id">Merge Institution</label>
                      <select class="form-control select2" style="width: 100%;" name="merge_id" id="merge_id">
                        <option value="">Select Institution</option>
                        <?php
                        $query="SELECT department_id, department_name, department_location FROM department WHERE department_status!=1 ORDER BY department_name ASC";
                        $statement = $connect->prepare($query);
                        $statement->execute();
                        $result = $statement->fetchAll();
                        foreach($result as $row)
                        {
                          ?>
                          <option value="<?php echo $row['department_id']; ?>"><?php echo $row['department_name'].'-'.$row['department_location']; ?></option>
                          <?php
                        }
                        ?>
                      </select>
                    </div>
                    <div class="form-group">                      
                      <label for="department_id">Institution</label>
                      <select class="form-control select2" style="width: 100%;" name="department_id[]" id="department_id" multiple>
                        <!-- <?php
                        $query="SELECT department_id, department_name, department_location FROM department WHERE department_status!=1 ORDER BY department_name ASC";
                        $statement = $connect->prepare($query);
                        $statement->execute();
                        $result = $statement->fetchAll();
                        foreach($result as $row)
                        {
                          ?>
                          <option value="<?php echo $row['department_id']; ?>"><?php echo $row['department_name'].'-'.$row['department_location']; ?></option>
                          <?php
                        }
                        ?> -->
                      </select>
                    </div>
                    </div>
                    <div class="col-md-12">
                      <button class="btn btn-sm btn-primary" type="submit" name="add_save"><i class="fas fa-save"> Save</i></button>
                    </div>
                  </form>

              
                  
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
  $(document).ready(function() {

    $('#add_deduction_form').validate({
    rules: {
      effective_date: { required: true},
      ins_id: {required: true}
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

<script type="text/javascript">
  // ajax script for getting state data
   $(document).on('change','#merge_id', function(){
      var merge_id = $(this).val();
      if(merge_id){
          $.ajax({
              type:'POST',
              url:'/ins_backend-script',
              data:{'merge_id':merge_id,'request':1},
              success:function(result){
                  $('#department_id').html(result);
                 
              }
          });          
      }else{
          $('#department_id').html('<option value="">First Select Districts</option>');          
      }
  });
 

</script>