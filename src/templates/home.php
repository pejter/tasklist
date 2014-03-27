<style type="text/css" media="screen">
	@import "../static/css/home.css";
</style>
<div class="container">
	<table class="table table-hover">
		<thead>
			<tr>
				<th>Task name</th>
				<th>Status</th>
				<th>Due date</th>
			</tr>
		</thead>
		<tbody>
			<?php
			while ($task = $tasks->fetch()) {
			echo "<tr onclick=\"window.document.location='task/{$task['taskID']}';\">
				<td>{$task['name']}</td>
				<td>{$task['description']}</td>
				<td>{$task['due_date']}</td>
			</tr>";
			}?>
		</tbody>
	</table>
</div>