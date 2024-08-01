<?php 
	use yii\helpers\Html;
	use yii\widgets\DetailView;

?>

<div class="container mt-3">
  <?php if(yii::$app->session->hasFlash('message')):?>
    <div class="alert alert-success">
      <?php echo yii::$app->session->getFlash('message');?>
    </div>  
  <?php endif;?>

  <h2>Basic Table</h2>
  <span><?= Html::a('Create', ['site/create'], ['class' => 'btn btn-primary']) ?></span>
  <span><?= Html::a('Generate PDF', ['site/gen-mypdf'], ['class' => 'btn btn-success', 'target' => '_blank']) ?></span>
  <span><?= Html::a('PDF with Contents', ['site/gen-pdfcontent'], ['class' => 'btn btn-warning', 'target' => '_blank']) ?></span>
  
  <table class="table">
    <thead>
      <tr>
      <th style="width: 60px;">Serial No.</th>
      <th style="width: 100px;">Title</th>
      <th style="width: 250px;">Description</th>
      <th style="width: 150px;">Category</th>
      <th style="width: 200px;">Action</th>
      </tr>
    </thead>
    <tbody>
    <?php if(count($posts)>0):?>	
      	<?php foreach($posts as $index=> $post):?>
      	<tr>
        <td><?php echo $index + 1; ?></td>
        	<td><?php echo $post->title; ?></td>
        	<td><?php echo $post->description;?></td>
        	<td><?php echo $post->category;?></td>
        	<td>
        		<span><?= Html::a('View', ['view', 'id' => $post->id], ['class' => 'btn btn-primary btn-sm']) ?></span>
            <span><?= Html::a('Edit', ['update', 'id' => $post->id], ['class' => 'btn btn-success btn-sm']) ?></span>
            <span><?= Html::a('Delete', ['delete', 'id' => $post->id], ['class' =>'btn btn-danger btn-sm'], ['data-method' => 'post', 'data-confirm' => 'Are you sure you want to delete this item?']) ?></span>
            <span><?= Html::a('PDF', ['gen-pdf', 'id' => $post->id], ['class' => 'btn btn-warning btn-sm']) ?></span>
        	</td>
      	</tr>
      	<?php endforeach; ?>
     
    <?php else:?>
      	<tr>
      		<td> No Record Found... </td>
      	</tr>
    <?php endif; ?>	
      
    </tbody>
  </table>
</div>