@php
    $chart = $allReservedApplicationsInLastAcademicYear['chart'];
    $labels = $allReservedApplicationsInLastAcademicYear['labels'];
    $data = $allReservedApplicationsInLastAcademicYear['data'];
    $colors = $allReservedApplicationsInLastAcademicYear['colors'];
@endphp

<div class="flex mr-4 w-full">
    <div id="chart3"></div>
</div>

<script src="{{ $chart->cdn() }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var options = {
            chart: {
                type: 'bar',
                height: 250,
                width: 600,
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    distributed: true,
                }
            },
            colors: @json($colors),
            xaxis: {
                categories: @json($labels),
            },
            series: [{
                name: 'All Reserved Applications',
                data: @json($data)
            }],
            title: {
                text: 'Total Number of Reserved Applications by Academic Year'
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart3"), options);
        chart.render();
    });
</script>
