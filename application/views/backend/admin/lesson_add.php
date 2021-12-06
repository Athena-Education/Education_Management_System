<?php
// $param2 IS FOR COURSE ID AND $param3 IS FOR LESSON TYPE
$course_details = $this->crud_model->get_course_by_id($param2)->row_array();
$sections = $this->crud_model->get_section('course', $param2)->result_array();
?>
<!-- SHOWING THE LESSON TYPE IN AN ALERT VIEW -->
<div class="alert alert-info" role="alert">
    <?php echo get_phrase("lesson_type"); ?> :
    <strong>
        <?php
        if ($param3 == 'html5') {
            echo get_phrase("video_url").' [.mp4]';
        }elseif ($param3 == 'video') {
            echo get_phrase("video_file");
        }elseif ($param3 == 'youtube' || $param3 == 'vimeo') {
            echo get_phrase($param3).' '.get_phrase("video");
        }else{
            echo get_phrase($param3);
        }
        ?>.
    </strong>

    <strong><a href="#" class="ml-1" data-toggle="modal" data-dismiss="modal" onclick="showAjaxModal('<?php echo site_url('modal/popup/lesson_types/'.$param2.'/'.$param3); ?>', '<?php echo get_phrase('add_new_lesson'); ?>')"><?php echo get_phrase("change"); ?></a></strong>
</div>

<!-- ACTUAL LESSON ADDING FORM -->
<form action="<?php echo site_url('admin/lessons/'.$param2.'/add'); ?>" method="post" enctype="multipart/form-data">
    <input type="hidden" name="course_id" value="<?php echo $param2; ?>">
    <div class="form-group">
        <label><?php echo get_phrase('title'); ?></label>
        <input type="text" name = "title" class="form-control" required>
    </div>

    <div class="form-group">
        <label><?php echo get_phrase('section'); ?></label>
        <select class="form-control select2" data-toggle="select2" name="section_id" required>
            <?php foreach ($sections as $section): ?>
                <option value="<?php echo $section['id']; ?>"><?php echo $section['title']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <?php if ($param3 == 'youtube'): include('youtube_type_lesson_add.php'); endif; ?>
    <?php if ($param3 == 'vimeo'): include('vimeo_type_lesson_add.php'); endif; ?>
    <?php if ($param3 == 'html5'): include('html5_type_lesson_add.php'); endif; ?>
    <?php if ($param3 == 'video'): include('video_type_lesson_add.php'); endif; ?>
    <?php if ($param3 == 'amazon-s3' && addon_status('amazon-s3')): include('amazon_s3_type_lesson_add.php'); endif; ?>
    <?php if ($param3 == 'document'): include('document_type_lesson_add.php'); endif; ?>
    <?php if ($param3 == 'image'): include('image_type_lesson_add.php'); endif; ?>
    <?php if ($param3 == 'iframe'): include('iframe_type_lesson_add.php'); endif; ?>

    <div class="form-group">
        <label><?php echo get_phrase('summary'); ?></label>
        <textarea name="summary" class="form-control"></textarea>
    </div>

    <div class="text-center">
        <button class = "btn btn-success" type="submit" name="button"><?php echo get_phrase('add_lesson'); ?></button>
    </div>
</form>

<script type="text/javascript">
$(document).ready(function() {
    initSelect2(['#section_id','#lesson_type', '#lesson_provider', '#lesson_provider_for_mobile_application']);
    initTimepicker();

    // HIDING THE SEARCHBOX FROM SELECT2
    $('select').select2({
        minimumResultsForSearch: -1
    });
});
function ajax_get_video_details(video_url) {
    $('#perloader').show();
    if(checkURLValidity(video_url)){
        $.ajax({
            url: '<?php echo site_url('admin/ajax_get_video_details');?>',
            type : 'POST',
            data : {video_url : video_url},
            success: function(response)
            {
                jQuery('#duration').val(response);
                $('#perloader').hide();
                $('#invalid_url').hide();
            }
        });
    }else {
        $('#invalid_url').show();
        $('#perloader').hide();
        jQuery('#duration').val('');

    }
}

function checkURLValidity(video_url) {
    var youtubePregMatch = /^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
    var vimeoPregMatch = /^(http\:\/\/|https\:\/\/)?(www\.)?(vimeo\.com\/)([0-9]+)$/;
    if (video_url.match(youtubePregMatch)) {
        return true;
    }
    else if (vimeoPregMatch.test(video_url)) {
        return true;
    }
    else {
        return false;
    }
}
</script>
