<?php
	$howMany = 5;
	@$page = $_GET['page'];
	
	if (@$_GET['link'] == NULL)
	{
		$link = $sql->GetDefaultLink();
	}
	else
	{
		$link = $_GET['link'];
	}

	$news = $sql->ReadArticles(Sql::NOTHING, $page*$howMany, $howMany, $link);

	if ($news != null)
	{   
		foreach($news as $n)
		{   
			if ($sql->GetSetting("comments") == 1)
			{
				if ($n['secondPart'] != null)
					$data = array("id"=>$n['id'], "title"=>$n['title'], "author"=>$n['author'], "date"=>$n['date'], 
						"comments"=>$sql->NumberOfComments($n['id']), "content"=>$n['firstPart']."<br />"."<a href=\"index.php?action=article&id=".$n['id']."\">więcej</a>");
				else
					$data = array("id"=>$n['id'], "title"=>$n['title'], "author"=>$n['author'], "date"=>$n['date'], 
						"comments"=>$sql->NumberOfComments($n['id']), "content"=>$n['firstPart']);
				$newsBlock = $newsBlock.$template->Render("news_item", $data);
			}
			else
			{
				if ($n['secondPart'] != null)
					$data = array("id"=>$n['id'], "title"=>$n['title'], "author"=>$n['author'], "date"=>$n['date'], 
							"content"=>$n['firstPart']."<br />"."<a href=\"index.php?action=article&id=".$n['id']."\">więcej</a>");
				else
					$data = array("id"=>$n['id'], "title"=>$n['title'], "author"=>$n['author'], "date"=>$n['date'], 
							"content"=>$n['firstPart']);
				$newsBlock = $newsBlock.$template->Render("news_item_nc", $data);
			}
		}
		
		$count = $sql->NumberOfArticles(Sql::NOTHING, $link);

		if ($count > $howMany) //wyswietlenie pagingu tylko w przypadku wiekszej ilosci newsow niz strona
		{
			$newsBlock = $newsBlock."".$template->Render("news_paging", showPaging($page, $howMany, $count));
		}
	}
	else
	{   
		$newsBlock = $newsBlock."Brak arytkułów w podanym linku";
	}
?>
