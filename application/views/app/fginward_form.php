<form>
    <div class="action-sheet-content">
        <input type="hidden" name="id" id="id" value="">
        <input type="hidden" name="p_or_m" id="p_or_m" value="1">
        
        <div class="form-group  boxed animated">
            <label for="ref_date">Date</label>
            <input type="date" name="ref_date" id="ref_date" class="form-control" value="<?=getFyDate()?>">
        </div>

        <div class="form-group  boxed animated">
            <label for="item_id">Item Name</label>
            <select name="item_id" id="item_id" class="form-control select2 itemDetails" data-res_function="resItemDetail">
                <option value="">Select Item</option>
                <?=getItemListOption($itemList)?>
            </select>
        </div>

        <div class="form-group  boxed animated">
            <label for="unique_id">Brand</label>
            <select name="unique_id" id="unique_id" class="form-control select2">
                <option value="">Select Brand</option>
                <?=getBrandListOption($brandList)?>
            </select>
            <input type="hidden" name="batch_no" id="batch_no" value="">
        </div>

        <div class="form-group  boxed animated">
            <label for="qty">Qty</label>
            <input type="number" name="qty" id="qty" class="form-control floatOnly" value="">
        </div>

        <div class="form-group  boxed animated">
            <label for="size">Packing Standard</label>
            <input type="text" name="size" id="size" class="form-control numericOnly" value="" readonly />
        </div>

        <div class="form-group  boxed animated">
            <label for="remark">Remark</label>
            <input type="text" name="remark" id="remark" class="form-control numericOnly" value="">
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
    $('.select2').each(function() { 
        $(this).select2({ dropdownParent: $(this).parent()});
    });
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