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
		<a href="javascript:void(0)" class="headerButton  text-white addNew"  data-button="both" data-modal_id="actionSheetForm" data-function="addStock" data-form_title="Add FG STock" data-savecontroller="stockTrans">
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

<!-- <div class="extraHeader pe-0 ps-0">
	<ul class="nav nav-tabs lined" role="tablist">
	
		<li class="nav-item"> <a class="nav-link  <?=empty($lead_status) ? 'active' : ''?>"  href="<?=base_url($headData->controller . "/index/0")?>" role="tab">Pending</a> </li>
		<li class="nav-item"> <a class="nav-link  <?=(!empty($lead_status) && $lead_status == 3) ? 'active' : ''?>"  href="<?=base_url($headData->controller . "/index/3")?>" role="tab">Won</a> </li>
		<li class="nav-item"> <a class="nav-link <?=(!empty($lead_status) && $lead_status == 4) ? 'active' : ''?>"  href="<?=base_url($headData->controller . "/index/4/")?>" role="tab">Lost</a> </li>
	</ul>
</div> -->

<!-- Start Main Content -->
<div id="appCapsule" class=" full-height">
	<div class="tab-content mb-1">
		<ul class="listview image-listview media flush no-line list-grid mb-1" data-isotope='{ "itemSelector": ".listItem" }'>
			<?php
				$listRows = '';
				$i = 1;
				if (!empty($stockData)) {
					foreach ($stockData as $row):
						
			
                        $deleteParam = "{'postData':{'id' : ".$row->id."},'message' : 'Stock','controller':'stockTrans'}";
                        $deleteButton = '<a class="dropdown-item leadAction"  onclick="trash('.$deleteParam.');" >
                                            <ion-icon name="trash"></ion-icon>  Delete
                                        </a>';
						echo '<li>
									<div class="listItem item transition "  data-category="transition">
										<div class="in ">
											<div>
												<a href="#" class="text-bold-500 text-dark">'.$row->item_name.'</a>
												<div class="text-small  text-secondary"><label class="text-bold">Date : </label> '.formatDate($row->ref_date).' | <label class="text-bold">Brand : </label>'.$row->batch_no.'</div>
												<div class="text-small text-secondary "> <label class="text-bold">Pcs/Box : </label> '.$row->size.'</div>
												<div class="text-small text-secondary "> <label class="text-bold">Remark: </label> '.$row->remark.'</div>
											</div>
											<div class="text-end">
												<div class="card-button dropdown">
													<button type="button" class="btn btn-link btn-icon" data-bs-toggle="dropdown" aria-expanded="false">
														<ion-icon name="ellipsis-horizontal" role="img" class="md hydrated" aria-label="ellipsis horizontal"></ion-icon>
													</button>
													<div class="dropdown-menu dropdown-menu-end" style="">
														'.$deleteButton.'
													</div>
												</div>
                                                <div><h5>'.floatval($row->qty).'</h5></div>
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

