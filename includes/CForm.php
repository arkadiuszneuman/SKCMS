<?php

abstract class CItem
{
    protected $id, $name,$value, $class, $onclick, $onkeyup, $addional;

    protected function SetNode($node, $val)
    {
        echo $node.'="'.$val.'" ';
    }

    public function SetAddionalAttribs($addional)
    {
        $this->addional = $addional;
    }

    public function SetClass($class)
    {
        $this->class = $class;
    }

    abstract public function Draw();
    abstract public function GetText();

    public function SetId($id)
    {
        $this->id = $id;
    }

    public function SetOnclick($onclick)
    {
        $this->onclick = $onclick;
    }

    public function SetOnKeyUp($onkeyup)
    {
        $this->onkeyup = $onkeyup;
    }

    public function CItem($name = null, $id = null, $class = null)
    {
        $this->name = $name;
        $this->id = $id;
    }
}

abstract class CInput extends CItem
{
    private $type, $text;

    const TEXT = 1;
    const PASSWORD = 2;
    const SUBMIT = 4;
    const CHECKBOX = 8;

    public function CInput($type, $text = null, $name = null, $id = null)
    {
        $this->type = $type;       
        $this->text = $text;

        parent::CItem($name, $id);
    }

    //text bedzie wyswietlany obok inputu
    public function GetText()
    {
        return $this->text;
    }

    
    public function SetValue($value)
    {
        $this->value = $value;
    }
    

    public function Draw()
    {
        if ($this instanceof CTextArea)
        {
            ?><textarea <?php
        }
        else
        {
        ?>
            <input type="<?php
                    switch($this->type)
                    {
                        case CInput::TEXT:
                            echo "text";
                            break;
                        case CInput::PASSWORD:
                            echo "password";
                            break;
                        case CInput::SUBMIT:
                            echo "submit";
                            break;
                        case CInput::CHECKBOX:
                            echo "checkbox";
                            break;
                    }
               ?>" <?php
        }
               if ($this->name != null)
               {
                   $this->SetNode("name", $this->name);
               }
               if ($this->id != null)
               {
                   $this->SetNode("id", $this->id);
               }
               if ($this->value != null && !($this instanceof CTextArea))
               {
                   $this->SetNode("value", $this->value);
               }
               if ($this->onclick != null)
               {
                   $this->SetNode("onclick", $this->onclick);
               }
               if ($this->onkeyup != null)
               {
                   $this->SetNode("onkeyup", $this->onkeyup);
               }
               if ($this->class != null)
               {
                   $this->SetNode("class", $this->class);
               }

               if ($this->addional != null)
                   echo $this->addional;

               if ($this instanceof CTextArea)
               {
                   ?>><?php
               }
               else
               {
                   ?> /><?php
               }
               if ($this instanceof CTextArea)
               {
                   if ($this->value != null)
                        echo $this->value
                           
                   ?></textarea><?php
               }
    }
   
}

class CTextBox extends CInput
{
    /**
     * Stworzenie textboksa (pole tekstowe do z możliwością wpisania tekstu)
     * @param string $text Wyświetlany tekst (opcjonalnie)
     * @param string $name Nazwa (name) (opcjonalnie)
     * @param string $id ID (opcjonalnie)
     * @param string $onkeyup Reakcja na zdarzenie onKeyUp (opcjonalnie)
     */
    public function CTextBox($text = null, $name = null, $id = null, $onkeyup = null)
    {
        parent::CInput(CInput::TEXT, $text, $name, $id);
        $this->SetOnKeyUp($onkeyup);
    }
}

class CPassword extends CInput
{
    public function CPassword($text = null, $name = null, $id = null, $onkeyup = null)
    {
        parent::CInput(CInput::PASSWORD, $text, $name, $id);
        $this->SetOnKeyUp($onkeyup);
    }
}

class CButton extends CInput
{
    public function CButton($value, $name = null, $id = null, $onclick = null)
    {
        $this->CInput(CInput::SUBMIT, null, $name, $id);
        $this->SetValue($value);
        $this->SetOnclick($onclick);
    }
}

class CTextArea extends CInput
{
    public function CTextArea($text = null, $name = null, $id = null)
    {
       parent::CInput(null, $text, $name, $id);
    }
}

class CComboBox extends CItem
{
    private $valItems, $valTxts, $sel = 0, $text, $selText;

    public function CComboBox($text, $name = null, $id = null)
    {
        $this->text = $text;
        parent::CItem($name, $id);
    }

    //wybrana bedzie taka opcja, ktora ma to samo w tekscie co przekazany selText
    public function Selected($selText)
    {
        $this->selText = $selText;
    }

    public function Draw()
    {
        ?><select <?php
            if ($this->name != null)
                $this->SetNode("name", $this->name);

            if ($this->id != null)
                $this->SetNode("id", $this->id);

            ?>><?php

            for ($i = 0; $i < count($this->valItems); ++$i)
            {
                ?><option <?php
                        if ($this->valItems[$i] != null)
                                $this->SetNode("value", $this->valItems[$i]);

                        if ($this->selText != null) //jesli zostal wybrany jakis tekst jako selected to ma wybierac selected po tekscie
                        {
                            if ($this->selText == $this->valTxts[$i])
                                    echo "SELECTED";
                        }
                        else if ($this->sel == $i) //a jesli nie to po wybranym w metodzie additem
                            echo "SELECTED";
                        ?>><?php echo $this->valTxts[$i] ?></option><?php
            }
            ?></select><?php
    }

    public function AddItem($text, $value = null, $selected = false)
    {
        $this->valItems[] = $value;
        $this->valTxts[] = $text;

        if ($selected)
            $this->sel = count($this->valItems) - 1;
    }

    public function GetText()
    {
        return $this->text;
    }
}

class CCheckBox extends CInput
{
	/**
	 * 
	 * Stworzenie ComboBoxa (true-false)
	 * @param string $text Tekst wyświetlany przy comboboksie (opcjonalnie)
	 * @param string $name Nazwa comboboksa (może być wspólna dla wielu (wtedy z []) (opcjonalnie)
	 * @param bool $selected Czy combo ma byc zaznaczone na poczatku (opcjonalnie)
	 * @param bool $disabled Czy combo ma byc wyszarzone na poczatku (opcjonalnie)
	 * @param string $value Nazwa comboboksa (w przypadku uzycia wielu tych samych nazw) (opcjonalnie)
	 * @param string $id Id comboboksa (opcjonalnie)
	 */
    public function CCheckBox($text = null, $name = null, $selected = false, $disabled = false, $value = null, $id = null)
    {
        parent::CInput(CInput::CHECKBOX, $text, $name, $id);
        if ($selected)
            $this->SetAddionalAttribs("CHECKED");

        if ($disabled)
            $this->SetAddionalAttribs("DISABLED");

        if ($value != null)
            $this->SetValue ($value);
    }
}

class CForm
{
    private $method, $arrayObjects, $action, $id, $brs = true;
    const POST = 1;
    const GET = 2;

    public function CForm($method, $action = null, $arrayObjects = null, $id = null)
    {
        $this->method = $method;
        $this->arrayObjects = $arrayObjects;
        $this->id = $id;
        $this->action = $action;
    }

    public function SetId($id)
    {
        $this->id = $id;
    }

    public function AddItem($item)
    {
        $this->arrayObjects[] = $item;
    }

    //czy tworzyc Bry po kazdym elemencie
    public function SetBrs($br)
    {
        $this->brs = $br;
    }

    //malowanie itemow, jesli ostatni element malujemy to nie robic brki
    private function DrawItem($item, $br = true)
    {
        
        if ($item instanceof CItem)
        {
            if (!($item instanceof CButton) && !($item instanceof CCheckBox))
            {
                ?><b><?php
                echo $item->GetText();
                ?></b><?php
            }

            $item->Draw();
            
            if ($item instanceof CCheckBox)
            {
                ?><b><?php
                echo $item->GetText();
                ?></b><?php
            }

            if ($br)
            {
                if ($this->brs)
                {
                    ?><br /><?php
                }
            }
        }
        else
            echo $item;

    }

    protected function SetNode($node, $value)
    {
        echo $node.'="'.$value.'" ';
    }

    public function Draw()
    {
        ?>
            <form <?php
            if ($this->method == CForm::POST)
                $this->SetNode("method", "POST");
            else if ($this->method == CForm::GET)
                $this->SetNode("method", "GET");

            if ($this->action)
                $this->SetNode("action", $this->action);
            if ($this->id != null)
                    $this->SetNode("id", $this->id);

            ?>><?php
            for ($i = 0; $i < count($this->arrayObjects); ++$i)
            {
                if ($i == count($this->arrayObjects) - 1)
                    $this->DrawItem($this->arrayObjects[$i], false);
                else
                    $this->DrawItem($this->arrayObjects[$i]);
            }
            ?></form><?php
    }
}
?>
