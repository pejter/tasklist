<!DOCTYPE html>
<html lang="pl">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="shortcut icon" href="/static/favicon.png">

	<title>Tasklist</title>

	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
	<link rel="stylesheet" href="/static/css/main.css">
</head>
<body>
<nav class="navbar navbar-default" role="navigation">
	<div class="container nav-container">
		<ul class="nav navbar-nav">
			<li><a href="/">Main page</a></li>
			<li><a href="/login">Login</a></li>
			<li><a href="/register">Register</a></li>
		</ul>
		<span id="ident"><?php if(isset($_SESSION['username'])) echo "<span>".$_SESSION['username']."</span><a id=\"auth\" href=\"/logout?redirect_url={$_SERVER['REQUEST_URI']}\">Logout</a>"; else echo '<a id="auth" href="/login">Login</a>'; ?></span>
	</div>
</nav>

<?php echo $yield ?>
</body>
</html>