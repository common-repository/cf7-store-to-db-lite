$('#cd7stdb-reports-setting-wrapper').find("#cd7stdb-generate-report-button").click(function (e) {
    e.preventDefault();
    var $this = $(this).parent();
    var cf7_form_id = $('#entries-post-form-specific option:selected').val();
    var cf7_form_data_from = $('#cd7stdb-range-entry-filter-from').val();
    var cf7_form_data_to = $('#cd7stdb-range-entry-filter-to').val();
    console.log(cf7_form_id);
    console.log(cf7_form_data_from);
    console.log(cf7_form_data_to);
    $.ajax({
        url: cd7stdb_admin_js_params.ajax_url,
        data: {
            cf7_form_id: cf7_form_id,
            cf7_form_data_from: cf7_form_data_from,
            cf7_form_data_to: cf7_form_data_to,
            _wpnonce: cd7stdb_admin_js_params.ajax_nonce,
            action: 'cd7stdb_admin_report_form'
        },
        type: 'post',
        beforeSend: function () {
            $('.cd7stdb-view-wrap').show();
        },
        success: function (response) {
            console.log(response);
            $('.cd7stdb-report-wrap').append($('.cd7stdb-report-wrap').html('<canvas id="chart_0" height="400vw" width="800vw" data-total-submission="' + response + '"></canvas>'));
            var data = {
                labels: ['All Entries'],
                datasets: [{
                        label: "Number of post per month",
                        backgroundColor: "rgba(255,99,132,0.2)",
                        borderColor: "rgba(255,99,132,1)",
                        borderWidth: 1,
                        hoverBackgroundColor: "rgba(255,99,132,0.4)",
                        hoverBorderColor: "rgba(255,99,132,1)",
                        data: response
                    }]
            };
            var option = {
                responsive: false,
                scales: {
                    yAxes: [{
                            stacked: true,
                            gridLines: {
                                display: true,
                                color: "rgba(255,99,132,0.2)"
                            }
                        }],
                    xAxes: [{
                            gridLines: {
                                display: false
                            }
                        }]
                },
                maxBarThickness: 10,
                hoverBackgroundColorL: '#008cff'
            };
            chart = new Chart(ctx, {
                type: 'bar',
                options: option,
                data: data
            });
        },
        complete: function () {
            $('.cd7stdb-view-wrap').hide();
        }
    });
});
/* asdasdasda sd 
 
 $args = array(
 'post_type' => 'cf7storetodbs',
 'meta_query' => array(
 'relation' => 'OR',
 array(
 'key' => 'cf7stdb_cf7_id',
 'value' => $cf7_form_id,
 'compare' => 'LIKE'
 ),
 array(
 'key' => 'post_date',
 'value' => array($cf7_form_data_from, $cf7_form_data_to),
 'type' => 'DATE',
 'compare' => 'BETWEEN'
 )
 )
 );
 $query = new WP_Query($args);
 if ($query - > have_posts()) {
 while ($query - > have_posts()) {
 $query - > the_post();
 }
 wp_reset_postdata();
 }
 $count = $query - > post_count;
 array_push($form_data, $count);
 echo json_encode($form_data);
 */