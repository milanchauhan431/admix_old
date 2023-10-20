<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

function getAccountingDtHeader($page){
    /* Sales Invoice Header */
    $data['salesInvoice'][] = ["name"=>"Action","style"=>"width:5%;","sortable"=>"FALSE","textAlign"=>"center"];
	$data['salesInvoice'][] = ["name"=>"#","style"=>"width:5%;","sortable"=>"FALSE","textAlign"=>"center"]; 
	$data['salesInvoice'][] = ["name"=>"Inv No."];
	$data['salesInvoice'][] = ["name"=>"Inv Date"];
	$data['salesInvoice'][] = ["name"=>"Customer Name"];
	$data['salesInvoice'][] = ["name"=>"Taxable Amount"];
	$data['salesInvoice'][] = ["name"=>"GST Amount"];
    $data['salesInvoice'][] = ["name"=>"Net Amount"];

    /* Credit Note Header */
    $data['creditNote'][] = ["name"=>"Action","style"=>"width:5%;","sortable"=>"FALSE","textAlign"=>"center"];
	$data['creditNote'][] = ["name"=>"#","style"=>"width:5%;","sortable"=>"FALSE","textAlign"=>"center"]; 
	$data['creditNote'][] = ["name"=>"CN Type."];
	$data['creditNote'][] = ["name"=>"CN No."];
	$data['creditNote'][] = ["name"=>"CN Date"];
	$data['creditNote'][] = ["name"=>"Party Name"];
	$data['creditNote'][] = ["name"=>"Taxable Amount"];
	$data['creditNote'][] = ["name"=>"GST Amount"];
    $data['creditNote'][] = ["name"=>"Net Amount"];

    /* Purchase Invoice Header */
    $data['purchaseInvoice'][] = ["name"=>"Action","style"=>"width:5%;","sortable"=>"FALSE","textAlign"=>"center"];
	$data['purchaseInvoice'][] = ["name"=>"#","style"=>"width:5%;","sortable"=>"FALSE","textAlign"=>"center"]; 
	$data['purchaseInvoice'][] = ["name"=>"Inv No."];
	$data['purchaseInvoice'][] = ["name"=>"Inv Date"];
	$data['purchaseInvoice'][] = ["name"=>"Party Name"];
	$data['purchaseInvoice'][] = ["name"=>"Taxable Amount"];
	$data['purchaseInvoice'][] = ["name"=>"GST Amount"];
    $data['purchaseInvoice'][] = ["name"=>"Net Amount"];

    /* Debit Note Header */
    $data['debitNote'][] = ["name"=>"Action","style"=>"width:5%;","sortable"=>"FALSE","textAlign"=>"center"];
	$data['debitNote'][] = ["name"=>"#","style"=>"width:5%;","sortable"=>"FALSE","textAlign"=>"center"]; 
	$data['debitNote'][] = ["name"=>"DN Type."];
	$data['debitNote'][] = ["name"=>"DN No."];
	$data['debitNote'][] = ["name"=>"DN Date"];
	$data['debitNote'][] = ["name"=>"Party Name"];
	$data['debitNote'][] = ["name"=>"Taxable Amount"];
	$data['debitNote'][] = ["name"=>"GST Amount"];
    $data['debitNote'][] = ["name"=>"Net Amount"];

    /* GST Expense Header */
    $data['gstExpense'][] = ["name"=>"Action","style"=>"width:5%;","sortable"=>"FALSE","textAlign"=>"center"];
	$data['gstExpense'][] = ["name"=>"#","style"=>"width:5%;","sortable"=>"FALSE","textAlign"=>"center"]; 
	$data['gstExpense'][] = ["name"=>"Inv No."];
	$data['gstExpense'][] = ["name"=>"Inv Date"];
	$data['gstExpense'][] = ["name"=>"Party Name"];
	$data['gstExpense'][] = ["name"=>"Taxable Amount"];
	$data['gstExpense'][] = ["name"=>"GST Amount"];
    $data['gstExpense'][] = ["name"=>"Net Amount"];

    /* GST Income Header */
    $data['gstIncome'][] = ["name"=>"Action","style"=>"width:5%;","sortable"=>"FALSE","textAlign"=>"center"];
	$data['gstIncome'][] = ["name"=>"#","style"=>"width:5%;","sortable"=>"FALSE","textAlign"=>"center"]; 
	$data['gstIncome'][] = ["name"=>"Inv No."];
	$data['gstIncome'][] = ["name"=>"Inv Date"];
	$data['gstIncome'][] = ["name"=>"Party Name"];
	$data['gstIncome'][] = ["name"=>"Taxable Amount"];
	$data['gstIncome'][] = ["name"=>"GST Amount"];
    $data['gstIncome'][] = ["name"=>"Net Amount"];

    /* Journal Entry Header */
    $data['journalEntry'][] = ["name" => "Action", "style" => "width:5%;","sortable"=>"FALSE","textAlign"=>"center"];
    $data['journalEntry'][] = ["name" => "#", "style" => "width:5%;","sortable"=>"FALSE","textAlign"=>"center"];
    $data['journalEntry'][] = ["name" => "JV No."];
    $data['journalEntry'][] = ["name" => "JV Date."];
    $data['journalEntry'][] = ["name" => "Ledger Name"];
    $data['journalEntry'][] = ["name" => "Debit", "textAlign" => "right"];
    $data['journalEntry'][] = ["name" => "Credit", "textAlign" => "right"];
    $data['journalEntry'][] = ["name" => "Note"];

    /* Payment Voucher  */
    $data['paymentVoucher'][] = ["name" => "Action", "style" => "width:5%;","sortable"=>"FALSE","textAlign"=>"center"];
    $data['paymentVoucher'][] = ["name" => "#", "style" => "width:5%;", "sortable"=>"FALSE","textAlign"=>"center"];
    $data['paymentVoucher'][] = ["name" => "Voucher No."];
    $data['paymentVoucher'][] = ["name" => "Voucher Date"];
    $data['paymentVoucher'][] = ["name" => "Party Name"];
    $data['paymentVoucher'][] = ["name" => "Bank/Cash"];
    $data['paymentVoucher'][] = ["name" => "Amount", "style" => "width:5%;", "textAlign" => "center"];
    $data['paymentVoucher'][] = ["name" => "Doc. No."];
    $data['paymentVoucher'][] = ["name" => "Doc. Date"];
    $data['paymentVoucher'][] = ["name" => "Note"];

    return tableHeader($data[$page]);
}

/* Sales Invoice Table Data */
function getSalesInvoiceData($data){
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="'.base_url('salesInvoice/edit/'.$data->id).'" datatip="Edit" flow="down" ><i class="ti-pencil-alt"></i></a>';

    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Sales Invoice'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $print = '<a href="javascript:void(0)" class="btn btn-info btn-edit printDialog permission-approve1" datatip="Print Invoice" flow="down" data-id="'.$data->id.'" data-fn_name="printInvoice"><i class="fa fa-print"></i></a>';

    $ewbPDF = '';$ewbDetailPDF = '';$generateEWB = '';  $cancelEwb = '';
    $ewbParam = "{'postData':{'id' : ".$data->id.",'party_id' : ".$data->party_id."}, 'modal_id' : 'modal-xl', 'form_id' : 'generateEwayBill', 'title' : 'E-way Bill For Invoice No. : ".($data->trans_number)."', 'fnedit' : 'addEwayBill', 'fnsave' : 'generateEwb', 'js_store_fn' : 'generateEwb','controller':'ebill','syncBtn':1}";

    if(!empty($data->ewb_status)):
        $ewbPDF = '<a href="'.base_url('ebill/ewb_pdf/'.$data->eway_bill_no).'" target="_blank" datatip="EWB PDF" flow="down" class="btn btn-dark"><i class="fa fa-print"></i></a>';

        $ewbDetailPDF = '<a href="'.base_url('ebill/ewb_detail_pdf/'.$data->eway_bill_no).'" target="_blank" datatip="EWB DETAIL PDF" flow="down" class="btn btn-warning"><i class="fa fa-print"></i></a>';

        if($data->ewb_status == 3):
            $generateEWB = '<a href="javascript:void(0)" class="btn btn-dark" datatip="E-way Bill" flow="down" onclick="ebillFrom('.$ewbParam.');"><i class="fa fa-truck"></i></a>';
        else:
            $cancelEwbParam = "{'postData':{'id' : ".$data->id."}, 'modal_id' : 'modal-md', 'form_id' : 'cancelEwb', 'title' : 'Cancel Eway Bill [ Invoice No. : ".($data->trans_number)." ]', 'fnedit' : 'loadCancelEwayBillForm', 'fnsave' : 'cancelEwayBill', 'js_store_fn' : 'cancelEwayBill','controller':'ebill','syncBtn':0,'save_btn_text':'Cancel EWB'}";
            $cancelEwb = '<a href="javascript:void(0)" class="btn btn-danger" datatip="Cancel Eway Bill" flow="down" onclick="ebillFrom('.$cancelEwbParam.');"><i class="fa fa-times"></i></a>';
        endif;
    else:
        if(empty($data->eway_bill_no)):
            $generateEWB = '<a href="javascript:void(0)" class="btn btn-dark" datatip="E-way Bill" flow="down" onclick="ebillFrom('.$ewbParam.');"><i class="fa fa-truck"></i></a>';
        endif;
    endif;

    $generateEinv = ""; $einvPdf = "";
    if(!empty($data->e_inv_status)):
        $einvPdf = '<a href="'.base_url('ebill/einv_pdf/'.$data->e_inv_no).'" target="_blank" datatip="E-Invoice PDF" flow="down" class="btn btn-dark"><i class="fa fa-print"></i></a>';
    else:
        if(empty($data->e_inv_no)):
            $einvParam = "{'postData':{'id' : ".$data->id.",'party_id' : ".$data->party_id."}, 'modal_id' : 'modal-xl', 'form_id' : 'generateEinv', 'title' : 'E-Invoice For Invoice No. : ".$data->trans_number."', 'fnedit' : 'addEinvoice', 'fnsave' : 'generateEinvoice', 'js_store_fn' : 'generateEinvoice','controller':'ebill','syncBtn':1}";

            $generateEinv = '<a href="javascript:void(0)" class="btn btn-dark" datatip="E-Invoice" flow="down" onclick="ebillFrom('.$einvParam.');"><i class="icon-Receipt-3"></i></a>';
        endif;
    endif;

    $cancelInv = "";
    if($data->trans_status != 3):
        $cancelInvParam = "{'postData':{'id' : ".$data->id."}, 'modal_id' : 'modal-md', 'form_id' : 'cancelInv', 'title' : 'Cancel Invoice No. : ".$data->trans_number."', 'fnedit' : 'loadCancelInvForm', 'fnsave' : 'cancelEinvoice', 'js_store_fn' : 'cancelEinv','controller':'ebill','syncBtn':0,'save_btn_text':'Cancel Invoice'}";
        $cancelInv = '<a href="javascript:void(0)" class="btn btn-danger" datatip="Cancel Invoice" flow="down" onclick="ebillFrom('.$cancelInvParam.');"><i class="fa fa-times"></i></a>';
    else:
        $edit="";$delete="";$generateEinv = "";$generateEWB = '';  $cancelEwb = '';
    endif;//$cancelInv = "";

    $action = getActionButton($print.$ewbPDF.$ewbDetailPDF.$generateEWB.$cancelEwb.$editButton.$deleteButton);

    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->taxable_amount,$data->gst_amount,$data->net_amount];
}

/* Credit Note Table Data */
function getCreaditNoteData($data){
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="'.base_url('creditNote/edit/'.$data->id).'" datatip="Edit" flow="down" ><i class="ti-pencil-alt"></i></a>';

    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Credit Note'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $print = '<a href="javascript:void(0)" class="btn btn-warning btn-edit printDialog permission-approve1" datatip="Print Invoice" flow="down" data-id="'.$data->id.'" data-fn_name="printCreditNote"><i class="fa fa-print"></i></a>';

    $action = getActionButton($print.$editButton.$deleteButton);

    return [$action,$data->sr_no,$data->order_type,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->taxable_amount,$data->gst_amount,$data->net_amount];
}

/* Purchase Invoice Table Data */
function getPurchaseInvoiceData($data){
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="'.base_url('purchaseInvoice/edit/'.$data->id).'" datatip="Edit" flow="down" ><i class="ti-pencil-alt"></i></a>';

    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Sales Invoice'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($editButton.$deleteButton);

    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->taxable_amount,$data->gst_amount,$data->net_amount];
}

/* Debit Note Table Data */
function getDebitNoteData($data){
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="'.base_url('debitNote/edit/'.$data->id).'" datatip="Edit" flow="down" ><i class="ti-pencil-alt"></i></a>';

    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Debit Note'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $print = '<a href="javascript:void(0)" class="btn btn-warning btn-edit printDialog permission-approve1" datatip="Print Debit Note" flow="down" data-id="'.$data->id.'" data-fn_name="printDebitNote"><i class="fa fa-print"></i></a>';

    $action = getActionButton($print.$editButton.$deleteButton);

    return [$action,$data->sr_no,$data->order_type,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->taxable_amount,$data->gst_amount,$data->net_amount];
}

/* GST Expense Table Data */
function getGstExpenseData($data){
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="'.base_url('gstExpense/edit/'.$data->id).'" datatip="Edit" flow="down" ><i class="ti-pencil-alt"></i></a>';

    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Expese'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($editButton.$deleteButton);

    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->taxable_amount,$data->gst_amount,$data->net_amount];
}

/* GST Income Table Data */
function getGstIncomeData($data){
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="'.base_url('gstIncome/edit/'.$data->id).'" datatip="Edit" flow="down" ><i class="ti-pencil-alt"></i></a>';

    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Income'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($editButton.$deleteButton);

    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->party_name,$data->taxable_amount,$data->gst_amount,$data->net_amount];
}

/* Journal Entry Data */
function getJournalEntryData($data){
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="'.base_url('journalEntry/edit/'.$data->id).'" datatip="Edit" flow="down" ><i class="ti-pencil-alt"></i></a>';

    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Journal Entry'}";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($editButton . $deleteButton);
	$debit = $credit = "";
    if($data->c_or_d == 'DR'){$debit = $data->amount;}else{$credit = $data->amount;}

    return [$action, $data->sr_no, $data->trans_number, formatDate($data->trans_date), $data->acc_name, $debit, $credit, $data->remark];
}


/* Payment Voucher Data */
function getPaymentVoucher($data){
    $editButton = '';$deleteButton = '';

    $deleteParam = "{'postData':{'id' : ".$data->id."},'message' : 'Voucher'}";
    $editParam = "{'postData':{'id' : ".$data->id."}, 'modal_id' : 'modal-lg', 'form_id' : 'editVoucher', 'title' : 'Update Voucher'}";
    
    if($data->trans_status == 0): 
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
        
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;

    $printVoucher = '';
    /* if($data->vou_name_s == "BCRct"){
        $printVoucher = '<a href="javascript:void(0)" class="btn btn-info btn-edit printPaymentVoucher" datatip="Print Voucher" flow="down" data-id="'.$data->id.'" data-function="voucher_pdf"><i class="fa fa-print"></i></a>';
    } */
    $action = getActionButton($printVoucher.$editButton.$deleteButton);
    return [$action,$data->sr_no,$data->trans_number,formatDate($data->trans_date),$data->opp_acc_name,$data->vou_acc_name,$data->net_amount,$data->doc_no,$data->doc_date,$data->remark];
}
?>