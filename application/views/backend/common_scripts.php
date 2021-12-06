<script type="text/javascript">
  function togglePriceFields(elem) {
    if($("#"+elem).is(':checked')){
      $('.paid-course-stuffs').slideUp();
    }else
      $('.paid-course-stuffs').slideDown();
    }
</script>

<?php if ($page_name == 'courses-server-side'): ?>
  <script type="text/javascript">
  jQuery(document).ready(function($) {
        $.fn.dataTable.ext.errMode = 'throw';
        $('#course-datatable-server-side').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax":{
                "url": "<?php echo site_url(strtolower($this->session->userdata('role')).'/get_courses') ?>",
                "dataType": "json",
                "type": "POST",
                "data" : {selected_category_id : '<?php echo $selected_category_id; ?>',
                          selected_status : '<?php echo $selected_status ?>',
                          selected_instructor_id : '<?php echo $selected_instructor_id ?>',
                          selected_price : '<?php echo $selected_price ?>'}
            },
            "columns": [
                { "data": "#" },
                { "data": "title" },
                { "data": "category" },
                { "data": "lesson_and_section" },
                { "data": "enrolled_student" },
                { "data": "status" },
                { "data": "price" },
                { "data": "actions" },
            ],
            "columnDefs": [{
                targets: "_all",
                orderable: false
             }]
        });
    });
  </script>
<?php endif; ?>
