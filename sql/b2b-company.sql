SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE IF NOT EXISTS `silk_company` (
  `id` int(11) NOT NULL,
  `company_id` varchar(50) DEFAULT NULL,
  `name` varchar(64) DEFAULT NULL,
  `payment_methods` varchar(255) DEFAULT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `silk_company_company` (
  `entity_id` int(11) NOT NULL COMMENT 'Company ID',
  `name` varchar(255) NOT NULL COMMENT 'Name',
  `zip` varchar(255) NOT NULL COMMENT 'Zip',
  `address` varchar(255) NOT NULL COMMENT 'Company Address',
  `address2` varchar(255) NOT NULL COMMENT 'Company Address2',
  `street` varchar(255) NOT NULL COMMENT 'Street',
  `city` varchar(255) NOT NULL COMMENT 'City',
  `state` varchar(255) NOT NULL COMMENT 'State/Province',
  `country` varchar(255) NOT NULL COMMENT 'Country',
  `phone` varchar(255) NOT NULL COMMENT 'Phone',
  `fax` varchar(255) NOT NULL COMMENT 'Fax',
  `email` varchar(255) NOT NULL COMMENT 'Email',
  `payment_term` int(11) NOT NULL COMMENT 'Payment Term',
  `group` int(11) NOT NULL COMMENT 'Company Group',
  `homepage` varchar(255) NOT NULL COMMENT 'Home Page',
  `about` text NOT NULL COMMENT 'About',
  `remark` text NOT NULL COMMENT 'Remark',
  `status` smallint(6) DEFAULT NULL COMMENT 'Enabled',
  `url_key` varchar(255) DEFAULT NULL COMMENT 'URL key',
  `in_rss` smallint(6) DEFAULT NULL COMMENT 'In RSS',
  `meta_title` varchar(255) DEFAULT NULL COMMENT 'Meta title',
  `meta_keywords` text COMMENT 'Meta keywords',
  `meta_description` text COMMENT 'Meta description',
  `allow_comment` int(11) DEFAULT NULL COMMENT 'Allow Comment',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT 'Company Modification Time',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Company Creation Time'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Company Table';

CREATE TABLE IF NOT EXISTS `silk_company_company_comment` (
  `comment_id` int(11) NOT NULL COMMENT 'Company Comment ID',
  `company_id` int(11) NOT NULL COMMENT 'Company ID',
  `title` varchar(255) NOT NULL COMMENT 'Comment Title',
  `comment` text NOT NULL COMMENT 'Comment',
  `status` smallint(6) NOT NULL COMMENT 'Comment status',
  `customer_id` int(10) unsigned DEFAULT NULL COMMENT 'Customer id',
  `name` varchar(255) NOT NULL COMMENT 'Customer name',
  `email` varchar(255) NOT NULL COMMENT 'Customer email',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT 'Company Comment Modification Time',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Company Comment Creation Time'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Company Comments Table';

CREATE TABLE IF NOT EXISTS `silk_company_company_comment_store` (
  `comment_id` int(11) NOT NULL COMMENT 'Comment ID',
  `store_id` smallint(5) unsigned NOT NULL COMMENT 'Store ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Companys Comments To Store Linkage Table';

CREATE TABLE IF NOT EXISTS `silk_company_company_store` (
  `company_id` int(11) NOT NULL COMMENT 'Company ID',
  `store_id` smallint(5) unsigned NOT NULL COMMENT 'Store ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Companys To Store Linkage Table';


ALTER TABLE `silk_company`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `silk_company_company`
  ADD PRIMARY KEY (`entity_id`);

ALTER TABLE `silk_company_company_comment`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `FK_04A6686C60775D7151BAF0969C663841` (`company_id`),
  ADD KEY `FK_SILK_COMPANY_COMPANY_COMMENT_CSTR_ID_CSTR_ENTT_ENTT_ID` (`customer_id`);

ALTER TABLE `silk_company_company_comment_store`
  ADD PRIMARY KEY (`comment_id`,`store_id`),
  ADD KEY `IDX_SILK_COMPANY_COMPANY_COMMENT_STORE_STORE_ID` (`store_id`);

ALTER TABLE `silk_company_company_store`
  ADD PRIMARY KEY (`company_id`,`store_id`),
  ADD KEY `IDX_SILK_COMPANY_COMPANY_STORE_STORE_ID` (`store_id`);


ALTER TABLE `silk_company`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `silk_company_company`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Company ID';
ALTER TABLE `silk_company_company_comment`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Company Comment ID';

ALTER TABLE `silk_company_company_comment`
  ADD CONSTRAINT `FK_04A6686C60775D7151BAF0969C663841` FOREIGN KEY (`company_id`) REFERENCES `silk_company_company` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_SILK_COMPANY_COMPANY_COMMENT_CSTR_ID_CSTR_ENTT_ENTT_ID` FOREIGN KEY (`customer_id`) REFERENCES `customer_entity` (`entity_id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `silk_company_company_comment_store`
  ADD CONSTRAINT `FK_26EE9353361F54901508D6A77DC9112F` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_4F6C5E564BEBCE20F4598F356C860304` FOREIGN KEY (`comment_id`) REFERENCES `silk_company_company_comment` (`comment_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `silk_company_company_store`
  ADD CONSTRAINT `FK_EC8D5C2F8F273BDD1049C6F6030EB8E8` FOREIGN KEY (`company_id`) REFERENCES `silk_company_company` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_SILK_COMPANY_COMPANY_STORE_STORE_ID_CORE_STORE_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
