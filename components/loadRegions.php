<?
function loadRegions($data, $explodeTabs) {
    
  $arRegions = array();

  foreach ($data as $oneLine) {
    $explodeData = explodeData($oneLine, $explodeTabs);
    

    if(empty($explodeData)){
      unset($explodeData);
    }

    
    
    if($explodeData[0] == 'Поддомен' || $explodeData[0] == 'Адрес на сайте'){
      continue;
    }

    $region = $explodeData[0];

    if(empty($region)){
      $region = translit($explodeData[1]);
    }

    if($region){
      $arRegions[$region] = array(
        'name' => $explodeData[1],
        'adress' => $explodeData[2],
      );


      unset($explodeData[0]);
      unset($explodeData[1]);
      unset($explodeData[2]);

      $count = 1;

      foreach ($explodeData as $replaces) {
        if($count % 2 !== 0)
          $arRegions[$region]['from'][] = $replaces;
        else
          $arRegions[$region]['to'][] = $replaces;

        $count += 1;
      }
    }
    
  }
  return $arRegions;
}