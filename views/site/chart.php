<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Chart';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-chart">
    <h1><?= Html::encode($this->title) ?></h1>

    <div id="period-selections">
        <span>Select Start Date:</span>
        <input type="date" name="period_start">

        <span>Select End Date:</span>
        <input type="date" name="period_end">

        <span>Select Group Unit:</span>
        <select name="period_group_unit">
            <option value="year">Year</option>
            <option value="month">Month</option>
            <option value="day">Day</option>
        </select>
        <button id="btn-view" type="button">View</button>
    </div>
    <canvas id="myChart" width="400" height="400"></canvas>
    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            const formData = new FormData();
            const response = new XMLHttpRequest();
            let period_start, period_end, period_group_unit;
            jQuery("#btn-view").click(function () {
                period_start = $("input[name=period_start]").val();
                period_end = $("input[name=period_end]").val();
                period_group_unit = $("select[name=period_group_unit]").val();
                if (!period_start || !period_end || !period_group_unit) {
                    alert("All options are required.");
                    return;
                } else {
                    let chart = Chart.getChart("myChart");
                    if (chart != undefined) {
                        chart.destroy();
                    }
                    var request_url = window.location.origin +
                        "/index.php/messages/total?period_start=" + period_start +
                        "&period_end=" + period_end +
                        "&period_group_unit=" + period_group_unit;
                    response.open("GET", request_url);
                    response.send(formData);

                    response.onload = (e) => {
                        var return_data = JSON.parse(JSON.parse(response.response)).data;
                        if (!(return_data)) {
                            return;
                        }
                        var response_labels = [];
                        var response_data = [];
                        for (let i = 0; i < return_data.length; i++) {
                            response_labels[i] = return_data[i]["period_start"] + " - " + return_data[i]["period_end"];
                            response_data[i] = return_data[i]["message_number"];
                        }
                        const ctx = document.getElementById('myChart');
                        const myChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: response_labels,
                                datasets: [{
                                    label: '# of Messages',
                                    data: response_data,
                                    borderColor: ['rgba(10, 200, 100, 150)'],
                                }],
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    }
                }
            });
        });
    </script>
</div>
