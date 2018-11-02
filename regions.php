<?

include dirname(__FILE__).'/settings.php';
include dirname(__FILE__).'/components/loadFile.php';
include dirname(__FILE__).'/components/loadRegions.php';
include dirname(__FILE__).'/components/loadAdress.php';
include dirname(__FILE__).'/components/explodeData.php';
include dirname(__FILE__).'/components/getDomain.php';
include dirname(__FILE__).'/components/replaces.php';
include dirname(__FILE__).'/components/replaceMap.php';
include dirname(__FILE__).'/components/regionsYandexMap.php';
include dirname(__FILE__).'/components/absoluteLinksRemove.php';
include dirname(__FILE__).'/components/showListRegion.php';
include dirname(__FILE__).'/components/translit.php';

function replaceRegions($content, $arSettings, $explodeTabs = false, $typeReturn = 'echo') {
  
  $locationMainDomain = 'Location: '.$arSettings['domain-protocol'].'://'.$arSettings['domain'].'/'; //Location для основного домена
  $arDataReplaceTags = array(
    'yandexMap' => '#<!--yandex_map-->(.+?)<!--/yandex_map-->#is', //Подстановка карты (с заменой текушей, для этого надо обернуть текущую карту)
    'yandexMapList' => '<!--yandex_map_list--><!--/yandex_map_list-->', //Вывод карты со всеми точками
    'deleteOnSubdomen' => '#<!--delete_on_subdomen-->(.+?)<!--/delete_on_subdomen-->#is', //Удалить на поддоменах
    'dontMakeReplaceOnSubdomen' => '#<!--dont_make_replace_on_subdomen-->(.+?)<!--/dont_make_replace_on_subdomen-->#is', //Не заменять на поддоменах (чаще всего нужно на реквизитах)
    'showListRegion' => '<!--showListRegion--><!--/showListRegion-->', //Показать переключалку городов
  );

  $data = loadFile($arSettings['regionsFile']);

  if($arSettings['absoluteLinksRemove'] == 'Y') {
    $content = absoluteLinksRemove($content, $arSettings['domain'], $arSettings['domain-protocol']);
  }

  if(!empty($data)) {
    $arRegions = loadRegions($data, $explodeTabs);
    $arAdress = loadAdress($data, $explodeTabs);
    $currentSubdomain = getDomain($arSettings['domain']);

    //print_r($arRegions);

    
    if(empty($arRegions[$currentSubdomain]) && $currentSubdomain != $arSettings['domain']) {     
      header($locationMainDomain, true, 301);
      exit();
    }

    if ($_SERVER['REQUEST_SCHEME'] !== $arSettings['domain-protocol'] && $_SERVER['HTTP_HOST'] === $arSettings['domain']) {
      header($locationMainDomain, true, 301);
      exit();
    }

    if($currentSubdomain != $arSettings['domain']){
      $content = replaces($content, $arRegions[$currentSubdomain], $arAdress, $currentSubdomain, $arDataReplaceTags);
    }

    $YandexMapList = regionsYandexMap($arRegions, $arSettings);
    $content = str_replace($arDataReplaceTags['yandexMapList'], $YandexMapList ,$content);

    if (!empty($arSettings['mainCity'])) {
      $showListRegion = showListRegion($arRegions, $arSettings, $arSettings['mainCity']);
      $content = str_replace($arDataReplaceTags['showListRegion'], $showListRegion, $content);
    }  
  }
  
  if ($typeReturn === 'return') {
    return $content;
  } else {
    echo $content;
  }
}