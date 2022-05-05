<!DOCTYPE html>
<html lang="en" dir="ltr">
  <?php $title = 'Enhancements'; include './header.inc' ?>
  <body class="enhancements_body">
    <?php $select = 4; include './menu.inc' ?>
    <h1 class="enhancements_header">Enhancements</h1>
    <h2 class="enhancements_header">Extra Improvements to the Website</h2>
    <div id="segment_enhancements_starter">
       <h2 class="header_offset">Going Beyond the Basic Standards of the Project</h2>
       <section class="beyondsection">
          <h3>ToolTips</h3>
          <p class="paragraph_mover">Utilisation of tool tips to provide further information on the website to those utilising it. On the navigation bar, tooltips appear on all pages.</p>
          <a class="hyperlink_highlighter" href="topic.html#HTTP_DEF">Topic Page</a><a class="hyperlink_highlighter" href="quiz.html">Quizs Page</a><a class="hyperlink_highlighter" href="index.html">Index Page</a>
       </section>
    </div>
    <div class="segment_enhancements">
       <h2 class="header_offset">Code Implemented</h2>
       <section class="beyondsection">
          <h3>Tooltips</h3>
          <p>The background of text and text formatting</p>
          <code class="declinegrowthtable">
            <span>.tooltip { font-size: 18px; display: none; position: absolute; text-align: center; width: 10em; background-color: #1f1f1fe3; border-radius: 10px; color: white; padding: 2px 4px; transform: translateY(22px); } }</span>
          </code>
          <p>Making tooltip appear, the paragraph after class hover objects, appear. Paragraph element holds the tooltip class which is hidden by default.</p>
          <code class="declinegrowthtable">.show-tooltip:hover + p {
          display: block;
          }
          </code>
       </section>
       <br>
       <section class="beyondsection">
          <h3>Mobile Responsive Design</h3>
          <p>Using media queries and flex box at certain viewport widths, class styling is overridden with new properties or values to reposition or change divs to make the site content a reasonable size on mobile devices. For example here is the navbar media query:</p>
          <code class="declinegrowthtable enhancements-code">@media only screen and (max-width: 704px) { .nav-bar { justify-content: center; }}</code><br>
          <code class="declinegrowthtable enhancements-code">@media only screen and (max-width: 430px) { .nav-bar { flex-direction: column;</code><br>
          <code class="declinegrowthtable enhancements-code">align-items: center;padding-bottom: 10px;gap: 0px; }}</code><br>
       </section>
    </div>
    <div class="segment_enhancements">
       <h2 class="header_offset">Third Party References</h2>
       <section class="beyondsection">
          <h3>ToolTips</h3>
          <a class="hyperlink_highlighter" href="https://www.w3schools.com/css/css_tooltip.asp">https://www.w3schools.com
          /css/css_tooltip.asp</a>
       </section>
    </div>
    <footer class="footersetter">
       <a href="mailto:103995646@student.swin.edu.au">By: A Patchy Team</a>
    </footer>
  </body>
</html>
