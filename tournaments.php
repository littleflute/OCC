<?php 
// http://sergeiki.blogspot.com
// Sergei Ki {
include 'verifysession.php';
include 'render.php';
include 'io.php';

//error_reporting(E_ALL);
//ini_set('display_errors', 1);
// also in .htaccess I have been added during debugging php_value display_errors on

abstract class Tournament
{
  // Create Tournament Games
  public static function _createTGs($tid) {

    global $res_tournaments,$uid;
    
    $uid=="NozhVspinU" or die("<b>You don't have rights to create tournaments. Admins only.</b>");

    $parties = file("$res_tournaments/$tid/parties");
	$hfile=fopen("$res_tournaments/$tid/tgames",'w');

    // wite players, black players
    $wpls=$bpls=$parties;

    foreach($wpls as $wv) {
      
      array_shift($bpls);

      foreach($bpls as $bv) {
      
        $gid=ioCreateGame(trim($wv),trim($bv),"",$tid)."\n";
        fwrite($hfile,$gid);
        
        $gid=ioCreateGame(trim($bv),trim($wv),"",$tid)."\n";
        fwrite($hfile,$gid);
      }
    }
    fclose($hfile);
   	//header('location:tournament.php');
  }

  // Tournament Table HTML
  public static function _TT_HTML($tid) {

    global $res_tournaments;

	$parties = file("$res_tournaments/$tid/parties"); //print_r($parties);
	$tgames = file("$res_tournaments/$tid/tgames");
	$atinfo = file("$res_tournaments/$tid/tinfo");
	$places = file("$res_tournaments/$tid/places");
    $at = 'width="30px" style="border: 1px solid black;;padding:10px 4px 10px 4px;" align="center" width=65px';

    $count_parties=count($parties);


    for($row=1; $row<=$count_parties; $row++) {

      $table_array[$row][$row]='';

      for ($col=$row+1; $col<=$count_parties; $col++) {

        $tgame1=each($tgames); $tgame2=each($tgames); //echo $tgame1[1];

        $tginfo1=ioLoadGameInfo(null,trim($tgame1[1])); //echo 'tginfo1=<pre>'.$tginfo1['curstate'].'</pre>';
        $tginfo2=ioLoadGameInfo(null,trim($tgame2[1])); 

        //if (empty($tginfo1['curstate'])) { echo "tginfo1={$tgame1[1]}<pre>"; print_r($tginfo1); echo '</pre>'; }

        $table_array[$row][$col]=array($tgame1[1],$tgame2[1],$tginfo1['curstate'],$tginfo2['curstate']);
        $table_array[$col][$row]=array($tgame2[1],$tgame1[1],$tginfo2['curstate'],$tginfo1['curstate']);

      }
    }

    $ttitle=each($atinfo);
    $tt_html="<h1>{$ttitle[1]}</h1>";

    $atimgs=each($atinfo);
    $atimgs=explode(' ',$atimgs[1]);
    foreach($atimgs as $img) {
      $tt_html.="<img src=\"images/default/$img\"/>";
    }


    $tt_html.='<table border=0 cellspacing=0 cellpadding=5 class="textBox" style="border-collapse:collapse;border: 1px solid black;">';

    $tt_html.='<th>';
    for ($col=1; $col<=$count_parties; $col++) $tt_html.="<td $at>$col</td>";
    $tt_html.="<td $at class='textBoxHighlight'>Ps</td>";
    $tt_html.="<td $at class='textBoxHighlight'>Pl</td>";
    $tt_html.='</th>';

    foreach($table_array as $row) {

      $tt_html.='<tr>';

      $player=each($parties); $player[0]++;
      $tt_html.="<td align=\"left\" $at class='textBoxHighlight'>{$player[0]}.&nbsp;{$player[1]}</td>";

      $points[$player[1]]=0;

      foreach($row as $cel) {

  		if ($cel=='') { 
  		  $tt_html.="<td $at class='textBoxHighlight'>&nbsp;</td>"; }
  		else {

          if ($cel[2]=='w') { $cel[2]='1'; $points[$player[1]]++; }
   		  if ($cel[2]=='-') { $cel[2]='&frac12'; $points[$player[1]]+=0.5; }
   		  if ($cel[2]=='b') { $cel[2]='0'; }

   		  if ($cel[3]=='w') { $cel[3]='0'; }
   		  if ($cel[3]=='-') { $cel[3]='&frac12'; $points[$player[1]]+=0.5; }
   		  if ($cel[3]=='b') { $cel[3]='1'; $points[$player[1]]++; }

  		  $tt_html.="<td $at> <a href=http://occ.ms/board.php?gid=".$cel[0].">".$cel[2]."</a>&nbsp;|&nbsp;<a href=http://occ.ms/board.php?gid=".$cel[1].">".$cel[3]."</a> </td>";
  		}
  	  }
    
      $tt_html.="<td $at class='textBoxHighlight'>{$points[$player[1]]}</td>";

      $place=each($places);
      $tt_html.="<td $at class='textBoxHighlight'><b>{$place[1]}</b></td>";

      $tt_html.='</tr>';
    }

    $tt_html.='</table><br><br>';

    return $tt_html;
  }
}


/* Render page */
renderPageBegin('OCC - Tournaments',null,array(
	'Overview'=>'index.php',
	'Search'=>'search.php',
	'Help'=>'help.php',
	'Logout'=>'logout.php'),
	'Tournaments');

//if (isset($_GET['tname'])) Tournament::_createTGs("karpov"); 

// Print Karpov Tournament Table
echo Tournament::_TT_HTML("karpov");

// Print Turov Tournament Table
echo Tournament::_TT_HTML("turov");

renderPageEnd(null); // } Sergei Ki
?>