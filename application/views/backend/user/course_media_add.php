<?php
  $course_media_files = themeConfiguration(get_frontend_settings('theme'), 'course_media_files');
  $course_media_placeholders = themeConfiguration(get_frontend_settings('theme'), 'course_media_placeholders');
  foreach ($course_media_files as $course_media => $size): ?>
  <div class="col-xl-8">
    <div class="form-group row mb-3">
      <label class="col-md-2 col-form-label" for="<?php echo $course_media.'_label' ?>"><?php echo get_phrase($course_media); ?></label>
      <div class="col-md-10">
        <div class="wrapper-image-preview" style="margin-left: -6px;">
          <div class="box" style="width: 250px;">
            <div class="js--image-preview" style="background-image: url(<?php echo base_url().$course_media_placeholders[$course_media.'_placeholder']; ?>); background-color: #F5F5F5;"></div>
            <div class="upload-options">
              <label for="<?php echo $course_media; ?>" class="btn"> <i class="mdi mdi-camera"></i> <?php echo get_phrase($course_media); ?> <br> <small>(<?php echo $size; ?>)</small> </label>
              <input id="<?php echo $course_media; ?>" style="visibility:hidden;" type="file" class="image-upload" name="<?php echo $course_media; ?>" accept="image/*">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php endforeach; ?>
