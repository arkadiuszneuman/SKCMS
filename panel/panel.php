<?php
session_start();
?>

<script type="text/javascript" src="./javascript/panel.js"></script>
<link rel="stylesheet" type="text/css" href="./css/panel.css" />

<?php

    include('../structure/up.html');
    include('../sql.php');

    function CreateTable($news, $links, $task)
    {
        if ($news == null)
            return;

        @$page = $_GET['page'];
        if ($page == null)
            $page = 0;

        if ($task === "edit")
        {
            ?>
                <form name="binFrm" method="POST" action="./panel.php?task=moveToBin&page=<?php echo $page ?>">
                <div id="options">
                    Zaznaczone: <br /><input type="submit" class="buttonInput" value="Zapisz zmiany">
                </div>
            <?php
        }
        else if ($task === "bin")
        {
            ?>
                <form name="binFrm" method="POST" action="./panel.php?task=restoreRemove&page=<?php echo $page ?>">
                <div id="options">
                    Zaznaczone: <br />
                   <input type="submit" class="buttonInput" value="Przywróć" name="restore">
                   <input type="submit" class="buttonInput" value="Usuń" name="remove">
                </div>
            <?php
        }
        ?>
                <table id="tabPanel">
                    <tr id="upper">
                        <td>Lp</td>
                        <td>Zazn.</td>
                        <td class="topic">Temat</td>
                        <td>Wyświetlany w</td>
                    </tr>
        <?php
        $even = true;
        $i = 1;
        foreach ($news as $n)
        {
            if ($even)
            {
                ?><tr class="even"><?php
            }
            else
            {
                ?><tr class="odd"><?php
            }

            ?><td><?php echo $i ?></td>
            <td><input type="checkbox" name="check[]" value="<?php echo $n['id'] ?>" /></td>
            <td class="topic"><a href="./panel.php?task=editNote&id=<?php echo $n['id'] ?>" title="Kliknij, aby edytować"><?php echo $n['title'] ?></a></td>
            <td>
                <select name="visibleIn[<?php echo $n['id'] ?>]">
                    <option value="0">Brak</option>
                    <?php //wyswieltenie dropdownow, gdzie bedzie wyswietlany przy kazdym artykule
                        foreach ($links as $link)
                        {
                            ?><option value=<?php echo $link['id']?> 
                                <?php
                                    if (@$n['idLink'] == $link['id']) //ustawienie odpowiedniego linku, jesli artukul jest do niego przypisany
                                    {
                                            ?> SELECTED<?php
                                    }
                                    ?>><?php
                                    echo $link['link'];
                                    
                                ?></option><?php
                        }
                    ?>
                </select>
            </td>
            </tr><?php

            ++$i;
            $even = !$even;
        }
        ?></table><?php

        ?></form><?php
    }

    function CreateTableLinks($links)
    {
        if ($links == null)
            return;

        ?>
                <table id="tabPanel">
                    <tr id="upper">
                        <td>Lp</td>
                        <td class="topic">Link</td>
                    </tr>
        <?php
        $even = true;
        $i = 1;
        foreach ($links as $link)
        {
            if ($even)
            {
                ?><tr class="even"><?php
            }
            else
            {
                ?><tr class="odd"><?php
            }

            ?><td><?php echo $i ?></td>
            <td class="topic">
                <a href="./panel.php?task=editLinks&id=<?php echo $link['id'] ?>"  title="Kliknij, aby edytować"><?php echo $link['link'] ?></a></td>
            </tr><?php

            ++$i;
            $even = !$even;
        }
        ?></table><?php
    }

    function SendInfo($info)
    {
        if (!isset($_SESSION['info']))
            $_SESSION['info'] = "";

        if ($_SESSION['info'] != "")
            $_SESSION['info'] = $_SESSION['info']."<br />";
        $_SESSION['info'] = $_SESSION['info'].$info;
    }

    function DrawInfo()
    {
        //wlaczenie zielonej info u gory
        if (isset($_SESSION['info']) && !empty($_SESSION['info']))
        {
            ?><div id="info"><?php echo $_SESSION['info'] ?></div><?php
            $_SESSION['info'] = "";
        }
    }

    function DrawHeader()
    {
        ?>
            <div id="header">
                <div id="left"><div id="logo"></div></div>
                <div id="center">PANEL ADMINISTRACYJNY</div>
            </div>
        <?php
    }

    function DrawMenu()
    {
        ?>
            <div id="menu">
                <div id="links">
                    <a href="./panel.php?task=addNote" class="button">Dodaj notkę</a>
                    <a href="./panel.php?task=editNote&page=0" class="button">Edytuj notkę</a>
                    <a href="./panel.php?task=showBin" class="button">Kosz</a><br />
                    <a href="./panel.php?task=editLinks" class="button">Menu</a>
                </div>
                <div id="shadowRight"></div>
            </div>
        <?php
    }

    function AddNote($sql)
    {
        if(isset($_POST['newnote']))
        {
            $title = $_POST['title'];
            $note = $_POST['note'];

            $title = trim($title);
            $note = trim($note);

            if (empty($title) || empty($note))
            {
                SendInfo("Notka nie dodana z powodu braku tytułu lub treści");
            }
            else
            {
                if ($sql->AddNews($title, $note))
                    SendInfo("News został wysłany");
                else
                    SendInfo("News nie został wysłany");
            }

            DrawInfo();

            echo "<br />";
        }
        ?>
        <form method="POST" action="panel.php?task=addNote">
            <b>Tytuł:</b> <input type="text" size="65" name="title" /><br />
            <b>Treść:</b> <textarea name="note" rows="10" cols="50"></textarea><br />
            <input type="submit" value="Wyślij" name="newnote" />
        </form>
        <?php
    }

    function MoveToBin($sql)
    {
        $page = $_GET['page'];
        if ($page == null)
            $page = 0;

        $isChanged = false; //czy jakis zostal zmodyfikowany
        if ($sql->UpdateArticleLink($_POST['visibleIn'], $page, Sql::NOTHING, $isChanged))
        {
            if ($isChanged)
                SendInfo("Link/Linki do newsów zostały zaktualizowane");
        }
        else
            SendInfo("Link/Linki do newsów nie zostały zaktualizowane");

        @$checkboxes = $_POST['check']; //zlapanie z formularza checknietych checkboxow

        if (count($checkboxes) > 0)
        {
            if ($sql->RemoveNewsToBin($checkboxes))
                SendInfo("Artykuł/Artykuły zostały przeniesione do kosza");
            else
                SendInfo("Nie można przenieść artykułu/artykułów do kosza");
        }
    }

    function EditNote($sql)
    {
        if(isset($_POST['edit'])) //po kliknieciu wyslij przy edycji notki
        {
            $title = $_POST['title'];
            $note = $_POST['note'];
            @$id = $_GET['id'];

            $title = trim($title);
            $note = trim($note);

            if (empty($title) || empty($note))
            {
                SendInfo("News nie zaktualizowany z powodu braku tytułu lub treści");
            }
            else
            {
                if ($sql->EditNews($id, $title, $note))
                    SendInfo("News został zaktualizowany");
                else
                    SendInfo("News nie został zaktualizowany");

                $id = 0; //zeby przeszedl do malowania tabelki
            }

            echo "<br />";
        }
        else
        {
            @$id = $_GET['id'];
        }

        if ($id != 0) //jesli wybrana zostala jakas notka to dawaj formularz, a jesli nie...
        {
            $news = $sql->ReadSelectedNews($id);

            ?>
            <form method="POST" action="panel.php?task=editNote&id=<?php echo $id ?>">
                    <b>Tytuł:</b> <input type="text" size="65" name="title" value="<?php echo $news['title'] ?>" /><br />
                    <b>Treść:</b> <textarea name="note" rows="10" cols="50"><?php echo $news['note']?></textarea><br />
                    <input type="submit" value="Wyślij" name="edit" />
            </form>

            <?php
        }
        else //... to wyswietli sie lista notek do wybrania
        {
            DrawInfo();
            //wyswietlenie knefla Zapisz zmiany
            @$id = $_GET['id'];
            @$page = $_GET['page'];

            $news = $sql->ReadNews(true, $page*20, 20);
            $count = $sql->NumberOfNews();
            CreateTable($news, $sql->ReadLinks(), "edit");

            if ($page > 0)
            {
                ?><a href="./panel.php?task=editNote&page=<?php echo ($page-1) ?>">Poprzednia strona</a> <?php
            }
            else
            {
                ?>Poprzednia strona <?php
            }

            if (($page+1)*20 < $count) //wyswietlenie nastepna strona i ostatnia strona
            {
                ?><a href="./panel.php?task=editNote&page=<?php echo ($page+1) ?>">Następna strona</a><?php
            }
            else
            {
                ?>Następna strona <?php
            }
            
        }
    }

    function RestoreRemove($sql)
    {
        @$checkboxes = $_POST['check']; //zlapanie z formularza checknietych checkboxow

        if (count($checkboxes) > 0)
        {
            if (isset($_POST['restore']))
            {
                if ($sql->RecoverNewsFromBin($checkboxes))
                    SendInfo("Artykuł/Artykuły zostały przywrócone");
                else
                    SendInfo("Nie można przywrócić artykułu/artykułów");
            }
            else if (isset($_POST['remove']))
            {
                if ($sql->RemoveNews($checkboxes))
                    SendInfo("Artykuł/Artykuły zostały usunięte");
                else
                    SendInfo("Nie można usunąć artykułu/artykułów");
            }
        }
        else
        {
             SendInfo("Nie zaznaczono żadnego arykułu");
        }
    }

    function ShowBin($sql)
    {
        DrawInfo();
        $news = $sql->ReadNewsFromBin(true, 0, 100);
        CreateTable($news, $sql->ReadLinks(), "bin");
    }

    function EditLinks($sql)
    {
        @$id = $_GET['id'];

        if(isset($_POST['newlink'])) //dodanie nowego linku
        {
            $link = $_POST['link'];
            $link = trim($link);

            if (empty($link))
            {
                SendInfo("Nie dodano nowego linku z powodu braku nazwy linku");
            }
            else
            {
                if ($sql->AddLink($link))
                    SendInfo("Link został dodany");
                else
                    SendInfo("Link nie został dodany");
            }

            echo "<br />";
        }
        else if (isset($_POST['editlink'])) //lub edycja linku
        {
            $link = $_POST['link'];
            $link = trim($link);

            if (empty($link))
            {
                SendInfo("Nie dodano nowego linku z powodu braku nazwy linku");
            }
            else
            {
                if ($sql->EditLink($id, $link))
                    SendInfo("Link został zmieniony");
                else
                    SendInfo("Link nie został zmieniony");
            }

            echo "<br />";
        }

        DrawInfo();
        
        ?>
        <form method="POST" action="panel.php?task=editLinks<?php ($id != 0) ? print("&id=$id") : print("") ?>">
            <b>Dodaj nowy link:</b> <input type="text" size="65" name="link"
            <?php
                if ($id != 0)
                {
                    $link = $sql->ReadLinks($id)
                    ?> value="<?php echo $link[0]['link'] ?>"<?php
                }
            ?>
            /><br />
            <input type="submit" value="<?php ($id != 0) ? print("Edytuj") : print("Wyślij"); ?>" name="<?php ($id != 0) ? print("editlink") : print("newlink"); ?>" />
        </form>
        <?php

        $links = $sql->ReadLinks();
        CreateTableLinks($links);
    }

    $sql = new Sql();
    if (isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] == true)
    {   
        DrawHeader();
        DrawMenu();
        ?>
            <div id="srodek">
        <?php

        @$task = $_GET['task'];
        if (empty($task)) //domyslnie ma byc edycja notki
            $task = "editNote";
        switch ($task)
        {
            case "addNote":
                AddNote($sql);
                break;

            case "moveToBin": //usuwanie notki
                MoveToBin($sql);
                EditNote($sql); //po wywaleniu newsa do kosza wyswietlamy tez tabelke z newsami
                break;

            case "editNote":
                EditNote($sql);
                break;

            case "restoreRemove":
                RestoreRemove($sql);
                ShowBin($sql);
                break;

            case "showBin":
                ShowBin($sql);
                break;

            case "editLinks":
                EditLinks($sql);
                break;
        }

        echo '<br /><br /><a href="../index.php">Powrót do strony głownej</a>';
    }
    else //jesli nie zalogowany
    {
        if(isset($_POST['send'])) //sprawdzenie formularza rejestracji
        {
            $login = $_POST['login'];
            $pass = $_POST['pass1'];
            $name = $_POST['name'];
            $mail = $_POST['mail'];

            if ($sql->AddAdmin($login, md5($pass), $name, $mail)) //rejestracja i przeniesienie do panelu
            {
                echo "Dodano admina<br />Zostaniesz przeniesiony do panelu za 3 sekundy...";
                $page = $_SERVER['PHP_SELF'];
                header("Refresh: 3; url=$page");
                $_SESSION['zalogowany'] = true;
            }
        }
        /*else if (isset($_POST['log'])) //sprawdzenie formularza logowania
        {
            $login = $_POST['login'];
            $pass = $_POST['pass'];

            if ($sql->CheckAdmin($login, md5($pass))) //sprawdzenie loginu i przeniesienie do panelu w razie powodzenia
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
        else //jesli chce sie dostac do panelu to najpierw trzeba sie zalogowac
        {
            echo "Musisz się zalogować<br />Zostaniesz przeniesiony do strony logowania za 3 sekundy...";
            $_SESSION['zalogowany'] = true;
            $page = "./user.php?task=login";
            header("Refresh: 3; url=$page");
        }*/
    }

    $sql->Close();
    
    //zamkniecie diva srodek
    ?>
            </div> 
    <?php

    include('../structure/down.html');
?>
