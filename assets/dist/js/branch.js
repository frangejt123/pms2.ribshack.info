$(document).ready(function(){
	/* populate branch list */
	$.ajax({
		method: "POST",
		url: baseurl+"/branch/getAll",
		success: function(res){
			var res = JSON.parse(res);
			var tr = "";

			$.each(res, function(ind, row){
				tr += '<tr id="'+row["id"]+'">' +
						'<td>'+row["branch_name"]+'</td>' +
						'<td>'+row["address"]+'</td>' +
						'<td>'+row["tin"]+'</td>' +
						'<td>'+row["operated_by"]+'</td>' +
						'<td>'+row["pos_count"]+'</td>' +
						'</tr>';
			});

			$("table#branchtable tbody").html(tr);
		}
	})


	$("button#new_branch_btn").on("click", function(){
		$("div#new_branch_modal").modal("show");
	});

	$("#newbranch_submitBtn").on("click", function(){
		var branch_name = $("input#branch_name").val();
		var address = $("#address").val();
		var tin = $("input#tin").val();
		var operated_by = $("input#operated_by").val();
		var pos_count = $("input#pos_count").val();

		var data = {
			branch_name,
			address,
			tin,
			operated_by,
			pos_count,
		};

		var inputs = $("form#newbranchForm").find("input");
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
			url: baseurl+"/branch/insert",
			success: function(res){
				var res = JSON.parse(res);
				if(res["success"]){
					var tr = '<tr id="'+res["id"]+'">' +
							'<td>'+branch_name+'</td>' +
							'<td>'+address+'</td>' +
							'<td>'+tin+'</td>' +
							'<td>'+operated_by+'</td>' +
							'<td>'+pos_count+'</td>' +
							'</tr>';

					$("table#branchtable tbody").prepend(tr);
					$("div#new_branch_modal").modal("hide");

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
	$("table#branchtable tbody").on("click", "tr", function(){
		var tds = $(this).find("td");
		var id = $(this).attr("id");

		var branch_name = $(tds[0]).html();
		var address = $(tds[1]).html();
		var tin = $(tds[2]).html();
		var operated_by = $(tds[3]).html();
		var pos_count = $(tds[4]).html();

		$("input#update_branch_name").val(branch_name);
		$("#update_address").val(address);
		$("input#update_tin").val(tin);
		$("input#update_operated_by").val(operated_by);
		$("input#update_pos_count").val(pos_count);

		$("div#branch_detail_modal").data("id", id);
		$("div#branch_detail_modal").modal("show");
	});

	$("#updatebranch_submitBtn").on("click", function(){
		var branch_name = $("input#update_branch_name").val();
		var address = $("#update_address").val();
		var tin = $("input#update_tin").val();
		var operated_by = $("input#update_operated_by").val();
		var pos_count = $("input#update_pos_count").val();

		var id = $("div#branch_detail_modal").data("id");
		var data = {
			id,
			branch_name,
			address,
			tin,
			operated_by,
			pos_count,
		};

		var inputs = $("form#detailbranchForm").find("input");
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
			url: baseurl+"/branch/update",
			success: function(res){
				var res = JSON.parse(res);
				if(res["success"]){
					var td = '<td>'+branch_name+'</td>' +
						'<td>'+address+'</td>' +
						'<td>'+tin+'</td>' +
						'<td>'+operated_by+'</td>' +
						'<td>'+pos_count+'</td>';

					$("table#branchtable tbody tr#"+id).html(td);
					$("div#branch_detail_modal").modal("hide");

					$.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Changes successfully updated!", {
						type: "success",
						width: 300
					});
				}
			}
		});
	});

	/*delete record*/
	$("button#delete_branch").on("click", function(){
		$("div#confirm_modal").modal("show");
	});

	$("a#confirm_delete_branch_btn").on("click", function(){
		var id = $("div#branch_detail_modal").data("id");

		var datas = {
			"id" : id
		}

		$.ajax({
			url: baseurl+"/branch/delete",
			method: "POST",
			data: datas,
			success: function(data){
				var data = JSON.parse(data);
				if(data["success"]){
					$.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-check-circle' style='font-size: 20px'></span> &nbsp; Record successfully deleted.", {
						type: "success",
						width: 300
					});

					$("div#branch_detail_modal").modal("hide");
					$("table#branchtable").find("tr#"+id).remove();
				}

			}
		});

		$('div#branch_detail_modal').on('hide.bs.modal', function () {
			$("div#confirm_modal").modal("hide");
			$('html, body').css({
				overflow: 'hidden',
				height: '100%'
			});
		});
	});

	$("#tin, #update_tin, #pos_count, #update_pos_count").on("keypress keyup blur",function (event) {
		//this.value = this.value.replace(/[^0-9\.]/g,'');
		$(this).val($(this).val().replace(/[^0-9\.]/g,''));
		if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
			event.preventDefault();
		}
	});

});
