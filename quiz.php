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
          <p>
             <label for="ID">Apache ID</label>
             <input type="text" name="ID" id="ID" pattern="[0-9]{9}" required="required">
          </p>
          <p>
             <label for="ID">Given Name</label>
             <input type="text" name="g_name" id="g_name" pattern="^[a-zA-Z]+$" maxlength="30" required="required">
             <label for="ID">Family Name</label>
             <input type="text" name="family_name" id="family_name" pattern="^[a-zA-Z]+$" maxlength="30" required="required">
          </p>
       </fieldset>
       <fieldset>
          <legend class="quiz-question"><strong>Question 1</strong></legend>
          <p class="quiz-question-1"><strong>Q1)Whats main Apache attack tool, which exploits a bug in Apache software?</strong></p>
          <input type="radio" id="slowloris" name="opt" value="slowloris">
          <label for="slowloris">Slowloris</label>
          <input type="radio" id="phishing" name="opt" value="phishing">
          <label for="phishing">Phishing</label>
          <input type="radio" id="mimt" name="opt" value="mimt">
          <label for="mimt">MITM</label>
          <input type="radio" id="SQL" name="opt" value="SQL">
          <label for="SQL">SQL Injection</label>
       </fieldset>
       <fieldset>
          <legend class="quiz-question"><strong>Question 2</strong></legend>
          <p class="quiz-question-2"><strong>Q2)Apache provides a variety of MultiProcessing Modules (MPMs),<br>which allow it to run on which of following modes?</strong></p>
          <input type="checkbox" id="C1" name="C1" value="process">
          <label for="C1">Process-based mode</label>
          <input type="checkbox" id="C2" name="C2" value="hybrid">
          <label for="C2">Hybrid (process and thread) mode</label>
          <input type="checkbox" id="C3" name="C3" value="event">
          <label for="C3">Event-hybrid mode</label>
          <input type="checkbox" id="C4" name="C4" value="professional_wrong">
          <label for="C4">Professional mode</label>
          <input type="checkbox" id="C5" name="C5" value="key_wrong">
          <label for="C5">Keys Mode</label>
       </fieldset>
       <fieldset>
          <legend class="quiz-question"><strong>Question 3</strong></legend>
          <p class="quiz-question-3"><strong>Q3)Briefly describe, what are variety of features that Apache supports?</strong></p>
          <label for="answer3">Answer</label><br>
          <textarea id="answer3" name="answer3" rows="4" cols="40" placeholder= "Type your answer here"></textarea>
       </fieldset>
       <fieldset>
          <legend class="quiz-question"><strong>Question 4</strong></legend>
          <p class="quiz-question-4"><strong>Q4)In which year was the Apache HTTP Server codebase,<br>relicensed to the Apache 2.0 License?</strong></p>
          <label for="year">Select Here</label>
          <select name="year" id="year">
             <option value="">Please Select</option>
             <option value="2004_T">2004</option>
             <option value="2010_F">2010</option>
             <option value="1998_F">1998</option>
             <option value="2015_F">2015</option>
          </select>
       </fieldset>
       <fieldset>
          <legend class="quiz-question" ><strong>Question 5</strong></legend>
          <p class="quiz-question-5"><strong>Q5)In which year was the Apache HTTP Server became the most used web server?</strong></p>
          <label for="year_used">Select here</label>
          <select name="year_used" id="year_used">
             <option value="">Please Select </option>
             <option value="1994_F">1994</option>
             <option value="1995_F">1995</option>
             <option value="1996_T">1996</option>
             <option value="1997_F">1997</option>
          </select>
       </fieldset>
       <input type="submit" value="Submit">
       <input type="reset" value="Reset">
    </form>
    <footer>
       <a href="mailto:103995646@student.swin.edu.au">By: A Patchy Team</a>
    </footer>
  </body>
</html>
