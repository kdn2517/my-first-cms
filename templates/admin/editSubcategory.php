<?php include "templates/include/header.php" ?>
<?php include "templates/admin/include/header.php" ?>

        <h1><?php echo $results['pageTitle']?></h1>

        <form action="admin.php?action=<?php echo $results['formAction']?>" 
              method="post"> 
<!-- Обработка формы будет направлена файлу admin.php ф-ции newSubcategory 
   либо editSubcategory в зависимости от formAction, сохранённого в result-е -->
        <input type="hidden" name="subcategoryId" 
               value="<?php echo $results['subcategory']->id ?>"/>

    <?php if ( isset( $results['errorMessage'] ) ) { ?>
            <div class="errorMessage"><?php echo $results['errorMessage'] ?></div>
    <?php } ?>

        <ul>

          <li>
            <label for="name">Subcategory Name</label>
            <input type="text" name="name" id="name" 
                   placeholder="Name of the subcategory" required 
                   autofocus maxlength="255" value="<?php 
                   echo htmlspecialchars( $results['subcategory']->name )?>" />
          </li>

          <li>
            <label for="description">Description</label>
            <textarea name="description" id="description" 
                      placeholder="Brief description of the subcategory" required 
                      maxlength="1000" style="height: 5em;"><?php 
                      echo htmlspecialchars( $results['subcategory']->description )?>
            </textarea>
          </li>
          <li>
            <label for="category">Category</label>
            <select name="category">
              <?php foreach ($results['categories'] as $category) { ?>
                <option value="<?php echo $category->id?>"<?php 
                echo ($category->id == $results['subcategory']->category) 
                                                           ? " selected" : ""?>>
                    <?php echo htmlspecialchars( $category->name )?>
                </option>
              <?php } ?>
            </select>
          </li>

        </ul>

        <div class="buttons">
          <input type="submit" name="saveChanges" value="Save Changes" />
          <input type="submit" formnovalidate name="cancel" value="Cancel" />
        </div>

      </form>

    <?php if ( $results['subcategory']->id ) { ?>
          <p><a href="admin.php?action=deleteSubcategory&amp;subcategoryId=<?php 
            echo $results['subcategory']->id ?>" 
            onclick="return confirm('Delete This Subcategory?')">
                  Delete This Subcategory
          </a></p>
    <?php } ?>

<?php include "templates/include/footer.php" ?>

