DROP TABLE IF EXISTS `{PRFX}users`;
CREATE TABLE IF NOT EXISTS `{PRFX}users` (
	`id` int(11) NOT NULL auto_increment,
	`username` varchar(128) NOT NULL,
	`email` varchar(128) NOT NULL,
	`password` varchar(64) NOT NULL,
	`first_name` varchar(128) NULL,
	`last_name` varchar(128) NULL,
	`created` int(11) NOT NULL,
	`lang` VARCHAR(16),
	PRIMARY KEY  (`id`)
);

DROP TABLE IF EXISTS `{PRFX}objectmeta`;
CREATE TABLE IF NOT EXISTS `{PRFX}objectmeta` (
	`id` int(11) NOT NULL auto_increment,
	`obj_class` varchar(32) NOT NULL,
	`obj_id` int(11) NOT NULL,
	`meta_name` varchar(32) NOT NULL,
	`meta_value` TEXT,
	`meta_data` TEXT,
	PRIMARY KEY  (`id`)
);

DROP TABLE IF EXISTS `{PRFX}conf`;
CREATE TABLE IF NOT EXISTS `{PRFX}conf` (
	`id` int(11) NOT NULL auto_increment,
	`name` varchar(255) NOT NULL,
	`value` TEXT,
	PRIMARY KEY  (`id`)
);

DROP TABLE IF EXISTS `{PRFX}templates`;
CREATE TABLE IF NOT EXISTS `{PRFX}templates` (
	`id` int(11) NOT NULL auto_increment,
	`lang` varchar(16) NOT NULL,
	`template` varchar(255) NOT NULL,
	`subject` varchar(255) NOT NULL,
	`body` TEXT,
	PRIMARY KEY  (`id`)
);

DROP TABLE IF EXISTS `{PRFX}forms`;
CREATE TABLE IF NOT EXISTS `{PRFX}forms` (
	`id` int(11) NOT NULL auto_increment,

	`title` VARCHAR(128),
	`class` VARCHAR(32),
	`details` VARCHAR(128),

	PRIMARY KEY  (`id`)
	);
	
DROP TABLE IF EXISTS `{PRFX}form_controls`;
CREATE TABLE IF NOT EXISTS `{PRFX}form_controls` (
	`id` int(11) NOT NULL auto_increment,

	`form_id` int(11) NOT NULL,
	`name` VARCHAR(128),
	`type` VARCHAR(32),
	`title` VARCHAR(255),
	`description` TEXT,
	`show_order` int(11),

	`ext_access` VARCHAR(32),

	`attr` TEXT,
	`validators` TEXT,
	`default_value` TEXT,

	PRIMARY KEY  (`id`)
	);

DROP TABLE IF EXISTS `{PRFX}services`;
CREATE TABLE IF NOT EXISTS `{PRFX}services` (
	`id` int(11) NOT NULL auto_increment,

	`title` VARCHAR(255),
	`description` TEXT,

	`min_cancel` int(11) DEFAULT 86400,

	`allow_queue` TINYINT DEFAULT 0,
	`blocks_location` TINYINT DEFAULT 0,
	`blocks_resource` TINYINT DEFAULT 0,

	`duration` int(11) DEFAULT 1800,
	`duration_increment` int(11) DEFAULT 1800,
	`duration_max` int(11) DEFAULT 1800,

	`lead_in` int(11) DEFAULT 0,
	`lead_out` int(11) DEFAULT 0,
	`price` VARCHAR(16) DEFAULT '',
	`price_increment` VARCHAR(16) DEFAULT '',
	`prepay` VARCHAR(16) DEFAULT '',

	`recur_total` int(11) DEFAULT 1,
	`recur_options` VARCHAR(64) DEFAULT 'd-2d-w',

	`return_url` TEXT,

	`show_order` int(11) DEFAULT 1,
	PRIMARY KEY  (`id`)
	);

DROP TABLE IF EXISTS `{PRFX}service_cats`;
CREATE TABLE IF NOT EXISTS `{PRFX}service_cats` (
	`id` int(11) NOT NULL auto_increment,

	`title` VARCHAR(255),
	`description` TEXT,

	`show_order` int(11) DEFAULT 1,
	PRIMARY KEY  (`id`)
	);

DROP TABLE IF EXISTS `{PRFX}locations`;
CREATE TABLE IF NOT EXISTS `{PRFX}locations` (
	`id` int(11) NOT NULL auto_increment,

	`title` VARCHAR(255),
	`description` TEXT,
	`capacity` int(11) DEFAULT 0,
	`show_order` int(11) DEFAULT 1,
	`archive` TINYINT NOT NULL DEFAULT 0,

	PRIMARY KEY  (`id`)
	);

DROP TABLE IF EXISTS `{PRFX}resources`;
CREATE TABLE IF NOT EXISTS `{PRFX}resources` (
	`id` int(11) NOT NULL auto_increment,

	`title` VARCHAR(255),
	`description` TEXT,
	`show_order` int(11) DEFAULT 1,
	`archive` TINYINT NOT NULL DEFAULT 0,

	PRIMARY KEY  (`id`)
	);

DROP TABLE IF EXISTS `{PRFX}timeblocks`;
CREATE TABLE IF NOT EXISTS `{PRFX}timeblocks` (
	`id` int(11) NOT NULL auto_increment,

	`starts_at` int(11) NOT NULL,
	`ends_at` int(11) NOT NULL,
	`selectable_every` int(11) NOT NULL,

	`location_id` int(11) NOT NULL,
	`resource_id` int(11) NOT NULL,
	`service_id` int(11) NOT NULL,

	`valid_from` int(11) NOT NULL,
	`valid_to` int(11) NOT NULL,

	`min_from_now` int(11) NOT NULL DEFAULT 10800,
	`max_from_now` int(11) NOT NULL DEFAULT 4838400,

	`capacity` int(11) DEFAULT 1,

	`applied_on` int(11) NOT NULL,
	`week_applied_on` TINYINT DEFAULT 0,
	`group_id` int(11) NOT NULL,

	PRIMARY KEY  (`id`)
	);

DROP TABLE IF EXISTS `{PRFX}timeoffs`;
CREATE TABLE IF NOT EXISTS `{PRFX}timeoffs` (
	`id` int(11) NOT NULL auto_increment,

	`resource_id` int(11) NOT NULL,
	`location_id` int(11) NOT NULL,

	`starts_at` int(11) NOT NULL,
	`ends_at` int(11) NOT NULL,

	`description` TEXT,
	PRIMARY KEY  (`id`)
	);

DROP TABLE IF EXISTS `{PRFX}appointments`;
CREATE TABLE IF NOT EXISTS `{PRFX}appointments` (
	`id` int(11) NOT NULL auto_increment,

	`service_id` int(11) NOT NULL,
	`resource_id` int(11) NOT NULL,
	`customer_id` int(11) NOT NULL,
	`location_id` int(11) NOT NULL,
	`seats` int(11) NOT NULL DEFAULT 1,

	`created_at` int(11) NOT NULL,
	`starts_at` int(11) NOT NULL,
	`duration` int(11) NOT NULL,
	`lead_in` int(11) NOT NULL,
	`lead_out` int(11) NOT NULL,

	`approved` tinyint NOT NULL DEFAULT 0,
	`completed` tinyint NOT NULL DEFAULT 0,

	`auth_code` varchar(32) NOT NULL DEFAULT '',
	`group_ref` VARCHAR(32) NOT NULL DEFAULT '',
	`price` VARCHAR(16) DEFAULT '',
	`need_reminder` int(11) NOT NULL DEFAULT 0,

	PRIMARY KEY  (`id`)
	);

DROP TABLE IF EXISTS `{PRFX}orders`;
CREATE TABLE IF NOT EXISTS `{PRFX}orders` (
	`id` int(11) NOT NULL auto_increment,

	`created_at` int(11),
	`is_active` tinyint DEFAULT 1,

	`pack_id` int(11) NOT NULL,
	`customer_id` int(11) NOT NULL,

	PRIMARY KEY  (`id`)
	);

DROP TABLE IF EXISTS `{PRFX}invoices`;
CREATE TABLE IF NOT EXISTS `{PRFX}invoices` (
	`id` int(11) NOT NULL auto_increment,

	`refno` VARCHAR(16),
	`amount` DOUBLE,
	`currency` VARCHAR(3),
	`created_at` int(11),
	`due_at` int(11),
	`customer_id` int(11) DEFAULT 0,
	PRIMARY KEY  (`id`)
	);

DROP TABLE IF EXISTS `{PRFX}transactions`;
CREATE TABLE IF NOT EXISTS `{PRFX}transactions` (
	`id` int(11) NOT NULL auto_increment,

	`amount` DOUBLE,
	`created_at` int(11),

	`invoice_id` int(11) NOT NULL,
	`amount_net` DOUBLE,
	`pgateway` VARCHAR(32),
	`pgateway_ref` TEXT,
	`pgateway_response` TEXT,

	PRIMARY KEY  (`id`)
	);

DROP TABLE IF EXISTS `{PRFX}packs`;
CREATE TABLE IF NOT EXISTS `{PRFX}packs` (
	`id` int(11) NOT NULL auto_increment,

	`price` DOUBLE,
	`title` VARCHAR(255),
	`show_order` int(11) DEFAULT 1,
	`asset_id` int(11) DEFAULT 0,
	`asset_value` DOUBLE NOT NULL DEFAULT 0,
	`expires_in` varchar(32) NOT NULL DEFAULT '1 months',

	PRIMARY KEY  (`id`)
	);

CREATE TABLE IF NOT EXISTS `{PRFX}promotions` (
	`id` int(11) NOT NULL auto_increment,
	`title` VARCHAR(255),
	`rule` TEXT,
	`price` TEXT,
	PRIMARY KEY  (`id`)
	);

CREATE TABLE IF NOT EXISTS `{PRFX}coupons` (
	`id` int(11) NOT NULL auto_increment,
	`code` TEXT,
	`use_limit` INT,
	`promotion_id` INT,
	PRIMARY KEY  (`id`)
	);

CREATE TABLE IF NOT EXISTS `{PRFX}accounting_journal` (
	`id` int(11) NOT NULL auto_increment,

	`created_at` int(11) NOT NULL,

	`obj_class` VARCHAR(32) NOT NULL,
	`obj_id` int(11) NOT NULL,
	`action` VARCHAR(32) NOT NULL,
	PRIMARY KEY  (`id`)
	);

CREATE TABLE IF NOT EXISTS `{PRFX}accounting_posting` (
	`id` int(11) NOT NULL auto_increment,
	`journal_id` int(11) NOT NULL,
	`expires_at` int(11) NOT NULL,

	`account_type` VARCHAR(32) NOT NULL,
	`account_id` int(11) NOT NULL,
	`asset_id` int(11) NOT NULL,
	`asset_value` DOUBLE NOT NULL DEFAULT 0,
	PRIMARY KEY  (`id`)
	);

CREATE TABLE IF NOT EXISTS `{PRFX}accounting_assets` (
	`id` int(11) NOT NULL auto_increment,
	`asset` TEXT,
	PRIMARY KEY  (`id`)
	);

CREATE TABLE IF NOT EXISTS `{PRFX}invoice_items` (
	`id` int(11) NOT NULL auto_increment,
	`invoice_id` INT(11) NOT NULL,

	`amount` DECIMAL(11,2) NOT NULL DEFAULT 0,
	`qty` DECIMAL(7,2) NOT NULL DEFAULT 1,
	`taxable` int(1) NOT NULL DEFAULT 1,

	`title` TEXT DEFAULT '',
	`obj_class` varchar(32) NOT NULL DEFAULT '',
	`obj_id` int(11) NOT NULL DEFAULT 0,
	PRIMARY KEY  (`id`)
	);

CREATE TABLE IF NOT EXISTS `{PRFX}logaudit` (
	`id` int(11) NOT NULL auto_increment,
	`user_id` INT(11) NOT NULL,
	`action_time` INT(11) NOT NULL,

	`obj_class` varchar(32) NOT NULL DEFAULT '',
	`obj_id` int(11) NOT NULL DEFAULT 0,

	`property_name` varchar(32) NOT NULL DEFAULT '',
	`old_value` TEXT DEFAULT '',
	PRIMARY KEY  (`id`)
	);
