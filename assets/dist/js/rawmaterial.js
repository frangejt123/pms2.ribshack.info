$(document).ready(function(){
	/* populate measurement list */
	$.ajax({
		method: "POST",
		url: baseurl+"/rawmaterial/getAll",
		success: function(res){
			var res = JSON.parse(res);
			var tr = "";

			var type = ["Raw Material", "Premix & Sauce", "Drinks"];

			$.each(res, function(ind, row){
				tr += '<tr id="'+row["id"]+'">' +
					'<td>'+row["description"]+'</td>'+
					'<td id="'+row["type"]+'">'+type[row["type"]]+'</td>' +
					'<td id="'+row["uom"]+'">'+row["uom_description"]+'</td>' +
					'</tr>';
			});

			$("table#rawmaterialtable tbody").html(tr);
		}
	})


	$("button#new_rm_btn").on("click", function(){
		$("div#new_rm_modal").modal("show");
		// var inputs = $("form#newProductForm").find("input");
		// $.each(inputs, function(ind, row){
		// 	$(this).removeClass("emptyField");
		// 	$(this).val("");
		// });
		// $("select#product_uom").removeClass("emptyField");
	});

	$("div#new_rm_modal").on('shown.bs.modal', function () {
		populateSelect2(null, null);
	});

	$("#newRM_submitBtn").on("click", function(){
		var description = $("input#description").val();
		var type = $("#rm_type").val();
		var uom = $("#rm_uom").val();

		var type_description = $("select#rm_type option:selected").html();
		var uom_description = $("select#rm_uom option:selected").html();

		var data = {
			"description": description,
			"type": type,
			"uom": uom,
		};

		var inputs = $("form#newRMForm").find("input");
		var empty = 0;
		$.each(inputs, function(ind, row){
			$(this).removeClass("emptyField");
			if($(this).val() == ""){
				$(this).addClass("emptyField");
				empty++;
			}
		});

		if(empty > 0){
			$.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Please fill in required fields.", {
				type: "danger",
				width: 300
			});
			return;
		}

		$.ajax({
			method: "POST",
			data: data,
			url: baseurl+"/rawmaterial/insert",
			success: function(res){
				var res = JSON.parse(res);
				if(res["success"]){
					var tr = '<tr id="'+res["id"]+'">' +
								'<td>'+description+'</td>' +
								'<td id="'+type+'">'+type_description+'</td>' +
								'<td id="'+uom+'">'+uom_description+'</td>' +
						'</tr>';

					$("table#rawmaterialtable tbody").prepend(tr);
					$("div#new_rm_modal").modal("hide");

					$.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Changes successfully saved!", {
						type: "success",
						allow_dismiss: false,
						width: 300
					});
				}
			}
		})
	});

	/* on row click */
	$("table#rawmaterialtable tbody").on("click", "tr", function(){
		var id = $(this).attr("id");

		$("div#rm_detail_modal").data("id", id);
		$("div#rm_detail_modal").modal("show");
	});

	$("div#rm_detail_modal").on('shown.bs.modal', function () {
		var id = $("div#rm_detail_modal").data("id");
		var tds = $("table#rawmaterialtable tbody tr#"+id).find("td");

		var description = $(tds[0]).html();
		var typeval = $(tds[1]).attr("id");
		var uomval = $(tds[2]).attr("id");

		$("input#update_description").val(description);
		populateSelect2(typeval, uomval);
	});

	$("#updateRM_submitBtn").on("click", function(){
		var id = $("div#rm_detail_modal").data("id");
		var description = $("input#update_description").val();
		var type = $("#update_rm_type").val();
		var uom = $("#update_rm_uom").val();

		var type_description = $("select#update_rm_type option:selected").html();
		var uom_description = $("select#update_rm_uom option:selected").html();

		var d = {
			"id": id,
			"description": description,
			"type": type,
			"uom": uom
		};

		var inputs = $("form#detailRMForm").find("input");

		var empty = 0;
		$.each(inputs, function(ind, row){
			$(this).removeClass("emptyField");
			if($(this).val() == ""){
				$(this).addClass("emptyField");
				empty++;
			}
		});

		if(empty > 0){
			$.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Please fill in required fields.", {
				type: "danger",
				width: 300
			});
			return;
		}

		$.ajax({
			method: "POST",
			data: d,
			url: baseurl+"/rawmaterial/update",
			success: function(res){
				var res = JSON.parse(res);
				if(res["success"]){
					var td = '<td>'+description+'</td>' +
						'<td id="'+type+'">'+type_description+'</td>' +
						'<td id="'+uom+'">'+uom_description+'</td>';

					$("table#rawmaterialtable tbody tr#"+id).html(td);
					$("div#rm_detail_modal").modal("hide");

					$.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Changes successfully updated!", {
						type: "success",
						width: 300
					});
				}
			}
		});
	});

	/*delete record*/
	$("button#delete_rm").on("click", function(){
		$("div#confirm_modal").modal("show");
	});

	$("a#confirm_delete_rm_btn").on("click", function(){
		var id = $("div#rm_detail_modal").data("id");

		var data = {
			"id" : id
		};

		$.ajax({
			url: baseurl+"/rawmaterial/delete",
			method: "POST",
			data: data,
			success: function(data){
				var data = JSON.parse(data);
				if(data["success"]){
					$.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-check-circle' style='font-size: 20px'></span> &nbsp; Record successfully deleted.", {
						type: "success",
						width: 300
					});

					$("div#rm_detail_modal").modal("hide");
					$("table#rawmaterialtable").find("tr#"+id).remove();
				}

			}
		});

		$('div#rm_detail_modal').on('hide.bs.modal', function () {
			$("div#confirm_modal").modal("hide");
			$('html, body').css({
				overflow: 'hidden',
				height: '100%'
			});
		});
	});

	function populateSelect2(type, uom){
		var dateType = [
			{"id": '', "text": ""},
			{"id": '0', "text": "Raw Material"},
			{"id": '1', "text": "Premix & Sauce"},
			{"id": '2', "text": "Drinks"}
		];

		$('.select2#rm_type, .select2#update_rm_type').select2({
			placeholder: "Select Type",
			data: dateType
		}).val(type).trigger("change");

		$.ajax({
			method: "POST",
			url: baseurl+"/uom/getAll",
			success: function(res){
				var res = JSON.parse(res);
				var dataUom = [{"id":"","text":""}];

				$.each(res, function(i, r){
					dataUom.push({"id":r["id"],"text":r["description"]});
				});

				$('.select2#rm_uom, .select2#update_rm_uom').select2({
					placeholder: "Select Unit of Measurement",
					data: dataUom
				}).val(uom).trigger("change");

			}
		});
	}

});
