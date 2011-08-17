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


/**
 * Config
 */
$GLOBALS['TL_DCA']['tl_calendar_events']['config']['onload_callback'][] = array('tl_calendarlanguage_events', 'showSelectbox');


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['languageMain'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar_events']['languageMain'],
	'exclude'                 => false,
	'inputType'               => 'select',
	'options_callback'        => array('tl_calendarlanguage_events', 'getMasterCalendar'),
	'eval'					  => array('includeBlankOption'=>true, 'tl_class'=>'w50'),
);


class tl_calendarlanguage_events extends Backend 
{

	/**
	 * Get records from the master calendar
	 *
	 * @param	DataContainer
	 * @return	array
	 * @link	http://www.contao.org/callbacks.html#options_callback
	 */
	public function getMasterCalendar(DataContainer $dc)
	{
		$sameDay = $GLOBALS['TL_LANG']['tl_calendar_events']['sameDay'];
		$otherDay = $GLOBALS['TL_LANG']['tl_calendar_events']['otherDay'];
			
		$arrEvents = array($sameDay => array(), $otherDay => array());
		$objEvents = $this->Database->prepare("SELECT * FROM tl_calendar_events WHERE pid=(SELECT tl_calendar.master FROM tl_calendar LEFT OUTER JOIN tl_calendar_events ON tl_calendar_events.pid=tl_calendar.id WHERE tl_calendar_events.id={$dc->id} AND tl_calendar.master > 0) ORDER BY startTime DESC")->execute($objCalendar->master);
		
		if ($objEvents->numRows)
		{
			while( $objEvents->next() )
			{
				if ($objEvents->startDate == $dc->activeRecord->startDate)
				{
					$arrEvents[$sameDay][$objEvents->id] = $objEvents->title . ' (' . $this->parseDate($GLOBALS['TL_CONFIG']['datimFormat'], $objEvents->startTime) . ')';
				}
				else
				{
					$arrEvents[$otherDay][$objEvents->id] = $objEvents->title . ' (' . $this->parseDate($GLOBALS['TL_CONFIG']['datimFormat'], $objEvents->startTime) . ')';
				}
			}
		}

		return $arrEvents;
	}


	/**
	 * Show the select menu only on slave calendars
	 *
	 * @param	DataContainer
	 * @return	void
	 * @link	http://www.contao.org/callbacks.html#onload_callback
	 */
	public function showSelectbox(DataContainer $dc)
	{
		if($this->Input->get('act') == "edit")
		{
			$objCalendar = $this->Database->execute("SELECT tl_calendar.* FROM tl_calendar LEFT OUTER JOIN tl_calendar_events ON tl_calendar_events.pid=tl_calendar.id WHERE tl_calendar_events.id=".$dc->id);

			if ($objCalendar->numRows && $objCalendar->master > 0)
			{
				$GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['default'] = preg_replace('@([,|;])(alias[,|;])@','$1languageMain,$2', $GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['default']);
				$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['title']['eval']['tl_class'] = 'w50';
			}
		}
		else if($this->Input->get('act') == "editAll")
		{
			$GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['default'] = preg_replace('@([,|;]{1}alias)([,|;]{1})@','$1,languageMain$2', $GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['default']);
		}
	}
}

