<?php 
	use yii\helpers\Html;
	use yii\widgets\DetailView;

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Yii 2</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<div class="container mt-3">
    <h2>Data</h2>
    <table class="table">
        <thead>
            <tr>
                <th style="width: 60px;">Serial No.</th>
                <th style="width: 100px;">Title</th>
                <th style="width: 250px;">Description</th>
                <th style="width: 150px;">Category</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($posts)): ?>
                <?php foreach ($posts as $index => $post): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($post->title); ?></td>
                        <td><?php echo htmlspecialchars($post->description); ?></td>
                        <td><?php echo htmlspecialchars($post->category); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No records found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
