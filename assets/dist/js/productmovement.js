$(document).ready(function(){
	//initialize global variable
	var selectedid;

    $('.select2#period_branch').select2({
        placeholder: "Select a branch"
    });

	$("table#productmovementtable").on("dblclick", "td.td_beg_data", function(e){
		changeTDtoInput(this, "td_beg_data");
	});

	$("table#productmovementtable").on("dblclick", "td.td_end_data", function(e){
		changeTDtoInput(this, "td_end_data");
	});

    $("table#productmovementtable").on("dblclick", "td.td_del_data", function(e){
        changeTDtoInput(this, "td_del_data");
    });

    $("table#productmovementtable").on("dblclick", "td.td_transin_data", function(e){
        changeTDtoInput(this, "td_transin_data");
    });

    $("table#productmovementtable").on("dblclick", "td.td_ret_data", function(e){
        changeTDtoInput(this, "td_ret_data");
    });

    $("table#productmovementtable").on("dblclick", "td.td_transout_data", function(e){
        changeTDtoInput(this, "td_transout_data");
    });

    /* PRODUCT MOVEMENT BUTTONS */
    $("#complete_pms_btn").on("click", function(){
        if($(this).hasClass("disabled"))
            return;

        var selectedid = $("ul#pms_date_ul").find("li.activeli").attr("id");
        $("a#complete_confirm_trigger_btn").trigger("click");
        $('#complete_confirm_modal').data("eid", selectedid);
    });

    $("#update_pms_btn").on("click", function(){
        if($(this).hasClass("disabled"))
            return;

        $("div#update_pms_modal").modal("show");
    });

    $("#delete_pms_btn").on("click", function(){
        if($(this).hasClass("disabled"))
            return;

      //var selectedid = $("ul#pms_date_ul").find("li.activeli").attr("id");
      $("a#confirm_trigger_btn").trigger("click");
      //$('#confirm_modal').data("eid", selectedid);

    });

	$("#update_sales_btn").on("click", function(){
		if($(this).hasClass("disabled"))
			return;

		$("div#update_sales_modal").modal("show");
	});

	$("div#update_sales_modal, #new_pms_modal").on("shown.bs.modal", function(){
		$("#update_pms_sales_inp, #pms_sales").on("blur", function(e){
			var value = $(this).val();

			var actualNumber = +value.replace(/,/g, '');
			var formatted = actualNumber.toLocaleString('en-US', {maximumFractionDigits: 2});

			$(this).val(formatted);
		}).ForceNumericOnly();
	});

    $("#print_pms_btn").on("click", function(){
        var selectedid = $("ul#pms_date_ul").find("li.activeli").attr("id");
        window.open(baseurl + "/productmovement/report_productmovement?q="+selectedid.split("_")[1], '_blank');
    });

    var csvfiles = [];
    var updatecsvfiles = [];

    $("button#clear_pms_field").on("click", function(){
        $("input.pms_field").val("");
        csvfiles = [];
        updatecsvfiles = [];
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

    function parseTable(data){
		var li = "";
		$.each(data, function(ind, row){
			row["beginning"] = row["beginning"] == null ? 0 : row["beginning"];
			row["ending"] = row["ending"] == null ? 0 : row["ending"];
			row["actual"] = row["actual"] == null ? 0 : row["actual"];

			var label_cls = row["discrepancy"] < 0 ? "label-danger" : "label-primary";

			li += '<tr id="'+row["product_id"]+'" data-period="'+row["id"]+'">'
				+ '<td class="clickable js-pmsinfo-toggle" data-toggle="collapse" id="'+row["product_id"]+'" data-target="child'+row["product_id"]+'">'+row["product_id"]+'</td>'
				+ '<td class="clickable js-pmsinfo-toggle" data-toggle="collapse" id="'+row["product_id"]+'" data-target="child'+row["product_id"]+'">'+row["description"]+'</td>';

				for(var i=1;i<=pos_count;i++){
					li += '<td>'+row["pos"+i]+'</td>';
				}

				li += '<td>'+row["pos_total"]+'</td>'
				+ '<td class="td_beg_data">'+row["beginning"]+'</td>'
				+ '<td class="td_del_data">'+row["delivery"]+'</td>'
				+ '<td class="td_transin_data">'+row["trans_in"]+'</td>'
				+ '<td class="td_end_data">'+row["ending"]+'</td>'
				+ '<td class="td_ret_data">'+row["return_stock"]+'</td>'
				+ '<td class="td_transout_data">'+row["trans_out"]+'</td>'
				+ '<td class="td_actual_data">'+row["actual"]+'</td>'
				+ '<td class="td_discrepancy_data"><span class="label '+label_cls+'">'+row["discrepancy"]+'</span></td>'
				+ '</tr>';

			if(row["child"].length > 0){
				li += '<tr class="collapse child'+row["product_id"]+'">'
					+ '<td colspan="11">'
					+ '<table class="pmsinfo pmsinfo--child" data-detail-view="">'
					+ '<thead>'
					+ '<th style="width: 100px"></th>'
					+ '<th style="width: 140px"></th>';

					for(var i=1;i<=pos_count;i++){
						li += '<th style="width: 90px"></th>';
					}

					li += '<th style="width: 100px"></th>'
					+ '</thead>'
					+ '<tbody>';

				$.each(row["child"], function(cind, crow){
					li += '<tr class="subrow'+cind+'" data-href="#">'
						+ '<td style="padding-left: 15px;">'+crow["product_id"]+'</td>'
						+ '<td style="padding-left: 15px;">'+crow["description"]+'</td>';

						for(var i=1;i<=pos_count;i++){
							li += '<td style="padding-left: 15px;">'+crow["pos"+i]+'</td>';
						}

					li += '<td style="padding-left: 15px;">'+crow["pos_total"]+'</td>'
						+ '</tr>';
				});
				li += "</tbody></table></td></tr>"
			}//if child exist
		});//for each product

		$("table#productmovementtable tbody").html(li);
	}

    $("table#productmovementtable").on("click", "td.js-pmsinfo-toggle", function(){
        var tgt = $(this).attr("data-target");
        var id = $(this).attr("id");
        $("table#productmovementtable").find("tr#"+id).css({"font-weight":"normal","color":"#333"});
        if($("table#productmovementtable").find("tr.collapse."+tgt).length > 0)
            if($("table#productmovementtable").find("tr.collapse."+tgt).hasClass("uncollapse")){
                $("table#productmovementtable").find("tr.collapse."+tgt).hide().removeClass("uncollapse");
            }else{
                $("table#productmovementtable").find("tr#"+id).css({"font-weight":"bold","color":"#0b8cc1"});
                $("table#productmovementtable").find("tr.collapse."+tgt).show().addClass("uncollapse");
            }
    });

	function changeTDtoInput(tdobj, classname){
		var that = tdobj;
		var id = $(that).parent().attr("data-period");
		var value = $(that).html();

        var selectedPeriod = $("ul#pms_date_ul").find("li.activeli");
        if($(selectedPeriod).hasClass("complete_pms")){
            return;
        }

        var txtbox = "table#productmovementtable tr td."+classname+" input";
		var datatype = {
			"td_beg_data": "beginning",
			"td_end_data": "ending",
            "td_del_data": "delivery",
            "td_transin_data": "trans_in",
            "td_ret_data": "return_stock",
            "td_transout_data": "trans_out"
		};
		if(!$(that).attr("focused")){
			$(that).html("<input type='text' class='tableinput' value='"+value+"' style='border:0px; border-bottom: 1px solid #999;background: transparent;width: 40px;line-height: 15px;'>");
			var txtboxval = $(txtbox).val();
			$(txtbox).focus().val('').val(txtboxval).on("blur", function(){
				$(that).html($(txtbox).val()).removeAttr("focused");
				var type = datatype[classname];
				var value = $(this).val();
				saveRowData(id, type, value);
			}).on("keyup", function(e){
				var keycode = (e.keyCode ? e.keyCode : e.which);
			    if(keycode == '13'){
			        $(this).blur();
			    }
			}).ForceNumericOnly();
		}
		$(that).attr("focused", true);
	}

	function saveRowData(id, type, value){
        var d = {
            "id": id
        }

        d[type] = value;

        $.ajax({
            url: baseurl + "/productmovement/update",
            type: 'POST',
            data: d,
            success: function (res){
                var res = JSON.parse(res);
               if(res["success"]){
                var cls = res["discrepancy"] < 0 ? "label-danger" : "label-primary";
                var spandata = '<span class="label '+cls+'">'+res["discrepancy"]+'</span>';
                $("table#productmovementtable").find("tr[data-period='"+id+"'] td.td_discrepancy_data").html(spandata);
                $("table#productmovementtable").find("tr[data-period='"+id+"'] td.td_actual_data").html(res["actual"]);
               }
            }
        });
	}

	$("button#new_pms_btn").on("click", function(){
        if($(this).hasClass("disabled"))
            return;

		$("div#new_pms_modal").modal("show");
	});

	$("input#pms_date").datepicker({
		autoclose: true,
		todayHighlight : true,
		format: 'mm/dd/yyyy'
	});

	$("input#period_date").datepicker({
		autoclose: true,
		todayHighlight : true,
		format: 'mm/dd/yyyy'
	}).on('changeDate', function(){
		var pdate = $(this).val();
		var d = {"periodate": pdate, "branch_id": userbranch};

		if(access_level == 0){
			var branch_id = $("select#period_branch").select2('val');
			d["branch_id"] = branch_id;
		}

		$.ajax({
			url: baseurl + "/productmovement/getproductmovement",
			data: d,
			type: 'POST',
			success: function (data) {
				if(data == "nodata"){
					$("#sales_div").hide();

					var tbodytr = '<tr id="row1">'
						+'<td colspan="7" align="center"><span style="color: #f30"><b>No data to display.</b></span></td>'
					+'</tr>'
					$("table#productmovementtable tbody").html(tbodytr);

					// if(!$(this).hasClass("complete_pms"))
					// 	$("#complete_pms_btn, #update_pms_btn, #delete_pms_btn").removeClass("disabled");
					// else
					$("#complete_pms_btn, #update_pms_btn, #delete_pms_btn").addClass("disabled");

					$("#print_pms_btn").addClass("disabled");

				}else{
					var data = JSON.parse(data);
					$("#sales_div").find("h3").html("Sales: "+data["sales"]);
					$("#sales_div").show();

					//if(!$(this).hasClass("complete_pms"))
					$("#update_sales_btn, #update_pms_btn, #delete_pms_btn").removeClass("disabled");
					// else
					// 	$("#complete_pms_btn, #update_pms_btn, #delete_pms_btn").addClass("disabled");

					$("#print_pms_btn").removeClass("disabled");
					selectedid = data["id"];
					parseTable(data["product"]);
				}
			}
		});
	});

	/* import data */
	$(document).on('change', ':file', function () {
        var input = $(this),
                numFiles = input.get(0).files ? input.get(0).files.length : 1,
                label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        input.trigger('fileselect', [numFiles, label]);
    });

    $(':file').on('fileselect', function (event, numFiles, label) {
        var input = $(this).parents('.input-group').find(':text'),
                log = numFiles > 1 ? numFiles + ' files selected' : label;

        if (input.length) {
            input.val(log);
        } else {
            if (log)
                alert(log);
        }
    });

	for(var i=1;i<=pos_count;i++){
		$('input#csvtxtbox'+i).on('change', prepareUpload);
		$('input#updatecsvtxtbox'+i).on('change', prepareUploadUpdate);
	}

	function prepareUpload(event) {
		csvfiles.push(event.target.files);
		files = csvfiles;
    }

    function prepareUploadUpdate(event) {
        updatecsvfiles.push(event.target.files);
        updatefiles = updatecsvfiles;
    }

    $("button#updateproductmovementdata").on("click", function(e){
        var errorfile = 0;
        var empty = 0;
        var fileinputs = $("#update_pms_modal").find("input.fileinput");
        // $("#error_cont").hide();

        $.each(fileinputs, function (ind, row) {

            var extension = $(row).val().replace(/^.*\./, '');
            if (extension == $(row).val()) {
                extension = '';
            } else {
                extension = extension.toLowerCase();
            }

            if(extension != "")
                if (extension != "csv")
                    errorfile++;

            if($(row).val() != ""){
                empty++;
            }
        });

        if(empty == 0){
            $.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Please upload (.CSV) file.", {
              type: "danger",
              allow_dismiss: false,
              width: 300
            });
            return;
        }

        if (errorfile > 0)
            $.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Please upload (.CSV) file.", {
              type: "danger",
              allow_dismiss: false,
              width: 300
            });
        else
           uploadupdateFiles(e);
    });

    function uploadupdateFiles(event) {
        event.stopPropagation(); // Stop stuff happening
        event.preventDefault(); // Totally stop stuff happening
        var period_id = selectedid;//$("ul#pms_date_ul").find("li.activeli").attr("id").split("_")[1];

        // Create a formdata object and add the files
        var data = new FormData();
        data.append("period_id", period_id);

		var filteredfile = {};
		$.each(updatefiles, function(i, r){
			if(filteredfile[r[0]['name']] == undefined){
				filteredfile[r[0]['name']] = r;
			}
		});

		var filecount = 0;
        if (typeof filteredfile !== 'undefined')
            $.each(filteredfile, function (key, value){
				$.each(value, function (k, v){
					data.append("csvfile" + filecount, v);
					filecount += 1;
				});
            });

        doUpdate(data);
    }

    function doUpdate(data) {
        $.ajax({
            url: baseurl + "/productmovement/uploadUpdate",
            type: 'POST',
            data: data,
            cache: false,
            processData: false, // Don't process the files
            contentType: false, // Set content type to false as jQuery will tell the server its a query string request
            success: function (res){
				console.log(res);
                var res = JSON.parse(res);
                if(res["success"]){
                    var selectedid = $("ul#pms_date_ul").find("li.activeli").attr("id");
                    $("ul#pms_date_ul li#"+selectedid).trigger("click");

                    $("div#update_pms_modal").modal("hide");
                    updatecsvfiles = [];
                    $.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-exclamation-check' style='font-size: 20px'></span> &nbsp; Changes successfully save.", {
                      type: "success",
                      allow_dismiss: false,
                      width: 300
                    });
                }
            }
        });
    }

    $("button#mergeproductbtn").on("click", function(e){
        var errorfile = 0;
        var fileinputs = $("#new_pms_modal").find("input.fileinput");
        // $("#error_cont").hide();
        
        $.each(fileinputs, function (ind, row) {

            var extension = $(row).val().replace(/^.*\./, '');
            if (extension == $(row).val()) {
                extension = '';
            } else {
                extension = extension.toLowerCase();
            }

            if(extension != "")
                if (extension != "csv")
                    errorfile++;
        });

        if (errorfile > 0)
            $.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Please upload (.CSV) file.", {
              type: "danger",
              allow_dismiss: false,
              width: 300
            });
        else
    	   uploadFiles(e);
    });

    function uploadFiles(event) {
        event.stopPropagation(); // Stop stuff happening
        event.preventDefault(); // Totally stop stuff happening
        var pms_date = $("#pms_date").val();
		var pms_sales = $("#pms_sales").val();
        var branch_id = $("select#period_branch").select2('val');

        var existing_pms_data = $("ul#pms_date_ul").find("li");
        var formatedDate = $.datepicker.formatDate('M dd, yy', new Date(pms_date))
        var doexists = 0;
        $.each(existing_pms_data, function(ind, row){
            var d = $(row).html();
            if(formatedDate == d){
                doexists++;
            }
        });

        if(pms_date == ""){
             $.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Please select Period Date.", {
              type: "danger",
              allow_dismiss: false,
              width: 300
            });

            return;
        }

        if(doexists){
            $.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-exclamation-circle' style='font-size: 20px'></span> &nbsp; Period Date already exist.", {
              type: "danger",
              allow_dismiss: false,
              width: 300
            });

            return;
        }

        //check if period exist
		var datedata = {'date': pms_date, 'branch_id': userbranch};
		$.ajax({
			url: baseurl + "/productmovement/checkperiod",
			type: 'POST',
			data: datedata,
			cache: false,
			success: function (res){
				if(res > 0){
					alert("Period date already exist.");
					return;
				}
				// Create a formdata object and add the files
				var data = new FormData();
				data.append("pms_date", pms_date);
				data.append("branch_id", branch_id);
				data.append("sales", pms_sales.replace(",", ""));

				var filteredfile = {};
				$.each(files, function(i, r){
					if(filteredfile[r[0]['name']] == undefined){
						filteredfile[r[0]['name']] = r;
					}
				});

				var filecount = 0;
				if (typeof filteredfile !== undefined)
					$.each(filteredfile, function (key, value){
						$.each(value, function (k, v){
							data.append("csvfile" + filecount, v);
							filecount += 1;
						});
					});

				doUpload(data);
			}
		});
    }

    function doUpload(data) {
        $.ajax({
            url: baseurl + "/productmovement/mergeData",
            type: 'POST',
            data: data,
            cache: false,
            processData: false, // Don't process the files
            contentType: false, // Set content type to false as jQuery will tell the server its a query string request
            success: function (res){
                var res = JSON.parse(res);
                if(res["success"]){
                    var li = "<li class='list-group-item"+status+"' id='pmsli_"+res["period_id"]+"'>"+res["period_date"]+"</li>";
                    $("ul#pms_date_ul").prepend(li);
                    $("div#new_pms_modal").modal("hide");

                    csvfiles = [];
                    $.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-exclamation-check' style='font-size: 20px'></span> &nbsp; Changes successfully save.", {
                      type: "success",
                      allow_dismiss: false,
                      width: 300
                    });
                }
            }
        });
    }
	/* import data */

    /*delete period*/
    $("a#confirm_delete_btn").on("click", function(){
      //var id = $("div#confirm_modal").data("eid");
      var data = {"id":selectedid};
      $.ajax({
          url: baseurl + "/productmovement/deletePeriod",
          method:"POST",
          data: data,
          success:function(data){ 
            var data = JSON.parse(data);
            if(data["success"]){
              //$("ul#pms_date_ul li#"+id).remove();
              var tr = '<tr id="row1"><td colspan="7" align="center"><font style="color: #f30"><b>No data to display.</b></font></td></tr>'
              $("table#productmovementtable tbody").html(tr);

              $.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-check-circle' style='font-size: 20px'></span> &nbsp;Period successfully deleted.", {
                type: "success",
                allow_dismiss: false,
                width: 300
              });

              $("#confirm_modal").modal("hide");
            }
          }  
      });
    });
    /* delete period */

    /*complete period*/
    $("a#confirm_complete_btn").on("click", function(){
          var id = $("div#complete_confirm_modal").data("eid");
          var data = {"id":id.split("_")[1]};
          $.ajax({
              url: baseurl + "/productmovement/completePeriod",
              method:"POST",
              data: data,
              success:function(data){
                var data = JSON.parse(data);
                if(data["success"]){
                  $("ul#pms_date_ul li#"+id).addClass("complete_pms");
                  $("#complete_pms_btn, #update_pms_btn, #delete_pms_btn").addClass("disabled");

                  $.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-check-circle' style='font-size: 20px'></span> &nbsp;Period successfully completed.", {
                    type: "success",
                    allow_dismiss: false,
                    width: 300
                  });

                  $("#complete_confirm_modal").modal("hide");
                }
              }  
          });
        });

    $("#save_pms_sales").on("click", function(){
		var sales = $("#update_pms_sales_inp").val().replace(",", "");
		var data = {"id":selectedid, sales};
		$.ajax({
			url: baseurl + "/productmovement/updatesales",
			method:"POST",
			data: data,
			success:function(data){
				var data = JSON.parse(data);
				if(data["success"]){
					$.bootstrapGrowl("&nbsp; &nbsp; <span class='fa fa-check' style='font-size: 20px'></span> &nbsp; Changes successfully saved.", {
						type: "success",
						allow_dismiss: false,
						width: 300
					});
					$("#sales_div").find("h3").text("Sales: "+data["sales"]);
					$("#update_sales_modal").modal("hide");
				}
			}
		});
	});

    // Restricts input for each element in the set of matched elements to the given inputFilter.
    // Numeric only control handler
    jQuery.fn.ForceNumericOnly =
    function()
    {
        return this.each(function()
        {
            $(this).keydown(function(e)
            {
                var key = e.charCode || e.keyCode || 0;
                // enter, arrows, numbers and keypad numbers ONLY
                // home, end, period, and numpad decimal
                return (
                    key == 8 ||
                    key == 13 ||
                    key == 190 ||
				    key == 110 ||
                    (key >= 35 && key <= 40) ||
                    (key >= 48 && key <= 57) ||
                    (key >= 96 && key <= 105));
            });
        });
    };
});
