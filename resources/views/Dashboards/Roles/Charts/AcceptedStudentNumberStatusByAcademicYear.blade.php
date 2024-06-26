@php
    $chart = $acceptedStudentNumberStatusByAcademicYear['chart'];
    $labels = $acceptedStudentNumberStatusByAcademicYear['labels'];
    $data = $acceptedStudentNumberStatusByAcademicYear['data'];
    $colors = $acceptedStudentNumberStatusByAcademicYear['colors'];
@endphp

<div class="flex mr-4 w-full">
    <div id="chart2"></div>
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
                name: 'Approved Students',
                data: @json($data)
            }],
            title: {
                text: 'Approved students by academic year'
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart2"), options);
        chart.render();
    });
</script>
