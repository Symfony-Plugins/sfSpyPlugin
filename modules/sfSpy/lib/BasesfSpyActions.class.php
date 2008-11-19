<?php

/*
 * This file is part of the sfSpyPlugin package.
 * (c) 2007-2008 Francois Zaninotto <francois.zaninotto@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfSpy Administration module
 * Contains actions for watching, and playing back user sessions.
 *
 * @package    plugins.sfSpyPlugin
 * @author     Francois Zaninotto <francois.zaninotto@symfony-project.com>
 */
class BasesfSpyActions extends autosfSpyActions
{
  /**
   * Lists current sessions
   * Requires database session storage
   */
  public function executeListSessions()
  {
    // TODO: check that storage uses database and throw exception if not
    $this->sessions = sfSpySessionPeer::doSelect(new Criteria);
  }
  
  /**
   * Creates a new observer for a given session
   * Expects request parameters:
   *  - session_id (required): Id of a current session to observe
   *  - live (0 by default): 0 to watch live, 1 to record
   */
  public function executeStartObserver()
  {
    $session_id = $this->getRequestParameter('session_id');
    $this->forward404Unless($session_id);
    $is_live = $this->getRequestParameter('live', false);
    
    $observer = sfSpyObserverPeer::startObserver($session_id, $is_live);
    
    if($is_live)
    {
      $this->redirect('sfSpy/watchLive?id='.$observer->getId());
    }
    else
    {
      $this->redirect('sfSpy/listSessions');
    }
  }
  
  /**
   * Stops an observer
   * Expects request parameters:
   *  - id (required): Id of an active observer
   */
  public function executeStopObserver()
  {
    $observer_id = $this->getRequestParameter('id');
    $this->forward404Unless($observer_id);
    
    $observer = sfSpyObserverPeer::retrieveByPk($observer_id);
    $this->forward404Unless($observer);
    
    $observer->stop();
    
    if($observer->getIsLive())
    {
      $this->redirect('sfSpy/listSessions');
    }
    else
    {
      $this->redirect('sfSpy/edit?id='.$observer->getId());
    }
  }
  
  /**
   * Watch a session live
   * Expects request parameters:
   *  - id (required): Id of an active observer
   */
  public function executeWatchLive()
  {
    $observer_id = $this->getRequestParameter('id');
    $this->observer = sfSpyObserverPeer::retrieveByPk($observer_id);
    $this->forward404Unless($this->observer);
  }
  
  /**
   * Changes a not-live observer to a live one
   * Expects request parameters:
   *  - id (required): Id of an active observer
   */
  public function executeSwitchLive()
  {
    $observer_id = $this->getRequestParameter('id');
    $observer = sfSpyObserverPeer::retrieveByPk($observer_id);
    $this->forward404Unless($observer);
    
    $observer->setIsLive(false);
    $observer->deleteEvents();
    
    // Reinit creation date
    $observer->setCreatedAt(time());
    
    $observer->save();
    
    $this->redirect('sfSpy/watchLive?id='.$observer_id);
  }

  /**
   * During live watching, gets the Javascript necessary to show the latest event
   * Expects request parameters:
   *  - id (required): Id of an active observer
   *  - timestamp (optional): Minimal timestamp for the event
   */
  public function executeGetLatestEvent()
  {
    $observer = sfSpyObserverPeer::retrieveByPk($this->getRequestParameter('id'));
    $this->forward404Unless($observer);
    $this->getResponse()->setContentType('text/javascript');
    if($observer->getUpdatedAt('U') == $this->getRequestParameter('timestamp'))
    {
      return sfView::NONE;
    }
    
    $this->events = $observer->getLatestEvents();
    $this->observer = $observer;
  }
  
  /**
   * Displays the content of a page event
   * Expects request parameters:
   *  - id (required): Id of an observer
   *  - timestamp (required): Timestamp of a given page event for the observer
   */
  public function executeGetPage()
  {
    $page = sfSpyPagePeer::retrieveByPk($this->getRequestParameter('id'), $this->getRequestParameter('timestamp'));
    $this->forward404Unless($page);
    
    sfConfig::set('sf_web_debug', false);
    
    return $this->renderText($page->getHtml());
  }
  
  /**
   * Replays a recorded session
   * Expects request parameters:
   *  - id (required): Id of a finished observer
   */
  public function executeReplay()
  {
    $observer_id = $this->getRequestParameter('id');
    $this->observer = sfSpyObserverPeer::retrieveByPk($observer_id);
    $this->forward404Unless($this->observer);
    
    $this->timeline = $this->observer->getTimeline();
  }
}
