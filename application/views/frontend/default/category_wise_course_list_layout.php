<ul>
    <?php foreach ($courses as $course) :
        $instructor_details = $this->user_model->get_all_user($course['user_id'])->row_array(); ?>
        <li>
            <div class="course-box-2">
                <div class="course-image">
                    <a href="<?php echo site_url('home/course/' . rawurlencode(slugify($course['title'])) . '/' . $course['id']) ?>">
                        <img src="<?php echo $this->crud_model->get_course_thumbnail_url($course['id']); ?>" alt="" class="img-fluid">
                    </a>
                </div>
                <div class="course-details">
                    <a href="<?php echo site_url('home/course/' . rawurlencode(slugify($course['title'])) . '/' . $course['id']); ?>" class="course-title"><?php echo $course['title']; ?></a>
                    <?php if ($course['multi_instructor']) : ?>
                        <?php $instructors = $this->user_model->get_multi_instructor_details_with_csv($course['user_id']); ?>
                        <?php foreach ($instructors as $key => $instructor) : ?>
                            <a href="<?php echo site_url('home/instructor_page/' . $instructor['id']) ?>" class="course-instructor">
                                <span class="instructor-name"><?php echo $instructor['first_name'] . ' ' . $instructor['last_name']; ?></span>
                            </a>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <a href="<?php echo site_url('home/instructor_page/' . $instructor_details['id']) ?>" class="course-instructor">
                            <span class="instructor-name"><?php echo $instructor_details['first_name'] . ' ' . $instructor_details['last_name']; ?></span>
                        </a>
                    <?php endif; ?>

                    <div class="course-subtitle">
                        <?php echo $course['short_description']; ?>
                    </div>

                    <div class="course-meta">
                        <?php if ($course['course_type'] == 'general') : ?>
                            <span class=""><i class="fas fa-play-circle"></i>
                                <?php
                                $number_of_lessons = $this->crud_model->get_lessons('course', $course['id'])->num_rows();
                                echo $number_of_lessons . ' ' . site_phrase('lessons');
                                ?>
                            </span>
                            <span class=""><i class="far fa-clock"></i>
                                <?php echo $this->crud_model->get_total_duration_of_lesson_by_course_id($course['id']); ?>
                            </span>
                        <?php endif; ?>
                        <span class=""><i class="fas fa-closed-captioning"></i><?php echo site_phrase($course['language']); ?></span>
                        <span class=""><i class="fa fa-level-up"></i><?php echo site_phrase($course['level']); ?></span>
                        <p class="text-left text-secondary d-inline-block course-compare" style="font-size: 13px; cursor : pointer; font-weight : 500; color : #4d98ad !important;" redirect_to="<?php echo site_url('home/compare?course-1=' . rawurlencode(slugify($course['title'])) . '&&course-id-1=' . $course['id']); ?>">
                            <i class="fas fa-balance-scale"></i> <?php echo site_phrase('compare_this_course'); ?>
                        </p>
                    </div>
                </div>
                <div class="course-price-rating">
                    <div class="course-price">
                        <?php if ($course['is_free_course'] == 1) : ?>
                            <span class="current-price"><?php echo site_phrase('free'); ?></span>
                        <?php else : ?>
                            <?php if ($course['discount_flag'] == 1) : ?>
                                <span class="current-price"><?php echo currency($course['discounted_price']); ?></span>
                                <span class="original-price"><?php echo currency($course['price']); ?></span>
                            <?php else : ?>
                                <span class="current-price"><?php echo currency($course['price']); ?></span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div class="rating">
                        <?php
                        $total_rating =  $this->crud_model->get_ratings('course', $course['id'], true)->row()->rating;
                        $number_of_ratings = $this->crud_model->get_ratings('course', $course['id'])->num_rows();
                        if ($number_of_ratings > 0) {
                            $average_ceil_rating = ceil($total_rating / $number_of_ratings);
                        } else {
                            $average_ceil_rating = 0;
                        }

                        for ($i = 1; $i < 6; $i++) : ?>
                            <?php if ($i <= $average_ceil_rating) : ?>
                                <i class="fas fa-star filled"></i>
                            <?php else : ?>
                                <i class="fas fa-star"></i>
                            <?php endif; ?>
                        <?php endfor; ?>
                        <span class="d-inline-block average-rating"><?php echo $average_ceil_rating; ?></span>
                    </div>
                    <div class="rating-number">
                        <?php echo $this->crud_model->get_ratings('course', $course['id'])->num_rows() . ' ' . site_phrase('ratings'); ?>
                    </div>
                </div>
            </div>
        </li>
    <?php endforeach; ?>
</ul>