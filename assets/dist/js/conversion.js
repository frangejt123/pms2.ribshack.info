$(document).ready(function(){
	/* populate measurement list */
	$.ajax({
		method: "POST",
		url: baseurl+"/conversion/getAll",
		success: function(res){
			var res = JSON.parse(res);
			var tr = "";

			var type = ["Raw Material", "Premix & Sauce", "Drinks"];

			$.each(res, function(ind, row){
				tr += '<tr id="'+row["id"]+'">' +
					'<td id="'+row["product_code"]+'">'+row["product_description"]+'</td>'+
					'<td id="'+row["raw_material_id"]+'">'+row["raw_material"]+'</td>'+
					'<td>'+row["conversion"]+'</td>' +
					'</tr>';
			});

			$("table#conversiontable tbody").html(tr);
		}
	})


	$("button#new_conversion_btn").on("click", function(){
		$("div#new_conversion_modal").modal("show");
	});

	$("div#new_conversion_modal").on('shown.bs.modal', function () {
		populateSelect2(null, null);
	});

	$("#newRM_submitBtn").on("click", function(){
		var conversion = $("input#conversion").val();
		var product = $("#product_code").val();
		var raw_material = $("#raw_material").val();

		var product_description = $("select#product_code option:selected").html();
		var raw_material_description = $("select#raw_material option:selected").html();

		var data = {
			"product_code": product,
			"raw_material_id": raw_material,
			"conversion": conversion
		};

		var inputs = $("form#newConversionForm").find("input");
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
			url: baseurl+"/conversion/insert",
			success: function(res){
				var res = JSON.parse(res);
				if(res["success"]){
					var tr = '<tr id="'+res["id"]+'">' +
						'<td id="'+product+'">'+product_description+'</td>' +
						'<td id="'+raw_material+'">'+raw_material_description+'</td>' +
						'<td>'+conversion+'</td>' +
						'</tr>';

					$("table#conversiontable tbody").prepend(tr);
					$("div#new_conversion_modal").modal("hide");

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
	$("table#conversiontable tbody").on("click", "tr", function(){
		var id = $(this).attr("id");

		$("div#conversion_detail_modal").data("id", id);
		$("div#conversion_detail_modal").modal("show");
	});

	$("div#conversion_detail_modal").on('shown.bs.modal', function () {
		var id = $("div#conversion_detail_modal").data("id");
		var tds = $("table#conversiontable tbody tr#"+id).find("td");

		var product = $(tds[0]).attr("id");
		var rawmaterial = $(tds[1]).attr("id");
		var conversion = $(tds[2]).html();

		$("input#update_conversion").val(conversion);
		populateSelect2(product, rawmaterial);
	});

	$("#updateConversion_submitBtn").on("click", function(){
		var id = $("div#conversion_detail_modal").data("id");
		var conversion = $("input#update_conversion").val();
		var product = $("#update_product_code").val();
		var raw_material = $("#update_raw_material").val();

		var product_description = $("select#update_product_code option:selected").html();
		var raw_material_description = $("select#update_raw_material option:selected").html();

		var d = {
			"id": id,
			"product_code": product,
			"raw_material_id": raw_material,
			"conversion": conversion
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
			url: baseurl+"/conversion/update",
			success: function(res){
				var res = JSON.parse(res);
				if(res["success"]){
					var td = '<td id="'+product+'">'+product_description+'</td>' +
						'<td id="'+raw_material+'">'+raw_material_description+'</td>' +
						'<td>'+conversion+'</td>';

					$("table#conversiontable tbody tr#"+id).html(td);
					$("div#conversion_detail_modal").modal("hide");

					$.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Changes successfully updated!", {
						type: "success",
						width: 300
					});
				}
			}
		});
	});

	/*delete record*/
	$("button#delete_conversion").on("click", function(){
		$("div#confirm_modal").modal("show");
	});

	$("a#confirm_delete_conversion_btn").on("click", function(){
		var id = $("div#conversion_detail_modal").data("id");

		var data = {
			"id" : id
		};

		$.ajax({
			url: baseurl+"/conversion/delete",
			method: "POST",
			data: data,
			success: function(data){
				var data = JSON.parse(data);
				if(data["success"]){
					$.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-check-circle' style='font-size: 20px'></span> &nbsp; Record successfully deleted.", {
						type: "success",
						width: 300
					});

					$("div#conversion_detail_modal").modal("hide");
					$("table#conversiontable").find("tr#"+id).remove();
				}

			}
		});

		$('div#conversion_detail_modal').on('hide.bs.modal', function () {
			$("div#confirm_modal").modal("hide");
			$('html, body').css({
				overflow: 'hidden',
				height: '100%'
			});
		});
	});

	function populateSelect2(product, rawmaterial){
		$.ajax({
			method: "POST",
			url: baseurl+"/rawmaterial/getAll",
			success: function(res){
				var res = JSON.parse(res);
				var dataRM = [{"id":"","text":""}];

				$.each(res, function(i, r){
					dataRM.push({"id":r["id"],"text":r["description"]});
				});

				$('.select2#raw_material, .select2#update_raw_material').select2({
					placeholder: "Select Type",
					data: dataRM
				}).val(rawmaterial).trigger("change");

			}
		});

		$.ajax({
			method: "POST",
			url: baseurl+"/product/getParent",
			success: function(res){
				var res = JSON.parse(res);
				var dataUom = [{"id":"","text":""}];

				$.each(res["product"], function(i, r){
					dataUom.push({"id":r["id"],"text":r["description"]});
				});

				$('.select2#product_code, .select2#update_product_code').select2({
					placeholder: "Select Product",
					data: dataUom
				}).val(product).trigger("change");

			}
		});
	}

});
