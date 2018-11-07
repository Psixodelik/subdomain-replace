<?
  header('Content-Type:text/plain; charset=utf-8');

  include $_SERVER['DOCUMENT_ROOT'].'/subdomain-replace/regions.php';

  $data = loadFile($arSettings['regionsFile']);
  $arRegions = loadRegions($data, true);
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
