<?
/*
Plugin name: Timetable
Description: Show your party schedule through the intranet
*/
if (!defined("ADMIN_DIR")) exit();

function timetable_ucomp($a,$b)
{
  if ($a->date < $b->date) return -1;
  if ($a->date > $b->date) return  1;
  $s = strcasecmp($a->type,$b->type);
  if ($s) return $s;
  return strcasecmp($a->event,$b->event);
}

function timetable_content( $data )
{
  $content = &$data["content"];

  if (get_page_title() != "Timetable") return;
  $content = "";

  $d = 0;
  $lastdate = -1;
  $lasttime = -1;
  $rows = SQLLib::selectRows("select * from timetable order by `day`,`date`");

  $compos = SQLLib::selectRows("select * from compos order by start");
  foreach ($compos as $v)
  {
    $a = new stdClass();
    $a->type = "compo";
    $a->event = $v->name;
    $a->date = $v->start;
    $rows[] = $a;
  }
  usort($rows,"timetable_ucomp");

  $content .= sprintf("<h2>Timetable</h2>\n");

  $firstDay = 0;
  foreach($rows as $v) 
  {
    $day = date("l",strtotime(substr($v->date,0,10)));

    if ($day != $lastdate) 
    {
      if ($d++)
        $content .= sprintf("</table>\n\n");

      $content .= sprintf("<h3>%s</h3>\n",$day);
      $content .= sprintf("<table class=\"timetable\">\n");
      $content .= sprintf("<tr>\n");
      $content .= sprintf("  <th class='timetabletime'>Time</th>\n");
      $content .= sprintf("  <th class='timetableevent'>Event</th>\n");
      $content .= sprintf("</tr>\n");
      $lastdate = $day;
    }

    $content .= sprintf("<tr>\n");

    if ($lasttime == $v->date)
      $content .= sprintf("  <td>&nbsp;</td>\n");
    else
      $content .= sprintf("  <td>%s</td>\n",substr($v->date,11,5));

    $lasttime = $v->date;

    $text = $v->event;
    if ($v->link)
      $text = sprintf("<a href='%s'>%s</a>",build_url($v->link),$v->event);

    switch ($v->type) {
      case "mainevent": {
        $content .= sprintf("  <td><span class='timetable_eventtype_mainevent'>%s</span></td>\n",$text);
      } break;
      case "event": {
        $content .= sprintf("  <td><span class='timetable_eventtype_event'>%s</span></td>\n",$text);
      } break;
      case "deadline": {
        $content .= sprintf("  <td><span class='timetable_eventtype_deadline'>Deadline:</span> %s</td>\n",$text);
      } break;
      case "compo": {
        $content .= sprintf("  <td><span class='timetable_eventtype_compo'>Compo:</span> %s</td>\n",$text);
      } break;
      case "seminar": {
        $content .= sprintf("  <td><span class='timetable_eventtype_seminar'>Seminar:</span> %s</td>\n",$text);
      } break;
    }
    $content .= sprintf("</tr>\n");
  }
  $content .= sprintf("</table>\n");

}
add_hook("index_content","timetable_content");

function timetable_addmenu( $data )
{
  $data["links"]["pluginoptions.php?plugin=timetable"] = "Timetable";
}

add_hook("admin_menu","timetable_addmenu");

function timetable_activation()
{
  $r = SQLLib::selectRow("show tables where tables_in_".SQL_DATABASE."='timetable'");
  if (!$r)
  {
    SQLLib::Query(" CREATE TABLE `timetable` (".
      "   `id` int(11) NOT NULL auto_increment,".
      "   `day` smallint(6) NOT NULL,".
      "   `date` datetime NOT NULL default '00:00:00',".
      "   `type` enum('mainevent','event','deadline','compo','seminar') collate utf8_unicode_ci NOT NULL,".
      "   `event` text collate utf8_unicode_ci NOT NULL,".
      "   `link` text collate utf8_unicode_ci NOT NULL,".
      "   PRIMARY KEY  (`id`)".
      " ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;".
      " ");
  }
}

add_activation_hook( __FILE__, "timetable_activation" );

function timetable_toc( $data )
{
  $data["pages"]["Timetable"] = "Timetable";
}
add_hook("admin_toc_pages","timetable_toc");
?>