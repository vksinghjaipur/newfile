<?php
	use yii\helpers\Html;
	use yii\widgets\DetailView;
  use yii\helpers\Url;

?>

<div class="container mt-3">
  <h2>Chart</h2>


<span><?= Html::a('Generate Chart PDF', ['site/chartpdf'], ['class' => 'btn btn-primary', 'target' => '_blank']) ?></span>
  
  <?php
  $imageUrl = Url::to('@web/images/nps.jpeg', true);
  echo Html::img($imageUrl, ['alt' => 'NPS Image', 'style' => 'width: 100%; height: auto;']);
  ?>





<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<canvas id="myChart" style="width:100%;max-width:600px"></canvas>

<script>
  const xValues = ["Italy", "France", "Spain", "USA", "Argentina"];
  const yValues = [55, 49, 44, 34, 15];
  const barColors = ["red", "green","blue","orange","brown"];

  const myChart = new Chart("myChart", {
    type: "bar",
    data: {
      labels: xValues,
      datasets: [{
        backgroundColor: barColors,
        data: yValues
      }]
    },
    options: {
      legend: { display: false },
      title: {
        display: true,
        text: "World Wine Production 2018"
      }
    }
  });

  function sendChartToServer() {
    const canvas = document.getElementById('myChart');
    const image = canvas.toDataURL('image/png');
    console.log('Image Data:', image); // Log image data

    const xhr = new XMLHttpRequest();
    const csrfToken = '<?= Yii::$app->request->csrfToken ?>'; // Get CSRF token
    xhr.open('POST', '<?= Yii::$app->urlManager->createUrl('site/save-chart') ?>', true);
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