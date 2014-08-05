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
	'log.enabled' => true,
	'log.level' => \Slim\Log::DEBUG,
	'templates.path' => './templates',
	'view' => '\Slim\LayoutView',
	'layout' => 'main.php'
));
\Slim\Route::setDefaultConditions(array(
	'id' => '[0-9]+'
));
//$db->debug = $app->config('debug');

$app->view->appendData(array(
    'app' => $app
));

$app->roles = array(
	'user-add' => 0,
	'user-edit' => 0,
	'group-add' => 1,
	'group-edit' => 1,
	'task-add' => 2,
	'task-edit' => 2
);

$app->refresh = function () use ($app){
	$app->redirect($app->request->getPath());
};

$app->getUser = function () use ($db){
	return $db->user()->where('userID', $_SESSION['userID']);
};

function hasPerm(\Slim\Route $route){
	$app = \Slim\Slim::getInstance();
	if(!isset($_SESSION['userID'])){
		$app->flash('error',"You have to log in to access this page");
		$app->redirect('/login');
	}
	$user = $app->getUser->fetch();
	if($user['position'] > $app->roles[$route->getName()]){
		$app->flash('error',"You don't have permissions to access this page");
		$app->redirect('/');
	}
}

//check session timeout and logout current user
if(isset($_SESSION['username']))
if($_SESSION['lastRequest']+SESSION_TIMEOUT<time()){
	session_destroy();
	$app->refresh;
} else {
	$_SESSION['lastRequest'] = time();
}


//routes
$app->get('/', function () use ($app,$db){
	if(!isset($_SESSION['userID'])){
		$app->render('home-nologin.php');
	} else {
		$groups = $db->membership()->where('userID',$_SESSION['userID']);
		$tasks = $db->task()->where('groupsID',$groups)->order("due_date DESC");

		$app->render('home.php',array(
			'tasks' => $tasks
		));
	}
})->name('task-list');

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
	if($user==null){
		$app->flash('error', "Wrong username");
		$app->refresh;
	} elseif($user['password']==md5($post['password'])){
		$_SESSION['lastRequest'] = time();
		$_SESSION['username'] = $user['login'];
		$_SESSION['userID'] = $user['userID'];
		$app->flash('success',"Login successful! Welcome: {$user['login']}");
		$app->redirect('/');
	} else {
		$app->refresh;
	}
});

$app->get('/logout', function () use ($app){
	session_destroy();
	$address = $app->request->get('redirect_url');
	$app->flash('success','Successfully logged out');
	if($address==null){
		$app->redirect('/');
	} else
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

require 'routes/task.php';
require 'routes/group.php';
require 'routes/user.php';

$app->run();
