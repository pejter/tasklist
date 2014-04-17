<div class="container panel panel-default">
	<h3 class="panel-heading"><?php echo $group['name'] ?>
		<small><a href="<?php echo $group['groupsID'] ?>/edit"><button class="btn btn-primary">Edit group</button></a></small>
	</h3>
	<ul class="list-group">
		<?php foreach($members as $member){ ?>
		<li class="list-group-item"><a href="/user/<?php echo $member['userID'] ?>"><?php echo $member['name'].' '.$member['surname'] ?></a></li>
		<?php } ?>
	</ul>
</div>