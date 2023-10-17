<?php $this->load->view('app/includes/header');?>

<div class="appHeader bg-primary">
	<div class="left">
		<a href="#" class="headerButton goBack text-white">
			<ion-icon name="chevron-back-outline" role="img" class="md hydrated" aria-label="chevron back outline"></ion-icon>
		</a>
	</div>
	<div class="pageTitle text-white"><?=$headData->pageTitle?></div>
	<div class="right">
		<a href="#" class="headerButton toggle-searchbox text-white">
			<ion-icon name="search-outline" role="img" class="md hydrated searchbtn" aria-label="search outline"></ion-icon>
		</a>
		<a href="javascript:void(0)" class="headerButton  text-white addNew"  data-button="both" data-modal_id="actionSheetForm" data-function="addLead" data-form_title="Add Approach" data-savecontroller="lead">
			<ion-icon name="add-outline" role="img" class="md hydrated " aria-label="Add Approach"></ion-icon>
		</a>
	</div>
</div>
<div id="search" class="appHeader bg-info">
	<!--<form class="search-form">-->
		<div class="form-group searchbox">
			<input type="text" class="form-control quicksearch" placeholder="Search...">
			<i class="input-icon icon ion-ios-search"></i>
			<a href="#" class="ms-1 close toggle-searchbox"><i class="icon ion-ios-close-circle text-white"></i></a>
		</div>
	<!--</form>-->
</div>

<div class="extraHeader pe-0 ps-0">
	<ul class="nav nav-tabs lined" role="tablist">
	
		<li class="nav-item"> <a class="nav-link  <?=empty($lead_status) ? 'active' : ''?>"  href="<?=base_url($headData->controller . "/index/0")?>" role="tab">Pending</a> </li>
		<li class="nav-item"> <a class="nav-link  <?=(!empty($lead_status) && $lead_status == 3) ? 'active' : ''?>"  href="<?=base_url($headData->controller . "/index/3")?>" role="tab">Won</a> </li>
		<li class="nav-item"> <a class="nav-link <?=(!empty($lead_status) && $lead_status == 4) ? 'active' : ''?>"  href="<?=base_url($headData->controller . "/index/4/")?>" role="tab">Lost</a> </li>
	</ul>
</div>

<!-- Start Main Content -->
<div id="appCapsule" class="extra-header-active full-height">
	<div class="tab-content mb-1">
		<ul class="listview image-listview media flush no-line list-grid mb-1" data-isotope='{ "itemSelector": ".listItem" }'>
			<?php
				$listRows = '';
				$i = 1;
				if (!empty($leadData)) {
					foreach ($leadData as $row):
						$row->appointments = '';
						$row->followupDate = '';
						$row->followupNote = '';
						$followupData = $this->leads->getFollowupData(['entry_type' => 1, 'lead_id' => $row->id]);
						if(!empty($followupData)):
							$row->followupDate = formatDate($followupData->appointment_date);
							$row->next_fup_date = formatDate($followupData->next_fup_date);
							$row->followupNote =$followupData->notes;
						endif;
			
						if ($row->lead_status == 0 || $row->lead_status == 1):
							$appointsData = $this->leads->getAppointments(['entry_type' => 2, 'lead_id' => $row->id, 'status' => 0]);
							if (!empty($appointsData)):
								$apArray = [];
								foreach ($appointsData as $ap):
									$style = "";
									if (date('Y-m-d H:i:s') >= date("Y-m-d H:i:s", strtotime($ap->appointment_date . ' ' . $ap->appointment_time . ' -24 Hours'))):
										$style = 'text-danger';
									endif;
			
									$appoParam = "{'postData' : {'id' : ".$ap->id."}, 'modal_id' : 'actionSheetForm', 'form_id' : 'appointmentStatus', 'title' : 'Appointment Status','fnsave':'saveAppointmentStatus','fnedit':'appointmentStatus'}";
			
									$apArray[] = '<a href="javascript:void(0)" class="' . $style . '" onclick="edit('.$appoParam.');">' . formatDate($ap->appointment_date, 'd-m-Y ') . formatDate($ap->appointment_time, 'H:i A') . '</a>';
								endforeach;
			
								$row->appointments = implode("<hr style='margin-top:0px;margin-bottom:0px'>", $apArray);
							endif;
						endif;
						$followupBtn = '';$appointmentBtn ='';$enqBtn='';$editButton="";$deleteButton="";$leadStatusButton = "";
				   
						if(in_array($row->lead_status,[0,4])):
							$followupParam = "{'postData': {'id' : ".$row->id.",'party_id':".$row->party_id.",'sales_executive':".$row->sales_executive.",'entry_type':1}, 'modal_id' : 'actionSheetForm', 'form_id' : 'followUp', 'title' : 'Follow up', 'fnedit' : 'addFollowup', 'fnsave' : 'saveFollowup','res_function' : 'resFollowup', 'button' : 'close','saveController':'lead'}";
							$followupBtn = '<a class="dropdown-item leadAction"  onclick="edit('.$followupParam.');" >
												<ion-icon name="clipboard-outline"></ion-icon>  Followup
											</a>';
			
							$appointmentParam = "{'postData': {'id' : ".$row->id.",'party_id':".$row->party_id.",'entry_type':2}, 'modal_id' : 'actionSheetForm', 'form_id' : 'appointment', 'title' : 'Appointments', 'fnedit' : 'addAppointment', 'fnsave' : 'saveAppointment','res_function' : 'resAppointments', 'button' : 'close','saveController':'lead'}";
							$appointmentBtn = '<a class="dropdown-item leadAction"  onclick="edit('.$appointmentParam.');" >
												<ion-icon name="calendar-outline"></ion-icon>  Appointment
											</a>';
						endif;
			
						if($row->lead_status == 0 && empty($row->enq_id)):      
							$editParam = "{'postData' : {'id' : ".$row->id."}, 'modal_id' : 'actionSheetForm', 'form_id' : 'editLead', 'title' : 'Update Approach','saveController':'lead'}";
						
							$editButton = '<a class="dropdown-item leadAction"  onclick="edit('.$editParam.');" >
												<ion-icon name="pencil-outline"></ion-icon>  Edit
											</a>';
			
							
							$leadParam = "{'postData' : {'id' : ".$row->id."}, 'modal_id' : 'actionSheetForm', 'form_id' : 'approachStatus', 'title' : 'Update Approach Status','fnedit':'approachStatus','fnsave':'saveApproachStatus','saveController':'lead'}";
							$leadStatusButton = '<a class="dropdown-item leadAction"  onclick="edit('.$leadParam.');">
													<ion-icon name="checkmark"></ion-icon> Approach Status
												</a>';
						endif;
			
						echo '<li>
									<div class="listItem item transition "  data-category="transition">
										<div class="in ">
											<div>
												<a href="#" class="text-bold-500 text-dark">'.$row->party_name.'</a>
												<div class="text-small  text-secondary \"><label class="text-bold">Approach No : </label> '.sprintf("%04d",$row->lead_no).' | <label class="text-bold">Approach Date : </label>'.formatDate($row->lead_date).'</div>
												<div class="text-small text-secondary "> <label class="text-bold">Lead From : </label> '.$row->lead_from.' | <label class="text-bold">Sales Executive : </label> '.$row->emp_name.'</div>
												<div class="text-small text-secondary "> <label class="text-bold">Followup Date: </label> '.$row->followupDate.' | <label class="text-bold">Next Followup : </label> '.$row->next_fup_date.'</div>
											</div>
											<div class="text-end">
												<div class="card-button dropdown">
													<button type="button" class="btn btn-link btn-icon" data-bs-toggle="dropdown" aria-expanded="false">
														<ion-icon name="ellipsis-horizontal" role="img" class="md hydrated" aria-label="ellipsis horizontal"></ion-icon>
													</button>
													<div class="dropdown-menu dropdown-menu-end" style="">
														'.$appointmentBtn.$followupBtn.$leadStatusButton.$editButton.$deleteButton.'
													</div>
												</div>
											</div>
										</div>
									</div>
								</li>';
					endforeach;
				}
			?>
			
		</ul>
	</div>
</div>
<!-- End Main Content -->


<?php $this->load->view('app/includes/bottom_menu');?>
<?php $this->load->view('app/includes/sidebar');?>
<?php $this->load->view('app/includes/add_to_home');?>
<?php $this->load->view('app/includes/footer');?>
<script>
$(document).ready(function(){
	var qsRegex;
	var isoOptions = {
		itemSelector: '.listItem',
		layoutMode: 'fitRows',
		filter: function() {return qsRegex ? $(this).text().match( qsRegex ) : true;}
	};
	// init isotope
	var $grid = $('.list-grid').isotope( isoOptions );
	var $qs = $('.quicksearch').keyup( debounce( function() {qsRegex = new RegExp( $qs.val(), 'gi' );$grid.isotope();}, 200 ) );

//$(document).on('keyup',".quicksearch",function(){console.log($(this).val());});
});

function searchItems(ele){
	console.log($(ele).val());
}

function debounce( fn, threshold ) {
  var timeout;
  threshold = threshold || 100;
  return function debounced() {
	clearTimeout( timeout );
	var args = arguments;
	var _this = this;
	function delayed() {fn.apply( _this, args );}
	timeout = setTimeout( delayed, threshold );
  };
}
</script>

