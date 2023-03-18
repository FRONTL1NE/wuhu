<?php
if (!defined("ADMIN_DIR")) exit();

if (is_user_logged_in())
{
  redirect( build_url("News",array("login"=>"alreadyloggedin")) );
}

run_hook("login_start");

if ($_POST["login"])
{
  $_SESSION["logindata"] = NULL;

  $userID = SQLLib::selectRow(sprintf_esc("select id from users where `username`='%s' and `password`='%s'",$_POST["login"],hashPassword($_POST["password"])))->id;

  run_hook("login_authenticate",array("userID"=>&$userID));

  if ($userID)
  {
    $_SESSION["logindata"] = SQLLib::selectRow(sprintf_esc("select * from users where id=%d",$userID));
    header( "Location: ".build_url("News",array("login"=>"success")) );
  }
  else
  {
    header( "Location: ".build_url("Login",array("login"=>"failure")) );
  }
  exit();
}
if ($_GET["login"]=="failure")
  echo "<div class='error'>Login failed!</div>";
?>
<form action="<?=build_url("Login")?>" method="post" id='loginForm'>
<p>
  <label for="loginusername">ユーザー名 / Username:</label>
  <input id="loginusername" name="login" type="text" required='yes' />
</p>
<p>
  <label for="loginpassword">パスワード / Password:</label>
  <input id="loginpassword" name="password" type="password" required='yes' />
</p>
<p>
  <input type="submit" value="Go!" />
</p>
<p>
  <small>パスワードを忘れた場合、オーガナイザーにお問い合わせください / Ask organizers if you lost your password</small>
</p>
</form>
<hr />
<?php
run_hook("login_end");
?>
