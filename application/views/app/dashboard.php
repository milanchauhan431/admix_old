<?php $this->load->view('app/includes/header'); ?>
<?php $this->load->view('app/includes/topbar'); ?>

<!-- Start Main Content -->
<div id="appCapsule" class="bg-light">
	<!-- * Exchange Action Sheet -->
	<div class="section pt-1 mb-2 bg-light">
		<div class="wallet-card">
			<div class="wallet-footer">
			
				<div class="item">
					<a href="<?=base_url('app/approach');?>">
						<div class="icon-wrapper">
							<i class=" far fa-clipboard"></i>
						</div>
						<strong>Approaches</strong>
					</a>
				</div>
				<div class="item">
					<a href="<?=base_url('app/stockTrans');?>">
						<div class="icon-wrapper bg-success">
							<i class="fas fa-database"></i>				
						</div>
						<strong>FG Inward</strong>
					</a>
				</div>
			</div>
		</div>
	</div>
	
</div>

<!-- End Main Content -->


<?php $this->load->view('app/includes/bottom_menu'); ?>
<?php $this->load->view('app/includes/sidebar'); ?>
<?php $this->load->view('app/includes/add_to_home'); ?>
<?php $this->load->view('app/includes/footer'); ?>

