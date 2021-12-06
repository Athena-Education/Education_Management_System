<?php include "profile_menus.php"; ?>

<section class="my-courses-area">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="my-course-search-bar">
                    <form action="">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="<?php echo site_phrase('search_my_courses'); ?>" onkeyup="getMyWishListsBySearchString(this.value)">
                            <div class="input-group-append">
                                <button class="btn" type="button"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="row no-gutters" id="my_wishlists_area">
            <?php foreach ($my_courses as $my_course) :
                $instructor_details = $this->user_model->get_all_user($my_course['user_id'])->row_array(); ?>
                <div class="col-lg-3">
                    <div class="course-box-wrap">
                        <div class="course-box">
                            <div class="course-image">
                                <a href="<?php echo site_url('home/course/' . rawurlencode(slugify($my_course['title'])) . '/' . $my_course['id']); ?>"><img src="<?php echo $this->crud_model->get_course_thumbnail_url($my_course['id']); ?>" alt="" class="img-fluid"></a>
                                <div class="instructor-img-hover">
                                    <a href="<?php echo site_url('home/instructor_page/' . $instructor_details['id']); ?>"><img src="<?php echo $this->user_model->get_user_image_url($instructor_details['id']); ?>" alt=""></a>
                                    <span>
                                        <?php
                                        $lessons = $this->crud_model->get_lessons('course', $my_course['id'])->num_rows();
                                        echo $lessons . ' ' . site_phrase('lessons');
                                        ?>
                                    </span>
                                    <span>
                                        <?php
                                        echo $this->crud_model->get_total_duration_of_lesson_by_course_id($my_course['id']);
                                        ?>
                                    </span>
                                </div>
                                <div class="wishlist-add wishlisted">
                                    <button type="button" data-toggle="tooltip" data-placement="left" title="" style="cursor : auto;" onclick="handleWishList(this)" id="<?php echo $my_course['id']; ?>">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="course-details">
                                <a href="<?php echo site_url('home/course/' . rawurlencode(slugify($my_course['title'])) . '/' . $my_course['id']); ?>">
                                    <h5 class="title"><?php echo $my_course['title']; ?></h5>
                                </a>
                                <p class="instructors">
                                    <?php if ($my_course['multi_instructor']) : ?>
                                        <?php $instructors = $this->user_model->get_multi_instructor_details_with_csv($my_course['user_id']); ?>
                                        <?php foreach ($instructors as $key => $instructor) : ?>
                                            <?php echo $instructor['first_name'] . ' ' . $instructor['last_name']; ?>
                                            <?php echo $key + 1 == count($instructors) ? '' : ', '; ?>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <?php echo $instructor_details['first_name'] . ' ' . $instructor_details['last_name']; ?>
                                    <?php endif; ?>
                                </p>

                                <?php if ($my_course['is_free_course'] == 1) : ?>
                                    <p class="price text-right"><?php echo site_phrase('free'); ?></p>
                                <?php else : ?>
                                    <?php if ($my_course['discount_flag'] == 1) : ?>
                                        <p class="price text-right"><small><?php echo currency($my_course['price']); ?></small><?php echo currency($my_course['discounted_price']); ?></p>
                                    <?php else : ?>
                                        <p class="price text-right"><?php echo currency($my_course['price']); ?></p>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script type="text/javascript">
    function getMyWishListsBySearchString(search_string) {
        $.ajax({
            type: 'POST',
            url: '<?php echo site_url('home/get_my_wishlists_by_search_string'); ?>',
            data: {
                search_string: search_string
            },
            success: function(response) {
                $('#my_wishlists_area').html(response);
            }
        });
    }

    async function handleWishList(elem) {
        try {
            var result = await async_modal();
            if (result) {
                $.ajax({
                    url: '<?php echo site_url('home/handleWishList'); ?>',
                    type: 'POST',
                    data: {
                        course_id: elem.id
                    },
                    success: function(response) {
                        if ($(elem).hasClass('active')) {
                            $(elem).removeClass('active')
                        } else {
                            $(elem).addClass('active')
                        }
                        $('#wishlist_items').html(response);
                        $.ajax({
                            url: '<?php echo site_url('home/reload_my_wishlists'); ?>',
                            type: 'POST',
                            success: function(response) {
                                $('#my_wishlists_area').html(response);
                            }
                        });
                    }
                });
            }
        } catch (e) {
            console.log("Error occured", e.message);
        }
    }
</script>