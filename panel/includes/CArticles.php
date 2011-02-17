<?php

include_once("CPanel.php");

class CArticles extends CPanel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function Articles()
    {
		if (Privileges::CheckPrivilege(Privileges::ARTICLES, $this->privileges))
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
					if ($this->sql->ArticlesToBin($checkboxes))
						$this->SendInfo("Artykuł/Artykuły zostały przeniesione do kosza");
					else
						$this->SendInfo("Nie można przenieść artykułu/artykułów do kosza");
				}

				$id = 0; //zeby przeszedl do malowania tabelki
			}
			else if(isset($_POST['submit'])) //po kliknieciu wyslij przy edycji notki
			{
				$title = $_POST['title'];
				$firstPart = $_POST['firstPart'];
				$secondPart = $_POST['secondPart'];
				@$id = $_GET['id'];
				$author = $_POST['author'];
				$link = $_POST['link'];

				$title = trim($title);
				$firstPart = trim($firstPart);
				$secondPart = trim($secondPart);

				if (empty($title) || empty($firstPart))
					$this->SendInfo("Artykuł nie zaktualizowany z powodu braku tytułu lub treści");
				else if ($id < 1)
					$this->SendInfo("Artykuł nie zaktualizowany z powodu złego id");
				else
				{
					if ($this->sql->EditArticle($id, $title, $firstPart, $author, $link, $secondPart))
						$this->SendInfo("Artykuł został zaktualizowany");
					else
						$this->SendInfo("Artykuł nie został zaktualizowany");

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
				$this->DrawTextAreas("articles&id=$id", $news['title'], $news['firstPart'], $news['secondPart'], $news['author'], $news['link']);
			}
			else //... to wyswietli sie lista notek do wybrania
			{
				$this->DrawInfo();

				@$page = $_GET['page'];

				$news = $this->sql->ReadArticles(Sql::NOTHING, $page*$this->howMany, $this->howMany);
				$count = $this->sql->NumberOfArticles(Sql::NOTHING);
				$this->DrawTable($news, CPanel::EDIT, $this->sql->ReadCategories());
				$this->DrawPaging($count);
			}
		}
		else
		{
			echo "Nie możesz tu być!";
		}
    }
}

?>
