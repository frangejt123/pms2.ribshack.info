<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Ribshack | Reports</title>
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
		<a href="#" class="logo">
			<!-- mini logo for sidebar mini 50x50 pixels -->
			<span class="logo-mini"><b>RGC</b></span>
			<!-- logo for regular state and mobile devices -->
			<span class="logo-lg"><b>RIBSHACK</b></span>
		</a>
		<!-- Header Navbar: style can be found in header.less -->
		<nav class="navbar navbar-static-top">
			<!-- Sidebar toggle button-->
			<a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
				<span class="sr-only">Toggle navigation</span>
			</a>

			<div class="navbar-custom-menu">
				<ul class="nav navbar-nav">
					<!-- User Account: style can be found in dropdown.less -->
					<li class="dropdown user user-menu">
						<a href="<?php echo base_url(); ?>assets/#" class="dropdown-toggle" data-toggle="dropdown">
							<span class="hidden-xs">USER</span>
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
			<ul class="sidebar-menu" data-widget="tree">
				<li>
					<a href="<?php echo base_url(); ?>index.php/dashboard">
						<i class="fa fa-dashboard"></i> <span>Dashboard</span>
					</a>
				</li>
				<li>
					<a href="<?php echo base_url(); ?>index.php/weekview">
						<i class="fa fa-calendar"></i> <span>Weekly Data</span>
					</a>
				</li>
				<li>
					<a href="<?php echo base_url(); ?>index.php/productmovement">
						<i class="fa fa-bar-chart"></i> <span>Product Movement</span>
					</a>
				</li>
				<li class="active">
					<a href="#">
						<i class="fa fa-file-text-o"></i> <span>Reports</span>
					</a>
				</li>
				<li class="header"></li>
				<li><a href="<?php echo base_url(); ?>index.php/product"><i class="fa fa-cubes"></i> <span>Product</span></a></li>
				<li><a href="<?php echo base_url(); ?>index.php/uom"><i class="fa fa-sliders"></i> <span>Unit of Measurement</span></a></li>
				<li><a href="<?php echo base_url(); ?>index.php/branch"><i class="fa fa-home"></i> <span>Branch</span></a></li>
				<?php
				if($_SESSION["rgc_access_level"] == 0){
					echo '<li><a href="'.base_url().'index.php/rawmaterial"><i class="fa fa-asterisk"></i> <span>Raw Materials</span></a></li>';
					echo '<li><a href="'.base_url().'index.php/conversion"><i class="fa fa-balance-scale"></i> <span>Conversion</span></a></li>';
					echo '<li><a href="'.base_url().'index.php/userlist"><i class="fa fa-users"></i> <span>User List</span></a></li>';
				}
				?>
				<li class="header"></li>
				<li><a href="#" id="changepass_btn"><i class="fa fa-key"></i> <span>Change Password</span></a></li>
			</ul>
		</section>
		<!-- /.sidebar -->
	</aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-lg-3">
          <div class="box">
           <div class="box-body no-padding">
                <form role="form" id="newProductForm">
                    <div class="box-body">

                      <div class="form-group">
                        <label>Report Name</label>
                        <select class="select2 js-states form-control" id="report_name">
                        </select>
                      </div>

                      <div class="form-group">
                        <label for="product_id">Date From</label>
                        <div id="datepicker">
                            <div class="input-group">
                             <input type="text" class="form-control datepicker" id="report_date_from" value="" 
                             style="width: 274px">
                            </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="product_id">Date To</label>
                        <div id="datepicker">
                            <div class="input-group">
                             <input type="text" class="form-control datepicker" id="report_date_to" value="" 
                             style="width: 274px">
                            </div>
                        </div>
                      </div>

                      <?php
                        if($_SESSION["rgc_access_level"] == 0){
                          echo '<div class="form-group">
                                  <label for="product_id">Branch</label>
                                  <select class="select2 js-states form-control" id="report_branch">
                                  </select>
                                </div>';
                        }
                      ?>

                      <div class="btn-group btn-group-justified" id="form-mode-buttons" role="group" >
                          <button type="button" id="clear_new_product" class="btn btn-default" data-key-method="cancel"  style="width:49%">
                              <i class="fa fa-undo"></i>&nbsp; Clear
                          </button>
                          <button type="button" id="print_report" class="btn btn-primary" data-key-method="ok" style="width:49%">
                               <i class="fa fa-print"></i>&nbsp; Print
                          </button>
                      </div>
                    </div>
                </form>
            </div>
          </div>
        </div>

        <div class="col-lg-9">
          <div class="box" id="iframecontainer">
            <!-- report iframe -->
            
          </div>
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

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
  var reportnames = JSON.parse('<?php print_r($report); ?>');
  var access_level = '<?php echo $_SESSION["rgc_access_level"]; ?>';
  var branch_id = '<?php echo $_SESSION["rgc_branch_id"]; ?>';
  var reportbranch = JSON.parse('<?php print_r($branch); ?>');
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
<script src="<?php echo base_url(); ?>assets/dist/js/report.js"></script>
<script src="<?php echo base_url(); ?>assets/dist/js/select.min.js"></script>
</body>
</html>
