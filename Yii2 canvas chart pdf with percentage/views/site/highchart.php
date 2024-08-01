<?php
	use yii\helpers\Html;
	use yii\widgets\DetailView;
  use yii\helpers\Url;

?>

<div class="container mt-3">
  <h2>High Chart</h2>


  <span><?= Html::a('Generate High Chart PDF', ['site/highchartpdf'], ['class' => 'btn btn-primary', 'target' => '_blank']) ?></span>
  
  


    <div id="improvementChart1" style="width: 100%; height: 300px; background: #F7F7F7;"></div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>

    <script>
    Highcharts.chart('improvementChart1', {
        chart: {
            backgroundColor: null,
        },
        title: {
            text: null,
        },
        xAxis: {
            categories: ['Easy process of doing fund transfers', 'Process is quick', '24x7 , 365 days incl holidays', 'Knowing beneficiary / payee name before making payment']
        },
        credits: {
            enabled: false
        },
        yAxis: [{
            title: {
                text: '',
                style: {
                    color: 'rgba(248, 134, 28, 1)'
                }
            },
            labels: {
                enabled: false
            },
            gridLineWidth: 0
        }],
        tooltip: {
            valueSuffix: null
        },
        plotOptions: {
            column: {
                pointWidth: 50,
                dataLabels: {
                    enabled: true,
                    color: '#1B1B1B',
                    style: {
                        fontWeight: 'bold',
                    },
                    formatter: function () {
                        return this.y + '%';
                    }
                }
            },
            series: {
                showInLegend: false
            }
        },
        series: [{
            type: 'column',
            color: '#2EA5BC',
            name: '2024',
            data: [33, 25, 25, 17],
        }],
        exporting: {
            enabled: true,
            buttons: {
                contextButton: {
                    menuItems: [
                        'downloadPNG',
                        'downloadJPEG',
                        'downloadPDF',
                        'downloadSVG'
                    ]
                }
            }
        }
    });

    function sendChartToServer() {
        var chart = Highcharts.charts[0]; // Assuming this is the only chart on the page
        chart.exportChartLocal({
            type: 'image/png',
            filename: 'highchart',
            sourceWidth: chart.chartWidth,
            sourceHeight: chart.chartHeight,
            success: function (imageData) {
                console.log('Image Data:', imageData);

                const xhr = new XMLHttpRequest();
                const csrfToken = '<?= Yii::$app->request->csrfToken ?>';
                xhr.open('POST', '<?= Yii::$app->urlManager->createUrl('site/save-highchart') ?>', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('X-CSRF-Token', csrfToken);
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        console.log('Server Response:', xhr.responseText);
                        alert(xhr.responseText); // Debugging response
                    }
                };
                xhr.send('image=' + encodeURIComponent(imageData) + '&_csrf=' + csrfToken);
            },
            error: function (err) {
                console.error('Error generating image:', err);
            }
        });
    }

    window.onload = function () {
        setTimeout(sendChartToServer, 1000); // Delay to ensure chart is rendered
    };
    </script>

 
</div>