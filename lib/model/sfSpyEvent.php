<?php

/*
 * This file is part of the sfSpyPlugin package.
 * (c) 2007-2008 Francois Zaninotto <francois.zaninotto@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
/**
 * Subclass for representing a row from the 'sf_spy_event' table.
 *
 * 
 *
 * @package plugins.sfSpyPlugin.lib.model
 */ 
class sfSpyEvent extends BasesfSpyEvent
{
  protected $processed_details;
  
  /**
   * Stores the HTML code of a page event in a related sfSpyPage object
   *
   * @param String
   */
  public function setPage($code, $url)
  {
    $page = sfSpyPagePeer::retrieveByPk($this->getObserverId(), $this->getCreatedAt('U'));
    if(!$page)
    {
      $page = new sfSpyPage();
      $page->setObserverId($this->getObserverId());
      $page->setCreatedAt($this->getCreatedAt());
    }
    $page->setHtml($code);
    $page->setUrl($url);
    $page->save();
  }
  
  /**
   * Sets detail string or an array or detail strings for events that have multiple details.
   * Events store all details in the "details" field,
   * Separated by a pipe (|) if more than one detail exist.
   * The data stored in the details depends on the event type:
   * page :  url
   * reload: timestamp | url
   *
   * @param mixed Array or string
   */
  public function setDetails($details)
  {
    if(is_array($details))
    {
      parent::setDetails(implode('|', $details));
    }
    else
    {
      parent::setDetails($details);
    }
  }
  
  /**
   * Gets detail string or an array or detail strings for events that have multiple details.
   * @see sfSpyEvent::setDetails()
   *
   * @param Integer Optional index of the detail
   *
   * @return mixed Array or String
   */
  public function getDetails($index = null)
  {
    if(!$this->processed_details)
    {
      if(strpos($this->details, '|'))
      {
        // More than one detail: convert to array
        $this->processed_details = explode('|', $this->details);
      }
      else
      {
        $this->processed_details = $this->details;
      }
    }
    
    return ($index === null) ? $this->processed_details : $this->processed_details[$index];
  }
  
  /**
   * Override the base save() method to update the related observer calculated fields
   */
  public function save($con = null)
  {
    if(!$con)
    {
      $con = Propel::getConnection();
    }
    
    // database transaction
    try
    {
      $con->begin();
      
      parent::save($con);
      
      $observer = $this->getsfSpyObserver();
      $observer->setUpdatedAt($this->getCreatedAt());
      $observer->save($con);
      
      // Check to see if the observer was initialized with a max duration and therefore could need stopping
      $duration = $observer->getDuration();
      if($duration && ($this->getCreatedAt('U') - $observer->getCreatedAt('U')) > $duration)
      {
        $observer->stop();
      }
      
      $con->commit();
    }
    catch (Exception $e)
    {
      $con->rollback();
      throw $e;
    }
  }
}
