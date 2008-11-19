<?php

/*
 * This file is part of the sfSpyPlugin package.
 * (c) 2007-2008 Francois Zaninotto <francois.zaninotto@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Subclass for performing query and update operations on the 'sf_spy_event' table.
 *
 * 
 *
 * @package plugins.sfSpyPlugin.lib.model
 */ 
class sfSpyEventPeer extends BasesfSpyEventPeer
{
  const PAGE_TYPE   = 'page';
  const RELOAD_TYPE = 'reload';
  const POST_TYPE   = 'post';
}
