<?php

/*
 * This file is part of the sfSpyPlugin package.
 * (c) 2007-2008 Francois Zaninotto <francois.zaninotto@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// Don't ask me why, but without this line, PHP segfaults
require_once 'om/BasesfSpyObserverPeer.php';

/**
 * Subclass for performing query and update operations on the 'sf_spy_observer' table.
 *
 * 
 *
 * @package plugins.sfSpyPlugin.lib.model
 */ 
class sfSpyObserverPeer extends BasesfSpyObserverPeer
{
  /**
   * Start an observer for a given session
   *
   * @param Integer Session id
   * @param Boolean Optional live Flag. If true, the observer will always override the same event object
   * @param String  Optional observer name
   * @param Integer Optional duration in seconds. If set, the observer will automatically stop at the last event following the duration.
   *
   * @return sfSpyObserver
   */
  public static function startObserver($sessionId, $isLive = false, $name = '', $duration = null)
  {
    $observer = new sfSpyObserver();
    $observer->setSessionId($sessionId);
    $observer->setIsActive(true);
    $observer->setIsLive($isLive);
    $observer->setName($name);
    $observer->setDuration($duration);
    $observer->save();
    
    return $observer;
  }

  /**
   * Stop an observer for a given session
   * @see sfSpyObserver::stop()
   *
   * @param Integer Session id
   *
   * @return Boolean True if an observer exists for this session, false otherwise
   */  
  public static function stopObserver($sessionId)
  {
    $c = new Criteria();
    $c->add(self::SESSION_ID, $sessionId);
    $c->add(self::IS_ACTIVE, true);
    $observer = self::doSelectOne();
    if($observer)
    {
      $observer->stop();
      return true;
    }
    else
    {
      // No observer running for this session
      return false;
    }
  }
  
  /**
   * Checks if an active observer exists for a given session id
   *
   * @param Integer Session id
   *
   * @return Boolean True if an active observer exists for this session, false otherwise
   */
  public static function isObserved($sessionId)
  {
    $c = new Criteria();
    $c->add(self::SESSION_ID, $sessionId);
    $c->add(self::IS_ACTIVE, true);
    $nbObservers = self::doCount($c);
    
    return ($nbObservers > 0);
  }
  
  /**
   * Retrieves an active observer for a given session id
   *
   * @param Integer Session id
   *
   * @return mixed sfSpyObserver object or null
   */
  public static function retrieveBySessionId($sessionId)
  {
    $c = new Criteria();
    $c->add(self::SESSION_ID, $sessionId);
    $c->add(self::IS_ACTIVE, true);
    
    return self::doSelectOne($c);
  }

}
