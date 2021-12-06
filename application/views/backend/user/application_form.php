<h4 class="header-title mb-3"><?php echo get_phrase('instructor_application_form'); ?></h4>
<div class="alert alert-info" role="alert">
    <h4 class="alert-heading"><?php echo get_phrase('heads_up'); ?>!</h4>
    <p><?php echo get_settings('instructor_application_note'); ?></p>
</div>
<form class="required-form" action="<?php echo site_url('user/become_an_instructor'); ?>" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $this->session->userdata('user_id'); ?>">
    <div class="form-group">
        <label for="name"><?php echo get_phrase('name'); ?></label>
        <input type="text" class="form-control" name="name" id="name" aria-describedby="name-help" placeholder="<?php echo get_phrase('your_name_will_go_here'); ?>" value="<?php echo $user_details['first_name'].' '.$user_details['last_name']; ?>" readonly required>
        <small id="name-help" class="form-text text-muted"><?php echo get_phrase('your_name_is_required'); ?></small>
    </div>
    <div class="form-group">
        <label for="email"><?php echo get_phrase('email_address'); ?></label>
        <input type="email" class="form-control" name="email" id="email" aria-describedby="email-help" placeholder="<?php echo get_phrase('your_email_will_go_here'); ?>" value="<?php echo $user_details['email']; ?>" readonly required>
        <small id="email-help" class="form-text text-muted"><?php echo get_phrase('your_email_is_required'); ?></small>
    </div>
    <div class="form-group">
        <label for="address"><?php echo get_phrase('address'); ?></label>
        <textarea name="address" id = "address" class="form-control" required></textarea>
        <small id="address-help" class="form-text text-muted"><?php echo get_phrase('your_address_is_required'); ?></small>
    </div>
    <div class="form-group">
        <label for="phone"><?php echo get_phrase('phone_number'); ?></label>
        <input type="text" class="form-control" name="phone" id="phone" aria-describedby="phone-help" placeholder="<?php echo get_phrase('your_phone_number_will_go_here'); ?>" required>
        <small id="phone-help" class="form-text text-muted"><?php echo get_phrase('your_phone_number_is_required'); ?></small>
    </div>
    <div class="form-group">
        <label for="message"><?php echo get_phrase('any_message'); ?></label>
        <textarea name="message" id = "message" class="form-control"></textarea>
        <small id="message-help" class="form-text text-muted"><?php echo get_phrase('if_any_message_you_want_to_share'); ?></small>
    </div>
    <div class="form-group">
        <label> <?php echo get_phrase('document'); ?></label>
        <div class="input-group">
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="document" name="document" onchange="changeTitleOfImageUploader(this)">
                <label class="custom-file-label" for="document"><?php echo get_phrase('document'); ?></label>
            </div>
        </div>
        <small id="attachment-help" class="form-text text-muted"><?php echo get_phrase('if_any_document_you_want_to_share'); ?> ( .doc, .docs, .pdf, .txt, .png, .jpg, jpeg ) <?php echo get_phrase('are_accepted'); ?></small>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="text-center">
                <div class="mb-3 mt-3">
                    <button type="button" class="btn btn-primary text-center" onclick="checkRequiredFields()"><?php echo get_phrase('apply'); ?></button>
                </div>
            </div>
        </div> <!-- end col -->
    </div>
</form>
