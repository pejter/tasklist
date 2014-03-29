<div class="container">
	<table class="table table-bordered">
		<tbody>
			<tr>
				<td><h4>Name: </h4></td><td><h4><?php echo $task['name']?></h4></td>
			</tr>
			<tr>
				<td><h4>Description: </h4></td><td><h4><?php echo $task['description'] ?></h4></td>
			</tr>
			<tr>
				<td><h4>Due date: </h4></td><td><h4><?php echo date('d.m.Y H:i',$task['due_date']) ?></h4></td>
			</tr>
			<tr>
				<td><h4>Groups: </h4></td><td><h4><?php echo $task['group'] ?></h4></td>
			</tr>
			<tr>
				<td><h4>Creator: </h4></td><td><h4><?php echo $task['creator'] ?></h4></td>
			</tr>
		</tbody>
	</table>
	<?php if($canEdit){ ?>
		<a href="<?php echo $task['taskID'] ?>/edit" class="btn btn-primary btn-lg active" role="button">Edit this task</a>
	<?php } ?>
</div>