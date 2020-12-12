$(document).ready(function () {
	var table;

	$("input#week_data_date").daterangepicker({
		"maxSpan": {
			"days": 6
		},
		"showWeekNumbers": true,
	}).on('apply.daterangepicker', function(ev, picker) {
		if(access_level == 0){
			var branch_id = $("select#period_branch").select2('val');
		}
		branch_id = userbranch;

		if(branch_id == "" || branch_id == undefined){
			alert("Please select branch");
			return;
		}

		var startDate = picker.startDate.format('YYYY-MM-DD');
		var endDate = picker.endDate.format('YYYY-MM-DD');

		var dates = enumerateDaysBetweenDates(picker.startDate, picker.endDate);
		var weeklyheader = '<th> </th>'
							+ '<th>Week Total</th>'
							+ '<th>Week Avg</th>';

		var filtereddate = [];
		if(startDate !== endDate){
			$.each(dates, function (ind, row) {
				weeklyheader += '<th>' + moment(row).format('MMM. D') + '</th>';
				filtereddate.push(moment(row).format('YYYYMMDD'));
			});
		}else{
			weeklyheader += '<th>' + moment(picker.startDate).format('MMM. D') + '</th>';
			filtereddate.push(moment(picker.startDate).format('YYYYMMDD'));
		}

		if ( $.fn.dataTable.isDataTable( '#weekly_pms_tbl') ) {
			$('#weekly_pms_tbl').DataTable().destroy();
			$("table#weekly_pms_tbl tbody").empty();
			$("table#weekly_pms_tbl thead tr").empty();
		}

		getTabledata('raw_material_tbl', 0, startDate, endDate, branch_id);
		getTabledata('premix_sauce_tbl', 1, startDate, endDate, branch_id);
		getTabledata('drinks_tbl', 2, startDate, endDate, branch_id);
		$("table#weekly_pms_tbl thead tr").html(weeklyheader);

		getPMS(startDate, endDate, filtereddate, branch_id);
	});

	$.ajax({
		method: "POST",
		url: baseurl+"/branch/getAll",
		success: function(res){
			var res = JSON.parse(res);
			var data = [{"id":"","text":""}];
			$.each(res, function(i, r){
				data.push({"id":r["id"],"text":r["branch_name"]});
			});

			$('.select2#period_branch').select2({
				placeholder: "Select a branch",
				data: data
			});
		}
	});

	var weekly_pms_table;
	var fdate;

	// Add event listener for opening and closing details
	$('#weekly_pms_tbl tbody').on('click', 'td.product_parent', function () {
		var tr = $(this).closest('tr');
		var row = weekly_pms_table.row( tr );

		if(row.data().child !== undefined){
			if ( row.child.isShown() ) {
				// This row is already open - close it
				row.child.hide();
				tr.removeClass('shown parent-selected');
			}
			else {
				// Open this row
				row.child(format(row.data())).show();
				//format(row.data());
				tr.addClass('shown parent-selected');
			}
		}
	});

	function format ( d ) {
		// `d` is the original data object for the row
		var tr = "";
		var thead = "<th></th><th>Week Total</th><th>Week Avg</th>";

		$.each(fdate, function(ind, row){
			thead += '<th>'+moment(row).format('MMM. D')+'</th>';
		});

		$.each(d['child'], function(i, r){
			tr += '<tr class="weekly_view_child">';

			var wt = 0;

			$.each(fdate, function(ind, row){
				wt += parseFloat(r[row]);
			});

			var wa = parseFloat((wt / fdate.length)).toFixed(2);
			tr += '<td>' + r['desc'] + "</td><td>"+wt.toFixed(2)+"</td><td>"+wa+"</td>";

			$.each(fdate, function(ind, row){
				tr += '<td>' + r[row] + "</td>";
			});

			tr += '</tr>';
		});

		return '<table class="table"><thead><tr>'+thead+'</tr></thead>'+
			tr
			+ '</table>';
	}

	function getPMS(startDate, endDate, filtereddate, branch_id) {
			fdate = filtereddate;
			var tbl_col = [
				{'data': 'desc', 'className':'product_parent'},
				{'data': 'week_total'},
				{'data': 'week_avg'}
			];

			$.each(filtereddate, function(ind, row){
				var d = {'data': row};
				tbl_col.push(d);
			});

		weekly_pms_table = $('#weekly_pms_tbl').DataTable({
				"processing": true,
				"serverSide": true,
				"bLengthChange": false,
				"order": [],
				"ordering": false,
				'serverMethod': 'post',
				'searching': false,
				'paging': false,
				'bInfo': false,
				'ajax': {
					'url': baseurl + "/weekview/getTotalpms",
					'data': function (d) {
						d.startDate = startDate;
						d.endDate = endDate;
						d.branch_id = branch_id;
					}
				},
				"columns": tbl_col
			});
	}

	function getTabledata(tableid, type, startDate, endDate, branch_id) {
		if ( $.fn.dataTable.isDataTable( '#' + tableid) ) {
			$('#' + tableid).DataTable().destroy();
		}

		$('#' + tableid).DataTable({
				"processing": true,
				"serverSide": true,
				"pageLength": 20,
				"bLengthChange": false,
				"order": [],
				"ordering": false,
				'serverMethod': 'post',
				'searching': false,
				'paging': false,
				'bInfo': false,
				'retrieve': true,
				'ajax': {
					'url': baseurl + "/weekview/getAll",
					'data': function (d) {
						d.type = type;
						d.startDate = startDate;
						d.endDate = endDate;
						d.branch_id = branch_id;
					}
				},
				"columns": [
					{"data": "raw_material"},
					{"data": "uom_abbr"},
					{"data": "week_total"},
					{"data": "week_avg"}
				]
			});
	}

	function enumerateDaysBetweenDates(startDate, endDate) {
		var dates = [];

		var currDate = moment(startDate).startOf('day');
		var lastDate = moment(endDate).startOf('day');

		dates.push(currDate.toDate());
		while(currDate.add(1, 'days').diff(lastDate) < 0) {
			dates.push(currDate.clone().toDate());
		}
		dates.push(lastDate.toDate());

		return dates;
	}
});
