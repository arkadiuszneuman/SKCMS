<?php
	session_start();
	include ('includes/layout.php');
	include ('includes/init.php');
	include ('sql.php');

    $sql = new Sql();
    ?><div id="all"><?php

	$newsBlock = "";
	$mainContent = "";
	$sidebarContent = "";
	$template = new Layout($sql->GetSetting("defaultstyle"));

	echo $template->RenderHeader();

	if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] == false)
    {
		$data = array("title"=>"Użytkownik", "content"=>"<a href=\"#\" onclick=\"Login('login');\">Zaloguj</a>", "item_id"=>"login");
		$sidebarContent = $sidebarContent."".$template->Render("sidebar_item", $data);
    }
    else
    {
		$data = array("title"=>"Użytkownik", "content"=>"<a href=\"./panel/\">Panel administracyjny</a> &nbsp; &nbsp;
        <a href=\"#\" onclick=\"Login('logoff')\">Wyloguj</a>", "item_id"=>"login");
		$sidebarContent = $sidebarContent."".$template->Render("sidebar_item", $data);
    }
    
    //okienko z logowaniem
    ?>
        <div id="windowLogin"></div>
    <?php

    //wyswietlenie linkow
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
					$data = array("id"=>$n['id'], "title"=>$n['title'], "author"=>$n['author'], "date"=>$n['date'], 
					"comments"=>$sql->NumberOfComments($n['id']), "content"=>$n['note']);
					$newsBlock = $newsBlock.$template->Render("news_item", $data);
                }

                $count = $sql->NumberOfArticles(Sql::NOTHING, $link['id']);
                if ($count > $howMany) //wyswietlenie pagingu tylko w przypadku wiekszej ilosci newsow niz strona
                    $newsBlock = $newsBlock."".$template->Render("news_paging", showPaging($page, $howMany, $count));
            }
            else
            {
				$newsBlock = $newsBlock.""."Brak arytkułów w podanym linku";
			}

            break;
        }
    }
	$data = array("content"=>$newsBlock);
	$mainContent = $template->Render("news", $data);
	$data = array("content"=>$sidebarContent);
	$asideContent = $template->Render("sidebar", $data);
	$data = array("mainContent"=>$mainContent, "aside"=>$asideContent);
	echo $template->Render("content", $data);
	echo $template->RenderFooter();

    ?></div><?php

    $sql->Close();
?>
