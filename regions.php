<?

  /* Настройки */

  $arSettings = array(
    'domain' => '', //Основной домен сайта
    'domain-protocol' => '', //Протокол основного домена
    'subdomain-protokol' => '', //Протокол поддоменов
    'regionsFile' => dirname(__FILE__)."/regions.csv", //Путь к файлу с заменами
    'absoluteLinksRemove' => 'N', //Удаление абсолютных ссылок на сайте
  );

  function replaceRegions($content, $typeReturn = 'echo') {
    global $arSettings;
    $arDataReplaceTags = array(
      'yandexMap' => '#<!--yandex_map-->(.+?)<!--/yandex_map-->#is', //Подстановка карты (с заменой текушей, для этого надо обернуть текущую карту)
      'yandexMapList' => '<!--yandex_map_list--><!--/yandex_map_list-->', //Вывод карты со всеми точками
      'deleteOnSubdomen' => '#<!--delete_on_subdomen-->(.+?)<!--/delete_on_subdomen-->#is', //Удалить на поддоменах
      'dontMakeReplaceOnSubdomen' => '#<!--dont_make_replace_on_subdomen-->(.+?)<!--/dont_make_replace_on_subdomen-->#is', //Не заменять на поддоменах (чаще всего нужно на реквизитах)
    );

    $data = loadFile($arSettings['regionsFile']);

    if($arSettings['absoluteLinksRemove'] == 'Y') {
      $content = absoluteLinksRemove($content, $arSettings['domain'], $arSettings['domain-protocol']);
    }

    if(!empty($data)) {
      $arRegions = loadRegions($data);
      $arAdress = loadAdress($data);

      $currentSubdomain = getDomain($arSettings['domain']);

      if(empty($arRegions[$currentSubdomain]) && $currentSubdomain != $arSettings['domain']) {
        $location = 'Location: '.$arSettings['domain-protocol'].'://'.$arSettings['domain'].'/';
        header($location, true, 301);
        exit();
      }

      if($currentSubdomain != $arSettings['domain']){
        $content = replaces($content, $arRegions[$currentSubdomain], $arAdress, $currentSubdomain, $arDataReplaceTags);
      }

      $YandexMapList = regionsYandexMap($arRegions, $arSettings);
      $content = str_replace($arDataReplaceTags['yandexMapList'], $YandexMapList ,$content);
    }
    
    if ($typeReturn === 'return') {
      return $content;
    } else {
      echo $content;
    }
  }

  function loadFile($path) {
    return file($path);
  }

  function loadRegions($data) {
    
    $arRegions = array();

    foreach ($data as $oneLine) {
      $explodeData = explodeData($oneLine);

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

  function loadAdress($data) {
    foreach ($data as $oneLine) {
      $explodeData = explodeData($oneLine);

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

  function explodeData($line) {
    $explodeData = explode(';', trim($line));
    return $explodeData;
  }

  function getDomain($mainDomain) {
    $exploded_sname = explode(".", $_SERVER["SERVER_NAME"]);
    $subdomain = $exploded_sname[0];

    $mainDomainExp = explode(".", $mainDomain);
    $mainDomainExp = $mainDomainExp[0];

    if($subdomain !== $mainDomainExp && $subdomain !== 'www'){
      return $subdomain;
    }
    else{
      return $mainDomain;
    }

  }

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

  function replaceMap($adress) {
    $YandexMap = '<script src="//api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
      <script type="text/javascript">
        ymaps.ready(function() {
          var myMap = new ymaps.Map("map", {
            center: [55.753994, 37.622093],
            zoom: 16
          });
      ';
    $YandexMap .= "
      var myGeocoder = ymaps.geocode('".$adress."');
      myGeocoder.then(function (res) {
          coord = res.geoObjects.get(0).geometry.getCoordinates();
          myMap.geoObjects.add(new ymaps.Placemark(coord, {balloonContent: \"".$adress."\"}, {iconColor: '#0095b6'}));
          
          // Центрирует и масштабирует карту по объектам
          myMap.setCenter(coord);
        }
      ); 
    ";
    $YandexMap .= '});
    </script>
    <div class="mo">
      <div id="map" style="height:500px; width: 100%"></div>
    </div>';

    return $YandexMap;
  }

  function regionsYandexMap($arRegions, $arSettings) {
    $content_map = '
      <style>
        ul.gorod_list {
            list-style: none;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            margin: 0;
            padding: 0;
            -webkit-box-pack: start;
                -ms-flex-pack: start;
                    justify-content: flex-start;
            -ms-flex-wrap: wrap;
                flex-wrap: wrap;
            margin-top: 30px;
        }

        ul.gorod_list li {
            width: calc((100% / 5) - 10px);
            margin-right: 10px;
            height: 40px;
            border: 1px solid #CCC;
            margin-bottom: 10px;
        }

        ul.gorod_list li a {
            font-size: 12px;
            font-weight: bold;
            color: #0095b6;
            width: 100%;
            height: 100%;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
                -ms-flex-align: center;
                    align-items: center;
            padding: 0 0 0 15px;
        }

        ul.gorod_list li:hover {
            background: #0095b6;
            -webkit-transition: 1s;
            transition: 1s;
        }

        ul.gorod_list li:hover a {
            color: #FFF;
        }
      </style>
    ';
    $content_map .= '<script src="//api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>';
    $content_map .= '<script type="text/javascript">
      ymaps.ready(function() {
        var myMap = new ymaps.Map("map", {
          center: [55.753994, 37.622093],
          zoom: 9
        });
    ';

    foreach ($arRegions as $domain => $region) {
      $content_map .= "
        var myGeocoder = ymaps.geocode('".$region["adress"]."');
        myGeocoder.then(function(res) {
          coord = res.geoObjects.get(0).geometry.getCoordinates();
          myMap.geoObjects.add(new ymaps.Placemark(coord, {balloonContent: \"<a href='".$arSettings['subdomain-protokol']."://".$domain.".".$arSettings['domain']."/'>".$region["name"]."</a>\"}, {iconColor: '#0095b6'}));
        }); 
      ";
    }

    $content_map .= '});
      </script>
      <div class="mo">
        <div id="map" style="height:500px;"></div>
      </div>';

    $content_map .= '<ul class="gorod_list">';

    foreach ($arRegions as $domain => $region) {
      $content_map .= "<li><a href='".$arSettings['subdomain-protokol']."://".$domain.".".$arSettings['domain']."/' target='_blank'>".$region["name"]."</a></li>";
    }

    $content_map .= '</ul>';

    return $content_map;
  }

  function absoluteLinksRemove($content, $domain, $protokol) {
    $content = str_replace($protokol.'://'.$domain, '', $content);
    return $content;
  }

  function translit($s) {
    $s = strip_tags((string)$s); // убираем HTML-теги
    $s = str_replace(array("\n", "\r"), " ", $s); // убираем перевод каретки
    $s = preg_replace("/\s+/", ' ', $s); // удаляем повторяющие пробелы
    $s = trim($s); // убираем пробелы в начале и конце строки
    $s = function_exists('mb_strtolower') ? mb_strtolower($s) : strtolower($s); // переводим строку в нижний регистр (иногда надо задать локаль)
    $trans = array('а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'j','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'sch','ь'=>'','ы'=>'y','ъ'=>'','э'=>'e','ю'=>'yu','я'=>'ya','А'=>'A','Б'=>'B','В'=>'V','Г'=>'G','Д'=>'D','Е'=>'E','Ё'=>'E','Ж'=>'J',  'З'=>'Z','И'=>'I','Й'=>'Y','К'=>'K','Л'=>'L','М'=>'M','Н'=>'N','О'=>'O','П'=>'P','Р'=>'R','С'=>'S','Т'=>'T','У'=>'U','Ф'=>'F','Х'=>'H','Ц'=>'C','Ч'=>'Ch','Ш'=>'Sh','Щ'=>'Sch','Ь'=>'','Ы'=>'Y','Ъ'=>'','Э'=>'E','Ю'=>'Yu','Я'=>'Ya',""=>"-","\""=>"",'"'=>"","("=>"",")"=>"","."=>"-"," "=>"-","'"=>'','№'=>'','---'=>'-','--'=>'-');
    $s =  str_replace( array_keys($trans), array_values($trans), $s);
    
    $s = preg_replace("/[^0-9a-z-_ ]/i", "", $s); // очищаем строку от недопустимых символов
    $s = str_replace(" ", "-", $s); // заменяем пробелы знаком минус
    return $s;
  }

?>
