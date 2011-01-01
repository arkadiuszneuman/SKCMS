<?php
	session_start();
	include ('includes/layout.php');

	$newsBlock = "";
	$mainContent = "";
	$sidebarContent = "";
	$template = new Layout();
?>

<script type="text/javascript" src="./javascript/ajax.js"></script>
<link rel="stylesheet" type="text/css" href="./css/windowLogin.css" />

<?php
    function showPaging($page, $howMany, $count)
    {
		$toReturn = "";
        $link = $_GET['link'];
        if ($page > 0) //wyswietlenie poprzednia strona
        {
            $toReturn = $toReturn."<a href=\"./index.php?link=".$link."&page=0\">Pierwsza</a>
				<a href=\"./index.php?link=".$link."&page=".($page-1)."\">Poprzednia strona</a>";
        }
        else
        {
            $toReturn = $toReturn."Pierwsza  Poprzednia strona    ";
        }

        for ($i = $page-2; $i < $page+5; ++$i) //wyswietlenie numerow stron
        {
            if ($i > 0 && ($i-1)*$howMany < $count) //numery stron tylko w zakresie min max
            {
                if ($i != $page + 1) //link nie moze byc aktualna strona
                {
                    $toReturn = $toReturn."<a href=\"./index.php?link=".$link."&page=".($i-1)."\">".$i."</a>";
                }
                else
                {
                    $toReturn = $toReturn."".$i." ";
                }
            }
        }

        if (($page+1)*$howMany < $count) //wyswietlenie nastepna strona i ostatnia strona
        {
            $toReturn = $toReturn."<a href=\"./index.php?link=".$link."&page=".($page+1)."\">Następna strona</a>";
            if ($count%$howMany != 0) //jesli ilosc newsow przez ilosc newsow na strone jest nierowna
            {
                $toReturn = $toReturn."<a href=\"./index.php?link=".$link."&page=".((int)($count/$howMany))."\">Ostatnia</a>";
            }
            else
            {
                $toReturn = $toReturn."<a href=\"./index.php?link=".$link."&page=".(($count/$howMany) - 1)."\">Ostatnia</a>";
            }
        }
        else
        {
            $toReturn = $toReturn."Następna strona   Ostatnia";
        }

		return $toReturn;
    }

    include('sql.php');
	echo $template->RenderHeader("Kermitek");
	if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] == false)
    {
        ?>
<!--        <a href="./user.php?task=login">Panel administracyjny</a>-->
        <?php
		$mainContent = $mainContent."<a href=\"#\" onclick=\"Login('login');\">Zaloguj</a>";
    }
    else
    {
        ?>
<!--        <a href="./user.php?task=login">Panel administracyjny</a>-->
	<?php
		$data = array("title"=>"Użytkownik", "content"=>"<a href=\"./panel/\">Panel administracyjny</a> &nbsp; &nbsp;
        <a href=\"#\" onclick=\"Login('logoff')\">Wyloguj</a>");
		$sidebarContent = $sidebarContent."".$template->Render("sidebar_item", $data);
    }
    
    //okienko z logowaniem
    ?>
 <div id="windowLogin"></div>
    <?php

    //wyswietlenie linkow
    $sql = new Sql();
    $links = $sql->ReadLinks();
	$menu = "";

    foreach ($links as $link)
    {
        $txt = $link['link'];
        $txt =  str_replace(' ','_',$txt);
		$menu = $menu."<li><a href=\"./index.php?link=".$txt."\" class=\"link\">".$link['link']."</a></li>";
    }
	$data = array("menu"=>$menu);
	echo $template->Render("menu", $data);

    @$page = $_GET['page'];
    $howMany = 3;
   
    foreach ($links as $link)
    {
        if (@$_GET['link'] == null)
            $_GET['link'] = str_replace(' ','_',$link['link']);

        if (str_replace(' ','_',$link['link']) == $_GET['link'])
        {
            $news = $sql->ReadArticles(Sql::NOTHING, $page*$howMany, $howMany, $link['id']);

            if ($news != null)
            {
                foreach($news as $n)
                {
					$data = array("title"=>$n['title'], "author"=>"Kermit", "date"=>$n['date'], "comments"=>"0",
					"content"=>nl2br($n['note']));
					$newsBlock = $newsBlock.$template->Render("news_item", $data);
                }

                $count = $sql->NumberOfArticles(Sql::NOTHING, $link['id']);
                if ($count > $howMany) //wyswietlenie pagingu tylko w przypadku wiekszej ilosci newsow niz strona
                    $newsBlock = $newsBlock."".showPaging($page, $howMany, $count);
            }
            else
                echo "Brak arytkułów w podanym linku";

            break;
        }
    }
	$data = array("content"=>$newsBlock);
	$mainContent = $mainContent."".$template->Render("news", $data);
	$data = array("content"=>$sidebarContent);
	$mainContent = $mainContent."".$template->Render("sidebar", $data);
	$data = array("cont"=>$mainContent);
	echo $template->Render("content", $data);
    ?>
<!--    <div id="news">Ładowanie</div></div>--> 
    <?php
	echo $template->RenderFooter("Copyright SKCMS TEAM :D");
    $sql->Close();
?>
