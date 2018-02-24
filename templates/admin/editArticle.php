<?php include "templates/include/header.php" ?>
<?php include "templates/admin/include/header.php";
// Если отправленная форма не прошла проверку на сервере, то, чтобы не вводить 
// все заново оставляем уже введенные данные 
if($_POST){
    $articleId = $_POST['articleId'];
    $title = $_POST['title'];
    $summary = $_POST['summary'];
    $content = $_POST['content'];
    $categoryId = $_POST['categoryId'];
    $subcategoryId = $_POST['subcategoryId'];     
    if (isset($_POST['publicationDate'])) {
        $publicationDate = explode('-', $_POST['publicationDate']);
        if (count($publicationDate) == 3) {
            list ($y, $m, $d) = $publicationDate;
            $publicationDate = mktime (0, 0, 0, $m, $d, $y);
        }
    }
    if(isset($_POST['active'])) {
        $activeArticle = 1;
    } else {
        $activeArticle = 0;
    }
    $authors = $_POST['authors'];
} else {
    $articleId = $results['article']->id;
    $title = $results['article']->title;
    $summary = $results['article']->summary;
    $content = $results['article']->content;
    $categoryId = $results['article']->categoryId;
    $subcategoryId = $results['article']->subcategoryId;
    $publicationDate = $results['article']->publicationDate;
    $activeArticle = $results['article']->activeArticle;
    $authors = $results['article']->authors;
} var_dump($results['article']->authors);
?>

        <h1><?php echo $results['pageTitle']?></h1>

        <form action="admin.php?action=<?php 
                                   echo $results['formAction']?>" method="post">
            <input type="hidden" name="articleId" value="<?php 
                                               echo $articleId ?>">

    <?php if (isset($results['errorMessage'])) { ?>
            <div class="errorMessage"><?php 
                                          echo $results['errorMessage'] ?></div>
    <?php } ?>

            <ul>

              <li>
                <label for="title">Article Title</label>
                <input type="text" name="title" id="title" 
                       placeholder="Name of the article" 
                       required autofocus maxlength="255" value="<?php 
                       echo htmlspecialchars($title)?>" />
              </li>

              <li>
                <label for="summary">Article Summary</label>
                <textarea name="summary" id="summary" 
                          placeholder="Brief description of the article" 
                          required maxlength="1000" style="height: 5em;"><?php 
                          echo htmlspecialchars($summary)?>
                </textarea>
              </li>

              <li>
                <label for="content">Article Content</label>
                <textarea name="content" id="content" 
                          placeholder="The HTML content of the article" required 
                          maxlength="100000" style="height: 30em;"><?php 
                          echo htmlspecialchars($content)?>
                </textarea>
              </li>

              <li>
                <label for="categoryId">Article Category</label>
                <select name="categoryId">
<!-- Выводим значение категории, если категории нет - выводим категорию 777,
записанную как "нет категории"-->
                <?php foreach ($results['categories'] as $category) { 
                    if(!$results['article']->$categoryId){
                        $results['article']->$categoryId = 777;   
                    } ?>
                  <option value="<?php echo $category->id?>"<?php 
                    echo ($category->id == $categoryId) ? " selected" : ""?>>
                        <?php echo htmlspecialchars( $category->name )?>
                  </option>
                <?php } ?>
                </select>
              </li>
              
               <li>
                   
                <label for="subcategoryId">Article Subcategory</label>
                <select name="subcategoryId">

                <?php foreach ($results['subcategories'] as $subcategory ) { 
                    if(!$results['article']->subcategoryId){
                        $results['article']->subcategoryId = 777;
                    }?>
                  <option value="<?php echo $subcategory->id?>"<?php 
                    echo ($subcategory->id == $subcategoryId) 
                                                           ? " selected" : ""?>>
                        <?php echo htmlspecialchars($subcategory->name)?>
                  </option>
                <?php } ?>
                </select>
              </li>
              
              <li>  
                <label for="authors[]">Autors</label>
                <select name="authors[]" multiple>
                <?php               
                foreach ($results['authors'] as $author) {
                    if(!$authors[0]){
                        $authors[] = 777;
                    }?>
                  <option value="<?php echo $author->id?>"<?php 
                  foreach($authors as $oneAuthor) {
                      echo ($author->id == $oneAuthor) ? " selected" : "";
                  }?>>
                    
                        <?php 
                      echo htmlspecialchars($author->login )?>
                  </option>
                <?php } ?>
                </select>
              </li>

              <li>
                <label for="publicationDate">Publication Date</label>
                <input type="date" name="publicationDate" id="publicationDate" 
                       placeholder="YYYY-MM-DD" required maxlength="10" 
                       value="<?php echo $publicationDate 
                               ? date("Y-m-d", $publicationDate) 
                               : "" ?>" />
              </li>
              
              <li>
                  <label for="checkActivity">Active</label>
                  <input type="checkbox" name="active" value="1" 
                         id="checkboxActivity"
                  <?php
                        if($activeArticle == 1) {
                            echo 'checked = "checked"';
                        }
                  ?>
                  >
              </li>


            </ul>

            <div class="buttons">
              <input type="submit" name="saveChanges" value="Save Changes" />
              <input type="submit" formnovalidate name="cancel" value="Cancel" />
            </div>

        </form>

    <?php if ($results['article']->id ) { ?>
          <p><a href="admin.php?action=deleteArticle&amp;articleId=<?php 
            echo $results['article']->id ?>" 
            onclick="return confirm('Delete This Article?')">
                  Delete This Article
          </a></p>
    <?php } ?>
	  
<?php include "templates/include/footer.php" ?>

              