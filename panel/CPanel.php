<?php
include_once('../sql.php');
include("../CForm.php");

class CPanel
{
    protected $sql = null;
    protected $howMany = 20; //ilosc artykulow w tabelce
    protected $privileges = 0; //aktualny poziom uprawnien, ladowany na bierzaco

    //uzywane do DrawTable
    const EDIT = 1;
    const BIN = 2;
    const LINKS = 4;
    const USERS = 8;

    protected function Header()
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

    protected function DrawUp()
    {
        ?>
            <div id="header">
                <div id="left"><div id="logo"></div></div>
                <div id="center">PANEL ADMINISTRACYJNY</div>
            </div>
        <?php
    }

    protected function DrawLeft()
    {
        ?>
            <div id="menu">
                <div id="links">
                    <div id="hello">Witaj <?php echo $_SESSION['name'] ?></div>
                    <?php
                    if (Privileges::CheckPrivilege(Privileges::ARTICLES, $this->privileges))
                    {
                        ?><a href="./?task=addNote" class="button">Dodaj artykuł</a>
                        <a href="./?task=articles&page=0" class="button">Artykuły</a>
                        <a href="./?task=bin" class="button">Kosz</a><?php
                    }
                    if (Privileges::CheckPrivilege(Privileges::MENU, $this->privileges))
                    {
                        ?><a href="./?task=links" class="button">Menu</a>
						<a href="./?task=blocks" class="button">Bloki</a><?php
                    }
                    if (Privileges::CheckPrivilege(Privileges::USERS, $this->privileges))
                    {
                        ?><a href="./?task=users" class="button">Użytkownicy</a><?php
                    }
                    ?>
                    <a href="./?task=preferences" class="button">Ustawienia</a>
                </div>
                <div id="shadowRight"></div>
            </div>
        <?php
    }

    //załadownie do zmiennych danych
    protected function LoadPreferences()
    {
        $this->privileges = $this->sql->CheckPrivileges($_SESSION['name']);
    }

    //metody odpowiedzialne za ramkę zieloną z informacjami
    protected function SendInfo($info)
    {
        if (!isset($_SESSION['info']))
            $_SESSION['info'] = "";

        if ($_SESSION['info'] != "")
            $_SESSION['info'] = $_SESSION['info']."<br />";
        $_SESSION['info'] = $_SESSION['info'].$info;
    }

    protected function DrawInfo()
    {
        //wlaczenie zielonej info u gory
        if (isset($_SESSION['info']) && !empty($_SESSION['info']))
        {
            ?><div id="info"><?php echo $_SESSION['info'] ?></div><?php
            $_SESSION['info'] = "";
        }
    }

    /**
     * 
     * uzywane przy dodawaniu i edycji artykulu
     * @param string $action Akcja wykonywana po kliknieciu przycisku Wyślij
     * @param string $title Tytuł wpisany w pole tekstowe (opcjonalnie)
     * @param string $article Artykuł wpisany w pole tekstowe (opcjonalnie)
     * @param string $author Autor wpisany w pole tekstowe (opcjonalnie)
     * @param int $linkID Id linku, ktory ma byc wybrany w comboboksie (opcjonalnie)
     */
    protected function DrawTextAreas($action, $title = null, $article = null, $author = null, $linkID = null)
    {
		if ($author == null)
			$author = $_SESSION['name'];
        
		//nowa forma
        $form = new CForm(CForm::POST, "?task=$action");
        //textbox z tytulem w formie
        $text = new CTextBox("Tytuł: ", "title");
        $text->SetValue($title);
        $text->SetAddionalAttribs('size="65"');
        $form->AddItem($text);
        //combobox z linkami w formie
		$text = new CComboBox("Kategoria: ", "link");
		$links = $this->sql->ReadCategories();
		$text->AddItem("Brak", "");
		foreach ($links as $link)
		{
			if ($linkID == $link['id'])
			{
				$text->AddItem($link['link'], $link['id'], true);
			}
			else
			{
				$text->AddItem($link['link'], $link['id']);
			}
		}
		$text->SetAddionalAttribs('size="65"');
		$form->AddItem($text);
		//tekstarea z poczatkiem artykulu
        $text = new CTextArea("Treść: ", "firstPart");
        $text->SetValue($article);
        $text->SetAddionalAttribs('rows="20" cols="100"');
        $form->AddItem($text);
        //tekstarea z rozwinieciem artykulu
        $text = new CTextArea("Rozwinięcie (opcjonalnie): ", "secondPart");
        $text->SetValue($article);
        $text->SetAddionalAttribs('rows="20" cols="100"');
        $form->AddItem($text);
        //textarea z autorem
		$text = new CTextBox("Autor: ", "author");
		$text->SetValue($author);
		$text->SetAddionalAttribs('size="65"');
		$form->AddItem($text);
		//przycisk
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
    protected function DrawPaging($count)
    {
        if ($this->howMany >= $count) //jesli ilosc artykulow na stronie jest wieksza od ilosci ogolnej arykulow to nie maluj pagingu
            return;

        @$task = $_GET['task'];
        @$page = $_GET['page'];

        if ($task == null)
            $task = "articles";
        if ($page == null)
            $page = 0;

        if ($page > 0) //wyswietlenie poprzednia strona
        {
            ?><a href="./?task=<?php echo $task ?>&page=0">Pierwsza</a>   <?php
            ?><a href="./?task=<?php echo $task ?>&page=<?php echo ($page-1) ?>">Poprzednia strona</a>   <?php
        }
        else
        {
            echo "Pierwsza  ";
            echo "Poprzednia strona    ";
        }

        for ($i = $page-2; $i < $page+5; ++$i) //wyswietlenie numerow stron
        {
            if ($i > 0 && ($i-1)*$this->howMany < $count) //numery stron tylko w zakresie min max
            {
                if ($i != $page + 1) //link nie moze byc aktualna strona
                {
                    ?><a href="./?task=<?php echo $task ?>&page=<?php echo ($i-1) ?>"><?php echo $i ?></a>   <?php
                }
                else
                {
                    echo $i." ";
                }
            }
        }

        if (($page+1)*$this->howMany < $count) //wyswietlenie nastepna strona i ostatnia strona
        {
            ?><a href="./?task=<?php echo $task ?>&page=<?php echo ($page+1) ?>">Następna strona</a>   <?php
            if ($count%$this->howMany != 0) //jesli ilosc newsow przez ilosc newsow na strone jest nierowna
            {
                ?><a href="./?task=<?php echo $task ?>&page=<?php echo ((int)($count/$this->howMany)) ?>">Ostatnia</a>   <?php
            }
            else
            {
                ?><a href="./?task=<?php echo $link ?>&page=<?php echo (($count/$this->howMany) - 1) ?>">Ostatnia</a>   <?php
            }
        }
        else
        {
            echo "Następna strona   ";
            echo "Ostatnia";
        }
    }

    //malowanie tabeli z artykulami data - dane malowane w tabelce, task - jaki typ tabelki, edycji lub newsow, links - linki potrzebne w tabelce kosz i edycja
    protected function DrawTable($data, $task, $links = null)
    {
        if ($data == null)
            return;

        @$page = $_GET['page'];
        if ($page == null)
            $page = 0;


        if ($task == CPanel::EDIT)
        {
            ?>
                <form name="binFrm" method="POST" action="./?task=articles&page=<?php echo $page ?>">
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
                <form name="binFrm" method="POST" action="./?task=bin&page=<?php echo $page ?>">
                <div id="options">
                    Zaznaczone: <br />
                    <?php
                    $item = new CButton("Przywróć", "restore");
                    $item->SetClass("buttonInput");
                    $item->Draw();
                    if (Privileges::CheckPrivilege(Privileges::BIN, $this->privileges)) //usuwanie artykulow z kosza
                    {
                        $item = new CButton("Usuń", "remove");
                        $item->SetClass("buttonInput");
                        $item->Draw();
                    }
                    ?>
                </div>
            <?php
        }
        else if ($task == CPanel::LINKS)
        {
            ?>
                <form name="binFrm" method="POST" action="./?task=links&page=<?php echo $page ?>">
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
                            <td><img alt="Zapisz" title="Zapisz zmiany" id="saveOrder" src="./graphics/floppy_disk.png" /> Porządek</td>
                        </tr>
            <?php
        }
        else if ($task == CPanel::USERS)
        {
            ?>
                    <table id="tabPanel">
                        <tr id="upper">
                            <td>Lp</td>
                            <td>Użytkownik</td>
                            <td>Uprawnienia</td>
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
                <?php echo (($page) * $this->howMany + $i) ?>
            </td>

            <?php
            if ($task == CPanel::EDIT || $task == CPanel::BIN || $task == CPanel::LINKS)
            {
                ?>
                <td>
                    <input type="checkbox" name="check[]" value="<?php echo $n['id'] ?>" />
                </td>
                <?php
            }
            ?>

            
            <?php
                if ($task == CPanel::EDIT || $task == CPanel::BIN)
                {
                    ?><td class="topic">
                        <a href="./?task=articles&id=<?php echo $n['id'] ?>" title="Kliknij, aby edytować"><?php echo $n['title'] ?></a>
                        <?php
                }
                else if ($task == CPanel::LINKS)
                {
                    ?><td class="topic">
                        <a href="./?task=links&id=<?php echo $n['id'] ?>" title="Kliknij, aby edytować"><?php echo $n['link'] ?></a>
                        <?php
                }
                else if ($task == CPanel::USERS)
                {
                    if ($n['login'] !== $_SESSION['name'])
                    {
                        ?><td><a href="#" onclick="OpenWindow(<?php echo $n['id'] ?>, '<?php echo $n['login'] ?>', <?php echo $n['privileges'] ?>);" title="Kliknij, aby edytować"><?php echo $n['login'] ?></a><?php
                    }
                    else
                    {
                        ?><td><?php echo $_SESSION['name'];?></td><?php //uzytkownik nie ma mozliwości edytowania samego siebie
                    }
                }
            ?>
            </td>
            <td>

            <?php
            if ($task == CPanel::EDIT || $task == CPanel::BIN)
            {
                ?>
                
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
                <?php
            }
            else if ($task == CPanel::LINKS)
            {
                $txt = new CTextBox(null, $n['id']);
                $txt->SetAddionalAttribs('size="1"');
                $txt->SetValue($n['order']);
                $txt->Draw();
            }
            else if ($task == CPanel::USERS)
            {
                $privileges = Privileges::WhatPrivileges($n['privileges']);
                for ($x = 0; $x < count($privileges); ++$x)
                {
                    echo Privileges::PrivilegeToString($privileges[$x]);
                    if ($x != count($privileges)-1)
                    {
                        ?>, <?php
                    }
                }
            }
            ?>
            </td>
            </tr>
            <?php

            ++$i;
            $even = !$even;
        }
        ?></table><?php

        ?></form><?php
    }

///////////////////////////////////////////////////////////////////////////////////////////////////////

    public function __construct()
    {
        $this->Header(); //header pliku
        $this->sql = new Sql();
        $this->LoadPreferences(); //ladowanie ustawien i uprawnien
        $this->DrawUp(); //gorna belka
        $this->DrawLeft(); //lewa belka z menu            
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
}

?>
