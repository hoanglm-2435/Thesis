$(document).ready(function () {
    const url = window.location.pathname;
    const cateId = url.substring(url.lastIndexOf('/') + 1);

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: 'get',
        url: route('cate.get-chart', cateId),
        success: function (response) {
            Chart.defaults.global.defaultFontColor = '#000000';
            Chart.defaults.global.defaultFontFamily = 'Helvetica';
            let barChart = document.getElementById('barChart');
            new Chart(barChart, {
                type: 'bar',
                data: {
                    labels: response.labels,
                    datasets: [
                        {
                            label: response.revenue_label,
                            data: response.total_revenue,
                            // data: [450000, 450000, 800000, 450000, 450000, 450000, 450000, 1000000, 450000, 450000, 450000, 450000],
                            backgroundColor: 'blue',
                            borderColor: 'rgba(0, 128, 128, 0.7)',
                            borderWidth: 1,
                            yAxisID: "revenue"
                        },
                        {
                            label: response.sold_label,
                            data: response.total_sold,
                            // data: [45, 59, 80, 81, 56, 55, 40, 100, 50, 10, 60, 12],
                            backgroundColor: 'rgba(0, 128, 128, 0.7)',
                            borderColor: 'rgba(0, 128, 128, 1)',
                            borderWidth: 1,
                            yAxisID: "sold"
                        }
                    ]
                },
                options: {
                    responsive: true,
                    elements: {
                        line :{
                            fill: false
                        }
                    },
                    title: {
                        display: true,
                        position: 'bottom',
                        text: response.title_chart,
                        fontSize: 16
                    },
                    scales: {
                        yAxes: [{
                            display: true,
                            position: 'left',
                            type: "linear",
                            scaleLabel: {
                                display: true,
                                labelString: 'VND',
                                beginAtZero: true,
                            },
                            gridLines: {
                                display: true
                            },
                            ticks: {
                                beginAtZero: true,
                                callback: function (value, index, values) {
                                    return value.toLocaleString('vi-VN', {
                                        style: 'currency',
                                        currency: 'VND'
                                    });
                                }
                            },
                            id: "revenue"
                        },{
                            scaleLabel: {
                                display: true,
                                labelString: response.right_label,
                                beginAtZero: true,
                            },
                            display: true,
                            type: "linear",
                            position:"right",
                            gridLines: {
                                display: true
                            },
                            ticks: {
                                stepSize: 100,
                            },
                            id: "sold"
                        }]
                    },
                    tooltips: {
                        callbacks: {
                            label: function (tooltipItem, data) {
                                if (tooltipItem.datasetIndex === 0) {
                                    return tooltipItem.yLabel.toLocaleString('vi-VN', {
                                        style: 'currency',
                                        currency: 'VND'
                                    });
                                } else if (tooltipItem.datasetIndex === 1) {
                                    return tooltipItem.yLabel + ' ' + response.right_label;
                                }
                            }
                        }
                    }
                }
            });
        }
    })
});
