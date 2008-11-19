<?php

/*
 * This file is part of the sfSpyPlugin package.
 * (c) 2007-2008 Francois Zaninotto <francois.zaninotto@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Subclass for representing a row from the 'sf_spy_observer' table.
 *
 * 
 *
 * @package plugins.sfSpyPlugin.lib.model
 */ 
class sfSpyObserver extends BasesfSpyObserver
{
  /**
   * Stop an active observer
   * If the observer records a session, update its calculated fields
   */
  public function stop()
  {
    if(!$this->getIsActive())
    {
      throw new sfException('Trying to stop a non-active observer');
    }
    if($this->getIsLive())
    {
      $this->delete();
      // onDelete cascade should take care of the related sfSpyEvents and sfSpyPages
    }
    else
    {
      $this->setIsActive(false);
      $this->setNbEvents($this->countsfSpyEvents());
      $eventTimeLimits = $this->getEventTimeLimits('U');
      $this->setDuration($eventTimeLimits[1] - $eventTimeLimits[0]);
      $this->setCreatedAt((integer) $eventTimeLimits[0]);
      $this->save();
    }
  }
  
  /**
   * Returns a human-readable version of the observer duration (mm:ss)
   *
   * @return String
   */
  public function getDurationFormatted()
  {
    return date('i\'s\'\'', $this->getDuration());
  }
  
  /**
   * For live observers, get the related event object or create it if it doesn't exist yet
   *
   * @return sfSpyEvent
   */
  public function getEventOrCreate()
  {
    if(!$this->getIsLive())
    {
      throw new sfException('Trying to get a unique event on a non-live observer');
    }
    $c = new Criteria();
    $c->add(sfSpyEventPeer::OBSERVER_ID, $this->getId());
    $event = sfSpyEventPeer::doSelectOne($c);
    
    if(!$event)
    {
      $event = new sfSpyEvent();
      $event->setObserverId($this->getId());
    }
    
    return $event;
  }
  
  /**
   * Retrieve the ordered list of events of the recorded session.
   * Return format is an associative array, the keys being the seconds after the recorded session beginning.
   * Example: Array(
   *   12 => array("page" => "/first_url"),
   *   17 => array("page" => "/second_url", "pointer" => "134x543")
   * )
   *
   * @return Array The recorded session timeline
   */
  public function getTimeline()
  {
    $c = new Criteria();
    $c->add(sfSpyEventPeer::OBSERVER_ID, $this->getId());
    $c->addAscendingOrderByColumn(sfSpyEventPeer::CREATED_AT);
    $events = sfSpyEventPeer::doSelect($c);
    $timeline = array();
    
    foreach($events as $event)
    {
      $time = $event->getCreatedAt('U') - $this->getCreatedAt('U');
      $timeline[$time][$event->getType()] = $event->getDetails();
    }
    ksort($timeline);
    
    return $timeline;
  }
  
  /**
   * Get the latest events of an observer
   *
   * @return Array List of sfSpyEvent objects
   */
  public function getLatestEvents()
  {
    $c = new Criteria();
    $c->add(sfSpyEventPeer::CREATED_AT, $this->getUpdatedAt('U'));
    $c->addDescendingOrderByColumn(sfSpyEventPeer::CREATED_AT);
    
    return $this->getsfSpyEvents($c);
  }
  
  /**
   * Gets the date of the first and latest event for the observer
   *
   * @param String Optional date format ('U' by default)
   *
   * @return Array First and last date, formatted as a string or a timestamp
   */
  public function getEventTimeLimits($format = 'U')
  {
    $c = new Criteria();
    $c->addAscendingOrderByColumn(sfSpyEventPeer::CREATED_AT);
    $c->setLimit(1);
    $early_events = $this->getsfSpyEvents($c);

    $c = new Criteria();
    $c->addDescendingOrderByColumn(sfSpyEventPeer::CREATED_AT);
    $c->setLimit(1);
    $late_events = $this->getsfSpyEvents($c);
    
    return array($early_events[0]->getCreatedAt($format), $late_events[0]->getCreatedAt($format));
  }
  
  /**
   * Deletes the related sfSpyEvent and sfSpyPage objects
   */
  public function deleteEvents()
  {
    $c = new Criteria();
    $c->add(sfSpyEventPeer::OBSERVER_ID, $this->getId());
    sfSpyEventPeer::doDelete($c);
    
    $c = new Criteria();
    $c->add(sfSpyPagePeer::OBSERVER_ID, $this->getId());
    sfSpyPagePeer::doDelete($c);
  }
}
