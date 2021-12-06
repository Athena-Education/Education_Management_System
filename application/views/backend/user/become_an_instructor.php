<?php
    $applications = $this->user_model->get_applications($this->session->userdata('user_id'), 'user');
 ?>
<div class="row ">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="page-title"> <i class="mdi mdi-apple-keyboard-command title_icon"></i> <?php echo get_phrase('become_an_instructor'); ?></h4>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<?php if ($this->session->userdata('is_instructor') != 1): ?>
    <div class="row justify-content-center">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <?php if ($applications->num_rows() == 0): ?>
                        <?php include 'application_form.php'; ?>
                    <?php else: ?>
                        <?php include 'application_list.php'; ?>
                    <?php endif; ?>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-info" role="alert">
        <h4 class="alert-heading"><?php echo get_phrase('congratulations'); ?>!</h4>
        <p><?php echo get_phrase('you_are_already_an_instructor'); ?></p>
    </div>
<?php endif; ?>


<style media="screen">
body {
    overflow-x: hidden;
}
</style>
