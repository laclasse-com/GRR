<?php

namespace Laclasse;

use \Settings;

require_once(__DIR__ . '/../laclasse_api.inc.php');

/**
 * Menu gauche affichage des sites via select
 *
 * @param string $link
 * @param string $current_site
 * @param string $year
 * @param string $month
 * @param string $day
 * @param string $user
 * @return string
 */
function make_site_select_html($link, $current_site, $year, $month, $day, $user)
{
	global $vocab;
	$nb_sites_a_afficher = 0;

	$out_html .= '<b><i>'.get_vocab('sites').get_vocab('deux_points').'</i></b><form id="site_001" action="'.$_SERVER['PHP_SELF'].'"><div>';
	$out_html .= '<select class="form-control" name="site" onchange="site_go()">';

	
	$sql = "SELECT id, sitename ,sitecode
			FROM ".TABLE_PREFIX."_site
			ORDER BY id ASC";

	$user_data = json_decode(interroger_annuaire_ENT(
        Settings::get('laclasse_api_user') . $user,
        Settings::get('laclasse_app_id'),
		Settings::get('laclasse_api_key')));

	$res = grr_sql_query($sql);
	if ($res)
	{
		for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
		{
			// If you're not supposed to have access just go to next iteration
			if(authUserAccesSite($user_data, $row[2]) == false) continue;
			$nb_sites_a_afficher++;	
			$selected = ($row[0] == $current_site) ? 'selected="selected"' : '';
			$link2 = $link.'?year='.$year.'&amp;month='.$month.'&amp;day='.$day.'&amp;area='.$default_area;
			$out_html .= '<option '.$selected.' value="'.$link2.'">'.htmlspecialchars($row[1]).'</option>'.PHP_EOL;
		}
	}
	if ($nb_sites_a_afficher > 1)
	{
		$out_html .= "</select>".PHP_EOL;
		$out_html .= "</div>".PHP_EOL;
		$out_html .= "<script type=\"text/javascript\">".PHP_EOL;
		$out_html .= "function site_go()".PHP_EOL;
		$out_html .= "{".PHP_EOL;
		$out_html .= "box = document.getElementById(\"site_001\").site;".PHP_EOL;
		$out_html .= "destination = box.options[box.selectedIndex].value;".PHP_EOL;
		$out_html .= "if (destination) location.href = destination;".PHP_EOL;
		$out_html .= "}".PHP_EOL;
		$out_html .= "</script>".PHP_EOL;
		$out_html .= "<noscript>".PHP_EOL;
		$out_html .= "<div>".PHP_EOL;
		$out_html .= "<input type=\"submit\" value=\"Change\" />".PHP_EOL;
		$out_html .= "</div>".PHP_EOL;
		$out_html .= "</noscript>".PHP_EOL;
		$out_html .= "</form>".PHP_EOL;
		return $out_html;
	}
}

/**
 * Affichage des domaines sous la forme d'une liste
 *
 * @param string $link
 * @param string $current_site
 * @param string $year
 * @param string $month
 * @param string $day
 * @param string $user
 * @return string
 */
function make_site_list_html($link, $current_site, $year, $month, $day,$user)
{
	global $vocab;
	
	$user_data = json_decode(interroger_annuaire_ENT(
        Settings::get('laclasse_api_user') . $user,
        Settings::get('laclasse_app_id'),
		Settings::get('laclasse_api_key')));

	$out_html .= '
		<b><i><span class="bground">'.get_vocab('sites').get_vocab('deux_points').'</span></i></b>
		<br />';
	$sql = "SELECT id,sitename,sitecode
			FROM ".TABLE_PREFIX."_site
			ORDER BY sitename";
	$nb_sites_a_afficher = 0;
	$res = grr_sql_query($sql);
	if ($res)
	{
		for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
		{
			// If you're not supposed to have access just go to next iteration
			if(authUserAccesSite($user_data, $row[2]) == false) continue;
			$nb_sites_a_afficher++;

			if ($row[0] == $current_site)
			{
				$out_html .= '
				<b><a id="liste_select"   href="'.$link.'?year='.$year.'&amp;month='.$month.'&amp;day='.$day.'&amp;id_site='.$row[0].'" title="'.$row[1].'">&gt; '.htmlspecialchars($row[1]).'</a></b>
				<br />'."\n";
			} else
			{
				$out_html .= '
				<a id="liste"  href="'.$link.'?year='.$year.'&amp;month='.$month.'&amp;day='.$day.'&amp;id_site='.$row[0].'" title="'.$row[1].'">'.htmlspecialchars($row[1]).'</a>
				<br />'."\n";
			}
		}
	}
	if ($nb_sites_a_afficher > 1)
		return $out_html;
	else
		return '';
	
}

function make_site_item_html($link, $current_site, $year, $month, $day, $user)
{
    global $vocab;
    $nb_sites_a_afficher = 0;
	$out_html = '<ul class="list-group"><li class="list-group-item">'.get_vocab('sites').get_vocab('deux_points').'</li></ul><form class="ressource" id="site_001" action="'.$_SERVER['PHP_SELF'].'"><div>';
	
	$sql = "SELECT id, sitename ,sitecode
			FROM ".TABLE_PREFIX."_site
			ORDER BY id ASC";

	$user_data = json_decode(interroger_annuaire_ENT(
        Settings::get('laclasse_api_user') . $user,
        Settings::get('laclasse_app_id'),
		Settings::get('laclasse_api_key')));

	$res = grr_sql_query($sql);
	if ($res)
	{
		for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
		{
			// If you're not supposed to have access just go to next iteration
			if(authUserAccesSite($user_data, $row[2]) == false) continue;
			$nb_sites_a_afficher++;

			$sql = "SELECT id_area FROM ".TABLE_PREFIX."_j_site_area WHERE ".TABLE_PREFIX."_j_site_area.id_site='".$row[0]."'";
			$res2 = grr_sql_query($sql);
			$default_area = -1;
			if ($res2 && grr_sql_count($res2) > 0)
			{
				for ($j = 0; ($row2 = grr_sql_row($res2, $j)); $j++)
				{
					if (authUserAccesArea($user,$row2[0]) == 1)
					{
						$default_area = $row2[0];
						$j = grr_sql_count($res2) + 1;
					}
				}
			}
			grr_sql_free($res2);
			if ($default_area != -1)
			{
				$link2 = $link.'?year='.$year.'&amp;month='.$month.'&amp;day='.$day.'&amp;area='.$default_area;
				$out_html .="\n";
			}
			else
				$link2 = $link.'?year='.$year.'&amp;month='.$month.'&amp;day='.$day.'&amp;id_site='.$row[0];
			if ($current_site != null)
			{
				if ($current_site == $row[0])
					$out_html .= "<input id=\"item_select\" type=\"button\" class=\"btn btn-primary btn-xs\" name=\"$row[0]\" value=\"".htmlspecialchars($row[1])."\" onclick=\"location.href='$link2';charger();\" /><br />".PHP_EOL;
				else
					$out_html .= "<input type=\"button\" class=\"btn btn-default btn-xs item\" name=\"$row[0]\" value=\"".htmlspecialchars($row[1])." \" onclick=\"location.href='$link2';charger();\" /><br />".PHP_EOL;
			}
			else
				$out_html .= "<input type=\"button\" class=\"btn btn-default btn-xs item\" name=\"$row[0]\" value=\"".htmlspecialchars($row[1])." \" onclick=\"location.href='$link2';charger();\" /><br />".PHP_EOL;
		}
	}
	if ($nb_sites_a_afficher > 1)
	{
		// s'il y a au moins deux sites à afficher, on met une liste déroulante, sinon, on affiche rien.
		$out_html .= '</form>'.PHP_EOL;
		$out_html .= '</div>'.PHP_EOL;
		$out_html .= '<script type="text/javascript">'.PHP_EOL;
		$out_html .= 'function site_go()'.PHP_EOL;
		$out_html .= '{'.PHP_EOL;
		$out_html .= 'box = document.getElementById("site_001").site;'.PHP_EOL;
		$out_html .= 'destination = box.options[box.selectedIndex].value;'.PHP_EOL;
		$out_html .= 'if (destination) location.href = destination;'.PHP_EOL;
		$out_html .= '}'.PHP_EOL;
		$out_html .= '</script>'.PHP_EOL;
		$out_html .= '<noscript>'.PHP_EOL;
		$out_html .= '<div>'.PHP_EOL;
		$out_html .= '<input type="submit" value="change" />'.PHP_EOL;
		$out_html .= '</div>'.PHP_EOL;
		$out_html .= '</noscript>'.PHP_EOL;
		$out_html .= '</form>'.PHP_EOL;
		return $out_html;
	}
}

/**
 * Vérifie si l'utilisateur a accès à un site
 *
 * @param object $user_data : les données utilisateur venant de l'annuaire
 * @param string $sitecode : le site à accéder
 * @return bool true si il a accès, faux sinon
 */
function authUserAccesSite($user_data,$sitecode) {
	// For debug purpose so I don't have to use another account
	// cause me from the past is lazy too
	if($user_data->super_admin) return true;

	$filtered_array = array_filter($user_data->profiles , function($profile) use ($sitecode){
		return $profile->structure_id == $sitecode;
	});
	
	if(empty($filtered_array)) return false;

	$profile = reset($filtered_array);
	
	return !in_array($profile->type,[ 'ELV' , 'TUT' , 'ACA' ]);

}

?>