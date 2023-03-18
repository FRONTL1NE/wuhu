<?php
if (!defined("ADMIN_DIR")) exit();

run_hook("register_start");

function validate() {
  if (strlen($_POST["username"])<3)
  {
    echo "<div class='error'>This username is too short, must be at least 4 characters!</div>";
    return 0;
  }
  if (strlen($_POST["password"])<4)
  {
    echo "<div class='error'>This password is too short, must be at least 4 characters!</div>";
    return 0;
  }
  if (!preg_match("/^[a-zA-Z0-9]{3,}$/",$_POST["username"]))
  {
    echo "<div class='error'>This username contains invalid characters!</div>";
    return 0;
  }
  /*
  if (!preg_match("/^[a-zA-Z0-9]{4,}$/",$_POST["password"]))
  {
    echo "<div class='error'>This password contains invalid characters!</div>";
    return 0;
  }
  */
  if (strcmp($_POST["password"],$_POST["password2"])!=0)
  {
    echo "<div class='error'>Passwords don't match!</div>";
    return 0;
  }

  $r = SQLLib::selectRows(sprintf_esc("select * from users where `username`='%s'",$_POST["username"]));
  if ($r)
  {
    echo "<div class='error'>This username is already taken!</div>";
    return 0;
  }

  $votekey = sanitize_votekey($_POST["votekey"]);
  $r = SQLLib::selectRow(sprintf_esc("select * from votekeys where `votekey`='%s'",$votekey));
  if (!$r)
  {
    echo "<div class='error'>This votekey is invalid!</div>";
    return 0;
  }
  if ($r->userid)
  {
    echo "<div class='error'>This votekey is already in use!</div>";
    return 0;
  }

  return 1;
}
$success = false;
if ($_POST["username"]) {
  if (validate())
  {
    $userdata = array(
      "username"=> ($_POST["username"]),
      "password"=> hashPassword($_POST["password"]),
      "nickname"=> ($_POST["nickname"] ? $_POST["nickname"] : $_POST["username"]),
      "group"=> ($_POST["group"]),
      "region"=> ($_POST["region"]),
      "age"=> ($_POST["age"]),
      "gender"=> ($_POST["gender"]),
      "occupation"=> ($_POST["occupation"]),
      "industry"=> ($_POST["industry"]),
      "venue"=> $_POST["venue"],
      "will-submit-demo"=> ($_POST["will-submit-demo"] == "on") ? 1 : 0,
      "will-submit-glsl"=> ($_POST["will-submit-glsl"] == "on") ? 1 : 0,
      "will-submit-wild"=> ($_POST["will-submit-wild"] == "on") ? 1 : 0,
      "will-submit-music"=> ($_POST["will-submit-music"] == "on") ? 1 : 0,
      "will-submit-gfx"=> ($_POST["will-submit-gfx"] == "on") ? 1 : 0,
      "hype"=> ($_POST["hype"]),
      "regip"=> ($_SERVER["REMOTE_ADDR"]),
      "regtime"=> (date("Y-m-d H:i:s")),
    );
    $error = "";
    run_hook("register_processdata",array("data"=>&$userdata));
    if (!$error)
    {
      $trans = new SQLTrans();
      $userID = SQLLib::InsertRow("users",$userdata);
      SQLLib::UpdateRow("votekeys",array("userid"=>$userID),sprintf_esc("`votekey`='%s'",sanitize_votekey($_POST["votekey"])));
      echo "<div class='success'>Registration successful!</div>";
      $success = true;
    }
    else
    {
      echo "<div class='failure'>"._html($error)."</div>";
    }
  }
}
if(!$success)
{
?>
<form action="<?=build_url("Login")?>" method="post" id='registerForm'>
<p>
  <label for="username">ユーザー名 / Username: <b style="color:red">*</b></label>
  <input id="username" name="username" type="text" value="<?=_html($_POST["username"])?>" required='yes'/>
</p>
<p>
  <label for="password">パスワード / Password: <b style="color:red">*</b></label>
  <input id="password" name="password" type="password" required='yes' />
</p>
<p>
  <label for="password2">パスワードを再入力 / Password again: <b style="color:red">*</b></label>
  <input id="password2" name="password2" type="password" required='yes' />
</p>
<p>
  <label for="votekey">投票キー / Votekey: <b style="color:red">*</b><br /><small>(<a href="https://discord.gg/Jzp84UjuDP" target="_blank" rel="noopener noreferrer">Discord</a>の #votekey-request チャンネルでリクエストしてください)</small></label>
  <input id="votekey" name="votekey" type="text" value="<?=_html($_POST["votekey"])?>" required='yes'/>
</p>
<p>
  <label for="nickname">ニックネーム / Nick/Handle: <b style="color:red">*</b></label>
  <input id="nickname" name="nickname" type="text" value="<?=_html($_POST["nickname"])?>" required='yes'/>
</p>
<p>
  <label for="group">グループ / Group: <small>(もしあれば / optional)</small></label>
  <input id="group" name="group" type="text" value="<?=_html($_POST["group"])?>"/>
</p>
<p>
  以下のアンケートは、統計的なデータ収集にのみ使われます。
  個人が特定できる形での利用・公開はいたしません。
  回答はすべて任意ですが、よろしければご協力をお願いします。
</p>
<p>
  The form below will be used only for statistical data collection.
  It will not be used or shown in a way that allows individuals to be identified.
  All responses are voluntary, but we would appreciate if you would like to participate.
</p>
<p>
  <label for="region">地域 / Region: <small>(optional, not visible)</small></label>
  <input id="region" name="region" type="text" value="<?=_html($_POST["region"])?>" placeholder="Japan, Germany, Portugal..."/>
</p>
<p>
  <label>年齢 / Age: <small>(optional, not visible)</small></label>
  <select id="age" name="age" value="<?=_html($_POST["age"])?>">
    <option value=""></option>
    <option value="0" <?=$_POST["age"] == '0' ? "selected" : ""?>>-17</option>
    <option value="18" <?=$_POST["age"] == '18' ? "selected" : ""?>>18-24</option>
    <option value="25" <?=$_POST["age"] == '25' ? "selected" : ""?>>25-29</option>
    <option value="30" <?=$_POST["age"] == '30' ? "selected" : ""?>>30-34</option>
    <option value="35" <?=$_POST["age"] == '35' ? "selected" : ""?>>35-39</option>
    <option value="40" <?=$_POST["age"] == '40' ? "selected" : ""?>>40-44</option>
    <option value="45" <?=$_POST["age"] == '45' ? "selected" : ""?>>45-49</option>
    <option value="50" <?=$_POST["age"] == '50' ? "selected" : ""?>>50-</option>
    <option value="no" <?=$_POST["age"] == 'no' ? "selected" : ""?>>Prefer not to answer</option>
  </select>
</p>
<p>
  <label for="gender">性別 / Gender: <small>(optional, not visible)</small></label>
  <input id="gender" name="gender" type="text" value="<?=_html($_POST["gender"])?>" placeholder="Male, Female, Non-binary, ..."/>
</p>
<p>
  <label for="occupation">職業 / Occupation: <small>(optional, not visible)</small></label>
  <input id="occupation" name="occupation" type="text" value="<?=_html($_POST["occupation"])?>" placeholder="Student, Engineer, Product Manager..."/>
</p>
<p>
  <label for="industry">業種または専攻 / Industry or Major: <small>(optional, not visible)</small></label>
  <input id="industry" name="industry" type="text" value="<?=_html($_POST["industry"])?>" placeholder="Games, Web Services, Physics, HCI..."/>
</p>
<p>
  <label for="venue">会場に来る予定ですか？ / Are you gonna be at the venue?: <small>(optional, not visible)</small></label>
</p>
<ul>
  <li>
    <input id="venue-yes" name="venue" type="radio" value="yes" <?=$_POST["venue"] == 'yes' ? "checked" : ""?> />
    <label for="venue-yes" style="display:inline">Yes, I will be at the venue!</label>
  </li>
  <li>
    <input id="venue-no" name="venue" type="radio" value="no" <?=$_POST["venue"] == 'no' ? "checked" : ""?> />
    <label for="venue-no" style="display:inline">No, join remotely</label>
  </li>
</ul>
<p>
  <label>投稿予定のコンポ / I will submit for: <small>(optional, not visible)</small></label>
</p>
<ul>
  <li>
    <input id="will-submit-demo" name="will-submit-demo" type="checkbox" <?=$_POST["will-submit-demo"] == 'on' ? "checked" : ""?> />
    <label for="will-submit-demo" style="display:inline">Combined PC Demo Compo</label>
  </li>
  <li>
    <input id="will-submit-glsl" name="will-submit-glsl" type="checkbox" <?=$_POST["will-submit-glsl"] == 'on' ? "checked" : ""?> />
    <label for="will-submit-glsl" style="display:inline">GLSL Graphics Compo</label>
  </li>
  <li>
    <input id="will-submit-wild" name="will-submit-wild" type="checkbox" <?=$_POST["will-submit-wild"] == 'on' ? "checked" : ""?> />
    <label for="will-submit-wild" style="display:inline">Wild Compo</label>
  </li>
  <li>
    <input id="will-submit-music" name="will-submit-music" type="checkbox" <?=$_POST["will-submit-music"] == 'on' ? "checked" : ""?> />
    <label for="will-submit-music" style="display:inline">Combined Music Compo</label>
  </li>
  <li>
    <input id="will-submit-gfx" name="will-submit-gfx" type="checkbox" <?=$_POST["will-submit-gfx"] == 'on' ? "checked" : ""?> />
    <label for="will-submit-gfx" style="display:inline">Combined Graphics Compo</label>
  </li>
</ul>
<p>
  <label for="hype">SESSIONSで一番楽しみなこと / What is the event you are most looking forward to in SESSIONS?: <small>(optional, not visible)</small></label>
  <input id="hype" name="hype" type="text" value="<?=_html($_POST["hype"])?>" />
</p>
<?php
run_hook("register_endform");
?>
<p id='regsubmit'>
  <input type="submit" value="Go!" />
</p>
</form>
<?php
}

run_hook("register_end");
?>
