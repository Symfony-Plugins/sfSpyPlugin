<?php

/*
 * This file is part of the sfSpyPlugin package.
 * (c) 2007-2008 Francois Zaninotto <francois.zaninotto@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfSpy Listen module
 * Contains actions for recording user sessions.
 *
 * @package    plugins.sfSpyPlugin
 * @author     Francois Zaninotto <francois.zaninotto@symfony-project.com>
 */
class sfSpyListenActions extends sfActions
{
  
  /**
   * Displays the content of a page event
   * Expects request parameters:
   *  - id (required): Id of an observer
   *  - timestamp (required): Timestamp of a given page event for the observer
   */
  public function executeTellIsRead()
  {
    $this->forward404Unless(sfConfig::get('app_sfSpyPlugin_enabled', false));
    $observerId = $this->getRequestParameter('id');
    $timestamp = $this->getRequestParameter('timestamp');
    $page = sfSpyPagePeer::retrieveByPk($observerId, $timestamp);
    $this->forward404Unless($page);
    
    if ($page->getIsRead())
    {
      // Page already read. Probably a reload on the client side via history navigation
      $event = new sfSpyEvent();
      $event->setObserverId($observerId);
      $event->setDetails(array($timestamp, $page->getUrl()));
      $event->setType(sfSpyEventPeer::RELOAD_TYPE);
      $event->save();
    }
    else
    {
      // First time the page is read
      $page->setIsRead(true);
      $page->save();
    }
    
    sfConfig::set('sf_web_debug', false);
    $this->setLayout(false);
    $this->getResponse()->setContentType('text/javascript');
    
    return $this->renderText('');
  }
  
}

?>