<html lang="pl">
<head>
    <meta charset="iso-8859-2">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="../../docs-assets/ico/favicon.png">

    <title>Tasklist Login Page</title>

    <link href="template/css/bootstrap.css" type="text/css" rel="stylesheet">
    <link href="signin.css" type="text/css" rel="stylesheet">

  </head>

  <body>

    <div class="container">

      <form class="form-signin">
        <h2 class="form-signin-heading">Zaloguj się</h2>
        <input type="text" class="form-control" placeholder="Adres Email" required="" autofocus="">
        <input type="password" class="form-control" placeholder="Hasło" required="">
        <label class="checkbox">
          <input type="checkbox" value="remember-me"> Zapamiętaj
        </label>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Zaloguj</button>
      </form>

    </div> <!-- /container -->

</body></html>
<?php

require 'vendor/autoload.php';


$db_name = "task";
$db_host = "localhost";
$db_user = "root";
$db_pass = "toor";

$pdo = new PDO("mysql:host=".$db_host.";dbname=".$db_name, $db_user, $db_pass);
$db = new NotORM($pdo);

$app = new \Slim\Slim(array(
	'mode' => 'development',
	'debug' => true,
	'templates.path' => './templates'
));

//routes
$app->get('/', function (){
	echo "Tasks list";
});

$app->group('/task', function () use ($app) {

	$app->get('/add', function (){
		echo "Adding task";
	});
	$app->get('/:taskID',function ($taskID){
		echo "Task: ".$taskID." details";
	});

	$app->get('/:taskID/edit', function ($taskID){
		echo "Task:".$taskID." edit";
	});
});

$app->group('/group', function () use ($app){

	$app->get('/add', function (){
		echo "adding group";
	});
	
	$app->get('/:groupID',function ($groupID){
		echo "Group: ".$groupID." details";
	});

	$app->get('/:groupID/edit', function ($groupID){
		echo "Group: ".$groupID." edit";
	});
});

$app->group('/user', function () use ($app){

	$app->get('/add', function (){
		echo "adding user";
	});
	$app->get('/:userID', function ($userID){
		echo "User: ".$userID." details";
	});

	$app->get('/:userID/edit', function ($userID){
		echo "User: ".$userID." edit";
	});
});

$app->run();
