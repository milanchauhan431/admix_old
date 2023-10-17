<form data-res_function="resAppointments">
    <div class="action-sheet-content">  
        <input type="hidden" name="id" id="id" value="">
        <input type="hidden" name="lead_id" id="lead_id" value="<?=$lead_id?>">
        <input type="hidden" name="entry_type" id="entry_type" value="2">
        <div class=" form-group  boxed animated">
            <label for="appointment_date">Appintment Date</label>
            <input type="date" name="appointment_date" id="appointment_date" min="<?=date("Y-m-d")?>" class="form-control req" value="<?=(!empty($dataRow->appointment_date))?$dataRow->appointment_date:date("Y-m-d")?>" />
        </div>
        <div class=" form-group  boxed animated">
            <label for="appointment_time">Appintment Time</label>
            <input type="time" name="appointment_time" id="appointment_time" min="<?=date("Y-m-d H:i:s")?>" class="form-control req" value="<?=(!empty($dataRow->appointment_time))?date("h:i:s",strtotime($dataRow->appointment_time)):date("h:i:s")?>" />
        </div>
        <div class="form-group  boxed animated">
                <label for="mode">Mode</label>
                <select name="mode" id="mode" class="form-control req select2">
                    <?php
                       foreach($appointmentMode as $key=>$row):
                           $selected = (!empty($dataRow->mode) and $dataRow->mode == $row)?"selected":"";
                           echo '<option value="'.$key.'" '.$selected .'>'.$row.'</option>';
                       endforeach;
                   ?>
                    ?>
                </select>
        </div>
        <div class="form-group  boxed animated">
            <label for="contact_person">Contact Person</label>
            <input type="text" name="contact_person" id="contact_person" class="form-control text-capitalize req" value="<?=(!empty($dataRow->contact_person))?$dataRow->contact_person:""?>" />
        </div>
        <div class="form-group  boxed animated">
            <label for="purpose">Purpose</label>
            <div class="input-group">
                <textarea name="purpose" id="purpose" class="form-control"><?=(!empty($dataRow->purpose))?$dataRow->purpose:""?></textarea>
            </div>
            <div class="error purpose"></div>
        </div>
        <div class="form-group  boxed animated">
            <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save float-right mt-30" onclick="customStore({'formId':'appointment','fnsave':'saveAppointment','controller':'lead'});"> <i class="fa fa-check"></i> Save</button>
        </div>
        <hr>
        <style>#appointmentTable td,#appointmentTable th{font-size:0.8rem;}</style>
        <div class="form-group  boxed animated">
            <label for="">Appointments : </label>
            <div class="table-responsive">
                <table id='appointmentTable' class="table table-bordered">
                    <thead class="thead-info">
                        <tr>
                            <th style="width:10px;">#</th>
                            <th style="width:20px;">Appointment Schedule</th>
                            <th style="width:30px;">Mode</th>
                            <th style="width:50px;">Appointment With</th>
                            <th style="width:120px;">Notes</th>
                            <th style="width:20px;">Action</th>
                        </tr>                            
                    </thead>
                    <tbody id="appointmentData">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
</form>


<script>
    
$(document).ready(function(){
    $('.select2').each(function() { 
        $(this).select2({ dropdownParent: $(this).parent()});
    });
    var appointmentTrans = {'postData':{'lead_id':$("#lead_id").val(),'entry_type':$("#entry_type").val()},'table_id':"appointmentTable",'tbody_id':'appointmentData','tfoot_id':'','fnget':'appointmentListHtml'};
    getTransHtml(appointmentTrans);
});

function resAppointments(data){
    if(data.status==1){
        $("#DialogIconedSuccess .modal-body").html(data.message);
        $("#DialogIconedSuccess").modal('show');

        $("#contact_person").val("");
        $("#purpose").val("");

        var appointmentTrans = {'postData':{'lead_id':$("#lead_id").val(),'entry_type':$("#entry_type").val()},'table_id':"appointmentTable",'tbody_id':'appointmentData','tfoot_id':'','fnget':'appointmentListHtml'};
        getTransHtml(appointmentTrans);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) { $("."+key).html(value); });
        }else{
            $("#DialogIconedDanger .modal-body").html(data.message);
            $("#DialogIconedDanger").modal('show');
        }			
    }
}

function resTrashAppointment(data){
    if(data.status==1){
        $("#DialogIconedSuccess .modal-body").html(data.message);
        $("#DialogIconedSuccess").modal('show');

        var appointmentTrans = {'postData':{'lead_id':$("#lead_id").val(),'entry_type':$("#entry_type").val()},'table_id':"appointmentTable",'tbody_id':'appointmentData','tfoot_id':'','fnget':'appointmentListHtml'};
        getTransHtml(appointmentTrans);
    }else{
        if(typeof data.message === "object"){
            $(".error").html("");
            $.each( data.message, function( key, value ) { $("."+key).html(value); });
        }else{
            $("#DialogIconedDanger .modal-body").html(data.message);
            $("#DialogIconedDanger").modal('show');
        }			
    }
}
</script>