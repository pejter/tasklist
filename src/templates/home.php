<style type="text/css" media="screen">
	@import '/static/css/home.css';
</style>

<div class="container">
	<table class="table table-hover">
		<thead>
			<tr>
				<th>Task name</th>
				<th>Status</th>
				<th>Days left</th>
			</tr>
		</thead>
		<tbody>
			<?php while ($task = $tasks->fetch()) {
				$due_date = new DateTime();
				$due_date->setTimestamp($task['due_date']);
				$daysLeft = date_diff($due_date, new DateTime())->days; ?>
				<tr onclick="window.document.location='task/<?php echo $task['taskID'] ?>';">
					<td><?php echo $task['name'] ?></td>
					<td><?php echo $task['description'] ?></td>
					<td><?php echo $daysLeft ?></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
</div>