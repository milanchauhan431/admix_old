<?php
class StockTrans extends MY_Controller{
    private $indexPage = "stock_trans/index";
    private $form = "stock_trans/form";    

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "FG Stock Inward";
		$this->data['headData']->controller = "stockTrans";        
        $this->data['headData']->pageUrl = "stockTrans";
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'stockTrans']);
	}

    public function index(){
        $this->data['tableHeader'] = getStoreDtHeader("stockTrans");
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status = 0){
        $data = $this->input->post();
        $data['entry_type'] = $this->data['entryData']->id;
        $result = $this->itemStock->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getStockTransData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addStock(){
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>[1]]);
        $this->data['brandList'] = $this->brandMaster->getBrandList();
        $this->data['sizeList'] = $this->sizeMaster->getSizeList();
        $this->load->view($this->form, $this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		

        if(empty($data['size_id']))
			$errorMessage['size_id_error'] = "Size is required.";
        if(empty($data['color']))
			$errorMessage['color_error'] = "Color is required.";
        if(empty($data['capacity']))
			$errorMessage['capacity_error'] = "Capacity is required.";
        if(empty(floatVal($data['qty'])))
			$errorMessage['qty'] = "Qty is required.";

        
        if(!empty($data['size_id']) && !empty($data['color']) && !empty($data['capacity'])):
            $itemDetail = $this->item->getItem(['size_id'=>$data['size_id'],'color'=>$data['color'],'capacity'=>$data['capacity']]);

            if(empty($itemDetail)):
                $errorMessage['item_error'] = "Item not found.";
            endif;

            unset($data['size_id'],$data['color'],$data['capacity']);

            $data['item_id'] = $itemDetail->id;
            $data['size'] = $itemDetail->packing_standard;
            if(!empty(floatVal($data['qty'])) && !empty($data['size'])):
                if(is_int(($data['qty'] / $data['size'])) == false):
                    $errorMessage['qty'] = "Invalid qty against packing standard.";
                endif;
            endif;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['entry_type'] = $this->data['entryData']->id;
            $data['location_id'] = $this->RTD_STORE->id;
            $this->printJson($this->itemStock->save($data));
        endif;
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->itemStock->delete($id));
        endif;
    }
}
?>