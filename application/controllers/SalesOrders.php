<?php
class SalesOrders extends MY_Controller{
    private $indexPage = "sales_order/index";
    private $form = "sales_order/form"; 
    private $partyOrder = "sales_order/party_order";   
    private $orderForm = "sales_order/party_order_form";   

    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "Sales Order";
		$this->data['headData']->controller = "salesOrders";        
        $this->data['headData']->pageUrl = "salesOrders";
        $this->data['entryData'] = $this->transMainModel->getEntryType(['controller'=>'salesOrders']);
	}

    public function index(){
        $this->data['tableHeader'] = getSalesDtHeader("salesOrders");
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status = 0){
        $data = $this->input->post();$data['status'] = $status;
        $data['entry_type'] = $this->data['entryData']->id;
        $result = $this->salesOrder->getDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getSalesOrderData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addOrder(){
        $this->data['entry_type'] = $this->data['entryData']->id;
        $this->data['trans_prefix'] = $this->data['entryData']->trans_prefix;
        $this->data['trans_no'] = $this->data['entryData']->trans_no;
        $this->data['trans_number'] = $this->data['trans_prefix'].$this->data['trans_no'];
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>1]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->data['brandList'] = $this->brandMaster->getBrandList();
        $this->data['unitList'] = $this->item->itemUnits();
        $this->data['hsnList'] = $this->hsnModel->getHSNList();
		$this->data['taxList'] = $this->taxMaster->getActiveTaxList(2);
        $this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(2);
        $this->data['termsList'] = $this->terms->getTermsList(['type'=>'Sales']);
        $this->load->view($this->form,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['party_id']))
            $errorMessage['party_id'] = "Party Name is required.";
        /* if(empty($data['order_type']))
            $errorMessage['order_type'] = "Production Type is required."; */
        if(empty($data['itemData']))
            $errorMessage['itemData'] = "Item Details is required.";
        
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            /* $this->load->library('upload');
            $filePath = realpath(APPPATH . '../assets/uploads/sales_order');

            $itemData = array();
            foreach($data['itemData'] as $key => $row):
                if(isset($_FILES['itemData']['name'][$key]['attachment']) && !empty($_FILES['itemData']['name'][$key]['attachment'])):
                    $_FILES['userfile']['name']     = $_FILES['itemData']['name'][$key]['attachment'];
                    $_FILES['userfile']['type']     = $_FILES['itemData']['type'][$key]['attachment'];
                    $_FILES['userfile']['tmp_name'] = $_FILES['itemData']['tmp_name'][$key]['attachment'];
                    $_FILES['userfile']['error']    = $_FILES['itemData']['error'][$key]['attachment'];
                    $_FILES['userfile']['size']     = $_FILES['itemData']['size'][$key]['attachment'];

                    $config = ['file_name' => time()."_soi_".$_FILES['userfile']['name'],'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$filePath];

                    $this->upload->initialize($config);
                    if(!$this->upload->do_upload()):
                        $errorMessage['ba_file_'.$key] = $this->upload->display_errors();
                        $this->printJson(["status"=>0,"message"=>$errorMessage]);
                    else:
                        $uploadData = $this->upload->data();
                        $row['attachment'] = $uploadData['file_name'];
                    endif;
                endif;

                if(!empty($row['id']) && $row['attachment_status'] == 2):
                    $soItem = $this->salesOrder->getSalesOrderItem(['id'=>$row['id']]);
                    if(!empty($soItem->attachment)):
                        if(file_exists($filePath."/".$soItem->attachment)):
                            unlink($filePath."/".$soItem->attachment);
                        endif;
                    endif;

                    $row['attachment'] = "";
                endif;

                unset($row['attachment_status']);

                $itemData[] = $row;
            endforeach;
            $data['itemData'] = $itemData; */
            
            $data['vou_name_l'] = $this->data['entryData']->vou_name_long;
            $data['vou_name_s'] = $this->data['entryData']->vou_name_short;
            $this->printJson($this->salesOrder->save($data));
        endif;
    }

    public function edit($id,$accept = 0){
        $this->data['is_approve'] = (!empty($accept))?$this->loginId:0;
        $this->data['approve_date'] = (!empty($accept))?date("Y-m-d"):"";
        $this->data['dataRow'] = $dataRow = $this->salesOrder->getSalesOrder(['id'=>$id,'itemList'=>1]);
        $this->data['gstinList'] = $this->party->getPartyGSTDetail(['party_id' => $dataRow->party_id]);
        $this->data['partyList'] = $this->party->getPartyList(['party_category' => 1]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->data['brandList'] = $this->brandMaster->getBrandList();
        $this->data['unitList'] = $this->item->itemUnits();
        $this->data['hsnList'] = $this->hsnModel->getHSNList();
		$this->data['taxList'] = $this->taxMaster->getActiveTaxList(2);
        $this->data['expenseList'] = $this->expenseMaster->getActiveExpenseList(2);
        $this->data['termsList'] = $this->terms->getTermsList(['type'=>'Sales']);
        $this->load->view($this->form,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->salesOrder->delete($id));
        endif;
    }

    public function printOrder($id,$pdf_type=''){
        $this->data['dataRow'] = $dataRow = $this->salesOrder->getSalesOrder(['id'=>$id,'itemList'=>1]);
        $this->data['partyData'] = $this->party->getParty(['id'=>$dataRow->party_id]);
        $this->data['companyData'] = $companyData = $this->masterModel->getCompanyInfo();
        
        $logo = base_url('assets/images/logo.png');
        $this->data['letter_head'] =  base_url('assets/images/letterhead-top.png');
        
        $pdfData = $this->load->view('sales_order/print', $this->data, true);        
        
        $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
            <tr>
                <td style="width:25%;">SO. No. & Date : '.$dataRow->trans_number . ' [' . formatDate($dataRow->trans_date) . ']</td>
                <td style="width:25%;"></td>
                <td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
            </tr>
        </table>';
        
		$mpdf = new \Mpdf\Mpdf();
		$filePath = realpath(APPPATH . '../assets/uploads/sales_quotation/');
        $pdfFileName = $filePath.'/' . str_replace(["/","-"],"_",$dataRow->trans_number) . '.pdf';
        
        /* $stylesheet = file_get_contents(base_url('assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css'));
        $stylesheet = file_get_contents(base_url('assets/css/style.css?v=' . time())); */
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetWatermarkImage($logo, 0.03, array(120, 120));
        $mpdf->showWatermarkImage = true;
        $mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',10,5,5,15,5,5,'','','','','','','','','','A4-P');
        $mpdf->WriteHTML($pdfData);
		
		ob_clean();
		$mpdf->Output($pdfFileName, 'I');
		
    }

    public function getPartyOrders(){
        $data = $this->input->post();
        $this->data['orderItems'] = $this->salesOrder->getPendingOrderItems($data);
        $this->load->view('sales_invoice/create_invoice',$this->data);
    }

    /* Party Order Start */

    public function partyOrders(){
        $this->data['headData']->pageTitle = "Orders";
        $this->data['tableHeader'] = getSalesDtHeader("partyOrders");
        $this->load->view($this->partyOrder,$this->data);
    }

    public function getPartyOrderDTRows($status = 0){
        $data = $this->input->post();$data['status'] = $status;
        $data['entry_type'] = $this->data['entryData']->id;
        $result = $this->salesOrder->getPartyOrderDTRows($data);
        $sendData = array();$i=($data['start']+1);
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $sendData[] = getPartyOrderData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addPartyOrder(){
        $this->data['partyList'] = $this->party->getPartyList(['party_category'=>1]);
        $this->data['itemList'] = $this->item->getItemList(['item_type'=>1]);
        $this->data['brandList'] = $this->brandMaster->getBrandList();
        $this->load->view($this->orderForm,$this->data);
    }

    public function savePartyOrder(){
        $data = $this->input->post();
        $errorMessage = array();

        if(array_sum(array_column($data['itemData'],'qty')) <= 0):
            $this->printJson(['status'=>0,'message'=>['item_error'=>'Please enter at least one item qty.']]);
        endif;

        $partyData = $this->party->getParty(['id'=>$this->partyId]);
        $gstType = ($partyData->state_code != 24)?2:1;

        $total_amount = $taxable_amount = $cgst_amount = $sgst_amount = $igst_amount = $net_amount = 0;

        $itemData = array();
        foreach($data['itemData'] as $row):
            if($row['qty'] > 0):
                $itemDetail = $this->item->getItem(['id'=>$row['item_id']]);
                $brandDetail = $this->brandMaster->getBrand(['id'=>$row['brand_id']]);

                $gstPer = $igstPer = $cgstPer = $sgstPer = 0;
                $amount = $taxableAmt = $netAmt = $discAmt = 0;
                $gstAmt = $igstAmt = $cgstAmt = $sgstAmt = 0;                

                $gstPer = $igstPer = $itemDetail->gst_per;
                $cgstPer = $sgstPer = round(($itemDetail->gst_per/2),2);                

                $amount = $taxableAmt = $row['qty'] * $itemDetail->price;
                if(!empty($itemDetail->defualt_disc) && !empty($amount)):
                    $discAmt = round((($amount * $itemDetail->defualt_disc)/100),2);
                    $taxableAmt = $amount - $discAmt;
                endif;

                if(!empty($taxableAmt)):
                    $gstAmt = $igstAmt = round((($gstPer * $taxableAmt)/100),2);
                    $cgstAmt = $sgstAmt = round(($gstAmt/2),2);
                endif;

                $netAmt = $taxableAmt + $gstAmt;

                $total_amount += $amount;
                $taxable_amount += $taxableAmt;
                if($gstType == 1):
                    $cgst_amount += $cgstAmt;
                    $sgst_amount += $sgstAmt;
                else:
                    $igst_amount += $igstAmt;
                endif;
                $net_amount += $netAmt;

                $itemData[] = [
                    'id' => '',
                    'item_id' => $itemDetail->id,
                    'item_name' => $itemDetail->item_name,
                    'item_code' => $itemDetail->item_code,
                    'item_type' => $itemDetail->item_type,
                    'brand_id' => $brandDetail->id,
                    'brand_name' => $brandDetail->brand_name,
                    'hsn_code' => $itemDetail->hsn_code,
                    'qty' => $row['qty'],
                    'unit_id' => $itemDetail->unit_id,
                    'unit_name' => $itemDetail->unit_name,
                    'price' => $itemDetail->price,
                    'disc_per' => $itemDetail->defualt_disc,
                    'disc_amount' => $discAmt,
                    'amount' => $amount,
                    'taxable_amount' => $taxableAmt,
                    'net_amount' => $netAmt,
                    'amount' => $amount,
                    'cgst_per' => $cgstPer,
                    'cgst_amount' => $cgstAmt,
                    'sgst_per' => $sgstPer,
                    'sgst_amount' => $sgstAmt,
                    'igst_per' => $igstPer,
                    'igst_amount' => $igstAmt,
                    'gst_per' => $gstPer,
                    'gst_amount' => $gstAmt,
                    'item_remark' => $row['item_remark'],
                ];
            endif;
        endforeach;

        $trans_prefix = $this->data['entryData']->trans_prefix;
        $trans_no = $this->data['entryData']->trans_no;
        $masterData = [
            'id' => '',
            'entry_type' => $this->data['entryData']->id,
            'trans_prefix' => $trans_prefix,
            'trans_no' => $trans_no,
            'trans_number' => $trans_prefix.$trans_no,
            'trans_date' => date("Y-m-d"),
            'sales_executive' => $partyData->id,
            'party_id' => $partyData->id,
            'party_name' => $partyData->party_name,
            'gstin' => $partyData->gstin,
            'gst_type' => $gstType,
            'party_state_code' => $partyData->state_code,
            'apply_round' => 1,
            'ledger_eff' => 0,
            'masterDetails' => [
                't_col_1' => $partyData->contact_person,
                't_col_2' => $partyData->party_mobile,
                't_col_3' => $partyData->party_address,
                't_col_4' => $partyData->party_pincode,
            ],
            'itemData' => $itemData,
            'total_amount' => $total_amount,
            'taxable_amount' => $taxable_amount,
            'cgst_amount' => $cgst_amount,
            'sgst_amount' => $sgst_amount,
            'igst_amount' => $igst_amount,
            'net_amount' => $net_amount
        ];

        $masterData['vou_name_l'] = $this->data['entryData']->vou_name_long;
        $masterData['vou_name_s'] = $this->data['entryData']->vou_name_short;
        $this->printJson($this->salesOrder->save($masterData));
    }
    /* Party Order End */
}
?>