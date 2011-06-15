<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2011 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Andreas Schempp 2011
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 * @version    $Id$
 */


class CalendarLanguage extends Frontend
{
	
	public function translateUrlParameters($arrGet, $strLanguage, $arrRootPage)
	{
		$strEvent = $this->Input->get('events');
		
		// Switch news item language
        if ($strEvent != '')
        {
        	$objCalendars = $this->Database->prepare("SELECT tl_calendar_events.*, tl_calendar.master FROM tl_calendar_events LEFT OUTER JOIN tl_calendar ON tl_calendar_events.pid=tl_calendar.id WHERE tl_calendar_events.id=? OR tl_calendar_events.alias=?")
        							  ->limit(1)
        							  ->execute((int)$strEvent, $strEvent);
        	// We found a calendar item!!
        	if ($objCalendars->numRows)
        	{
        		$id = ($objCalendars->master > 0) ? $objCalendars->languageMain : $objCalendars->id;
        		$objEvent = $this->Database->prepare("SELECT tl_calendar_events.id, tl_calendar_events.alias FROM tl_calendar_events LEFT OUTER JOIN tl_calendar ON tl_calendar_events.pid=tl_calendar.id WHERE tl_calendar.language=? AND (tl_calendar_events.id=? OR languageMain=?)")->execute($strLanguage, $id, $id);

				if ($objEvent->numRows)
				{
					$arrGet['url']['events'] = $objEvent->alias ? $objEvent->alias : $objEvent->id;
				}
        	}
        }
        
		return $arrGet;
	}
}

