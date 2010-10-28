<?php
    include('structure/up.html');
?>

<script type="text/javascript">
    function checkPass()
    {
        var form = document.getElementById("regForm");
        var password1 = form.pass1;
        var password2 = form.pass2;

        if (password1.value != password2.value || password1.value.length < 3)
        {
            password1.style.border = "2px solid red";
            password2.style.border = "2px solid red";
        }
        else
        {
            password1.style.border = "2px solid lightgreen";
            password2.style.border = "2px solid lightgreen";
        }
    }
</script>

<form id="regForm" method="POST" action="panel.php">
    <b>Nazwa użytkownika:</b> <input type="text" name="login"><br>
    <b>Hasło:</b> <input type="password" onKeyUp="checkPass()" name="pass1"><br>
    <b>Powtórz hasło:</b> <input type="password"onKeyUp="checkPass()" name="pass2"><br>
    <b>Nazwa wyświetlana:</b> <input type="text" name="name"><br>
    <b>Adres e-mail:</b> <input type="text" name="mail"><br>
    <input type="submit" value="Wyślij" name="send">
</form>

<?php
    include('structure/down.html');
?>
