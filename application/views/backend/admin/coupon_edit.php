<!-- start page title -->
<div class="row ">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="page-title"> <i class="mdi mdi-apple-keyboard-command title_icon"></i> <?php echo $page_title; ?>
                    <a href="<?php echo site_url('admin/coupons'); ?>" class="btn btn-outline-primary btn-rounded alignToTitle"><?php echo get_phrase('back_to_coupons'); ?></a>
                </h4>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>

<div class="row justify-content-center">
    <div class="col-xl-7">
        <div class="card">
            <div class="card-body">
                <div class="col-lg-12">
                    <h4 class="mb-3 header-title"><?php echo get_phrase('coupon_edit_form'); ?></h4>

                    <form class="required-form" action="<?php echo site_url('admin/coupons/edit/' . $coupon['id']); ?>" method="post" enctype="multipart/form-data">

                        <div class="form-group">
                            <label for="code"><?php echo get_phrase('coupon_code'); ?><span class="required">*</span></label>
                            <input type="text" class="form-control" id="code" name="code" value="<?php echo $coupon['code']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="discount_percentage"><?php echo get_phrase('discount_percentage'); ?></label>
                            <div class="input-group">
                                <input type="number" name="discount_percentage" id="discount_percentage" class="form-control" value="<?php echo $coupon['discount_percentage']; ?>" min="0" max="100">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="mdi mdi-percent"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="expiry_date"><?php echo get_phrase('expiry_date'); ?><span class="required">*</span></label>
                            <input type="text" name="expiry_date" class="form-control date" id="expiry_date" data-toggle="date-picker" data-single-date-picker="true" value="<?php echo date('m/d/Y', $coupon['expiry_date']); ?>">
                        </div>

                        <button type="submit" class="btn btn-primary"><?php echo get_phrase("submit"); ?></button>
                    </form>
                </div>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>