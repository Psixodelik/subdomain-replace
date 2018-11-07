<?
function showListRegion($arRegions, $arSettings, $mainCity){
  $ListRegion = '<style>
    #region-wrapper {
        background: none repeat scroll 0 0 #ffffff;
        border: 1px solid #cccccc;
        border-radius: 10px;
        box-shadow: 0 0 10px #000000;
        display: none;
        left: 40%;
        margin-left: -230px;
        margin-top: -140px;
        overflow: hidden;
        padding: 20px 0 20px 20px;
        position: fixed;
        top: 30%;
        width: 760px;
        z-index: 100 !important;
    }
    .head-r {
        font-size: 18px;
        margin-bottom: 14px;
    }
    #region-wrapper ul {
        list-style: outside none none;
        margin: 0;
        padding: 0;
    }
    #region-wrapper ul li {
        color: #222222;
        float: left;
        font-size: 13px;
        list-style: outside none none;
        margin: 5px 0;
        padding-left: 15px;
        width: 136px;
    }
    #region-wrapper ul li a {
        border-bottom: 1px solid #cccccc;
        color: #336699;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.4s ease 0s;
    }
    .close-r {
        background: none repeat scroll 0 0 #fafafa;
        border: 1px solid #cccccc;
        border-radius: 6px;
        color: #999999;
        cursor: pointer;
        font-family: Trebuchet MS;
        font-size: 20px;
        height: 20px;
        line-height: 20px;
        position: absolute;
        right: -1px;
        text-align: center;
        top: -3px;
        width: 20px;
    }
    .h_goroda {
      text-align: center;
      margin-bottom: 5px;
      font-size: 13px;
      color: #5C4231;
      font-weight: bold;
    }
    .h_goroda_a {
      color: rgb(232,151,0);
    }
    .h_goroda_a:hover {
      text-decoration: underline;
    }
    @media (max-width: 767px){
      .hidden-xs {display: none !important;}
    }
    @media (max-width: 991px) and (min-width: 768px){
      .hidden-sm {display: none !important;}
    }
    @media (max-width: 1199px) and (min-width: 992px) {
      .hidden-md {display: none !important;}
    }
  </style>';
  $ListRegion .= '<script>
  jQuery(document).ready(function(){
    jQuery(".h_goroda_a").click(function(){
      jQuery("#region-wrapper").fadeIn(300);
      return false;
    });
    jQuery("#region-wrapper .close-r").click(function(){
      jQuery("#region-wrapper").fadeOut(300);
      return false;
    });
    jQuery("#region-wrapper .regions-list a").click(function(){
      name = jQuery(this).text();
      jQuery(".h_goroda_a").text(name);
      jQuery("#region-wrapper").fadeOut(300);
    });
  });
  </script>';

  $curRegion = getDomain($arSettings['domain']);

  if($curRegion == $arSettings['domain']){
    $city = $mainCity;
  }
  else{
    $city = $arRegions[$curRegion]['name'];
  }

  $ListRegion .= '<div class="h_goroda hidden-sm hidden-md hidden-xs">Регион: <a href="#" class="h_goroda_a">'.$city.'</a></div>';
  $ListRegion .= '
    <div id="region-wrapper">
      <div class="head-r">Выберите ваш регион:</div>
      <ul class="regions-list">
  ';
  $ListRegion .= "<li><a href='".$arSettings['domain-protocol']."://www.".$arSettings['domain']."/'>Москва</a></li>";

  foreach ($arRegions as $domain => $region) {
    $ListRegion .= "<li><a href='".$arSettings['subdomain-protocol']."://".$domain.".".$arSettings['domain']."/'>".$region["name"]."</a></li>";
  }

  $ListRegion .= '
      </ul>
      <a class="close-r">x</a>
    </div>
  ';

  return $ListRegion;
}