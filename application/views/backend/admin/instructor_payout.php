<div class="row ">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="page-title"> <i class="mdi mdi-apple-keyboard-command title_icon"></i> <?php echo get_phrase('instructor_payouts'); ?></h4>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>

<div class="row justify-content-center">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="mb-3 header-title"><?php echo get_phrase('list_of_payouts'); ?></h4>
                <ul class="nav nav-tabs nav-bordered mb-3">
                    <li class="nav-item">
                        <a href="#completed-b1" data-toggle="tab" aria-expanded="true" class="nav-link active">
                            <i class="mdi mdi-account-circle d-lg-none d-block mr-1"></i>
                            <span class="d-none d-lg-block"><?php echo get_phrase('completed_payouts'); ?></span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#pending-b1" data-toggle="tab" aria-expanded="false" class="nav-link">
                            <i class="mdi mdi-home-variant d-lg-none d-block mr-1"></i>
                            <span class="d-none d-lg-block"><?php echo get_phrase('pending_payouts'); ?> <span class="badge badge-danger-lighten"><?php echo $pending_payouts->num_rows(); ?></span></span>
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane show active" id="completed-b1">
                        <div class="row justify-content-md-center">
                            <div class="col-xl-6">
                                <form class="form-inline" action="<?php echo site_url('admin/instructor_payout/filter_by_date_range') ?>" method="get">
                                    <div class="col-xl-10">
                                        <div class="form-group">
                                            <div id="reportrange" class="form-control" data-toggle="date-picker-range" data-target-display="#selectedValue"  data-cancel-class="btn-light" style="width: 100%;">
                                                <i class="mdi mdi-calendar"></i>&nbsp;
                                                <span id="selectedValue"><?php echo date("F d, Y" , $timestamp_start) . " - " . date("F d, Y" , $timestamp_end);?></span> <i class="mdi mdi-menu-down"></i>
                                            </div>
                                            <input id="date_range" type="hidden" name="date_range" value="<?php echo date("d F, Y" , $timestamp_start) . " - " . date("d F, Y" , $timestamp_end);?>">
                                        </div>
                                    </div>
                                    <div class="col-xl-2">
                                        <button type="submit" class="btn btn-info" id="submit-button" onclick="update_date_range();"> <?php echo get_phrase('filter');?></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="table-responsive-sm mt-4">
                            <table id="completed-payout" class="table table-striped table-centered mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?php echo get_phrase('image'); ?></th>
                                        <th><?php echo get_phrase('instructor'); ?></th>
                                        <th><?php echo get_phrase('payout_amount'); ?></th>
                                        <th><?php echo get_phrase('payment_type'); ?></th>
                                        <th><?php echo get_phrase('payout_date'); ?></th>
                                        <th><?php echo get_phrase('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($completed_payouts->result_array() as $key => $completed_payout):
                                        $completed_payout_user_data = $this->db->get_where('users', array('id' => $completed_payout['user_id']))->row_array(); ?>
                                        <tr class="gradeU">
                                            <td> <?php echo ++$key; ?> </td>
                                            <td>
                                                <img src="<?php echo $this->user_model->get_user_image_url($completed_payout_user_data['id']);?>" alt="" height="50" width="50" class="img-fluid rounded-circle img-thumbnail">
                                            </td>
                                            <td>
                                                <strong><?php echo $completed_payout_user_data['first_name'].' '.$completed_payout_user_data['last_name']; ?></strong>
                                            </td>
                                            <td> <?php echo currency($completed_payout['amount']); ?> </td>
                                            <td> <?php echo ucfirst($completed_payout['payment_type']); ?> </td>
                                            <td> <?php echo date('D, d M Y', $completed_payout['date_added']); ?> </td>
                                            <td> <a href="<?php echo site_url('admin/invoice/'.$completed_payout['id']); ?>" class="btn btn-outline-primary btn-rounded btn-sm"><i class="mdi mdi-printer-settings"></i></a> </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane" id="pending-b1">
                        <div class="table-responsive-sm mt-4">
                            <table id="pending-payout" class="table table-striped table-centered mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?php echo get_phrase('image'); ?></th>
                                        <th><?php echo get_phrase('instructor'); ?></th>
                                        <th><?php echo get_phrase('payout_amount'); ?></th>
                                        <th><?php echo get_phrase('payout_date'); ?></th>
                                        <th><?php echo get_phrase('option'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pending_payouts->result_array() as $key => $pending_payout):
                                        $pending_payout_user_data = $this->db->get_where('users', array('id' => $pending_payout['user_id']))->row_array();
                                        $paypal_keys          = json_decode($pending_payout_user_data['paypal_keys'], true);
                                        $stripe_keys          = json_decode($pending_payout_user_data['stripe_keys'], true);
                                        ?>
                                        <tr class="gradeU">
                                            <td> <?php echo ++$key; ?> </td>
                                            <td>
                                                <img src="<?php echo $this->user_model->get_user_image_url($pending_payout_user_data['id']);?>" alt="" height="50" width="50" class="img-fluid rounded-circle img-thumbnail">
                                            </td>
                                            <td>
                                                <strong><?php echo $pending_payout_user_data['first_name'].' '.$pending_payout_user_data['last_name']; ?></strong>
                                            </td>
                                            <td> <?php echo currency($pending_payout['amount']); ?> </td>
                                            <td> <?php echo date('D, d M Y', $pending_payout['date_added']); ?> </td>
                                            <td style="text-align: center;">
                                              <?php if ($pending_payout['status'] == 0): ?>
                                                <?php if ($paypal_keys[0]['production_client_id'] != ""): ?>
                                                  <form action="<?php echo site_url('admin/paypal_checkout_for_instructor_revenue'); ?>" method="post">
                                                    <input type="hidden" name="amount_to_pay"        value="<?php echo $pending_payout['amount']; ?>">
                                                    <input type="hidden" name="payout_id"            value="<?php echo $pending_payout['id']; ?>">
                                                    <input type="hidden" name="instructor_name"      value="<?php echo $pending_payout_user_data['first_name'].' '.$pending_payout_user_data['last_name']; ?>">
                                                    <input type="hidden" name="production_client_id" value="<?php echo $paypal_keys[0]['production_client_id']; ?>">
                                                    <input type="submit" class="btn btn-outline-info btn-sm btn-rounded"        value="<?php echo get_phrase('pay_with_paypal'); ?>">
                                                  </form>
                                                <?php else: ?>
                                                  <button type="button" class = "btn btn-outline-danger btn-sm btn-rounded" name="button" onclick="alert('<?php echo get_phrase('this_instructor_has_not_provided_valid_paypal_client_id'); ?>')"><?php echo get_phrase('pay_with_paypal'); ?></button>
                                                <?php endif; ?>
                                                <?php if ($stripe_keys[0]['public_live_key'] != "" && $stripe_keys[0]['secret_live_key']): ?>
                                                  <?php include 'stripe_payment_gateway_form.php'; ?>
                                                <?php else: ?>
                                                  <button type="button" class = "btn btn-outline-danger btn-sm btn-rounded" name="button" onclick="alert('<?php echo get_phrase('this_instructor_has_not_provided_valid_public_key_or_secret_key'); ?>')"><?php echo get_phrase('pay_with_stripe'); ?></button>
                                                <?php endif; ?>
                                              <?php else: ?>
                                                <a href="<?php echo site_url('admin/invoice/'.$pending_payout['id']); ?>" class="btn btn-outline-primary btn-rounded btn-sm"><i class="mdi mdi-printer-settings"></i></a>
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
        initDataTable(['#pending-payout', '#completed-payout']);
    });

    function update_date_range()
    {
        var x = $("#selectedValue").html();
        $("#date_range").val(x);
    }
</script>
