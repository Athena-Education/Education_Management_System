<?php
$user_details = $this->user_model->get_user($this->session->userdata('user_id'))->row_array();
$cart_items = $this->session->userdata('cart_items');
?>
<div class="icon">
    <a href=""><i class="far fa-heart"></i></a>
    <span class="number"><?php echo sizeof($this->crud_model->getWishLists()); ?></span>
</div>
<div class="dropdown course-list-dropdown corner-triangle top-right">
    <div class="list-wrapper">
        <div class="item-list">
            <ul>
                <?php
                foreach (json_decode($user_details['wishlist']) as $wishlist) :
                    $course_details = $this->crud_model->get_course_by_id($wishlist)->row_array();
                    $instructor_details = $this->user_model->get_all_user($course_details['user_id'])->row_array();
                ?>
                    <li>
                        <div class="item clearfix">
                            <div class="item-image">
                                <a href="">
                                    <img src="<?php echo $this->crud_model->get_course_thumbnail_url($wishlist); ?>" alt="" class="img-fluid">
                                </a>
                            </div>
                            <div class="item-details">
                                <a href="<?php echo site_url('home/course/' . rawurlencode(slugify($course_details['title'])) . '/' . $course_details['id']); ?>">
                                    <div class="course-name"><?php echo $course_details['title']; ?></div>
                                    <div class="instructor-name">
                                        <?php if ($course_details['multi_instructor']) : ?>
                                            <?php $instructors = $this->user_model->get_multi_instructor_details_with_csv($course_details['user_id']); ?>
                                            <?php foreach ($instructors as $key => $instructor) : ?>
                                                <?php echo site_phrase('by') . ' ' . $instructor['first_name'] . ' ' . $instructor['last_name']; ?>
                                                <?php echo $key + 1 == count($instructors) ? '' : ', '; ?>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <?php echo site_phrase('by') . ' ' . $instructor_details['first_name'] . ' ' . $instructor_details['last_name']; ?>
                                        <?php endif; ?>
                                    </div>

                                    <div class="item-price">
                                        <?php if ($course_details['is_free_course'] == 1) : ?>
                                            <span class="current-price"><?php echo site_phrase('free'); ?></span>
                                        <?php else :  ?>
                                            <?php if ($course_details['discount_flag'] == 1) : ?>
                                                <span class="current-price"><?php echo currency($course_details['discounted_price']); ?></span>
                                                <span class="original-price"><?php echo currency($course_details['price']); ?></span>
                                            <?php else : ?>
                                                <span class="current-price"><?php echo currency($course_details['price']); ?></span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </a>
                                <?php if (is_purchased($course_details['id'])) : ?>
                                    <button><?= site_phrase('already_purchased'); ?></button>
                                <?php else : ?>
                                    <?php if ($course_details['is_free_course'] == 0) : ?>
                                        <button type="button" id="<?php echo $course_details['id']; ?>" onclick="handleCartItems(this)" class="<?php if (in_array($course_details['id'], $cart_items)) echo 'addedToCart'; ?>">
                                            <?php
                                            if (in_array($course_details['id'], $cart_items)) {
                                                echo site_phrase('added_to_cart');
                                            } else {
                                                echo site_phrase('add_to_cart');
                                            }
                                            ?>
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="dropdown-footer">
            <a href="<?php echo site_url('home/my_wishlist'); ?>"><?php echo site_phrase('go_to_wishlist'); ?></a>
        </div>
    </div>
    <div class="empty-box text-center d-none">
        <p><?php echo site_phrase('your_wishlist_is_empty'); ?>.</p>
        <a href=""><?php echo site_phrase('explore_courses'); ?></a>
    </div>
</div>