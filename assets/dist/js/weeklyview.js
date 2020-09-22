$(document).ready(function () {
	var table;

	$("input#week_data_date").daterangepicker({
		"maxSpan": {
			"days": 6
		},
		"showWeekNumbers": true,
	}).on('apply.daterangepicker', function(ev, picker) {
		var startDate = picker.startDate.format('YYYY-MM-DD');
		var endDate = picker.endDate.format('YYYY-MM-DD');

		var dates = enumerateDaysBetweenDates(picker.startDate, picker.endDate);
		var weeklyheader = '<th> </th>'
							+ '<th>Week Total</th>'
							+ '<th>Week Avg</th>';

		if(startDate !== endDate)
			$.each(dates, function (ind, row) {
				weeklyheader += '<th>' + moment(row).format('MMM. D') + '</th>';
			});
		else
			weeklyheader += '<th>' + moment(picker.startDate).format('MMM. D') + '</th>';

		if ( $.fn.dataTable.isDataTable( '#weekly_pms_tbl') ) {
			$('#weekly_pms_tbl').DataTable().destroy();
			$("table#weekly_pms_tbl tbody").empty();
			$("table#weekly_pms_tbl thead tr").empty();
		}



		getTabledata('raw_material_tbl', 0, startDate, endDate);
		getTabledata('premix_sauce_tbl', 1, startDate, endDate);
		getTabledata('drinks_tbl', 2, startDate, endDate);
		$("table#weekly_pms_tbl thead tr").html(weeklyheader);

		getPMS(startDate, endDate, weeklyheader);
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

	function getPMS(startDate, endDate, weeklyheader) {
			$('#weekly_pms_tbl').DataTable({
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
						if(access_level == 0){
							var branch_id = $("select#period_branch").select2('val');
							d.branch_id = branch_id;
						}
					}
				}
			});
	}

	function getTabledata(tableid, type, startDate, endDate) {
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
						if(access_level == 0){
							var branch_id = $("select#period_branch").select2('val');
							d.branch_id = branch_id;
						}
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
