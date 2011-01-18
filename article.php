<?php
	session_start();
	include ('includes/layout.php');
	include ('includes/init.php');
	include ('includes/functions.php');
	include ('sql.php');

    ?><div id="all"><?php

	$newsBlock = "";
	$mainContent = "";
	$sidebarContent = "";
	$template = new Layout();
	$id = $_GET['id'];

	$headerData = array("title"=>"SKCMS - Zwierzęcy System Zarządzania Treścią", 
	"includes"=>"<script type=\"text/javascript\" src=\"./javascript/ajax.js\"></script>\n
        <script type=\"text/javascript\" src=\"./javascript/registerfrm.js\"></script>\n
        <link rel=\"stylesheet\" type=\"text/css\" href=\"./css/windowLogin.css\">");
	echo $template->Render("header", $headerData);  

	if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] == false)
    {
		$data = array("title"=>"Użytkownik", "content"=>"<a href=\"#\" onclick=\"Login('login');\">Zaloguj</a>");

		$sidebarContent = $sidebarContent."".$template->Render("sidebar_item", $data);
    }
    else
    {
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
	
	if(isset($_POST['submit']) && ($_POST['hash'] != $_SESSION['hash']))
	{
		$name = $_POST['author'];
		$note = $_POST['note'];
		$user_id = $_POST['user_id'];

		echo $user_id;

		if ($user_id == 0)		
		{
			$sql->AddComment($id, $name, null, $note);
		}
		else
		{
			$sql->AddComment($id, null, $user_id, $note);
		}
		
		$_SESSION['hash'] = $_POST['hash'];
	}

	$news = $sql->ReadArticle($id);
	if ($news != null)
	{
		$commentsBlock = "";
		$comments =	$sql->ReadComments($id);
		if ($comments != null)
		{
			foreach ($comments as $comment)
			{	
				$data = array("commentId"=>$comment['id'], "author"=>$comment['user'], "date"=>$comment['date'],
					"note"=>$comment['note']);
				$commentsBlock = $commentsBlock.$template->Render("comment_body", $data);
			}
		}
		else
		{
			$commentsBlock = "Brak komentarzy";
		}

		if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] == false)
		{
			$data = array("title"=>$news['title'], "author"=>$news['author'], "date"=>$news['date'], "comments"=>"0",
				"content"=>$news['note'], "comments"=>$commentsBlock, "hash"=>GenerateHash(), 
				"commentAuthor"=>"", "user_id"=>"0");
		}
		else
		{
			$data = array("title"=>$news['title'], "author"=>$news['author'], "date"=>$news['date'], "comments"=>"0",
				"content"=>$news['note'], "comments"=>$commentsBlock, "hash"=>GenerateHash(), 
				"commentAuthor"=>$_SESSION['name'], "readonly"=>"readonly", "user_id"=>$sql->ReturnUserID($_SESSION['name']));
		}
		$newsBlock = $newsBlock.$template->Render("article_details", $data);
	
		$data = array("content"=>$newsBlock);
		$mainContent = $template->Render("news", $data);
	}
	else
	{
		$data = array("content"=>"Brak arytkułów w podanym linku");
		$mainContent = $template->Render("news", $data);
	}

	$data = array("content"=>$sidebarContent);
	$asideContent = $template->Render("sidebar", $data);
	$data = array("mainContent"=>$mainContent, "aside"=>$asideContent);
	echo $template->Render("content", $data);
	echo $template->RenderFooter("Copyright SKCMS TEAM :D");

    ?></div><?php

    $sql->Close();
?>
