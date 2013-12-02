<?php
require 'vendor/autoload.php';
require 'config.php'; //engine config
define('ENGINE', 'ENGINE'); //sql engine used by the database, possible: MySQL(mysql), SQLite(sqlite), PostgreSQL(pgsql), MS SQL(sqlsrv), Oracle(oci)

$pdo = new PDO(ENGINE.":host=".$db_host.";dbname=".$db_name, $db_user, $db_pass);
$db = new NotORM($pdo);

$app = new \Slim\Slim(array(
	'mode' => 'development',
	'debug' => true,
	'templates.path' => './templates',
	'view' => '\Slim\LayoutView',
	'layout' => './templates/main.php'
));


//routes
$app->get('/', function (){
	$tasks = $db->task();

	$app->render('home.php',array(
		'tasks' => $tasks,
		'title' => 'Home'
	));
});

$app->group('/task', function () use ($app,$db) {

	//task adding
	$app->get('/add', function (){
		$app->render('task-add.php');
	});

	$app->post('/add', function (){
		$post = $app->request()->post();
		$result = $db->task()->insert($post);

		if(!$result)
			echo "Error!";
		else
			echo "Task added";
	});

	$app->get('/:taskID',function ($taskID){
		$task = $db->task()->where('id',$taskID);

		if($task->fetch())
			$app->render('task-details.php',array(
				'task' => $task
			));
		else
			echo "Task not found";
	});

	//task editing
	$app->get('/:taskID/edit', function ($taskID){
		$task = $db->task()->where(id, $taskID);

		if($task->fetch())
			$app->render('task-edit.php', array(
				'task' => $task
			));
	});

	$app->put('/:taskID/edit', function ($taskID){
		$task = $db->task()->where(id, $taskID);

		if($task->fetch()){
			$put = $app->request()->put();
			$result = $task->update($put);

			if(!$result)
				echo "Error!";
			else
				echo "Task updated successfully";
		}
		else
			echo "Task not found";
	});
});

$app->group('/group', function () use ($app,$db){

	$app->get('/add', function (){
		echo "adding group";
	});

	$app->get('/:groupID',function ($groupID){
		$group = $db->group();

		$members = array();
		foreach($group->membership() as $user){
			$members += $user->user();
		}

		$app->render('group-details.php', array(
			'group' => $group,
			'members' => $members
		));
	});

	$app->get('/:groupID/edit', function ($groupID){
		echo "Group: ".$groupID." edit";
	});
});

$app->group('/user', function () use ($app,$db){

	$app->get('/add', function (){
		$post = $app->request()->post();
		$result = $db->user()->insert($post);

		if(!$result)
			echo "Error!"
		else
			echo "User added"
	});
	
	$app->get('/:userID', function ($userID){
		$user = $db->user()->where(id, $userID);

		$groups = array();
		foreach($user->membership() as $group){
			$groups[] = $group["name"];
		}

		$app->render('user-details.php', array(
			'user' => $user,
			'groups' => $groups
		));
	});

	$app->get('/:userID/edit', function ($userID){
		echo "User: ".$userID." edit";
	});
});

$app->run();
