<?php 

require ('../config.php');
$article = Article::getById($_POST['articleId']);
echo $article->content;


