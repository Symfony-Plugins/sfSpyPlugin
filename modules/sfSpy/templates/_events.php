<ul>
<?php foreach ($sf_spy_observer->getsfSpyEvents() as $event): ?>
  <?php if ($event->getType()== sfSpyEventPeer::PAGE_TYPE): ?>
    <li><?php echo date('i\'s\'\'', $event->getCreatedAt('U') - $sf_spy_observer->getCreatedAt('U')) ?> <?php echo $event->getDetails() ?></li>
  <?php elseif ($event->getType()== sfSpyEventPeer::RELOAD_TYPE): ?>
    <li><?php echo date('i\'s\'\'', $event->getCreatedAt('U') - $sf_spy_observer->getCreatedAt('U')) ?> <?php echo __('Reloading %url%', array('%url%' => $event->getDetails(1))) ?></li>
  <?php elseif ($event->getType()== sfSpyEventPeer::POST_TYPE): ?>
    <li>
      <?php echo date('i\'s\'\'', $event->getCreatedAt('U') - $sf_spy_observer->getCreatedAt('U')) ?> <?php echo __('Posting data to %url%', array('%url%' => $event->getDetails(0))) ?>
      <ul style="padding-left:60px"><?php echo $event->getDetails(1, ESC_RAW) ?></ul>
    </li>
  <?php endif; ?>
<?php endforeach ?>
</ul>