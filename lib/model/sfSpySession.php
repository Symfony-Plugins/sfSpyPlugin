<?php

/**
 * Subclass for representing a row from the 'sf_spy_session' table.
 *
 * 
 *
 * @package plugins.sfSpyPlugin.lib.model
 */ 
class sfSpySession extends BasesfSpySession
{
  protected $dataArray = null;
  
  public function getSessionData($key = null)
  {
    if($this->dataArray === null)
    {
      $sessData = parent::getSessData();
      // Sorry for the big hack
      $currentSession = $_SESSION;
      $_SESSION = array();
      $ret = session_decode($sessData);
      if(!$ret)
      {
        $_SESSION = array();
        $_SESSION = $currentSession;
        
        return false;
      }
      
      $this->dataArray = $_SESSION;
      $_SESSION = array();
      $_SESSION = $currentSession;
    }
    
    if($key)
    {
      return isset($this->dataArray[$key]) ? $this->dataArray[$key] : null;
    }
    else
    {
      return $this->dataArray;
    }
  }
  
  public function getLastRequest($format = 'Y-m-d H:i:s')
  {
    $sessionData = $this->getSessionData();
    if(isset($sessionData['symfony/user/sfUser/lastRequest']))
    {
      return date($format, $sessionData['symfony/user/sfUser/lastRequest']);
    }
    else
    {
      return null;
    }
  }
  
  public function getIsAuthenticated()
  {
    return (boolean) $this->getSessionData('symfony/user/sfUser/authenticated');
  }
  
  public function getCredentials()
  {
    return $this->getSessionData('symfony/user/sfUser/credentials');
  }
  
  public function getAttributes()
  {
    return $this->getSessionData('symfony/user/sfUser/attributes');
  }

  public function getCulture()
  {
    return $this->getSessionData('symfony/user/sfUser/culture');
  }

}
