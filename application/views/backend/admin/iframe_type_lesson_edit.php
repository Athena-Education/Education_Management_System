<input type="hidden" name="lesson_type" value="other-iframe">

<div class="form-group">
    <label><?php echo get_phrase('iframe_source'); ?>( <?php echo get_phrase('provide_the_source_only'); ?> )</label>
    <input type="text" id = "iframe_source" name = "iframe_source" class="form-control" placeholder="<?php echo get_phrase('provide_the_source_only'); ?>" value="<?php echo $lesson_details['attachment']; ?>">
</div>
