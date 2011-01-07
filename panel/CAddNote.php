<?php

class CAddNote extends CPanel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function AddNote()
    {
        if(isset($_POST['submit']))
        {
            $title = $_POST['title'];
            $note = $_POST['note'];

            $title = trim($title);
            $note = trim($note);

            if (empty($title) || empty($note))
            {
                $this->SendInfo("Notka nie dodana z powodu braku tytułu lub treści");
            }
            else
            {
                if ($this->sql->AddArticle($title, $note))
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
