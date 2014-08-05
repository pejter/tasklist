<?php
$app->group('/user', function () use ($app,$db){

	//user adding
	$app->get('/add', function () use ($app){
		$app->render('user-add.php');
	})->name('user-add');

	$app->post('/add', function () use ($app,$db){
		if(!$db->user()->insert($app->request->post()))
			echo "Error!";
		else
			echo "User added";
	});

	//user details
	$app->get('/:id', function ($id) use ($app,$db){
		$user = $db->user()->where('userID', $id);

		$groups = array();
		foreach($user->membership() as $group){
			$groups[] = $group["name"];
		}

		$app->render('user-details.php', array(
			'user' => $user,
			'groups' => $groups
		));
	});

	//user editing
	$app->get('/:id/edit', function ($id){
		echo "User: ".$id." edit";
	});
});
?>