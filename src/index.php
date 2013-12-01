<?php

require 'vendor/autoload.php';
require 'NotORM.php';



$db_name = "";
$db_host = "";
$db_user = "";
$db_pass = "";

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