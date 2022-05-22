<!DOCTYPE html>
<html lang="en" dir="ltr">
  <?php $title = 'PHP Enhancements'; include './header.inc' ?>
  <body>
    <?php $select = 5; include './menu.inc' ?>
  <body class="php-enh-body">
    <div class="php-enh-main">
      <div class="php-enh-headding">
        <h1>Checkout our web page feature extinction enhancements.</h1>
      </div>
      <div class="php-enh-content-flex">
        <div class="php-enh-content">
          <h2>Normalise database data structure</h2>
          <p>The data submitted for the quiz forum is grouped into two tables to improve data integrity and maintainability. The first group is the user details, the second is the score and attempts. Both groups use a primary-foreign key called unique_id which links the tables together.</p>
        </div>
        <!-- <hr class="php-enh-hr">
        <hr class="php-enh-hr"> -->
        <div class="php-enh-content">
          <h2>Secure database access</h2>
          <p>To prevent unauthorised parties from accessing our stores database of users and scores we protect it with an intuitive and secure login system. Only authorised users are able to access the supervisor web page.</p>
        </div>
      </div>
    </div>
    <?php include './footer.inc'; ?>
  </body>
</html>
