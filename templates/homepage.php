<?php include "templates/include/header.php" ?>
    <ul id="headlines">
    <?php foreach ($results['articles'] as $article) { ?>
        <li class='<?php echo $article->id?>'>
            <h2>
                <span class="pubDate">
                    <?php echo date('j F', $article->publicationDate)?>
                </span>
                
                <a href=".?action=viewArticle&amp;articleId=<?php 
                                                           echo $article->id?>">
                    <?php echo htmlspecialchars($article->title)?>
                </a>
                
                <?php if (isset($article->categoryId)) { ?>
                    <span class="category">
                        Категория 
                        <a href=".?action=archive&amp;categoryId=<?php 
                                                   echo $article->categoryId?>">
                            <?php echo htmlspecialchars($results['categories']
                                                 [$article->categoryId]->name)?>
                        </a>
                    </span>
                <?php } 
                else { ?>
                    <span class="category">
                        <?php echo "Без категории"?>
                    </span>
                <?php } ?>
                <?php if (isset($article->categoryId)) { ?>
                <span class="category">
                    Подкатегория 
                    <a href=".?action=viewArticleSubcategory&amp;subcategoryId=<?php 
                                                echo $article->subcategoryId?>">
                        <?php echo htmlspecialchars($results['subcategories']
                                              [$article->subcategoryId]->name)?>
                    </a>
                </span>
                <?php } ?>
                
                <?php if (isset($article->authors)) { ?>
                <span class="category">
                    Авторы: 
                    <?php 
                    foreach($article->authors as $user) { ?>
                    <a href=".?action=viewArticleAuthor&amp;authorId=<?php
                                                            echo $user?>">
                    <?php            
                        echo $results['authors'][$user]->login . ", ";
                    ?>
                    </a><?php } ?>
                </span>
                <?php } ?>
            </h2>
            <p class="summary"><?php 
                            echo htmlspecialchars($article->content50char)?></p>
            <img id="loader-identity" src="JS/ajax-loader.gif" alt="gif">
            <a href=".?action=viewArticle&amp;articleId=<?php 
                echo $article->id?>" class="showContentPOSTmethod" 
                data-contentId="<?php 
                                echo $article->id?>">Запросить методом POST</a>
            <a href=".?action=viewArticle&amp;articleId=<?php 
                echo $article->id?>" class="showContent" 
                data-contentId="<?php echo $article->id?>">Показать полностью</a>
        </li>
    <?php } ?>
    </ul>
    <p><a href="./?action=archive">Article Archive</a></p>
<?php include "templates/include/footer.php" ?>

    
