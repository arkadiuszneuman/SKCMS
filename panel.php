<?php

    include('structure/up.html');
    include('sql.php');

    $sql = new Sql("127.0.0.1", "root", "", "database");

    session_start();

    if (isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] == true)
    {
        echo "Tutaj bedzie wszystko po zalogowaniu się";

        echo "<br><br><a href=\".\index.php\">Powrót do strony głownej</a>";
    }
    else
    {
        if(isset($_POST['send']))
        {
            $login = $_POST['login'];
            $pass = $_POST['pass1'];
            $name = $_POST['name'];
            $mail = $_POST['mail'];

            if ($sql->AddAdmin($login, md5($pass), $name, $mail))
            {
                echo "Dodano admina<br>Zostaniesz przeniesiony do panelu za 3 sekundy...";
                $page = $_SERVER['PHP_SELF'];
                header("Refresh: 3; url=$page");
                $_SESSION['zalogowany'] = true;
            }
        }
        else if (isset($_POST['log']))
        {
            $login = $_POST['login'];
            $pass = $_POST['pass'];

            if ($sql->CheckAdmin($login, md5($pass)))
            {
                echo "Zalogowano<br>Zostaniesz przeniesiony do panelu za 3 sekundy...";
                $_SESSION['zalogowany'] = true;
                $page = $_SERVER['PHP_SELF'];
                header("Refresh: 3; url=$page");
            }
            else
            {
                echo "Błędny login lub hasło\n";
            }
        }
    }

    $sql->Close();

    include('structure/down.html');
?>
