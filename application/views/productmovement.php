<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Ribshack | Product Movement</title>
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
				<a href="<?php echo base_url(); ?>index.php/weekview">
					<i class="fa fa-calendar"></i> <span>Weekly Data</span>
				</a>
			</li>
			<li class="active">
				<a href="#">
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
    <a href="#complete_confirm_modal" class="trigger-btn" data-toggle="modal" id="complete_confirm_trigger_btn"></a>
    <a href="#confirm_modal" class="trigger-btn" data-toggle="modal" id="confirm_trigger_btn"></a>
    <!-- Main content -->
    <section class="content">
      <?php
        $cls = "";
        if($_SESSION["rgc_access_level"] == 0){
          echo '<span class="pull-left">
                  <div class="form-group" id="period_branch_form">
                     <select class="select2 js-states form-control" style="width: 185px;" id="period_branch">
                      </select>
                  </div>
                </span>';
          $cls = " disabled";
        }
      ?>
	  <span class="pull-left" style="margin-left: 20px;">
		<div class="form-group">
	    	<div class="row" id="datepicker">
				 <input type="text" class="form-control datepicker" id="period_date" value="" placeholder="Select Date">
			</div>
		</div>
	  </span>
		<?php
			if($_SESSION["rgc_access_level"] == 0){
				echo '<span class="pull-right">
					<button type="button" class="btn btn-primary disabled" id="print_pms_btn">
					  <i class="fa fa-print"></i>&nbsp; Print</button>&nbsp; &nbsp;
				  </span>';
			}else{
		?>
      <span class="pull-right">
        <button type="button" class="btn btn-info<?php echo $cls; ?>" id="new_pms_btn">
          <i class="fa fa-plus"></i>&nbsp; New Product Movement</button>
      </span>
      <span class="pull-right">
        <button type="button" class="btn btn-success disabled" id="complete_pms_btn">
          <i class="fa fa-check"></i>&nbsp; Complete</button>&nbsp; &nbsp;
      </span>
      <span class="btn-separator pull-right"></span>
      <span class="pull-right">
        <button type="button" class="btn btn-warning disabled" id="update_pms_btn">
          <i class="fa fa-file"></i>&nbsp; Upload</button>&nbsp; &nbsp;
      </span>
      <span class="btn-separator pull-right"></span>
      <span class="pull-right">
        <button type="button" class="btn btn-danger disabled" id="delete_pms_btn">
          <i class="fa fa-trash"></i>&nbsp; Delete</button>&nbsp; &nbsp;
      </span>
		<?php
			}
		?>
      <div class="clearfix"></div>
      <br/>

      <div class="row">
<!--        <div class="col-lg-2">-->
<!--          <ul class="list-group" id="pms_date_ul">-->
<!--          </ul>-->
<!--        </div>-->

        <div class="col-lg-12">
          <div class="box">
           <div class="box-body no-padding">
              <table class="table table-hover" id="productmovementtable" style="font-size: 12px">
                <thead>
                  <tr>
                    <th style="width: 100px">Code</th>
                    <th style="width: 300px">Description</th>
				    <?php
						  for($i = 1; $i<=$poscount; $i++) {
							  echo '<th style="width: 140px">POS' . $i . '</th>';
						  }
				    ?>
					<th style="width: 140px">Total Qty</th>
                    <th style="width: 140px">Beginning</th>
                    <th style="width: 140px">Delivery</th>
                    <th style="width: 140px">Transfer In</th>
                    <th style="width: 140px">Ending</th>
                    <th style="width: 140px">Return</th>
                    <th style="width: 140px">Transfer Out</th>
                    <th style="width: 140px">Actual</th>
                    <th style="width: 140px">Discrepancy</th>
                  </tr>
                </thead>
                <tbody>
                  <tr id="row1">
                    <td colspan="7" align="center"><font style="color: #f30"><b>No data to display.</b></font></td>
                  </tr>
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



<!-- modal -->
<div class="modal fade" id="new_pms_modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-med" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
          <h4 class="modal-title"><span style="border-radius: 2px; padding: 6px; border: 1px solid #008d4c; background-color: #00a65a; color: #FFF"; 
            class="fa fa-plus"></span> &nbsp; <b>New Product Movement</b></h4>
        </div>
        <div class="modal-body">
          <form role="form">
            <div class="box-body">
                <div class="row" id="datepicker">
                    <div class="input-group">
                      <span class="input-group-addon">&nbsp; &nbsp; &nbsp; Date &nbsp; &nbsp;</span>
                     <input type="text" class="form-control datepicker" id="pms_date" value="">
                    </div>
                </div>

				<?php
				for($i = 1; $i<=$poscount; $i++){
					echo '<div class="row" id="row-browse_csv_file">
								<br />
								<div class="form-group">
									<div class="input-group">
										<label class="input-group-btn">
											<span class="btn btn-info">
												Browse&hellip; <input type="file" style="display: none;" id="csvtxtbox'.$i.'">
											</span>
										</label>
										<input type="text" id="csv_file_input'.$i.'" class="form-control fileinput pms_field" value="" readonly>
									</div>
								</div>
							</div>';
				}
				?>

                <div class="row">
                    <div id="error_cont" style='display: none;'>
                    </div>
                </div>
             </div>
          </form>
        </div><!-- body -->
        <div class="modal-footer">
              <div id="footer">
                  <div class="btn-group btn-group-justified" id="form-mode-buttons" role="group">
                    <button type="button" id="clear_pms_field" class="btn btn-default" data-key-method="cancel"  style="width:45%">
                          <i class="fa fa-trash-o"></i>&nbsp; Clear
                    </button>
                    <button type="button" id="mergeproductbtn" class="btn btn-primary" data-key-method="ok" style="width:53%;margin-right: 2px;">
                           <i class="fa fa-save"></i>&nbsp; Save
                    </button>
                  </div>
              </div>
        </div>
      </div>
    </div>
</div>
<!-- modal -->

<!-- modal -->
<div class="modal fade" id="update_pms_modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-med" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
          <h4 class="modal-title"><span style="border-radius: 2px; padding: 6px; border: 1px solid #985f0d; background-color: #f39c12; color: #FFF"; 
            class="fa fa-file"></span> &nbsp; <b>Upload .CSV File</b></h4>
        </div>
        <div class="modal-body">
          <form role="form">
            <div class="box-body">
               <?php
			   		for($i = 1; $i<=$poscount; $i++){
						echo '<div class="row" id="row-browse_csv_file">
								<br />
								<div class="form-group">
									<div class="input-group">
										<label class="input-group-btn">
											<span class="btn btn-info">
												Browse&hellip; <input type="file" style="display: none;" id="updatecsvtxtbox'.$i.'">
											</span>
										</label>
										<input type="text" id="update_csv_file_input'.$i.'" class="form-control fileinput pms_field" value="" readonly>
									</div>
								</div>
							</div>';
					}
                ?>
                <div class="row">
                    <div id="error_cont" style='display: none;'>
                    </div>
                </div>
             </div>
          </form>
        </div><!-- body -->
        <div class="modal-footer">
              <div id="footer">
                  <div class="btn-group btn-group-justified" id="form-mode-buttons" role="group">
                      <button type="button" id="clear_pms_field" class="btn btn-default" data-key-method="cancel"  style="width:45%">
                          <i class="fa fa-trash-o"></i>&nbsp; Clear
                      </button>
                      <button type="button" id="updateproductmovementdata" class="btn btn-primary" data-key-method="ok" style="width:53%;margin-right: 2px;">
                           <i class="fa fa-save"></i>&nbsp; Save
                      </button>
                  </div>
              </div>
        </div>
      </div>
    </div>
</div>
<!-- modal -->


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

<!-- confirm modal -->
  <div class="confirm_mask"></div>
  <div id="confirm_modal" class="modal fade"  
    tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-confirm">
      <div class="modal-content">
        <div class="modal-header">      
          <h5 class="modal-title">
            <span class="fa fa-trash"></span> Delete Period
          </h5>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to delete this data?
          This action cannot be undone and you will be unable to recover any data.</p>
        </div>
        <div class="modal-footer">
          <div class="row">
            <div class="col-xs-5">
              <div class="progress" style="margin-top: 7px;display: none"
                 id="delete_emp_progress">
                <div class="progress-bar progress-bar-striped 
                progress-bar-animated progress-bar-warning active" 
                role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" 
                style="width: 100%">
                </div>
              </div>
            </div>
            <div class="col-xs-7">
              <a href="#" class="btn btn-info" data-dismiss="modal">Cancel</a>
              <a href="#" class="btn btn-danger" id="confirm_delete_btn">Delete</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>     
  <!-- confirm modal -->

  <!-- confirm modal -->
  <!-- Modal HTML -->
  <div id="complete_confirm_modal" class="modal fade"  
    tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-confirm">
      <div class="modal-content">
        <div class="modal-header">      
          <h5 class="modal-title">
            <span class="fa fa-check"></span> Complete Period
          </h5>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to complete this period?
          This action cannot be undone and you will be unable to update/delete this period.</p>
        </div>
        <div class="modal-footer">
          <div class="row">
            <div class="col-xs-5">
              <div class="progress" style="margin-top: 7px;display: none"
                 id="delete_emp_progress">
                <div class="progress-bar progress-bar-striped progress-bar-animated progress-bar-warning active" 
                role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" 
                style="width: 100%">
                </div>
              </div>
            </div>
            <div class="col-xs-7">
              <a href="#" class="btn btn-info" data-dismiss="modal">Cancel</a>
              <a href="#" class="btn btn-success" id="confirm_complete_btn">Complete</a>
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


<!-- jQuery 3 -->
<script src="<?php echo base_url(); ?>assets/bower_components/jquery/dist/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?php echo base_url(); ?>assets/bower_components/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
  var baseurl = '<?php echo base_url(); ?>'+'index.php';
  var access_level = '<?php echo $_SESSION["rgc_access_level"]; ?>';

  var pos_count = '<?php echo $poscount; ?>';
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
<!-- AdminLTE App -->
<script src="<?php echo base_url(); ?>assets/dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo base_url(); ?>assets/dist/js/bootstrap-growl.js"></script>
<script src="<?php echo base_url(); ?>assets/dist/js/demo.js"></script>
<script src="<?php echo base_url(); ?>assets/dist/js/app.js"></script>
<script src="<?php echo base_url(); ?>assets/dist/js/productmovement.js"></script>
<script src="<?php echo base_url(); ?>assets/dist/js/select.min.js"></script>
</body>
</html>
