function tellIsRead()
{
  jQuery.getScript(is_read_url);
}

jQuery(document).ready(function(){
  jQuery(window).bind('load', tellIsRead);
  jQuery(window).bind('pageshow', function(event) { if (event.persisted) tellIsRead();} );
})