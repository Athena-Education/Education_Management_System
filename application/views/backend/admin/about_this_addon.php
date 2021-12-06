<?php
$addon_details = $this->db->get_where('addons', array('id' => $param2))->row_array();
?>
<h5 class="mt-0"><?php echo $addon_details['name']; ?></h5>
<p><?php echo $addon_details['about']; ?></p>
<p><?php echo get_phrase('for_more_details_check_out_our'); ?> <a href="http://academy-lms.com/" target="_blank"><?php echo get_phrase('website'); ?></a> </p>
