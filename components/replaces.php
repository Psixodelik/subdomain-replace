<?
function replaces($content, $regions, $arReplaceAdress, $subdomain, $arDataReplaceTags) {
  $content = preg_replace($arDataReplaceTags['deleteOnSubdomen'], '',$content);
  preg_match_all($arDataReplaceTags['dontMakeReplaceOnSubdomen'], $content, $match);

  $content = str_replace($arReplaceAdress, $regions['adress'], $content);
  $content = str_replace($regions['from'], $regions['to'], $content);
  
  if (!empty($match[1])) {
        foreach($match[1] as $v) {
          $content = preg_replace($arDataReplaceTags['dontMakeReplaceOnSubdomen'], $v, $content, 1);
        }
      }

      $YandexMap = replaceMap($regions['adress']);
      $content = preg_replace($arDataReplaceTags['yandexMap'], $YandexMap ,$content);

  return $content;
}