<?php

class IndexPage{
  private $content;

  public function display(){
    echo $this->content;
  }

  public function __construct(){
    $this->content = "<html>".$this->createHead().$this->createBody()."</html>";
  }

  private function createHead(){
    return "<head>
        <title>SignIn</title>
        <link rel=\"stylesheet\" href=\"style.css\"
      </head>";
  }

  private function createBody(){
    return "<body>".$this->createHeader().$this->createButtons()."</body>";
  }

  private function createHeader(){
    return "<div>
      <h1 class=\"titleFont\">Expenses website</h1>
      <h3>A penny saved is a penny earned.</br>
       - <em>Benjamin Franklin</em></h3>
    </div>";
  }

  private function createButtons(){
    return "<div class=\"centeredButtons\">
          <button class=\"btn SignIn\" onclick=\"location.href='signIn.php'\" type=\"button\">Sign In</button>
          <button class=\"btn SignUp\" onclick=\"location.href='signUp.php'\" type=\"button\">Sign Up</button>
        </div>";
  }
}

$page = new IndexPage;
$page->display();
