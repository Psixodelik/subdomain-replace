<?
function explodeData($line, $explodeTabs) {

  $line = trim($line); // Убираем спец. символы
  $line = str_replace('""', '"', $line); // Заменяем две двойные кавычки на одни (иногда происходит при экспорте в csv)

  $explodeChar = $explodeTabs ? '	' : ';';

  $explodeData = explode($explodeChar, trim($line));

  $lrTrimData = array_map(function($oneData){

    $oneData = ltrim($oneData, '"');
    $oneData = rtrim($oneData, '"');
    return $oneData;

  }, $explodeData);

  return $lrTrimData;
}