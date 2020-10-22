$(document).ready(function(){

	function getCurrentWeek() {
		var currentDate = moment();

		var weekStart = currentDate.clone().startOf('isoWeek');
		var weekEnd = currentDate.clone().endOf('isoWeek');

		var days = [];

		for (var i = 0; i <= 6; i++) {
			days.push(moment(weekStart).add(i, 'days').format("YYYY-MM-DD"));
		}

		return days;
	}

	var days_of_week = {};
	days_of_week["date"] = getCurrentWeek();

	$.ajax({
		method: "POST",
		url: baseurl+"/dashboard/productContribution",
		data: days_of_week,
		success: function(res){
			res = JSON.parse(res);

			$("h3#weekly_sales_cont").html(res["total_sales"]);
			$("h3#ave_sales_cont").html(res["ave_sales"]);

			Highcharts.chart('pie-chart', {
				chart: {
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false,
					type: 'pie'
				},
				title: {
					text: 'Product Contribution'
				},
				subtitle: {
					text: res["datefrom"] + ' - ' + res["dateto"]
				},
				tooltip: {
					pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
				},
				accessibility: {
					point: {
						valueSuffix: '%'
					}
				},
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: true,
							format: '<b>{point.name}</b>: {point.percentage:.1f} %'
						}
					}
				},
				exporting: {
					buttons: {
						contextButton: {
							menuItems: [
								'printChart',
								'separator',
								'downloadPNG',
								'downloadJPEG',
								'downloadPDF'
							]
						}
					}
				},
				series: [{
					name: 'Product',
					colorByPoint: true,
					data: res["product_cont"]
				}]
			});
		}
	});

	$.ajax({
		method: "POST",
		url: baseurl+"/dashboard/daily_sales",
		data: days_of_week,
		success: function(res){
			res = JSON.parse(res);

			var tr = "";
			$.each(res, function(ind, row){
				tr += "<tr><td>"+row["date"]+"</td><td>"+row["sales"]+"</td></tr>";
			});

			$("table#daily_sales_tbl tbody").html(tr);
		}
	});

	$.ajax({
		method: "POST",
		url: baseurl+"/dashboard/top_sales",
		data: days_of_week,
		success: function(res){

		}
	});


	// // Create the chart
	// Highcharts.chart('top-product-container', {
	// 	chart: {
	// 		type: 'column'
	// 	},
	// 	title: {
	// 		text: 'Best Seller — Product'
	// 	},
	// 	subtitle: {
	// 		text: 'October 12, 2020 - October 18, 2020'
	// 	},
	// 	accessibility: {
	// 		announceNewData: {
	// 			enabled: true
	// 		}
	// 	},
	// 	exporting: {
	// 		buttons: {
	// 			contextButton: {
	// 				menuItems: [
	// 					'printChart',
	// 					'separator',
	// 					'downloadPNG',
	// 					'downloadJPEG',
	// 					'downloadPDF'
	// 				]
	// 			}
	// 		}
	// 	},
	// 	xAxis: {
	// 		type: 'category'
	// 	},
	// 	yAxis: {
	// 		title: {
	// 			text: 'Quantity'
	// 		}
	//
	// 	},
	// 	legend: {
	// 		enabled: false
	// 	},
	// 	plotOptions: {
	// 		series: {
	// 			borderWidth: 0,
	// 			dataLabels: {
	// 				enabled: true,
	// 				format: '{point.y:.1f}'
	// 			}
	// 		}
	// 	},
	//
	// 	tooltip: {
	// 		headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
	// 		pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}</b><br/>'
	// 	},
	// 	series: [
	// 		{
	// 			name: "Product",
	// 			colorByPoint: true,
	// 			data: [
	// 				{
	// 					name: "Spareribs",
	// 					y: 62.74,
	// 					drilldown: "spareribs",
	// 					color: "red"
	// 				},
	// 				{
	// 					name: "Backribs",
	// 					y: 10.57,
	// 					drilldown: "backribs"
	// 				},
	// 				{
	// 					name: "Pork BBQ",
	// 					y: 7.23,
	// 					drilldown: "porkbbq"
	// 				},
	// 				{
	// 					name: "Chicken Paa",
	// 					y: 5.58,
	// 					drilldown: "chickenpaa"
	// 				},
	// 				{
	// 					name: "Chicken Pecho",
	// 					y: 4.02,
	// 					drilldown: "chickenpecho"
	// 				}
	// 			]
	// 		}
	// 	],
	// 	drilldown: {
	// 		series: [
	// 			{
	// 				name: "Spareribs",
	// 				id: "spareribs",
	// 				data: [
	// 					[
	// 						"Mon",
	// 						0.1
	// 					],
	// 					[
	// 						"Tue",
	// 						1.3
	// 					],
	// 					[
	// 						"Wed",
	// 						53.02
	// 					],
	// 					[
	// 						"Thu",
	// 						1.4
	// 					],
	// 					[
	// 						"Fri",
	// 						0.88
	// 					],
	// 					[
	// 						"Sat",
	// 						0.56
	// 					],
	// 					[
	// 						"Sun",
	// 						0.45
	// 					]
	// 				]
	// 			},
	// 			{
	// 				name: "Backribs",
	// 				id: "backribs",
	// 				data: [
	// 					[
	// 						"Mon",
	// 						0.1
	// 					],
	// 					[
	// 						"Tue",
	// 						1.3
	// 					],
	// 					[
	// 						"Wed",
	// 						53.02
	// 					],
	// 					[
	// 						"Thu",
	// 						1.4
	// 					],
	// 					[
	// 						"Fri",
	// 						0.88
	// 					],
	// 					[
	// 						"Sat",
	// 						0.56
	// 					],
	// 					[
	// 						"Sun",
	// 						0.45
	// 					]
	// 				]
	// 			},
	// 			{
	// 				name: "Pork BBQ",
	// 				id: "porkbbq",
	// 				data: [
	// 					[
	// 						"Mon",
	// 						0.1
	// 					],
	// 					[
	// 						"Tue",
	// 						1.3
	// 					],
	// 					[
	// 						"Wed",
	// 						53.02
	// 					],
	// 					[
	// 						"Thu",
	// 						1.4
	// 					],
	// 					[
	// 						"Fri",
	// 						0.88
	// 					],
	// 					[
	// 						"Sat",
	// 						0.56
	// 					],
	// 					[
	// 						"Sun",
	// 						0.45
	// 					]
	// 				]
	// 			},
	// 			{
	// 				name: "Chicken paa",
	// 				id: "chickenpaa",
	// 				data: [
	// 					[
	// 						"Mon",
	// 						0.1
	// 					],
	// 					[
	// 						"Tue",
	// 						1.3
	// 					],
	// 					[
	// 						"Wed",
	// 						53.02
	// 					],
	// 					[
	// 						"Thu",
	// 						1.4
	// 					],
	// 					[
	// 						"Fri",
	// 						0.88
	// 					],
	// 					[
	// 						"Sat",
	// 						0.56
	// 					],
	// 					[
	// 						"Sun",
	// 						0.45
	// 					]
	// 				]
	// 			},
	// 			{
	// 				name: "Chicken Pecho",
	// 				id: "chickenpecho",
	// 				data: [
	// 					[
	// 						"Mon",
	// 						0.1
	// 					],
	// 					[
	// 						"Tue",
	// 						1.3
	// 					],
	// 					[
	// 						"Wed",
	// 						53.02
	// 					],
	// 					[
	// 						"Thu",
	// 						1.4
	// 					],
	// 					[
	// 						"Fri",
	// 						0.88
	// 					],
	// 					[
	// 						"Sat",
	// 						0.56
	// 					],
	// 					[
	// 						"Sun",
	// 						0.45
	// 					]
	// 				]
	// 			}
	// 		]
	// 	}
	// });
	//
	// // Create the chart
	// Highcharts.chart('top-drinks-container', {
	// 	chart: {
	// 		type: 'column'
	// 	},
	// 	title: {
	// 		text: 'Best Seller — Drinks'
	// 	},
	// 	subtitle: {
	// 		text: 'October 12, 2020 - October 18, 2020'
	// 	},
	// 	accessibility: {
	// 		announceNewData: {
	// 			enabled: true
	// 		}
	// 	},
	// 	exporting: {
	// 		buttons: {
	// 			contextButton: {
	// 				menuItems: [
	// 					'printChart',
	// 					'separator',
	// 					'downloadPNG',
	// 					'downloadJPEG',
	// 					'downloadPDF'
	// 				]
	// 			}
	// 		}
	// 	},
	// 	xAxis: {
	// 		type: 'category'
	// 	},
	// 	yAxis: {
	// 		title: {
	// 			text: 'Quantity'
	// 		}
	//
	// 	},
	// 	legend: {
	// 		enabled: false
	// 	},
	// 	plotOptions: {
	// 		series: {
	// 			borderWidth: 0,
	// 			dataLabels: {
	// 				enabled: true,
	// 				format: '{point.y:.1f}'
	// 			}
	// 		}
	// 	},
	//
	// 	tooltip: {
	// 		headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
	// 		pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}</b><br/>'
	// 	},
	//
	// 	series: [
	// 		{
	// 			name: "Drinks",
	// 			colorByPoint: true,
	// 			data: [
	// 				{
	// 					name: "Lemonade 12oz",
	// 					y: 6.74,
	// 					drilldown: "lemonade12"
	// 				},
	// 				{
	// 					name: "Lemonade 16oz",
	// 					y: 53.23,
	// 					drilldown: "lemonade16"
	// 				},
	// 				{
	// 					name: "Iced Tea 16oz",
	// 					y: 20.58,
	// 					drilldown: "icedtea16"
	// 				},
	// 				{
	// 					name: "Iced Tea 12oz",
	// 					y: 4.02,
	// 					drilldown: "icedtea12"
	// 				},
	// 				{
	// 					name: "Pepsi",
	// 					y: 1.92,
	// 					drilldown: "pepsi"
	// 				}
	// 			]
	// 		}
	// 	],
	// 	drilldown: {
	// 		series: [
	// 			{
	// 				name: "Lemonade 12oz",
	// 				id: "lemonade12",
	// 				data: [
	// 					[
	// 						"Mon",
	// 						7.1
	// 					],
	// 					[
	// 						"Tue",
	// 						10.3
	// 					],
	// 					[
	// 						"Wed",
	// 						1.02
	// 					],
	// 					[
	// 						"Thu",
	// 						1.4
	// 					],
	// 					[
	// 						"Fri",
	// 						2.88
	// 					],
	// 					[
	// 						"Sat",
	// 						0.56
	// 					],
	// 					[
	// 						"Sun",
	// 						20.45
	// 					]
	// 				]
	// 			},
	// 			{
	// 				name: "Lemonade 16oz",
	// 				id: "lemonade16",
	// 				data: [
	// 					[
	// 						"Mon",
	// 						7.1
	// 					],
	// 					[
	// 						"Tue",
	// 						10.3
	// 					],
	// 					[
	// 						"Wed",
	// 						1.02
	// 					],
	// 					[
	// 						"Thu",
	// 						1.4
	// 					],
	// 					[
	// 						"Fri",
	// 						2.88
	// 					],
	// 					[
	// 						"Sat",
	// 						0.56
	// 					],
	// 					[
	// 						"Sun",
	// 						20.45
	// 					]
	// 				]
	// 			},
	// 			{
	// 				name: "Iced Tea 12oz",
	// 				id: "icedtea12",
	// 				data: [
	// 					[
	// 						"Mon",
	// 						7.1
	// 					],
	// 					[
	// 						"Tue",
	// 						10.3
	// 					],
	// 					[
	// 						"Wed",
	// 						1.02
	// 					],
	// 					[
	// 						"Thu",
	// 						1.4
	// 					],
	// 					[
	// 						"Fri",
	// 						2.88
	// 					],
	// 					[
	// 						"Sat",
	// 						0.56
	// 					],
	// 					[
	// 						"Sun",
	// 						20.45
	// 					]
	// 				]
	// 			},
	// 			{
	// 				name: "Iced Tea 16oz",
	// 				id: "icedtea16",
	// 				data: [
	// 					[
	// 						"Mon",
	// 						7.1
	// 					],
	// 					[
	// 						"Tue",
	// 						10.3
	// 					],
	// 					[
	// 						"Wed",
	// 						1.02
	// 					],
	// 					[
	// 						"Thu",
	// 						1.4
	// 					],
	// 					[
	// 						"Fri",
	// 						2.88
	// 					],
	// 					[
	// 						"Sat",
	// 						0.56
	// 					],
	// 					[
	// 						"Sun",
	// 						20.45
	// 					]
	// 				]
	// 			},
	// 			{
	// 				name: "Pepsi",
	// 				id: "pepsi",
	// 				data: [
	// 					[
	// 						"Mon",
	// 						7.1
	// 					],
	// 					[
	// 						"Tue",
	// 						10.3
	// 					],
	// 					[
	// 						"Wed",
	// 						1.02
	// 					],
	// 					[
	// 						"Thu",
	// 						1.4
	// 					],
	// 					[
	// 						"Fri",
	// 						2.88
	// 					],
	// 					[
	// 						"Sat",
	// 						0.56
	// 					],
	// 					[
	// 						"Sun",
	// 						20.45
	// 					]
	// 				]
	// 			},
	// 		]
	// 	}
	// });

});
