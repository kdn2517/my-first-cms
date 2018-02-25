// выводим идентификатор
   
function showLoaderIdentity(content) 
    {
        var id = "#loader-identity" + content;
        $(id).show("slow");
    }

    // скрываем идентификатор
    function hideLoaderIdentity(content) 
    {
       var id = "#loader-identity" + content;
       $(id).hide();  
    }


