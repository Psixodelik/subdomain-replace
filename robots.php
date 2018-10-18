<?

  include 'regions.php';
  $data = loadFile($arSettings['regionsFile']);
  $arRegions = loadRegions($data);
  $currentSubdomain = getDomain($arSettings['domain']);

  if(empty($arRegions[$currentSubdomain]) && $currentSubdomain != $arSettings['domain']) {
    $location = 'Location: '.$arSettings['domain-protocol'].'://'.$arSettings['domain'].'/';
    header($location, true, 301);
    exit();
  }


	header('Content-Type:text/plain');

	$arSettings = array(
		'domain' => '',
		'domain-protocol' => '',
		'subdomain-protocol' => '',
	);

	if($_SERVER["SERVER_NAME"] == $arSettings['domain']){
		$protocol = $arSettings['domain-protocol'].'://';
	}
	else{
		$protocol = $arSettings['subdomain-protocol'].'://';
	}

  // для вставки текущего поддомена с протоколом используйте $protocol.$_SERVER["SERVER_NAME"]

?>