<?php $social_links = json_decode($user_details['social_links'], true); ?>
<?php include "profile_menus.php"; ?>

<section class="user-dashboard-area">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="user-dashboard-box">
                    <div class="user-dashboard-sidebar">
                        <div class="user-box">
                            <img src="<?php echo $this->user_model->get_user_image_url($this->session->userdata('user_id')); ?>" alt="" class="img-fluid">
                            <div class="name">
                                <div class="name"><?php echo $user_details['first_name'].' '.$user_details['last_name']; ?></div>
                            </div>
                        </div>
                        <div class="user-dashboard-menu">
                            <ul>
                                <li class="active"><a href="<?php echo site_url('home/profile/user_profile'); ?>"><?php echo site_phrase('profile'); ?></a></li>
                                <li><a href="<?php echo site_url('home/profile/user_credentials'); ?>"><?php echo site_phrase('account'); ?></a></li>
                                <li><a href="<?php echo site_url('home/profile/user_photo'); ?>"><?php echo site_phrase('photo'); ?></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="user-dashboard-content">
                        <div class="content-title-box">
                            <div class="title"><?php echo site_phrase('profile'); ?></div>
                            <div class="subtitle"><?php echo site_phrase('add_information_about_yourself_to_share_on_your_profile'); ?>.</div>
                        </div>
                        <form action="<?php echo site_url('home/update_profile/update_basics'); ?>" method="post">
                            <div class="content-box">
                                <div class="basic-group">
                                    <div class="form-group">
                                        <label for="FristName"><?php echo site_phrase('basics'); ?>:</label>
                                        <input type="text" class="form-control" name = "first_name" id="FristName" placeholder="<?php echo site_phrase('first_name'); ?>" value="<?php echo $user_details['first_name']; ?>">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name = "last_name" placeholder="<?php echo site_phrase('last_name'); ?>" value="<?php echo $user_details['last_name']; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="Biography"><?php echo site_phrase('biography'); ?>:</label>
                                        <textarea class="form-control author-biography-editor" name = "biography" id="Biography"><?php echo $user_details['biography']; ?></textarea>
                                    </div>
                                </div>
                                <div class="link-group">
                                    <div class="form-group">
                                        <input type="text" class="form-control" maxlength="60" name = "twitter_link" placeholder="<?php echo site_phrase('twitter_link'); ?>" value="<?php echo $social_links['twitter']; ?>">
                                        <small class="form-text text-muted"><?php echo site_phrase('add_your_twitter_link'); ?>.</small>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" maxlength="60" name = "facebook_link" placeholder="<?php echo site_phrase('facebook_link'); ?>" value="<?php echo $social_links['facebook']; ?>">
                                        <small class="form-text text-muted"><?php echo site_phrase('add_your_facebook_link'); ?>.</small>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" maxlength="60" name = "linkedin_link" placeholder="<?php echo site_phrase('linkedin_link'); ?>" value="<?php echo $social_links['linkedin']; ?>">
                                        <small class="form-text text-muted"><?php echo site_phrase('add_your_linkedin_link'); ?>.</small>
                                    </div>
                                </div>
                            </div>
                            <div class="content-update-box">
                                <button type="submit" class="btn"><?= site_phrase('save'); ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
