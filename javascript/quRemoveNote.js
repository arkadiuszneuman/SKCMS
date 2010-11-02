function quRemoveNote(note, id) //funkcja upewnia sie, ze chcemy usunac newsa i przekierowywuje do odpowiedniej strony w razie odpowiedzi twierdzacej
{
    if (confirm("Czy chcesz usunąć newsa: "+note+"?"))
    {
        window.location.href = "./panel.php?task=removeNote&id="+id;
    }
}