<?php
session_start();
?>

<script type="text/javascript" src="./javascript/panel.js"></script>
<link rel="stylesheet" type="text/css" href="./css/panel.css" />

<?php

    include('structure/up.html');
    include('sql.php');

    function CreateTable($news, $links)
    {
        if ($news == null)
            return;

        //akcja formy ustawiana w javascripcie
        ?>
            <form name="binFrm" method="POST" action="">
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
                    <option>Brak</option>
                    <?php //wyswieltenie dropdownow, gdzie bedzie wyswietlany przy kazdym artykule
                        foreach ($links as $link)
                        {
                            ?><option
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
        $_SESSION['info'] = $info;
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
                    <a href="./panel.php?task=editNote" class="button">Edytuj notkę</a>
                    <?php
                        @$task = $_GET['task'];
                        /*if ($task === "editNote" || empty($task) || $task === "moveToBin") //sprawdzenie czy jest w edycji notki (lub czy nie zostalo wykonane move to bin) i jesli wyswietla sie tabelka to dodanie przycisku do kosza
                        {
                            @$id = $_GET['id'];
                            if ($id == 0)
                            {
                                ?><a href="http://Kosz" id="toBinBtn" class="submenu">Do kosza</a> <!-- javascript lapie remove i anuluje link oraz wysyla formularz --><?php
                            }
                        }*/
                    ?>
                    <a href="./panel.php?task=showBin" class="button">Kosz</a><br />
                    <?php
                            if ($task === "showBin" || $task === "binToNews" || $task === "binRemove")
                            {
                                ?><a href="http://Przywroc" id="binToNews" class="submenu">Przywróć</a> <!-- javascript lapie remove i anuluje link oraz wysyla formularz --><?php
                                ?><a href="http://Usun" id="binRemove" class="submenu">Usuń bezpowrotnie</a> <!-- javascript lapie remove i anuluje link oraz wysyla formularz --><?php
                            }
                    ?>
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
        echo "razdwa";
        @$checkboxes = $_POST['check']; //zlapanie z formularza checknietych checkboxow
        if ($sql->RemoveNewsToBin($checkboxes)) 
            SendInfo("Artykuł/Artykuły zostały przeniesione do kosza");
        else
            SendInfo("Nie można przenieść artykułu/artykułów do kosza");

        $x = 1;
        for ($i = 0; $i < count($_POST['visibleIn']); ++$i)
        {
            
            if (isset($_POST['visibleIn'][$i]))
            {
                echo $x.'. '.$i." ".$_POST['visibleIn'][$i]."<br>";
                ++$x;
            }
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
                    <b>Tytuł:</b> <input type="text" size="65" name="title" value=" <?php echo $news['title'] ?>" /><br />
                    <b>Treść:</b> <textarea name="note" rows="10" cols="50">"<?php echo $news['note']?> </textarea><br />
                    <input type="submit" value="Wyślij" name="edit" />
            </form>

            <?php
        }
        else //... to wyswietli sie lista notek do wybrania
        {
            DrawInfo();
            //wyswietlenie knefla Zapisz zmiany
            @$id = $_GET['id'];
            if ($id == 0)
            {
                ?><a href="http://Kosz" id="saveButton"  class="button">Zapisz zmiany</a> <!-- javascript lapie remove i anuluje link oraz wysyla formularz --><?php
            }

            @$page = $_GET['page'];

            $news = $sql->ReadNews(true, $page*20, 20);
            $count = $sql->NumberOfNews();
            CreateTable($news, $sql->ReadLinks());

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

    function BinToNews($sql)
    {
        @$checkboxes = $_POST['check']; //zlapanie z formularza checknietych checkboxow
        if ($sql->RecoverNewsFromBin($checkboxes)) 
            SendInfo("News/Newsy zostały przywrócone");
        else
            SendInfo("Nie można przywrócić newsów");
    }

    function BinRemove($sql)
    {
        @$checkboxes = $_POST['check']; //zlapanie z formularza checknietych checkboxow
        if ($sql->RemoveNews($checkboxes))
            SendInfo("News/Newsy zostały usunięte");
        else
            SendInfo("Nie można usunąć newsów");
    }

    function ShowBin($sql)
    {
        DrawInfo();
        $news = $sql->ReadNewsFromBin(true, 0, 100);
        CreateTable($news, $sql->ReadLinks());
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

            case "editNote":
                EditNote($sql);
                break;

            case "binToNews":
                BinToNews($sql);
                ShowBin($sql);
                break;

            case "binRemove":
                BinRemove($sql);
                ShowBin($sql);
                break;

            case "showBin":
                ShowBin($sql);
                break;

            case "editLinks":
                EditLinks($sql);
                break;
        }

        echo '<br /><br /><a href="./index.php">Powrót do strony głownej</a>';
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
        else if (isset($_POST['log'])) //sprawdzenie formularza logowania
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
        }
    }

    $sql->Close();
    
    //zamkniecie diva srodek
    ?>
            </div> 
    <?php

    include('structure/down.html');
?>
