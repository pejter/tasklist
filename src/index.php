<?php

require 'vendor/autoload.php';
require 'config.php'; //engine config
define('ENGINE', 'ENGINE'); //sql engine used by the database, possible: MySQL(mysql), SQLite(sqlite), PostgreSQL(pgsql), MS SQL(sqlsrv), Oracle(oci)
define('SESSION_TIMEOUT', 600);

$pdo = new PDO(ENGINE.":host=".$db_host.";dbname=".$db_name, $db_user, $db_pass);
$db = new NotORM($pdo);

//application config
$app = new \Slim\Slim(array(
	'mode' => 'development',
	'debug' => true,
	'templates.path' => './templates',
	'view' => '\Slim\LayoutView',
	'layout' => './templates/main.php'
));
\Slim\Route::setDefaultConditions(array(
	'id' => '[0-9]{1,}'
));
$db->debug = $app->config('debug');

function refresh(){
	$app->redirect($app->request->getPath());
}

//check session timeout and logout current user
if($_SESSION['lastRequest']+$_SESSION['timeout']<time()){
	destroy_session();
	refresh();
} else {
	$_SESSION['lastRequest'] = time();
}


//routes
$app->get('/', function () use ($app,$db){
	$tasks = $db->task();

	$app->render('home.php',array(
		'tasks' => $tasks,
		'title' => 'Home'
	));
});

//login routes
$app->get('/login', function () use ($app){
	if(!isset($_SESSION['username']))
		$app->render('signin.php');
	else {
		$address = $app->request->get('redirect_url');
		if($address==null)
			$app->redirect('/');
		else
			$app->redirect($address);
	}
});

$app->post('/login', function () use ($app,$db){
	$post = $app->request->post();
	$user = $db->user()->where('username=? AND password=?', $post['username'], md5($post['password']))->fetch();
	if($user){
		session_start();
		$_SESSION['lastRequest'] = time();
		$_SESSION['timeout'] = SESSION_TIMEOUT;
		$_SESSION['username'] = $user['username'];
	}
});

$app->get('/logout', function () use ($app){
	session_destroy();
	$address = $app->request->get('redirect_url');
	if($address==null)
		$app->redirect('/');
	else
		$app->redirect($address);
});

//register
$app->get('/register', function () use ($app){
	if(!isset($_SESSION['username']))
		$app->render('register.php');
	else
		$app->render('/');
});

$app->post('/register', function () use ($app,$db){
	$userInfo = $app->request->post();
	if(!preg_match('/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i', $userInfo['email']))
		echo "Not an email";
	elseif($db->user()->where('user=? OR email=?'))
		echo "User already exists!";
	else
		$db->user()->insert($userInfo);
});

$app->group('/task', function () use ($app) {

	//task adding
	$app->get('/add', function () use ($app){
		$app->render('task-add.php');
	});

	$app->post('/add', function () use ($app,$db){
		$post = $app->request->post();
		$result = $db->task()->insert($post);

		if(!$result)
			echo "Error!";
		else
			echo "Task added";
	});

	//task details
	$app->get('/:id',function ($id) use ($app,$db){
		$task = $db->task()->where('id',$id);

		if($task->fetch())
			$app->render('task-details.php',array(
				'task' => $task
			));
		else
			echo "Task not found";
	});

	//task editing
	$app->get('/:id/edit', function ($id) use ($app,$db){
		$task = $db->task()->where('id', $id);

		if($task->fetch())
			$app->render('task-edit.php', array(
				'task' => $task
			));
	});

	$app->put('/:id/edit', function ($id) use ($app,$db){
		$task = $db->task()->where('id', $id);

		if($task->fetch()){
			$put = $app->request->put();
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

	//group adding
	$app->get('/add', function (){
		echo "adding group";
	});

	//group details
	$app->get('/:id',function ($id) use ($app,$db){
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

	//group editing
	$app->get('/:id/edit', function ($id){
		echo "Group: ".$id." edit";
	});
});

$app->group('/user', function () use ($app,$db){

	//user adding
	$app->get('/add', function () use ($app,$db){
		$post = $app->request->post();
		$result = $db->user()->insert($post);

		if(!$result)
			echo "Error!";
		else
			echo "User added";
	});
	
	//user details
	$app->get('/:id', function ($id) use ($app,$db){
		$user = $db->user()->where('id', $id);

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

$app->run();
