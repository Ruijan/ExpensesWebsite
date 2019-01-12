<?php

function createHead(){
  return "<head>
      <title>SignIn</title>
      <link rel=\"stylesheet\" href=\"style.css\"
    </head>";
}

function createHeader(){
  return "<div>
    <h1 class=\"titleFont\">Expenses website</h1>
    <h3>A penny saved is a penny earned.</br>
     - <em>Benjamin Franklin</em></h3>
  </div>";
}

function createButtons(){
  return "<div class=\"centeredButtons\">
        <button class=\"btn SignIn\" onclick=\"location.href='signIn.php'\" type=\"button\">Sign In</button>
        <button class=\"btn SignUp\" onclick=\"location.href='signUp.php'\" type=\"button\">Sign Up</button>
      </div>";
}

function createBody(){
  return "<body>".createHeader().createButtons()."</body>";
}

function createHtmlPage(){
  return "<html>".createHead().createBody()."</html>";
}

echo createHtmlPage();
