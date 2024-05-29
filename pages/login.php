
<?php  
session_start();  
error_reporting(0);
include('config.php');
$connect = pdoConnection();
if(isset($_SESSION["user_id"]))  
{  
    header("location:/dashboard"); 
    exit; 
}  

    if(isset($_POST["login"]))  
    {  
   
        $query = "SELECT * FROM system_users WHERE username = :username";  
        $statement = $connect->prepare($query);  
        $statement->execute(  
             array(  
                  'username'     =>     $_POST["username"],  
             )  
        );  
        $count = $statement->rowCount();  
        if($count > 0)  
        {  
            $result = $statement->fetchAll();
            foreach($result as $row)
            { 
                
                if(md5($_POST["password"])== $row["password"]){

                    $sub_query = "INSERT INTO login_details (user_id, last_activity) VALUES (:user_id, :last_activity)";
                    $statement = $connect->prepare($sub_query);
                    $statement->execute(
                        array(
                            'user_id'  => $row["user_id"],
                            'last_activity' => date("Y-m-d H:i:s", STRTOTIME(date('h:i:sa')))
                        )
                    );

                    $_SESSION["user_id"] = $row["user_id"];
                    $user_id = $row["user_id"]; 
                    header("location:/dashboard");  
                }else{
                    $msgpassword = '<span class="text-danger">Wrong Password.</span>';
                }
                
            }
        }  
        else  
        {  
             $msgusername = '<span class="text-warning">Wrong username.</span>';
        }  
            
    }  

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Payroll Management System</title>
    <link rel = "icon" href ="/dist/img/logo.png" type = "image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/plugins/login/css/bootstrap.css">
    <link rel="stylesheet" href="/plugins/login/css/custom.css">
    <link rel="stylesheet" href="/plugins/login/css/custom.min.css">
   
    <link rel="stylesheet" href="/plugins/login/css/animate.css">
<!--     <link rel="stylesheet" href="css/prism-okaidia.css"> 
    <link rel="stylesheet" href="css/font-awesome.min.css"> 
     -->
    <script src="/plugins/login/js/wow.min.js"></script>
    <script>
    new WOW().init();
    </script>
<body class="body_bg_image">
  </head>
    <div class="container">
      <div class="row">
        <div class="col-lg-2"></div>
         <div class="col-lg-4">
          <div class="wow fadeInLeft" style="text-align: center;">
                <img src="/dist/img/logo.png" width="200px" align="centere" >
            <p width="350px" align="centere"  style="color:rgb(255, 165, 0); font-size:35px; text-align: center; font-family: sans-serif; text-shadow: 1px 1px 2px black;"  >

            Payroll Management System</p> 
                </div>
         </div>
        <div class="col-lg-4 align-self-center wow fadeInRight">
          
            <form method="POST" action="" id="quickForm">
                <div class="form-group">
                    <label class="form-label mt-4 login_title">Login</label>
                    <?php echo $message; ?>
                    <div class="form-floating mb-3">
                      <input type="text" class="form-control" id="floatingInput" autocomplete="off"  placeholder="administrator / User" name="username" autofocus maxlength="5">
                      <label for="floatingInput">User name</label>
                      <?php echo $msgusername; ?>
                    </div>
                    <div class="form-floating">
                      <input type="password" class="form-control" id="floatingPassword" autocomplete="off" placeholder="Password" name="password" >
                      <label for="floatingPassword">Password</label>
                      <?php echo $msgpassword; ?>
                    </div>
                </div>
                <div class="form-group">
                   <br>
                   <input  type="submit" class="btn btn-outline-info" name="login" value="Login">
                   <span class="text_white"title="" data-bs-container="body" data-bs-toggle="popover" data-bs-placement="bottom" data-bs-content="Please type your valid user informations to login to system." data-bs-original-title="Help box">
                       Help
                    </span>

                </div>

            </form>
            

        </div>
      </div>

      <div class="newstyle fixed-bottom">
      <div class="container">
          <div class="row">
            <div class="col-lg-2"></div>
            <div class="col-lg-8">
                <center><a href="#" class="footer_text">Software Solution by BIK Solution</a></center>
            </div>
            <div class="col-lg-2 "><a href="#" class="footer_text"> Version 1.0.0 </a></div>
          </div>
      </div>
        
      </div>
</body>
   <script src="/plugins/login/js/jquery.min.js"></script>
   <script src="/plugins/login/js/bootstrap.bundle.min.js"></script>
   <script src="/plugins/login/js/prism.js" data-manual></script>  
   <script src="/plugins/login/js/custom.js"></script> 
       
      <script>
      $(function () {
        $('input').iCheck({
          checkboxClass: 'icheckbox_square-blue',
          radioClass: 'iradio_square-blue',
          increaseArea: '20%' // optional
        });

      });
    </script>

</html>