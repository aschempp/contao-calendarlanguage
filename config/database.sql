-- **********************************************************
-- *                                                        *
-- * IMPORTANT NOTE                                         *
-- *                                                        *
-- * Do not import this file manually but use the TYPOlight *
-- * install tool to create and maintain database tables!   *
-- *                                                        *
-- **********************************************************

-- 
-- Table `tl_calendar`
-- 

CREATE TABLE `tl_calendar` (
  `master` int(10) unsigned NOT NULL default '0',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


----------------------------------------------------------

-- 
-- Table `tl_calendar_events`
-- 

CREATE TABLE `tl_calendar_events` (
  `languageMain` int(10) unsigned NOT NULL default '0',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

