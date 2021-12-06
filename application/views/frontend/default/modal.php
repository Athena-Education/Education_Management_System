<script type="text/javascript">
function showAjaxModal(url)
{
    // SHOWING AJAX PRELOADER IMAGE
    jQuery('#modal_ajax .modal-body').html('<div class="w-100 text-center pt-5"><img class="mt-5 mb-5" width="80px" src="<?= base_url(); ?>assets/global/gif/page-loader-2.gif"></div>');

    // LOADING THE AJAX MODAL
    jQuery('#modal_ajax').modal('show', {backdrop: 'true'});

    // SHOW AJAX RESPONSE ON REQUEST SUCCESS
    $.ajax({
        url: url,
        success: function(response)
        {
            jQuery('#modal_ajax .modal-body').html(response);
        }
    });
}
</script>

<!-- (Ajax Modal)-->
<div class="modal fade" id="modal_ajax">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body" style="overflow:auto;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>




<script type="text/javascript">
function confirm_modal(delete_url)
{
    jQuery('#modal-4').modal('show', {backdrop: 'static'});
    document.getElementById('delete_link').setAttribute('href' , delete_url);
}
</script>

<!-- (Normal Modal)-->
<div class="modal fade" id="modal-4">
    <div class="modal-dialog">
        <div class="modal-content" style="margin-top:100px;">

            <div class="modal-header">
                <h4 class="modal-title text-center"><?php echo site_phrase('are_you_sure'); ?> ?</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>


            <div class="modal-footer" style="margin:0px; border-top:0px; text-align:center;">
                <a href="#" class="btn btn-danger btn-yes" id="delete_link" data-dismiss="modal"><?php echo site_phrase('yes');?></a>
                <button type="button" class="btn btn-info btn-cancel" data-dismiss="modal"><?php echo site_phrase('no');?></button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
function async_modal() {
    const asyncModal = new Promise(function(resolve, reject){
        $('#modal-4').modal('show');
        $('#modal-4 .btn-yes').click(function(){
            resolve(true);
        });
        $('#modal-4 .btn-cancel').click(function(){
            resolve(false);
        });
    });
    return asyncModal;
}
</script>
