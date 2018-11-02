<?
function loadAdress($data, $explodeTabs) {
  foreach ($data as $oneLine) {
    $explodeData = explodeData($oneLine, $explodeTabs);

    if(empty($explodeData)){
      unset($explodeData);
    }

    if($explodeData[0] == 'Адрес на сайте'){
      $arAdress = array();
      unset($explodeData[0]);
      foreach ($explodeData as $adress) {
        $arAdress[] = $adress;
      }
    }
  }

  return $arAdress;
}