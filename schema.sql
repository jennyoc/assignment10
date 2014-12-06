CREATE TABLE IF NOT EXISTS `tblDogs` (
  `pmkDogId` int(11) NOT NULL AUTO_INCREMENT,
  `fnkShelterId` int(11) NOT NULL DEFAULT '0',
  `fldDogName` varchar(100) NOT NULL DEFAULT '0',
  `fldBreed` varchar(10000) DEFAULT NULL,
  `fldSize` varchar(15) DEFAULT NULL,
  `fldSizeId` int(4) DEFAULT NULL,
  `fldAge` varchar(11) DEFAULT NULL,
  `fldStage` varchar(10) DEFAULT NULL,
  `fldCoat` varchar(20) DEFAULT NULL,
  `fldGender` varchar(20) DEFAULT NULL,
  `fldGenderId` int(4) DEFAULT NULL,
  `fldChildren` varchar(20) DEFAULT NULL,
  `fldChildrenId` int(4) DEFAULT NULL,
  PRIMARY KEY (`pmkDogId`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tblUsers` (
  `pmkUserId` int(11) NOT NULL AUTO_INCREMENT,
  `fldFirstName` varchar(100) NOT NULL DEFAULT '0',
  `fldLastName` varchar(100) NOT NULL DEFAULT '0',
  `fldEmail` varchar(20) DEFAULT NULL,
  `fldDateJoined`timestamp DEFAULT CURRENT_TIMESTAMP,
  `fldConfirmed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pmkUserId`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tblShelters` (
  `pmkShelterId` int(255) NOT NULL AUTO_INCREMENT,
  `fldShelterName` varchar(10000) NOT NULL DEFAULT '0',
  `fldAddress` varchar(1000) NOT NULL DEFAULT '0',
  `fldCity` varchar(20) NOT NULL DEFAULT '0',
  `fldZip`varchar(10) NOT NULL DEFAULT '0',
  `fldPhone` varchar(15) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pmkShelterId`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			