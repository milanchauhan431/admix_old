<form>
    <div class="col-md-12">
        <div class="error item_error"></div>
        <div class="row">
            <input type="hidden" name="id" id="id" value="">
            <input type="hidden" name="p_or_m" id="p_or_m" value="1">
            
            

            <div class="col-md-12 form-group">
                <!-- <label for="item_id">Item Name</label>
                <select name="item_id" id="item_id" class="form-control select2 itemDetails" data-res_function="resItemDetail">
                    <option value="">Select Item</option>
                    <?=getItemListOption($itemList)?>
                </select> -->

                <div class="input-group">
                    <label for="size_id" style="width:33%;">Size</label>
                    <label for="color" style="width:33%;">Color</label>
                    <label for="capacity" style="width:33%;">Capacity</label>
                </div>
                <div class="input-group">                    
                    <div class="input-group-append" style="width:33%;">
                        <select name="size_id" id="size_id" class="form-control select2">
                            <option value="">Select Size</option>
                            <?php
                                foreach($sizeList as $row):
                                    $selected = (!empty($dataRow->size_id) && $dataRow->size_id == $row->id)?"selected":"";
                                    echo '<option value="'.$row->id.'" '.$selected.'>'.$row->size.'</option>';
                                endforeach;
                            ?>
                        </select>
                    </div>
                    
                    <div class="input-group-append" style="width:33%;">
                        <select name="color" id="color" class="form-control select2">
                            <?php
                                foreach($this->fgColorCode as $color=>$text):
                                    $selected = (!empty($dataRow->color) && $dataRow->color == $color)?"selected":"";
                                    echo '<option value="'.$color.'" '.$selected.'>'.$color.'</option>';
                                endforeach;
                            ?>
                        </select>
                    </div>

                    <div class="input-group-append" style="width:33%;">
                        <select name="capacity" id="capacity" class="form-control select2">
                            <?php
                                foreach($this->fgCapacity as $capacity=>$text):
                                    $selected = (!empty($dataRow->capacity) && $dataRow->capacity == $capacity)?"selected":"";
                                    echo '<option value="'.$capacity.'" '.$selected.'>'.$capacity.'</option>';
                                endforeach;
                            ?>
                        </select>
                    </div>
                </div>
                <div class="input-group">
                    <div class="error size_id_error" style="width:33%;"></div>
                    <div class="error color_error" style="width:33%;"></div>
                    <div class="error capacity_error" style="width:33%;"></div>
                </div>
            </div>

            <div class="col-md-4 form-group">
                <label for="ref_date">Date</label>
                <input type="date" name="ref_date" id="ref_date" class="form-control" value="<?=getFyDate()?>">
            </div>

            <div class="col-md-4 form-group">
                <label for="unique_id">Brand</label>
                <select name="unique_id" id="unique_id" class="form-control select2">
                    <option value="">Select Brand</option>
                    <?=getBrandListOption($brandList)?>
                </select>
                <input type="hidden" name="batch_no" id="batch_no" value="">
            </div>

            <div class="col-md-4 form-group">
                <label for="qty">Qty</label>
                <input type="text" name="qty" id="qty" class="form-control floatOnly" value="">
            </div>

            <!-- <div class="col-md-4 form-group">
                <label for="size">Pcs./Box</label>
                <input type="text" name="size" id="size" class="form-control numericOnly" value="" readonly />
            </div> -->

            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control numericOnly" value="">
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
    $(document).on('change','#unique_id',function(){
        if($(this).find(":selected").val() != ""){
            $("#batch_no").val($(this).find(":selected").text());
        }else{
            $("#batch_no").val("");
        }
    });
});
function resItemDetail(response){
    if(response != ""){
        var itemDetail = response.data.itemDetail;
        $("#size").val(itemDetail.packing_standard);
    }else{
        $("#size").val("");
    }
}
</script>