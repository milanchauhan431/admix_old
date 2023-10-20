<?php
class StockTrans extends MY_Controller
{
	private $indexPage = "app/fginward_index";
	private $formPage = "app/fginward_form"; 

	public function __construct()
	{
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Fg Stock Inward";
		$this->data['headData']->controller = "app/stockTrans";
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'stockTrans']);
	}

	public function index()
	{
		$this->data['bottomMenuName'] ='stockTrans';
        $this->data['stockData'] =  $this->itemStock->getStockDataForApp();
		$this->load->view($this->indexPage, $this->data);
	}

	public function addStock(){
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>[1]]);
        $this->data['brandList'] = $this->brandMaster->getBrandList();
        $this->data['sizeList'] = $this->sizeMaster->getSizeList();
        $this->load->view($this->formPage, $this->data);
    }
}
?>