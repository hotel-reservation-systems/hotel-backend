<?php
# Open the database connection
$db = new SQLite3($sqlite3_filename, SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
?>

  <section id="feed">
    <div class="container">
      <div class="row">
        <div class="col-12 col-lg-3">
        </div>
        <div class="col-12 col-lg-6">
          <h2>Our Feed 我们的动态</h2>
          <?php
          $statement = $db->prepare('SELECT * FROM "posts" ORDER BY "created" DESC');
          $result = $statement->execute();
          while ($post = $result->fetchArray(SQLITE3_ASSOC)) {
          ?>
          <table class="feed-table">
            <tr>
              <th rowspan="2" class="feed-avatar"><img src="<?php echo $site_url; ?>/assets/img/authuser_<?php echo $post["authuser"]; ?>.jpg" title="Avatar of <?php echo currentAuthuserName($post["authuser"]); ?>" alt="Avatar of <?php echo currentAuthuserName($post["authuser"]); ?>" class="img-thumbnail rounded" width="72"></th>
              <td><h3 class="feed-username" title="Post sent by <?php echo currentAuthuserName($post["authuser"]); ?>"><?php echo currentAuthuserName($post["authuser"]); ?></h3></td>
            </tr>
            <tr>
              <?php
              # Explicitly define UTC datetime
              $isoDate = date_create($post["created"], timezone_open('UTC'));
              $isoDateString = date_format($isoDate, "c");
              ?>
              <td title="Post Date" class="feed-date"><?php echo $isoDateString; ?></td>
            </tr>
            <tr>
              <th class="feed-content-left"></th>
              <td title="Post Content" class="feed-content"><?php echo $post["content"]; ?></td>
            </tr>
            <tr>
              <th colspan="2" class="feed-actions">[Like] [Comment] [Share] (尚未实现的功能🙈)</th>
            </tr>
          </table>
          <?php
          }
          // Free the memory, this is NOT done automatically, while the script is running
          $result->finalize();
          // Close the database.
          $db->close();
          ?>
        </div>
        <div class="col-12 col-lg-3">
        </div>
      </div>
    </div>
  </section>
