<?php include "templates/include/header.php" ;?>
	  
    <h1><?php echo htmlspecialchars($results['pageHeading'])?></h1>
  
    <h3 class="subcategoryDescription">Статьи автора "<?php 
                       echo htmlspecialchars($results['author']->login) ?>"</h3>
    
    <ul id="headlines" class="archive">

    <?php foreach ($results['articles'] as $article) { ?>

            <li>
                <h2>
                    <span class="pubDate">
                        <?php echo date('j F Y', $article->publicationDate)?>
                    </span>
                    <a href=".?action=viewArticle&amp;articleId=<?php 
                                                           echo $article->id?>">
                        <?php echo htmlspecialchars($article->title)?>
                    </a>
                            
                </h2>
              <p class="summary"><?php 
                                  echo htmlspecialchars($article->summary)?></p>
            </li>

    <?php } ?>

    </ul>

    <p><?php echo $results['totalRows']?> article<?php 
                   echo ($results['totalRows'] != 1) ? 's' : '' ?> in total.</p>

    <p><a href="./">Return to Homepage</a></p>
	  
<?php include "templates/include/footer.php" ?>