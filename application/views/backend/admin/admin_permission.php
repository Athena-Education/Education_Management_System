<?php
// DEFINING MODULE FOR SETTING PERMISSION
// MAKE SURE TO KEEP A PERMISSION FOR USERS AND THEME
$modules = [
    'category', 'course', 'user', 'instructor', 'student', 'enrolment', 'revenue', 'messaging', 'addon', 'theme', 'settings', 'coupon'
];

?>
<div class="row ">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="page-title"> <i class="mdi mdi-apple-keyboard-command title_icon"></i> <?php echo ucwords($page_title); ?>
                    <a href="<?php echo site_url('admin/admins'); ?>" class="btn btn-outline-primary btn-rounded alignToTitle"> <i class="mdi mdi-arrow-left"></i> <?php echo get_phrase('back_to_admins'); ?></a>
                </h4>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>


<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title">
                    <?php echo get_phrase('assign_permission_for'); ?> : <?php echo $permission_assign_to['first_name'] . ' ' . $permission_assign_to['last_name']; ?>
                </h4>
                <div class="text-muted">
                    <small> <strong><?php echo get_phrase('note'); ?></strong> : <?php echo get_phrase('you_can_toggle_the_switch_for_enabling_or_disabling_a_feature_to_access'); ?>.</small>
                </div>
                <div class="table-responsive-sm mt-4">
                    <table class="table table-striped table-centered mb-0">
                        <thead>
                            <tr>
                                <th><?php echo get_phrase('feature'); ?></th>
                                <th><?php echo get_phrase('action'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($modules as $module) :
                                $module_permission = has_permission($module, $permission_assign_to['id']);
                            ?>
                                <tr>
                                    <td><?php echo ucwords(get_phrase($module)); ?></td>
                                    <td>
                                        <!-- Bool Switch-->
                                        <input type="checkbox" class="" id="<?php echo $permission_assign_to['id'] . '-' . $module; ?>" data-switch="bool" onchange="setPermission('<?php echo $permission_assign_to['id'] . '-' . $module; ?>')" <?php echo $module_permission ? "checked" : ""; ?> />
                                        <label for="<?php echo $permission_assign_to['id']  . '-' . $module; ?>" data-on-label="On" data-off-label="Off"></label>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>



<script>
    "use strict";

    function setPermission(arg) {
        // CALL THE SERVER SIDE
        $.ajax({
            url: '<?php echo site_url('admin/assign_permission'); ?>',
            type: 'POST',
            data: {
                arg: arg
            },
            success: function(response) {
                $.NotificationApp.send("<?php echo get_phrase('heads_up'); ?>!", '<?php echo get_phrase('permission_updated'); ?>', "top-right", "rgba(0,0,0,0.2)", "info");
            }
        });
    }
</script>