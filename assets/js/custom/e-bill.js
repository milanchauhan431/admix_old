$(document).ready(function(){
	
	$(document).on("change", "#transport_name", function() {
		var transVal = $(this).val();
		var transId = $(this).children('option:selected').data('val');
		$("#transport_id").val(transId);
	});

	$("#vehicle_no").attr("autocomplete", "off");
	$('#vehicle_no').typeahead({
		source: function(query, result) {
			$.ajax({
				url: base_url + 'ebill/vehicleSearch',
				method: "POST",
				global: false,
				data: {
					query: query
				},
				dataType: "json",
				success: function(data) {
					result($.map(data, function(item) {
						return item;
					}));
				}
			});
		}
	});	
	
	
	$(document).on('change',"#generateEwayBill #transaction_type", function() {
		$("#generateEwayBill #from_address").val("");
		$("#generateEwayBill #from_pincode").val("");
		$("#generateEwayBill #ship_pincode").val("");
		$("#generateEwayBill #ship_address").val("");
		$("#generateEwayBill #from_city").val("");
		$("#generateEwayBill #from_city").select2();
		$("#generateEwayBill #from_state").val("");
		$("#generateEwayBill #from_state").select2();
		$("#generateEwayBill #ship_city").val("");
		$("#generateEwayBill #ship_city").select2();
		$("#generateEwayBill #ship_state").val("");
		$("#generateEwayBill #ship_state").select2();
		
		var party_id = $("#generateEwayBill #party_id").val();
		var transaction_type = $("#generateEwayBill #transaction_type").val();
		$(".transaction_type").html('');
		$(".party_id").html('');
		if (transaction_type == "") {
			$(".transaction_type").html('Transaction Type is Required');
		} else {
			if (party_id == "") {
				$("#transaction_type").val("");
				$(".party_id").html('Party is Required');
				//$("#transaction_type").comboSelect();
			} else {
				$.ajax({
					url: base_url + 'ebill/getEwbAddress',
					type: 'post',
					data: {
						party_id: party_id,transaction_type:transaction_type
					},
					dataType: 'json',
					success: function(data) {
						$("#generateEwayBill #from_address").val(data.from_address);
						$("#generateEwayBill #from_pincode").val(data.from_pincode);
						$("#generateEwayBill #ship_pincode").val(data.ship_pincode);
						$("#generateEwayBill #ship_address").val(data.ship_address);
						$("#generateEwayBill #from_city").html("");
						$("#generateEwayBill #from_city").html(data.from_city);
						$("#generateEwayBill #from_city").select2();
						$("#generateEwayBill #from_state").val(data.from_state);
						$("#generateEwayBill #from_state").select2();
						$("#generateEwayBill #ship_city").html("");
						$("#generateEwayBill #ship_city").html(data.ship_city);
						$("#generateEwayBill #ship_city").select2();
						$("#generateEwayBill #ship_state").val(data.ship_state);
						$("#generateEwayBill #ship_state").select2();
					}
				});
			}
		}
	});

	$(document).on('change',"#from_state",function(){
		var id=$("#from_state").val();
		$.ajax({
			url: base_url + 'parties/getCitiesOptions',
			type: 'post',
			data: {
				state_id: id
			},
			dataType: 'json',
			success: function(data) {
				if (data.status == 0) {
					//swal("Sorry...!", data.message, "error");
				} else {
					$("#from_city").html(data.result);					
					$("#from_city").select2();
				}
			}
		});
	});

	$(document).on('change',"#dispatch_country",function(){
		var id=$("#dispatch_country").val();
		$.ajax({
			url: base_url + 'parties/getStatesOptions',
			type: 'post',
			data: {
				country_id: id
			},
			dataType: 'json',
			success: function(data) {
				if (data.status == 0) {
					//swal("Sorry...!", data.message, "error");
				} else {
					$("#dispatch_state").html(data.result);					
					$("#dispatch_state").select2();
					$("#dispatch_state").focus();
					$("#dispatch_city").html('<option value="">Select City</option>');					
					$("#dispatch_city").select2();
				}
			}
		});
	});

	$(document).on('change',"#dispatch_state",function(){
		var id=$("#dispatch_state").val();
		$.ajax({
			url: base_url + 'parties/getCitiesOptions',
			type: 'post',
			data: {
				state_id: id
			},
			dataType: 'json',
			success: function(data) {
				if (data.status == 0) {
					//swal("Sorry...!", data.message, "error");
				} else {
					$("#dispatch_city").html(data.result);					
					$("#dispatch_city").select2();
					$("#dispatch_city").focus();
				}
			}
		});
	});

	$(document).on('change',"#billing_country",function(){
		var id=$("#billing_country").val();
		$.ajax({
			url: base_url + 'parties/getStatesOptions',
			type: 'post',
			data: {
				country_id: id
			},
			dataType: 'json',
			success: function(data) {
				if (data.status == 0) {
					//swal("Sorry...!", data.message, "error");
				} else {
					$("#billing_state").html(data.result);					
					$("#billing_state").select2();
					$("#billing_state").focus();
					$("#billing_city").html('<option value="">Select City</option>');					
					$("#billing_city").select2();
				}
			}
		});
	});

	$(document).on('change',"#billing_state",function(){
		var id=$("#billing_state").val();
		$.ajax({
			url: base_url + 'parties/getCitiesOptions',
			type: 'post',
			data: {
				state_id: id
			},
			dataType: 'json',
			success: function(data) {
				if (data.status == 0) {
					//swal("Sorry...!", data.message, "error");
				} else {
					$("#billing_city").html(data.result);					
					$("#billing_city").select2();
					$("#billing_city").focus();
				}
			}
		});
	});

	$(document).on('change',"#ship_country",function(){
		var id=$("#ship_country").val();
		$.ajax({
			url: base_url + 'parties/getStatesOptions',
			type: 'post',
			data: {
				country_id: id
			},
			dataType: 'json',
			success: function(data) {
				if (data.status == 0) {
					//swal("Sorry...!", data.message, "error");
				} else {
					$("#ship_state").html(data.result);					
					$("#ship_state").select2();
					$("#ship_state").focus();
					$("#ship_city").html('<option value="">Select City</option>');					
					$("#ship_city").select2();
				}
			}
		});
	});

	$(document).on('change',"#ship_state",function(){
		var id=$("#ship_state").val();
		$.ajax({
			url: base_url + 'parties/getCitiesOptions',
			type: 'post',
			data: {
				state_id: id
			},
			dataType: 'json',
			success: function(data) {
				if (data.status == 0) {
					//swal("Sorry...!", data.message, "error");
				} else {
					$("#ship_city").html(data.result);					
					$("#ship_city").select2();
					$("#ship_city").focus();
				}
			}
		});
	});

	$('#from_address').attr({ maxLength : 120 });
	$('#ship_address').attr({ maxLength : 120 });
});

function ebillFrom(data){
	var button = data.button;if(button == "" || button == null){button="both";};
	var fnedit = data.fnedit;if(fnedit == "" || fnedit == null){fnedit="edit";}
	var fnsave = data.fnsave;if(fnsave == "" || fnsave == null){fnsave="save";}
	var controllerName = data.controller;if(controllerName == "" || controllerName == null){controllerName=controller;}
	var resFunction = data.res_function || "";
	var jsStoreFn = data.js_store_fn || 'store';
	var syncBtn = data.syncBtn;if(syncBtn == "" || syncBtn == null){syncBtn=0;}
	var save_btn_text = data.save_btn_text;if(save_btn_text == "" || save_btn_text == null){save_btn_text= '<i class="fa fa-print"></i> Generate'}

	var fnJson = "{'formId':'"+data.form_id+"','fnsave':'"+fnsave+"','controller':'"+controllerName+"'}";

	$.ajax({ 
		type: "POST",   
		url: base_url + controllerName + '/' + fnedit,
		data: data.postData,
	}).done(function(response){
		$("#"+data.modal_id).modal({show:true});
		$("#"+data.modal_id).css({'z-index':9999,'overflow':'auto'});
		$("#"+data.modal_id).addClass(data.form_id+"Modal");
		$("#"+data.modal_id+' .modal-title').html("");
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html('');
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
		if(resFunction != ""){
			$("#"+data.modal_id+" .modal-body form").attr('data-res_function',resFunction);
		}
		$("#"+data.modal_id+" .modal-footer .btn-save").html(save_btn_text);
		$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',jsStoreFn+"("+fnJson+");");

		$("#"+data.modal_id+" .modal-header .close").attr('data-modal_id',data.modal_id);
		$("#"+data.modal_id+" .modal-header .close").attr('data-modal_class',data.form_id+"Modal");
		$("#"+data.modal_id+" .modal-footer .btn-close").attr('data-modal_id',data.modal_id);
		$("#"+data.modal_id+" .modal-footer .btn-close").attr('data-modal_class',data.form_id+"Modal");

		if(button == "close"){
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").hide();
		}else if(button == "save"){
			$("#"+data.modal_id+" .modal-footer .btn-close").hide();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}else{
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}

		$(".syncEwbBtn").remove();
		if(syncBtn == 1 && fnedit == "addEwayBill"){
			var syncEwbFnJson = "{'formId':'"+data.form_id+"','fnsave':'syncEwayBill','controller':'"+controllerName+"'}";
			var syncButton = $("<button><i class='fas fa-sync'></i> Sync E-Way Bill</button>");
			syncButton.attr('type','button');
			syncButton.attr('onclick','syncEwayBill('+syncEwbFnJson+');');
			syncButton.attr('class','btn waves-effect waves-light btn-outline-primary ml-2 syncEwbBtn');
			$("#"+data.modal_id+' .modal-footer').append(syncButton);
		}

		$(".syncEinvBtn").remove();
		$(".jsonDownloadBtn").remove();
		if(syncBtn == 1 && fnedit == "addEinvoice"){
			var syncEinvFnJson = "{'formId':'"+data.form_id+"','fnsave':'syncEinvoice','controller':'"+controllerName+"'}";
			var syncButton = $("<button><i class='fas fa-sync'></i> Sync E-Invoice</button>");
			syncButton.attr('type','button');
			syncButton.attr('onclick','syncEinv('+syncEinvFnJson+');');
			syncButton.attr('class','btn waves-effect waves-light btn-outline-primary ml-2 syncEinvBtn');
			$("#"+data.modal_id+' .modal-footer').append(syncButton);

			var jsonButton = $("<button><i class='fas fa-download'></i> E-INV Json</button>");
			jsonButton.attr('type','button');
			jsonButton.attr('onclick','downloadEinvJson("'+data.form_id+'");');
			jsonButton.attr('class','btn waves-effect waves-light btn-outline-dark ml-2 jsonDownloadBtn');
			$("#"+data.modal_id+' .modal-footer').append(jsonButton);
		}
		
		$(".single-select").comboSelect();
		$('.select2').select2({with:null});
		$("#"+data.modal_id+" .scrollable").perfectScrollbar({suppressScrollX: true});
		initMultiSelect();setPlaceHolder();

		if(fnedit == "addEwayBill"){
			setTimeout(function(){ 
				$("#"+data.form_id+" #transaction_type").trigger('change');
			},1000);
		}

		if(fnedit == "addEinvoice"){
			$("#"+data.form_id+" #dispatch_country").val(101);
			$("#"+data.form_id+" #dispatch_state").val(4030);
			$("#"+data.form_id+" #dispatch_city").val(133679);
			$('.select2').select2();
			//$(".single-select").comboSelect();
		}
	});
}

function generateEwb(postData){
	setPlaceHolder();
	var formId = postData.formId || "";
	var fnsave = postData.fnsave || "save";
	var controllerName = postData.controller || controller;

	var form = $('#'+formId)[0];
	var fd = new FormData(form);

	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure to generate E-Way Bill?',
		type: 'green',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controllerName + '/' + fnsave,
						data:fd,
						type: "POST",
						processData:false,
						contentType:false,
						dataType:"json",
					}).done(function(data){
						if(data.status===0){
							$(".error").html("");
							$.each( data.message, function( key, value ) {$("."+key).html(value);});
						}else if(data.status==1){
							initTable(); $('#'+formId)[0].reset();colseModal(formId);
							toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
						}else{
							initTable(); 
							toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
						}				
					});
				}
			},
			cancel: {
				btnClass: 'btn waves-effect waves-light btn-outline-secondary',
				action: function(){

				}
			}
		}
	});
}

function syncEwayBill(postData){
	setPlaceHolder();
	var formId = postData.formId || "";
	var fnsave = postData.fnsave || "save";
	var controllerName = postData.controller || controller;

	var form = $('#'+formId)[0];
	var fd = new FormData(form);

	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure to Sync E-Way Bill?',
		type: 'green',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controllerName + '/' + fnsave,
						data:fd,
						type: "POST",
						processData:false,
						contentType:false,
						dataType:"json",
					}).done(function(data){
						if(data.status===0){
							$(".error").html("");
							$.each( data.message, function( key, value ) {$("."+key).html(value);});
						}else if(data.status==1){
							initTable(); $('#'+formId)[0].reset();colseModal(formId);
							toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
						}else{
							initTable(); 
							toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
						}								
					});
				}
			},
			cancel: {
                btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                action: function(){

				}
            }
		}
	});
}

function cancelEwayBill(postData){
	setPlaceHolder();
	var formId = postData.formId || "";
	var fnsave = postData.fnsave || "save";
	var controllerName = postData.controller || controller;

	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure to generate E-Way Bill?',
		type: 'green',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controllerName + '/' + fnsave,
						data:fd,
						type: "POST",
						processData:false,
						contentType:false,
						dataType:"json",
					}).done(function(data){
						if(data.status===0){
							$(".error").html("");
							$.each( data.message, function( key, value ) {$("."+key).html(value);});
						}else if(data.status==1){
							initTable(); $('#'+formId)[0].reset();colseModal(formId);
							toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
						}else{
							initTable();
							toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
						}				
					});
				}
			},
			cancel: {
				btnClass: 'btn waves-effect waves-light btn-outline-secondary',
				action: function(){

				}
			}
		}
	});
}

function generateEinvoice(postData){	
	setPlaceHolder();
	var formId = postData.formId || "";
	var fnsave = postData.fnsave || "save";
	var controllerName = postData.controller || controller;

	var form = $('#'+formId)[0];
	var fd = new FormData(form);

	var einvPreview = 'Are you sure to generate E-Invoice?';
	$.confirm({
		title: 'Confirm!',
		content: einvPreview,
		type: 'green',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controllerName + '/' + fnsave,
						data:fd,
						type: "POST",
						processData:false,
						contentType:false,
						dataType:"json",
					}).done(function(data){
						if(data.status===0){
							$(".error").html("");
							$.each( data.message, function( key, value ) {$("."+key).html(value);});
						}else if(data.status==1){
							initTable(); $('#'+formId)[0].reset();colseModal(formId);
							toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
						}else{
							initTable();
							toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
						}								
					});
				}
			},
			cancel: {
                btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                action: function(){

				}
            }
		}
	});
}

function syncEinv(postData){
	setPlaceHolder();
	var formId = postData.formId || "";
	var fnsave = postData.fnsave || "save";
	var controllerName = postData.controller || controller;

	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure to Sync E-Invoice?',
		type: 'green',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controllerName + '/' + fnsave,
						data:fd,
						type: "POST",
						processData:false,
						contentType:false,
						dataType:"json",
					}).done(function(data){
						if(data.status===0){
							$(".error").html("");
							$.each( data.message, function( key, value ) {$("."+key).html(value);});
						}else if(data.status==1){
							initTable(); $('#'+formId)[0].reset();colseModal(formId);
							toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
						}else{
							initTable();
							toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
						}								
					});
				}
			},
			cancel: {
                btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                action: function(){

				}
            }
		}
	});
}

function cancelEinv(postData){	
	setPlaceHolder();
	var formId = postData.formId || "";
	var fnsave = postData.fnsave || "save";
	var controllerName = postData.controller || controller;

	var form = $('#'+formId)[0];
	var fd = new FormData(form);

	var einvPreview = 'Are you sure to cancel this Tax Invoice?';
	$.confirm({
		title: 'Confirm!',
		content: einvPreview,
		type: 'green',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controllerName + '/' + fnsave,
						data:fd,
						type: "POST",
						processData:false,
						contentType:false,
						dataType:"json",
					}).done(function(data){
						if(data.status===0){
							$(".error").html("");
							$.each( data.message, function( key, value ) {$("."+key).html(value);});
						}else if(data.status==1){
							initTable(); $('#'+formId)[0].reset();colseModal(formId);
							toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
						}else{
							initTable();
							toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
						}								
					});
				}
			},
			cancel: {
                btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                action: function(){

				}
            }
		}
	});
}

function downloadEinvJson(formId){
	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure to Download E-Invoice Json File?',
		type: 'green',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + 'ebill/downloadEinvJson',
						data:fd,
						type: "POST",
						processData:false,
						contentType:false,
						dataType:"json",
					}).done(function(data){
						if(data.status===0){
							$(".error").html("");
							$.each( data.message, function( key, value ) {$("."+key).html(value);});
						}else if(data.status==1){  
							var jsonText = "["+JSON.stringify(data.json_data)+"]";
							var blob = new Blob([jsonText], {
								type: 'application/json'
							});
							var link = document.createElement('a');
							link.href = window.URL.createObjectURL(blob);
							link.download = "EINV-JSON-"+data.inv_no+".json";
							link.click();

							toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
						}else{
							toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
						}								
					});
				}
			},
			cancel: {
                btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                action: function(){

				}
            }
		}
	});
}

