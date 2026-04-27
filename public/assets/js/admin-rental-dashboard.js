document.addEventListener('DOMContentLoaded', function () {
    var chartElement = document.getElementById('adminRentalMonthlyBookingsChart');
    var dataElement = document.getElementById('admin-rental-monthly-bookings-data');

    if (!chartElement || !dataElement || typeof Chart === 'undefined') {
        return;
    }

    var payload = {};

    try {
        payload = JSON.parse(dataElement.textContent || '{}');
    } catch (error) {
        payload = {};
    }

    var labels = Array.isArray(payload.labels) ? payload.labels : [];
    var values = Array.isArray(payload.values) ? payload.values : [];

    var hasData = values.some(function (value) {
        return Number(value) > 0;
    });

    var emptyState = document.querySelector('[data-admin-rental-chart-empty]');

    if (!hasData) {
        if (emptyState) {
            emptyState.hidden = false;
        }
        if (chartElement.parentElement) {
            chartElement.parentElement.hidden = true;
        }
        return;
    }

    if (emptyState) {
        emptyState.hidden = true;
    }

    new Chart(chartElement, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Booking',
                data: values,
                backgroundColor: 'rgba(108, 140, 245, 0.82)',
                borderColor: 'rgba(90, 121, 230, 1)',
                borderWidth: 1,
                borderRadius: 10,
                maxBarThickness: 42,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    backgroundColor: '#1f2937',
                    titleFont: {
                        family: 'Montserrat',
                        weight: '700',
                    },
                    bodyFont: {
                        family: 'Poppins',
                    },
                    padding: 12,
                    displayColors: false,
                },
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                    },
                    ticks: {
                        color: '#6b7280',
                        font: {
                            family: 'Poppins',
                        },
                    },
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        color: '#6b7280',
                        font: {
                            family: 'Poppins',
                        },
                    },
                    grid: {
                        color: 'rgba(229, 231, 235, 0.85)',
                    },
                },
            },
        },
    });
});
