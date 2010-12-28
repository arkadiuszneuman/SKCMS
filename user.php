<?php
    session_start();
    @$task = $_GET['task'];

    switch($task)
    {
        case "login":
            ?>
            <form method="POST" action="./panel/panel.php">
                <b>Nazwa użytkownika:</b> <input type="text" name="login"><br>
                <b>Hasło:</b> <input type="password" name="pass"><br>
                <input type="submit" value="Wyślij" name="log" onclick="Login('check'); return false;">
            </form>

            <br />
            <a href="#" onclick="Login('registration');">Rejestruj</a>
            <?php
            break;

        case "check":
            include('sql.php');

            $login = $_GET['login'];
            $pass = $_GET['pass'];

            $sql = new Sql();

            if ($sql->CheckAdmin($login, md5($pass))) //sprawdzenie loginu i przeniesienie do panelu w razie powodzenia
            {
                echo "Zalogowano";
                $_SESSION['zalogowany'] = true;
                
                //echo '<script type="text/javascript">RefreshSite();</script>';
            }
            else
            {
                echo "Błędny login lub hasło";
            }

            $sql->Close();
            break;

        case "logoff":
            if (isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] == true)
            {
                $_SESSION['zalogowany'] = false;
                echo "Wylogowano";
                //echo <br>Za 3 sekundy zostaniesz przeniesiony do poprzedniej lokalizacji...";
            }
            break;

        case "registration":
        default: //domyslnie jest rejestracja
            ?>
            <script type="text/javascript" src="./javascript/registerfrm.js"></script>
            

            <form id="regForm" method="POST" action="panel.php">
                <b>Nazwa użytkownika:</b> <input type="text" name="login"><br>
                <b>Hasło:</b> <input type="password" name="pass"id="pass1"><br>
                <b>Powtórz hasło:</b> <input type="password" name="pass" id="pass2"><br>
                <b>Nazwa wyświetlana:</b> <input type="text" name="name"><br>
                <b>Adres e-mail:</b> <input type="text" name="mail"><br>
                <input type="submit" value="Wyślij" name="send">
            </form>
            <?php
            break;
    }

    //zamkniecie okienka z logowaniem
    ?>
    <br /><br /><a href="#" onclick="RefreshSite(); Close();">Zamknij</a>
    <?php
?>
