<?php
$curl_enabled = function_exists('curl_version');
$installed_themes = $this->crud_model->get_installed_themes();
$uninstalled_themes = $this->crud_model->get_uninstalled_themes();
?>
<!-- It will show list of uninstalled themes for installing as an alert -->
<?php foreach ($uninstalled_themes as $key => $uninstalled_theme) : ?>
  <div class="alert alert-info new-theme-alert" role="alert">
    <i class="dripicons-information mr-2"></i> <strong><?php echo ucfirst(substr($uninstalled_theme, 0, -4)); ?></strong> <?php echo get_phrase('theme_is_showed_up') . '. ' . get_phrase('hit_the_install_button_for_installing'); ?>.
    <a href="<?php echo site_url('admin/install_theme/' . $uninstalled_theme); ?>" class="btn btn-primary btn-rounded float-right"><?php echo get_phrase('install') . ' ' . ucfirst(substr($uninstalled_theme, 0, -4)) . ' ' . get_phrase('theme'); ?></a>
  </div>
<?php endforeach; ?>

<div class="row ">
  <div class="col-xl-12">
    <div class="card">
      <div class="card-body">
        <h4 class="page-title"> <i class="mdi mdi-apple-keyboard-command title_icon"></i> <?php echo get_phrase('theme_settings'); ?>
          <a href="https://academy-lms.com/themes" target="_blank" class="btn btn-outline-primary btn-rounded alignToTitle"> <i class="mdi mdi-cart"></i> <?php echo get_phrase('buy_new_theme'); ?></a>
        </h4>
      </div> <!-- end card body-->
    </div> <!-- end card -->
  </div><!-- end col-->
</div>


<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-body">

        <h4 class="header-title mb-3"><?php echo get_phrase('installed_themes'); ?></h4>

        <!-- <ul class="nav nav-tabs nav-bordered mb-3">
          <li class="nav-item">
            <a href="#installed_themes" data-toggle="tab" aria-expanded="false" class="nav-link active">
              <i class="mdi mdi-home-variant d-lg-none d-block mr-1"></i>
              <span class="d-none d-lg-block"><?php echo get_phrase('installed_themes'); ?></span>
            </a>
          </li>
          <li class="nav-item">
            <a href="#premium_themes" data-toggle="tab" aria-expanded="true" class="nav-link">
              <i class="mdi mdi-account-circle d-lg-none d-block mr-1"></i>
              <span class="d-none d-lg-block"><?php echo get_phrase('add_new_themes'); ?></span>
            </a>
          </li>
        </ul> -->

        <div class="tab-content">
          <div class="tab-pane show active" id="installed_themes">
            <div class="row">
              <?php foreach ($installed_themes as $key => $installed_theme) : ?>
                <div class="col-xl-4">
                  <div class="card-deck-wrapper">
                    <div class="card-deck">
                      <div class="card d-block">
                        <img class="card-img-top" src="<?php echo base_url('assets/frontend/' . $installed_theme . '/preview.png'); ?>" alt="Card image cap">
                        <div class="card-body">
                          <h5 class="card-title"><?php echo ucfirst($installed_theme); ?></h5>
                          <div class="">
                            <?php if (get_frontend_settings('theme') == $installed_theme) : ?>
                              <a href="javascript::" class="btn btn-icon btn-success col" id="" style="" style="margin-right:5px;">
                                <i class="mdi mdi-home"></i> <?php echo get_phrase('active_theme'); ?>
                              </a>
                            <?php else : ?>
                              <a href="javascript::" class="btn btn-icon btn-secondary col-5" id="" style="" style="margin-right:5px;" onclick="activate_theme('<?php echo $installed_theme; ?>')">
                                <i class="mdi mdi-shield-check"></i> <?php echo get_phrase('activate'); ?>
                              </a>
                              <a href="javascript::" class="btn btn-icon btn-secondary float-right col-5" id="" style="" style="margin-right:5px;" onclick="confirm_modal('<?php echo site_url('admin/theme_actions/remove/' . $installed_theme); ?>');">
                                <i class="mdi mdi-alert-octagram"></i> <?php echo get_phrase('remove'); ?>
                              </a>
                            <?php endif; ?>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <!--new themes-->
        </div>

      </div> <!-- end card-body-->
    </div> <!-- end card-->
  </div>
</div>

<script type="text/javascript">
  function activate_theme(theme) {
    $.ajax({
      url: '<?php echo site_url('admin/theme_actions/activate/'); ?>',
      type: 'POST',
      data: {
        theme: theme
      },
      success: function(response) {
        if (response) {
          success_notify(theme.toUpperCase() + ' <?php echo get_phrase('theme_successfully_activated') ?>');
          setTimeout(
            function() {
              location.reload();
            }, 1000);
        } else {
          error_notify('<?php echo get_phrase('you_do_not_have_right_to_access_this_theme'); ?>');
        }
      }
    });
  }
</script>