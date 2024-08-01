<?php
	use yii\helpers\Html;
	use yii\widgets\DetailView;
  use yii\helpers\Url;

?>

<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Chart.js Example</title>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

        <style>
        #chart-container {
            width: 70%;
            height: auto;
        }
        </style>

   </head>

<body>

<div class="container mt-3">
   <h2>High Chart</h2>

   <div>
   <span><?= Html::a('Generate CanvasChart PDF', ['site/canvaschartpdf'], ['class' => 'btn btn-primary', 'target' => '_blank']) ?></span>
    </div><br>

   <div id="chart-container">
        <canvas id="myBarChart" style="width: 100%; height: 300px; background: #F7F7F7;"></canvas>
    </div>    

    
     <script type="text/javascript">
        function calculateFontSize(chart) {
            const width = chart.width;
            const baseFontSize = 14; // Base font size
            const responsiveFontSize = Math.max(Math.min(width / 30, baseFontSize), 10);
            return responsiveFontSize;
        }

        const ctx = document.getElementById('myBarChart').getContext('2d');
        const myBarChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [
                    'Easy process of doing fund transfers', 
                    'Process is quick', 
                    '24x7, 365 days incl holidays', 
                    ['Knowing beneficiary / payee name', 'before making payment'] // Multiline label
                ],
                datasets: [{
                    label: '',
                    data: [30, 50, 15, 5], // Ensure these are numeric percentages
                    backgroundColor: 'rgba(71, 190, 213)',
                    barThickness: 30 // Adjust the bar thickness here if needed
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false // Disable grid lines on the x-axis
                        },
                        ticks: {
                            autoSkip: false,
                            maxRotation: 0, // Rotate labels to fit better
                            minRotation: 0,
                            align: 'center' // Align labels to start of the tick
                        },
                        barPercentage: 0.4, // Adjust this value to increase or decrease the bar width (1.0 = full width, <1.0 = thinner bars)
                        categoryPercentage: 0.4 // Adjust this value to increase or decrease the space between bars
                    },
                    y: {
                        grid: {
                            display: false // Disable grid lines on the y-axis
                        },
                        display: false, // Disable the y-axis labels
                        beginAtZero: true, // Ensure the y-axis starts at zero
                        max: 60 // Set the maximum value of the y-axis to ensure all bars are visible
                    }
                },
                plugins: {
                    legend: {
                        display: false // Disable the legend
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw + '%'; // Ensure percentage is shown
                            }
                        }
                    },
                    datalabels: {
                        anchor: 'end',
                        align: 'end',
                        formatter: function(value) {
                            return value + '%';
                        },
                        color: 'black',
                        font: function(context) {
                            const chart = context.chart;
                            const fontSize = calculateFontSize(chart);
                            return {
                                size: fontSize,
                                weight: 'bold'
                            };
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        // Update the chart to apply responsive data label font size
        myBarChart.update();
    </script>





   <script type="text/javascript">
      function sendChartToServer() {
         const canvas = document.getElementById('myBarChart');
         const image = canvas.toDataURL('image/png');
         console.log('Image Data:', image); // Log image data

         const xhr = new XMLHttpRequest();
         const csrfToken = '<?= Yii::$app->request->csrfToken ?>'; // Get CSRF token
         xhr.open('POST', '<?= Yii::$app->urlManager->createUrl('site/save-canvaschart') ?>', true);
         xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
         xhr.setRequestHeader('X-CSRF-Token', csrfToken); // Set CSRF token
         xhr.onreadystatechange = function () {
           if (xhr.readyState === XMLHttpRequest.DONE) {
             console.log('Server Response:', xhr.responseText); // Log server response
             //alert(xhr.responseText); // Debugging response
           }
         };
         xhr.send('image=' + encodeURIComponent(image) + '&_csrf=' + csrfToken);
      }

       // Ensure chart is rendered before sending data
       window.onload = function() {
         setTimeout(sendChartToServer, 1000); // Delay to ensure chart is rendered
       };
   </script>

</div>
   


 
</body>
</html>