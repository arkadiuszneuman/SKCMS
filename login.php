<?php
    include('structure/up.html');
?>

<form method="POST" action="panel.php">
    <b>nazwa uzytkownika:</b> <input type="text" name="login"><br>
    <b>haslo:</b> <input type="password" name="pass"><br>
    <input type="submit" value="Wyślij" name="log">
</form>

<br />
<a href="./register.php">Rejestruj</a>

<?php
    include('structure/down.html');
?>
