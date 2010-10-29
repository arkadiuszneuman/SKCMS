<?php

    include('structure/up.html');
    include('sql.php');

    $sql = new Sql("127.0.0.1", "root", "", "database");

    session_start();

    if (isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] == true)
    {
        ?>

        <a href="./panel.php?task=addNote">Dodaj notkę</a>&nbsp;&nbsp;
        <a href="./panel.php?task=editNote">Edytuj notkę</a>&nbsp;&nbsp;
        <a href="./panel.php?task=removeNote">Usuń notkę</a><br />

        <?php

        @$task = $_GET['task'];
        switch ($task)
        {
            case "addNote":
                if(isset($_POST['newnote']))
                {
                    $title = $_POST['title'];
                    $note = $_POST['note'];

                    $title = trim($title);
                    $note = trim($note);

                    if (empty($title) || empty($note))
                    {
                        echo "Notka nie dodana z powodu braku tytułu lub treści";
                    }
                    else
                    {
                        if ($sql->AddNews($title, $note))
                            echo "News został wysłany";
                        else
                            echo "News nie został wysłany";
                    }

                    echo "<br />";
                }
                ?>
                <form method="POST" action="panel.php?task=addNote">
                    <b>Tytuł:</b> <input type="text" size="65" name="title" /><br />
                    <b>Treść:</b> <textarea name="note" rows="10" cols="50"></textarea><br />
                    <input type="submit" value="Wyślij" name="newnote" />
                </form>
                <?php
                break;

            case "editNote":
                break;

            case "removeNote":
                break;
        }

        echo '<br /><br /><a href="./index.php">Powrót do strony głownej</a>';
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
                echo "Dodano admina<br />Zostaniesz przeniesiony do panelu za 3 sekundy...";
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
                echo "Zalogowano<br />Zostaniesz przeniesiony do panelu za 3 sekundy...";
                $_SESSION['zalogowany'] = true;
                $page = $_SERVER['PHP_SELF'];
                header("Refresh: 3; url=$page");
            }
            else
            {
                echo "Błędny login lub hasło\n";
            }
        }
        else
        {
            echo "Musisz się zalogować<br />Zostaniesz przeniesiony do strony logowania za 3 sekundy...";
            $_SESSION['zalogowany'] = true;
            $page = "./user.php?task=login";
            header("Refresh: 3; url=$page");
        }
    }

    $sql->Close();

    include('structure/down.html');
?>
