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
            $title = $_POST['title']; //tytul
            $firstPart = $_POST['firstPart']; //pierwsza czesc artykulu
            $secondPart = $_POST['secondPart']; //druga czesc artykulu widoczna po rozwinieciu
			$author = $_POST['author']; //autor
			$link = $_POST['link']; //link do ktorego przynalezy artykul

            $title = trim($title);
            $firstPart = trim($firstPart);
            $secondPart = trim($secondPart);

            if (empty($title) || empty($firstPart))
            {
                $this->SendInfo("Artykuł nie dodany z powodu braku tytułu lub treści");
            }
            else
            {
                if ($this->sql->AddArticle($title, $firstPart, $author, $link, $secondPart))
                    $this->SendInfo("Artykuł został wysłany");
                else
                    $this->SendInfo("Artykuł nie został wysłany");
            }

            $this->DrawInfo();

            ?><br /><?php
        }

        $this->DrawTextAreas("addNote");
    }
}
?>
