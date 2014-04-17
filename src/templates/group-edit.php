<div class="container">
<form class="form-horizontal" role="form" action="#" method="POST">
	<input type="hidden" name="_METHOD" value="PUT"/>
	<input type="hidden" name="groupsID" value="<?php echo $group['groupsID'] ?>"/>
	
	<div class="form-group">
		<label for="name" class="col-sm-4 control-label">Name</label>
		<div class="col-sm-6">
			<input type="text" class="form-control" id="name" name="name" value="<?php echo $group['name'] ?>">
		</div>
	</div>
</form>
</div>