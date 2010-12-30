<?php
include('../sql.php');

class CPanel
{
    private $sql = null;
    private $howMany = 20; //ilosc artykulow w tabelce

    //uzywane do DrawTable
    const EDIT = 1;
    const BIN = 2;
    const LINKS = 4;

    private function Header()
    {
        ?>
        <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
        <html>
          <head>
            <title>SKCMS - Panel Administracyjny</title>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <script type="text/javascript" src="./javascript/panel.js"></script>
            <link rel="stylesheet" type="text/css" href="./css/panel.css" />
          </head>
          <body>
        <?php
    }

    private function DrawUp()
    {
        ?>
            <div id="header">
                <div id="left"><div id="logo"></div></div>
                <div id="center">PANEL ADMINISTRACYJNY</div>
            </div>
        <?php
    }

    private function DrawLeft()
    {
        ?>
            <div id="menu">
                <div id="links">
                    <a href="./panel.php?task=addNote" class="button">Dodaj notkę</a>
                    <a href="./panel.php?task=editArticles&page=0" class="button">Edytuj notkę</a>
                    <a href="./panel.php?task=bin" class="button">Kosz</a>
                    <a href="./panel.php?task=editLinks" class="button">Menu</a>
                    <a href="./panel.php?task=preferences" class="button">Ustawienia</a>
                </div>
                <div id="shadowRight"></div>
            </div>
        <?php
    }

    //metody odpowiedzialne za ramkę zieloną z informacjami
    private function SendInfo($info)
    {
        if (!isset($_SESSION['info']))
            $_SESSION['info'] = "";

        if ($_SESSION['info'] != "")
            $_SESSION['info'] = $_SESSION['info']."<br />";
        $_SESSION['info'] = $_SESSION['info'].$info;
    }

    private function DrawInfo()
    {
        //wlaczenie zielonej info u gory
        if (isset($_SESSION['info']) && !empty($_SESSION['info']))
        {
            ?><div id="info"><?php echo $_SESSION['info'] ?></div><?php
            $_SESSION['info'] = "";
        }
    }

    //uzywane przy dodawaniu i edycji artykulu
    private function DrawTextAreas($action, $title = null, $article = null)
    {
        include("..\CForm.php");
        $form = new CForm(CForm::POST, "panel.php?task=$action");
        $text = new CTextBox("Tytuł: ", "title");
        $text->SetValue($title);
        $text->SetAddionalAttribs('size="65"');
        $form->AddItem($text);
        $text = new CTextArea("Treść: ", "note");
        $text->SetValue($article);
        $text->SetAddionalAttribs('rows="20" cols="100"');
        $form->AddItem($text);
        $form->AddItem(new CButton("Wyślij", "submit"));

        $form->Draw();

        ?>

        <script language="javascript" type="text/javascript" src="./javascript/tiny_mce/tiny_mce.js"></script>
        <script language="javascript" type="text/javascript">
          tinyMCE.init({	// General options
              mode : "textareas",
              theme : "advanced",
              theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
              theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
              theme_advanced_buttons3 : "hr,removeformat,visualaid,|,sub,sup,|,charmap",
              theme_advanced_toolbar_location : "top",	theme_advanced_toolbar_align : "left",	theme_advanced_statusbar_location : "bottom",	theme_advanced_resizing : true,	skin : "o2k7",	skin_variant : "silver"
          });
        </script>
        <?php
    }

    //rysowanie pagingu, howMany - ile artykulow na strone, count - ilosc artykulow, link - przekierowanie
    private function DrawPaging($howMany, $count) 
    {
        if ($howMany >= $count) //jesli ilosc artykulow na stronie jest wieksza od ilosci ogolnej arykulow to nie maluj pagingu
            return;

        @$task = $_GET['task'];
        @$page = $_GET['page'];

        if ($task == null)
            $task = "editArticles";
        if ($page == null)
            $page = 0;

        if ($page > 0) //wyswietlenie poprzednia strona
        {
            ?><a href="./panel.php?task=<?php echo $task ?>&page=0">Pierwsza</a>   <?php
            ?><a href="./panel.php?task=<?php echo $task ?>&page=<?php echo ($page-1) ?>">Poprzednia strona</a>   <?php
        }
        else
        {
            echo "Pierwsza  ";
            echo "Poprzednia strona    ";
        }

        for ($i = $page-2; $i < $page+5; ++$i) //wyswietlenie numerow stron
        {
            if ($i > 0 && ($i-1)*$howMany < $count) //numery stron tylko w zakresie min max
            {
                if ($i != $page + 1) //link nie moze byc aktualna strona
                {
                    ?><a href="./panel.php?task=<?php echo $task ?>&page=<?php echo ($i-1) ?>"><?php echo $i ?></a>   <?php
                }
                else
                {
                    echo $i." ";
                }
            }
        }

        if (($page+1)*$howMany < $count) //wyswietlenie nastepna strona i ostatnia strona
        {
            ?><a href="./panel.php?task=<?php echo $task ?>&page=<?php echo ($page+1) ?>">Następna strona</a>   <?php
            if ($count%$howMany != 0) //jesli ilosc newsow przez ilosc newsow na strone jest nierowna
            {
                ?><a href="./panel.php?task=<?php echo $task ?>&page=<?php echo ((int)($count/$howMany)) ?>">Ostatnia</a>   <?php
            }
            else
            {
                ?><a href="./panel.php?task=<?php echo $link ?>&page=<?php echo (($count/$howMany) - 1) ?>">Ostatnia</a>   <?php
            }
        }
        else
        {
            echo "Następna strona   ";
            echo "Ostatnia";
        }
    }

    //malowanie tabeli z artykulami data - dane malowane w tabelce, task - malowanie tabelki kosza, edycji lub newsow, links - linki potrzebne w tabelce kosz i edycja
    private function DrawTable($data, $task, $links = null)
    {
        if ($data == null)
            return;

        @$page = $_GET['page'];
        if ($page == null)
            $page = 0;

        include("..\CForm.php");
        if ($task == CPanel::EDIT)
        {
            ?>
                <form name="binFrm" method="POST" action="./panel.php?task=editArticles&page=<?php echo $page ?>">
                <div id="options">
                    Zaznaczone: <br /><?php
                    $item = new CButton("Zapisz zmiany", "moveToBin");
                    $item->SetClass("buttonInput");
                    $item->Draw();
                    ?>
                </div>
            <?php
        }
        else if ($task == CPanel::BIN)
        {
            ?>
                <form name="binFrm" method="POST" action="./panel.php?task=bin&page=<?php echo $page ?>">
                <div id="options">
                    Zaznaczone: <br />
                    <?php
                    $item = new CButton("Przywróć", "restore");
                    $item->SetClass("buttonInput");
                    $item->Draw();
                    $item = new CButton("Usuń", "remove");
                    $item->SetClass("buttonInput");
                    $item->Draw();
                    ?>
                </div>
            <?php
        }
        else if ($task == CPanel::LINKS)
        {
            ?>
                <form name="binFrm" method="POST" action="./panel.php?task=editLinks&page=<?php echo $page ?>">
                <div id="options">
                    Zaznaczone: <br />
                     <?php
                    $item = new CButton("Usuń", "remove");
                    $item->SetClass("buttonInput");
                    $item->Draw();
                    ?>
                </div>
            <?php
        }

        if ($task == CPanel::EDIT || $task == CPanel::BIN)
        {
            ?>
                    <table id="tabPanel">
                        <tr id="upper">
                            <td>Lp</td>
                            <td>Zazn.</td>
                            <td class="topic">Temat</td>
                            <td>Wyświetlany w</td>
                        </tr>
            <?php
        }
        else if ($task == CPanel::LINKS)
        {
            ?>
                    <table id="tabPanel">
                        <tr id="upper">
                            <td>Lp</td>
                            <td>Zazn.</td>
                            <td class="topic">Link</td>
                        </tr>
            <?php
        }
        $even = true;
        $i = 1;
        foreach ($data as $n)
        {
            if ($even)
            {
                ?><tr class="even"><?php
            }
            else
            {
                ?><tr class="odd"><?php
            }

            ?>
            <td>
                <?php echo $i ?>
            </td>

            <td>
                <input type="checkbox" name="check[]" value="<?php echo $n['id'] ?>" />
            </td>

            <td class="topic">
                <?php
                if ($task == CPanel::EDIT || $task == CPanel::BIN)
                {
                    ?><a href="./panel.php?task=editArticles&id=<?php echo $n['id'] ?>" title="Kliknij, aby edytować"><?php echo $n['title'] ?></a><?php
                }
                else if ($task == CPanel::LINKS)
                {
                    ?><a href="./panel.php?task=editLinks&id=<?php echo $n['id'] ?>" title="Kliknij, aby edytować"><?php echo $n['link'] ?></a><?php
                }
            ?>
            </td>
            <?php

            if ($task == CPanel::EDIT || $task == CPanel::BIN)
            {
            ?>
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
            <?php
            }
            ?></tr><?php

            ++$i;
            $even = !$even;
        }
        ?></table><?php

        ?></form><?php
    }

///////////////////////////////////////////////////////////////////////////////////////////////////////

    public function CPanel()
    {
        $this->Header(); //header pliku
        $this->DrawUp(); //gorna belka
        $this->DrawLeft(); //lewa belka z menu
        $this->sql = new Sql();
        ?><div id="srodek"><?php
    }
    
    public function __destruct()
    {
        $this->sql->Close();
        ?>
          </div>
        </body>
      </html>
      <?php
    }

    public function AddNote()
    {
        if(isset($_POST['submit']))
        {
            $title = $_POST['title'];
            $note = $_POST['note'];

            $title = trim($title);
            $note = trim($note);

            if (empty($title) || empty($note))
            {
                $this->SendInfo("Notka nie dodana z powodu braku tytułu lub treści");
            }
            else
            {
                if ($this->sql->AddArticle($title, $note))
                    $this->SendInfo("News został wysłany");
                else
                    $this->SendInfo("News nie został wysłany");
            }

            $this->DrawInfo();

            ?><br /><?php
        }

        $this->DrawTextAreas("addNote");
    }

    public function EditArticles()
    {
        //przycisk, ktorym przenosi sie do kosza
        if (isset($_POST['moveToBin']))
        {
            @$page = $_GET['page'];
            if ($page == null)
                $page = 0;

            $isChanged = false; //czy jakis zostal zmodyfikowany
            if ($this->sql->UpdateArticleLink($_POST['visibleIn'], $page, Sql::NOTHING, $isChanged))
            {
                if ($isChanged)
                    $this->SendInfo("Link/Linki do newsów zostały zaktualizowane");
            }
            else
                $this->SendInfo("Link/Linki do newsów nie zostały zaktualizowane");

            @$checkboxes = $_POST['check']; //zlapanie z formularza checknietych checkboxow

            if (count($checkboxes) > 0)
            {
                if ($this->sql->RemoveNewsToBin($checkboxes))
                    $this->SendInfo("Artykuł/Artykuły zostały przeniesione do kosza");
                else
                    $this->SendInfo("Nie można przenieść artykułu/artykułów do kosza");
            }

            $id = 0; //zeby przeszedl do malowania tabelki
        }
        else if(isset($_POST['submit'])) //po kliknieciu wyslij przy edycji notki
        {
            $title = $_POST['title'];
            $note = $_POST['note'];
            @$id = $_GET['id'];

            $title = trim($title);
            $note = trim($note);

            if (empty($title) || empty($note))
                $this->SendInfo("News nie zaktualizowany z powodu braku tytułu lub treści");
            else if ($id < 1)
                $this->SendInfo("News nie zaktualizowany z powodu złego id");
            else
            {
                if ($this->sql->EditArticle($id, $title, $note))
                    $this->SendInfo("News został zaktualizowany");
                else
                    $this->SendInfo("News nie został zaktualizowany");

                $id = 0; //zeby przeszedl do malowania tabelki
            }

            ?><br /><?php
        }
        else
        {
            @$id = $_GET['id'];
        }

        if ($id != 0) //jesli wybrana zostala jakas notka to dawaj formularz, a jesli nie...
        {
            $news = $this->sql->ReadArticle($id);
            $this->DrawTextAreas("editNote&id=$id", $news['title'], $news['note']);
        }
        else //... to wyswietli sie lista notek do wybrania
        {
            $this->DrawInfo();

            @$page = $_GET['page'];

            $news = $this->sql->ReadArticles(Sql::NOTHING, $page*$this->howMany, $this->howMany);
            $count = $this->sql->NumberOfArticles(Sql::NOTHING);
            $this->DrawTable($news, CPanel::EDIT, $this->sql->ReadLinks());
            $this->DrawPaging($this->howMany, $count);
        }
    }

    public function Bin()
    {
        if (isset($_POST['restore']) || (isset($_POST['remove'])))
        {
            @$checkboxes = $_POST['check']; //zlapanie z formularza checknietych checkboxow
            if (count($checkboxes) > 0)
            {
                if (isset($_POST['restore']))
                {
                    if ($this->sql->RecoverNewsFromBin($checkboxes))
                        $this->SendInfo("Artykuł/Artykuły zostały przywrócone");
                    else
                        $this->SendInfo("Nie można przywrócić artykułu/artykułów");
                }
                else if (isset($_POST['remove']))
                {
                    if ($this->sql->RemoveArticle($checkboxes))
                        $this->SendInfo("Artykuł/Artykuły zostały usunięte");
                    else
                        $this->SendInfo("Nie można usunąć artykułu/artykułów");
                }
            }
            else
                 $this->SendInfo("Nie zaznaczono żadnego arykułu");
        }

        $this->DrawInfo();
        @$page = $_GET['page'];
        if ($page == null)
            $page = 0;
        $news = $this->sql->ReadArticles(Sql::BIN, $page*$this->howMany, $this->howMany);
        $count = $this->sql->NumberOfArticles(Sql::BIN);
        $this->DrawTable($news, CPanel::BIN, $this->sql->ReadLinks());
        $this->DrawPaging($this->howMany, $count);
    }

    public function EditLinks()
    {
        @$id = $_GET['id'];
        if ($id == null)
            $id = 0;

        if (isset($_POST['remove']))
        {
            @$checkboxes = $_POST['check']; //zlapanie z formularza checknietych checkboxow
            if (count($checkboxes) > 0)
            {
                if ($this->sql->RemoveLink($checkboxes))
                    $this->SendInfo("Link/linki zostały usunięte");
                else
                    $this->SendInfo("Nie można usunąć linku/linków");
            }
            else
                 $this->SendInfo("Nie zaznaczono żadnego linku");
        }
        else if(isset($_POST['newlink'])) //dodanie nowego linku
        {
            $link = $_POST['link'];
            $link = trim($link);

            if (empty($link))
            {
                $this->SendInfo("Nie dodano nowego linku z powodu braku nazwy linku");
            }
            else
            {
                if ($this->sql->AddLink($link))
                    $this->SendInfo("Link został dodany");
                else
                    $this->SendInfo("Link nie został dodany");
            }

            ?><br /><?php
        }
        else if (isset($_POST['editlink'])) //lub edycja linku
        {
            $link = $_POST['link'];
            $link = trim($link);

            if (empty($link))
            {
                $this->SendInfo("Nie dodano nowego linku z powodu braku nazwy linku");
            }
            else
            {
                if ($this->sql->EditLink($id, $link))
                    $this->SendInfo("Link został zmieniony");
                else
                    $this->SendInfo("Link nie został zmieniony");
            }

            ?><br /><?php
        }

        $this->DrawInfo();
        
        ?>
        <form method="POST" action="panel.php?task=editLinks<?php ($id != 0) ? print("&id=$id") : print("") ?>">
            <b>Dodaj nowy link:</b> <input type="text" size="65" name="link"
            <?php
                if ($id != 0)
                {
                    $link = $this->sql->ReadLinks((int)$id);
                    ?> value="<?php echo $link[0]['link'] ?>"<?php
                }
            ?>
            /><br />
            <input type="submit" value="<?php ($id != 0) ? print("Edytuj") : print("Wyślij"); ?>" name="<?php ($id != 0) ? print("editlink") : print("newlink"); ?>" />
        </form>
        <?php

        $links = $this->sql->ReadLinks();
        $this->DrawTable($links, CPanel::LINKS);
    }

    public function Preferences()
    {
        
    }
}

?>
