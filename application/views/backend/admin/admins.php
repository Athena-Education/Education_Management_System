<div class="row ">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="page-title"> <i class="mdi mdi-apple-keyboard-command title_icon"></i> <?php echo $page_title; ?>
                    <a href="<?php echo site_url('admin/admin_form/add_admin_form'); ?>" class="btn btn-outline-primary btn-rounded alignToTitle"><i class="mdi mdi-plus"></i><?php echo get_phrase('add_admin'); ?></a>
                </h4>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="mb-3 header-title"><?php echo get_phrase('admins'); ?></h4>
                <div class="table-responsive-sm mt-4">
                    <table id="basic-datatable" class="table table-striped table-centered mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo get_phrase('photo'); ?></th>
                                <th><?php echo get_phrase('name'); ?></th>
                                <th><?php echo get_phrase('email'); ?></th>
                                <th><?php echo get_phrase('actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($admins as $key => $user) : ?>
                                <tr>
                                    <td><?php echo $key + 1; ?></td>
                                    <td>
                                        <img src="<?php echo $this->user_model->get_user_image_url($user['id']); ?>" alt="" height="50" width="50" class="img-fluid rounded-circle img-thumbnail">
                                    </td>
                                    <td><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></td>
                                    <td><?php echo $user['email']; ?></td>
                                    <td>
                                        <?php if (!is_root_admin($user['id'])) : ?>
                                            <div class="dropright dropright">
                                                <button type="button" class="btn btn-sm btn-outline-primary btn-rounded btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="<?php echo site_url('admin/permissions?permission_assing_to=' . $user['id']) ?>"><?php echo get_phrase('assign_permission'); ?></a></li>
                                                    <li><a class="dropdown-item" href="<?php echo site_url('admin/admin_form/edit_admin_form/' . $user['id']) ?>"><?php echo get_phrase('edit'); ?></a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="confirm_modal('<?php echo site_url('admin/admins/delete/' . $user['id']); ?>');"><?php echo get_phrase('delete'); ?></a></li>
                                                </ul>
                                            </div>
                                        <?php else : ?>
                                            <span class="badge badge-success"><?php echo ucwords(get_phrase('root_admin')); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>