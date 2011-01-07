<?php

class CPreferences extends CPanel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function Preferences()
    {
        //TODO SPRAWDZAC PRZY KAZDYM WEJSCIU UPRAWNIENIA
        if (isset($_POST['save']))
        {
            $this->howMany = $_POST['countTable'];
            if ($this->sql->SavePreferences($this->howMany))
                $this->SendInfo("Zapisano zmiany");
            else
                $this->SendInfo("Nie zapisano zmian");
        }

        $this->DrawInfo();

        ?><h2>USTAWIENIA:</h2><div id="preferences"><?php
        $form = new CForm(CForm::POST, "?task=preferences");
        $select = new CComboBox("Ilość artykułów w tabeli: ", "countTable");
        $select->AddItem("5");
        $select->AddItem("10");
        $select->AddItem("15");
        $select->AddItem("20");
        $select->AddItem("30");
        $select->AddItem("50");
        $select->Selected($this->howMany); //ustawienie wybranej opcji w combo
        $form->AddItem($select);

        $form->AddItem("<br />");
        $form->AddItem("<br />");
        $form->AddItem("<br />");
        $form->AddItem("<br />");
        $form->AddItem("<br />");
        $form->AddItem("<br />");
        $form->AddItem(new CButton("Zapisz zmiany", "save"));
        $form->Draw();
        ?></div><?php
    }
}

?>
