<?php
class EbillModel extends MasterModel{
    private $eBillLog = "e_bill_log";
    private $ewayBillMaster = "eway_bill_master";

    
    /* Generate Eway Bill JSON DATA */
    public function ewbJsonSingle($ewbData){
		$ref_id = $ewbData['ref_id'];
        $postData=array();$billData=array();$itemList=array();

        $invData = $this->salesInvoice->getSalesInvoice(['id'=>$ref_id,'itemList'=>1]);

        $orgData = $this->getCompanyInfo();
        $cityDataFrom = $this->party->getCity(['id'=>$ewbData['from_city']]);
        $cityDataTo = $this->party->getCity(['id'=>$ewbData['ship_city']]);

        $postData['Gstin'] = $orgData->company_gst_no;
        $postData['companyInfo'] = [
            'name' => $orgData->company_name,
            'email' => $orgData->company_email,
            'phone_no' => $orgData->company_contact,
            'contact_no' => $orgData->company_contact,
            'country_name' => (!empty($ewbData['from_city']))?$cityDataFrom->country_name:$orgData->company_country,
            'state_name' => (!empty($ewbData['from_city']))?$cityDataFrom->state_name:$orgData->company_state,
            'city_name' => (!empty($ewbData['from_city']))?$cityDataFrom->name:$orgData->company_city,
            'address' => (!empty($ewbData['from_address']))?$ewbData['from_address']:$orgData->company_address,
            'pincode' => (!empty($ewbData['from_pincode']))?$ewbData['from_pincode']:$orgData->company_pincode,
            'gst_no' => $orgData->company_gst_no,
            'pan_no' => $orgData->company_pan_no,
            'state_code' => $orgData->company_state_code
        ];

        
        $partyData = $this->party->getParty($invData->party_id);    
        $postData['partyInfo'] = [
            'name' => $partyData->party_name,
            'gst_no' => (!empty($partyData->gstin))?$partyData->gstin:"URP",
            'pan_no' => $partyData->pan_no,            
            'email' => $partyData->party_email,
            'contact_email' => $partyData->contact_email,
            'phone_no' => $partyData->party_phone,
            'contact_no' => $partyData->party_mobile,
            'billing_address' => str_replace('"',"",((!empty($ewbData['ship_address']))?$ewbData['ship_address']:$partyData->party_address)),
            'billing_pincode' => (!empty($ewbData['ship_pincode']))?$ewbData['ship_pincode']:$partyData->party_pincode,
            'billing_country_name' => (!empty($ewbData['ship_city']))?$cityDataTo->country_name:$partyData->country_name,
            'billing_state_name' => (!empty($ewbData['ship_city']))?$cityDataTo->state_name:$partyData->state_name,
            'billing_city_name' => (!empty($ewbData['ship_city']))?$cityDataTo->name:$partyData->city_name,
            'billing_state_code' => $cityDataTo->state_code,
            'ship_address' => str_replace('"',"",((!empty($ewbData['ship_address']))?$ewbData['ship_address']:$partyData->party_address)),
            'ship_pincode' => (!empty($ewbData['ship_pincode']))?$ewbData['ship_pincode']:$partyData->party_pincode,
            'ship_country_name' => (!empty($ewbData['ship_city']))?$cityDataTo->country_name:$partyData->country_name,
            'ship_state_name' => (!empty($ewbData['ship_city']))?$cityDataTo->state_name:$partyData->state_name,
            'ship_city_name' => (!empty($ewbData['ship_city']))?$cityDataTo->name:$partyData->city_name,
            'ship_state_code' => $cityDataTo->state_code,
        ];

        $mainHsnCode = '';
        foreach($invData->itemList as $row):            
            $itemList[]= [
                "productName"=> $row->item_name,
                "productDesc"=> "", 
                "hsnCode"=> (!empty($row->hsn_code))?intVal($row->hsn_code):"", 
                "quantity"=> floatVal($row->qty),
                "qtyUnit"=> $row->unit_name, 
                "taxableAmount"=> floatVal($row->amount), 
                "sgstRate"=> floatVal((($invData->gst_type == 1)?$row->sgst_per:0)),
                "cgstRate"=> floatVal((($invData->gst_type == 1)?$row->cgst_per:0)),
                "igstRate"=> floatVal((($invData->gst_type == 2)?$row->igst_per:0)), 
                "cessRate"=> 0, 
                "cessNonAdvol"=> 0
            ];

            $mainHsnCode = (!empty($row->hsn_code))?intVal($row->hsn_code):"";
        endforeach;

        $ewbData['from_address'] = str_replace(["\r\n", "\r", "\n",'"'], " ", $ewbData['from_address']);
        $orgAdd1 = substr($ewbData['from_address'],0,100);
        $orgAdd2 = (strlen($ewbData['from_address']) > 100)?substr($ewbData['from_address'],100,200):"";

        $ewbData['ship_address'] = str_replace(["\r\n", "\r", "\n",'"'], " ", $ewbData['ship_address']);
        $toAddr1 = substr($ewbData['ship_address'],0,100);
        $toAddr2 = (strlen($ewbData['ship_address']) > 100)?substr($ewbData['ship_address'],100,200):"";
                    
        $billData["supplyType"] = $ewbData['supply_type'];
        $billData["subSupplyType"] = $ewbData['sub_supply_type'];
        $billData["subSupplyDesc"] = "";
        $billData["docType"] = $ewbData['doc_type'];
        $billData["docNo"] = $invData->trans_number;
        $billData["docDate"] = date("d/m/Y",strtotime($invData->trans_date));
        $billData["fromGstin"] = $orgData->company_gst_no;
        $billData["fromTrdName"] = $orgData->company_name;
        $billData["fromAddr1"] = $orgAdd1;
        $billData["fromAddr2"] = $orgAdd2;
        $billData["fromPlace"] = $cityDataFrom->name;
        $billData["fromPincode"] = (int) $ewbData['from_pincode'];
        $billData["fromStateCode"] = (int) $orgData->company_state_code;
        $billData["actFromStateCode"] = (int) $orgData->company_state_code;
        $billData["toGstin"] = (!empty($partyData->gstin))?$partyData->gstin:"URP";
        $billData["toTrdName"] = $partyData->party_name;
        $billData["toAddr1"] = $toAddr1;
        $billData["toAddr2"] = $toAddr2;
        $billData["toPlace"] = $cityDataTo->name;
        $billData["toPincode"] = (int) $ewbData['ship_pincode']; 
        $billData["toStateCode"] = (int) $cityDataTo->state_code;
        $billData["actToStateCode"] = (int) $cityDataTo->state_code;
        $billData['transactionType'] = (int) $ewbData['transaction_type'];
        $billData['dispatchFromGSTIN'] = "";
        $billData['dispatchFromTradeName'] = "";
        $billData['shipToGSTIN'] = "";
        $billData['shipToTradeName'] = "";
        $billData["otherValue"] = floatVal(round($invData->net_amount - ($invData->taxable_amount + $invData->igst_amount),2));
        $billData["totalValue"] = floatVal($invData->taxable_amount);
        $billData["cgstValue"] = ($cityDataTo->state_code == $orgData->company_state_code)?floatVal($invData->cgst_amount):0;
        $billData["sgstValue"] = ($cityDataTo->state_code == $orgData->company_state_code)?floatVal($invData->sgst_amount):0;
        $billData["igstValue"] = ($cityDataTo->state_code != $orgData->company_state_code)?floatVal($invData->igst_amount):0;
        $billData["cessValue"] = 0;
        $billData['cessNonAdvolValue'] = 0;
        $billData["totInvValue"] = floatVal($invData->net_amount);
        $billData["transporterId"] = $ewbData['transport_id'];
        $billData["transporterName"] = $ewbData['transport_name'];
        $billData["transDocNo"] = $ewbData['transport_doc_no'];
        $billData["transMode"] = $ewbData['trans_mode']; 
        $billData["transDistance"] = $ewbData['trans_distance'];
        $billData["transDocDate"] = (!empty($ewbData['transport_doc_date']))?date("d/m/Y",strtotime($ewbData['transport_doc_date'])):"";
        $billData["vehicleNo"] = $ewbData['vehicle_no'];
        $billData["vehicleType"] = $ewbData['vehicle_type'];
        $billData['mainHsnCode'] = $mainHsnCode;
        $billData['itemList']=$itemList;
        
		$postData['ewbData'] = $billData;
        
		return $postData;
    }

    /* Generate New Eway Bill */
    public function generateEwayBill($data){
        $ref_id = $data['ref_id'];
        $postData = $this->ewbJsonSingle($data);
        //print_r($postData);exit;

        $curlEwaybill = curl_init();
        curl_setopt_array($curlEwaybill, array(
            CURLOPT_URL => "https://ebill.nativebittechnologies.com/generate/ewayBill",
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
			$ewayLog = [
                'id' => '',
                'type' => 'EWB',
                'response_status'=> "Fail",
                'post_data' => json_encode($postData),
                'response_data'=> $response,
                'created_by'=> $this->loginId,
                'created_at' => date("Y-m-d H:i:s")
            ];
            $this->store($this->eBillLog,$ewayLog);
			
            return ['status'=>2,'message'=>'Somthing is wrong1. cURL Error #:'. $error]; 
        else:
            $responseEwaybill = json_decode($response,false);	
                        
            if(isset($responseEwaybill->status) && $responseEwaybill->status == 0):				
				$ewayLog = [
                    'id' => '',
                    'type' => 'EWB',
                    'response_status'=> "Fail",
                    'post_data' => json_encode($postData),
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->eBillLog,$ewayLog);
				
                return ['status'=>2,'message'=>'Somthing is wrong2. E-way Bill Error #: '. $responseEwaybill->error_message,'data'=>$responseEwaybill->data ];
            else:						
                $ewayLog = [
                    'id' => '',
                    'type' => 'EWB',
                    'response_status'=> "Success",
                    'eway_bill_no' => $responseEwaybill->data->eway_bill_no,
                    'post_data' => json_encode($postData),
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->eBillLog,$ewayLog);

                $this->edit("trans_main",['id'=>$data['ref_id']],['ewb_status'=>1,'eway_bill_no'=>$responseEwaybill->data->eway_bill_no]);

                return ['status'=>1,'message'=>'E-way Bill Generated successfully.'];
            endif;
        endif;
    }

    /* SYNC Eway Bill Data From GOV. Portal */
    public function syncEwayBill($data){
        $ref_id = $data['ref_id'];
        $postData = $this->ewbJsonSingle($data);
        //print_r($postData);exit;

        $curlEwaybill = curl_init();
        curl_setopt_array($curlEwaybill, array(
            CURLOPT_URL => "https://ebill.nativebittechnologies.com/generate/syncEwayBill",
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
			$ewayLog = [
                'id' => '',
                'type' => "SYNCEWB",
                'response_status'=> "Fail",
                'post_data' => json_encode($postData),
                'response_data'=> $response,
                'created_by'=> $this->loginId,
                'created_at' => date("Y-m-d H:i:s")
            ];
            $this->store($this->eBillLog,$ewayLog);
			
            return ['status'=>2,'message'=>'Somthing is wrong1. cURL Error #:'. $error]; 
        else:
            $responseEwaybill = json_decode($response,false);	
                        
            if(isset($responseEwaybill->status) && $responseEwaybill->status == 0):				
				$ewayLog = [
                    'id' => '',
                    'type' => "SYNCEWB",
                    'response_status'=> "Fail",
                    'post_data' => json_encode($postData),
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->eBillLog,$ewayLog);
				
                return ['status'=>2,'message'=>'Somthing is wrong2. E-way Bill Error #: '. $responseEwaybill->error_message,'data'=>$responseEwaybill->data ];
            else:						
                $ewayLog = [
                    'id' => '',
                    'type' => "SYNCEWB",
                    'response_status'=> "Success",
                    'eway_bill_no'=>$responseEwaybill->data->eway_bill_no,
                    'post_data' => json_encode($postData),
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->eBillLog,$ewayLog);

                $calcelReason = [
                    1 => "Duplicate", 
                    2 => "Data entry mistake", 
                    3 => "Order Cancelled", 
                    4 => "Others"
                ];

                $cancel_reason = (!empty($responseEwaybill->data->cancel_reason))?$calcelReason[$responseEwaybill->data->cancel_reason]:"";
                $ewbStatus = (!empty($responseEwaybill->data->cancel_reason))?3:2;

                $this->edit("trans_main",['id'=>$data['ref_id']],['ewb_status'=>$ewbStatus,'eway_bill_no'=>$responseEwaybill->data->eway_bill_no,'close_reason'=>$cancel_reason,'close_date'=>(!empty($responseEwaybill->data->cancel_date))?$responseEwaybill->data->cancel_date:NULL]);

                return ['status'=>1,'message'=>'E-way Bill SYNC successfully.','data'=>$responseEwaybill];
            endif;
        endif;
    }

    /* Cancel Eway Bill By Eway Bill No. */
    public function cancelEwayBill($postData){
        $ref_id = $postData['ref_id'];

        $orgData = $this->getCompanyInfo();
        $postData['Gstin'] = $orgData->company_gst_no;

        $curlEwaybill = curl_init();
        curl_setopt_array($curlEwaybill, array(
            CURLOPT_URL => "https://ebill.nativebittechnologies.com/generate/cancelEwayBill",
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
			$ewayLog = [
                'id' => '',
                'type' => "CNLEWB",
                'response_status'=> "Fail",
                'post_data' => json_encode($postData),
                'response_data'=> $response,
                'created_by'=> $this->loginId,
                'created_at' => date("Y-m-d H:i:s")
            ];
            $this->store($this->eBillLog,$ewayLog);
			
            return ['status'=>2,'message'=>'Somthing is wrong1. cURL Error #:'. $error]; 
        else:
            $responseEwaybill = json_decode($response,false);	
                        
            if(isset($responseEwaybill->status) && $responseEwaybill->status == 0):				
				$ewayLog = [
                    'id' => '',
                    'type' => "CNLEWB",
                    'response_status'=> "Fail",
                    'post_data' => json_encode($postData),
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->eBillLog,$ewayLog);
				
                return ['status'=>2,'message'=>'Somthing is wrong2. E-way Bill Error #: '. $responseEwaybill->error_message,'data'=>$responseEwaybill->data ];
            else:						
                $ewayLog = [
                    'id' => '',
                    'type' => "CNLEWB",
                    'response_status'=> "Success",
                    'eway_bill_no' => $postData['ewbNo'],
                    'post_data' => json_encode($postData),
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->eBillLog,$ewayLog);

                $calcelReason = [
                    1 => "Duplicate", 
                    2 => "Data entry mistake", 
                    3 => "Order Cancelled", 
                    4 => "Others"
                ];

                $cancel_reason = (!empty($responseEwaybill->data->cancel_reason))?$calcelReason[$responseEwaybill->data->cancel_reason]:"";
                $cancel_date = (!empty($responseEwaybill->data->cancel_date))?date("Y-m-d H:i:s",strtotime($responseEwaybill->data->cancel_date)):NULL;

                $this->edit("trans_main",['id'=>$ref_id],['ewb_status'=>3,'close_reason'=>$cancel_reason,'close_date'=>$cancel_date]);

                return ['status'=>1,'message'=>'E-way Bill Cancel successfully.','data'=>$responseEwaybill];
            endif;
        endif;
    }

    /* Generate Einvoice Json */
    public function einvJson($data){
        $postData = array();
		$ref_id = $data['ref_id'];

        $orgData = $this->getCompanyInfo();

        $invData = $this->salesInvoice->getInvoice($ref_id);
                
        $partyData = $this->party->getParty($invData->party_id);

        $disCityData = $this->party->getCity($data['dispatch_city']);
        $bilCityData = $this->party->getCity($data['billing_city']);
        $shipCityData = $this->party->getCity($data['ship_city']);

        $postData['Gstin'] = $orgData->company_gst_no;
        $postData['companyInfo'] = [
            'name' => $orgData->company_name,
            'email' => $orgData->company_email,
            'phone_no' => $orgData->company_contact,
            'contact_no' => $orgData->company_contact,
            'country_name' => $orgData->company_country,
            'state_name' => $orgData->company_state,
            'city_name' => $orgData->company_city,
            'address' => $orgData->company_address,
            'pincode' => $orgData->company_pincode,
            'gst_no' => $orgData->company_gst_no,
            'pan_no' => $orgData->company_pan_no,
            'state_code' => $orgData->company_state_code
        ];

        
        $partyData = $this->party->getParty($invData->party_id);    
        $postData['partyInfo'] = [
            'name' => $partyData->party_name,
            'gst_no' => (!empty($partyData->gstin))?$partyData->gstin:"URP",
            'pan_no' => $partyData->party_pan,            
            'email' => $partyData->party_email,
            'contact_email' => $partyData->contact_email,
            'phone_no' => $partyData->party_phone,
            'contact_no' => $partyData->party_mobile,
            'billing_address' => str_replace('"',"",((!empty($data['billing_address']))?$data['billing_address']:$partyData->party_address)),
            'billing_pincode' => (!empty($data['billing_pincode']))?$data['billing_pincode']:$partyData->party_pincode,
            'billing_country_name' => (!empty($data['billing_city']))?$bilCityData->country_name:$partyData->country_name,
            'billing_state_name' => (!empty($data['billing_city']))?$bilCityData->state_name:$partyData->state_name,
            'billing_city_name' => (!empty($data['billing_city']))?$bilCityData->name:$partyData->city_name,
            'billing_state_code' => $bilCityData->state_code,
            'ship_address' => str_replace('"',"",((!empty($data['ship_address']))?$data['ship_address']:$partyData->party_address)),
            'ship_pincode' => (!empty($data['ship_pincode']))?$data['ship_pincode']:$partyData->party_pincode,
            'ship_country_name' => (!empty($data['ship_city']))?$shipCityData->country_name:$partyData->country_name,
            'ship_state_name' => (!empty($data['ship_city']))?$shipCityData->state_name:$partyData->state_name,
            'ship_city_name' => (!empty($data['ship_city']))?$shipCityData->name:$partyData->city_name,
            'ship_state_code' => $shipCityData->state_code,
        ];        

        $einvData = array();
        $einvData["Version"] = "1.1";

        $einvData["TranDtls"] = [
            "TaxSch" => "GST", 
            "SupTyp" => $data['type_of_transaction'], 
            "RegRev" => "N", 
            "EcmGstin" => null, 
            "IgstOnIntra" => "N" 
        ];

        $einvData["DocDtls"] = [
            "Typ" => $data['doc_type'], 
            "No" => getPrefixNumber($invData->trans_prefix,$invData->trans_no), 
            "Dt" => date("d/m/Y",strtotime($invData->trans_date))
        ];

        $orgData->company_address = str_replace(["\r\n", "\r", "\n"], " ", $orgData->company_address);
        $orgAdd1 = substr($orgData->company_address,0,100);
        $orgAdd2 = (strlen($orgData->company_address) > 100)?substr($orgData->company_address,100,200):"";
        $orgData->company_contact = str_replace(["+"," ","-"],"",$orgData->company_contact);
        $einvData["SellerDtls"] = [
            "Gstin" => $orgData->company_gst_no, 
            "LglNm" => $orgData->company_name,
            "TrdNm" => $orgData->company_name, 
            "Addr1" => $orgAdd1, 
            "Loc" => $orgData->company_city,  
            "Pin" => (int) $orgData->company_pincode,
            "Stcd" => $orgData->company_state_code, 
            "Ph" => $orgData->company_contact,  
            "Em" => $orgData->company_email
        ];
        if(strlen($orgAdd2)):
            $einvData["SellerDtls"]['Addr2'] = $orgAdd2;
        endif;

        $billingAddress = (!empty($data['billing_address']))?$data['billing_address']:$partyData->party_address;
        $billingAddress = str_replace(["\r\n", "\r", "\n"], " ", $billingAddress);
        $partyAdd1 = substr($billingAddress,0,100);
        $partyAdd2 = (strlen($billingAddress) > 100)?substr($billingAddress,100,200):"";
        $billingPincode = (!empty($data['billing_pincode']))?$data['billing_pincode']:$partyData->party_pincode;
        $einvData["BuyerDtls"] = [
            "Gstin" => $partyData->gstin, 
            "LglNm" => $partyData->party_name, 
            "TrdNm" => $partyData->party_name,
            "Pos" => $bilCityData->state_code, 
            "Addr1" => $partyAdd1, 
            "Loc" => $bilCityData->name, 
            "Pin" => (int) $billingPincode, 
            "Stcd" => $bilCityData->state_code, 
            "Ph" => trim(str_replace(["+","-"],"",$partyData->party_mobile)), 
            "Em" => $partyData->contact_email
        ];
        if(strlen($partyAdd2) > 3):
            $einvData["BuyerDtls"]['Addr2'] = $partyAdd2;
        endif;

        $dispatchAddress = (!empty($data['dispatch_address']))?$data['dispatch_address']:$orgData->company_address;
        $dispatchAddress = str_replace(["\r\n", "\r", "\n"], " ", $dispatchAddress);
        $dispatchAdd1 = substr($dispatchAddress,0,100);
        $dispatchAdd2 = (strlen($dispatchAddress) > 100)?substr($dispatchAddress,100,200):"";
        $dispatchPincode = (!empty($data['dispatch_pincode']))?$data['dispatch_pincode']:$orgData->company_pincode;
        $einvData["DispDtls"] = [
            "Nm" => $orgData->company_name,
            "Addr1" => $dispatchAdd1,  
            "Loc" => $disCityData->name,  
            "Pin" => (int) $dispatchPincode,
            "Stcd" => $disCityData->state_code, 
        ];
        if(strlen($dispatchAdd2)):
            $einvData["DispDtls"]['Addr2'] = $dispatchAdd2;
        endif;

        $shippingAddress = (!empty($data['ship_address']))?$data['ship_address']:$partyData->party_address;
        $shippingAddress = str_replace(["\r\n", "\r", "\n"], " ", $shippingAddress);
        $shipAdd1 = substr($shippingAddress,0,100);
        $shipAdd2 = (strlen($shippingAddress) > 100)?substr($shippingAddress,100,200):"";
        $shipCode = (!empty($data['ship_pincode']))?$data['ship_pincode']:$partyData->party_pincode;
        $einvData["ShipDtls"] = [
            "Gstin" => $partyData->gstin,
            "LglNm" => $partyData->party_name,
            "TrdNm" => $partyData->party_name, 
            "Addr1" => $shipAdd1, 
            "Loc" => $shipCityData->name,
            "Pin" => (int) $shipCode,  
            "Stcd" => $shipCityData->state_code
        ];
        if(strlen($shipAdd2) > 3):
            $einvData["ShipDtls"]['Addr2'] = $shipAdd2;
        endif;

        $i=1;
        foreach($invData->itemData as $row):
            $cgst_amount = 0;
            $sgst_amount = 0;
            $igst_amount = 0;

            if($invData->gst_type == 1):
                $cgst_amount = $row->cgst_amount;
                $sgst_amount = $row->sgst_amount;                
            elseif($invData->gst_type == 2):
                $igst_amount = $row->igst_amount;
            endif;

            $row->item_name = str_replace(['"'], ' ', $row->item_name);
            $einvData["ItemList"][] = [
                "SlNo" => strval($i++), 
                "PrdDesc" => $row->item_name, 
                "IsServc" => ($invData->sales_type == 3)?"Y":(($row->item_type == 10)?"Y":"N"),//($row->item_type == 10)?"Y":"N", 
                "HsnCd" => $row->hsn_code, //"9613",
                // "Barcde" => "123456", 
                "Qty" => round($row->qty,2), 
                "FreeQty" => 0, 
                "Unit" => $row->unit_name, 
                "UnitPrice" => round($row->price,2), 
                "TotAmt" => round($row->amount,2), 
                "Discount" => round($row->disc_amount,2), 
                // "PreTaxVal" => 1, 
                "AssAmt" => round($row->taxable_amount,2), 
                "GstRt" => round($row->gst_per,2), 
                "IgstAmt" => round($igst_amount,2), 
                "CgstAmt" => round($cgst_amount,2), 
                "SgstAmt" => round($sgst_amount,2), 
                // "CesRt" => 5, 
                // "CesAmt" => 498.94, 
                // "CesNonAdvlAmt" => 10, 
                // "StateCesRt" => 12, 
                // "StateCesAmt" => 1197.46, 
                // "StateCesNonAdvlAmt" => 5, 
                // "OthChrg" => 10, 
                "TotItemVal" => round($row->net_amount,2), 
                // "OrdLineRef" => "3256", 
                // "OrgCntry" => "AG", 
                // "PrdSlNo" => "12345", 
                // "BchDtls" => [
                //     "Nm" => "123456", 
                //     "Expdt" => "01/08/2020", 
                //     "wrDt" => "01/09/2020" 
                // ], 
                // "AttribDtls" => [
                //     [
                //         "Nm" => "Rice", 
                //         "Val" => "10000" 
                //     ] 
                // ] 
            ];
        endforeach;        

        $einvData["ValDtls"] = [
            "AssVal" => round($invData->taxable_amount,2), 
            "CgstVal" => ($invData->gst_type == 1)?round($invData->cgst_amount,2):0, 
            "SgstVal" => ($invData->gst_type == 1)?round($invData->sgst_amount,2):0, 
            "IgstVal" => ($invData->gst_type == 2)?round($invData->igst_amount,2):0,
            // "CesVal" => 508.94, 
            // "StCesVal" => 1202.46, 
            // "Discount" => floatVal($row->disc_amount), 
            //floatVal($invData->net_amount - ($invData->taxable_amount + $invData->igst_amount));
            "OthChrg" => round(($invData->net_amount - ($invData->taxable_amount + $invData->igst_amount)),2), 
            "RndOffAmt" => round($invData->round_off_amount,2), 
            "TotInvVal" => round($invData->net_amount,2), 
            // "TotInvValFc" => 12897.7
        ];

        /* $einvData["PayDtls"] = [
            "Nm" => "ABCDE", 
            "Accdet" => "5697389713210", 
            "Mode" => "Cash", 
            "Fininsbr" => "SBIN11000", 
            "Payterm" => "100", 
            "Payinstr" => "Gift", 
            "Crtrn" => "test", 
            "Dirdr" => "test", 
            "Crday" => 100, 
            "Paidamt" => 10000, 
            "Paymtdue" => 5000
        ]; */

        /* $einvData["RefDtls"] = [
            "InvRm" => "TEST", 
            "DocPerdDtls" => [
                "InvStDt" => "01/08/2020", 
                "InvEndDt" => "01/09/2020" 
            ], 
            "PrecDocDtls" => [
                [
                    "InvNo" => "DOC/002", 
                    "InvDt" => "01/08/2020", 
                    "OthRefNo" => "123456" 
                ] 
            ], 
            "ContrDtls" => [
                [
                    "RecAdvRefr" => "Doc/003", 
                    "RecAdvDt" => "01/08/2020", 
                    "Tendrefr" => "Abc001", 
                    "Contrrefr" => "Co123", 
                    "Extrefr" => "Yo456", 
                    "Projrefr" => "Doc-456", 
                    "Porefr" => "Doc-789", 
                    "PoRefDt" => "01/08/2020" 
                ] 
            ]
        ]; */

        /* $einvData["AddlDocDtls"] = [
            [
                "Url" => "https://einv-apisandbox.nic.in", 
                "Docs" => "Test Doc", 
                "Info" => "Document Test" 
            ]
        ]; */

        /* $einvData["ExpDtls"] = [
            "ShipBNo" => "A-248", 
            "ShipBDt" => "01/08/2020", 
            "Port" => "INABG1", 
            "RefClm" => "N", 
            "ForCur" => "AED", 
            "CntCode" => "AE"
        ]; */

        if($data['ewb_status'] == 1):
            $einvData["EwbDtls"] = [
                "TransId" => $data['transport_id'],
                "TransName" => $data['transport_name'],
                "Distance" => $data['trans_distance'],
                "TransDocNo" => $data['transport_doc_no'],
                "TransDocDt" => (!empty($data['transport_doc_date']))?date("d/m/Y",strtotime($data['transport_doc_date'])):"",
                "VehNo" => $data['vehicle_no'],
                "VehType" => $data['vehicle_type'],
                "TransMode" => $data['trans_mode']
            ];
        endif;

        $postData['einvData'] = $einvData;
        return $postData;
    }

    /* Generate New E-Invoice */
    public function generateEinvoice($data){
        $ref_id = $data['ref_id'];
        $postData = $this->einvJson($data);
        //print_r($postData);exit;

        $curlEwaybill = curl_init();
        curl_setopt_array($curlEwaybill, array(
            CURLOPT_URL => "https://ebill.nativebittechnologies.com/generate/eInvoice",
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
			$ewayLog = [
                'id' => '',
                'type' => 3,
                'response_status'=> "Fail",
                'response_data'=> $response,
                'created_by'=> $this->loginId,
                'created_at' => date("Y-m-d H:i:s")
            ];
            $this->store($this->eBillLog,$ewayLog);
			
            return ['status'=>2,'message'=>'Somthing is wrong1. cURL Error #:'. $error]; 
        else:
            $responseEinv = json_decode($response,false);	
                        
            if(isset($responseEinv->status) && $responseEinv->status == 0):				
				$ewayLog = [
                    'id' => '',
                    'type' => 3,
                    'response_status'=> "Fail",
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->eBillLog,$ewayLog);
				
                return ['status'=>2,'message'=>'Somthing is wrong2. E-Invoice Error #: '. $responseEinv->error_message ];
            else:						
                $ewayLog = [
                    'id' => '',
                    'type' => 3,
                    'response_status'=> "Success",
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->eBillLog,$ewayLog);

                $this->edit("trans_main",['id'=>$ref_id],['e_inv_status'=>1,'e_inv_no'=>$responseEinv->data->ack_no,'e_inv_irn'=>$responseEinv->data->irn]);

                return ['status'=>1,'message'=>'E-Invoice Generated successfully.'];
            endif;
        endif;
    }

    /* SYNC E-Invoice From GOV. Portal */
    public function syncEinvoice($data){
        $ref_id = $data['ref_id'];
        $postData = $this->einvJson($data);

        $curlEwaybill = curl_init();
        curl_setopt_array($curlEwaybill, array(
            CURLOPT_URL => "https://ebill.nativebittechnologies.com/generate/syncEinv",
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
			$ewayLog = [
                'id' => '',
                'type' => 4,
                'response_status'=> "Fail",
                'response_data'=> $response,
                'created_by'=> $this->loginId,
                'created_at' => date("Y-m-d H:i:s")
            ];
            $this->store($this->eBillLog,$ewayLog);
			
            return ['status'=>2,'message'=>'Somthing is wrong1. cURL Error #:'. $error]; 
        else:
            $responseEinv = json_decode($response,false);	
                        
            if(isset($responseEinv->status) && $responseEinv->status == 0):				
				$ewayLog = [
                    'id' => '',
                    'type' => 4,
                    'response_status'=> "Fail",
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->eBillLog,$ewayLog);
				
                return ['status'=>2,'message'=>'Somthing is wrong2. E-Invoice Error #: '. $responseEinv->error_message ];
            else:						
                $ewayLog = [
                    'id' => '',
                    'type' => 4,
                    'response_status'=> "Success",
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->eBillLog,$ewayLog);

                $calcelReason = [
                    1 => "Duplicate", 
                    2 => "Data entry mistake", 
                    3 => "Order Cancelled", 
                    4 => "Others"
                ];

                $cancel_reason = (!empty($responseEinv->data->cancel_reason))?$calcelReason[$responseEinv->data->cancel_reason]:"";
                $einvStatus = (!empty($responseEinv->data->cancel_reason))?3:2;

                $this->edit("trans_main",['id'=>$ref_id],['e_inv_status'=>$einvStatus,'e_inv_no'=>$responseEinv->data->ack_no,'e_inv_irn'=>$responseEinv->data->irn,'close_reason'=>$cancel_reason,'close_date'=>(!empty($responseEinv->data->cancel_date))?$responseEinv->data->cancel_date:NULL]);

                return ['status'=>1,'message'=>'E-Invoice Sync successfully.','data'=>$responseEinv];
            endif;
        endif;
    }

    /* Cancel E-Invoice on irn */
    public function cancelEinv($postData){
        $ref_id = $postData['ref_id'];

        $calcelReason = [
            1 => "Duplicate", 
            2 => "Data entry mistake", 
            3 => "Order Cancelled", 
            4 => "Others"
        ];

        if(!empty($postData['Irn'])):
            $orgData = $this->getCompanyInfo();
            $postData['Gstin'] = $orgData->company_gst_no;

            $curlEwaybill = curl_init();
            curl_setopt_array($curlEwaybill, array(
                CURLOPT_URL => "https://ebill.nativebittechnologies.com/generate/cancelEinv",
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
                $ewayLog = [
                    'id' => '',
                    'type' => 5,
                    'response_status'=> "Fail",
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->eBillLog,$ewayLog);
                
                return ['status'=>2,'message'=>'Somthing is wrong1. cURL Error #:'. $error]; 
            else:
                $responseEinv = json_decode($response,false);	
                            
                if(isset($responseEinv->status) && $responseEinv->status == 0):				
                    $ewayLog = [
                        'id' => '',
                        'type' => 5,
                        'response_status'=> "Fail",
                        'response_data'=> $response,
                        'created_by'=> $this->loginId,
                        'created_at' => date("Y-m-d H:i:s")
                    ];
                    $this->store($this->eBillLog,$ewayLog);
                    
                    return ['status'=>2,'message'=>'Somthing is wrong2. E-Invoice Error #: '. $responseEinv->error_message ];
                else:						
                    $ewayLog = [
                        'id' => '',
                        'type' => 5,
                        'response_status'=> "Success",
                        'response_data'=> $response,
                        'created_by'=> $this->loginId,
                        'created_at' => date("Y-m-d H:i:s")
                    ];
                    $this->store($this->eBillLog,$ewayLog);
                    
                    $cancel_reason = (!empty($responseEinv->data->cancel_reason))?$calcelReason[$responseEinv->data->cancel_reason]:"";
                    $cancel_date = (!empty($responseEinv->data->cancel_date))?date("Y-m-d H:i:s",strtotime($responseEinv->data->cancel_date)):NULL;

                    $this->edit("trans_main",['id'=>$ref_id],['e_inv_status'=>3,'close_reason'=>$cancel_reason,'close_date'=>$cancel_date,'trans_status'=>3]);

                    return ['status'=>1,'message'=>'E-Invoice Cancel successfully.','data'=>$responseEinv];
                endif;
            endif;
        else:
            $cancel_reason = $calcelReason[$postData['CnlRsn']]." - ".$postData['CnlRem'];
            $cancel_date = date("Y-m-d H:i:s");

            $this->edit("trans_main",['id'=>$ref_id],['close_reason'=>$cancel_reason,'close_date'=>$cancel_date,'trans_status'=>3]);

            return ['status'=>1,'message'=>'Tax Invoice Cancel successfully.'];
        endif;
    }

    public function generateEinvJson($data){
        $jsonData = $this->einvJson($data);
        /* $validate = $this->validateEinvoiceJson($jsonData['einvData']);
        if($validate['status'] == 2):
            return $validate;
        endif; */

        $invNo = $jsonData['einvData']['DocDtls']['No'];
        return ['status'=>1,'message'=>'Json Generated successfully.','json_data'=>$jsonData['einvData'],'inv_no'=>$invNo];
    }
    
    public function getEmasterData($no,$type){
        $queryData = array();
        $queryData['tableName'] = $this->eBillLog;
        $queryData['where_in']['type'] = $type;

        if(in_array($type,["EWB","SYNCEWB","'EWB','SYNCEWB'"])):
            $queryData['where']['eway_bill_no'] = $no;
        else:
            $queryData['where']['ack_no'] = $no;
        endif;
        $queryData['order_by']['id'] = 'DESC';
        $queryData['limit'] = 1;        
        $result = $this->row($queryData);

        $response = json_decode($result->response_data,false);
        $response = $response->data;
        //print_r($response);exit;
        $response->json_data = json_decode($response->json_data,false);  
        $response->response_json = json_decode($response->response_json,false);      
        $response->company_info = json_decode($response->company_info,false);
        $response->party_info = json_decode($response->party_info,false);
        //print_r($response->json_data);exit;

        return $response;
    }
}
?>