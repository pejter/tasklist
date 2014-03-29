<div class="container">
<form class="form-horizontal" role="form" action="#" method="POST">
	<input type="hidden" name="_METHOD" value="PUT"/>
	<input type="hidden" name="taskID" value="<?php echo $task['taskID'] ?>"/>
	
	<div class="form-group">
		<label for="name" class="col-sm-4 control-label">Name</label>
		<div class="col-sm-6">
			<input type="text" class="form-control" id="name" name="name" value="<?php echo $task['name'] ?>">
		</div>
	</div>
	<div class="form-group">
		<label for="description" class="col-sm-4 control-label">Description</label>
		<div class="col-sm-6">
			<input type="text" class="form-control" id="description" name="description" value="<?php echo $task['description'] ?>">
		</div>
	</div>
	<div class="form-group">
		<label for="due_date" class="col-sm-4 control-label">Due date</label>
		<div class="col-sm-6">
			<input type="text" class="form-control" id="due_date" name="due_date" value="<?php echo date('d/m/Y H:i', $task['due_date']) ?>">
		</div>
	</div>
	<div class="form-group">
		<label for="groupsID" class="col-sm-4 control-label">Group</label>
		<div class="col-sm-6">
			<input type="number" class="form-control" id="groupsID" name="groupsID" value="<?php echo $task['groupsID'] ?>">
		</div>
	</div>
	<div class="form-group">
		<label for="userID" class="col-sm-4 control-label">Created by</label>
		<div class="col-sm-6">
			<input type="number" class="form-control" id="userID" name="userID" value="<?php echo $task['userID'] ?>">
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-4 col-sm-6">
			<button type="submit" class="btn btn-default">Submit changes</button>
		</div>
	</div>
</form>
</div>