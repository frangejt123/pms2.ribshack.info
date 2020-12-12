<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Ribshack | Weekly Data</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
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
              <span class="hidden-xs"><?php echo strtoupper($_SESSION["rgc_firstname"]." ".$_SESSION["rgc_lastname"]); ?></span>
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
			<li class="active">
				<a href="#">
					<i class="fa fa-calendar"></i> <span>Weekly Data</span>
				</a>
			</li>
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
    <!-- Content Header (Page header) -->
    <section class="content-header">
		<div class="row">
			<div class="col-md-6">
				<?php
					$cls = "";
					$style = "";
					if($_SESSION["rgc_access_level"] == 0){
						echo '<span class="pull-left">
								  <div class="form-group" id="period_branch_form">
									 <select class="select2 js-states form-control" style="width: 185px;" id="period_branch">
									  </select>
								  </div>
								</span>';
						$cls = " disabled";
						$style = "margin-left: 16px;";
					}
				?>
				<span class="pull-left" style="<?php echo $style; ?>">
					<div class="row" id="week_data_datepicker">
						<input type="text" class="form-control datepicker" id="week_data_date" value="" placeholder="Filter Date">
					</div>
				</span>
			</div>
		</div>
    </section>

    <!-- Main content -->
    <section class="content">
		<div class="row">
			<div class="col-md-4">
				<div class="box box-danger">
					<div class="box-header with-border">
						<h3 class="box-title">Raw Materials</h3>
					</div>
					<!-- /.box-header -->
					<div class="box-body">
						<table class="table table-hover" id="raw_material_tbl">
							<thead>
								<th>Description</th>
								<th>UoM</th>
								<th>Week Total</th>
								<th>Week Avg</th>
							</thead>
							<tbody>
								<!-- raw materials data -->
							</tbody>
						</table>
					</div>
					<!-- /.box-body -->
				</div>
				<!-- /.box -->
			</div>
			<!-- /.col -->

			<div class="col-md-4">
				<div class="box box-success">
					<div class="box-header with-border">
						<h3 class="box-title">Premix & Sauce</h3>
					</div>
					<!-- /.box-header -->
					<div class="box-body">
						<table class="table table-hover" id="premix_sauce_tbl">
							<thead>
								<th>Description</th>
								<th>UoM</th>
								<th>Week Total</th>
								<th>Week Avg</th>
							</thead>
							<tbody>
							<!-- raw materials data -->
							</tbody>
						</table>
					</div>
					<!-- /.box-body -->
				</div>
				<!-- /.box -->
			</div>
			<!-- /.col -->

			<div class="col-md-4">
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">Drinks</h3>
					</div>
					<!-- /.box-header -->
					<div class="box-body">
						<table class="table table-hover" id="drinks_tbl">
							<thead>
								<th>Description</th>
								<th>UoM</th>
								<th>Week Total</th>
								<th>Week Avg</th>
							</thead>
							<tbody>
							<!-- raw materials data -->
							</tbody>
						</table>
					</div>
					<!-- /.box-body -->
				</div>
				<!-- /.box -->
			</div>
			<!-- /.col -->

		</div>

		<div class="row">

			<div class="col-md-12">
				<div class="box box-warning">
					<div class="box-header with-border">
						<h3 class="box-title">Weekly Data</h3>


						<div class="pull-right">
							<div class="form-group">
								<input type="text" class="form-control" placeholder="Search...">
							</div>
						</div>
					</div>
					<!-- /.box-header -->
					<div class="box-body">
						<table class="table table-hover" id="weekly_pms_tbl">
							<thead>
								<tr>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
					<!-- /.box-body -->
				</div>
				<!-- /.box -->
			</div>
			<!-- /.col -->

		</div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

	<!-- modal [change pwd] -->
	<div class="modal fade" id="changepass_modal" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span></button>
					<h4 class="modal-title"><span
								style="border-radius: 2px; padding: 6px;
            border: 1px solid #008d4c; background-color: #00a65a; color: #FFF";
								class="fa fa-key"></span> &nbsp; <b>Change Password</b></h4>
				</div>
				<div class="modal-body">
					<form role="form" id="changePasswordForm">
						<div class="box-body">
							<div class="form-group">
								<label for="current_password">Current Password</label>
								<input type="password" class="form-control" id="current_password">
							</div>

							<div class="form-group">
								<label for="new_password">New Password</label>
								<input type="password" class="form-control" id="new_password">
							</div>

							<div class="form-group">
								<label for="confirm_new_password">Confirm New Password</label>
								<input type="password" class="form-control" id="confirm_new_password">
							</div>

						</div>
					</form>
				</div><!-- body -->
				<div class="modal-footer">
					<div id="footer">
						<div class="btn-group btn-group-justified" id="form-mode-buttons" role="group" >
							<button type="button" id="clear_changepass" class="btn btn-default" style="width:49%">
								<i class="fa fa-undo"></i>&nbsp; Clear
							</button>
							<button type="button" id="changepassword_submitbtn" class="btn btn-primary" data-key-method="ok" style="width:49%">
								<i class="fa fa-save"></i>&nbsp; Save
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- modal -->

	<footer class="main-footer">
		<div class="pull-right hidden-xs">
			<b>v1.0</b>
		</div>
		<strong><a href="http://www.delcom.com.ph/" target="_blank">Delcom Systems and  Solution Corporation</a>.</strong> All rights reserved 2019.
	</footer>
</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="<?php echo base_url(); ?>assets/bower_components/jquery/dist/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?php echo base_url(); ?>assets/bower_components/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script src="<?php echo base_url(); ?>assets/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url(); ?>assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script>
  $.widget.bridge('uibutton', $.ui.button);
  var baseurl = '<?php echo base_url(); ?>'+'index.php';
  var access_level = '<?php echo $_SESSION["rgc_access_level"]; ?>';
  var userbranch = '<?php echo $_SESSION["rgc_branch_id"]; ?>';
  // $("a#sign_out_btn").on("click", function(){
	//   window.location = baseurl + "?out=1"
  // });
</script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- daterangepicker -->
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
<!-- AdminLTE App -->
<script src="<?php echo base_url(); ?>assets/dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo base_url(); ?>assets/dist/js/demo.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo base_url(); ?>assets/dist/js/app.js"></script>
<script src="<?php echo base_url(); ?>assets/dist/js/select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/dist/js/weeklyview.js"></script>
</body>
</html>
