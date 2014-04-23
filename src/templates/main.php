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
			<li><a href="<?php echo $app->urlFor('task-list') ?>">Main page</a></li>
			<li><a href="<?php echo $app->urlFor('task-add') ?>">Add task</a></li>
			<li><a href="<?php echo $app->urlFor('groups-add') ?>">Add group</a></li>

		</ul>
		<span id="ident"><?php if(isset($_SESSION['username'])) echo "<span>".$_SESSION['username']."</span><a id=\"auth\" href=\"/logout?redirect_url={$_SERVER['REQUEST_URI']}\">Logout</a>"; else echo '<a id="auth" href="/login?redirect_url={$_SERVER[\'REQUEST_URI\']}">Login</a>'; ?></span>
	</div>
</nav>

<?php if(isset($_SESSION['slim.flash']['error'])){ ?>
	<h5 class="container alert alert-danger alert-dismissable">
		<?php echo $_SESSION['slim.flash']['error'] ?>
	</h5>
<?php } ?>

<?php if(isset($_SESSION['slim.flash']['warning'])){ ?>
	<h5 class="container alert alert-warning alert-dismissable">
		<?php echo $_SESSION['slim.flash']['warning'] ?>
	</h5>
<?php } ?>

<?php if(isset($_SESSION['slim.flash']['success'])){ ?>
	<h5 class="container alert alert-success alert-dismissable">
		<?php echo $_SESSION['slim.flash']['success'] ?>
	</h5>
<?php } ?>

<?php echo $yield ?>

<script type="text/javascript" charset="utf-8">
	var menu = document.getElementsByClassName('nav')[0].children;
	for (var i = menu.length - 1; i >= 0; i--) {
		if(menu[i].children[0].href == window.document.location)
			menu[i].setAttribute("class", "active");
	};
</script>
</body>
</html>