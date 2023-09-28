<?php
class GstReportModel extends MasterModel{

    public function _b2b($data){
        $party_id = (!empty($data['party_id']))?"and trans_main.party_id = ".$data['party_id']:"";

        $result = $this->db->query("
            SELECT trans_main.id,trans_main.entry_type,trans_main.trans_number,trans_main.trans_prefix,trans_main.trans_no,trans_main.doc_no,trans_main.trans_date,trans_main.party_name,trans_main.net_amount,trans_main.vou_name_s,trans_main.gstin,trans_main.party_state_code,trans_main.gst_type,trans_child.hsn_code,trans_child.gst_per,SUM(trans_child.taxable_amount) as taxable_amount,SUM(CASE WHEN trans_main.gst_type = 1 THEN trans_child.cgst_amount ELSE 0 END) as cgst_amount,SUM(CASE WHEN trans_main.gst_type = 1 THEN trans_child.sgst_amount ELSE 0 END) as sgst_amount,SUM(CASE WHEN trans_main.gst_type = 2 THEN trans_child.igst_amount ELSE 0 END) as igst_amount,SUM(trans_child.cess_amount) as cess_amount,states.name as state_name,trans_main.sales_type,trans_main.doc_date,trans_main.itc,trans_main.tax_class
            FROM trans_child 
            LEFT JOIN trans_main ON trans_main.id = trans_child.trans_main_id
            LEFT JOIN party_master on party_master.id = trans_main.party_id
            LEFT JOIN states on trans_main.party_state_code = states.gst_statecode
            WHERE trans_child.is_delete = 0
            AND trans_main.vou_name_s IN (".$data['vou_name_s'].")
            AND trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'
            AND trans_main.gstin != 'URP'
            AND trans_main.trans_status != 3
            ".$party_id."
            GROUP BY trans_child.gst_per,trans_main.trans_no
            order by trans_main.trans_date,trans_main.trans_no ASC
        ")->result();
        
        return $result;
    }

    public function _b2bur($data){
        $party_id = (!empty($data['party_id']))?"and trans_main.party_id = ".$data['party_id']:"";

        $result = $this->db->query("
            SELECT trans_main.id,trans_main.entry_type,trans_main.trans_number,trans_main.trans_prefix,trans_main.trans_no,trans_main.doc_no,trans_main.trans_date,trans_main.party_name,trans_main.net_amount,trans_main.vou_name_s,trans_main.gst_type,trans_main.party_state_code,trans_child.hsn_code,trans_child.gst_per,SUM(trans_child.taxable_amount) as taxable_amount,SUM(CASE WHEN trans_main.gst_type = 1 THEN trans_child.cgst_amount ELSE 0 END) as cgst_amount,SUM(CASE WHEN trans_main.gst_type = 1 THEN trans_child.sgst_amount ELSE 0 END) as sgst_amount,SUM(CASE WHEN trans_main.gst_type = 2 THEN trans_child.igst_amount ELSE 0 END) as igst_amount,SUM(trans_child.cess_amount) as cess_amount,states.name as state_name,trans_main.sales_type,trans_main.doc_date,trans_main.itc
            FROM trans_child 
            LEFT JOIN trans_main ON trans_main.id = trans_child.trans_main_id
            LEFT JOIN party_master on party_master.id = trans_main.party_id
            LEFT JOIN states on trans_main.party_state_code = states.gst_statecode
            WHERE trans_child.is_delete = 0
            AND trans_main.vou_name_s IN (".$data['vou_name_s'].")
            AND trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'
            AND trans_main.gstin = 'URP'
            AND trans_main.trans_status != 3
            ".$party_id."
            GROUP BY trans_child.gst_per,trans_main.trans_number
            ORDER BY trans_main.doc_date,trans_main.trans_no ASC
        ")->result();
        
        return $result;
    }

    public function _b2ba($data){
        return [];
    }

    public function _b2cl($data){
        $party_id = (!empty($data['party_id']))?"and trans_main.party_id = ".$data['party_id']:"";

        $result = $this->db->query("
            SELECT trans_main.id,trans_main.entry_type,trans_main.trans_number,trans_main.trans_prefix,trans_main.trans_no,trans_main.doc_no,trans_main.trans_date,trans_main.party_name,trans_main.net_amount,trans_main.vou_name_s,trans_main.gst_type,trans_child.hsn_code,trans_child.gst_per,SUM(trans_child.taxable_amount) as taxable_amount,SUM(CASE WHEN trans_main.gst_type = 1 THEN trans_child.cgst_amount ELSE 0 END) as cgst_amount,SUM(CASE WHEN trans_main.gst_type = 1 THEN trans_child.sgst_amount ELSE 0 END) as sgst_amount,SUM(CASE WHEN trans_main.gst_type = 2 THEN trans_child.igst_amount ELSE 0 END) as igst_amount,SUM(trans_child.cess_amount) as cess_amount,states.name as state_name,trans_main.sales_type,trans_main.doc_date,trans_main.gstin,states.gst_statecode as party_state_code
            FROM trans_child 
            LEFT JOIN trans_main ON trans_main.id = trans_child.trans_main_id
            LEFT JOIN party_master on party_master.id = trans_main.party_id
            LEFT JOIN states on party_master.state_id = states.id
            WHERE trans_child.is_delete = 0
            AND trans_main.vou_name_s IN (".$data['vou_name_s'].")
            AND trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'
            AND trans_main.gstin = 'URP'
            AND trans_main.trans_status != 3
            ".$party_id."
            AND trans_main.taxable_amount > 250000
            GROUP BY trans_child.gst_per,trans_main.trans_number
            order by trans_main.trans_date,trans_main.trans_no ASC
        ")->result();
        
        return $result;
    }

    public function _b2cla($data){
        return [];
    }

    public function _b2cs($data){
        $party_id = (!empty($data['party_id']))?"and trans_main.party_id = ".$data['party_id']:"";

        $result = $this->db->query("
            SELECT trans_main.id,trans_main.entry_type,trans_main.trans_number,trans_main.trans_prefix,trans_main.trans_no,trans_main.doc_no,trans_main.trans_date,trans_main.party_name,trans_main.net_amount,trans_main.vou_name_s,trans_main.gst_type,trans_child.hsn_code,trans_child.gst_per,SUM(trans_child.taxable_amount) as taxable_amount,SUM(CASE WHEN trans_main.gst_type = 1 THEN trans_child.cgst_amount ELSE 0 END) as cgst_amount,SUM(CASE WHEN trans_main.gst_type = 1 THEN trans_child.sgst_amount ELSE 0 END) as sgst_amount,SUM(CASE WHEN trans_main.gst_type = 2 THEN trans_child.igst_amount ELSE 0 END) as igst_amount,SUM(trans_child.cess_amount) as cess_amount,states.name as state_name,trans_main.sales_type,trans_main.doc_date,trans_main.gstin,states.gst_statecode as party_state_code
            FROM trans_child 
            LEFT JOIN trans_main ON trans_main.id = trans_child.trans_main_id
            LEFT JOIN party_master on party_master.id = trans_main.party_id
            LEFT JOIN states on party_master.state_id = states.id
            WHERE trans_child.is_delete = 0
            AND trans_main.vou_name_s IN (".$data['vou_name_s'].")
            AND trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'
            AND trans_main.gstin = 'URP'
            AND trans_main.trans_status != 3
            ".$party_id."
            AND trans_main.taxable_amount <= 250000
            GROUP BY trans_child.gst_per,trans_main.trans_number
            order by trans_main.trans_date,trans_main.trans_no ASC
        ")->result();

        return $result;
    }

    public function _b2csa($data){
        return [];
    }

    public function _cdnr($data){
        $party_id = (!empty($data['party_id']))?"and trans_main.party_id = ".$data['party_id']:"";
        $orderType = ($data['report'] == "gstr1")?"'Increase Sales','Decrease Sales','Sales Return'":"'Increase Purchase','Decrease Purchase','Purchase Return'";

        $result = $this->db->query("
            SELECT trans_main.id,trans_main.entry_type,trans_main.trans_number,trans_main.trans_prefix,trans_main.trans_no,trans_main.doc_no,trans_main.trans_date,trans_main.doc_date,trans_main.party_name,trans_main.net_amount,trans_main.vou_name_s,trans_main.gst_type,trans_child.hsn_code,trans_child.gst_per,SUM(trans_child.taxable_amount) as taxable_amount,SUM(CASE WHEN trans_main.gst_type = 1 THEN trans_child.cgst_amount ELSE 0 END) as cgst_amount,SUM(CASE WHEN trans_main.gst_type = 1 THEN trans_child.sgst_amount ELSE 0 END) as sgst_amount,SUM(CASE WHEN trans_main.gst_type = 2 THEN trans_child.igst_amount ELSE 0 END) as igst_amount,SUM(trans_child.cess_amount) as cess_amount,states.name as state_name,trans_main.sales_type,trans_main.gstin,trans_main.party_state_code,trans_main.tax_class
            FROM trans_child 
            LEFT JOIN trans_main ON trans_main.id = trans_child.trans_main_id
            LEFT JOIN party_master on party_master.id = trans_main.party_id
            LEFT JOIN states on trans_main.party_state_code = states.gst_statecode
            WHERE trans_child.is_delete = 0
            AND trans_main.vou_name_s IN (".$data['vou_name_s'].")
            AND trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'
            AND trans_main.gstin != 'URP'
            AND trans_main.order_type IN (".$orderType.")
            AND trans_main.trans_status != 3
            ".$party_id."
            GROUP BY trans_child.gst_per,trans_main.trans_number
            ORDER BY trans_main.trans_date,trans_main.trans_no ASC
        ")->result();

        return $result;
    }

    public function _cdnra($data){
        return [];
    }

    public function _cdnur($data){
        $party_id = (!empty($data['party_id']))?"and trans_main.party_id = ".$data['party_id']:"";
        $orderType = ($data['report'] == "gstr1")?"'Increase Sales','Decrease Sales','Sales Return'":"'Increase Purchase','Decrease Purchase','Purchase Return'";
        $taxClass = ($data['report'] == 'gstr1')?"'SALESGSTACC','SALESIGSTACC','SALESJOBGSTACC','SALESJOBIGSTACC','SALESTFACC','SALESEXEMPTEDTFACC','EXPORTGSTACC','EXPORTTFACC'":"'PURURDGSTACC','PURURDIGSTACC','PURTFACC','PUREXEMPTEDTFACC'";

        $result = $this->db->query("
            SELECT trans_main.id,trans_main.entry_type,trans_main.trans_number,trans_main.trans_prefix,trans_main.trans_no,trans_main.doc_no,trans_main.trans_date,trans_main.doc_date,trans_main.party_name,trans_main.net_amount,trans_main.vou_name_s,trans_main.gst_type,trans_child.hsn_code,trans_child.gst_per,SUM(trans_child.taxable_amount) as taxable_amount,SUM(CASE WHEN trans_main.gst_type = 1 THEN trans_child.cgst_amount ELSE 0 END) as cgst_amount,SUM(CASE WHEN trans_main.gst_type = 1 THEN trans_child.sgst_amount ELSE 0 END) as sgst_amount,SUM(CASE WHEN trans_main.gst_type = 2 THEN trans_child.igst_amount ELSE 0 END) as igst_amount,SUM(trans_child.cess_amount) as cess_amount,states.name as state_name,trans_main.sales_type,trans_main.gstin,states.gst_statecode as party_state_code,trans_main.tax_class
            FROM trans_child 
            LEFT JOIN trans_main ON trans_main.id = trans_child.trans_main_id
            LEFT JOIN party_master on party_master.id = trans_main.party_id
            LEFT JOIN states on party_master.state_id = states.id
            WHERE trans_child.is_delete = 0
            AND trans_main.vou_name_s IN (".$data['vou_name_s'].")
            AND trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'
            AND trans_main.gstin = 'URP'
            AND trans_main.order_type IN (".$orderType.")
            AND trans_main.trans_status != 3
            ".$party_id."
            GROUP BY trans_child.gst_per,trans_main.trans_number
            ORDER BY trans_main.trans_date,trans_main.trans_no ASC
        ")->result();

        return $result;
    }

    public function _cdnura($data){
        return [];
    }

    public function _exp($data){
        return [];
    }

    public function _expa($data){
        return [];
    }

    public function _at($data){
        $party_id = (!empty($data['party_id']))?"and trans_main.party_id = ".$data['party_id']:"";
        $result = $this->db->query("
            select states.name as state_name,states.gst_statecode,SUM(trans_main.net_amount) as net_amount,SUM(trans_main.cess_amount) as cess_amount,trans_main.gst_applicable,trans_main.entry_type
            from trans_main
            left join party_master on party_master.id = trans_main.party_id
            left join states on party_master.state_id = states.id
            where trans_main.trans_date >= '".$data['from_date']."' 
            and trans_main.trans_date <= '".$data['to_date']."'
            ".$party_id."
            and trans_main.entry_type in (".$data['entry_type'].")
            and trans_main.is_delete = 0
            and trans_main.payment_type = 1
            GROUP BY party_master.state_id
            order by trans_main.trans_date,trans_main.trans_no ASC
        ")->result();
        // print_r($this->printQuery());exit;
        return $result;
    }

    public function _ata($data){
        return [];
    }

    public function _atadj($data){
        return [];
    }

    public function _atadja($data){
        return [];
    }

    public function _exemp($data){
        return [];
    }

    public function _hsn($data){
        /* $queryData['tableName'] = 'trans_child';
        $queryData['select']="SUM(CASE WHEN trans_main.gst_type = 1 THEN trans_child.cgst_amount ELSE 0 END) as cgst_amount,SUM(CASE WHEN trans_main.gst_type =1 THEN trans_child.sgst_amount ELSE 0 END) as sgst_amount,SUM(CASE WHEN trans_main.gst_type = 2 THEN trans_child.igst_amount ELSE 0 END) as igst_amount,SUM(qty) as qty,SUM(trans_child.taxable_amount) as taxable_amount,SUM(CASE WHEN trans_main.gst_type = 3 THEN trans_child.taxable_amount ELSE  trans_child.net_amount END) as net_amount,SUM(trans_child.cess_amount) as cess_amount,unit_master.unit_name,unit_master.description as unit_description,trans_child.hsn_code,trans_child.gst_per";
        $queryData['leftJoin']['trans_main'] = 'trans_child.trans_main_id=trans_main.id';
        $queryData['leftJoin']['unit_master'] = 'unit_master.id=trans_child.unit_id';
        $queryData['where_in']['trans_main.entry_type']= $data['entry_type'];
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '" .$data['from_date'] . "' AND '" . $data['to_date'] . "'";
        
        if(!empty($data['party_id'])):
            $queryData['where']['trans_main.party_id']=$data['party_id'];
        endif;

        if($data['report'] == "gstr1"):
            $queryData['group_by'][]="trans_child.hsn_code,trans_child.unit_id,trans_child.gst_per";
        else:
            $queryData['group_by'][]="trans_child.hsn_code,trans_child.unit_id";
        endif;
        $result=$this->rows($queryData);
		// print_r($this->printQuery());exit;
        return $result; */

        $party_id = (!empty($data['party_id']))?"and trans_main.party_id = ".$data['party_id']:"";
        $tblnm = ($data['entry_type'] == '12,14,18')?"party_master":"trans_main";
        //$order_by = ($data['entry_type'] == '12,14,18')?"order by trans_main.doc_date,trans_main.trans_no ASC":"order by trans_main.trans_date,trans_main.trans_no ASC";
        $decreaseEntryType = ($data['report'] == "gstr1")?13:14;
        $groupBy = ($data['report'] == "gstr1")?" GROUP BY trans_child.hsn_code,trans_child.unit_id,trans_child.gst_per":" GROUP BY trans_child.hsn_code,trans_child.unit_id";

        $result = $this->db->query("
            SELECT trans_main.entry_type,trans_child.hsn_code,trans_child.gst_per,unit_master.unit_name,unit_master.description as unit_description,

            SUM(IF(trans_child.entry_type = $decreaseEntryType,(CASE WHEN trans_child.stock_eff = 1 THEN (trans_child.qty * -1) ELSE 0 END),trans_child.qty)) as qty,

            SUM(IF(trans_child.entry_type = $decreaseEntryType,(trans_child.taxable_amount * -1),trans_child.taxable_amount)) as taxable_amount,
            SUM(CASE WHEN trans_main.gst_type = 1 THEN IF(trans_child.entry_type = $decreaseEntryType,(trans_child.cgst_amount * -1),trans_child.cgst_amount) ELSE 0 END) as cgst_amount,
            SUM(CASE WHEN trans_main.gst_type = 1 THEN IF(trans_child.entry_type = $decreaseEntryType,(trans_child.sgst_amount * -1),trans_child.sgst_amount) ELSE 0 END) as sgst_amount,
            SUM(CASE WHEN trans_main.gst_type = 2 THEN IF(trans_child.entry_type = $decreaseEntryType,(trans_child.igst_amount * -1),trans_child.igst_amount) ELSE 0 END) as igst_amount,
            SUM(IF(trans_child.entry_type = $decreaseEntryType,(trans_child.cess_amount * -1),trans_child.cess_amount)) as cess_amount,
            SUM(IF(trans_child.entry_type = $decreaseEntryType,(trans_child.net_amount * -1),trans_child.net_amount)) as net_amount
            
            FROM trans_child 
            LEFT JOIN trans_main ON trans_main.id = trans_child.trans_main_id
            LEFT JOIN party_master ON party_master.id = trans_main.party_id
            LEFT JOIN unit_master ON unit_master.id=trans_child.unit_id
            WHERE trans_child.is_delete = 0
            AND trans_main.entry_type IN (".$data['entry_type'].")
            AND trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'
            AND trans_main.trans_status != 3
            ".$party_id."
            $groupBy
        ")->result();
        
        return $result;
    }
	
    public function _docs($data){
        /* $queryData['tableName'] = 'trans_main';
        //$queryData['select']="MAX(trans_main.trans_number) as max_trans_no,MIN(trans_main.trans_number) as min_trans_no,count(trans_main.id) as total_inv,trans_main.trans_prefix";
		$queryData['select'] = "MAX(trans_main.trans_no) as max_trans_no, MIN(trans_main.trans_no) as min_trans_no, SUM(CASE WHEN trans_status <> 3 THEN 1 ELSE 0 END) as total_inv, trans_main.trans_number,trans_main.trans_prefix,SUM(CASE WHEN trans_status = 3 THEN 1 ELSE 0 END) as total_cnl_inv";
        $queryData['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
        $queryData['where_in']['trans_main.entry_type']= $data['entry_type'];
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '" .$data['from_date'] . "' AND '" . $data['to_date'] . "'";
        $queryData['where']['party_master.group_code'] = "SD";
        if (!empty($data['party_id'])):
            $queryData['where']['trans_main.party_id']=$data['party_id'];
        endif;
        $queryData['group_by'][]="trans_main.entry_type,trans_main.trans_prefix";
        $result=$this->rows($queryData);
        //print_r($this->printQuery());exit; */

        $party_id = (!empty($data['party_id']))?" AND trans_main.party_id = ".$data['party_id']:"";

        $result = $this->db->query("
            SELECT MAX(trans_main.trans_no) as max_trans_no, MIN(trans_main.trans_no) as min_trans_no, SUM(CASE WHEN trans_status <> 3 THEN 1 ELSE 0 END) as total_inv, trans_main.trans_number,trans_main.trans_prefix,SUM(CASE WHEN trans_status = 3 THEN 1 ELSE 0 END) as total_cnl_inv
            
            FROM trans_main 
            LEFT JOIN party_master ON party_master.id = trans_main.party_id
            WHERE trans_main.is_delete = 0
            AND trans_main.entry_type IN (".$data['entry_type'].")
            AND trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'
            AND party_master.group_code IN ('SD','SC')
            ".$party_id."
            GROUP BY trans_main.entry_type,trans_main.trans_prefix
        ")->result();
        return $result;
    }

    public function _imps($data){
        return array();
    }

    public function _impg($data){
        return array();
    }

    public function _itcr($data){
        return array();
    }
}
?>