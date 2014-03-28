<?php
require 'vendor/autoload.php';
require 'config.php'; //engine config
define('ENGINE', 'mysql'); //sql engine used by the database, possible: MySQL(mysql), SQLite(sqlite), PostgreSQL(pgsql), MS SQL(sqlsrv), Oracle(oci)
define('SESSION_TIMEOUT', 600);

session_cache_limiter(false);
session_start();

$pdo = new PDO(ENGINE.':host='.$db_host.';dbname='.$db_name, $db_user, $db_pass);
$naming = new NotORM_Structure_Convention(
    $primary = "%sID", // $tableID
    $foreign = "%sID", // $tableID
    $table = "%s", // {$table}
    $prefix = "" // $table
);
$db = new NotORM($pdo, $naming);


//application config
$app = new \Slim\Slim(array(
	'mode' => 'development',
	'debug' => true,
	'session.handler' => null,
	'templates.path' => './templates',
	'view' => '\Slim\LayoutView',
	'layout' => 'main.php'
));
\Slim\Route::setDefaultConditions(array(
	'id' => '[0-9]{1,}'
));
//$db->debug = $app->config('debug');

function refresh(){
	$app->redirect($app->request->getPath());
}

//check session timeout and logout current user
if(isset($_SESSION['username']))
if($_SESSION['lastRequest']+SESSION_TIMEOUT<time()){
	session_destroy();
	refresh();
} else {
	$_SESSION['lastRequest'] = time();
}


//routes
$app->get('/', function () use ($app,$db){
	$tasks = $db->task()->order("due_date DESC");

	$app->render('home.php',array(
		'tasks' => $tasks
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
	$user = $db->user()->where('login', $post['login'])->fetch();
	if($user==null)
		echo "Wrong username";
	elseif($user['password']==md5($post['password'])){
		$_SESSION['lastRequest'] = time();
		$_SESSION['username'] = $user['login'];
		$app->render('login-success.html');
	} else {
		echo "Incorrect password";
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
		$app->redirect('/');
});

$app->post('/register', function () use ($app,$db){
	$userInfo = $app->request->post();
	if(!preg_match('/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i', $userInfo['mail']))
		echo "Not an email";
	elseif($db->user()->where('login=? OR mail=?', $userInfo['login'],$userInfo['mail'])->fetch())
		echo "User already exists!";
	else {
		$userInfo['password'] = md5($userInfo['password']);
		$db->user()->insert($userInfo);
		echo "User registered successfully";
	}
});

$app->group('/task', function () use ($app,$db) {

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
		$task = $db->task()->where('taskID',$id);

		if($task->fetch())
			$app->render('task-details.php',array(
				'task' => $task
			));
		else
			echo "Task not found";
	});

	//task editing
	$app->get('/:id/edit', function ($id) use ($app,$db){
		$task = $db->task()->where('taskID', $id);

		if($task->fetch())
			$app->render('task-edit.php', array(
				'task' => $task
			));
	});

	$app->put('/:id/edit', function ($id) use ($app,$db){
		$task = $db->task()->where('taskID', $id);

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

$app->run();
