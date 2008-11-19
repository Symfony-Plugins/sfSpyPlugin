<?php use_helper('Date', 'I18N') ?>
<html>
<head>
<!--[if lte IE 6]>
<style type="text/css">
/*<![CDATA[*/ 
html {overflow-x:auto; overflow-y:hidden;}
/*]]>*/
</style>
<![endif]-->
<?php echo javascript_include_tag(sfConfig::get('app_sfSpyPlugin_jQuery_path', '/js/jquery.pack.js')) ?>
<?php echo javascript_include_tag('/sfSpyPlugin/js/watch.js') ?>
<script type="text/javascript">
//<![CDATA[
var timestamp = 0;

jQuery(document).ready(function() {
  heartbeat.set({
    url:    "<?php echo url_for('sfSpy/getLatestEvent?id='.$sf_params->get('id'), true) ?>",
    delay:  1000
  });
});
//]]>
</script>
<?php echo stylesheet_tag('/sfSpyPlugin/css/watch.css') ?>
</head>
<body style="padding:0">
  
<iframe id="page" frameborder="0" width="100%" height="100%" /></iframe>

<div id="watch_box">
  <h1><?php echo __('Watching live session %session_id%', array('%session_id%' => $observer->getSessionId())) ?></h1>
  
  <?php echo __('Latest URL') ?>: <span id="latest_url"></span><br/>
  
  <div id="post_data" style="display:none">
    <?php echo __('POST') ?>: <span id="post_url"></span><br/>
    <ul id="post_vars">
    </ul>
  </div>
  
  <?php if ($observer->getIsLive()): ?>
    <?php echo __('Watching for') ?>
  <?php else: ?>
    <?php echo __('Recording for') ?>
  <?php endif ?>
  <span id="watching_for"><?php echo distance_of_time_in_words($observer->getCreatedAt('U')) ?></span><br />
  
  <?php if ($observer->getIsLive()): ?>
    (<?php echo link_to(__('stop watching'), 'sfSpy/stopObserver?id='.$sf_params->get('id')) ?>)
    (<?php echo link_to(__('start recording'), 'sfSpy/switchLive?id='.$sf_params->get('id')) ?>)
  <?php else: ?>
    (<?php echo link_to(__('stop recording'), 'sfSpy/stopObserver?id='.$sf_params->get('id')) ?>)
  <?php endif ?>

</div>
</body>
</html>