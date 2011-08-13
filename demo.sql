
--
-- Table structure for table `badge`
--

CREATE TABLE IF NOT EXISTS `badge` (
  `badge_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `label` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`badge_id`),
  UNIQUE KEY `label` (`label`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

-- --------------------------------------------------------

--
-- Table structure for table `content`
--

CREATE TABLE IF NOT EXISTS `content` (
  `content_id` int(11) NOT NULL AUTO_INCREMENT,
  `creator` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `label` varchar(250) NOT NULL,
  `content` text,
  `status` int(11) NOT NULL DEFAULT '1',
  `image` varchar(250) NOT NULL,
  PRIMARY KEY (`content_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=41859 ;

-- --------------------------------------------------------

--
-- Table structure for table `content_caption`
--

CREATE TABLE IF NOT EXISTS `content_caption` (
  `content_id` int(11) NOT NULL,
  PRIMARY KEY (`content_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `content_joke`
--

CREATE TABLE IF NOT EXISTS `content_joke` (
  `content_id` int(11) NOT NULL,
  PRIMARY KEY (`content_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `content_video`
--

CREATE TABLE IF NOT EXISTS `content_video` (
  `content_id` int(11) NOT NULL,
  `foreign_type` int(11) NOT NULL,
  `foreign_id` int(11) NOT NULL,
  PRIMARY KEY (`content_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `content__badge`
--

CREATE TABLE IF NOT EXISTS `content__badge` (
  `content_id` int(11) NOT NULL,
  `badge_id` int(11) NOT NULL,
  `num` int(11) NOT NULL,
  PRIMARY KEY (`content_id`,`badge_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `content__comment`
--

CREATE TABLE IF NOT EXISTS `content__comment` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `creator` int(11) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `content` text NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `video_id` int(11) NOT NULL,
  `submitted_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13934 ;

-- --------------------------------------------------------

--
-- Table structure for table `content__rating`
--

CREATE TABLE IF NOT EXISTS `content__rating` (
  `content_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  `timestampe` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`content_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `content__stats`
--

CREATE TABLE IF NOT EXISTS `content__stats` (
  `content_id` int(11) NOT NULL,
  `label` varchar(100) NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`content_id`,`label`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `content__tag`
--

CREATE TABLE IF NOT EXISTS `content__tag` (
  `content_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`content_id`,`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `content__taxonomy`
--

CREATE TABLE IF NOT EXISTS `content__taxonomy` (
  `content_id` int(11) NOT NULL,
  `taxonomy_id` int(11) NOT NULL,
  PRIMARY KEY (`content_id`,`taxonomy_id`),
  KEY `fk_content_tag_content1` (`content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `image`
--

CREATE TABLE IF NOT EXISTS `image` (
  `image_id` int(11) NOT NULL AUTO_INCREMENT,
  `creator` int(11) NOT NULL,
  PRIMARY KEY (`image_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tag`
--

CREATE TABLE IF NOT EXISTS `tag` (
  `tag_id` int(11) NOT NULL AUTO_INCREMENT,
  `creator` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `label` varchar(50) NOT NULL,
  `group` varchar(50) NOT NULL,
  `description` varchar(200) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `num_content` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`tag_id`),
  KEY `group` (`group`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1181 ;

-- --------------------------------------------------------

--
-- Table structure for table `tag__stats`
--

CREATE TABLE IF NOT EXISTS `tag__stats` (
  `tag_id` int(11) NOT NULL,
  `label` varchar(100) NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`tag_id`,`label`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `taxonomy`
--

CREATE TABLE IF NOT EXISTS `taxonomy` (
  `taxonomy_id` int(11) NOT NULL,
  `parent` int(11) NOT NULL,
  `label` varchar(200) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `image` varchar(250) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`taxonomy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(100) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `password` varchar(200) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `nickname` varchar(100) DEFAULT NULL,
  `label` varchar(100) NOT NULL,
  `quote` varchar(250) NOT NULL DEFAULT '',
  `bio` text NOT NULL,
  `image` varchar(250) NOT NULL DEFAULT '',
  `facebook_id` varchar(100) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9627 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_facebook`
--

CREATE TABLE IF NOT EXISTS `user_facebook` (
  `user_id` int(11) NOT NULL,
  `facebook_id` int(11) NOT NULL,
  `object` text NOT NULL,
  PRIMARY KEY (`user_id`,`facebook_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user__comment`
--

CREATE TABLE IF NOT EXISTS `user__comment` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `creator` int(11) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `content` text NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `video_id` int(11) NOT NULL,
  `submitted_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user__favorite`
--

CREATE TABLE IF NOT EXISTS `user__favorite` (
  `user_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

