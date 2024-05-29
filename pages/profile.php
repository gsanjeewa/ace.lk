<?php 
session_start();
include '../pages/config.php';
include '../inc/timezone.php'; 
$connect = pdoConnection();

if (isset($_POST['change'])){

  $data = array(
    'id'        =>  $_SESSION['user_id'],
    ':password' =>  md5($_POST['password']),     
  );

  $query = "UPDATE `system_users` SET `password`=:password WHERE `user_id`=:id";
    
  $statement = $connect->prepare($query);

  if($statement->execute($data))
  {
    $errMSG = '<div class="alert alert-dismissible alert-success bg-success text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>
    <span class="glyphicon glyphicon-info-sign"></span>Successhully Change.</div>';
    header('location:/logout');           
  }else{
    $errMSG = '<div class="alert alert-dismissible alert-danger bg-danger text-white"><button type="button" class="close" data-dismiss="alert">&times;</button>Can not Change.</div>';
  }
    
}

include '../inc/header.php';
?>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Profile</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Profile</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
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

        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-md-6">
            <div class="card card-outline card-success">
              <div class="card-header">
                <h3 class="card-title">Change Password</h3>                
              </div>
                <!-- /.card-header -->
              <div class="card-body">
                <form method="post" action="" id="add_change_form" enctype="multipart/form-data">
                   
                  <div class="form-group">
                      <input type="password" class="form-control form-control-user" name="current_password" id="current_password" placeholder="Current Password" >
                          
                  </div>
                  <div class="form-group">
                      <input type="password" class="form-control form-control-user" name="password" id="password" placeholder="New Password" >
                          
                  </div>
                  <div class="form-group">
                      <input type="password" class="form-control form-control-user" name="repeat_password" id="repeat_password" placeholder="Repeat New Password" >
                  </div>
                
                  <div class="form-group">
                    <input type="submit" class="btn btn-warning" value="Change" name="change" >
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

<script>
$(function () {
  
  $('#add_change_form').validate({
    rules: {      
      current_password: {
        required: true, 
        remote: {
          url: "/check_password",
          type: "post"
        }
      },
      
      password: {
        required: true,
        notEqualTo: "#current_password",
        strong_password: true,
      },
      repeat_password: {
        required: true,
        equalTo: "#password"
      },       
      
    },

    messages: {      
      
      current_password: {
        remote: 'Current Password is incorrect!'
      },

      password: {
        notEqualTo: 'Same Current Password!'   
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

  $.validator.addMethod("strong_password", function (value, element) {
    let password = value;
    if (!(/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[@#$%&])(.{8,20}$)/.test(password))) {
        return false;
    }
    return true;
}, function (value, element) {
    let password = $(element).val();
    if (!(/^(.{8,20}$)/.test(password))) {
        return 'Password must be between 8 and 20 characters long.';
    }
    else if (!(/^(?=.*[A-Z])/.test(password))) {
        return 'Password must contain atleast one uppercase.';
    }
    else if (!(/^(?=.*[a-z])/.test(password))) {
        return 'Password must contain atleast one lowercase.';
    }
    else if (!(/^(?=.*[0-9])/.test(password))) {
        return 'Password must contain atleast one digit.';
    }
    else if (!(/^(?=.*[@#$%&])/.test(password))) {
        return "Password must contain special characters from @#$%&.";
    }
    return false;
});


});
</script>