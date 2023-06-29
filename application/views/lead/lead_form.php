<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=!empty($dataRow->id)?$dataRow->id:''?>">
            <input type="hidden" name="entry_type" id="entry_type" value="1">

            <div class="col-md-6 form-group">
                <label for="party_id">Customer</label>
                <span class="dropdown float-right m-r-5">
                    <a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down">+ Add New</a>

                    <div class="dropdown-menu dropdown-menu-left user-dd animated flipInY" x-placement="start-left">
                        <div class="d-flex no-block align-items-center p-10 bg-primary text-white">ACTION</div>
                        
                        <a class="dropdown-item addNew" href="javascript:void(0)" data-button="both" data-modal_id="modal-xl" data-function="addParty" data-controller="parties" data-postdata='{"party_category" : 1 }' data-res_function="resPartyMaster" data-js_store_fn="customStore" data-form_title="Add Customer">+ Customer</a>
                        
                    </div>
                </span>
                <select class="form-control single-select partyDetails partyOptions" name="party_id" id="party_id" data-res_function="resPartyDetail" data-party_category="1">
                    <option value="">Select Customer</option>
                    <?=getPartyListOption($customerList,((!empty($dataRow->party_id))?$dataRow->party_id:0))?>
                </select>
            </div>

            <div class="col-md-6 form-group">
                <label for="lead_date">Approch Date</label>
                <input type="date" name="lead_date" id="lead_date" max="<?=date("Y-m-d")?>" class="form-control req" value="<?=(!empty($dataRow->lead_date))?$dataRow->lead_date:date("Y-m-d")?>" />
            </div>

            <div class="col-md-4 form-group">
                <label for="mode">Mode</label>
                <select name="mode" id="mode" class="form-control req single-select">
                    <?php
                        foreach($this->appointmentMode as $key=>$row):
							$selected = (!empty($dataRow->mode) && $dataRow->mode == $key)?"selected":"";
                            echo '<option value="'.$key.'" '.$selected .'>'.$row.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

			<div class="col-md-4 form-group">
                <label for="sales_executive">Sales Executives</label>
                <select class="form-control single-select" name="sales_executive" id="sales_executive">
                    <option value="">Select Sales Executive</option>
                    <?php
                    if(!empty($salesExecutives)){
                        foreach($salesExecutives as $row){
                            $selected = (!empty($dataRow->sales_executive) && $dataRow->sales_executive == $row->id)?'selected':(($this->loginId == $row->id)?'selected':'');
                            $disabled = (in_array($this->userRole,[-1,1]) || $row->id == $this->loginId)?:'disabled';
                            echo '<option value="'.$row->id.'" '.$selected.' '.$disabled.'>'.$row->emp_name.' </option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="status">Stage</label>
                <select class="form-control " name="status" id="status">
                    <?php
                        foreach($this->followupStage as $key=>$row):
							$selected = (!empty($dataRow->status) and $dataRow->status == $row)?"selected":"";
                            echo '<option value="'.$key.'" '.$selected .'>'.$row.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-12 form-group">
                <label for="notes">Notes</label>
                <div class="input-group">
                    <textarea name="notes" id="notes" class="form-control"><?=(!empty($dataRow->notes))?$dataRow->notes:""?></textarea>
                </div>
            </div>

        </div>
    </div>    
</form>
<script>
function resPartyDetail(response = ""){
    var html = '<option value="">Select GST No.</option>';
    if(response != ""){
        var partyDetail = response.data.partyDetail;
    }else{
        $("#party_name").val("");
    }
}
</script>