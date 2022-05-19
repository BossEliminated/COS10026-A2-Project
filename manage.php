<!DOCTYPE html>
<html lang="en" dir="ltr">
  <?php $title = 'Supervisor Management Quiz View'; include_once './header.inc' ?>
  <body>
	<?php session_start();?>
    <?php $select = 6; include_once './menu.inc' ?>
    <div class="manage-flex-content">
      <div class="manage-menu">
        <form method="post" action="manage.php">
          <!-- <button class="manage-hide" type="submit" name="action" value="5"></button> -->
          <button type="submit" name="action" value="1">List All attempts</button>
          <button type="submit" name="action" value="2">List half attempts</button>
          <button type="submit" name="action" value="3">Delete attempts</button>
          <button type="submit" name="action" value="4">Manage Score</button>
          <hr class="manage-menu-hr" />
          <div class="manage-id-field">
            <!-- Do not print these if logged in -->
            <label for="student_id">
              <input name="username" type="text" placeholder="Username" />
            </label>
            <label for="student_name">
              <input name="password" type="text" placeholder="Password" />
            </label>
            <!-- <p class="">If loged in Put User Name here</p> -->
          </div>
          <button type="submit" name="action" value="5">Login/Sign Up</button>
          <!-- Show logout button if loged in -->
          <!-- <button type="submit" name="action" value="6">Logout</button> -->
        </form>
        <div style="height: 500px;"></div>
      </div>
      <div class="manage-content">
        <h1>Management Page</h1>
        <section>
          <form method="post" action="manage.php">
            <label for="student_id">Student ID </label>
            <input name="student_id" id="student_id" type="text" placeholder="Student ID" />
            <label for="student_name">Student Name </label>
            <input name="student_name" id="student_name" type="text" placeholder="Name" />
            <br />
            <input type="radio" name="mark_filter" id="no_filter" value="0" />
            <label for="no_filter">No Filtering</label>
            <input type="radio" name="mark_filter" id="mark_filtering_hundred" value="1" />
            <label for="mark_filtering_hundred">Scored 100% on first Attempt</label>
            <input type="radio" name="mark_filter" id="mark_filtering_less_than" value="2"/>
            <label for="mark_filtering_less_than">Scored 50% on second Attempt </label>
            <br />
            <input type="submit" name="filter_all" value="Submit" />
          </form>
          <hr />
        </section>
        <section>
          <table>
            <?php
			$main_page = true;
			include_once './manage_processing.php'; ?>
          </table>
        </section>
      </div>
    </div>
    <?php include './footer.inc'; ?>
  </body>
</html>
