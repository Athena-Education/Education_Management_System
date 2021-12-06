<?php
$course_details = $this->crud_model->get_course_by_id($course_id)->row_array();

if(isset($bundle_id) && $bundle_id > 0):
    $my_course_url = strtolower($this->session->userdata('role')) == 'user' ? site_url('home/my_bundles') : 'javascript::';
    $btn_title = 'my_bundles';
else:
    $my_course_url = strtolower($this->session->userdata('role')) == 'user' ? site_url('home/my_courses') : 'javascript::';
    $btn_title = 'my_courses';
endif;
$course_details_url = site_url("home/course/".slugify($course_details['title'])."/".$course_id);
?>
<div class="container-fluid course_container">
    <!-- Top bar -->
    <div class="row">
        <div class="col-md-12 col-lg-7 col-xl-9 course_header_col">
            <h5>
                <img src="<?php echo base_url('uploads/system/').get_frontend_settings('small_logo');?>" height="25"> |
                <?php echo $course_details['title']; ?>
            </h5>
        </div>
        <div class="col-md-12 col-lg-5 col-xl-3 course_header_col text-right">
            <a href="javascript::" class="course_btn" onclick="toggle_lesson_view()"><i class="fa fa-arrows-alt-h"></i></a>
            <a href="<?php echo $my_course_url; ?>" class="course_btn"> <i class="fa fa-chevron-left"></i> <?php echo get_phrase($btn_title); ?></a>
            <a href="<?php echo $course_details_url; ?>" class="course_btn"><?php echo get_phrase('course_details'); ?> <i class="fa fa-chevron-right"></i></a>
        </div>
    </div>

    <div class="row" id = "lesson-container">
        <?php if (isset($lesson_id) == true || isset($scorm_curriculum) == true): ?>
            <!-- Course content, video, quizes, files starts-->
            <?php include $course_details['course_type'].'_course_content_body.php'; ?>
            <!-- Course content, video, quizes, files ends-->
        <?php endif; ?>

        <!-- Course sections and lesson selector sidebar starts-->
            <?php if($course_details['course_type'] == 'general'): ?>
                <?php include 'course_content_sidebar.php'; ?>
            <?php endif; ?>
        <!-- Course sections and lesson selector sidebar ends-->
    </div>

    
    <div class="row my-4">
        <div class="col-lg-9">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <ul class="nav nav-tabs <?php if(!addon_status('forum') && !addon_status('noticeboard')) echo 'border-0'; ?>">
                        <?php if(addon_status('forum')): ?>
                            <li class="nav-item">
                                <a class="nav-link remove-active active" id="qAndA" onclick="load_questions('<?= $course_id; ?>')" href="javascript:;"><?= site_phrase('forum'); ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if(addon_status('noticeboard')): ?>
                            <li class="nav-item">
                                <a class="nav-link remove-active <?php if(!addon_status('forum')) echo 'active'; ?>" id="noticeboard_tab" onclick="load_course_notices('<?= $course_id; ?>')" href="javascript:;"><?= site_phrase('noticeboard'); ?></a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <!--load body with ajax for any addon. First load course forum addon if exits or elseif-->
                <div class="col-md-12 p-4" id="load-tabs-body">
                    <?php if(addon_status('forum')): ?>
                        <?php include 'course_forum.php'; ?>
                    <?php elseif(addon_status('noticeboard')): ?>
                        <?php include 'noticeboard_body.php'; ?>
                    <?php endif; ?>
                </div>
                <?php if(addon_status('forum')): ?>
                    <?php include 'course_forum_scripts.php'; ?>
                <?php endif; ?>
                <?php if(addon_status('noticeboard')): ?>
                    <?php include 'noticeboard_scripts.php'; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
