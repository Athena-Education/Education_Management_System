<style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 1em;
    }

    table {
        table-layout: fixed;
    }

    table th,
    table td {
        padding: 0.5em 1em;
    }

    table thead th,
    table tbody td {
        text-align: center;
    }

    table thead {
        color: #686f7a;
    }

    table thead th {
        padding: 1em;
    }

    table[data-comparing=active] tbody th {
        border-bottom: none;
        font-size: 0.75em;
        color: #767676;
        padding-bottom: 0;
    }

    tr {
        border-bottom: 1px solid #e5ecf3;
    }
</style>
<section class="category-header-area">
    <div class="container-lg">
        <div class="row">
            <div class="col">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo site_url('home'); ?>"><i class="fas fa-home"></i></a></li>
                        <li class="breadcrumb-item">
                            <a href="#">
                                <?php echo html_escape($page_title); ?>
                            </a>
                        </li>
                    </ol>
                </nav>
                <h1 class="category-name">
                    <?php echo html_escape($page_title); ?>
                </h1>
            </div>
        </div>
    </div>
</section>

<section class="category-course-list-area">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <table class="table-striped">
                    <thead class="table">
                        <tr style="border-bottom: none;">
                            <th style="width: 16%;"></th>
                            <form action="<?php echo site_url('home/compare'); ?>" class="comparison-form" method="GET">
                                <th style="width: 28%;">
                                    <select class="form-control" name="" onchange="compareCourses(this.value, 1)">
                                        <option value=""><?php echo site_phrase('choose_a_course_to_compare'); ?></option>
                                        <?php foreach ($courses as $key => $course) : ?>
                                            <option value="<?php echo slugify($course['title']) . '_' . $course['id']; ?>" <?php if (isset($course_1_details['id']) && slugify($course_1_details['title']) . '_' . $course_1_details['id'] == slugify($course['title']) . '_' . $course['id']) echo 'selected'; ?>><?php echo $course['title']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="hidden" name="course-1" id="course-1" value="<?php echo isset($course_1_details['id']) ? slugify($course_1_details['title']) : ''; ?>">
                                    <input type="hidden" name="course-id-1" id="course-id-1" value="<?php echo isset($course_1_details['id']) ? slugify($course_1_details['id']) : ''; ?>">
                                </th>
                                <th style="width: 28%;">
                                    <select class="form-control" name="" onchange="compareCourses(this.value, 2)">
                                        <option value=""><?php echo site_phrase('choose_a_course_to_compare'); ?></option>
                                        <?php foreach ($courses as $key => $course) : ?>
                                            <option value="<?php echo slugify($course['title']) . '_' . $course['id']; ?>" <?php if (isset($course_2_details['id']) && slugify($course_2_details['title']) . '_' . $course_2_details['id'] == slugify($course['title']) . '_' . $course['id']) echo 'selected'; ?>><?php echo $course['title']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="hidden" name="course-2" id="course-2" value="<?php echo isset($course_2_details['id']) ? slugify($course_2_details['title']) : ''; ?>">
                                    <input type="hidden" name="course-id-2" id="course-id-2" value="<?php echo isset($course_2_details['id']) ? slugify($course_2_details['id']) : ''; ?>">
                                </th>
                                <th style="width: 28%;">
                                    <select class="form-control" name="" onchange="compareCourses(this.value, 3)">
                                        <option value=""><?php echo site_phrase('choose_a_course_to_compare'); ?></option>
                                        <?php foreach ($courses as $key => $course) : ?>
                                            <option value="<?php echo slugify($course['title']) . '_' . $course['id']; ?>" <?php if (isset($course_3_details['id']) && slugify($course_3_details['title']) . '_' . $course_3_details['id'] == slugify($course['title']) . '_' . $course['id']) echo 'selected'; ?>><?php echo $course['title']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="hidden" name="course-3" id="course-3" value="<?php echo isset($course_3_details['id']) ? slugify($course_3_details['title']) : ''; ?>">
                                    <input type="hidden" name="course-id-3" id="course-id-3" value="<?php echo isset($course_3_details['id']) ? slugify($course_3_details['id']) : ''; ?>">
                                </th>
                            </form>
                        </tr>
                    </thead>
                    <thead>

                        <tr>
                            <th></th>
                            <th><img src="<?php echo isset($course_1_details['id']) ? $this->crud_model->get_course_thumbnail_url($course_1_details['id']) : ''; ?>" alt="" class="img-fluid" style="height: 200px; margin-bottom: 10px;"><br><?php if (isset($course_1_details['id'])) : ?><a href="<?php echo site_url('home/course/' . rawurlencode(slugify($course_1_details['title'])) . '/' . $course_1_details['id']) ?>"><?php echo $course_1_details['title']; ?></a><?php endif; ?></th>
                            <th><img src="<?php echo isset($course_2_details['id']) ? $this->crud_model->get_course_thumbnail_url($course_2_details['id']) : ''; ?>" alt="" class="img-fluid" style="height: 200px; margin-bottom: 10px;"><br><?php if (isset($course_2_details['id'])) : ?><a href="<?php echo site_url('home/course/' . rawurlencode(slugify($course_2_details['title'])) . '/' . $course_2_details['id']) ?>"><?php echo $course_2_details['title']; ?></a><?php endif; ?></th>
                            <th><img src="<?php echo isset($course_3_details['id']) ? $this->crud_model->get_course_thumbnail_url($course_3_details['id']) : ''; ?>" alt="" class="img-fluid" style="height: 200px; margin-bottom: 10px;"><br><?php if (isset($course_3_details['id'])) : ?><a href="<?php echo site_url('home/course/' . rawurlencode(slugify($course_3_details['title'])) . '/' . $course_3_details['id']) ?>"><?php echo $course_3_details['title']; ?></a><?php endif; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th><?php echo site_phrase('has_discount'); ?></th>
                            <td><?php echo (isset($course_1_details['id']) && $course_1_details['discount_flag']) ? '✅' : '❌'; ?></td>
                            <td><?php echo (isset($course_2_details['id']) && $course_2_details['discount_flag']) ? '✅' : '❌'; ?></td>
                            <td><?php echo (isset($course_3_details['id']) && $course_3_details['discount_flag']) ? '✅' : '❌'; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo site_phrase('price'); ?></th>
                            <td>
                                <?php if (isset($course_1_details['id'])) : ?>
                                    <?php if ($course_1_details['is_free_course'] == 1) : ?>
                                        <?php echo site_phrase('free'); ?>
                                    <?php else : ?>
                                        <?php if ($course_1_details['discount_flag'] == 1) : ?>
                                            <?php echo currency($course_1_details['discounted_price']); ?>
                                        <?php else : ?>
                                            <?php echo currency($course_1_details['price']); ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (isset($course_2_details['id'])) : ?>
                                    <?php if ($course_2_details['is_free_course'] == 1) : ?>
                                        <?php echo site_phrase('free'); ?>
                                    <?php else : ?>
                                        <?php if ($course_2_details['discount_flag'] == 1) : ?>
                                            <?php echo currency($course_2_details['discounted_price']); ?>
                                        <?php else : ?>
                                            <?php echo currency($course_2_details['price']); ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (isset($course_3_details['id'])) : ?>
                                    <?php if ($course_3_details['is_free_course'] == 1) : ?>
                                        <?php echo site_phrase('free'); ?>
                                    <?php else : ?>
                                        <?php if ($course_3_details['discount_flag'] == 1) : ?>
                                            <?php echo currency($course_3_details['discounted_price']); ?>
                                        <?php else : ?>
                                            <?php echo currency($course_3_details['price']); ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo site_phrase('made_in'); ?></th>
                            <td><?php echo (isset($course_1_details['id'])) ? ucfirst($course_1_details['language']) : '-'; ?></td>
                            <td><?php echo (isset($course_2_details['id'])) ? ucfirst($course_2_details['language']) : '-'; ?></td>
                            <td><?php echo (isset($course_3_details['id'])) ? ucfirst($course_3_details['language']) : '-'; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo site_phrase('last_updated_at'); ?></th>
                            <td><?php echo (isset($course_1_details['id'])) ? date('D, d-M-Y', $course_1_details['last_modified']) : '-'; ?></td>
                            <td><?php echo (isset($course_2_details['id'])) ? date('D, d-M-Y', $course_2_details['last_modified']) : '-'; ?></td>
                            <td><?php echo (isset($course_3_details['id'])) ? date('D, d-M-Y', $course_3_details['last_modified']) : '-'; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo site_phrase('level'); ?></th>
                            <td><?php echo (isset($course_1_details['id'])) ? ucfirst($course_1_details['level']) : '-'; ?></td>
                            <td><?php echo (isset($course_2_details['id'])) ? ucfirst($course_2_details['level']) : '-'; ?></td>
                            <td><?php echo (isset($course_3_details['id'])) ? ucfirst($course_3_details['level']) : '-'; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo site_phrase('short_description'); ?></th>
                            <td><?php echo (isset($course_1_details['id'])) ? ucfirst($course_1_details['short_description']) : '-'; ?></td>
                            <td><?php echo (isset($course_2_details['id'])) ? ucfirst($course_2_details['short_description']) : '-'; ?></td>
                            <td><?php echo (isset($course_3_details['id'])) ? ucfirst($course_3_details['short_description']) : '-'; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo site_phrase('total_lessons'); ?></th>
                            <td><?php echo (isset($course_1_details['id'])) ? $this->crud_model->get_lessons('course', $course_1_details['id'])->num_rows() : '-'; ?></td>
                            <td><?php echo (isset($course_2_details['id'])) ? $this->crud_model->get_lessons('course', $course_2_details['id'])->num_rows() : '-'; ?></td>
                            <td><?php echo (isset($course_3_details['id'])) ? $this->crud_model->get_lessons('course', $course_3_details['id'])->num_rows() : '-'; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo site_phrase('total_duration'); ?></th>
                            <td><?php echo (isset($course_1_details['id'])) ? $this->crud_model->get_total_duration_of_lesson_by_course_id($course_1_details['id']) : '-'; ?></td>
                            <td><?php echo (isset($course_2_details['id'])) ? $this->crud_model->get_total_duration_of_lesson_by_course_id($course_2_details['id']) : '-'; ?></td>
                            <td><?php echo (isset($course_3_details['id'])) ? $this->crud_model->get_total_duration_of_lesson_by_course_id($course_3_details['id']) : '-'; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo site_phrase('number_of_reviews'); ?></th>
                            <td><?php echo (isset($course_1_details['id'])) ? $this->crud_model->get_ratings('course', $course_1_details['id'])->num_rows() : '-'; ?></td>
                            <td><?php echo (isset($course_2_details['id'])) ? $this->crud_model->get_ratings('course', $course_2_details['id'])->num_rows() : '-'; ?></td>
                            <td><?php echo (isset($course_3_details['id'])) ? $this->crud_model->get_ratings('course', $course_3_details['id'])->num_rows() : '-'; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo site_phrase('total_enrolment'); ?></th>
                            <td><?php echo (isset($course_1_details['id'])) ? $this->crud_model->enrol_history($course_1_details['id'])->num_rows() : '-'; ?></td>
                            <td><?php echo (isset($course_2_details['id'])) ? $this->crud_model->enrol_history($course_2_details['id'])->num_rows() : '-'; ?></td>
                            <td><?php echo (isset($course_3_details['id'])) ? $this->crud_model->enrol_history($course_3_details['id'])->num_rows() : '-'; ?></td>
                        </tr>

                        <tr>
                            <th><?php echo site_phrase('avg_rating'); ?></th>
                            <td>
                                <?php if (isset($course_1_details['id'])) : ?>
                                    <?php
                                    $total_rating =  $this->crud_model->get_ratings('course', $course_1_details['id'], true)->row()->rating;
                                    $number_of_ratings = $this->crud_model->get_ratings('course', $course_1_details['id'])->num_rows();
                                    if ($number_of_ratings > 0) {
                                        $average_ceil_rating = ceil($total_rating / $number_of_ratings);
                                    } else {
                                        $average_ceil_rating = 0;
                                    }

                                    for ($i = 1; $i < 6; $i++) : ?>
                                        <?php if ($i <= $average_ceil_rating) : ?>
                                            <i class="fas fa-star filled" style="color: #f5c85b;"></i>
                                        <?php else : ?>
                                            <i class="fas fa-star" style="color: #c7c7c7;"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (isset($course_2_details['id'])) : ?>
                                    <?php
                                    $total_rating =  $this->crud_model->get_ratings('course', $course_2_details['id'], true)->row()->rating;
                                    $number_of_ratings = $this->crud_model->get_ratings('course', $course_2_details['id'])->num_rows();
                                    if ($number_of_ratings > 0) {
                                        $average_ceil_rating = ceil($total_rating / $number_of_ratings);
                                    } else {
                                        $average_ceil_rating = 0;
                                    }

                                    for ($i = 1; $i < 6; $i++) : ?>
                                        <?php if ($i <= $average_ceil_rating) : ?>
                                            <i class="fas fa-star filled" style="color: #f5c85b;"></i>
                                        <?php else : ?>
                                            <i class="fas fa-star" style="color: #c7c7c7;"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (isset($course_3_details['id'])) : ?>
                                    <?php
                                    $total_rating =  $this->crud_model->get_ratings('course', $course_3_details['id'], true)->row()->rating;
                                    $number_of_ratings = $this->crud_model->get_ratings('course', $course_3_details['id'])->num_rows();
                                    if ($number_of_ratings > 0) {
                                        $average_ceil_rating = ceil($total_rating / $number_of_ratings);
                                    } else {
                                        $average_ceil_rating = 0;
                                    }

                                    for ($i = 1; $i < 6; $i++) : ?>
                                        <?php if ($i <= $average_ceil_rating) : ?>
                                            <i class="fas fa-star filled" style="color: #f5c85b;"></i>
                                        <?php else : ?>
                                            <i class="fas fa-star" style="color: #c7c7c7;"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo site_phrase('requirements'); ?></th>
                            <td class="text-left">
                                <ul class="what-you-get__items">
                                    <?php if (isset($course_1_details['id'])) : ?>
                                        <?php foreach (json_decode($course_1_details['requirements']) as $requirement) : ?>
                                            <?php if ($requirement != "") : ?>
                                                <li><?php echo $requirement; ?></li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </ul>
                            </td>
                            <td class="text-left">
                                <ul class="what-you-get__items">
                                    <?php if (isset($course_2_details['id'])) : ?>
                                        <?php foreach (json_decode($course_2_details['requirements']) as $requirement) : ?>
                                            <?php if ($requirement != "") : ?>
                                                <li><?php echo $requirement; ?></li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </ul>
                            </td>
                            <td class="text-left">
                                <ul class="what-you-get__items">
                                    <?php if (isset($course_3_details['id'])) : ?>
                                        <?php foreach (json_decode($course_3_details['requirements']) as $requirement) : ?>
                                            <?php if ($requirement != "") : ?>
                                                <li><?php echo $requirement; ?></li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo site_phrase('outcomes'); ?></th>
                            <td class="text-left">
                                <ul class="what-you-get__items">
                                    <?php if (isset($course_1_details['id'])) : ?>
                                        <?php foreach (json_decode($course_1_details['outcomes']) as $outcome) : ?>
                                            <?php if ($outcome != "") : ?>
                                                <li><?php echo $outcome; ?></li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </ul>
                            </td>
                            <td class="text-left">
                                <ul class="what-you-get__items">
                                    <?php if (isset($course_2_details['id'])) : ?>
                                        <?php foreach (json_decode($course_2_details['outcomes']) as $outcome) : ?>
                                            <?php if ($outcome != "") : ?>
                                                <li><?php echo $outcome; ?></li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </ul>
                            </td>
                            <td class="text-left">
                                <ul class="what-you-get__items">
                                    <?php if (isset($course_3_details['id'])) : ?>
                                        <?php foreach (json_decode($course_3_details['outcomes']) as $outcome) : ?>
                                            <?php if ($outcome != "") : ?>
                                                <li><?php echo $outcome; ?></li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo site_phrase('buy_now'); ?></th>
                            <td>
                                <?php if (isset($course_1_details['id'])) : ?>
                                    <?php if (is_purchased($course_1_details['id'])) : ?>
                                        <a href="javascript::" class="btn btn-buy-now"><?php echo site_phrase('already_purchased'); ?></a>
                                    <?php else : ?>
                                        <?php if ($course_1_details['is_free_course'] == 1) : ?>
                                            <?php if ($this->session->userdata('user_login') != 1) : ?>
                                                <a href="#" class="btn btn-buy-now" onclick="handleEnrolledButton()"><?php echo site_phrase('get_enrolled'); ?></a>
                                            <?php else : ?>
                                                <a href="<?php echo site_url('home/get_enrolled_to_free_course/' . $course_1_details['id']); ?>" class="btn btn-buy-now"><?php echo site_phrase('get_enrolled'); ?></a>
                                            <?php endif; ?>
                                        <?php else : ?>
                                            <a href="javascript::" class="btn btn-buy-now" id="course_<?php echo $course_1_details['id']; ?>" onclick="handleBuyNow(this)"><?php echo site_phrase('buy_now'); ?></a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (isset($course_2_details['id'])) : ?>
                                    <?php if (is_purchased($course_2_details['id'])) : ?>
                                        <a href="javascript::" class="btn btn-buy-now"><?php echo site_phrase('already_purchased'); ?></a>
                                    <?php else : ?>
                                        <?php if ($course_2_details['is_free_course'] == 1) : ?>
                                            <?php if ($this->session->userdata('user_login') != 1) : ?>
                                                <a href="#" class="btn btn-buy-now" onclick="handleEnrolledButton()"><?php echo site_phrase('get_enrolled'); ?></a>
                                            <?php else : ?>
                                                <a href="<?php echo site_url('home/get_enrolled_to_free_course/' . $course_2_details['id']); ?>" class="btn btn-buy-now"><?php echo site_phrase('get_enrolled'); ?></a>
                                            <?php endif; ?>
                                        <?php else : ?>
                                            <a href="javascript::" class="btn btn-buy-now" id="course_<?php echo $course_2_details['id']; ?>" onclick="handleBuyNow(this)"><?php echo site_phrase('buy_now'); ?></a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (isset($course_3_details['id'])) : ?>
                                    <?php if (is_purchased($course_3_details['id'])) : ?>
                                        <a href="javascript::" class="btn btn-buy-now"><?php echo site_phrase('already_purchased'); ?></a>
                                    <?php else : ?>
                                        <?php if ($course_3_details['is_free_course'] == 1) : ?>
                                            <?php if ($this->session->userdata('user_login') != 1) : ?>
                                                <a href="#" class="btn btn-buy-now" onclick="handleEnrolledButton()"><?php echo site_phrase('get_enrolled'); ?></a>
                                            <?php else : ?>
                                                <a href="<?php echo site_url('home/get_enrolled_to_free_course/' . $course_3_details['id']); ?>" class="btn btn-buy-now"><?php echo site_phrase('get_enrolled'); ?></a>
                                            <?php endif; ?>
                                        <?php else : ?>
                                            <a href="javascript::" class="btn btn-buy-now" id="course_<?php echo $course_3_details['id']; ?>" onclick="handleBuyNow(this)"><?php echo site_phrase('buy_now'); ?></a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<script>
    function compareCourses(selectedCourse, courseNumber) {
        selectedCourse = selectedCourse.split('_');
        var courseSlug = selectedCourse[0];
        var courseId = selectedCourse[1];
        $('#course-' + courseNumber).val(courseSlug);
        $('#course-id-' + courseNumber).val(courseId);

        $('.comparison-form').submit();
    }

    function handleBuyNow(elem) {

        url1 = '<?php echo site_url('home/handleCartItemForBuyNowButton'); ?>';
        url2 = '<?php echo site_url('home/refreshWishList'); ?>';
        urlToRedirect = '<?php echo site_url('home/shopping_cart'); ?>';
        var explodedArray = elem.id.split("_");
        var course_id = explodedArray[1];

        $.ajax({
            url: url1,
            type: 'POST',
            data: {
                course_id: course_id
            },
            success: function(response) {
                $('#cart_items').html(response);
                $.ajax({
                    url: url2,
                    type: 'POST',
                    success: function(response) {
                        $('#wishlist_items').html(response);
                        toastr.warning('<?php echo site_phrase('please_wait') . '....'; ?>');
                        setTimeout(
                            function() {
                                window.location.replace(urlToRedirect);
                            }, 1500);
                    }
                });
            }
        });
    }

    function handleEnrolledButton() {
        $.ajax({
            url: '<?php echo site_url('home/isLoggedIn'); ?>',
            success: function(response) {
                if (!response) {
                    window.location.replace("<?php echo site_url('login'); ?>");
                }
            }
        });
    }
</script>