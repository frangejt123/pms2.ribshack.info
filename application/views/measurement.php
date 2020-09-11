<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Ribshack | Measurement</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/skins/_all-skins.min.css">
  <!-- Morris chart -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/morris.js/morris.css">
  <!-- jvectormap -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/jvectormap/jquery-jvectormap.css">
  <!-- Date Picker -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/bootstrap-daterangepicker/daterangepicker.css">
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/fonts.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/style.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/select.min.css">
</head>
<body class="hold-transition skin-red sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <!-- Logo -->
    <a href="<?php echo base_url(); ?>assets/index2.html" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>RGC</b></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>RIBSHACK</b></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="<?php echo base_url(); ?>assets/#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="<?php echo base_url(); ?>assets/#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?php echo base_url(); ?>assets/dist/img/user2-160x160.jpg" class="user-image" alt="User Image">
              <span class="hidden-xs">Justine Frange</span>
            </a>
            <ul class="dropdown-menu">
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <!-- <a href="<?php //echo base_url(); ?>assets/#" class="btn btn-default btn-flat">Profile</a> -->
                </div>
                <div class="pull-right">
                  <a href="javascript:void(0)" id="sign_out_btn" class="btn btn-default btn-flat">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="<?php echo base_url(); ?>assets/dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>Justine Frange</p>
        </div>
      </div>
      <!-- /.search form -->
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MAIN NAVIGATION</li>
        <!-- <li>
          <a href="<?php //echo base_url(); ?>">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
          </a>
        </li> -->
        <li>
          <a href="<?php echo base_url(); ?>index.php/productmovement">
            <i class="fa fa-bar-chart"></i> <span>Product Movement</span>
          </a>
        </li>
        <li>
          <a href="<?php echo base_url(); ?>index.php/report">
            <i class="fa fa-file-text-o"></i> <span>Reports</span>
          </a>
        </li>
        <li class="header"></li>
        <li><a href="<?php echo base_url(); ?>index.php/product"><i class="fa fa-cubes"></i> <span>Product</span></a></li>
        <li class='active'><a href="#"><i class="fa fa-sliders"></i> <span>Unit of Measurement</span></a></li>
        <?php
          if($_SESSION["rgc_access_level"] == 0){
              echo '<li><a href="'.base_url().'index.php/userlist"><i class="fa fa-users"></i> <span>User List</span></a></li>';
          }
        ?>
        <li class="header"></li>
        <li><a href="#"><i class="fa fa-key"></i> <span>Change Password</span></a></li>
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
      <span class="pull-right">
        <button type="button" class="btn btn-info" id="new_uom_btn"><i class="fa fa-plus"></i>&nbsp; Add Measurement</button>
      </span>
      <div class="clearfix"></div>
      <br/>
      <div class="row">
        <div class="col-lg-12">
          <div class="box">
           <div class="box-body no-padding">
              <table class="table table-hover" id="measurementtable">
                <thead>
                  <tr>
                    <th>Abbreviation</th>
                    <th>Description</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

<!-- modal [new product] -->
<div class="modal fade" id="new_uom_modal" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
          <h4 class="modal-title"><span 
            style="border-radius: 2px; padding: 6px;
            border: 1px solid #008d4c; background-color: #00a65a; color: #FFF"; 
            class="fa fa-sliders"></span> &nbsp; <b>New Measurement</b></h4>
        </div>
        <div class="modal-body">
          <form role="form" id="newUomForm">
            <div class="box-body">
                <div class="form-group">
                  <label for="uom_abbr">Abbreviation</label>
                  <input type="text" class="form-control" id="uom_abbr">
                </div>

                <div class="form-group">
                  <label for="uom_description">Description</label>
                  <input type="text" class="form-control" id="uom_description">
                </div>
             </div>
          </form>
        </div><!-- body -->
        <div class="modal-footer">
              <div id="footer">
                  <div class="btn-group btn-group-justified" id="form-mode-buttons" role="group" >
                      <button type="button" id="clear_new_uom" class="btn btn-default" data-key-method="cancel"  style="width:49%">
                          <i class="fa fa-undo"></i>&nbsp; Clear
                      </button>
                      <button type="button" id="newUom_submitBtn" class="btn btn-primary" data-key-method="ok" style="width:49%">
                           <i class="fa fa-save"></i>&nbsp; Save
                      </button>
                  </div>
              </div>
        </div>
      </div>
    </div>
</div>
<!-- modal -->

<!-- modal [product_detail] -->
<div class="modal fade" id="uom_detail_modal" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
          <h4 class="modal-title"><span 
            style="border-radius: 2px; padding: 6px;
            border: 1px solid #008d4c; background-color: #00a65a; color: #FFF"; 
            class="fa fa-sliders"></span> &nbsp; <b>Measurement Detail</b></h4>
        </div>
        <div class="modal-body">
          <form role="form" id="detailUomForm">
            <div class="box-body">
                <div class="form-group">
                  <label for="detail_uom_id">Abbreviation</label>
                  <input type="text" class="form-control" id="detail_uom_abbr">
                </div>

                <div class="form-group">
                  <label for="detail_uom_description">Description</label>
                  <input type="text" class="form-control" id="detail_uom_description">
                </div>
             </div>
          </form>
        </div><!-- body -->
        <div class="modal-footer">
              <div id="footer">
                  <div class="btn-group btn-group-justified" id="form-mode-buttons" role="group" >
                      <button type="button" id="delete_uom" class="btn btn-danger" style="width:49%">
                          <i class="fa fa-trash-o"></i>&nbsp; Delete
                      </button>
                      <button type="button" id="updateUom_submitBtn" class="btn btn-primary" data-key-method="ok" style="width:49%">
                           <i class="fa fa-save"></i>&nbsp; Save
                      </button>
                  </div>
              </div>
        </div>
      </div>
    </div>
</div>
<!-- modal -->

 <!-- confirm modal -->
  <!-- Modal HTML -->
  <div id="confirm_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-confirm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <span class="fa fa-trash"></span> Delete Record
          </h5>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to delete this record? 
          This action cannot be undone and you will be unable to recover any data.</p>
        </div>
        <div class="modal-footer">
          <div class="row">
            <div class="col-xs-5">
            </div>
            <div class="col-xs-7">
              <a href="#" class="btn btn-info" data-dismiss="modal">Cancel</a>
              <a href="#" class="btn btn-danger" id="confirm_delete_uom_btn">Delete</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>     
  <!-- confirm modal -->

  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>v1.0</b>
    </div>
    <strong><a href="http://www.delcom.com.ph/" target="_blank">Delcom Systems and  Solution Corporation</a>.</strong> All rights reserved 2019.
  </footer>
</div>
<!-- ./wrapper -->
<div class="progress_mask">
    <div class="progress_container">
       <div class="progress progress-md active">
          <div class="progress-bar progress-bar-warning progress-bar-striped" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
            <span class="sr-only">100% Complete</span>
          </div>
        </div>
    </div>
</div>

<!-- jQuery 3 -->
<script src="<?php echo base_url(); ?>assets/bower_components/jquery/dist/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?php echo base_url(); ?>assets/bower_components/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
  var baseurl = '<?php echo base_url(); ?>'+'index.php';
</script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Morris.js charts -->
<script src="<?php echo base_url(); ?>assets/bower_components/moment/min/moment.min.js"></script>
<script src="<?php echo base_url(); ?>assets/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<!-- datepicker -->
<script src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="<?php echo base_url(); ?>assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<script src="<?php echo base_url(); ?>assets/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?php echo base_url(); ?>assets/bower_components/fastclick/lib/fastclick.js"></script>
<script src="<?php echo base_url(); ?>assets/dist/js/bootstrap-growl.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url(); ?>assets/dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo base_url(); ?>assets/dist/js/demo.js"></script>
<script src="<?php echo base_url(); ?>assets/dist/js/app.js"></script>
<script src="<?php echo base_url(); ?>assets/dist/js/measurement.js"></script>
<script src="<?php echo base_url(); ?>assets/dist/js/select.min.js"></script>
</body>
</html>
