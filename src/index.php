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

$app->group('/task', function () use ($app,$db) {

	//task adding
	$app->get('/add', 'hasPerm', function () use ($app){
		$app->render('task-add.php');
	})->name('task-add');

	$app->post('/add', function () use ($app,$db){
		$post = $app->request->post();

		list($date, $time) = explode(' ', $post['due_date']);
		list($due_date['d'], $due_date['m'], $due_date['y']) = explode('-', $date);
		list($due_time['h'], $due_time['i']) = explode(':', $time);
		$post['due_date'] = mktime($due_time['h'], $due_time['i'], 0, $due_date['m'], $due_date['d'], $due_date['y']);
		$result = $db->task()->insert($post);

		if(!$result){
			$app->flash('error', 'Error adding task');
			$app->refresh;
		} else {
			$app->flash('success', 'Task added');
			$app->redirect('/');
		}
	});

	//task details
	$app->get('/:id',function ($id) use ($app,$db){
		$result = $db->task()->where('taskID',$id);

		if($task = $result->fetch()){
			$task['creator'] = $task->user['name'].' '.$task->user['surname'];
			$task['group'] = $task->groups['name'];
			$app->render('task-details.php',array(
				'task' => $task,
				'canEdit' => ($task->user['position'] <= 2)
			));
		} else
			echo "Task not found";
	})->name('task-view');

	//task editing
	$app->get('/:id/edit', 'hasPerm', function ($id) use ($app,$db){
		$result = $db->task()->where('taskID', $id);

		if($task = $result->fetch())
			$app->render('task-edit.php', array(
				'task' => $task
			));
	})->name('task-edit');

	$app->put('/:id/edit', 'hasPerm', function ($id) use ($app,$db){
		$result = $db->task()->where('taskID', $id);

		if($task = $result->fetch()){
			$put = $app->request->put();
			unset($put['_METHOD']);
			list($date, $time) = explode(' ', $put['due_date']);
			list($due_date['d'], $due_date['m'], $due_date['y']) = explode('/', $date);
			list($due_time['h'], $due_time['i']) = explode(':', $time);
			$put['due_date'] = mktime($due_time['h'], $due_time['i'], 0, $due_date['m'], $due_date['d'], $due_date['y']);
			if($put != $task->jsonSerialize()){

				if(!$task->update($put)){
					echo "Error!<br>Request: ";
					var_dump($put);
					echo "<br>Record: ";
					var_dump($task->jsonSerialize());
				} else {
					$app->flash('success', 'Task updated successfully');
					$app->redirect('/task/'.$put['taskID']);
				}
			} else
				$app->redirect('/task/'.$put['taskID']);
		} else {
			$app->flash('error', 'Task not found');
			$app->redirect('/');
		}

	});
});

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

$app->run();
