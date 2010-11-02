function checkPass() //funkcja sprawdza czy 2 hasla sa takie same
{
    var form = document.getElementById("regForm");
    var password1 = form.pass1;
    var password2 = form.pass2;

    if (password1.value != password2.value || password1.value.length < 3)
    {
        password1.style.border = "2px solid red";
        password2.style.border = "2px solid red";
    }
    else
    {
        password1.style.border = "2px solid lightgreen";
        password2.style.border = "2px solid lightgreen";
    }
}

