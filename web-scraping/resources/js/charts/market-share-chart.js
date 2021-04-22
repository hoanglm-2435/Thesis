$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type: 'get',
        url: route('market-share.get-chart'),
        success: function (response) {
            // Chart.defaults.global.defaultFontColor = '#000000';
            // Chart.defaults.global.defaultFontFamily = 'Helvetica';

            const soldChart = $('#soldChart').get(0).getContext('2d')
            new Chart(soldChart, {
                type: 'doughnut',
                data: {
                    labels: response.labels,
                    datasets: [
                        {
                            data: response.total_sold,
                            backgroundColor: response.color,
                        }
                    ]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    title: {
                        display: true,
                        position: 'bottom',
                        text: 'Statistical chart of total products sold by category',
                        fontSize: 20
                    },
                    tooltips: {
                        callbacks: {
                            label: function (tooltipItem, data) {
                                const label = ' ' + data['labels'][tooltipItem['index']] + ': '
                                    + data['datasets'][0]['data'][tooltipItem['index']] + ' products';
                                return label;
                            },
                        }
                    }
                }
            })

            const revenueChart = $('#revenueChart').get(0).getContext('2d')
            new Chart(revenueChart, {
                type: 'doughnut',
                data: {
                    labels: response.labels,
                    datasets: [
                        {
                            data: response.total_revenue,
                            backgroundColor: response.color,
                        }
                    ]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    title: {
                        display: true,
                        position: 'bottom',
                        text: 'Statistical chart of total revenue by category',
                        fontSize: 20
                    },
                    tooltips: {
                        callbacks: {
                            label: function (tooltipItem, data) {
                                const formatter = new Intl.NumberFormat('it-IT', {
                                    style: 'currency',
                                    currency: 'VND',
                                    minimumFractionDigits: 0
                                })
                                const label = ' ' + data['labels'][tooltipItem['index']] + ': '
                                    + formatter.format(data['datasets'][0]['data'][tooltipItem['index']]);
                                return label;
                            }
                        }
                    }
                }
            })

            const shopChart = $('#shopChart').get(0).getContext('2d')
            new Chart(shopChart, {
                type: 'doughnut',
                data: {
                    labels: response.labels,
                    datasets: [
                        {
                            data: response.total_shop,
                            backgroundColor: response.color,
                        }
                    ]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    title: {
                        display: true,
                        position: 'bottom',
                        text: 'Statistics chart of the total number of shopee mall by category',
                        fontSize: 20
                    },
                    tooltips: {
                        callbacks: {
                            label: function (tooltipItem, data) {
                                const label = ' ' + data['labels'][tooltipItem['index']] + ': '
                                    + data['datasets'][0]['data'][tooltipItem['index']] + ' shop';
                                return label;
                            },
                        }
                    }
                }
            })

            const productChart = $('#productChart').get(0).getContext('2d')
            new Chart(productChart, {
                type: 'doughnut',
                data: {
                    labels: response.labels,
                    datasets: [
                        {
                            data: response.total_product,
                            backgroundColor: response.color,
                        }
                    ]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    title: {
                        display: true,
                        position: 'bottom',
                        text: 'Statistics chart of the total products of shopee mall by category',
                        fontSize: 20
                    },
                    tooltips: {
                        callbacks: {
                            label: function (tooltipItem, data) {
                                const label = ' ' + data['labels'][tooltipItem['index']] + ': '
                                    + data['datasets'][0]['data'][tooltipItem['index']] + ' products';
                                return label;
                            },
                        }
                    }
                }
            })
        }
    })
});
