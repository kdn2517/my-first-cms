$(document).ready(function ()
{
    $("#loadArticle").bind("click", function () {
        var articleId = $(this).attr('data-contentId');
        $.ajax({

            url: "ajax/loadArticle.php",
            type: "POST",
            data: ({articleId: articleId}),
            dataType: "html",
            beforeSend: function ()
            {
                var id = "#article" + articleId;
                $(id).text("Загрузка данных...");
            },
            success: function (data)
            {
                var id = "#article" + articleId;
                $(id).text(data);
            },
            error: function funcError()
            {
                var id = "#article" + articleId;
                $(id).text("Ошибка!!!");
            }
        });
    });
});


