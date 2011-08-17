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
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_calendar']['palettes']['default'] = str_replace('title,', 'title,language,master,', $GLOBALS['TL_DCA']['tl_calendar']['palettes']['default']);
$GLOBALS['TL_DCA']['tl_calendar']['subpalettes']['makeFeed'] = str_replace(',language,', ',', $GLOBALS['TL_DCA']['tl_calendar']['subpalettes']['makeFeed']);
$GLOBALS['TL_DCA']['tl_calendar']['fields']['format']['eval']['tl_class'] = 'clr';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_calendar']['fields']['master'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_calendar']['master'],
	'inputType'			=> 'select',
	'options_callback'	=> array('tl_calendarlanguage', 'getCalendars'),
	'eval'				=> array('includeBlankOption'=>true, 'blankOptionLabel'=>&$GLOBALS['TL_LANG']['tl_calendar']['isMaster']),
);


class tl_calendarlanguage extends Backend
{

	/**
	 * Get an array of possible calendars
	 *
	 * @param	DataContainer
	 * @return	array
	 * @link	http://www.contao.org/callbacks.html#options_callback
	 */
	public function getCalendars(DataContainer $dc)
	{
		$arrCalendars = array();
		$objCalendars = $this->Database->prepare("SELECT * FROM tl_calendar WHERE language!=? AND id!=? AND master=0 ORDER BY title")->execute($dc->activeRecord->language, $dc->id);
		
		while($objCalendars->next())
		{
			$arrCalendars[$objCalendars->id] = sprintf($GLOBALS['TL_LANG']['tl_calendar']['isSlave'], $objCalendars->title);
		}
		
		return $arrCalendars;
	}
}

