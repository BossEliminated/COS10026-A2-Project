<!DOCTYPE html>
<html lang="en" dir="ltr">
  <?php $title = 'Quiz'; include './header.inc' ?>
  <body>
    <?php $select = 3; include './menu.inc' ?>
    <h1>Quiz</h1>
    <hr>
    <p class="quiz-paragraph"><strong>Now lets take a Fun Quiz!</strong></p>
    <hr>
    <form method="post" action="http://mercury.swin.edu.au/it000000/formtest.php">
       <fieldset>
          <legend class="quiz-legend"><strong>Student Details</strong></legend>
             <label>Apache ID<input type="text" name="ID" pattern="[0-9]{9}" required="required" value="324234238"></label>
             <label>Given Name<input type="text" name="given_name" pattern="^[a-zA-Z]+$" maxlength="30" required="required" value="BOB"></label>
             <label>Family Name<input type="text" name="family_name" pattern="^[a-zA-Z]+$" maxlength="30" required="required" value="Test"></label>
       </fieldset>
       <fieldset>
          <legend class="quiz-question-1"><strong>Question 1</strong></legend>
          <p class="quiz-question-1"><strong>Q1)Whats main Apache attack tool, which exploits a bug in Apache software?</strong></p>
          <label><input type="radio" name="quiz-question-1" value="slowloris" checked>Slowloris</label>
          <label><input type="radio" name="quiz-question-1" value="phishing">Phishing</label>
          <label><input type="radio" name="quiz-question-1" value="mimt">MITM</label>
          <label><input type="radio" name="quiz-question-1" value="SQL">SQL Injection</label>
       </fieldset>
       <fieldset>
          <legend class="quiz-question"><strong>Question 2</strong></legend>
          <p class="quiz-question-2"><strong>Q2)Apache provides a variety of MultiProcessing Modules (MPMs),<br>which allow it to run on which of following modes?</strong></p>
          <label><input type="checkbox" name="quiz-question-2[]" value="process" checked>Process-based mode</label>
          <label><input type="checkbox" name="quiz-question-2[]" value="hybrid" checked>Hybrid (process and thread) mode</label>
          <label><input type="checkbox" name="quiz-question-2[]" value="event">Event-hybrid mode</label>
          <label><input type="checkbox" name="quiz-question-2[]" value="professional_wrong">Professional mode</label>
          <label><input type="checkbox" name="quiz-question-2[]" value="key_wrong">Keys Mode</label>
       </fieldset>
       <fieldset>
          <legend class="quiz-question"><strong>Question 3</strong></legend>
          <p class="quiz-question-3"><strong>Q3)Briefly describe, what are variety of features that Apache supports?</strong></p>
          <label>Answer<br><textarea name="quiz-question-3" rows="4" cols="40" placeholder="Type your answer here">Junk jsdkjsdk kjskldjvslkdjlkj jsdkl jsdlkj</textarea></label>
       </fieldset>
       <fieldset>
          <legend class="quiz-question"><strong>Question 4</strong></legend>
          <p class="quiz-question-4"><strong>Q4)In which year was the Apache HTTP Server codebase,<br>relicensed to the Apache 2.0 License?</strong></p>
          <label>
            Select Here
            <select name="quiz-question-4">
               <option value="">Please Select</option>
               <option value="2004_T" selected>2004</option>
               <option value="2010_F">2010</option>
               <option value="1998_F">1998</option>
               <option value="2015_F">2015</option>
            </select>
          </label>
       </fieldset>
       <fieldset>
          <legend class="quiz-question-5"><strong>Question 5</strong></legend>
          <p class="quiz-question-5"><strong>Q5)In which year was the Apache HTTP Server became the most used web server?</strong></p>
          <label>
            Select here
            <select name="year_used">
               <option value="">Please Select </option>
               <option value="1994_F" selected>1994</option>
               <option value="1995_F">1995</option>
               <option value="1996_T">1996</option>
               <option value="1997_F">1997</option>
            </select>
          </label>
       </fieldset>
       <input type="submit" value="Submit">
       <input type="reset" value="Reset">
    </form>
    <?php include './footer.inc'; ?>
  </body>
</html>
