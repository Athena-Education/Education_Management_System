<?php
$total_price = 0;
$course_details = $this->crud_model->get_course_by_id($cart_item)->row_array();
$instructor_details = $this->user_model->get_all_user($course_details['user_id'])->row_array();
$this_mobile_view = 'mobile';
$check_course_enrolled = $this->crud_model->check_course_enrolled($cart_item, $user_id);
?>
<div class="row px-1 mb-5">
	<div class="col-12">
		<img src="<?php echo $this->crud_model->get_course_thumbnail_url($cart_item); ?>" alt="" class="img-fluid" width='100%' style="height: 95%;">
	</div>
	<div class="col-12">
		<div class="details">
			<a href="javascript:void(0)">
				<h4><?php echo $course_details['title']; ?></h4>
			</a>
			<div class="row mt-1">
				<div class="col-4 text-secondary">
					<?php echo get_phrase('by'); ?>
					<span class="instructor-name"><?php echo $instructor_details['first_name'] . ' ' . $instructor_details['last_name']; ?></span>
				</div>
				<div class="col-8 cart-course-wrapper p-0">
					<?php if ($course_details['is_free_course'] == 1) : ?>
						<?php $total_price = 0; ?>
						<?php $this->session->set_userdata('total_price_of_checking_out', $total_price); ?>
						<div class="current-price" style="margin-left: 65%; font-size: 30px; color: #ec5252;"> <b><i><?php echo get_phrase('free'); ?></i></b></div>
					<?php else : ?>
						<?php if ($course_details['discount_flag'] == 1) : ?>
							<?php $total_price = $course_details['discounted_price']; ?>
							<?php $this->session->set_userdata('total_price_of_checking_out', $total_price); ?>
							<div class="float-right text-right" style="margin-left: 65%;">
								<div class="price">
									<div class="current-price" style="font-size: 30px;"> <?php echo currency($course_details['discounted_price']); ?> </div>
								</div>
								<div class="price">
									<div class="original-price" style="font-size: 15px;"> <?php echo currency($course_details['price']); ?> </div>
								</div>
							</div>
						<?php elseif ($course_details['discount_flag'] != 1) : ?>
							<?php $total_price = $course_details['price']; ?>
							<?php $this->session->set_userdata('total_price_of_checking_out', $total_price); ?>
							<div class="current-price" style="margin-left: 65%; font-size: 30px;"> <?php echo currency($course_details['price']); ?> </div>
						<?php endif; ?>
					<?php endif; ?>
				</div>

				<div class="col-12 mt-3">
					<?php if ($is_login_now == 1) : ?>
						<?php if ($course_details['is_free_course'] == 1) : ?>
							<?php if ($check_course_enrolled > 0) : ?>
								<?php if ($enroll_type == 'free') : ?>
									<div class="alert alert-success" role="alert">
										<i class="dripicons-checkmark mr-2"></i><strong><?php echo get_phrase('course_enrolled'); ?></strong> <?php echo get_phrase('successfully_done'); ?>.
									</div>
								<?php else : ?>
									<div class="alert alert-success" role="alert">
										<i class="dripicons-checkmark mr-2"></i><strong><?php echo get_phrase('this_course_is_already_enrolled'); ?></strong>.
									</div>
								<?php endif; ?>
							<?php else : ?>
								<button class="btn btn-lg w-100 float-right" onclick="free_course_enrolled()" style="height: 60px;"><?php echo get_phrase('get_enrolled'); ?></button>
							<?php endif; ?>
						<?php else : ?>
							<?php if ($check_course_enrolled > 0) : ?>
								<?php if ($enroll_type == 'paid') : ?>
									<div class="alert alert-success" role="alert">
										<i class="dripicons-checkmark mr-2"></i><strong><?php echo get_phrase('payment'); ?></strong> <?php echo get_phrase('successfully_done'); ?>.
									</div>
								<?php elseif ($enroll_type == 'error') : ?>
									<div class="alert alert-danger" role="alert">
										<i class="dripicons-checkmark mr-2"></i><?php echo get_phrase('an_error_occurred'); ?>.
									</div>
								<?php else : ?>
									<div class="alert alert-success" role="alert">
										<i class="dripicons-checkmark mr-2"></i><strong><?php echo get_phrase('this_course_is_already_purchased'); ?></strong>.
									</div>
								<?php endif; ?>
							<?php else : ?>
								<?php if ($enroll_type == 'pending') : ?>
									<div class="alert alert-warning" role="alert">
										<i class="dripicons-checkmark mr-2"></i><?php echo get_phrase('your_payment_will_be_reviewed_admin'); ?>.
									</div>
								<?php elseif ($enroll_type == 'error') : ?>
									<div class="alert alert-danger" role="alert">
										<i class="dripicons-checkmark mr-2"></i><?php echo get_phrase('an_error_occurred'); ?>.
									</div>
								<?php else : ?>
									<a href="<?php echo site_url('home/payment_gateway_mobile/' . $cart_item . '/' . $user_id); ?>" class="btn btn-lg w-100 float-right" style="height: 60px;"><?php echo get_phrase('checkout'); ?><?php echo ' / ' . currency($total_price); ?></a>
								<?php endif; ?>
							<?php endif; ?>
						<?php endif; ?>
					<?php else : ?>
						<div class="alert alert-warning" role="alert" style="">
							<i class="dripicons-warning mr-2"></i><?php echo get_phrase('please'); ?> <strong> <?php echo get_phrase('login'); ?> </strong> <?php echo get_phrase('first !'); ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="https://www.paypalobjects.com/js/external/dg.js"></script>
<script>
	var dgFlow = new PAYPAL.apps.DGFlow({
		trigger: 'submitBtn'
	});
	dgFlow = top.dgFlow || top.opener.top.dgFlow;
	dgFlow.closeFlow();
	// top.close();
</script>

<script type="text/javascript">
	// function handleCheckOut() {
	//     $('#paymentModal').modal('show');
	//     $('.total_price_of_checking_out').val('<?php echo $total_price; ?>');
	// }

	function free_course_enrolled() {
		$.ajax({
			url: "<?php echo site_url('home/get_enrolled_to_free_course_mobile/' . $cart_item . '/' . $user_id . '/true'); ?>",
			success: function() {
				window.location = '<?php echo site_url('home/payment_success_mobile/' . $cart_item . '/' . $user_id . '/free'); ?>';
			}
		});
	}
</script>
