/* 
 * global cd7stdbl_admin_js_params
 * Contact Form 7 Store To DB
 * Since Version 1.0.0
 */

/* 
 * Default Values 
 backgroundColor: "rgba(255,99,132,0.2)",
 borderColor: "rgba(255,99,132,1)",
 borderWidth: 1,
 hoverBackgroundColor: "rgba(255,99,132,0.4)",
 hoverBorderColor: "rgba(255,99,132,1)",
 const data1 = [6, 3, 2, 8, 4, 3, 2];
 const data2 = [3, 4, 3, 4, 5, 8, 5];
 */
(function ($) {
    $(function () {
        if ($('#chart_0').length) {
            var total_submission_datas = $('body').find('#chart_0').attr('data-total-submission');
            var datas_obj = $.parseJSON(total_submission_datas);
            //var total_submission_data = JSON.parse(total_submission_datas);
            //var total_submission_header_datas = $('body').find('#chart_0').attr('data-form-header');
            //var total_submission_header_data = JSON.parse(total_submission_header_datas);
            var total_submission_label = $('body').find('#chart_0').attr('data-label-text');
            var header_text = $('body').find('#chart_0').attr('data-label-header');
            var flag_value = $('body').find('#chart_0').attr('data-flag-value');
            const monthly_dat_label = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            if (flag_value === 'month') {
                xlabel = monthly_dat_label;
            } else {
                xlabel = monthly_dat_label;
            }
            var chartData = {
                labels: xlabel,
                datasets: datas_obj
            };

            var option = {
                layout: {
                    padding: {
                        left: 10,
                        right: 10
                    }
                },
                legend: {
                    display: true,
                    onClick: (e) => e.stopPropagation(),
                    labels: {
                        usePointStyle: true
                    }
                },
                responsive: true,
                scales: {
                    yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                stacked: true
                            },
                            scaleLabel: {
                                display: true,
                                labelString: total_submission_label
                            }
                        }]
                },
                tooltips: {
                    mode: 'index'
                },
                title: {
                    display: true,
                    text: header_text,
                    fontSize: 14,
                    position: 'top'
                }

            };
            var ctx = $("#chart_0");
            mychart = new Chart(ctx, {
                type: 'line',
                options: option,
                data: chartData,
                showTooltips: true,
                scaleSteps: 1,
                scaleOverride: true,
                scaleStepWidth: 50,
                scaleStartValue: 0,
                onAnimationComplete: function () {
                    var ctx = this.Chart.ctx;
                    ctx.font = this.scale.font;
                    ctx.fillStyle = this.scale.textColor;
                    ctx.textAlign = "center";
                    ctx.textBaseline = "bottom";
                    this.datasets.forEach(function (dataset) {
                        dataset.bars.forEach(function (bar) {
                            ctx.fillText(bar.value, bar.x, bar.y - 5);
                        });
                    });
                }
            });
        }

        $("#cd7stdb-store-report-button").click(function () {
            var canvasdiv = document.getElementById('chart_0');
            if ($('#chart_0').length) {
                var canvasreportheader = $('body').find('#chart_0').attr('data-entry-img-label');
            } else {
                var canvasreportheader = 'cf7stdb-entry-report';
            }

            html2canvas(canvasdiv,
                    {
                        useCORS: true,
                        allowTaint: true,
                        letterRendering: true,
                        onrendered: function (canvas) {
                            var a = document.createElement('a');
                            a.href = canvas.toDataURL("image/png").replace("image/jpeg", "image/octet-stream");
                            a.download = canvasreportheader + '.jpg';
                            a.click();
                        }
                    });
        });
        /*
         * Implementing Date Picker
         */
        $('.cd7stdb-range-entry-filter').datepicker({
            dateFormat: 'yy',
            changeMonth: false,
            changeYear: true
        });
        /*
         * Implementing Reset or clear
         */
        $('.cd7stdb-range-entry-clear').on('click', function (e) {
            e.preventDefault();
            $(this).parent().find('.cd7stdb-range-entry-filter').val('');
        });
        /*
         * Post Entry Status
         */
        $('a.row-title').click(function (e) {
            e.preventDefault();
            var $this = $(this);
            var href_redir_link = $this.attr("href");
            var cf7_form_id = $this.parents('tr.iedit').attr('id');
            var break_cf7_form_id = cf7_form_id.split('-');
            var post_id = break_cf7_form_id[1];
            $.ajax({
                url: cd7stdbl_admin_js_params.ajax_url,
                data: {
                    post_id: post_id,
                    _wpnonce: cd7stdbl_admin_js_params.ajax_nonce,
                    action: 'cd7stdbl_entry_status'
                },
                type: 'post',
                beforeSend: function () {
                    $this.parents('td.page-title').append('<span class="spinner cf7stdb-view-wrap is-active"></span>');
                },
                success: function (response) {
                    $this.parents('tr.iedit').find('td.column-status').html('<span class="cf7stdb-form-stat cf7stdb-stat-read">' + response + '</span>');
                    window.location.href = href_redir_link;
                },
                complete: function () {
                    $this.parents('tr.iedit').find('span.cf7stdb-view-wrap').remove();
                }
            });
        });

        /*
         * Report generate loader display
         */
        $('#cd7stdb-generate-report-button').click(function () {
            $(this).parent().find('.cf7stdb-report-load-wrap').show();
        });
    }); /** Function ends */
}(jQuery));
