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
      <form method="post" action="#results">
         <fieldset class="quiz-fieldset">
            <legend class="quiz-question">Student Details</legend>
               <label>Apache ID<input type="text" name="ID" pattern="[0-9]{9}" required="required" value="324234238"></label>
               <label>Given Name<input type="text" name="given_name" pattern="^(([a-zA-Z]|-| ){1,30})$" maxlength="30" required="required" value="BOB"></label>
               <label>Family Name<input type="text" name="family_name" pattern="^(([a-zA-Z]|-| ){1,30})$" maxlength="30" required="required" value="Test"></label>
         </fieldset>
         <fieldset class="quiz-fieldset">
            <legend class="quiz-question">Q1) Whats main Apache attack tool, which exploits a bug in Apache software?</legend>
            <label><input type="radio" name="quiz-question-1" value="slowloris" checked>Slowloris</label>
            <label><input type="radio" name="quiz-question-1" value="phishing">Phishing</label>
            <label><input type="radio" name="quiz-question-1" value="mimt">MITM</label>
            <label><input type="radio" name="quiz-question-1" value="SQL">SQL Injection</label>
         </fieldset>
         <fieldset class="quiz-fieldset">
            <legend class="quiz-question">Q2) Apache provides a variety of MultiProcessing Modules (MPMs), which allow it to run on which of following modes?</legend>
            <label><input type="checkbox" name="quiz-question-2[]" value="process-based_mode" checked>Process-based mode</label>
            <label><input type="checkbox" name="quiz-question-2[]" value="hybrid_mode" checked>Hybrid (process and thread) mode</label>
            <label><input type="checkbox" name="quiz-question-2[]" value="event-hybrid_mode">Event-hybrid mode</label>
            <label><input type="checkbox" name="quiz-question-2[]" value="professional_mode">Professional mode</label>
            <label><input type="checkbox" name="quiz-question-2[]" value="key_mode">Keys Mode</label>
         </fieldset>
         <fieldset class="quiz-fieldset">
            <legend class="quiz-question">Q3) Which variety of features that Apache supports?</legend>
            <label><input type="checkbox" name="quiz-question-3[]" value="bob" checked>Bob</label>
            <label><input type="checkbox" name="quiz-question-3[]" value="dave">Dave</label>
            <label><input type="checkbox" name="quiz-question-3[]" value="tree">Tree</label>
            <label><input type="checkbox" name="quiz-question-3[]" value="sky" checked>Sky</label>
            <label><input type="checkbox" name="quiz-question-3[]" value="games">Games</label>
         </fieldset>
         <fieldset class="quiz-fieldset">
            <legend class="quiz-question">Q4) In which year was the Apache HTTP Server codebase, relicensed to the Apache 2.0 License?</legend>
            <label>
              Select Here
              <select name="quiz-question-4">
                 <option value="">Please Select</option>
                 <option value="2004" selected>2004</option>
                 <option value="2010">2010</option>
                 <option value="1998">1998</option>
                 <option value="2015">2015</option>
              </select>
            </label>
         </fieldset>
         <fieldset class="quiz-fieldset">
            <legend class="quiz-question">Q5) In which year was the Apache HTTP Server became the most used web server?</legend>
            <label>
              Select here
              <select name="quiz-question-5">
                 <option value="">Please Select </option>
                 <option value="1994" selected>1994</option>
                 <option value="1995">1995</option>
                 <option value="1996">1996</option>
                 <option value="1997">1997</option>
              </select>
            </label>
         </fieldset>
         <input class="quiz-button" type="submit" value="Submit">
         <input class="quiz-button" type="reset" value="Reset">
      </form>
    </div>
    <div id="results" class="quiz-content quiz-results">
      <?php require_once 'markquiz.php' ?>
    </div>
    <?php include './footer.inc'; ?>
  </body>
</html>
