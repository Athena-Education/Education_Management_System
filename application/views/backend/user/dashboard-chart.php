<?php
     $months = array('january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december');
     $month_wise_income = array();

    for ($i = 0; $i < 12; $i++) {
        $first_day_of_month = "1 ".ucfirst($months[$i])." ".date("Y").' 00:00:00';
        // I am commenting this out because in some servers "cal_days_in_month" function does not work because they do not have the calender support enable. They have to enable it and recompile php
        $last_day_of_month = date("t", strtotime($first_day_of_month))." ".ucfirst($months[$i])." ".date("Y").' 00:00:00';

        $course_ids = $this->crud_model->get_instructor_wise_courses($this->session->userdata('user_id'), 'simple_array');
        if (count($course_ids) > 0) {
            $this->db->select_sum('instructor_revenue');
            $this->db->where('date_added >=' , strtotime($first_day_of_month));
            $this->db->where('date_added <=' , strtotime($last_day_of_month));
            $this->db->where_in('course_id', $course_ids);
            $total_instructor_revenue = $this->db->get('payment')->row()->instructor_revenue;
        }else{
            $total_instructor_revenue = 0;
        }
        $total_instructor_revenue > 0 ? array_push($month_wise_income, $total_instructor_revenue) : array_push($month_wise_income, 0);
    }
    $number_of_active_course  = $this->crud_model->get_status_wise_courses_for_instructor('active')->num_rows();
    $number_of_pending_course = $this->crud_model->get_status_wise_courses_for_instructor('pending')->num_rows();
?>

 <script type="text/javascript">
 ! function(o) {
     "use strict";
     var t = function() {
         this.$body = o("body"), this.charts = []
     };
     t.prototype.respChart = function(r, a, n, e) {
         Chart.defaults.global.defaultFontColor = "#8391a2", Chart.defaults.scale.gridLines.color = "#8391a2";
         var i = r.get(0).getContext("2d"),
             s = o(r).parent();
         return function() {
             var t;
             switch (r.attr("width", o(s).width()), a) {
                 case "Line":
                     t = new Chart(i, {
                         type: "line",
                         data: n,
                         options: e
                     });
                     break;
                 case "Doughnut":
                     t = new Chart(i, {
                         type: "doughnut",
                         data: n,
                         options: e
                     })
             }
             return t
         }()
     }, t.prototype.initCharts = function() {
         var t = [];
         if (0 < o("#task-area-chart").length) {
             t.push(this.respChart(o("#task-area-chart"), "Line", {
                 labels: [
                      <?php foreach ($months as $month): ?>
                    "<?php echo ucfirst($month); ?>",
                    <?php endforeach; ?>
                 ],
                 datasets: [{
                     label: "<?php echo get_phrase('this_year'); ?>",
                     backgroundColor: "rgba(114, 124, 245, 0.3)",
                     borderColor: "#727cf5",
                     data: [
                         <?php foreach ($month_wise_income as $income): ?>
                        "<?php echo $income; ?>",
                        <?php endforeach; ?>
                     ]
                 }]
             }, {
                 maintainAspectRatio: !1,
                 legend: {
                     display: !1
                 },
                 tooltips: {
                     intersect: !1
                 },
                 hover: {
                     intersect: !0
                 },
                 plugins: {
                     filler: {
                         propagate: !1
                     }
                 },
                 scales: {
                     xAxes: [{
                         reverse: !0,
                         gridLines: {
                             color: "rgba(0,0,0,0.05)"
                         }
                     }],
                     yAxes: [{
                         ticks: {
                             stepSize: 10,
                             display: !1
                         },
                         min: 10,
                         max: 100,
                         display: !0,
                         borderDash: [5, 5],
                         gridLines: {
                             color: "rgba(0,0,0,0)",
                             fontColor: "#fff"
                         }
                     }]
                 }
             }))
         }
         if (0 < o("#project-status-chart").length) {
             t.push(this.respChart(o("#project-status-chart"), "Doughnut", {
                 labels: ["<?php echo get_phrase('active_course'); ?>", "<?php echo get_phrase('pending_course'); ?>"],
                 datasets: [{
                     data: [<?php echo $number_of_active_course; ?>, <?php echo $number_of_pending_course; ?>],
                     backgroundColor: ["#0acf97", "#FFC107"],
                     borderColor: "transparent",
                     borderWidth: "2"
                 }]
             }, {
                 maintainAspectRatio: !1,
                 cutoutPercentage: 80,
                 legend: {
                     display: !1
                 }
             }))
         }
         return t
     }, t.prototype.init = function() {
         var r = this;
         Chart.defaults.global.defaultFontFamily = '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif', r.charts = this.initCharts(), o(window).on("resize", function(t) {
             o.each(r.charts, function(t, r) {
                 try {
                     r.destroy()
                 } catch (t) {}
             }), r.charts = r.initCharts()
         })
     }, o.ChartJs = new t, o.ChartJs.Constructor = t
 }(window.jQuery),
 function(t) {
     "use strict";
     window.jQuery.ChartJs.init()
 }();

 </script>
