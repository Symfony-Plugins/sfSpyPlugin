<?php

/*
 * This file is part of the sfSpyPlugin package.
 * (c) 2007-2008 Francois Zaninotto <francois.zaninotto@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfSpy Filter
 * Saves page events for observed sessions
 *
 * @package    plugins.sfSpyPlugin
 * @author     Francois Zaninotto <francois.zaninotto@symfony-project.com>
 */
class sfSpyFilter extends sfFilter
{
  protected function haveToExecute()
  {
    return $this->isFirstCall() 
      && sfConfig::get('app_sfSpyPlugin_enabled', false)
      && strpos($this->getContext()->getRequest()->getParameter('module'), 'sfSpy') === false;
  }
  
  /**
   * Executes this filter.
   *
   * @param sfFilterChain A sfFilterChain instance
   */
  public function execute($filterChain)
  { 
    $haveToExecute = $this->haveToExecute();
    
    $filterChain->execute();
    
    if ($haveToExecute)
    {
      $session_id = session_id();
      $context = $this->getContext();
      $request = $context->getRequest();
      $method = $request->getMethod();
      $url = $request->getUri();
      $content = $context->getResponse()->getContent();
      
      if($method == sfRequest::GET && !$request->isXmlHttpRequest())
      {
        if(sfSpyObserverPeer::isObserved($session_id))
        {
          $observer = sfSpyObserverPeer::retrieveBySessionId($session_id);
        
          // Save page event
          $event = new sfSpyEvent();
          $event->setsfSpyObserver($observer);
          $event->setDetails($url);
          $event->setType(sfSpyEventPeer::PAGE_TYPE);
          $event->save();

          // Save html code in page object
          $event->setPage($content, $url);

          // Include JavaScript to detect history navigation
          sfLoader::loadHelpers(array('Tag', 'Asset'));
          $html = '';
          if(sfConfig::get('app_sfSpyPlugin_include_jQuery', true))
          {
            $html  .= javascript_include_tag(sfConfig::get('app_sfSpyPlugin_jQuery_path'));
          }
          $html .= javascript_include_tag('/sfSpyPlugin/js/listen.js');

          // Configure JavaScript to detect history navigation
          $html .= "<script type=\"text/javascript\">\n";
          $html .= "var is_read_url = '" . $context->getController()->genUrl('sfSpyListen/tellIsRead?id='.$observer->getId().'&timestamp='.$event->getCreatedAt('U'), true)."';\n";
          $html .= "</script>\n";
          $context->getResponse()->setContent(str_ireplace('</head>', $html.'</head>', $content));
          
          if (sfConfig::get('sf_logging_enabled', false))
          {
            $context->getLogger()->info(sprintf('{sfSpy} %s GET request (observer Id: %s)', $observer->getIsLive() ? 'Observing' : 'Recording', $observer->getId()));
          }
        }
      }
      elseif ($method == sfRequest::POST && sfConfig::get('app_sfSpyPlugin_record_post', true))
      {
        if(sfSpyObserverPeer::isObserved($session_id))
        {
          $observer = sfSpyObserverPeer::retrieveBySessionId($session_id);
          
          // Save post event
          $event = new sfSpyEvent();
          $event->setsfSpyObserver($observer);
          $event->setDetails(array($url, $this->getPostList()));
          $event->setType(sfSpyEventPeer::POST_TYPE);
          $event->save();
          
          if (sfConfig::get('sf_logging_enabled', false))
          {
            $context->getLogger()->info(sprintf('{sfSpy} %s POST request (observer Id: %s)', $observer->getIsLive() ? 'Observing' : 'Recording', $observer->getId()));
          }
        }
      }
    }
  }
  
  protected function getPostList()
  {
    $output = array();
    foreach ($_POST as $key => $value)
    {
      $output []= $key . "=" . ($key == 'password' ? '******' : $value);
    }
    
    return '<li>' . implode('</li><li>', $output) . '</li>';
  }
}
