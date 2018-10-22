<?
  header('Content-Type:text/plain');

  include 'regions.php';
  global $arSettings;

  $data = loadFile($arSettings['regionsFile']);
  $arRegions = loadRegions($data);
  $currentSubdomain = getDomain($arSettings['domain']);
  $generalDomain = $_SERVER["SERVER_NAME"] === $arSettings['domain'];

  if(empty($arRegions[$currentSubdomain]) && $currentSubdomain != $arSettings['domain']) {
    $location = 'Location: '.$arSettings['domain-protocol'].'://'.$arSettings['domain'].'/';
    header($location, true, 301);
    exit();
  }

  if($generalDomain){
    $protocol = $arSettings['domain-protocol'].'://';
  }
  else{
    $protocol = $arSettings['subdomain-protocol'].'://';
  }
 
  if($generalDomain) {
    // robots для основго домена
  } else {
    // robots для поддоменов
    // для вставки текущего поддомена с протоколом используйте $protocol.$_SERVER["SERVER_NAME"]
  }
?>
