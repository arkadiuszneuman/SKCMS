<?php
	session_start();
	include ('includes/layout.php');
	include ('includes/init.php');
	include ('sql.php');

    $sql = new Sql();

	$newsBlock = "";
	$mainContent = "";
	$sidebarContent = "";
	$template = new Layout($sql->GetSetting("defaultstyle"));

	echo $template->RenderHeader();

  	$data['title'] = "Użytkownik";
	$data['item_id'] = "loginBlock";
	$data['content'] = 	include("blocks/login.php");
	$sidebarContent = $sidebarContent."".$template->Render("sidebar_item", $data);

  	$data['title'] = "Galeria";
	$data['item_id'] = "galleryBlock";
	$data['content'] = 	include("blocks/gallery.php");
	$sidebarContent = $sidebarContent."".$template->Render("sidebar_item", $data);
    //okienko z logowaniem

    //wyswietlenie linkow
    $links = $sql->ReadLinks();
	$menu = "";

    foreach ($links as $link)
    {
        $txt = $link['link'];
        $txt =  str_replace(' ','_',$txt);
		if ($link['type'] == 0)
		{
			$menu = $menu."<li><a href=\"./index.php?link=".$link['id']."\" class=\"link\">".$link['link']."</a></li>";
		}
		else
		{
			$menu = $menu."<li><a href=\"".$link['value']."\" class=\"link\">".$link['link']."</a></li>";
		}
    }
	$data = array("menu"=>$menu);
	echo $template->Render("menu", $data);
   
   	if(@$_GET['action'] == NULL)
	{
		$action = 'articles';
	}
	else
	{
		$action = $_GET['action'];
	}

	include ('modules/'.$action.'.php');

	$data = array("content"=>$newsBlock);
	$mainContent = $template->Render("news", $data);
	$data = array("content"=>$sidebarContent);
	$asideContent = $template->Render("sidebar", $data);
	$data = array("mainContent"=>$mainContent, "aside"=>$asideContent);
	echo $template->Render("content", $data);
	echo $template->RenderFooter();

    $sql->Close();
?>
