<!DOCTYPE html>
<html lang="en" dir="ltr">
  <?php $title = 'Quiz'; include './header.inc' ?>
  <body class="quiz-body">
    <?php $select = 3; include './menu.inc' ?>
    <div class="quiz-main-headding">
      <p>Quiz</p>
      <h1 class="quiz-title">Test your Apache knowledge</h1>
      <p>Can you complete these five questions?</p>
    </div>
    <div class="quiz-content">
      <form method="post" action="#results"> <!-- Disabled Validation -->
        <fieldset class="quiz-fieldset">
          <legend class="quiz-question">Details</legend>
          <div class="quiz-details-flex">
            <label class="quiz-details-input"><p>Apache ID</p><input type="text" name="ID" value="324234238" /></label>
            <label class="quiz-details-input"><p>Given Name</p><input type="text" name="given_name" value="BOB" /></label>
            <label class="quiz-details-input"><p>Family Name</p><input type="text" name="family_name" value="Test" /></label>
          </div>
        </fieldset>
        <fieldset class="quiz-fieldset">
          <legend class="quiz-question">Q1) Which is a competitor to Apache?</legend>
          <label><input type="radio" name="quiz-question-1" value="NGINX" checked />NGINX </label>
          <label><input type="radio" name="quiz-question-1" value="Microsoft_Edge" />Microsoft Edge</label>
          <label><input type="radio" name="quiz-question-1" value="MicroLP" />MicroLP</label>
          <label><input type="radio" name="quiz-question-1" value="BL2" />BL2</label>
        </fieldset>
        <fieldset class="quiz-fieldset">
          <legend class="quiz-question">Q2) Which operating systems does Apache run on?</legend>
          <label><input type="checkbox" name="quiz-question-2[]" value="Windows" checked />Windows</label>
          <label><input type="checkbox" name="quiz-question-2[]" value="Linux" checked />Linux</label>
          <label><input type="checkbox" name="quiz-question-2[]" value="MacOS" checked />MacOS</label>
          <label><input type="checkbox" name="quiz-question-2[]" value="Windows_95" />Windows 95</label>
        </fieldset>
        <fieldset class="quiz-fieldset">
          <legend class="quiz-question">Q3) Which variety of features dose Apache support?</legend>
          <label><input type="checkbox" name="quiz-question-3[]" value="Ipv6" checked />Ipv6</label>
          <label><input type="checkbox" name="quiz-question-3[]" value="SSL_Secure_Server" checked />SSL Secure Server</label>
          <label><input type="checkbox" name="quiz-question-3[]" value="Root_SSH_Access" checked />Root SSH Access</label>
          <label><input type="checkbox" name="quiz-question-3[]" value="Ipv23G" />Ipv23G</label>
          <label><input type="checkbox" name="quiz-question-3[]" value="YARN" />YARN</label>
          <label><input type="checkbox" name="quiz-question-3[]" value="Ada" />Ada</label>
        </fieldset>
        <fieldset class="quiz-fieldset">
          <legend class="quiz-question">Q4) What year was Apache's first version released?</legend>
          <label>
            Select Here
            <select name="quiz-question-4">
              <option value="">Please Select</option>
              <option value="1995" selected>1995</option>
              <option value="1994">1994</option>
              <option value="1992">1992</option>
              <option value="2001">2001</option>
            </select>
          </label>
        </fieldset>
        <fieldset class="quiz-fieldset">
          <legend class="quiz-question">Q5) In which year was the Apache HTTP Server became the most used web server?</legend>
          <label>
            Select here
            <select name="quiz-question-5">
              <option value="">Please Select </option>
              <option value="1994">1994</option>
              <option value="1995">1995</option>
              <option value="1996" selected >1996</option>
              <option value="1997">1997</option>
            </select>
          </label>
        </fieldset>
        <div class="quiz-button-container">
          <input class="quiz-button" type="submit" value="Submit" />
          <input class="quiz-button" type="reset" value="Reset" />
        </div>
      </form>
    </div>
    <?php require_once './markquiz.php' ?>
    <?php include './footer.inc'; ?>
  </body>
</html>
