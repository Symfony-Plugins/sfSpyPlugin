<?php use_helper('Date') ?>
<?php foreach ($events as $event): ?>
  <?php if ($event->getType() == sfSpyEventPeer::PAGE_TYPE): ?>
    timestamp = <?php echo $event->getCreatedAt('U') ?>;
    jQuery('#page').attr('src', '<?php echo url_for('sfSpy/getPage?id='.$observer->getId().'&timestamp='.$event->getCreatedAt('U'), true) ?>');
    jQuery('#latest_url').html('<?php echo $event->getDetails() ?>');
  <?php elseif ($event->getType() == sfSpyEventPeer::RELOAD_TYPE): ?>
    timestamp = <?php echo $event->getCreatedAt('U') ?>;
    jQuery('#page').attr('src', '<?php echo url_for('sfSpy/getPage?id='.$observer->getId().'&timestamp='.$event->getDetails(0), true) ?>');
    jQuery('#latest_url').html('<?php echo $event->getDetails(1) ?>');
  <?php elseif ($event->getType() == sfSpyEventPeer::POST_TYPE): ?>
    timestamp = <?php echo $event->getCreatedAt('U') ?>;
    jQuery('#latest_url').html('<?php echo $event->getDetails(0) ?>');
    jQuery('#post_url').html('<?php echo $event->getDetails(0) ?>');
    jQuery('#post_vars').html('<?php echo $event->getDetails(1, ESC_RAW) ?>');
    jQuery('#post_data').toggle();
    setTimeout("document.getElementById('post_data').style.display = 'none';", <?php echo sfConfig::get('app_sfSpyPlugin_post_visibility', 2) * 1000 ?>);
  <?php endif; ?>
<?php endforeach ?>
jQuery('#watching_for').html('<?php echo distance_of_time_in_words($observer->getCreatedAt('U')) ?>');
