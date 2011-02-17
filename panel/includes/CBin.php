<?php

include_once("CPanel.php");

class CBin extends CPanel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function Bin()
    {
        if (isset($_POST['restore']) || (isset($_POST['remove'])))
        {
            @$checkboxes = $_POST['check']; //zlapanie z formularza checknietych checkboxow
            if (count($checkboxes) > 0)
            {
                if (isset($_POST['restore']))
                {
                    if ($this->sql->BinToArticles($checkboxes))
                        $this->SendInfo("Artykuł/Artykuły zostały przywrócone");
                    else
                        $this->SendInfo("Nie można przywrócić artykułu/artykułów");
                }
                else if (isset($_POST['remove']))
                {
                    if ($this->sql->RemoveArticle($checkboxes))
                        $this->SendInfo("Artykuł/Artykuły zostały usunięte");
                    else
                        $this->SendInfo("Nie można usunąć artykułu/artykułów");
                }
            }
            else
                 $this->SendInfo("Nie zaznaczono żadnego arykułu");
        }

        $this->DrawInfo();
        @$page = $_GET['page'];
        if ($page == null)
            $page = 0;
        $news = $this->sql->ReadArticles(Sql::BIN, $page*$this->howMany, $this->howMany);
        $count = $this->sql->NumberOfArticles(Sql::BIN);
        $this->DrawTable($news, CPanel::BIN, $this->sql->ReadLinks());
        $this->DrawPaging($count);
    }
}

?>
