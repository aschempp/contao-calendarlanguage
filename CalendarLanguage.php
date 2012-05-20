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
	
	/**
	 * Translate the URL parameters using the changelanguage module hook
	 *
	 * @param	array
	 * @param	string
	 * @param	array
	 * @return	array
	 * @see		ModuleChangeLanguage::compile()
	 */
	public function translateUrlParameters($arrGet, $strLanguage, $arrRootPage)
	{
        if (!(isset($_GET['events']) || ($GLOBALS['TL_CONFIG']['useAutoItem'] && isset($_GET['auto_item']))))
        {
            return $arrGet;
        }

		global $objPage;
		
		// try to find the page(s) wich holds eventreader or eventlist modules as content elements
		$objPageId = $this->Database->prepare('
			SELECT page.id
			FROM tl_content content
			LEFT JOIN tl_article article ON article.id=content.pid
			LEFT JOIN tl_page page ON page.id=article.pid
			WHERE content.type="module"
			AND (content.module IN (SELECT id FROM tl_module WHERE type=?) OR content.module IN (SELECT id FROM tl_module WHERE type=? AND cal_readerModule > 0))
			AND content.invisible<>1
			AND article.published=1
			AND page.published=1')
				->execute('eventreader', 'eventlist');

		if ($objPageId->numRows == 0)
		{
			// if nothing is found and the page has it's own layout then we will try to find modules in layout
			if ($objPage->includeLayout)
			{
				// get modules id
				$objModules = $this->Database->prepare('SELECT id FROM tl_module WHERE type=? OR type=?')->execute('eventreader', 'eventlist');

				$arrLike = array();

				while ($objModules->next())
				{
					$arrLike[] = 'modules LIKE \'%"' . $objModules->id . '"%\'';
				}

				// try to find modules in layout
				$objLayout = $this->Database->prepare('SELECT * FROM tl_layout WHERE id=? AND (' . implode(' OR ', $arrLike) . ')')
						->limit(1)
						->execute($objPage->layout);

				// if nothing is found then do nothing
				if ($objLayout->numRows == 0)
				{
					return $arrGet;
				}
			}
			else
			{
				return $arrGet;
			}
		}
		else
		{
			// if we found pages
			$arrPageId = array();

			while ($objPageId->next())
			{
				$arrPageId[] = $objPageId->id;
			}

			// if current page id is not in array of page ids then do nothing
			if (!in_array($objPage->id, $arrPageId)) return $arrGet;
		}

        // Set the item from the auto_item parameter
		$strEvent = $this->Input->get($GLOBALS['TL_CONFIG']['useAutoItem'] && isset($_GET['auto_item']) ? 'auto_item' : 'events');

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
        
        if ($this->Input->get('day') != '')
		{
			$arrGet['get']['day'] = $this->Input->get('day');
		}
		elseif ($this->Input->get('month') != '')
		{
			$arrGet['get']['month'] = $this->Input->get('month');
		}
		elseif ($this->Input->get('year') != '')
		{
			$arrGet['get']['year'] = $this->Input->get('year');
		}
        
		return $arrGet;
	}
}

