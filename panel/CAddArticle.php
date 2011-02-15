<?php

class CAddArticle extends CPanel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function AddArticle()
    {
        if(isset($_POST['submit']))
        {
            $title = $_POST['title'];
            $note = $_POST['note'];
			$author = $_POST['author'];
			$link = $_POST['link'];

            $title = trim($title);
            $note = trim($note);

            if (empty($title) || empty($note))
            {
                $this->SendInfo("Notka nie dodana z powodu braku tytułu lub treści");
            }
            else
            {
                if ($this->sql->AddArticle($title, $note, $author, $link))
                    $this->SendInfo("News został wysłany");
                else
                    $this->SendInfo("News nie został wysłany");
            }

            $this->DrawInfo();

            ?><br /><?php
        }

        $this->DrawTextAreas("addNote");
    }
}
?>
