<?php
namespace Laclasse;

/**
 * Fonction d'envoi d'un GET http vers l'annuaire ENT.
 *
 * @param string $url_api
 * @param string $app_id
 * @param string $api_key
 * @param array $params
 * @return void
 */
function interroger_annuaire_ENT($url_api, $app_id, $api_key, $params = array()) {
     $query_string = "";
     foreach ($params as $k => $v) $query_string .= urlencode($k)."=".urlencode ($v)."&";
     $query_string = rtrim($query_string, '&');
     $url = $url_api."?".$query_string;
     $ch = curl_init();
     curl_setopt($ch, CURLOPT_URL, $url);
     curl_setopt($ch, CURLOPT_ENCODING ,"");
     curl_setopt($ch, CURLOPT_HEADER, 0);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
     curl_setopt($ch, CURLOPT_USERPWD, $app_id . ":" . $api_key);
     $data = curl_exec($ch);
     if (curl_errno($ch)) {
         return curl_error($ch);
     }
     curl_close($ch);
     return $data;
}