<?
function replaceMap($adress) {
  $YandexMap = '<script src="//api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
    <script type="text/javascript">
      ymaps.ready(function() {
        var myMap = new ymaps.Map("map", {
          center: [55.753994, 37.622093],
          zoom: 13
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
    <div id="map" style="width:680px;height:289px"></div>
  </div>';

  return $YandexMap;
}