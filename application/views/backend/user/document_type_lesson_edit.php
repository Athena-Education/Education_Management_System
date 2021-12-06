<div class="form-group">
    <label for="document_type"><?php echo get_phrase('document_type'); ?></label>
    <select class="form-control select2" data-toggle="select2" name="lesson_type" id="lesson_type" required>
        <option value=""><?php echo get_phrase('select_type_of_document'); ?></option>
        <option value="other-txt"    <?php if($lesson_details['attachment_type'] == 'txt')    echo 'selected'; ?>><?php echo get_phrase('text_file'); ?></option>
        <option value="other-pdf"    <?php if($lesson_details['attachment_type'] == 'pdf')    echo 'selected'; ?>><?php echo get_phrase('pdf_file'); ?></option>
        <option value="other-doc"    <?php if($lesson_details['attachment_type'] == 'doc')    echo 'selected'; ?>><?php echo get_phrase('document_file'); ?></option>
    </select>
</div>

<div class="form-group">
    <label> <?php echo get_phrase('attachment'); ?></label>
    <div class="input-group">
        <div class="custom-file">
            <input type="file" class="custom-file-input" id="attachment" name="attachment" onchange="changeTitleOfImageUploader(this)">
            <label class="custom-file-label" for="attachment"><?php echo get_phrase('attachment'); ?></label>
        </div>
    </div>
</div>
