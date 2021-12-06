<div class="row ">
  <div class="col-xl-12">
    <div class="card">
      <div class="card-body">
        <h4 class="page-title"> <i class="mdi mdi-power-plug title_icon"></i> <?php echo get_phrase('addon_manager'); ?>
          <a href="https://academy-lms.com/addons" target="_blank" class="btn btn-outline-primary btn-rounded alignToTitle"> <i class="mdi mdi-cart"></i> <?php echo get_phrase('buy_new_addon'); ?></a>
          <a href="<?php echo site_url('admin/addon/add'); ?>" class="btn btn-outline-primary btn-rounded alignToTitle mr-1"><i class="mdi mdi-download"></i> <?php echo get_phrase('install_addon'); ?></a>
        </h4>
      </div> <!-- end card body-->
    </div> <!-- end card -->
  </div><!-- end col-->
</div>

<!-- Start page title end -->
<div class="row justify-content-center">
  <div class="col-xl-12">
    <div class="card">
      <div class="card-body">
        <div class="table-responsive-sm mt-4">
          <table id="basic-datatable" class="table table-striped table-centered mb-0">
            <thead>
              <tr>
                <th><?php echo get_phrase('name'); ?></th>
                <th><?php echo get_phrase('version'); ?></th>
                <th><?php echo get_phrase('status'); ?></th>
                <th><?php echo get_phrase('actions'); ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($addons as $addon) : ?>
                <tr class="gradeU">
                  <td><?php echo $addon['name']; ?></td>
                  <td><?php echo $addon['version']; ?></td>
                  <td>
                    <?php if ($addon['status'] == 1) : ?>
                      <span class="badge badge-success"><?php echo get_phrase('active'); ?></span>
                    <?php else : ?>
                      <span class="badge badge-secondary"><?php echo get_phrase('deactive'); ?></span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div class="dropright dropright">
                      <button type="button" class="btn btn-sm btn-outline-primary btn-rounded btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="mdi mdi-dots-vertical"></i>
                      </button>
                      <ul class="dropdown-menu">
                        <a class="dropdown-item" href="<?php echo site_url('admin/addon/update'); ?>"><?php echo get_phrase('addon_update'); ?></a></li>
                        <?php if ($addon['status'] == 1) : ?>
                          <li><a class="dropdown-item" href="#" onclick="confirm_modal('<?php echo site_url('admin/addon/deactivate/' . $addon['id']); ?>');"><?php echo get_phrase('deactive'); ?></a></li>
                        <?php else : ?>
                          <li><a class="dropdown-item" href="#" onclick="confirm_modal('<?php echo site_url('admin/addon/activate/' . $addon['id']); ?>');"><?php echo get_phrase('active'); ?></a></li>
                        <?php endif; ?>
                        <li><a class="dropdown-item" href="#" onclick="confirm_modal('<?php echo site_url('admin/addon/delete/' . $addon['id']); ?>');"><?php echo get_phrase('delete'); ?></a></li>
                        <li><a class="dropdown-item" href="javascript::" onclick="showAjaxModal('<?php echo site_url('modal/popup/about_this_addon/' . $addon['id']); ?>', '<?php echo get_phrase('about_this_addon'); ?>')"><?php echo get_phrase('about_this_addon'); ?></a></li>
                      </ul>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>