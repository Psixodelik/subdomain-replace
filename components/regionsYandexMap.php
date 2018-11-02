<?
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
        myMap.geoObjects.add(new ymaps.Placemark(coord, {balloonContent: \"<a href='".$arSettings['subdomain-protocol']."://".$domain.".".$arSettings['domain']."/'>".$region["name"]."</a>\"}, {iconColor: '#0095b6'}));
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
    $content_map .= "<li><a href='".$arSettings['subdomain-protocol']."://".$domain.".".$arSettings['domain']."/' target='_blank'>".$region["name"]."</a></li>";
  }

  $content_map .= '</ul>';

  return $content_map;
}