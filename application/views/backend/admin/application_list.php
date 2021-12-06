<div class="row ">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="page-title"> <i class="mdi mdi-apple-keyboard-command title_icon"></i> <?php echo get_phrase('instructor_applications'); ?></h4>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>

<div class="row justify-content-center">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="mb-3 header-title"><?php echo get_phrase('list_of_applications'); ?></h4>
                <ul class="nav nav-tabs nav-bordered mb-3">
                    <li class="nav-item">
                        <a href="#pending-b1" data-toggle="tab" aria-expanded="false" class="nav-link active">
                            <i class="mdi mdi-home-variant d-lg-none d-block mr-1"></i>
                            <span class="d-none d-lg-block"><?php echo get_phrase('pending_applications'); ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#approved-b1" data-toggle="tab" aria-expanded="true" class="nav-link">
                            <i class="mdi mdi-account-circle d-lg-none d-block mr-1"></i>
                            <span class="d-none d-lg-block"><?php echo get_phrase('approved_applications'); ?></span>
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane show active" id="pending-b1">
                        <div class="table-responsive-sm mt-4">
                            <table id="pending-application" class="table table-striped table-centered mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?php echo get_phrase('name'); ?></th>
                                        <th><?php echo get_phrase('document'); ?></th>
                                        <th><?php echo get_phrase('details'); ?></th>
                                        <th><?php echo get_phrase('status'); ?></th>
                                        <th><?php echo get_phrase('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pending_applications->result_array() as $key => $pending_application):
                                        $user_data = $this->user_model->get_all_user($pending_application['user_id'])->row_array();?>
                                        <tr class="gradeU">
                                            <td>
                                                <?php echo ++$key; ?>
                                            </td>
                                            <td>
                                                <?php echo $user_data['first_name'].' '.$user_data['last_name']; ?>
                                            </td>
                                            <td>
                                                <a href="javascript::" class="btn btn-primary" onclick="showAjaxModal('<?php echo site_url('modal/popup/application_details/'.$pending_application['id']); ?>', '<?php echo get_phrase('applicant_details'); ?>')">
                                                    <i class="fa fa-info-circle"></i> <?php echo get_phrase('application_details'); ?>
                                                </a>
                                            </td>
                                            <td>
                                                <?php if (!empty($pending_application['document'])): ?>
                                                    <a href="<?php echo base_url().'uploads/document/'.$pending_application['document']; ?>" class="btn btn-info" download>
                                                        <i class="fa fa-download"></i> <?php echo get_phrase('download'); ?>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                            <td style="text-align: center;">
                                                <?php if ($pending_application['status'] == 0): ?>
                                                    <div class="badge badge-danger"><?php echo get_phrase('pending'); ?></div>
                                                <?php elseif($pending_application['status'] == 1): ?>
                                                    <div class="badge badge-success"><?php echo get_phrase('approved'); ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="dropright dropright">
                                                    <button type="button" class="btn btn-sm btn-outline-primary btn-rounded btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="mdi mdi-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item" href="#" onclick="confirm_modal('<?php echo site_url();?>admin/instructor_application/approve/<?php echo $pending_application['id']; ?>');">
                                                                <?php echo get_phrase('approve');?>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="#" onclick="confirm_modal('<?php echo site_url();?>admin/instructor_application/delete/<?php echo $pending_application['id']; ?>');">
                                                                <?php echo get_phrase('delete'); ?>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="approved-b1">
                        <div class="table-responsive-sm mt-4">
                            <table id="approved-application" class="table table-striped table-centered mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?php echo get_phrase('name'); ?></th>
                                        <th><?php echo get_phrase('document'); ?></th>
                                        <th><?php echo get_phrase('details'); ?></th>
                                        <th><?php echo get_phrase('status'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($approved_applications->result_array() as $key => $approved_application):
                                        $user_data = $this->user_model->get_all_user($approved_application['user_id'])->row_array();?>
                                        <tr class="gradeU">
                                            <td>
                                                <?php echo ++$key; ?>
                                            </td>
                                            <td>
                                                <?php echo $user_data['first_name'].' '.$user_data['last_name']; ?>
                                            </td>
                                            <td>
                                                <a href="javascript::" class="btn btn-primary" onclick="showAjaxModal('<?php echo site_url('modal/popup/application_details/'.$approved_application['id']); ?>', '<?php echo get_phrase('applicant_details'); ?>')">
                                                    <i class="fa fa-info-circle"></i> <?php echo get_phrase('application_details'); ?>
                                                </a>
                                            </td>
                                            <td>
                                                <?php if (!empty($approved_application['document'])): ?>
                                                    <a href="<?php echo base_url().'uploads/document/'.$approved_application['document']; ?>" class="btn btn-info" download>
                                                        <i class="fa fa-download"></i> <?php echo get_phrase('download'); ?>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                            <td style="text-align: center;">
                                                <?php if ($approved_application['status'] == 0): ?>
                                                    <div class="badge badge-danger"><?php echo get_phrase('pending'); ?></div>
                                                <?php elseif($approved_application['status'] == 1): ?>
                                                    <div class="badge badge-success"><?php echo get_phrase('approved'); ?></div>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        initDataTable(['#pending-application', '#approved-application']);
    });
</script>
