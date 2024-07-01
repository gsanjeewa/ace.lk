<?php 
/*session_start();*/
error_reporting(0);
require_once '../pages/system_permissions.php';

$directoryURI = $_SERVER['REQUEST_URI'];
$path = parse_url($directoryURI, PHP_URL_PATH);
$components = explode('/', $path);
$first_part = $components[1];
$second_part = $components[2];
$third_part = $components[3];

if(!isset($_SESSION['user_id'])){
    header ('Location: /');
    exit;
}

$query = 'SELECT * FROM system_users WHERE user_id = "'.$_SESSION['user_id'].'"';
$statement = $connect->prepare($query);
$statement->execute();
$result = $statement->fetchAll();
foreach($result as $row_users)
{

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">

  <title>Payroll Management System</title>
  <link rel = "icon" href ="/dist/img/logo.png" type = "image/x-icon">
  <!-- Google Font: Source Sans Pro -->
  <!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"> -->
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="/plugins/fontawesome-free/css/all.min.css">
  <!-- daterange picker -->
  <link rel="stylesheet" href="/plugins/daterangepicker/daterangepicker.css">
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <!--Crop-->
  <link rel="stylesheet" href="/plugins/cropper/cropper.css" />
  <!-- bs-stepper -->
  <link rel="stylesheet" href="/plugins/bs-stepper/css/bs-stepper.min.css">
  <!-- jquery-ui -->
  <link rel="stylesheet" href="/plugins/jquery-ui/jquery.ui.css">
  <!-- custom css-->
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <!-- Toastr -->
  <link rel="stylesheet" href="/plugins/toastr/toastr.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="/dist/css/adminlte.min.css">
  
  <link rel="stylesheet" href="/dist/css/custom.css">
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>      
    </ul>  

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Messages Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          Logout
          
        </a>
        <div class="dropdown-menu dropdown-menu-md dropdown-menu-right">
          <a href="#" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <img src="/dist/img/avatar5.png" alt="User Avatar" class="img-size-100 img-circle mr-3">              
            </div>
            <!-- Message End -->
          </a>
          <div class="dropdown-divider"></div>
          <a href="/profile" class="btn btn-sm btn-primary offset-md-1">Profile</a>
          <a href="/logout" class="btn btn-sm btn-default offset-md-1">Logout</a>
        </div>
      </li>   
    </ul>


  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <!-- <a href="index3.html" class="brand-link">
      <img src="/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light">AdminLTE 3</span>
    </a> -->

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="/dist/img/logo.png" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="/profile" class="d-block"><?php echo $row_users['first_name'].' '.$row_users['last_name']; ?></a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="true">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item">
            <a href="/dashboard" class="nav-link <?= ($first_part == 'dashboard') ? 'active':''; ?>">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard                
              </p>
            </a>
            
          </li>
          <?php

          if ((checkPermissions($_SESSION["user_id"], 1) == "true") OR (checkPermissions($_SESSION["user_id"], 3) == "true"))  {
            ?>

        <li class="nav-item has-treeview <?= ($first_part == 'employee_list') ? 'menu-open':''; ?>">
            <a href="#" class="nav-link <?= ($first_part == 'employee_list') ? 'active':''; ?>">
              <i class="nav-icon fas fa-user text-success"></i>
              <p>
                Employee List
                <i class="fas fa-angle-left right"></i>                
              </p>
            </a>
            <ul class="nav nav-treeview">
              <?php
              if (checkPermissions($_SESSION["user_id"], 3) == "true") {
              ?>
                <li class="nav-item">
                  <a href="/employee_list/employee" class="nav-link <?= ($second_part == 'employee') ? 'active':''; ?>">
                    <i class="far fa-circle nav-icon text-danger"></i>
                    <p>Employee</p>
                  </a>
                </li>
              <?php
              }
              if (checkPermissions($_SESSION["user_id"], 1) == "true") {
              ?>
                <li class="nav-item">
                  <a href="/employee_list/add_employee" class="nav-link <?= ($second_part == 'add_employee') ? 'active':''; ?>">
                    <i class="far fa-circle nav-icon text-warning"></i>
                    <p>Add Employee</p>
                  </a>
                </li> 
              <?php
              }
              if (checkPermissions($_SESSION["user_id"], 1) == "true") {
              ?>
                <li class="nav-item">
                  <a href="/employee_list/epf_excluded" class="nav-link <?= ($second_part == 'epf_excluded') ? 'active':''; ?>">
                    <i class="far fa-circle nav-icon text-primary"></i>
                    <p>EPF/ETF Excluded</p>
                  </a>
                </li> 
              <?php
              }

              if (checkPermissions($_SESSION["user_id"], 1) == "true") {
              ?>
                <li class="nav-item">
                  <a href="/employee_list/disable" class="nav-link <?= ($second_part == 'disable') ? 'active':''; ?>">
                    <i class="far fa-circle nav-icon text-secondary"></i>
                    <p>Disable</p>
                  </a>
                </li> 
              <?php
              }
              ?>
              <li class="nav-item has-treeview <?= ($second_part == 'resignation') ? 'menu-open':''; ?>">
                <a href="#" class="nav-link <?= ($second_part == 'resignation') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>
                    Resignation
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  
                  <li class="nav-item">
                    <a href="/employee_list/resignation/cal_payment" class="nav-link <?= ($third_part == 'cal_payment') ? 'active':''; ?>">
                      <i class="far fa-dot-circle nav-icon"></i>
                      <p>Calculate Payment</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="/employee_list/resignation/list" class="nav-link <?= ($third_part == 'list') ? 'active':''; ?>">
                      <i class="far fa-dot-circle nav-icon"></i>
                      <p>List</p>
                    </a>
                  </li>
                  <!-- <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-dot-circle nav-icon"></i>
                      <p>Level 3</p>
                    </a>
                  </li> -->
                </ul>
              </li>           
            </ul>
          </li>
    
          <?php
          }

          if ((checkPermissions($_SESSION["user_id"], 5) == "true") OR (checkPermissions($_SESSION["user_id"], 7) == "true") OR (checkPermissions($_SESSION["user_id"], 9) == "true") OR (checkPermissions($_SESSION["user_id"], 11) == "true")){
          ?>      
          
          <li class="nav-item has-treeview <?= ($first_part == 'allowance_list') ? 'menu-open':''; ?>">
            <a href="#" class="nav-link <?= ($first_part == 'allowance_list') ? 'active':''; ?>">
              <i class="nav-icon fas fa-list text-warning"></i>
              <p>
                Allowance List
                <i class="fas fa-angle-left right"></i>                
              </p>
            </a>
            <ul class="nav nav-treeview">
              <?php
              if (checkPermissions($_SESSION["user_id"], 7) == "true") {
                ?>
                <li class="nav-item">
                  <a href="/allowance_list/allowance" class="nav-link <?= ($second_part == 'allowance') ? 'active':''; ?>">
                    <i class="far fa-circle nav-icon text-danger"></i>
                    <p>Allowance</p>
                  </a>
                </li>
                <?php
              }
              if (checkPermissions($_SESSION["user_id"], 5) == "true") {
                ?>
                <li class="nav-item">
                  <a href="/allowance_list/add_allowance" class="nav-link <?= ($second_part == 'add_allowance') ? 'active':''; ?>">
                    <i class="far fa-circle nav-icon text-warning"></i>
                    <p>Add Allowance</p>
                  </a>
                </li>
                <?php
              }
              if (checkPermissions($_SESSION["user_id"], 11) == "true") {
                ?>
                <li class="nav-item">
                  <a href="/allowance_list/emp_allowance" class="nav-link <?= ($second_part == 'emp_allowance') ? 'active':''; ?>">
                    <i class="far fa-circle nav-icon text-success"></i>
                    <p>Employee Allowance</p>
                  </a>
                </li>
                <?php
              }
              if (checkPermissions($_SESSION["user_id"], 9) == "true") {
                ?>
                <li class="nav-item">
                  <a href="/allowance_list/add_emp_allowance" class="nav-link <?= ($second_part == 'add_emp_allowance') ? 'active':''; ?>">
                    <i class="far fa-circle nav-icon text-primary"></i>
                    <p>Add Employee Allowance</p>
                  </a>
                </li>
                <?php
              }

              if (checkPermissions($_SESSION["user_id"], 37) == "true") {
              ?>
              <li class="nav-item">
                <a href="/allowance_list/shifts_allowance" class="nav-link <?= ($second_part == 'shifts_allowance') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-secondary"></i>
                  <p>Shifts Allowance</p>
                </a>
              </li>
              <?php
              }

              if (checkPermissions($_SESSION["user_id"], 37) == "true") {
              ?>
              <li class="nav-item">
                <a href="/allowance_list/shifts_allowance_emp" class="nav-link <?= ($second_part == 'shifts_allowance_emp') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-info"></i>
                  <p>Shifts Allowance Emp</p>
                </a>
              </li>
              <?php
              }
              
              ?>
            </ul>
          </li>

          <?php
          }

          if ((checkPermissions($_SESSION["user_id"], 13) == "true") OR (checkPermissions($_SESSION["user_id"], 15) == "true") OR (checkPermissions($_SESSION["user_id"], 17) == "true") OR (checkPermissions($_SESSION["user_id"], 19) == "true")){
          ?>

          <li class="nav-item has-treeview <?= ($first_part == 'deduction_list') ? 'menu-open':''; ?>">
            <a href="#" class="nav-link <?= ($first_part == 'deduction_list') ? 'active':''; ?>">
              <i class="nav-icon fas fa-money-bill-alt text-danger"></i>
              <p>
                Deduction List
                <i class="fas fa-angle-left right"></i>                
              </p>
            </a>
            <ul class="nav nav-treeview">
              <?php
              if (checkPermissions($_SESSION["user_id"], 15) == "true") {
                ?>
                <li class="nav-item">
                  <a href="/deduction_list/deduction" class="nav-link <?= ($second_part == 'deduction') ? 'active':''; ?>">
                    <i class="far fa-circle nav-icon text-danger"></i>
                    <p>Deduction</p>
                  </a>
                </li>
                <?php
              }
              if (checkPermissions($_SESSION["user_id"], 13) == "true") {
                ?>
                <li class="nav-item">
                  <a href="/deduction_list/add_deduction" class="nav-link <?= ($second_part == 'add_deduction') ? 'active':''; ?>">
                    <i class="far fa-circle nav-icon text-warning"></i>
                    <p>Add Deduction</p>
                  </a>
                </li>
                <?php
              }
              if (checkPermissions($_SESSION["user_id"], 19) == "true") {
                ?>
                <li class="nav-item">
                  <a href="/deduction_list/emp_deduction" class="nav-link <?= ($second_part == 'emp_deduction') ? 'active':''; ?>">
                    <i class="far fa-circle nav-icon text-success"></i>
                    <p>Employee Deduction</p>
                  </a>
                </li>
                <?php
              }
              if (checkPermissions($_SESSION["user_id"], 17) == "true") {
                ?>
                <li class="nav-item">
                  <a href="/deduction_list/add_emp_deduction" class="nav-link <?= ($second_part == 'add_emp_deduction') ? 'active':''; ?>">
                    <i class="far fa-circle nav-icon text-primary"></i>
                    <p>Add Employee Deduction</p>
                  </a>
                </li> 
                <?php
              }
              if (checkPermissions($_SESSION["user_id"], 68) == "true") {
              ?>
              <li class="nav-item">
                <a href="/deduction_list/uniforms_ded" class="nav-link <?= ($second_part == 'uniforms_ded') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-danger"></i>
                  <p>Uniforms Deduction</p>
                </a>
              </li>
              <?php
              }

              if (checkPermissions($_SESSION["user_id"], 91) == "true") {
              ?>
              <li class="nav-item">
                <a href="/deduction_list/clothes_ded" class="nav-link <?= ($second_part == 'clothes_ded') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-warning"></i>
                  <p>Add Uniforms Deduction</p>
                </a>
              </li>
              <?php
              }

              ?>             
            </ul>
          </li>

          <?php
          }

          if ((checkPermissions($_SESSION["user_id"], 48) == "true") OR (checkPermissions($_SESSION["user_id"], 46) == "true") OR (checkPermissions($_SESSION["user_id"], 52) == "true") OR (checkPermissions($_SESSION["user_id"], 50) == "true")){
          ?>

          <li class="nav-item has-treeview <?= ($first_part == 'ration') ? 'menu-open':''; ?>">
            <a href="#" class="nav-link <?= ($first_part == 'ration') ? 'active':''; ?>">
              <i class="nav-icon fas fa-bread-slice text-primary"></i>
              <p>
                Ration
                <i class="fas fa-angle-left right"></i>                
              </p>
            </a>
            <ul class="nav nav-treeview">
              <?php
              
              if (checkPermissions($_SESSION["user_id"], 48) == "true") {
              ?>
              <li class="nav-item">
                <a href="/ration/supplier_list" class="nav-link <?= ($second_part == 'supplier_list') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-danger"></i>
                  <p>Supplier List</p>
                </a>
              </li>
              <?php
              }
              if (checkPermissions($_SESSION["user_id"], 46) == "true") {
              ?>
              <li class="nav-item">
                <a href="/ration/add_supplier" class="nav-link <?= ($second_part == 'add_supplier') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-warning"></i>
                  <p>Add Supplier</p>
                </a>
              </li>
              <?php
              }
              /*if (checkPermissions($_SESSION["user_id"], 52) == "true") {
              ?>
              <li class="nav-item">
                <a href="/ration/emp_ration" class="nav-link <?= ($second_part == 'emp_ration') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-danger"></i>
                  <p>Employee Ration</p>
                </a>
              </li>
              <?php
              }
              if (checkPermissions($_SESSION["user_id"], 50) == "true") {
              ?>
              <li class="nav-item">
                <a href="/ration/add_emp_ration" class="nav-link <?= ($second_part == 'add_emp_ration') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-warning"></i>
                  <p>Add Employee Ration</p>
                </a>
              </li> 
              <?php
              }*/
              
              ?>             
            </ul>
          </li>

          <?php
          }
          
          if ((checkPermissions($_SESSION["user_id"], 57) == "true") OR (checkPermissions($_SESSION["user_id"], 54) == "true") OR (checkPermissions($_SESSION["user_id"], 61) == "true") OR (checkPermissions($_SESSION["user_id"], 58) == "true")){
          ?>

          <li class="nav-item has-treeview <?= ($first_part == 'loan') ? 'menu-open':''; ?>">
            <a href="#" class="nav-link <?= ($first_part == 'loan') ? 'active':''; ?>">
              <i class="nav-icon fas fa-money-bill-wave text-info"></i>
              <p>
                Loan
                <i class="fas fa-angle-left right"></i>                
              </p>
            </a>
            <ul class="nav nav-treeview">
              <?php
              
              if (checkPermissions($_SESSION["user_id"], 57) == "true") {
              ?>
              <li class="nav-item">
                <a href="/loan/loan_list" class="nav-link <?= ($second_part == 'loan_list') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-danger"></i>
                  <p>Loan List</p>
                </a>
              </li>
              <?php
              }
              if (checkPermissions($_SESSION["user_id"], 54) == "true") {
              ?>
              <li class="nav-item">
                <a href="/loan/new_loan_req" class="nav-link <?= ($second_part == 'new_loan_req') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-warning"></i>
                  <p>Loan Request</p>
                </a>
              </li>
              <?php
              }
              /*if (checkPermissions($_SESSION["user_id"], 61) == "true") {
              ?>
              <li class="nav-item">
                <a href="/loan/salary_advance_list" class="nav-link <?= ($second_part == 'salary_advance_list') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-success"></i>
                  <p>Salary Advance List</p>
                </a>
              </li>
              <?php
              }*/
              /*if (checkPermissions($_SESSION["user_id"], 58) == "true") {
              ?>
              <li class="nav-item">
                <a href="/loan/new_salary_advance" class="nav-link <?= ($second_part == 'new_salary_advance') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-primary"></i>
                  <p>Salary Advance</p>
                </a>
              </li> 
              <?php
              }*/
              
              ?>             
            </ul>
          </li>

          <?php
          }

          if ((checkPermissions($_SESSION["user_id"], 21) == "true") OR (checkPermissions($_SESSION["user_id"], 23) == "true")){
          ?>
          
          <li class="nav-item has-treeview <?= ($first_part == 'institution_list') ? 'menu-open':''; ?>">
            <a href="#" class="nav-link <?= ($first_part == 'institution_list') ? 'active':''; ?>">
              <i class="nav-icon fas fa-building text-success"></i>
              <p>
                Institution List
                <i class="fas fa-angle-left right"></i>                
              </p>
            </a>
            <ul class="nav nav-treeview">
              <?php
              
              if (checkPermissions($_SESSION["user_id"], 23) == "true") {
              ?>
              <li class="nav-item">
                <a href="/institution_list/institution" class="nav-link <?= ($second_part == 'institution') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-danger"></i>
                  <p>Institution</p>
                </a>
              </li>
              <?php
              }
              if (checkPermissions($_SESSION["user_id"], 21) == "true") {
              ?>
              <li class="nav-item">
                <a href="/institution_list/add_institution" class="nav-link <?= ($second_part == 'add_institution') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-warning"></i>
                  <p>Add Institution</p>
                </a>
              </li> 
              <?php
              }
              
              ?>             
            </ul>
          </li>

          <?php
          }
          
          if ((checkPermissions($_SESSION["user_id"], 29) == "true") OR (checkPermissions($_SESSION["user_id"], 32) == "true") OR (checkPermissions($_SESSION["user_id"], 92) == "true")){
          ?>

          <li class="nav-item has-treeview <?= ($first_part == 'payroll_list') ? 'menu-open':''; ?>">
            <a href="#" class="nav-link <?= ($first_part == 'payroll_list') ? 'active':''; ?>">
              <i class="nav-icon fas fa-tags text-warning"></i>              
              <p>
                Payroll List
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <?php
              
              if (checkPermissions($_SESSION["user_id"], 32) == "true") {
              ?>
              <li class="nav-item">
                <a href="/payroll_list/payroll" class="nav-link <?= ($second_part == 'payroll') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-success"></i>
                  <p>Payroll</p>
                </a>
              </li>
              <?php
              }
              if (checkPermissions($_SESSION["user_id"], 29) == "true") {
              ?>
              <li class="nav-item">
                <a href="/payroll_list/add_payroll" class="nav-link <?= ($second_part == 'add_payroll') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-primary"></i>
                  <p>Add Payroll</p>
                </a>
              </li>
              <?php
              }
              if (checkPermissions($_SESSION["user_id"], 92) == "true") {
              ?>
              <li class="nav-item">
                <a href="/payroll_list/print_pay_slip" class="nav-link <?= ($second_part == 'print_pay_slip') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-warning"></i>
                  <p>Print Pay Slip</p>
                </a>
              </li>
              <?php
              }

              if (checkPermissions($_SESSION["user_id"], 92) == "true") {
              ?>
              <li class="nav-item">
                <a href="/payroll_list/duty_summary" class="nav-link <?= ($second_part == 'duty_summary') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-secondary"></i>
                  <p>Duty Summary</p>
                </a>
              </li>
              <?php
              }

              if (checkPermissions($_SESSION["user_id"], 92) == "true") {
              ?>
              <li class="nav-item">
                <a href="/payroll_list/att_tfr" class="nav-link <?= ($second_part == 'att_tfr') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-info"></i>
                  <p>Attendance Tfr</p>
                </a>
              </li>
              <?php
              }

              ?>
            </ul>
          </li>

          <?php
          }

          if ((checkPermissions($_SESSION["user_id"], 33) == "true") OR (checkPermissions($_SESSION["user_id"], 35) == "true") OR (checkPermissions($_SESSION["user_id"], 37) == "true") OR (checkPermissions($_SESSION["user_id"], 39) == "true")){
          ?>

          <li class="nav-item has-treeview <?= ($first_part == 'position_list') ? 'menu-open':''; ?>">
            <a href="#" class="nav-link <?= ($first_part == 'position_list') ? 'active':''; ?>">
              <i class="nav-icon fas fa-calendar-check text-success"></i>
              <p>
                Position List
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <?php
              
              if (checkPermissions($_SESSION["user_id"], 35) == "true") {
              ?>
              <li class="nav-item">
                <a href="/position_list/position" class="nav-link <?= ($second_part == 'position') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-success"></i>
                  <p>Position</p>
                </a>
              </li>
              <?php
              }
              if (checkPermissions($_SESSION["user_id"], 33) == "true") {
              ?>
              <li class="nav-item">
                <a href="/position_list/add_position" class="nav-link <?= ($second_part == 'add_position') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-warning"></i>
                  <p>Add Position</p>
                </a>
              </li>
              <?php
              }
              if (checkPermissions($_SESSION["user_id"], 39) == "true") {
              ?>
              <li class="nav-item">
                <a href="/position_list/position_pay" class="nav-link <?= ($second_part == 'position_pay') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-danger"></i>
                  <p>Position Pay</p>
                </a>
              </li>
              <?php
              }
              if (checkPermissions($_SESSION["user_id"], 37) == "true") {
              ?>
              <li class="nav-item">
                <a href="/position_list/add_position_pay" class="nav-link <?= ($second_part == 'add_position_pay') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-primary"></i>
                  <p>Add Position Pay</p>
                </a>
              </li>
              <?php
              }
              if (checkPermissions($_SESSION["user_id"], 80) == "true") {
              ?>
              <li class="nav-item">
                <a href="/position_list/increment_rate" class="nav-link <?= ($second_part == 'increment_rate') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-secondary"></i>
                  <p>Increment Rate</p>
                </a>
              </li>
              <?php
              }
              if (checkPermissions($_SESSION["user_id"], 81) == "true") {
              ?>
              <li class="nav-item">
                <a href="/position_list/add_increment" class="nav-link <?= ($second_part == 'add_increment') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-info"></i>
                  <p>Add Increment Rate</p>
                </a>
              </li>
              <?php
              }
              
              ?>
            </ul>
          </li>

          <?php
          }

          if ((checkPermissions($_SESSION["user_id"], 32) == "true") OR (checkPermissions($_SESSION["user_id"], 92) == "true")){
          ?>
          
          <li class="nav-item has-treeview <?= ($first_part == 'dummy') ? 'menu-open':''; ?>">
            <a href="#" class="nav-link <?= ($first_part == 'dummy') ? 'active':''; ?>">
              <i class="nav-icon fas fa-building text-success"></i>
              <p>
                Dummy
                <i class="fas fa-angle-left right"></i>                
              </p>
            </a>
            <ul class="nav nav-treeview">
              <?php
              
              if (checkPermissions($_SESSION["user_id"], 32) == "true") {
              ?>
              <li class="nav-item">
                <a href="/dummy/d_payroll" class="nav-link <?= ($second_part == 'd_payroll') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-danger"></i>
                  <p>Payroll</p>
                </a>
              </li>
              <?php
              }
              if (checkPermissions($_SESSION["user_id"], 92) == "true") {
              ?>
              <li class="nav-item">
                <a href="/dummy/d_print_pay_slip" class="nav-link <?= ($second_part == 'd_print_pay_slip') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-warning"></i>
                  <p>Print Pay Slip</p>
                </a>
              </li> 
              <?php
              }
              if (checkPermissions($_SESSION["user_id"], 37) == "true") {
              ?>
              <li class="nav-item">
                <a href="/dummy/d_shifts_rate" class="nav-link <?= ($second_part == 'd_shifts_rate') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-success"></i>
                  <p>Shifts Type</p>
                </a>
              </li>
              <?php
              }
              if (checkPermissions($_SESSION["user_id"], 39) == "true") {
              ?>
              <li class="nav-item">
                <a href="/dummy/d_position_pay" class="nav-link <?= ($second_part == 'd_position_pay') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-primary"></i>
                  <p>Position Pay</p>
                </a>
              </li>
              <?php
              }
              if (checkPermissions($_SESSION["user_id"], 39) == "true") {
              ?>
              <li class="nav-item">
                <a href="/dummy/d_ins_merge" class="nav-link <?= ($second_part == 'd_ins_merge') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-primary"></i>
                  <p>Institute Merge</p>
                </a>
              </li>
              <?php
              }
              if (checkPermissions($_SESSION["user_id"], 11) == "true") {
                ?>
                <li class="nav-item">
                  <a href="/dummy/d_emp_allowance" class="nav-link <?= ($second_part == 'd_emp_allowance') ? 'active':''; ?>">
                    <i class="far fa-circle nav-icon text-success"></i>
                    <p>Employee Allowance</p>
                  </a>
                </li>
                <?php
              }
              if (checkPermissions($_SESSION["user_id"], 9) == "true") {
                ?>
                <li class="nav-item">
                  <a href="/dummy/d_add_emp_allowance" class="nav-link <?= ($second_part == 'd_add_emp_allowance') ? 'active':''; ?>">
                    <i class="far fa-circle nav-icon text-primary"></i>
                    <p>Add Employee Allowance</p>
                  </a>
                </li>
                <?php
              }
              ?>             
            </ul>
          </li>

          <?php
          }

          if ((checkPermissions($_SESSION["user_id"], 62) == "true") 
            OR (checkPermissions($_SESSION["user_id"], 65) == "true") 
            OR (checkPermissions($_SESSION["user_id"], 67) == "true") 
            OR (checkPermissions($_SESSION["user_id"], 66) == "true") 
            OR (checkPermissions($_SESSION["user_id"], 68) == "true")){
          ?>

          <li class="nav-item has-treeview <?= ($first_part == 'inventory') ? 'menu-open':''; ?>">
            <a href="#" class="nav-link <?= ($first_part == 'inventory') ? 'active':''; ?>">
              <i class="nav-icon fas fa-cube text-danger"></i>             
              <p>
                Inventory
                <i class="fas fa-angle-left right"></i>                
              </p>
            </a>
            <ul class="nav nav-treeview">
              <?php
              
              if (checkPermissions($_SESSION["user_id"], 65) == "true") {
              ?>
              <li class="nav-item">
                <a href="/inventory/product_list" class="nav-link <?= ($second_part == 'product_list') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-danger"></i>
                  <p>Product List</p>
                </a>
              </li>
              <?php
              }
              if (checkPermissions($_SESSION["user_id"], 62) == "true") {
              ?>
              <li class="nav-item">
                <a href="/inventory/add_product" class="nav-link <?= ($second_part == 'add_product') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-warning"></i>
                  <p>Add Product</p>
                </a>
              </li>
              <?php
              }
              if (checkPermissions($_SESSION["user_id"], 65) == "true") {
              ?>
              <li class="nav-item">
                <a href="/inventory/location" class="nav-link <?= ($second_part == 'location') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-danger"></i>
                  <p>Location</p>
                </a>
              </li>
              <?php
              }
              if (checkPermissions($_SESSION["user_id"], 67) == "true") {
              ?>
              <li class="nav-item">
                <a href="/inventory/stock" class="nav-link <?= ($second_part == 'stock') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-success"></i>
                  <p>Product Stock</p>
                </a>
              </li>
              <?php
              }
              if (checkPermissions($_SESSION["user_id"], 66) == "true") {
              ?>
              <li class="nav-item">
                <a href="/inventory/add_stock" class="nav-link <?= ($second_part == 'add_stock') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-primary"></i>
                  <p>Add Stock</p>
                </a>
              </li>
              <?php
              }

              if (checkPermissions($_SESSION["user_id"], 68) == "true") {
              ?>
              <li class="nav-item">
                <a href="/inventory/issue_sub_loc" class="nav-link <?= ($second_part == 'issue_sub_loc') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-info"></i>
                  <p>Issue to Sub Loc</p>
                </a>
              </li>  
              <?php
              }

              if (checkPermissions($_SESSION["user_id"], 68) == "true") {
              ?>
              <li class="nav-item">
                <a href="/inventory/issue_product" class="nav-link <?= ($second_part == 'issue_product') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-info"></i>
                  <p>Issue to Employee</p>
                </a>
              </li>  
              <?php
              }              

              /*if (checkPermissions($_SESSION["user_id"], 68) == "true") {
              ?>
              <li class="nav-item">
                <a href="/inventory/re_print" class="nav-link <?= ($second_part == 're_print') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-info"></i>
                  <p>Invoice Re Print</p>
                </a>
              </li>  
              <?php
              }*/
              
              ?>                          
            </ul>
          </li>

          <?php
          }

          if ((checkPermissions($_SESSION["user_id"], 69) == "true") OR (checkPermissions($_SESSION["user_id"], 72) == "true")){
          ?>

          <li class="nav-item has-treeview <?= ($first_part == 'death') ? 'menu-open':''; ?>">
            <a href="#" class="nav-link <?= ($first_part == 'death') ? 'active':''; ?>">
              <i class="nav-icon fas fa-cube text-warning"></i>             
              <p>
                Death Donation
                <i class="fas fa-angle-left right"></i>                
              </p>
            </a>
            <ul class="nav nav-treeview">
              <?php
              
              if (checkPermissions($_SESSION["user_id"], 72) == "true") {
              ?>
              <li class="nav-item">
                <a href="/death/death_list" class="nav-link <?= ($second_part == 'death_list') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-danger"></i>
                  <p>Death Donation List</p>
                </a>
              </li>
              <?php
              }
              if (checkPermissions($_SESSION["user_id"], 69) == "true") {
              ?>
              <li class="nav-item">
                <a href="/death/add_death" class="nav-link <?= ($second_part == 'add_death') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-warning"></i>
                  <p>Add Death Donation</p>
                </a>
              </li>  
              <?php
              }

              if (checkPermissions($_SESSION["user_id"], 69) == "true") {
              ?>
              <li class="nav-item">
                <a href="/death/add_donation" class="nav-link <?= ($second_part == 'add_donation') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-primary"></i>
                  <p>Add Donation to emp</p>
                </a>
              </li>  
              <?php
              }
              
              ?>                                       
            </ul>
          </li>

          <?php
          }

          if ((checkPermissions($_SESSION["user_id"], 73) == "true") OR (checkPermissions($_SESSION["user_id"], 74) == "true") OR (checkPermissions($_SESSION["user_id"], 75) == "true") OR (checkPermissions($_SESSION["user_id"], 76) == "true") OR (checkPermissions($_SESSION["user_id"], 79) == "true")){
          ?>

          <li class="nav-item has-treeview <?= ($first_part == 'payment') ? 'menu-open':''; ?>">
            <a href="#" class="nav-link <?= ($first_part == 'payment') ? 'active':''; ?>">
              <i class="nav-icon fas fa-money-bill-wave"></i>
              <p>
                Payment
                <i class="fas fa-angle-left right"></i>                
              </p>
            </a>
            <ul class="nav nav-treeview">
              <?php
              
              if (checkPermissions($_SESSION["user_id"], 73) == "true") {
              ?>
              <li class="nav-item">
                <a href="/payment/salary" class="nav-link <?= ($second_part == 'salary') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-danger"></i>
                  <p>Salary</p>
                </a>
              </li>
              <?php
              }

              if (checkPermissions($_SESSION["user_id"], 73) == "true") {
              ?>
              <li class="nav-item">
                <a href="/payment/halt_release" class="nav-link <?= ($second_part == 'halt_release') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-danger"></i>
                  <p>Halt Release</p>
                </a>
              </li>
              <?php
              }

              if (checkPermissions($_SESSION["user_id"], 74) == "true") {
              ?>
              <li class="nav-item">
                <a href="/payment/supplier" class="nav-link <?= ($second_part == 'supplier') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-primary"></i>
                  <p>Ration Supplier</p>
                </a>
              </li>
              <?php
              }
              if (checkPermissions($_SESSION["user_id"], 75) == "true") {
              ?>
              <li class="nav-item">
                <a href="/payment/loan" class="nav-link <?= ($second_part == 'loan') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-success"></i>
                  <p>Loan</p>
                </a>
              </li>
              <?php
              }
              if (checkPermissions($_SESSION["user_id"], 76) == "true") {
              ?>
              <li class="nav-item">
                <a href="/payment/advance" class="nav-link <?= ($second_part == 'advance') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-warning"></i>
                  <p>Advance</p>
                </a>
              </li> 
              <?php
              }

              if (checkPermissions($_SESSION["user_id"], 76) == "true") {
                ?>
                <li class="nav-item">
                  <a href="/payment/advance_halt" class="nav-link <?= ($second_part == 'advance_halt') ? 'active':''; ?>">
                    <i class="far fa-circle nav-icon text-warning"></i>
                    <p>Advance (Halt)</p>
                  </a>
                </li> 
                <?php
                }

              if (checkPermissions($_SESSION["user_id"], 79) == "true") {
              ?>
              <li class="nav-item">
                <a href="/payment/epf" class="nav-link <?= ($second_part == 'epf') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-info"></i>
                  <p>EPF</p>
                </a>
              </li> 
              <?php
              }

              if (checkPermissions($_SESSION["user_id"], 82) == "true") {
              ?>
              <li class="nav-item">
                <a href="/payment/etf" class="nav-link <?= ($second_part == 'etf') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-info"></i>
                  <p>ETF</p>
                </a>
              </li> 
              <?php
              }
              if (checkPermissions($_SESSION["user_id"], 73) == "true") {
              ?>
              <li class="nav-item">
                <a href="/payment/resignations" class="nav-link <?= ($second_part == 'resignations') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-danger"></i>
                  <p>Resignation</p>
                </a>
              </li>
              <?php
              }
              
              ?>             
            </ul>
          </li>
          <?php
          }

          if ((checkPermissions($_SESSION["user_id"], 85) == "true") OR (checkPermissions($_SESSION["user_id"], 89) == "true") OR (checkPermissions($_SESSION["user_id"], 98) == "true") OR (checkPermissions($_SESSION["user_id"], 102) == "true")){
            ?>

            <li class="nav-item has-treeview <?= ($first_part == 'settings') ? 'menu-open':''; ?>">
              <a href="#" class="nav-link <?= ($first_part == 'settings') ? 'active':''; ?>">
                <i class="nav-icon fas fa-cogs"></i>
                <p>
                  Settings
                  <i class="fas fa-angle-left right"></i>                
                </p>
              </a>
              <ul class="nav nav-treeview">
                <?php
                
                if (checkPermissions($_SESSION["user_id"], 85) == "true") {
                ?>
                <li class="nav-item">
                  <a href="/settings/bank_list" class="nav-link <?= ($second_part == 'bank_list') ? 'active':''; ?>">
                    <i class="far fa-circle nav-icon text-danger"></i>
                    <p>Bank List</p>
                  </a>
                </li>
                <?php
                }
                if (checkPermissions($_SESSION["user_id"], 89) == "true") {
                ?>
                <li class="nav-item">
                  <a href="/settings/bank_branch_list" class="nav-link <?= ($second_part == 'bank_branch_list') ? 'active':''; ?>">
                    <i class="far fa-circle nav-icon text-warning"></i>
                    <p>Bank Branch List</p>
                  </a>
                </li> 
                <?php
                }

                if (checkPermissions($_SESSION["user_id"], 98) == "true") {
                ?>
                <li class="nav-item">
                  <a href="/settings/police_list" class="nav-link <?= ($second_part == 'police_list') ? 'active':''; ?>">
                    <i class="far fa-circle nav-icon text-secondary"></i>
                    <p>Police List</p>
                  </a>
                </li> 
                <?php
                }
                
                if (checkPermissions($_SESSION["user_id"], 102) == "true") {
                ?>
                <li class="nav-item">
                  <a href="/settings/gn_division_list" class="nav-link <?= ($second_part == 'gn_division_list') ? 'active':''; ?>">
                    <i class="far fa-circle nav-icon text-primary"></i>
                    <p>GN Division List</p>
                  </a>
                </li> 
                <?php
                }

                if (checkPermissions($_SESSION["user_id"], 102) == "true") {
                ?>
                <li class="nav-item">
                  <a href="/settings/sector_list" class="nav-link <?= ($second_part == 'sector_list') ? 'active':''; ?>">
                    <i class="far fa-circle nav-icon text-success"></i>
                    <p>Sector List</p>
                  </a>
                </li> 
                <?php
                }

                if (checkPermissions($_SESSION["user_id"], 102) == "true") {
                ?>
                <li class="nav-item">
                  <a href="/settings/invoice_rate" class="nav-link <?= ($second_part == 'invoice_rate') ? 'active':''; ?>">
                    <i class="far fa-circle nav-icon text-info"></i>
                    <p>Invoice Rate</p>
                  </a>
                </li> 
                <?php
                }

                if (checkPermissions($_SESSION["user_id"], 37) == "true") {
              ?>
              <li class="nav-item">
                <a href="/settings/shifts_rate" class="nav-link <?= ($second_part == 'shifts_rate') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-danger"></i>
                  <p>Shifts Rate</p>
                </a>
              </li>
              <?php
              }            

                ?>             
              </ul>
            </li>

            <?php
          }
          if ((checkPermissions($_SESSION["user_id"], 41) == "true") OR (checkPermissions($_SESSION["user_id"], 43) == "true") OR (checkPermissions($_SESSION["user_id"], 45) == "true")){
          ?>
          <li class="nav-item has-treeview <?= ($first_part == 'users') ? 'menu-open':''; ?>">
            <a href="#" class="nav-link <?= ($first_part == 'users') ? 'active':''; ?>">
              <i class="nav-icon fas fa-users"></i>
              <p>
                Users
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <?php
              
              if (checkPermissions($_SESSION["user_id"], 43) == "true") {
              ?>
              <li class="nav-item">
                <a href="/users/list" class="nav-link <?= ($second_part == 'list') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Users List</p>
                </a>
              </li>
              <?php
              }
              /*if (checkPermissions($_SESSION["user_id"], 41) == "true") {
              ?>
              <li class="nav-item">
                <a href="/users/add_users" class="nav-link <?= ($second_part == 'add_users') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Add Users</p>
                </a>
              </li>
              <?php
              }*/
              if (checkPermissions($_SESSION["user_id"], 45) == "true") {
              ?>
              <li class="nav-item">
                <a href="/users/role" class="nav-link <?= ($second_part == 'role') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Role</p>
                </a>
              </li>
              <?php
              }
              ?>
              
            </ul>
          </li>
          <?php
          }
          if ((checkPermissions($_SESSION["user_id"], 95) == "true") OR (checkPermissions($_SESSION["user_id"], 104) == "true") OR (checkPermissions($_SESSION["user_id"], 105) == "true")){
          ?>  
          <li class="nav-item has-treeview <?= ($first_part == 'reports') ? 'menu-open':''; ?>">
            <a href="#" class="nav-link <?= ($first_part == 'reports') ? 'active':''; ?>">
              <i class="nav-icon fas fa-bar-chart"></i>
              <p>
                Reports
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item has-treeview <?= ($second_part == 'advance') ? 'menu-open':''; ?>">
                <a href="#" class="nav-link <?= ($second_part == 'advance') ? 'active':''; ?>">
                  <i class="fas fa-money-bill-wave nav-icon text-primary"></i>
                  <p>
                    Salary Advance
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <?php
                  if (checkPermissions($_SESSION["user_id"], 105) == "true") {
                  ?>
                  <li class="nav-item">
                    <a href="/reports/advance/summary" class="nav-link <?= ($third_part == 'summary') ? 'active':''; ?>">
                      <i class="far fa-circle nav-icon text-warning"></i>
                      <p>Summary</p>
                    </a>
                  </li>
                 <?php
                  }
                  if (checkPermissions($_SESSION["user_id"], 105) == "true") {
                  ?>
                  <li class="nav-item">
                    <a href="/reports/advance/deposit_bank" class="nav-link <?= ($third_part == 'deposit_bank') ? 'active':''; ?>">
                      <i class="far fa-circle nav-icon text-success"></i>
                      <p>Deposit Bank</p>
                    </a>
                  </li>
                 <?php
                  }
                  if (checkPermissions($_SESSION["user_id"], 105) == "true") {
                  ?>
                  <li class="nav-item">
                    <a href="/reports/advance/cash_hand" class="nav-link <?= ($third_part == 'cash_hand') ? 'active':''; ?>">
                      <i class="far fa-circle nav-icon text-primary"></i>
                      <p>Cash Hand</p>
                    </a>
                  </li>
                 <?php
                  }
                  if (checkPermissions($_SESSION["user_id"], 105) == "true") {
                  ?>
                  <li class="nav-item">
                    <a href="/reports/advance/ins_wise" class="nav-link <?= ($third_part == 'ins_wise') ? 'active':''; ?>">
                      <i class="far fa-circle nav-icon text-danger"></i>
                      <p>Institute Wise</p>
                    </a>
                  </li>
                 <?php
                  }
                  if (checkPermissions($_SESSION["user_id"], 105) == "true") {
                  ?>
                  <li class="nav-item">
                    <a href="/reports/advance/request_form" class="nav-link <?= ($third_part == 'request_form') ? 'active':''; ?>">
                      <i class="far fa-circle nav-icon text-danger"></i>
                      <p>Request Form</p>
                    </a>
                  </li>
                 <?php
                  }
                  ?>
                  
                  <!-- <li class="nav-item">
                    <a href="#" class="nav-link">
                      <i class="far fa-dot-circle nav-icon"></i>
                      <p>Level 3</p>
                    </a>
                  </li> -->
                </ul>
              </li>
              <li class="nav-item has-treeview <?= ($second_part == 'payroll_list') ? 'menu-open':''; ?>">
                <a href="#" class="nav-link <?= ($second_part == 'payroll_list') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-danger"></i>
                  <p>
                    Payroll List
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
              <?php
              
              if (checkPermissions($_SESSION["user_id"], 95) == "true") {
              ?>
              <li class="nav-item">
                <a href="/reports/payroll_list/payrolls" class="nav-link <?= ($third_part == 'payrolls') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-warning"></i>
                  <p>Payroll</p>
                </a>
              </li>
             <?php
              }

              if (checkPermissions($_SESSION["user_id"], 95) == "true") {
              ?>
              <li class="nav-item">
                <a href="/reports/payroll_list/payrolls_ins" class="nav-link <?= ($third_part == 'payrolls_ins') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-info"></i>
                  <p>Payroll Ins wise</p>
                </a>
              </li>
             <?php
              }
              /*if (checkPermissions($_SESSION["user_id"], 95) == "true") {
              ?>
              <li class="nav-item">
                <a href="/reports/payroll_list/payrolls_halt" class="nav-link <?= ($third_part == 'payrolls_halt') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-danger"></i>
                  <p>Payroll Halt</p>
                </a>
              </li>
             <?php
              }*/
              if (checkPermissions($_SESSION["user_id"], 95) == "true") {
                  ?>
                  <li class="nav-item">
                    <a href="/reports/payroll_list/pdeposit_bank" class="nav-link <?= ($third_part == 'pdeposit_bank') ? 'active':''; ?>">
                      <i class="far fa-circle nav-icon text-success"></i>
                      <p>Deposit Bank</p>
                    </a>
                  </li>
                 <?php
                  }
                  if (checkPermissions($_SESSION["user_id"], 95) == "true") {
                  ?>
                  <li class="nav-item">
                    <a href="/reports/payroll_list/pcash_hand" class="nav-link <?= ($third_part == 'pcash_hand') ? 'active':''; ?>">
                      <i class="far fa-circle nav-icon text-primary"></i>
                      <p>Cash Hand</p>
                    </a>
                  </li>
                 <?php
                  }
              ?>
              </ul>
              </li>

              <li class="nav-item has-treeview <?= ($second_part == 'allowance') ? 'menu-open':''; ?>">
                <a href="#" class="nav-link <?= ($second_part == 'allowance') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-info"></i>
                  <p>
                    Allowance List
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
              <?php
              if (checkPermissions($_SESSION["user_id"], 95) == "true") {
              ?>
              <li class="nav-item">
                <a href="/reports/allowance/asummary" class="nav-link <?= ($third_part == 'asummary') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Summary</p>
                </a>
              </li>
             <?php
              }
              if (checkPermissions($_SESSION["user_id"], 95) == "true") {
              ?>
              <li class="nav-item">
                <a href="/reports/allowance/all_allowances" class="nav-link <?= ($third_part == 'all_allowances') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-success"></i>
                  <p>All Allowances</p>
                </a>
              </li>
             <?php
              }

              ?>
              </ul>
              </li>

              <li class="nav-item has-treeview <?= ($second_part == 'deduction') ? 'menu-open':''; ?>">
                <a href="#" class="nav-link <?= ($second_part == 'deduction') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-warning"></i>
                  <p>
                    Deduction List
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
              <?php
              if (checkPermissions($_SESSION["user_id"], 95) == "true") {
              ?>
              <li class="nav-item">
                <a href="/reports/deduction/dsummary" class="nav-link <?= ($third_part == 'dsummary') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Summary</p>
                </a>
              </li>
             <?php
              }

              if (checkPermissions($_SESSION["user_id"], 95) == "true") {
              ?>
              <li class="nav-item">
                <a href="/reports/deduction/all_deduction" class="nav-link <?= ($third_part == 'all_deduction') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-danger"></i>
                  <p>All Deduction</p>
                </a>
              </li>
             <?php
              }
              
              if (checkPermissions($_SESSION["user_id"], 3) == "true") {
              ?>
              <li class="nav-item">
                <a href="/reports/deduction/ration_depwise" class="nav-link <?= ($third_part == 'ration_depwise') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-info"></i>
                  <p>Ration Institute Wise</p>
                </a>
              </li>
             <?php
              }

              if (checkPermissions($_SESSION["user_id"], 3) == "true") {
              ?>
              <li class="nav-item">
                <a href="/reports/deduction/ration_form" class="nav-link <?= ($third_part == 'ration_form') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-success"></i>
                  <p>Ration form</p>
                </a>
              </li>
             <?php
              }

              if (checkPermissions($_SESSION["user_id"], 3) == "true") {
              ?>
              <li class="nav-item">
                <a href="/reports/deduction/uniforms" class="nav-link <?= ($third_part == 'uniforms') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-primary"></i>
                  <p>Uniforms</p>
                </a>
              </li>
             <?php
              }

              if (checkPermissions($_SESSION["user_id"], 3) == "true") {
              ?>
              <li class="nav-item">
                <a href="/reports/deduction/uniforms_sum" class="nav-link <?= ($third_part == 'uniforms_sum') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-warning"></i>
                  <p>Uniforms Summary</p>
                </a>
              </li>
             <?php
              }

              if (checkPermissions($_SESSION["user_id"], 3) == "true") {
              ?>
              <li class="nav-item">
                <a href="/reports/deduction/loan" class="nav-link <?= ($third_part == 'loan') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-secondary"></i>
                  <p>Loan</p>
                </a>
              </li>
             <?php
              }
              if (checkPermissions($_SESSION["user_id"], 3) == "true") {
              ?>
              <li class="nav-item">
                <a href="/reports/deduction/loan_month" class="nav-link <?= ($third_part == 'loan_month') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Loan Month Wise</p>
                </a>
              </li>
             <?php
              }
              if (checkPermissions($_SESSION["user_id"], 3) == "true") {
              ?>
              <li class="nav-item">
                <a href="/reports/deduction/death" class="nav-link <?= ($third_part == 'death') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-danger"></i>
                  <p>Death</p>
                </a>
              </li>
             <?php
              }
              ?>
              </ul>
              </li>
              <li class="nav-item has-treeview <?= ($second_part == 'misc') ? 'menu-open':''; ?>">
                <a href="#" class="nav-link <?= ($second_part == 'misc') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-success"></i>
                  <p>
                    Miscellaneous
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
              <?php

              if (checkPermissions($_SESSION["user_id"], 95) == "true") {
              ?>
              <li class="nav-item">
                <a href="/reports/misc/invoice" class="nav-link <?= ($third_part == 'invoice') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Invoice</p>
                </a>
              </li>
             <?php
              }

              if (checkPermissions($_SESSION["user_id"], 104) == "true") {
              ?>
              <li class="nav-item">
                <a href="/reports/misc/ins_pay" class="nav-link <?= ($third_part == 'ins_pay') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-primary"></i>
                  <p>Institute Pay</p>
                </a>
              </li>
             <?php
              }

              if (checkPermissions($_SESSION["user_id"], 104) == "true") {
              ?>
              <li class="nav-item">
                <a href="/reports/misc/to_be_applied" class="nav-link <?= ($third_part == 'to_be_applied') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-primary"></i>
                  <p>To be Applied</p>
                </a>
              </li>
             <?php
              }
              
              if (checkPermissions($_SESSION["user_id"], 3) == "true") {
              ?>
              <li class="nav-item">
                <a href="/reports/misc/employee_list" class="nav-link <?= ($third_part == 'employee_list') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-danger"></i>
                  <p>Employee List</p>
                </a>
              </li>
             <?php
              }              
              
              if (checkPermissions($_SESSION["user_id"], 3) == "true") {
              ?>
              <li class="nav-item">
                <a href="/reports/misc/svc_count" class="nav-link <?= ($third_part == 'svc_count') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Service Count</p>
                </a>
              </li>
             <?php
              }
              if (checkPermissions($_SESSION["user_id"], 3) == "true") {
              ?>
              <li class="nav-item">
                <a href="/reports/misc/positions_pay" class="nav-link <?= ($third_part == 'positions_pay') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-info"></i>
                  <p>Position Pay</p>
                </a>
              </li>
             <?php
              }

              if (checkPermissions($_SESSION["user_id"], 3) == "true") {
              ?>
              <li class="nav-item">
                <a href="/reports/misc/working_shifts_pay" class="nav-link <?= ($third_part == 'working_shifts_pay') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-info"></i>
                  <p>Working Shifts Pay</p>
                </a>
              </li>
             <?php
              }

              if (checkPermissions($_SESSION["user_id"], 3) == "true") {
              ?>
              <li class="nav-item">
                <a href="/reports/misc/epf_etf" class="nav-link <?= ($third_part == 'epf_etf') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon text-info"></i>
                  <p>EPF / ETF</p>
                </a>
              </li>
             <?php
              }

              if (checkPermissions($_SESSION["user_id"], 3) == "true") {
                ?>
                <li class="nav-item">
                  <a href="/reports/misc/etf" class="nav-link <?= ($third_part == 'etf') ? 'active':''; ?>">
                    <i class="far fa-circle nav-icon text-info"></i>
                    <p>ETF</p>
                  </a>
                </li>
               <?php
                }
              ?>
              <!-- <li class="nav-item">
                <a href="/users/role" class="nav-link <?= ($second_part == 'role') ? 'active':''; ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Role</p>
                </a>
              </li> -->
              </ul>
              </li>
              
            </ul>
          </li>
          <?php
          }
          ?>       
                    
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->