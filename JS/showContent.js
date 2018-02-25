$(function(){
    init_get();
    init_post();
});

function init_get() 
{
    $('a.showContentGETmethod').one('click', function(){
        var contentId = $(this).attr('data-contentId');
        console.log('ID статьи = ', contentId); 
        showLoaderIdentity(contentId);
        $.ajax({
            url:'/JS/showContentsHandler.php?articleId=' + contentId, 
            dataType: 'text'
        })
        .done (function(obj){
            hideLoaderIdentity(contentId);
            console.log('Ответ получен');
            $('li.' + contentId).append(obj);
        })
        .fail(function(){
            hideLoaderIdentity(contentId);
            console.log('Ошибка соединения с сервером');
        });
        
        return false;
        
    });  
}

function init_post() 
{
    $('a.showContentPOSTmethod').one('click', function(){
        var content = $(this).attr('data-contentId');
        showLoaderIdentity(content);
        $.ajax({
            url:'/JS/showContentsHandler.php', 
            data: ({articleId: content}),
            dataType: 'json',
//            converters: 'json text',
            method: 'POST'
        })
        .done (function(obj){
            hideLoaderIdentity(content);
            console.log('Ответ получен');
            $('li.' + content).append(obj);
        })
        .fail(function(){
            hideLoaderIdentity(content);
            console.log('Ошибка соединения с сервером');
        });
        
        return false;
        
    });  
}
