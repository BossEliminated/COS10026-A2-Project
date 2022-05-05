<header class="nav-bar">
  <a href="./"><img src="img/Apache_HTTP_server_logo_(2019-present).svg.png" alt="apache logo"></a>
  <div><a <?php if($select == 1){print('id="nav-bar-active"');};?> href="./">Home</a></div>
  <div>
    <a <?php if($select == 2){print('id="nav-bar-active"');};?> class="show-tooltip" href="topic">Topic</a>
    <p class="tooltip">Information about Apache Web Server</p>
  </div>
  <div>
    <a <?php if($select == 3){print('id="nav-bar-active"');};?> class="show-tooltip" href="quiz">Quiz</a>
    <p class="tooltip">Questions for you to answer</p>
  </div>
  <div>
    <a <?php if($select == 4){print('id="nav-bar-active"');};?> class="show-tooltip" href="enhancements">Enhancements</a>
    <p class="tooltip">Special changes to the website</p>
  </div>
</header>
