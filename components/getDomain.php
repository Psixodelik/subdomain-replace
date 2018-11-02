<?
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