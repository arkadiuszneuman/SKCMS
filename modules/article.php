<?php
	$privi = new Privileges();

	if (isset($_GET['delete']))
	{
		$userPrivileges = $sql->CheckPrivileges($_SESSION['name']);
		if (($userPrivileges >= 64) || (($privi->CheckPrivilege(2, $userPrivileges)) && ($sql->GetCommentAuthor($_GET['delete']) == $_SESSION['name'])))
		{
			$sql->DeleteComment($_GET['delete']);
		}
		
		$id = $_GET['id'];
		header("Location: index.php?action=article&id=$id");
	}

	$id = $_GET['id'];

	if(isset($_POST['submit']) && ($_POST['hash'] != $_SESSION['hash']))
	{
		$name = $_POST['author'];
		$note = nl2br(htmlspecialchars($_POST['note'], true));
		$user_id = $_POST['user_id'];

		if ($name != "")
		{
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
	}

	$news = $sql->ReadArticle($id);
	if ($news != null)
	{
		if ($sql->GetSetting("comments") == 1)
		{
			$commentsBlock = "";
			$comments =	$sql->ReadComments($id);
			if ($comments != null)
			{
				$userPrivileges = $sql->CheckPrivileges($_SESSION['name']);
				foreach ($comments as $comment)
				{	
					if ($userPrivileges >= 64)
					{
						$addThings = "<a href=\"?action=article&id=".$id."&delete=".$comment['id']."\">Usuń komentarz</a>";
					}
					elseif (($privi->CheckPrivilege(2, $userPrivileges)) && ($comment['user'] == $_SESSION['name']))
					{
						$addThings = "<a href=\"?action=article&id=".$id."&delete=".$comment['id']."\">Usuń komentarz</a>";
					}
					else
						$addThings = "";

					$data = array("commentId"=>$comment['id'], "author"=>$comment['user'], "date"=>$comment['date'],
							"note"=>$comment['note'], "addThings"=>$addThings);
					$commentsBlock = $commentsBlock.$template->Render("comment_body", $data);
				}
			}
			else
			{
				$commentsBlock = "Brak komentarzy";
			}

			$commentsNumber = "";
			$count = $sql->NumberOfComments($id);
			if ($count < 5)
			{
				if ($count == 0)
					$commentsNumber = $commentsNumber.""."Nie ma komentarzy";
				elseif ($count == 1)
					$commentsNumber = $commentsNumber.""."Jeden komentarz";
				else
					$commentsNumber = $commentsNumber."".$count.""." komentarze";
			}
			else
			{
				$commentsNumber = $commentsNumber."".$count.""." komentarzy";
			}

			if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] == false)
			{
				$data = array("title"=>$news['title'], "author"=>$news['author'], "date"=>$news['date'], "comments"=>"0",
						"content"=>$news['note'], "comments"=>$commentsBlock, "hash"=>GenerateHash(), 
						"commentAuthor"=>"", "user_id"=>"0", "commentsNumber"=>$commentsNumber);
			}
			else
			{
				$data = array("title"=>$news['title'], "author"=>$news['author'], "date"=>$news['date'], "comments"=>"0",
						"content"=>$news['note'], "comments"=>$commentsBlock, "hash"=>GenerateHash(), 
						"commentAuthor"=>$_SESSION['name'], "readonly"=>"readonly", "user_id"=>$sql->ReturnUserID($_SESSION['name']),
						"commentsNumber"=>$commentsNumber);
			}
			$newsBlock = $newsBlock.$template->Render("article_details", $data);
		}
		else
		{
			$data = array("title"=>$news['title'], "author"=>$news['author'], "date"=>$news['date'],
				"content"=>$news['note']);

			$newsBlock = $newsBlock.$template->Render("article_details_nc", $data);
		}

		$data = array("content"=>$newsBlock);
		$mainContent = $template->Render("news", $data);
	}
	else
	{
		$data = array("content"=>"Brak arytkułów w podanym linku");
		$mainContent = $template->Render("news", $data);
	}
?>
