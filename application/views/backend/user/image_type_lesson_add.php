<input type="hidden" name="lesson_type" value="other-img">

<div class="form-group">
    <label> <?php echo get_phrase('attachment'); ?></label>
    <div class="input-group">
        <div class="custom-file">
            <input type="file" class="custom-file-input" id="attachment" name="attachment" onchange="changeTitleOfImageUploader(this)">
            <label class="custom-file-label" for="attachment"><?php echo get_phrase('attachment'); ?></label>
        </div>
    </div>
</div>
