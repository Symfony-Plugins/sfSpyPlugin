<?php use_helper('Date', 'I18N') ?>
<?php use_stylesheet('/sf/sf_admin/css/main.css') ?>
<div id="sf_admin_container">
<div id="sf_admin_content">
<h1><?php echo __('Active sessions') ?></h1>
<table class="sf_admin_list" cellspacing="0">
  <thead>
    <tr>
      <th><?php echo __('Session ID') ?></th>
      <th><?php echo __('Last Request') ?></th>
      <th><?php echo __('Authenticated') ?></th>
      <th><?php echo __('Credentials') ?></th>
      <th><?php echo __('Attributes') ?></th>
      <th><?php echo __('Culture') ?></th>
      <th><?php echo __('Actions') ?></th>
    </tr>
  </thead>
  <tbody>
    <?php $i = 1; foreach ($sessions as $session): $odd = fmod(++$i, 2) ?>
    <tr class="sf_admin_row_<?php echo $odd ?>">
      <td><?php echo $session->getSessId() ?></td>
      <td><?php echo $session->getLastRequest() ?></td>
      <td>
      <?php if ($session->getIsAuthenticated()): ?>
        <?php echo image_tag(sfConfig::get('sf_admin_web_dir').'/images/tick.png') ?>
      <?php endif ?>
      </td>
      <td><pre><?php echo trim(sfYaml::dump($session->getCredentials(ESC_RAW)), "---\n") ?></pre></td>
      <td><pre><?php echo trim(sfYaml::dump($session->getAttributes(ESC_RAW)), "---\n") ?></pre></td>
      <td><?php echo $session->getCulture() ?></td>
      <td>
        <ul class="sf_admin_td_actions">
      <?php if ($observer = sfSpyObserverPeer::retrieveBySessionId($session->getSessId())): ?>
        <b><?php echo link_to($observer->getIsLive() ? __('watching live') : __('recording'), 'sfSpy/watchLive?id='.$observer->getId()) ?></b><br />
        (<?php echo distance_of_time_in_words($observer->getUpdatedAt('U')) ?> - <?php echo link_to(__('stop'), 'sfSpy/stopObserver?id='.$observer->getId()) ?>)
      <?php else: ?>
        <li><?php echo link_to(image_tag('/sfSpyPlugin/images/page_white_magnify.png', array('alt' => __('Watch live'), 'title' => __('Watch live'))), 'sfSpy/startObserver?session_id='.$session->getSessId().'&live=1') ?></li>
        <li><?php echo link_to(image_tag('/sfSpyPlugin/images/page_white_camera.png', array('alt' => __('Record'), 'title' => __('Record'))), 'sfSpy/startObserver?session_id='.$session->getSessId().'&live=0') ?></li>
      <?php endif ?>
       </ul>
      </td>
    </tr>
    <?php endforeach ?>
  </tbody>
  <tfoot>
    <tr>
      <th colspan="7"><?php echo format_number_choice('[0] no result|[1] 1 result|(1,+Inf] %1% results', array('%1%' => count($sessions)), count($sessions)) ?>
      </th>
    </tr>
  </tfoot>
</table>
<ul class="sf_admin_actions">
  <li><input style="background: #ffc url(/sfSpyPlugin/images/film_go.png) no-repeat 3px 2px" value="<?php echo __('View Recorded Sessions') ?>" type="button" onclick="document.location.href='<?php echo url_for('sfSpy/list') ?>';" /></li>
  </ul>
</div>
</div>