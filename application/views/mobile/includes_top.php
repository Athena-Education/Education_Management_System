<link rel="shortcut icon" href="<?php echo base_url('uploads/system/'.get_frontend_settings('favicon'));?>">
<!-- third party css -->
<link href="<?php echo base_url('assets/backend/css/vendor/jquery-jvectormap-1.2.2.css');?>" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url('assets/backend/css/vendor/dataTables.bootstrap4.css');?>" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url('assets/backend/css/vendor/responsive.bootstrap4.css');?>" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url('assets/backend/css/vendor/buttons.bootstrap4.css');?>" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url('assets/backend/css/vendor/select.bootstrap4.css');?>" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url('assets/backend/css/vendor/summernote-bs4.css') ?>" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url('assets/backend/css/vendor/fullcalendar.min.css'); ?>" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url('assets/backend/css/vendor/dropzone.css'); ?>" rel="stylesheet" type="text/css" />
<!-- third party css end -->
<!-- App css -->
<link href="<?php echo base_url('assets/backend/css/app.min.css') ?>" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url('assets/backend/css/icons.min.css'); ?>" rel="stylesheet" type="text/css" />

<link href="<?php echo base_url('assets/backend/css/main.css') ?>" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url('assets/frontend/default/css/main.css') ?>" rel="stylesheet" type="text/css" />

<!-- font awesome 5 -->
<link href="<?php echo base_url('assets/backend/css/fontawesome-all.min.css') ?>" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url('assets/backend/css/font-awesome-icon-picker/fontawesome-iconpicker.min.css') ?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css">

<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> -->
<script src="<?php echo base_url('assets/backend/js/jquery-3.3.1.min.js'); ?>" charset="utf-8"></script>
<script src="<?php echo site_url('assets/backend/js/onDomChange.js');?>"></script>
<!--Stripe API-->
<script src="https://js.stripe.com/v3/"></script>

<!-- Lesson page specific styles are here -->
<style type="text/css">
body {
    background-color: #fff !important;
}
.card {
    border-radius: 0px !important;
    background-color: #f7f8fa !important;
    border:0px !important;
}
.course_card {
    padding: 0px;
    background-color: #F7F8FA;
}
.course_container {
    background-color: #fff !important;
}
.course_col {
    padding: 0px;
}
.course_header_col {
    background-color: #29303b;
    color: #fff;
    padding: 15px 10px 10px;
}
.course_header_col img {
    padding: 0px 0px;
}
.course_btn {
    color: #95979a;
    border: 1px solid #95979a;
    padding: 7px 10px;
}
.course_btn:hover {
    color: #fff;
    border:1px solid #fff;
}
.lesson_duration{
    border-radius: 5px;
    padding-top: 8px;
    color: #5C5D61;
    font-size: 13px;
    font-weight: 100;
}
.quiz-card {
    border: 1px solid #dcdddf !important;
}
.bg-quiz-result-info {
    background-color: #007791 !important;
    padding: 13px !important;
}
.shopping_card{
    -webkit-box-shadow: 0px 5px 28px -14px #000000;
    box-shadow: 0px 5px 28px -14px #000000;
}
</style>
