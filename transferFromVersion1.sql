--
-- Transfers data from Contao extension portal64.de_client from version 1 to 2
--
-- @package			Portal64.de Client
-- @author			Julian Knorr
-- @copytight		Copyright (C) 2017 Julian Knorr
-- @date			  2017
--
-- Execute this statements BEFORE executing the internal updates in contao.

-- table tl_portal64_team
ALTER TABLE `tl_portal64_team`
  CHANGE `staffel` `league` VARCHAR(255)
CHARACTER SET utf8
COLLATE utf8_general_ci NOT NULL DEFAULT '',
  CHANGE `lnr` `lotNumber` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  CHANGE `VKZ` `vkz` VARCHAR(255)
CHARACTER SET utf8
COLLATE utf8_general_ci NOT NULL DEFAULT '',
  CHANGE `mannschaftsfuehrer` `teamster` VARCHAR(255)
CHARACTER SET utf8
COLLATE utf8_general_ci NOT NULL DEFAULT '',
  CHANGE `duaration` `eventDuration` VARCHAR(12)
CHARACTER SET utf8
COLLATE utf8_general_ci NOT NULL DEFAULT '',
  CHANGE `homelocation` `homeLocation` VARCHAR(255)
CHARACTER SET utf8
COLLATE utf8_general_ci NOT NULL DEFAULT '',
  CHANGE `ersatzTeams` `substituteTeams` BLOB NULL DEFAULT NULL;

-- table tl_portal64_team_player
ALTER TABLE `tl_portal64_team_player`
  CHANGE `rang` `rank` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  CHANGE `mglnr` `memberNumber` INT(10) UNSIGNED NOT NULL DEFAULT '0';

-- table tl_portal64_team_round
ALTER TABLE `tl_portal64_team_round`
  CHANGE `heim` `isHome` CHAR(1)
CHARACTER SET utf8
COLLATE utf8_general_ci NOT NULL DEFAULT '',
  CHANGE `gegner` `opponent` VARCHAR(255)
CHARACTER SET utf8
COLLATE utf8_general_ci NOT NULL DEFAULT '',
  CHANGE `eventid` `eventId` INT(10) UNSIGNED NOT NULL DEFAULT '0';

-- table tl_portal64_team_round_match
ALTER TABLE `tl_portal64_team_round_match`
  CHANGE `rangheim` `homeRank` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  CHANGE `ranggast` `guestRank` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  CHANGE `nameGegner` `opponentName` VARCHAR(255)
CHARACTER SET utf8
COLLATE utf8_general_ci NOT NULL DEFAULT '',
  CHANGE `ergebnisHeim` `resultHome` VARCHAR(10)
CHARACTER SET utf8
COLLATE utf8_general_ci NOT NULL DEFAULT '',
  CHANGE `ergebnisGast` `resultGuest` VARCHAR(10)
CHARACTER SET utf8
COLLATE utf8_general_ci NOT NULL DEFAULT '';
UPDATE `tl_portal64_team_round_match`
SET `opponentName` = ''
WHERE `opponentName` = ', ';

-- table tl_calendar_events
ALTER TABLE `tl_calendar_events`
  ADD `createdByPortal64` CHAR(1) NOT NULL DEFAULT '';
UPDATE `tl_calendar_events`
SET createdByPortal64 = '1'
WHERE id IN (SELECT eventId
             FROM tl_portal64_team_round);

-- table tl_content
ALTER TABLE `tl_content`
  CHANGE `tryFIDE` `displayFIDEData` CHAR(1)
CHARACTER SET utf8
COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `tl_content`
  ADD `showGameResults` CHAR(1) NOT NULL DEFAULT '',
  ADD `showSingleRound` CHAR(1) NOT NULL DEFAULT ''
  AFTER `showGameResults`,
  ADD `showTeamster` CHAR(1) NOT NULL DEFAULT '1'
  AFTER `showSingleRound`,
  ADD `showTeamsterMail` CHAR(1) NOT NULL DEFAULT ''
  AFTER `showTeamster`;
UPDATE tl_content
SET `showGameResults` = '1'
WHERE (`type` = 'ceteam' AND `crossResults` = '1') OR (`type` = 'ceteamresults' AND `singleResults` = '1');
UPDATE tl_content
SET `showSingleRound` = '1'
WHERE (`type` = 'ceteamresults' AND `singleResults` = '1');
UPDATE tl_content
SET `showTeamsterMail` = '1', showTeamster = '1'
WHERE `type` = 'ceteam' AND `customTpl` = 'ce_team_mf';
UPDATE tl_content
SET `type` = 'team'
WHERE `type` = 'ceteam';
UPDATE tl_content
SET `type` = 'teamresults'
WHERE `type` = 'ceteamresults';
UPDATE tl_content
SET `customTpl` = ''
WHERE `type` = 'team' OR `type` = 'teamresults';