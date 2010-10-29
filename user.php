<?php
    include('structure/up.html');

    @$task = $_GET['task'];

    switch($task)
    {
        case "login":
            ?>
            <form method="POST" action="panel.php">
                <b>Nazwa użytkownika:</b> <input type="text" name="login"><br>
                <b>Hasło:</b> <input type="password" name="pass"><br>
                <input type="submit" value="Wyślij" name="log">
            </form>

            <br />
            <a href="./user.php?task=registration">Rejestruj</a>
            <?php
            break;

        case "logoff":
            session_start();
            if (isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] == true)
            {
                $_SESSION['zalogowany'] = false;
                $page = "javascript: history.go(-1)";
                header("Refresh: 3; url=$page");

                echo "Wylogowano<br>Za 3 sekundy zostaniesz przeniesiony do poprzedniej lokalizacji...";
            }
            break;

        case "registration":
        //default: //domyslnie jest rejestracja
            ?>
            <script type="text/javascript" src="./javascript/registerfrm.js"></script>
            

            <form id="regForm" method="POST" action="panel.php">
                <b>Nazwa użytkownika:</b> <input type="text" name="login"><br>
                <b>Hasło:</b> <input type="password" onKeyUp="checkPass()" name="pass1"><br>
                <b>Powtórz hasło:</b> <input type="password"onKeyUp="checkPass()" name="pass2"><br>
                <b>Nazwa wyświetlana:</b> <input type="text" name="name"><br>
                <b>Adres e-mail:</b> <input type="text" name="mail"><br>
                <input type="submit" value="Wyślij" name="send">
            </form>
            <?php
            break;
    }

    include('structure/down.html');
?>
