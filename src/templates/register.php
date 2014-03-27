<style type="text/css" media="screen">
	@import '../static/css/signin.css';
</style>

<div class="container">

	<form class="form-signin" method="post">
		<h2 class="form-signin-heading">Dolacz do nas juz dzis!</h2>
		
		<input type="text" class="form-control" placeholder="Nazwa uzytkownika*" name="login" required="" autofocus="">
		<input type="text" class="form-control" placeholder="Adres Email*" name="mail" required="">
		<input type="text" class="form-control" placeholder="Imie" name="name">
		<input type="text" class="form-control" placeholder="Nazwisko" name="surname">
		<input type="password" class="form-control" placeholder="Haslo*" name="password" required="">
		
		<table>
		<tr>
		<td><button class="btn btn-lg btn-primary btn-block" type="submit">Rejestruj</button></td>
		<td><button class="btn btn-lg btn-primary btn-block" type="reset">Wyczysc</button></td>
		</tr>
		</table>
		<h5>Pola oznaczone <font color="red">*</font> są wymagane !</h4>
	</form>
</div> <!-- /container -->
