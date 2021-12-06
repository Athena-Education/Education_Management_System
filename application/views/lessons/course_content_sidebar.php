<div class="col-lg-3 mt-5 order-md-2 course_col hidden" id="lesson_list_loader" style="text-align: center;">
    <img src="<?php echo base_url('assets/backend/images/loader.gif'); ?>" alt="" height="50" width="50">
</div>
<div class="col-lg-3  order-md-2 course_col" id = "lesson_list_area">
    <div class="text-center" style="margin: 12px 10px;">
        <h5><?php echo get_phrase('course_content'); ?></h5>
    </div>
    <div class="row" style="margin: 12px -1px">
        <div class="col-12">
            <ul class="nav nav-tabs" id="lessonTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="section_and_lessons-tab" data-toggle="tab" href="#section_and_lessons" role="tab" aria-controls="section_and_lessons" aria-selected="true"><?php echo get_phrase('Lessons') ?></a>
                </li>
                <!-- ZOOM LIVE CLASS TAB STARTS -->
                <?php if (addon_status('live-class')): ?>
                    <li class="nav-item">
                        <a class="nav-link" id="liveclass-tab" data-toggle="tab" href="#liveclass" role="tab" aria-controls="liveclass" aria-selected="false">
                            <?php echo get_phrase('live_class'); ?>
                        </a>
                    </li>
                <?php endif; ?>
                <!-- ZOOM LIVE CLASS TAB ENDS -->

                <!-- CERTIFICATE TAB -->
                <?php if (addon_status('certificate')): ?>
                    <li class="nav-item">
                        <a class="nav-link" id="certificate-tab" data-toggle="tab" href="#certificate" role="tab" aria-controls="certificate" aria-selected="false" onclick="checkCertificateEligibility()"><?php echo get_phrase('certificate'); ?></a>
                    </li>
                <?php endif; ?>
                <!-- CERTIFICATE TAB -->
            </ul>
            <div class="tab-content" id="lessonTabContent">
                <div class="tab-pane fade show active" id="section_and_lessons" role="tabpanel" aria-labelledby="section_and_lessons-tab">
                    <!-- Lesson Content starts from here -->
                    <div class="accordion" id="accordionExample">
                        <?php
                        foreach ($sections as $key => $section):
                            $lessons = $this->crud_model->get_lessons('section', $section['id'])->result_array();?>
                            <div class="card" style="margin:0px 0px;">
                                <div class="card-header course_card" id="<?php echo 'heading-'.$section['id']; ?>">

                                    <h5 class="mb-0">
                                        <button class="btn btn-link w-100 text-left" type="button" data-toggle="collapse" data-target="<?php echo '#collapse-'.$section['id']; ?>" <?php if($opened_section_id == $section['id']): ?> aria-expanded="true" <?php else: ?> aria-expanded="false" <?php endif; ?> aria-controls="<?php echo 'collapse-'.$section['id']; ?>" style="color: #535a66; background: none; border: none; white-space: normal;" onclick = "toggleAccordionIcon(this, '<?php echo $section['id']; ?>')">
                                            <h6 style="color: #959aa2; font-size: 13px;">
                                                <?php echo get_phrase('section').' '.($key+1);?>
                                                <span style="float: right; font-weight: 100;" class="accordion_icon" id="accordion_icon_<?php echo $section['id']; ?>">
                                                    <?php if($opened_section_id == $section['id']): ?>
                                                        <i class="fa fa-minus"></i>
                                                    <?php else: ?>
                                                        <i class="fa fa-plus"></i>
                                                    <?php endif; ?>
                                                </span>
                                            </h6>
                                            <?php echo $section['title']; ?>
                                        </button>
                                    </h5>
                                </div>

                                <div id="<?php echo 'collapse-'.$section['id']; ?>" class="collapse <?php if($section_id == $section['id']) echo 'show'; ?>" aria-labelledby="<?php echo 'heading-'.$section['id']; ?>" data-parent="#accordionExample">
                                    <div class="card-body"  style="padding:0px;">
                                        <table style="width: 100%;">
                                            <?php foreach ($lessons as $key => $lesson): ?>
                                                <?php
                                                if(isset($bundle_id) && $bundle_id > 0):
                                                    $lesson_url = site_url('addons/course_bundles/lesson/'.rawurlencode(slugify($course_details['title'])).'/'.$bundle_id.'/'.$course_id.'/'.$lesson['id']);
                                                else:
                                                    $lesson_url = site_url('home/lesson/'.slugify($course_details['title']).'/'.$course_id.'/'.$lesson['id']);
                                                endif;
                                                ?>

                                                <tr style="width: 100%; padding: 5px 0px;background-color: <?php if ($lesson_id == $lesson['id'])echo '#E6F2F5'; else echo '#fff';?>;">
                                                    <td style="text-align: left; padding:7px 10px;">
                                                        <?php
                                                        $lesson_progress = lesson_progress($lesson['id']);
                                                        ?>
                                                        <div class="form-group">
                                                            <input type="checkbox" id="<?php echo $lesson['id']; ?>" onchange="markThisLessonAsCompleted(this.id);" value = 1 <?php if($lesson_progress == 1):?> checked <?php endif; ?>>
                                                            <label for="<?php echo $lesson['id']; ?>"></label>
                                                        </div>

                                                        <a href="<?= $lesson_url; ?>" id = "<?php echo $lesson['id']; ?>" style="color: #444549;font-size: 14px;font-weight: 400;">
                                                            <?php echo $key+1; ?>:
                                                            <?php if ($lesson['lesson_type'] != 'other'):?>
                                                                <?php echo $lesson['title']; ?>
                                                            <?php else: ?>
                                                                <?php echo $lesson['title']; ?>
                                                                <!-- <i class="fa fa-paperclip"></i> -->
                                                            <?php endif; ?>
                                                        </a>

                                                        <div class="lesson_duration">
                                                            <?php if ($lesson['lesson_type'] == 'video' || $lesson['lesson_type'] == '' || $lesson['lesson_type'] == NULL): ?>
                                                                <?php //echo $lesson['duration']; ?>
                                                                <i class="far fa-play-circle"></i>
                                                                <?php echo readable_time_for_humans($lesson['duration']); ?>
                                                            <?php elseif($lesson['lesson_type'] == 'quiz'): ?>
                                                                <i class="far fa-question-circle"></i> <?php echo get_phrase('quiz'); ?>
                                                            <?php else: ?>
                                                                <?php if ($lesson['attachment_type'] == 'iframe'): ?>
                                                                    <i class="fas fa-code"></i>  <?php echo get_phrase('external_source'); ?>
                                                                <?php else: ?>
                                                                    <?php $tmp           = explode('.', $lesson['attachment']);
                                                                    $fileExtension = strtolower(end($tmp));?>
                                                                    <?php if ($fileExtension == 'jpg' || $fileExtension == 'jpeg' || $fileExtension == 'png' || $fileExtension == 'bmp' || $fileExtension == 'svg'): ?>
                                                                        <i class="fas fa-camera-retro"></i>  <?php echo get_phrase('attachment'); ?>
                                                                    <?php elseif($fileExtension == 'pdf'): ?>
                                                                        <i class="far fa-file-pdf"></i>  <?php echo get_phrase('attachment'); ?>
                                                                    <?php elseif($fileExtension == 'doc' || $fileExtension == 'docx'): ?>
                                                                        <i class="far fa-file-word"></i>  <?php echo get_phrase('attachment'); ?>
                                                                    <?php elseif($fileExtension == 'txt'): ?>
                                                                        <i class="far fa-file-alt"></i>  <?php echo get_phrase('attachment'); ?>
                                                                    <?php else: ?>
                                                                        <i class="fa fa-file"></i>  <?php echo get_phrase('attachment'); ?>
                                                                    <?php endif; ?>
                                                                <?php endif; ?>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- Lesson Content ends from here -->
                </div>

                <!-- ZOOM LIVE CLASS TAB STARTS-->
                <?php if (addon_status('live-class')): ?>
                    <div class="tab-pane fade" id="liveclass" role="tabpanel" aria-labelledby="liveclass-tab" style="text-align: center;">
                        <?php
                        $live_class = $this->db->get_where('live_class', array('course_id' => $course_id));
                        if ($live_class->num_rows() > 0):
                            $live_class = $this->db->get_where('live_class', array('course_id' => $course_id))->row_array(); ?>
                            <div style="padding: 30px 0px;">
                                <i class="fa fa-calendar-check"></i> <?php echo get_phrase('live_class_schedule'); ?>
                                <h5 style="margin-top: 20px;"><?php echo date('h:i A', $live_class['time']); ?> : <?php echo date('D, d M Y', $live_class['date']); ?></h5>
                                <div class="live_class_note">
                                    <?php echo $live_class['note_to_students']; ?>
                                </div>
                                <a href="<?php echo site_url('addons/liveclass/join/'.$course_id);?>" class="btn btn_zoom">
                                    <i class="fa fa-video"></i>&nbsp;
                                    <?php echo get_phrase('join_live_video_class'); ?>
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning" role="alert" style="padding: 30px 0px;">
                              <?php echo get_phrase('live_class_is_not_scheduled_to_this_course_yet'); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <style>
                    .live_class_note {
                        border: 1px solid #bfdde4;
                        background-color: aliceblue;
                        margin: 30px 0px 10px;
                        color: #007791;
                        font-size: 12px;
                        padding: 10px;
                    }
                    .btn_zoom {
                        background-color: #2781FF;
                        border-color: #2781FF;
                    }
                    .btn_zoom:hover {
                        background-color: #2781FF;
                        border-color: #2781FF;
                    }
                    .btn_zoom:focus {
                        background-color: #2781FF;
                        border-color: #2781FF;
                    }
                    </style>
                <?php endif; ?>
                <!-- ZOOM LIVE CLASS TAB ENDS-->

                <?php if (addon_status('certificate')): ?>
                    <div class="tab-pane fade" id="certificate" role="tabpanel" aria-labelledby="certificate-tab" style="text-align: center;">

                        <div class="circular-progressdiv" id="course_progress_area"  data-percent="<?php echo course_progress($course_id); ?>">
                            <svg class="circular-progress" viewport="0 0 100 100" version="1.1" xmlns="http://www.w3.org/2000/svg" style="height: 180; width: 180;">
                                <circle r="80" cx="89" cy="89" fill="transparent" stroke-dasharray="502.4" stroke-dashoffset="0" ></circle>
                                <circle class="bar" r="80" cx="89" cy="89" fill="transparent" stroke-dasharray="502.4" stroke-dashoffset="0"></circle>
                            </svg>
                        </div>

                        <div class="alert alert-info" id="certificate-alert-warning" role="alert">
                            <h4 class="alert-heading"><?php echo get_phrase('Notice'); ?></h4>
                            <hr>
                            <p> <?php echo get_phrase('you have completed'); ?> <span id="progression"></span>% <?php echo get_phrase('of_the_course'); ?> </p>
                            <p><?php echo get_phrase('you_can_download_the_course_completion_certificate_after_completing_the_course'); ?></p>
                        </div>

                        <div class="alert alert-success" id="certificate-alert-success" role="alert">
                            <h4 class="alert-heading"><?php echo get_phrase('well_done'); ?></h4>
                            <hr>
                            <p><?php echo get_phrase('congratulations').'!!!'; ?></p>
                            <p><?php echo get_phrase('you_are_now_eligible_to_download_the_course_completion_certificate'); ?>.</p>
                        </div>

                        <div id="download_certificate_area" style="padding: 15px;">
                            <?php if (addon_status('certificate')): ?>
                                <a href="#" target="_blank" class="btn btn-primary" id = "certificate_download_btn" disabled><?php echo get_phrase('get_certificate'); ?></a>
                            <?php else: ?>
                                <a href="#" target="" class="btn btn-primary"><?php echo get_phrase('install_certificate_addon_first'); ?></a>
                            <?php endif; ?>
                        </div>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

$(document).ready(function() {
    checkCertificateEligibility();
});

function toggleAccordionIcon(elem, section_id) {
    var accordion_section_ids = [];
    $(".accordion_icon").each(function(){ accordion_section_ids.push(this.id); });
    accordion_section_ids.forEach(function(item) {
        if (item === 'accordion_icon_'+section_id) {
            if ($('#'+item).html().trim() === '<i class="fa fa-plus"></i>') {
                $('#'+item).html('<i class="fa fa-minus"></i>')
            }else {
                $('#'+item).html('<i class="fa fa-plus"></i>')
            }
        }else{
            $('#'+item).html('<i class="fa fa-plus"></i>')
        }
    });
}

function checkCertificateEligibility() {
    $('#lesson_list_area').hide();
    $('#lesson_list_loader').show();
    $.ajax({
        url: '<?php echo site_url('addons/certificate/check_certificate_eligibility/'.$course_id);?>',
        success: function(response)
        {
            if (parseInt(response) === 1) {
                $('#download_certificate_area').show();
                $('#certificate-alert-success').show();
                $('#certificate-alert-warning').hide();

            }
            checkCourseProgression();
            getCertificateShareableUrl();

            $('#lesson_list_area').show();
            $('#lesson_list_loader').hide();
        }
    });
}

function checkCourseProgression() {
    $.ajax({
        url: '<?php echo site_url('home/check_course_progress/'.$course_id);?>',
        success: function(response)
        {
            if (parseInt(response) === 100) {
                $('#download_certificate_area').show();
                $('#certificate-alert-success').show();
                $('#certificate-alert-warning').hide();
            }else{
                $('#download_certificate_area').hide();
                $('#certificate-alert-success').hide();
                $('#certificate-alert-warning').show();
            }
            $('#progression').text(Math.round(response));
            $('#course_progress_area').attr('data-percent', Math.round(response));
            initProgressBar(Math.round(response));
        }
    });
}

function initProgressBar(dataPercent) {
    console.log("Data Percent" + dataPercent);
    var totalProgress, progress;
    const circles = document.querySelectorAll('.circular-progress');
    for(var i = 0; i < circles.length; i++) {
        totalProgress = circles[i].querySelector('circle').getAttribute('stroke-dasharray');
        //progress = circles[i].parentElement.getAttribute('data-percent');
        progress = dataPercent;

        circles[i].querySelector('.bar').style['stroke-dashoffset'] = totalProgress * progress / 100;
    }
}

function getCertificateShareableUrl() {
    var user_id = '<?php echo $this->session->userdata('user_id'); ?>';
    var course_id = '<?php echo $course_id; ?>';
    $.ajax({
        url: '<?php echo site_url('addons/certificate/get_certificate_url');?>',
        type : 'POST',
        data : {user_id : user_id, course_id : course_id},
        success: function(response)
        {
            $('#certificate_download_btn').attr('href', response);
        }
    });
}

function sendCourseCompletionMail() {
    var user_id = '<?php echo $this->session->userdata('user_id'); ?>';
    var course_id = '<?php echo $course_id; ?>';
    $.ajax({
        url: '<?php echo site_url('addons/certificate/send_course_completion_mail');?>',
        type : 'POST',
        data : {user_id : user_id, course_id : course_id},
        success: function(response)
        {
            console.log(response);
        }
    });
}
</script>
