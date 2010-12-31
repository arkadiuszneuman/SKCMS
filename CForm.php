<?php

abstract class CItem
{
    protected $id, $value, $class, $onclick, $addional;

    protected function SetNode($node, $value)
    {
        echo $node.'="'.$value.'" ';
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
}

abstract class CInput extends CItem
{
    private $type, $name, $text;

    const TEXT = 1;
    const PASSWORD = 2;
    const SUBMIT = 4;

    public function CInput($type, $text = null, $name = null, $id = null)
    {
        $this->type = $type;
        $this->name = $name;
        $this->text = $text;
        $this->id = $id;
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
    public function CTextBox($text = null, $name = null, $id = null)
    {
        $this->CInput(CInput::TEXT, $text, $name, $id);
    }
}

class CPassword extends CInput
{
    public function CPassword($text = null, $name = null, $id = null)
    {
        $this->CInput(CInput::PASSWORD, $text, $name, $id);
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
        $this->CInput(null, $text, $name, $id);
    }
}

class CForm
{
    private $method, $arrayObjects, $action, $id;
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

    private function DrawItem($item)
    {
        
        if ($item instanceof CItem)
        {
            ?><b><?php
            echo $item->GetText();
            ?></b><?php
            $item->Draw();
            ?><br /><?php
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

            $this->SetNode("action", $this->action);
            if ($this->id != null)
                    $this->SetNode("id", $this->id);

            ?>><?php
            foreach($this->arrayObjects as $o)
            {
                $this->DrawItem($o);
            }
            ?></form><?php
    }
}
?>
