<div class="row ">
  <div class="col-xl-12">
    <div class="card">
      <div class="card-body">
        <h4 class="page-title"> <i class="mdi mdi-apple-keyboard-command title_icon"></i> <?php echo get_phrase('instructor_revenue'); ?></h4>
      </div> <!-- end card body-->
    </div> <!-- end card -->
  </div><!-- end col-->
</div>

<div class="row">
  <div class="col-xl-12">
    <div class="card">
      <div class="card-body">
        <h4 class="mb-3 header-title"><?php echo get_phrase('instructor_revenue'); ?></h4>
        <div class="table-responsive-sm mt-4">
          <table id="basic-datatable" class="table table-striped table-centered mb-0">
            <thead>
              <tr>
                <th><?php echo get_phrase('enrolled_course'); ?></th>
                <th><?php echo get_phrase('instructor'); ?></th>
                <th><?php echo get_phrase('total_amount'); ?></th>
                <th><?php echo get_phrase('instructor_revenue'); ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($payment_history as $payment) :
                $course_data = $this->db->get_where('course', array('id' => $payment['course_id']))->row_array();
                $user_data = $this->db->get_where('users', array('id' => $course_data['user_id']))->row_array(); ?>
                <?php
                $paypal_keys          = json_decode($user_data['paypal_keys'], true);
                $stripe_keys          = json_decode($user_data['stripe_keys'], true);
                ?>
                <tr class="gradeU">
                  <td>
                    <strong><a href="<?php echo site_url('home/course/' . slugify($course_data['title']) . '/' . $course_data['id']); ?>" target="_blank"><?php echo $course_data['title']; ?></a></strong><br>
                    <small class="text-muted"><?php echo get_phrase('enrolment_date') . ': ' . date('D, d-M-Y', $payment['date_added']); ?></small>
                    <?php if ($payment['coupon']) : ?>
                      <small class="d-block">
                        <span class="text-muted">
                          <?php echo get_phrase('coupon_applied'); ?> :
                        </span>
                        <span class="badge badge-success">
                          <i class="fas fa-tags"></i> <?php echo $payment['coupon']; ?>
                        </span>
                      </small>
                    <?php endif; ?>
                  </td>
                  <td><?php echo $user_data['first_name'] . ' ' . $user_data['last_name']; ?></td>
                  <td>
                    <?php echo currency($payment['amount']); ?>
                  </td>
                  <td>
                    <?php echo currency($payment['instructor_revenue']); ?>
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