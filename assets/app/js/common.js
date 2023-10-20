var qsRegex;
var isoOptions = {
  itemSelector: ".listItem",
  layoutMode: "fitRows",
  filter: function () {
    return qsRegex ? $(this).text().match(qsRegex) : true;
  },
};
// init isotope
var isotopeList = $(".list-grid").isotope(isoOptions);
var $qs = $(".quicksearch").keyup(
  debounce(function () {
    qsRegex = new RegExp($qs.val(), "gi");
    isotopeList.isotope();
  }, 200)
);

$(document).ready(function () {
  checkPermission();
  $(document).on("click", ".closeSearch", function (e) {
    $(".quicksearch").val("");
    $(".quicksearch").trigger("keyup");
  });

  // $(".appBottomMenu .item").removeClass("active");
  console.log(menu_id);
  // $(".appBottomMenu")
  //   .find(".item:eq(" + menu_id + ")")
  //   .addClass("active");
  // $('.appBottomMenu').find('.item:eq(' + menu_id + ')').addClass('active');
  $(document).ajaxStart(function () {
    $(".ajaxLoader").show();
  });
  $(document).ajaxComplete(function () {
    $(".ajaxLoader").hide();
  });

  
	$(document).on('click','.pswHideShow',function(){
		var type = $('.pswType').attr('type');
		if(type == "password"){
			$(".pswType").attr('type','text');
			$(this).html('<i class="fa fa-eye-slash"></i>');
		}else{
			$(".pswType").attr('type','password');
			$(this).html('<i class="fa fa-eye"></i>');
		}
	});

  $(".select2").select2();setPlaceHolder();

  $(document).on('click',".addNew",function(){
    var functionName = $(this).data("function");
    var modalId = $(this).data('modal_id');
    var button = $(this).data('button');
    var title = $(this).data('form_title');
    var formId = $(this).data('form_id') || functionName.split('/')[0];
    var controllerName = $(this).data('controller') || controller;
    var postData = $(this).data('postdata') || {};
    var fnsave = $(this).data("fnsave") || "save";
    var resFunction = $(this).data('res_function') || "";
    var savebtn_text = $(this).data('savebtn_text') || '<i class="fa fa-check"></i> Save';
    var saveController = $(this).data('savecontroller') || controller;
    var fnJson = "{'formId':'"+formId+"','controller':'"+saveController+"','fnsave':'"+fnsave+"'}";

    if(typeof postData === "string"){
      postData = JSON.parse(postData);
    }

        $.ajax({ 
            type: "post",   
            url: base_url + controllerName + '/' + functionName,   
            data: postData
        }).done(function(response){
            $("#"+modalId).modal('show');
            $("#"+modalId+'').addClass(formId+"Modal");
            $("#"+modalId+' .modal-title').html(title);
            $("#"+modalId+' .modal-body').html("");
                  $("#"+modalId+' .modal-body').html(response);
                  $("#"+modalId+" .modal-body form").attr('id',formId);
            if(resFunction != ""){
              $("#"+modalId+" .modal-body form").attr('data-res_function',resFunction);
            }
            $("#"+modalId+" .modal-footer .btn-save").html(savebtn_text);
            $("#"+modalId+" .modal-footer .btn-save").attr('onclick',"store("+fnJson+");");

            $("#"+modalId+" .modal-header .close").attr('data-modal_id',modalId);
            $("#"+modalId+" .modal-header .close").attr('data-modal_class',formId+"Modal");
            $("#"+modalId+" .modal-footer .btn-close").attr('data-modal_id',modalId);
            $("#"+modalId+" .modal-footer .btn-close").attr('data-modal_class',formId+"Modal");

            if(button == "close"){
                $("#"+modalId+" .modal-footer .btn-close").show();
                $("#"+modalId+" .modal-footer .btn-save").hide();
            }else if(button == "save"){
                $("#"+modalId+" .modal-footer .btn-close").hide();
                $("#"+modalId+" .modal-footer .btn-save").show();
            }else{
                $("#"+modalId+" .modal-footer .btn-close").show();
                $("#"+modalId+" .modal-footer .btn-save").show();
            }
      
        });
    });

    $(document).on('change click',".itemDetails",function(){
      var item_id = $(this).val();
      var resFunctionName = $(this).data('res_function') || "";
      
      if(item_id){
        $.ajax({
          url : base_url + '/items/getItemDetails',
          type:'post',
          data: {id:item_id},
          dataType : 'json',
        }).done(function(response){
          window[resFunctionName](response);
        });
      }else{
        window[resFunctionName]();
      }
    });
});

/** Init Isotope Grid **/
function initListview(ele = ".list-grid") {
  if ($(document).find(ele).data("isotope")) {
    isotopeList.isotope("destroy").isotope(isoOptions);
  } else {
    isotopeList = $(ele).isotope(isoOptions);
  }
}

function debounce(fn, threshold) {
  var timeout;
  threshold = threshold || 100;
  return function debounced() {
    clearTimeout(timeout);
    var args = arguments;
    var _this = this;

    function delayed() {
      fn.apply(_this, args);
    }
    timeout = setTimeout(delayed, threshold);
  };
}
$(".listTab a").on("shown.bs.tab", function (e) {
  $(".list-grid").isotope("layout");
});

function setPlaceHolder() {
  // $("input[name=item_name]").alphanum({allow: '-()."+@#%&*!|/[]{},?<>_=:^', allowSpace: true});
  var label = "";
  $("input").each(function () {
    if (!$(this).attr("placeholder")) {
      if (
        !$(this).hasClass("combo-input") &&
        $(this).attr("type") != "hidden"
      ) {
        label = "";
        inputElement = $(this).parent();
        if ($(this).parent().hasClass("input-group")) {
          inputElement = $(this).parent().parent();
        } else {
          inputElement = $(this).parent();
        }
        label = inputElement.children("label").text();
        label = label.replace("*", "");
        label = $.trim(label);
        if ($(this).hasClass("req")) {
          inputElement
            .children("label")
            .html(label + ' <strong class="text-danger">*</strong>');
        }
        if (label) {
          $(this).attr("placeholder", label);
        }
        $(this).attr("autocomplete", "off");
        var errorClass = "";
        var nm = $(this).attr("name");
        if ($(this).attr("id")) {
          errorClass = $(this).attr("id");
        } else {
          errorClass = $(this).attr("name");
          if (errorClass) {
            errorClass = errorClass.replace("[]", "");
          }
        }
        if (inputElement.find("." + errorClass).length <= 0) {
          inputElement.append('<div class="error ' + errorClass + '"></div>');
        }
      } else {
        $(this).attr("autocomplete", "off");
      }
    } else {
      if (
        !$(this).hasClass("combo-input") &&
        $(this).attr("type") != "hidden"
      ) {
        inputElement = $(this).parent();
        var errorClass = "";
        var nm = $(this).attr("name");
        if ($(this).attr("id")) {
          errorClass = $(this).attr("id");
        } else {
          errorClass = $(this).attr("name");
          if (errorClass) {
            errorClass = errorClass.replace("[]", "");
          }
        }
        if (inputElement.find("." + errorClass).length <= 0) {
          inputElement.append('<div class="error ' + errorClass + '"></div>');
        }
      } else {
        $(this).attr("autocomplete", "off");
      }
    }
  });
  $("textarea").each(function () {
    if (!$(this).attr("placeholder")) {
      label = "";
      label = $(this).parent().children("label").text();
      label = label.replace("*", "");
      label = $.trim(label);
      if ($(this).hasClass("req")) {
        $(this)
          .parent()
          .children("label")
          .html(label + ' <strong class="text-danger">*</strong>');
      }
      if (label) {
        $(this).attr("placeholder", label);
      }
      $(this).attr("autocomplete", "off");
      var errorClass = "";
      var nm = $(this).attr("name");
      if ($(this).attr("name")) {
        errorClass = $(this).attr("name");
      } else {
        errorClass = $(this).attr("id");
      }
      //if($(this).parent().find('.'+errorClass).length <= 0){$(this).parent().append('<div class="error '+ errorClass +'"></div>');}
    }
  });
  $("select").each(function () {
    if (!$(this).attr("placeholder")) {
      label = "";
      var selectElement = $(this).parent();
      if ($(this).hasClass("single-select")) {
        selectElement = $(this).parent().parent();
      }
      label = selectElement.children("label").text();
      label = label.replace("*", "");
      label = $.trim(label);
      if ($(this).hasClass("req")) {
        selectElement
          .children("label")
          .html(label + ' <strong class="text-danger">*</strong>');
      }
      var errorClass = "";
      var nm = $(this).attr("name");
      if ($(this).attr("name") && $(this).attr("name").search("[]") != -1) {
        errorClass = $(this).attr("name");
      } else {
        errorClass = $(this).attr("id");
      }
      if (selectElement.find("." + errorClass).length <= 0) {
        selectElement.append('<div class="error ' + errorClass + '"></div>');
      }
    }
  });
}

$(document).on("change", "#country_id", function () {
  var id = $(this).val();
  if (id == "") {
    $("#state_id").html('<option value="">Select State</option>');
    $("#city_id").html('<option value="">Select City</option>');
    // $(".single-select").comboSelect();
  } else {
    $.ajax({
      url: base_url + "parties/getStates",
      type: "post",
      data: { id: id },
      dataType: "json",
      success: function (data) {
        if (data.status == 0) {
          swal("Sorry...!", data.message, "error");
        } else {
          $("#state_id").html(data.result);
        //   $(".single-select").comboSelect();
          $("#state_id").focus();
        }
      },
    });
  }
});

$(document).on("change", "#state_id", function () {
  var id = $(this).val();
  if (id == "") {
    $("#city_id").html('<option value="">Select City</option>');
    // $(".single-select").comboSelect();
  } else {
    $.ajax({
      url: base_url + "parties/getCities",
      type: "post",
      data: { id: id },
      dataType: "json",
      success: function (data) {
        if (data.status == 0) {
          swal("Sorry...!", data.message, "error");
        } else {
          $("#city_id").html(data.result);
        //   $(".single-select").comboSelect();
          $("#city_id").focus();
        }
      },
    });
  }
});

function changePsw(formId){
	var fd = $('#'+formId).serialize();
	$.ajax({
		url: base_url + 'app/dashboard/changePassword',
		data:fd,
		type: "POST",
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {
				$("."+key).html(value);
			});
		}else if(data.status==1){
			$('#'+formId)[0].reset();
			$('.modal').modal('hide');
			$('#successDialog .modal-body').html(data.message);
			$('#successDialog').modal('show');		
		}else{
			$('#errorDialog .modal-body').html(data.message);
			$('#errorDialog').modal('show');		}
				
	});
}

function checkPermission(){
	$('.permission-read').show();
	$('.permission-write').show();
	$('.permission-modify').show();
	$('.permission-remove').show();
	$('.permission-approve').show();
	console.log(permissionRead);
	if(permissionRead == "1"){ $('.permission-read').show(); }else{ $('.permission-read').hide(); }
	if(permissionWrite == "1"){ $('.permission-write').show(); }else{ $('.permission-write').hide(); }
	if(permissionModify == "1"){ $('.permission-modify').show(); }else{ $('.permission-modify').hide(); }
	if(permissionRemove == "1"){ $('.permission-remove').show(); }else{ $('.permission-remove').hide(); }
	if(permissionApprove == "1"){ $('.permission-approve').show();}else{ $('.permission-approve').hide(); }
}

function setPlaceHolder(){
  // $("input[name=item_name]").alphanum({allow: '-()."+@#%&*!|/[]{},?<>_=:^', allowSpace: true});
  var label="";
  $('input').each(function () {
    if(!$(this).attr('placeholder') )
    {
      if(!$(this).hasClass('combo-input') && $(this).attr("type")!="hidden" )
      {
        label="";
        inputElement = $(this).parent();
        if($(this).parent().hasClass('input-group')){inputElement = $(this).parent().parent();}else{inputElement = $(this).parent();}
        label = inputElement.children("label").text();
        label = label.replace('*','');
        label = $.trim(label);
        if($(this).hasClass('req')){inputElement.children("label").html(label + ' <strong class="text-danger">*</strong>');}
        if(label){$(this).attr("placeholder", label);}
        $(this).attr("autocomplete", 'off');
        var errorClass="";
        var nm = $(this).attr('name');
        if($(this).attr('id')){errorClass=$(this).attr('id');}else{errorClass=$(this).attr('name');if(errorClass){errorClass = errorClass.replace("[]", "");}}
        if(inputElement.find('.'+errorClass).length <= 0){inputElement.append('<div class="error '+ errorClass +'"></div>');}
      }
      else{$(this).attr("autocomplete", 'off');}
      }
      else
    {
      if(!$(this).hasClass('combo-input') && $(this).attr("type")!="hidden" )
      {
        inputElement = $(this).parent();
        var errorClass="";
        var nm = $(this).attr('name');
        if($(this).attr('id')){errorClass=$(this).attr('id');}else{errorClass=$(this).attr('name');if(errorClass){errorClass = errorClass.replace("[]", "");}}
        if(inputElement.find('.'+errorClass).length <= 0){inputElement.append('<div class="error '+ errorClass +'"></div>');}
      }
      else{$(this).attr("autocomplete", 'off');}
      }
  });
  $('textarea').each(function () {
    if(!$(this).attr('placeholder') )
    {
        label="";
      label = $(this).parent().children("label").text();
      label = label.replace('*','');
      label = $.trim(label);
      if($(this).hasClass('req')){$(this).parent().children("label").html(label + ' <strong class="text-danger">*</strong>');}
      if(label){$(this).attr("placeholder", label);}
      $(this).attr("autocomplete", 'off');
      var errorClass="";
      var nm = $(this).attr('name');
      if($(this).attr('name')){errorClass=$(this).attr('name');}else{errorClass=$(this).attr('id');}
      //if($(this).parent().find('.'+errorClass).length <= 0){$(this).parent().append('<div class="error '+ errorClass +'"></div>');}
    }
  });
  $('select').each(function () {
    if(!$(this).attr('placeholder') )
    {
      label="";
      var selectElement = $(this).parent();
      if($(this).hasClass('single-select')){selectElement = $(this).parent().parent();}
      label = selectElement.children("label").text();
      label = label.replace('*','');
      label = $.trim(label);
      if($(this).hasClass('req')){selectElement.children("label").html(label + ' <strong class="text-danger">*</strong>');}
      var errorClass="";
      var nm = $(this).attr('name');
      if($(this).attr('name') && ($(this).attr('name').search('[]') != -1)){errorClass=$(this).attr('name');}else{errorClass=$(this).attr('id');}
      if(selectElement.find('.'+errorClass).length <= 0){selectElement.append('<div class="error '+ errorClass +'"></div>');}
    }
  });
}

	

	
	function store(postData){
		setPlaceHolder();

		var formId = postData.formId;
		var fnsave = postData.fnsave || "save";
		var controllerName = postData.controller || controller;

		var form = $('#'+formId)[0];
		var fd = new FormData(form);
		$.ajax({
			url: base_url + controllerName+'/'+fnsave,
			data:fd,
			type: "POST",
			processData:false,
			contentType:false,
			dataType:"json",
		}).done(function(data){
			if(data.status==1){
				$('#'+formId)[0].reset(); colseModal(formId);
				$("#DialogIconedSuccess .modal-body").html(data.message);
                $("#DialogIconedSuccess").modal('show');
				window.location.reload();
			}else{
				if(typeof data.message === "object"){
					$(".error").html("");
					$.each( data.message, function( key, value ) {$("."+key).html(value);});
				}else{
					$("#DialogIconedDanger .modal-body").html(data.message);
                    $("#DialogIconedDanger").modal('show');
					
				}			
			}				
		});
	}
  
	function colseModal(formId){
		var modal_id = $("."+formId+"Modal").attr('id');
		$("#"+modal_id).removeClass(formId+"Modal");
		$("#"+modal_id+' .modal-body').html("");
		$("#"+modal_id).modal('hide');	
		$(".modal").css({'overflow':'auto'});

		$("#"+modal_id+" .modal-header .close").attr('data-modal_id',"");
		$("#"+modal_id+" .modal-header .close").attr('data-modal_class',"");
		$("#"+modal_id+" .modal-footer .btn-close").attr('data-modal_id',"");
		$("#"+modal_id+" .modal-footer .btn-close").attr('data-modal_class',"");
		$(".select2").select2();
	}

	function edit(data){
		var button = data.button;if(button == "" || button == null){button="both";};
		var fnedit = data.fnedit;if(fnedit == "" || fnedit == null){fnedit="edit";}
		var fnsave = data.fnsave;if(fnsave == "" || fnsave == null){fnsave="save";}
		var controllerName = data.controller;if(controllerName == "" || controllerName == null){controllerName=controller;}
		var savebtn_text = data.savebtn_text;
		if(savebtn_text == "" || savebtn_text == null){savebtn_text='<i class="fa fa-check"></i> Save';}

		var resFunction = data.res_function || "";
		var jsStoreFn = data.js_store_fn || 'store';

    var saveController = data.saveController || controller;
		var fnJson = "{'formId':'"+data.form_id+"','fnsave':'"+fnsave+"','controller':'"+saveController+"'}";

		$.ajax({ 
			type: "POST",   
			url: base_url + controllerName + '/' + fnedit,   
			data: data.postData,
		}).done(function(response){
			$("#"+data.modal_id).modal('show');
			$("#"+data.modal_id).css({'z-index':1059,'overflow':'auto'});
			$("#"+data.modal_id).addClass(data.form_id+"Modal");
			$("#"+data.modal_id+' .modal-title').html(data.title);
			$("#"+data.modal_id+' .modal-body').html('');
			$("#"+data.modal_id+' .modal-body').html(response);
			$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
			if(resFunction != ""){
				$("#"+data.modal_id+" .modal-body form").attr('data-res_function',resFunction);
			}
			$("#"+data.modal_id+" .modal-footer .btn-save").html(savebtn_text);
			$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"store("+fnJson+");");

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

		
			$('.select2').select2();

			setPlaceHolder();
		});
	}

	function getTransHtml(data){
		var postData = data.postData || {};
		var fnget = data.fnget || "";
		var controllerName = data.controller || controller;
		var resFunctionName = data.res_function || "";

		var table_id = data.table_id || "";
		var thead_id = data.thead_id || "";
		var tbody_id = data.tbody_id || "";
		var tfoot_id = data.tfoot_id || "";	

		if(thead_id != ""){
			$("#"+table_id+" #"+thead_id).html(data.thead);
		}
		
		$.ajax({
			url: base_url + controllerName + '/' + fnget,
			data:postData,
			type: "POST",
			dataType:"json",
			beforeSend: function() {
				if(table_id != ""){
					var columnCount = $('#'+table_id+' thead tr').first().children().length;
					$("#"+table_id+" #"+tbody_id).html('<tr><td colspan="'+columnCount+'" class="text-center">Loading...</td></tr>');
				}
			},
		}).done(function(res){
			if(resFunctionName != ""){
				window[resFunctionName](response);
			}else{
				$("#"+table_id+" #"+tbody_id).html('');
				$("#"+table_id+" #"+tbody_id).html(res.tbodyData);

				if(tfoot_id != ""){
					$("#"+table_id+" #"+tfoot_id).html('');
					$("#"+table_id+" #"+tfoot_id).html(res.tfootData);
				}
			}
		});
	}

	function customStore(postData){
		setPlaceHolder();

		var formId = postData.formId;
		var fnsave = postData.fnsave || "save";
		var controllerName = postData.controller || controller;

		var form = $('#'+formId)[0];
		var fd = new FormData(form);
		var resFunctionName = $("#"+formId).data('res_function') || "";

		$.ajax({
			url: base_url  + controllerName+'/' + fnsave,
			data:fd,
			type: "POST",
			processData:false,
			contentType:false,
			dataType:"json",
		}).done(function(data){
			if(resFunctionName != ""){
				window[resFunctionName](data,formId);
			}else{
				if(data.status==1){
					$("#DialogIconedSuccess .modal-body").html(data.message);
                	$("#DialogIconedSuccess").modal('show');
				}else{
					if(typeof data.message === "object"){
						$(".error").html("");
						$.each( data.message, function( key, value ) {$("."+key).html(value);});
					}else{
						$("#DialogIconedDanger .modal-body").html(data.message);
                    	$("#DialogIconedDanger").modal('show');
					}			
				}	
			}			
		});
	}

	function trash(data){
		var controllerName = data.controller || controller;
		var fnName = data.fndelete || "delete";
		var msg = data.message || "Record";
		var send_data = data.postData;
		var resFunctionName = data.res_function || "";

		$.confirm({
			title: 'Confirm!',
			content: 'Are you sure want to delete this '+msg+'?',
			type: 'red',
			buttons: {   
				ok: {
					text: "ok!",
					btnClass: 'btn waves-effect waves-light btn-outline-success',
					keys: ['enter'],
					action: function(){
						$.ajax({
							url: base_url + controllerName+'/' + fnName,
							data: send_data,
							type: "POST",
							dataType:"json",
						}).done(function(response){
							if(resFunctionName != ""){
								window[resFunctionName](response);
							}else{
								if(response.status==0){
									$("#DialogIconedSuccess .modal-body").html(data.message);
        					$("#DialogIconedSuccess").modal('show');
								}else{
									$("#DialogIconedSuccess .modal-body").html(data.message);
        					$("#DialogIconedSuccess").modal('show');
								}	
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
