<form>
    <div class="action-sheet-content">
        <input type="hidden" name="id" id="id" value="<?=$lead_id?>">
        <input type="hidden" name="entry_type" id="entry_type" value="<?=$entry_type?>">

        <div class="form-group  boxed animated">
            <label for="lead_status">Status</label>
            <select name="lead_status" id="lead_status" class="form-control">
                <option value="">Select Status</option>
                <option value="3">Won/Approve</option>
                <option value="4">Lost/Close</option>
            </select>
        </div>

        <div class="form-group  boxed animated">
            <label for="reason">Notes</label>
            <textarea name="reason" id="reason" class="form-control"></textarea>
            <div class="error reason"></div>
        </div>
       
    </div>
</form>