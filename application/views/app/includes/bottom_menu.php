<div class="appBottomMenu">
	

	<a href="<?=base_url('app/dashboard');?>" class="item <?=($this->data['headData']->controller == 'app/dashboard')?'active':''?>">
		<div class="col">
			<ion-icon name="pie-chart-outline"></ion-icon>
			<strong>Dashboard </strong>
		</div>
	</a>
	<a href="<?=base_url('app/approach');?>" class="item <?=(($this->data['headData']->controller == 'app/approach') && $this->data['bottomMenuName'] == 'approach')?'active':''?>">
		<div class="col">
			<ion-icon name="document-text-outline"></ion-icon>
			<strong>Approaches</strong>
		</div>
	</a>
	<a href="<?=base_url('app/stockTrans');?>" class="item <?=(($this->data['headData']->controller == 'app/salesOrder') && $this->data['bottomMenuName'] == 'salesOrder')?'active':''?>">
		<div class="col">
			<ion-icon name="document-text-outline"></ion-icon>
			<strong>FG Inward</strong>
		</div>
	</a>
</div>
