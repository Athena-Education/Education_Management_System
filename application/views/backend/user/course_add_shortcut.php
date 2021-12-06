<div class="row">
	<div class="col-md-12">
		<form class="ajaxForm" action="<?= site_url('user/course_actions/add_shortcut'); ?>">
			<?php if(addon_status('scorm_course')): ?>
          <div class="form-group">
              <label for="course_type"><?php echo get_phrase('course_type'); ?></label>
              <select class="form-control select2" data-toggle="select2" name="course_type" id="course_type">
                  <option value="general"><?php echo get_phrase('general'); ?></option>
                  <option value="scorm"><?php echo get_phrase('scorm'); ?></option>
              </select>
          </div>
      <?php else: ?>
          <input type="hidden" name = "course_type" value="general">
      <?php endif; ?>

			<div class="form-group">
				<label><?php echo get_phrase('course_title'); ?> <span class="required">*</span></label>
				<input type="text" name="title" class="form-control" placeholder="<?php echo get_phrase('enter_course_title'); ?>">
			</div>

			<div class="form-group">
				<label><?php echo get_phrase('category'); ?> <span class="required">*</span></label>
				<select class="form-control select2" data-toggle="select2" name="sub_category_id" id="sub_category_id" required>
                    <option value=""><?php echo get_phrase('select_a_category'); ?></option>
                    <?php foreach ($categories->result_array() as $category): ?>
                        <optgroup label="<?php echo $category['name']; ?>">
                            <?php $sub_categories = $this->crud_model->get_sub_categories($category['id']);
                            foreach ($sub_categories as $sub_category): ?>
                            	<option value="<?php echo $sub_category['id']; ?>"><?php echo $sub_category['name']; ?></option>
                        	<?php endforeach; ?>
                    	</optgroup>
                	<?php endforeach; ?>
	            </select>
	            <small class="text-muted"><?php echo get_phrase('select_sub_category'); ?></small>
			</div>

			<div class="form-group">
                <label for="level"><?php echo get_phrase('level'); ?></label>
                <select class="form-control select2" data-toggle="select2" name="level" id="level">
                    <option value="beginner"><?php echo get_phrase('beginner'); ?></option>
                    <option value="advanced"><?php echo get_phrase('advanced'); ?></option>
                    <option value="intermediate"><?php echo get_phrase('intermediate'); ?></option>
                </select>
            </div>

			<div class="form-group">
                <label for="language_made_in"><?php echo get_phrase('language_made_in'); ?></label>
                <select class="form-control select2" data-toggle="select2" name="language_made_in" id="language_made_in">
                    <?php foreach ($languages as $language): ?>
                        <option value="<?php echo $language; ?>"><?php echo ucfirst($language); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>


            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" name="is_free_course" id="is_free_course_s" value="1" onclick="togglePriceFields(this.id)">
                    <label class="custom-control-label" for="is_free_course_s"><?php echo get_phrase('check_if_this_is_a_free_course'); ?></label>
                </div>
            </div>

            <div class="paid-course-stuffs">
                <div class="form-group">
                    <label for="price_s"><?php echo get_phrase('course_price').' ('.currency_code_and_symbol().')'; ?></label>
                    <input type="number" class="form-control" id="price_s" name = "price" placeholder="<?php echo get_phrase('enter_course_course_price'); ?>" min="0">
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="discount_flag" id="discount_flag_s" value="1">
                        <label class="custom-control-label" for="discount_flag_s"><?php echo get_phrase('check_if_this_course_has_discount'); ?></label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="" for="discounted_price"><?php echo get_phrase('discounted_price').' ('.currency_code_and_symbol().')'; ?></label>
                    <input type="number" class="form-control" name="discounted_price" id="discounted_price" onkeyup="calculateDiscountPercentage(this.value)" min="0">
                    <small class="text-muted"><?php echo get_phrase('this_course_has'); ?> <span id = "discounted_percentage_s" class="text-danger">0%</span> <?php echo get_phrase('discount'); ?></small>
                </div>
            </div>

            <div class="form-group">
                <button class="btn btn-primary float-right"><?php echo get_phrase('add_course'); ?></button>
            </div>
		</form>
	</div>
</div>

<script type="text/javascript">
	if($('select').hasClass('select2') == true){
        $('div').attr('tabindex', "");
        $(function(){$(".select2").select2()});
    }

	function priceChecked(elem){
  if (jQuery('#discountCheckbox').is(':checked')) {

    jQuery('#discountCheckbox').prop( "checked", false );
  }else {

    jQuery('#discountCheckbox').prop( "checked", true );
  }
}

function isFreeCourseChecked(elem) {

  if (jQuery('#'+elem.id).is(':checked')) {
    $('#price').prop('required',false);
  }else {
    $('#price').prop('required',true);
  }
}

function calculateDiscountPercentage(discounted_price) {
	
  if (discounted_price > 0) {
    var actualPrice = jQuery('#price_s').val();
    if ( actualPrice > 0) {
      var reducedPrice = actualPrice - discounted_price;
      var discountedPercentage = (reducedPrice / actualPrice) * 100;
      if (discountedPercentage > 0) {
        jQuery('#discounted_percentage_s').text(discountedPercentage.toFixed(2)+'%');

      }else {
        jQuery('#discounted_percentage_s').text('<?php echo '0%'; ?>');
      }
    }
  }
}


$(".ajaxForm").submit(function(e) {
    e.preventDefault(); // avoid to execute the actual submit of the form.
    var form = $(this);
    var url = form.attr('action');
    $.ajax({
       type: "POST",
       url: url,
       data: form.serialize(), // serializes the form's elements.
       success: function(response)
       {	
          var myArray = jQuery.parseJSON(response);
          if(myArray['status']){
            location.reload();
          }else{
            error_notify(myArray['message']);
          }
       }
    });
});
</script>