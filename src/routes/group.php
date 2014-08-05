<?php
$app->group('/group', function () use ($app,$db){

	//group adding
	$app->get('/add', function () use ($app){
		$app->render('group-add.php');
	})->name('groups-add');

	$app->post('/add', function () use ($app,$db){
		$post = $app->request->post();

		if(!$db->groups->insert($post)){
			$app->flash('error', 'Error adding group');
			$app->refresh;
		} else {
			$app->flash('success', 'Group added');
			$app->redirect('/');
		}
	});

	//group details
	$app->get('/:id',function ($id) use ($app,$db){
		$result = $db->groups()->where('groupsID', $id);

		if(!$group = $result->fetch()){
			$app->flash('error', 'Group not found');
			$app->redirect('/');
		}

		$members = array();
		foreach($group->membership() as $member){
			$members[] = $member->user;
		}

		$app->render('group-details.php', array(
			'group' => $group,
			'members' => $members
		));
	});

	//group editing
	$app->get('/:id/edit', 'hasPerm', function ($id) use ($app,$db){
		$result = $db->groups()->where('groupsID', $id);

		if(!$group = $result->fetch()){
			$app->flash('error', 'Group not found');
			$app->redirect('/');
		}

		$members = array();
		foreach($group->membership() as $member){
			$members[] = $member->user;
		}

		$app->render('group-edit.php', array(
			'group' => $group,
			'members' => $members
		));
	})->name('group-edit');

	$app->put('/:id/edit', function ($id) use ($app,$db){
		$result = $db->groups()->where('groupsID', $id);

		if($group = $result->fetch()){
			$put = $app->request->put();
			unset($put['_METHOD']);
			if($put != $group->jsonSerialize()){
				if($group->update($put)){
					$app->flash('success', 'Group updated successfully');
					$app->redirect('/group/'.$put['groupsID']);
				} else {
					$app->getLog()->debug(var_dump($put));
					$app->getLog()->debug(var_dump($group));
				}
			} else {
				$app->redirect('/');
			}
		} else {
			$app->flash('error', 'Group not found');
			$app->redirect('/');
		}
	});
});
?>