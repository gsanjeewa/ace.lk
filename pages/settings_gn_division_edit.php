<?php
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
require_once 'system_permissions.php';
$connect = pdoConnection();

if ((checkPermissions($_SESSION["user_id"], 96) == "false") OR (checkPermissions($_SESSION["user_id"], 97) == "false")) {

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
        $sql2="SELECT g.gn_id, g.ds_id, d.dis_id, g.gn from gn g INNER JOIN ds d ON g.ds_id=d.ds_id INNER JOIN districts s ON d.dis_id=s.dis_id where g.gn_id=:eid";
        $query2 = $connect -> prepare($sql2);
        $query2-> bindParam(':eid', $eid, PDO::PARAM_STR);
        $query2->execute();
        $results=$query2->fetchAll(PDO::FETCH_OBJ);
        if($query2->rowCount() > 0)
        {
            foreach($results as $row)
            {
                $_SESSION['editbid']=$row->gn_id;        
                ?>

                <form action="" class="form-sample" method="post" enctype="multipart/form-data" id="add_bank_form">
                   
                        <div class="form-group">
                        <label for="districts">District</label>
                        <select class="form-control select2" style="width: 100%;" id="districts" name="districts">
                          <option value="">Select Districts</option>
                          <?php
                          $query="SELECT * FROM districts ORDER BY districts ASC";
                          $statement = $connect->prepare($query);
                          $statement->execute();
                          $result = $statement->fetchAll();
                          foreach($result as $row_dis)
                          {
                            ?>
                            <option value="<?php echo $row_dis['dis_id'];?>"<?php if ($row_dis['dis_id']==$row->dis_id){ echo "SELECTED";}?>><?php echo $row_dis['districts']; ?></option>
                            <?php
                          }
                          ?>
                        </select>                        
                      </div>

                      <div class="form-group">
                        <label for="ds">DS Division</label>
                        <select class="form-control select2" style="width: 100%;" id="ds" name="ds">
                          <option value="">Select DS</option> 
                          <?php
                          $query="SELECT * FROM ds ORDER BY ds_id ASC";
                          $statement = $connect->prepare($query);
                          $statement->execute();
                          $result = $statement->fetchAll();
                          foreach($result as $row_ds)
                          {
                            ?>
                            <option value="<?php echo $row_ds['ds_id'];?>"<?php if ($row_ds['ds_id']==$row->ds_id){ echo "SELECTED";}?>><?php echo $row_ds['ds']; ?></option>
                            <?php
                          }
                          ?>                         
                        </select>                        
                      </div>

                        <div class="form-group">
                            <label class="col-sm-12 pl-0 pr-0">GN Division</label>
                            <div class="col-sm-12 pl-0 pr-0">
                                <input type="text" name="gn_division" id="gn_division" class="form-control text-uppercase" value="<?php echo $row->gn;?>" />
                            </div>
                        </div>                        
                    
                    <button type="submit" name="update_save" class="btn btn-primary btn-fw mr-2" style="float: left;">Update</button>
                </form>
                <?php 
            }
        }
    }else{
        ?>
        <form action="" method="post" enctype="multipart/form-data" id="add_bank_form">
            <div class="form-group">
                <label for="districts">District</label>
                <select class="form-control select2" style="width: 100%;" id="districts" name="districts">
                  <option value="">Select Districts</option>
                  <?php
                  $query="SELECT * FROM districts ORDER BY districts ASC";
                  $statement = $connect->prepare($query);
                  $statement->execute();
                  $result = $statement->fetchAll();
                  foreach($result as $row_dis)
                  {
                    ?>
                    <option value="<?php echo $row_dis['dis_id'];?>"><?php echo $row_dis['districts']; ?></option>
                    <?php
                  }
                  ?>
                </select>                        
            </div>

            <div class="form-group">
                        <label for="ds">DS Division</label>
                        <select class="form-control select2" style="width: 100%;" id="ds" name="ds">
                          <option value="">First Select District</option>                          
                        </select>                        
                      </div>

                    
                        <div class="form-group">
                            <label class="col-sm-12 pl-0 pr-0">GN Division</label>
                            <div class="col-sm-12 pl-0 pr-0">
                                <input type="text" name="gn_division" id="gn_division" class="form-control text-uppercase"  />
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
  $('.select2').select2()
  $('#add_bank_form').validate({
    rules: {
      districts: { required: true},
      police_stn: { required: true}      
    },

    messages: {      
      
       
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
   $(document).on('change','#districts', function(){
      var districtsID = $(this).val();
      if(districtsID){
          $.ajax({
              type:'POST',
              url:'/backend-script',
              data:{'districts_id':districtsID,'request':1},
              success:function(result){
                  $('#ds').html(result);
                 
              }
          });           
      }else{
          $('#ds').html('<option value="">First Select Districts</option>');
          
      }
  });  

</script>