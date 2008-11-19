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
<?php echo javascript_include_tag('/sfSpyPlugin/js/replay.js') ?>
<script type="text/javascript">
//<![CDATA[
var replay_speed = <?php echo sfConfig::get('app_sfSpyPlugin_replay_speed', 1) ?>;
var timeline     = <?php echo json_encode($sf_data->getRaw('timeline')) ?>;
var duration     = <?php echo $observer->getDuration() ?>;
var start_date   = <?php echo $observer->getCreatedAt('U') ?>;
var page_type    = "<?php echo sfSpyEventPeer::PAGE_TYPE ?>";
var reload_type  = "<?php echo sfSpyEventPeer::RELOAD_TYPE ?>";
var post_type    = "<?php echo sfSpyEventPeer::POST_TYPE ?>";
var post_visibility = <?php echo sfConfig::get('app_sfSpyPlugin_post_visibility', 2) ?>;
var page_url     = "<?php echo url_for('sfSpy/getPage?id=' . $observer->getId() . '&timestamp=PLACEHOLDER', true) ?>";

jQuery(document).ready(function() {
  showProgress();
});
//]]>
</script>
<?php echo stylesheet_tag('/sfSpyPlugin/css/watch.css') ?>
</head>
<body style="padding:0">
  
<iframe id="page" frameborder="0" width="100%" height="100%" /></iframe>

<div id="watch_box">
  <h1><?php echo __('Replaying "%name%"', array('%name%' => $observer->getName())) ?></h1>
  
  <div id="progress_container">
    <div id="progress"></div>
    <?php foreach ($timeline as $time => $events): ?>
      <?php foreach ($events as $type => $details): ?>
        <?php if ($type == sfSpyEventPeer::PAGE_TYPE): ?>
          <div class="page_marker" title="<?php echo $details ?>" style="left: <?php echo $time / $observer->getDuration() * 700 - 2 ?>px;" onclick="rewind(<?php echo $time ?>);"></div>
        <?php elseif ($type == sfSpyEventPeer::RELOAD_TYPE): ?>
          <div class="page_marker reload" title="<?php echo __('Reloading %url%', array('%url%' => $details[1])) ?>" style="left: <?php echo $time / $observer->getDuration() * 700 - 2 ?>px;" onclick="rewind(<?php echo $time ?>);"></div>
        <?php elseif ($type == sfSpyEventPeer::POST_TYPE): ?>
          <div class="page_marker post" title="<?php echo __('Posting data to %url%', array('%url%' => $details[0])) ?>" style="left: <?php echo $time / $observer->getDuration() * 700 - 2 ?>px;" onclick="rewind(<?php echo $time ?>);"></div>
        <?php endif; ?>
      <?php endforeach ?>
    <?php endforeach ?>
  </div>
  
  <div id="playback_controls">
    <?php include_partial('sfSpy/replay_control', array(
      'function' => 'rewind',
      'image'    => 'start', 
      'name'     => 'Start'
    )) ?>
    <?php include_partial('sfSpy/replay_control', array(
      'function' => 'backward',
      'image'    => 'rewind', 
      'name'     => 'Rewind'
    )) ?>
    <?php include_partial('sfSpy/replay_control', array(
      'function' => 'togglePause',
      'image'    => 'pause', 
      'name'     => 'Pause'
    )) ?>
    <?php include_partial('sfSpy/replay_control', array(
      'function' => 'forward',
      'image'    => 'fastforward', 
      'name'     => 'Fast Forward'
    )) ?>
    <?php echo link_to(image_tag('/sfSpyPlugin/images/control_eject_blue.png', array('alt' => __('Back to session details'), 'title' => __('Back to session details'), 'align' => 'absbottom')) . ' ' . __('Back to session details'), 'sfSpy/edit?id='.$observer->getId()) ?>
  </div>
  
  <?php echo __('URL') ?>: <span id="latest_url"></span>
  
  <div id="post_data" style="display:none">
    <?php echo __('POST') ?>: <span id="post_url"></span><br/>
    <ul id="post_vars">
    </ul>
  </div>
  
</div>
</body>
</html>