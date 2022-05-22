<header class="nav-bar">
  <a href="./"><img src="img/Apache_HTTP_server_logo_(2019-present).svg.png" alt="apache logo"></a>
  <div><a <?php if($select == 1){print('id="nav-bar-active"');};?> href="./">Home</a></div>
  <div>
    <a <?php if($select == 2){print('id="nav-bar-active"');};?> class="show-tooltip" href="topic.php">Topic</a>
    <p class="tooltip">Information about Apache Web Server</p>
  </div>
  <div>
    <a <?php if($select == 3){print('id="nav-bar-active"');};?> class="show-tooltip" href="quiz.php">Quiz</a>
    <p class="tooltip">Questions for you to answer</p>
  </div>
  <div>
    <a <?php if($select == 4){print('id="nav-bar-active"');};?> class="show-tooltip" href="enhancements.php">Enhancements</a>
    <p class="tooltip">Special changes to the website</p>
  </div>
  <div>
    <a <?php if($select == 5){print('id="nav-bar-active"');};?> class="show-tooltip" href="phpenhancements.php">PHP Enhancements</a>
    <p class="tooltip">Special PHP features on the website</p>
  </div>
  <div>
    <a <?php if($select == 6){print('id="nav-bar-active"');};?> class="show-tooltip" href="manage.php">Manage Page</a>
    <p class="tooltip">Supervisor view</p>
  </div>
</header>
