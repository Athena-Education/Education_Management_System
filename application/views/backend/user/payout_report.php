<!-- start page title -->
<div class="row ">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="page-title"> <i class="mdi mdi-apple-keyboard-command title_icon"></i> <?php echo get_phrase('payout_report'); ?>
                    <a href = "javascript:void(0)" class="btn btn-outline-primary btn-rounded alignToTitle" onclick="showAjaxModal('<?php echo site_url('modal/popup/request_withdrawal'); ?>', '<?php echo get_phrase('request_a_new_withdrawal'); ?>')"><i class="mdi mdi-plus"></i><?php echo get_phrase('request_a_new_withdrawal'); ?></a>
                </h4>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>

<div class="row justify-content-center">
    <div class="col-lg-4">
        <div class="card text-white bg-danger">
            <div class="card-body">
                <div class="float-right bg-white">
                    <i class="mdi mdi-currency-usd widget-icon text-danger"></i>
                </div>
                <h5 class="text-white font-weight-normal mt-0" title="<?php echo get_phrase('pending_amount'); ?>"><?php echo get_phrase('pending_amount'); ?></h5>
                <h3 class="mt-3 mb-3">
                    <span class="text-white"><i class="mdi mdi-arrow-down-bold"></i></span>
                    <?php echo $total_pending_amount > 0 ? currency($total_pending_amount) : currency_code_and_symbol().''.$total_pending_amount; ?>
                </h3>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col-->

    <div class="col-lg-4 pull-right">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="float-right bg-white">
                    <i class="mdi mdi-currency-usd widget-icon text-success"></i>
                </div>
                <h5 class="text-white font-weight-normal mt-0" title="<?php echo get_phrase('total_payout_amount'); ?>"><?php echo get_phrase('total_payout_amount'); ?></h5>
                <h3 class="mt-3 mb-3">
                    <span class="text-white"><i class="mdi mdi-arrow-down-bold"></i></span>
                    <?php echo $total_payout_amount > 0 ? currency($total_payout_amount) : currency_code_and_symbol().''.$total_payout_amount; ?>
                </h3>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col-->

    <div class="col-lg-4 pull-right">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="float-right bg-white">
                    <i class="mdi mdi-currency-usd widget-icon text-primary"></i>
                </div>
                <h5 class="text-white font-weight-normal mt-0" title="<?php echo get_phrase('requested_withdrawal_amount'); ?>"><?php echo get_phrase('requested_withdrawal_amount'); ?></h5>
                    <?php if ($requested_withdrawal_amount > 0): ?>
                        <h3 class="mt-3"><span class="text-white"><i class="mdi mdi-arrow-down-bold"></i></span> <?php echo currency($requested_withdrawal_amount); ?></h3>
                        <a href="javascript:void(0)" class="btn btn-icon btn-danger btn-sm" onclick="confirm_modal('<?php echo site_url('user/withdrawal/delete'); ?>');" style="float: right; margin-top: -18px;"> <i class="mdi mdi-delete"></i><?php echo get_phrase('delete_requested_withdrawal'); ?></a>
                    <?php else: ?>
                        <h3 class="mt-3 mb-3"><span class="text-white"><i class="mdi mdi-arrow-down-bold"></i></span> <?php echo currency_code_and_symbol().''.$requested_withdrawal_amount; ?></h3>
                    <?php endif; ?>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col-->
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="mb-3 header-title"><?php echo get_phrase('payout_report'); ?></h4>
                <div class="table-responsive-sm mt-4">
                    <table id="basic-datatable" class="table table-striped table-centered mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo get_phrase('payout_amount'); ?></th>
                                <th><?php echo get_phrase('payment_type'); ?></th>
                                <th><?php echo get_phrase('date_processed'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payouts->result_array() as $key => $payout):?>
                                <tr class="gradeU">
                                    <td> <?php echo ++$key; ?> </td>
                                    <td>
                                        <?php echo currency($payout['amount']); ?>
                                        <?php if (!$payout['status']): ?>
                                            <br><small class='badge badge-secondary-lighten'><strong><?php echo get_phrase('requested_at'); ?> :</strong> <?php echo date('D, d M Y', $payout['date_added']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($payout['status']): ?>
                                            <?php echo ucfirst($payout['payment_type']); ?>
                                        <?php else: ?>
                                            <span class='badge badge-warning'><?php echo get_phrase('pending'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($payout['status'] && !empty($payout['last_modified'])): ?>
                                            <?php echo date('D, d M Y', $payout['last_modified']); ?>
                                        <?php endif; ?>
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

<script type="text/javascript">
function update_date_range()
{
    var x = $("#selectedValue").html();
    $("#date_range").val(x);
}
</script>
