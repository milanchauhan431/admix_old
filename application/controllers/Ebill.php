<?php
class Ebill extends MY_Controller{
	public $doc_type = [
        'INV' => 'TAX Invoice',
        'BIL' => 'Bill Of Supply',
        'BOE' => 'Bill of Entry',
        'CHL' => 'Delivery Challan',
        'OTH' => 'Others'
    ];

    public $supply_type = [
		'O'=>'Outward',
		'I'=>'Inward'
	];

    public $sub_supply_type = [
		1 => 'Supply',
		2 => 'Import',
		3 => 'Export',
		4 => 'Job Work',
		5 => 'For Own Use',
		6 => 'Job Work Return',
		7 => 'Sales Return',
		8 => 'Others',
		9 => 'SKD/CKD',
		10 => 'Line Sales',
		11 => 'Recipient Not Known',
		12 => 'Exhibition or Fairs'
	];

    public $trans_mode = [
		1=>"Road",
		2=>"Rail",
		3=>"Air",
		4=>"Ship"
	];

	public $vehicle_type = [
		'R'=>'Regular',
		'O'=>'ODC'
	];

	public $transaction_type = [
		1=>'Regular',
		2=>'Bill To - Ship To',
		3=>'Bill From - Dispatch From',
		4=>'Combination of 2 and 3'
	];

    public $einvCalcelReason = [
        1 => "Duplicate", 
        2 => "Data entry mistake", 
        3 => "Order Cancelled", 
        4 => "Others"
    ];

	public $calcelReason = [
        1 => "Duplicate", 
        2 => "Data entry mistake", 
        3 => "Order Cancelled", 
        4 => "Others"
    ];
	
    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "E-Bill";
        $this->data['headData']->controller = "ebill";
	}

	/* Load From For new Eway Bill */
    public function addEwayBill(){
        $party_id = $this->input->post('party_id');
        $partyData = $this->party->getParty($party_id);
        $this->data['ref_id'] = $this->input->post('id');
        $this->data['party_id'] = $party_id;
        $this->data['transportData'] = $this->transport->getTransportList();
        $this->data['stateList'] = $this->party->getStates(['country_id'=>101]);
        $this->data['partyData'] = $partyData;
        $this->load->view("e_bill/eway_bill_form",$this->data);
    }

	/* Get Disaptch and Shipping Address on EWB Transaction Type */
    public function getEwbAddress(){
		$from_address = "";$from_pincode = "";$ship_address = "";$ship_pincode = "";
		$from_city="";$from_state="";$ship_city="";$ship_state="";
		$data = $this->input->post();
		
		$partyData = $this->party->getParty(['id'=>$data['party_id']]);
		$orgData = $this->masterModel->getCompanyInfo();
		
		$fromCity = $this->party->getCities(['state_id'=>4030]);
		$shipCity = $this->party->getCities(['state_id'=>$partyData->state_id]);
		
		if ($data['transaction_type'] == 1) {
			$fromCityOptions = '<option value="">Select City</option>';
			foreach($fromCity as $row):
				$fromCitySelected = ($orgData->company_city == $row->name)?"selected":"";
				$fromCityOptions .= '<option value="'.$row->id.'" '.$fromCitySelected.'>'.$row->name.'</option>';
			endforeach;

			$shipCityOptions = '<option value="">Select City</option>';
			foreach($shipCity as $row):
				$shipCitySelected = ($partyData->city_id == $row->id)?"selected":"";
				$shipCityOptions .= '<option value="'.$row->id.'" '.$shipCitySelected.'>'.$row->name.'</option>';
			endforeach;
			
			$from_address = $orgData->company_address;
			$from_pincode = $orgData->company_pincode;
			$ship_address = $partyData->party_address;
			$ship_pincode = $partyData->party_pincode;

			$from_city=$fromCityOptions;
			$from_state=4030;
			
			$ship_city=$shipCityOptions;
			$ship_state=$partyData->state_id;

		} elseif ($data['transaction_type'] == 2) {	
			$fromCityOptions = '<option value="">Select City</option>';
			foreach($fromCity as $row):
				$fromCitySelected = ($orgData->company_city == $row->name)?"selected":"";
				$fromCityOptions .= '<option value="'.$row->id.'" '.$fromCitySelected.'>'.$row->name.'</option>';
			endforeach;
	
			$from_address = $orgData->company_address;
			$from_pincode = $orgData->company_pincode;
			$ship_address = "";
			$ship_pincode = "";

			$from_city=$fromCityOptions;
			$from_state=4030;
			
			$ship_city="";
			$ship_state="";
		} elseif ($data['transaction_type'] == 3) {	

			$from_address = "";
			$from_pincode = "";
			$ship_address = $partyData->party_address;
			$ship_pincode = $partyData->party_pincode;

			$shipCityOptions = '<option value="">Select City</option>';
			foreach($shipCity as $row):
				$shipCitySelected = ($partyData->city_id == $row->id)?"selected":"";
				$shipCityOptions .= '<option value="'.$row->id.'" '.$shipCitySelected.'>'.$row->name.'</option>';
			endforeach;

			$from_city="";
			$from_state="";
			$ship_city=$shipCityOptions;
			$ship_state=$partyData->state_id;
		} elseif ($data['transaction_type'] == 4) {
			$from_address = "";
			$from_pincode = "";
			$ship_address = "";
			$ship_pincode = "";

			$from_city="";
			$from_state="";
			$ship_city="";
			$ship_state="";
		}

		$this->printJson(["status" => 1, "from_address" => $from_address, "from_pincode" => $from_pincode, "ship_address" => $ship_address, "ship_pincode" => $ship_pincode,"from_city"=>$from_city,"from_state"=>$from_state,"ship_city"=>$ship_city,"ship_state"=>$ship_state]);
	}  
	
	/* public function vehicleSearch(){
		$this->printJson($this->ebill->vehicleSearch());
	} */ 

	/* Generate New Eway Bill */
	public function generateEwb(){
		$data = $this->input->post();  
		$errorMessage = array();

        if(empty($data['doc_type']))
            $errorMessage['doc_type'] = "Document Type is required.";
        if(empty($data['supply_type']))
            $errorMessage['supply_type'] = "Supply Type is required.";
        if(empty($data['sub_supply_type']))
            $errorMessage['sub_supply_type'] = "Sub Supply Type is required.";
        if(empty($data['trans_mode']))
            $errorMessage['trans_mode'] = "Transport Mode is required.";
        if(empty($data['trans_distance']))
            $errorMessage['trans_distance'] = "Trans. Distance is required.";
        if(empty($data['vehicle_no']))
            $errorMessage['vehicle_no'] = "Vehicle no. is required.";
		if(empty($data['from_address']))
            $errorMessage['from_address'] = "Dispatch Address is required.";
		if(empty($data['ship_address']))
            $errorMessage['ship_address'] = "Shipping Address is required.";
        if(!isset($data['ref_id']))
            $errorMessage['ref_id'] = "Please select recoreds.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else: 
			$this->printJson($this->ebill->generateEwayBill($data));
		endif;
	}

	/* Sync E-Way Bill on Document No. */
	public function syncEwayBill(){
		$data = $this->input->post();
		$errorMessage = array();

		if(empty($data['ref_id']))
			$errorMessage['general_error'] = "Somthing is wrong.";

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$this->printJson($this->ebill->syncEwayBill($data));
		endif;
	}

	/* Cancel Eway Bill From */
	public function loadCancelEwayBillForm(){
		$id = $this->input->post('id');
		$invoiceData = $this->salesInvoice->getSalesInvoice(['id'=>$id,'itemList'=>0]);
		$this->data['ref_id'] = $id;
		$this->data['ewbNo'] = $invoiceData->eway_bill_no;
		$this->data['reasonList'] = $this->calcelReason;
		$this->load->view('e_bill/eway_bill_cancel_form',$this->data);
	}

	/* Cancel Eway Bill */
	public function cancelEwayBill(){
		$data = $this->input->post();
		$errorMessage = array();

		if(empty($data['ewbNo']))
			$errorMessage['ewbNo'] = "Eway Bill No. is required.";
		if(empty($data['cancelRsnCode']))
			$errorMessage['cancelRsnCode'] = "Cancel Reason is required.";
		if(empty($data['cancelRmrk']))
			$errorMessage['cancelRmrk'] = "Cancel Remark is required.";

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$this->printJson($this->ebill->cancelEwayBill($data));
		endif;
	}

	/* Print of Eway Bill */
	public function ewb_pdf($ewb_no=""){
		/* if(!empty($ewb_no)):
			$comapnyInfo = $this->masterModel->getCompanyInfo();
			$postData['Gstin'] = $comapnyInfo->company_gst_no;
			$postData['ewayBillNo'] = $ewb_no;

			$curlEwaybill = curl_init();
			curl_setopt_array($curlEwaybill, array(
				CURLOPT_URL => "https://ebill.nativebittechnologies.com/generate/ewayBillPdf",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_SSL_VERIFYHOST => FALSE,
				CURLOPT_SSL_VERIFYPEER => FALSE,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
				CURLOPT_POSTFIELDS => json_encode($postData)
			));

			$response = curl_exec($curlEwaybill);
			$error = curl_error($curlEwaybill);
			curl_close($curlEwaybill);
			
			if($error):
				$this->data['heading'] = "Eway Bill PDF Error.";
				$this->data['message'] = 'Somthing is wrong1. cURL Error #:'. $error;
				$this->load->view('page-404',$this->data);
			else:
				$response = json_decode($response);
				if(isset($response->status) && $response->status == 0):	
					$this->data['heading'] = "Eway Bill PDF Error.";
					$this->data['message'] = 'Somthing is wrong2. E-way Bill Error #: '. $response->error_message;
					$this->load->view('page-404',$this->data);
				else:
					return redirect($response->data->pdf_path);
				endif;
			endif;
		else:
			echo "<script>window.close();</script>";
		endif; */   

		$this->trashFiles();

		$ewbData = $this->ebill->getEmasterData($ewb_no,"'EWB','SYNCEWB'");
		//print_r($ewbData);exit;
		if(!empty($ewbData)):
			$qrText = 'EWB No.: '.$ewbData->eway_bill_no.', From:'.$ewbData->json_data->fromGstin.', Valid Untill: '.date("d/m/Y h:i:s A",strtotime($ewbData->valid_up_to));
			$ewbQrCode = $this->getQRCode($qrText,'assets/uploads/qr_code/',$ewbData->eway_bill_no);

			$pageData['doc_type'] = $this->doc_type;
			$pageData['supply_type'] = $this->supply_type;
			$pageData['sub_supply_type'] = $this->sub_supply_type;
			$pageData['trans_mode'] = $this->trans_mode;
			$pageData['vehicle_type'] = $this->vehicle_type;
			$pageData['transaction_type'] = $this->transaction_type;

			$pageData['qrCode'] = $ewbQrCode;
			$pageData['ewbData'] = $ewbData;
			$pdfData = $this->load->view('e_bill/eway_bill_pdf',$pageData,true);

			$filePath = realpath(APPPATH . '../assets/uploads/eway_bill/');
			$fileName = time().'_'.$ewbData->eway_bill_no.'.pdf';
			$pdfFileName = $filePath.'/'.$fileName;
			$stylesheet = file_get_contents(base_url('assets/css/epdf_style.css'));

			$mpdf = new \Mpdf\Mpdf();            
			$mpdf->WriteHTML($stylesheet, 1);
			$mpdf->SetDisplayMode('fullpage');
			/* if($ewbData->cancel_reason > 0):
				$mpdf->SetWatermarkText("#CANCELED", 0.2);
				$mpdf->showWatermarkText = true;
			endif; */
			$mpdf->SetProtection(array('print'));
			$mpdf->AddPage('P','','','','',5,5,5,5,5,5);
			$mpdf->WriteHTML($pdfData);
			$mpdf->Output($pdfFileName,'I');
		else:
			echo "<script>window.close();</script>";
		endif;
	
	}

	public function trashFiles(){
        /** define the directory **/
        $dirs = [
            realpath(APPPATH . '../assets/uploads/qr_code/'),
            /* realpath(APPPATH . '../assets/uploads/eway_bill/'),
            realpath(APPPATH . '../assets/uploads/eway_bill_detail/'),
            realpath(APPPATH . '../assets/uploads/e_inv/') */
        ];

        foreach($dirs as $dir):
            $files = array();
            $files = scandir($dir);
            unset($files[0],$files[1]);

            /*** cycle through all files in the directory ***/
            foreach($files as $file):
                /*** if file is 24 hours (86400 seconds) old then delete it ***/
                if(time() - filectime($dir.'/'.$file) > 86400):
                    unlink($dir.'/'.$file);
                    //print_r(filectime($dir.'/'.$file)); print_r("<hr>");
                endif;
            endforeach;
        endforeach;

        return true;
    }

	/* Detail Print of Eway Bill */
	public function ewb_detail_pdf($ewb_no=""){
		if(!empty($ewb_no)):
			$comapnyInfo = $this->masterModel->getCompanyInfo();
			$postData['Gstin'] = $comapnyInfo->company_gst_no;
			$postData['ewayBillNo'] = $ewb_no;

			$curlEwaybill = curl_init();
			curl_setopt_array($curlEwaybill, array(
				CURLOPT_URL => "https://ebill.nativebittechnologies.com/generate/ewayBillDetailPdf",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_SSL_VERIFYHOST => FALSE,
				CURLOPT_SSL_VERIFYPEER => FALSE,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
				CURLOPT_POSTFIELDS => json_encode($postData)
			));

			$response = curl_exec($curlEwaybill);
			$error = curl_error($curlEwaybill);
			curl_close($curlEwaybill);
			
			if($error):
				$this->data['heading'] = "Eway Bill PDF Error.";
				$this->data['message'] = 'Somthing is wrong1. cURL Error #:'. $error;
				$this->load->view('page-404',$this->data);
			else:
				$response = json_decode($response);
				if(isset($response->status) && $response->status == 0):	
					$this->data['heading'] = "Eway Bill PDF Error.";
					$this->data['message'] = 'Somthing is wrong2. E-way Bill Error #: '. $response->error_message;
					$this->load->view('page-404',$this->data);
				else:
					return redirect($response->data->pdf_path);
				endif;
			endif;
		else:
			echo "<script>window.close();</script>";
		endif;
	}

	/* Load From For new E-Invoice */
	public function addEinvoice(){
		$party_id = $this->input->post('party_id');
        $this->data['ref_id'] = $this->input->post('id');

        $partyData = $this->party->getParty($party_id);
        $this->data['partyData'] = $partyData;
		$this->data['companyInfo'] = $this->masterModel->getCompanyInfo();
        $this->data['transportData'] = $this->transport->getTransports();
        $this->data['countryList'] = $this->party->getCountries();
        $this->data['stateList'] = $this->party->getStates(101);
        $this->data['cityList'] = $this->party->getCities(4030);
		$this->data['billingState'] = $this->party->getStates($partyData->country_id,$partyData->state_id);
		$this->data['billingCity'] = $this->party->getCities($partyData->state_id,$partyData->city_id);
		$this->load->view('e_bill/einvoice_form',$this->data);
	}

	/* Generate New E-Invoice */
	public function generateEinvoice(){
		$data = $this->input->post();
		$errorMessage = array();

		if(empty($data['ref_id']))
			$errorMessage['general_error'] = "Somthing is wrong.";
		if(!empty($data['ewb_status']) && empty($data['trans_distance']))
			$errorMessage['trans_distance'] = "Trans. Distance is required.";

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$this->printJson($this->ebill->generateEinvoice($data));
		endif;
	}

	/* SYNC E-Invoice From GOV. Portal */
	public function syncEinvoice(){
		$data = $this->input->post();
		$errorMessage = array();

		if(empty($data['ref_id']))
			$errorMessage['general_error'] = "Somthing is wrong.";

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$this->printJson($this->ebill->syncEinvoice($data));
		endif;
	}

	/* Load Cencel E-Invoice From */
	public function loadCancelInvForm(){
		$id = $this->input->post('id');
		$invoiceData = $this->salesInvoice->getInvoice($id);
		$this->data['ref_id'] = $id;
		$this->data['akc_no'] = $invoiceData->e_inv_no;
		$this->data['irn'] = $invoiceData->e_inv_irn;
		$this->data['reasonList'] = $this->calcelReason;
		$this->load->view('e_bill/einvoice_cancel_form',$this->data);
	}

	/* Cancel E-Invoice on irn */
	public function cancelEinvoice(){
		$data = $this->input->post();//print_r($data);exit;
		$errorMessage = array();

		if(empty($data['CnlRsn']))
			$errorMessage['CnlRsn'] = "Cancel Reason is required.";
		if(empty($data['CnlRem']))
			$errorMessage['CnlRem'] = "Cancel Remark is required.";

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$this->printJson($this->ebill->cancelEinv($data));
		endif;
	}

	/* Print of E-Invoice */
	public function einv_pdf($ackNo=""){
		if(!empty($ackNo)):
			$comapnyInfo = $this->masterModel->getCompanyInfo();
			$postData['Gstin'] = $comapnyInfo->company_gst_no;
			$postData['ackNo'] = $ackNo;

			$curlEwaybill = curl_init();
			curl_setopt_array($curlEwaybill, array(
				CURLOPT_URL => "https://ebill.nativebittechnologies.com/generate/einvPdf",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_SSL_VERIFYHOST => FALSE,
				CURLOPT_SSL_VERIFYPEER => FALSE,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
				CURLOPT_POSTFIELDS => json_encode($postData)
			));

			$response = curl_exec($curlEwaybill);
			$error = curl_error($curlEwaybill);
			curl_close($curlEwaybill);
			
			if($error):
				$this->data['heading'] = "E-Invoice PDF Error.";
				$this->data['message'] = 'Somthing is wrong1. cURL Error #:'. $error;
				$this->load->view('page-404',$this->data);
			else:
				$response = json_decode($response);
				if(isset($response->status) && $response->status == 0):	
					$this->data['heading'] = "E-Invoice PDF Error.";
					$this->data['message'] = 'Somthing is wrong2. E-way Bill Error #: '. $response->error_message;
					$this->load->view('page-404',$this->data);
				else:
					return redirect($response->data->pdf_path);
				endif;
			endif;
		else:
			echo "<script>window.close();</script>";
		endif;
	}

	public function downloadEinvJson(){
		$data = $this->input->post();
		$errorMessage = array();

		if(empty($data['ref_id']))
			$errorMessage['general_error'] = "Somthing is wrong.";
		if(!empty($data['ewb_status']) && empty($data['trans_distance']))
			$errorMessage['trans_distance'] = "Trans. Distance is required.";

        if(empty($data['billing_country']))
            $errorMessage['billing_country'] = "Billing Country is required.";
        if(empty($data['billing_state']))
            $errorMessage['billing_state'] = "Billing State is required.";
        if(empty($data['billing_city']))
            $errorMessage['billing_city'] = "Billing City is required.";
        if(empty($data['billing_pincode']))
            $errorMessage['billing_pincode'] = "Billing Pincode is required.";
        if(empty($data['billing_address']))
            $errorMessage['billing_address'] = "Billing Address is required.";

        if(empty($data['ship_country']))
            $errorMessage['ship_country'] = "Shipping Country is required.";
        if(empty($data['ship_state']))
            $errorMessage['ship_state'] = "Shipping State is required.";
        if(empty($data['ship_city']))
            $errorMessage['ship_city'] = "Shipping City is required.";
        if(empty($data['ship_pincode']))
            $errorMessage['ship_pincode'] = "Shipping Pincode is required.";
        if(empty($data['ship_address']))
            $errorMessage['ship_address'] = "Shipping Address is required.";

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$result = $this->ebill->generateEinvJson($data);
			$this->printJson($result);
		endif;
	}

}
?>