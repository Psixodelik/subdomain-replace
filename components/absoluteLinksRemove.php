<?
function absoluteLinksRemove($content, $domain, $protocol) {
  $content = str_replace($protocol.'://'.$domain, '', $content);
  return $content;
}