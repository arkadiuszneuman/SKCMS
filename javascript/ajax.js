sendGet("news", 0); //zaladowanie pierwszej strony na poczatku

function include(filename) //do includowania w javascripcie
{
    document.write('<' + 'script');
    document.write(' language="javascript"');
    document.write(' type="text/javascript"');
    document.write(' src="' + filename + '">');
    document.write('</' + 'script' + '>');
}

function getXMLHttpRequest() //przygotowanie ajaxa do roznych przegladarek
{
  var request = false;

  try
  {
    request = new XMLHttpRequest(); //ff opera chrome
  }
  catch(err1)
  {
    try
    {
      request = new ActiveXObject('Msxml2.XMLHTTP'); //ie 6
    }
    catch(err2)
    {
      try
      {
        request = new ActiveXObject('Microsoft.XMLHTTP'); //ie 5.5
      }
      catch(err3)
      {
        request = false;
      }
    }
  }
  return request;
}

function sendGet(what, page) //site - gdzie przekierowac, what - funkcja, ktora wywola ajax po podaniu danych
{
    var r = getXMLHttpRequest();  

    if (what == "news")
    {
        r.open('GET', './ajaxPHP/ajaxnews.php?page='+page, true);

        r.onreadystatechange = function()
        {
            if (r.readyState == (1 || 0))
            {
                document.getElementById('newses').innerHTML = "Ładowanie...";
            }
            else if (r.readyState == 4)
            {
                document.getElementById('newses').innerHTML = r.responseText;
            }
            else
            {
                document.getElementById('newses').innerHTML = "Błąd";
            }
        }
    }
    r.send(null);
}