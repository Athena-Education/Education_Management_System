<form class="required-form ajaxForm" action="<?php echo site_url('admin/shortcut_enrol_student'); ?>" method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label for="user_id"><?php echo get_phrase('user'); ?><span class="required">*</span> </label>
        <select class="form-control select2" data-toggle="select2" name="user_id" id="user_id" required>
            <option value=""><?php echo get_phrase('select_a_user'); ?></option>
            <?php $user_list = $this->user_model->get_user()->result_array();
                foreach ($user_list as $user):?>
                <option value="<?php echo $user['id'] ?>"><?php echo $user['first_name'].' '.$user['last_name']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="course_id"><?php echo get_phrase('course_to_enrol'); ?><span class="required">*</span> </label>
        <select class="form-control select2" data-toggle="select2" name="course_id" id="course_id" required>
            <option value=""><?php echo get_phrase('select_a_course'); ?></option>
            <?php $course_list = $this->crud_model->get_courses()->result_array();
                foreach ($course_list as $course):
                if ($course['status'] != 'active')
                    continue;?>
                <option value="<?php echo $course['id'] ?>"><?php echo $course['title']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <button type="button" class="btn btn-primary float-right" onclick="checkRequiredFields()"><?php echo get_phrase('enrol_student'); ?></button>
</form>

<script type="text/javascript">
    if($('select').hasClass('select2') == true){
        $('div').attr('tabindex', "");
        $(function(){$(".select2").select2()});
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