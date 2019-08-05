# Create Table

CREATE TABLE `app_webhooks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL DEFAULT '2',
  `name` varchar(30) DEFAULT NULL COMMENT 'The name of this web hook, UX purposes only, used for identification',
  `url` varchar(250) DEFAULT NULL COMMENT 'The URL of the web hook - you must include the protocol, domain and path.',
  `type` varchar(30) DEFAULT 'application/json' COMMENT 'The content type of the outgoing connection',
  `events` varchar(250) DEFAULT NULL COMMENT 'One or more event that will trigger this web hook',
  `collections` varchar(250) DEFAULT NULL COMMENT 'CSV of collections that are affected by this web hook - ignore if all collections are affected',
  `secret` varchar(100) DEFAULT NULL COMMENT 'A hash string to send with the payload to validate request is from Directus',
  `created_on` datetime DEFAULT NULL COMMENT 'The date and time this web hook was created.',
  `created_by` int(11) DEFAULT NULL COMMENT 'The authenticated user that created the web hook',
  `modified_on` datetime DEFAULT NULL COMMENT 'The date and time this web hook was created',
  `modified_by` int(11) DEFAULT NULL COMMENT 'The authenticated user that modified this request.',
  `method` varchar(10) DEFAULT 'POST' COMMENT 'The method to use when sending request to webhook URL',
  `username` varchar(50) DEFAULT NULL COMMENT 'Username to use for authentication if required by your external application',
  `password` varchar(100) DEFAULT NULL COMMENT 'Password to use for authentication if required by your external application',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Update Directus Columns

INSERT INTO `directus_columns` (`table_name`, `column_name`, `data_type`, `ui`, `relationship_type`, `related_table`, `junction_table`, `junction_key_left`, `junction_key_right`, `hidden_input`, `required`, `sort`, `comment`, `options`)
VALUES
	('app_webhooks', 'id', 'INT', 'primary_key', NULL, NULL, NULL, NULL, NULL, 1, 0, 0, NULL, NULL),
	('app_webhooks', 'status', 'INT', 'status', NULL, NULL, NULL, NULL, NULL, 1, 0, 1, NULL, NULL),
	('app_webhooks', 'name', 'VARCHAR', 'text_input', NULL, NULL, NULL, NULL, NULL, 0, 1, 2, 'The name of this web hook, UX purposes only, used for identification', NULL),
	('app_webhooks', 'method', 'VARCHAR', 'dropdown', NULL, NULL, NULL, NULL, NULL, 0, 1, 3, 'The method to use when sending request to webhook URL', '{\"id\":\"dropdown\",\"options\":\"{\\n   \\\"POST\\\": \\\"POST\\\",\\n   \\\"GET\\\": \\\"GET\\\"\\n}\"}'),
	('app_webhooks', 'url', 'VARCHAR', 'text_input', NULL, NULL, NULL, NULL, NULL, 0, 1, 4, 'The URL of the web hook - you must include the protocol, domain and path.', NULL),
	('app_webhooks', 'type', 'VARCHAR', 'dropdown', NULL, NULL, NULL, NULL, NULL, 0, 1, 5, 'The content type of the outgoing connection - defaults to JSON', '{\"id\":\"dropdown\",\"options\":\"{\\n   \\\"application/json\\\": \\\"JSON\\\",\\n   \\\"text/plain\\\": \\\"PLAIN\\\",\\n   \\\"application/xml\\\": \\\"XML\\\",\\n   \\\"application/x-www-form-urlencoded\\\": \\\"URL ENCODED\\\"\\n}\"}'),
	('app_webhooks', 'events', 'VARCHAR', 'dropdown_multiselect', NULL, NULL, NULL, NULL, NULL, 0, 1, 6, 'One or more event that will trigger this web hook', '{\"id\":\"dropdown_multiselect\",\"options\":\"{\\n   \\\"table.create:before\\\": \\\"table.create:before\\\",\\n   \\\"table.create:after\\\": \\\"table.create:after\\\",\\n   \\\"table.drop:before\\\": \\\"table.drop:before\\\",\\n   \\\"table.drop:after\\\": \\\"table.drop:after\\\",\\n   \\\"table.insert:before\\\": \\\"table.insert:before\\\",\\n   \\\"table.insert:after\\\": \\\"table.insert:after\\\",\\n   \\\"table.update:before\\\": \\\"table.update:before\\\",\\n   \\\"table.update:after\\\": \\\"table.update:after\\\",\\n   \\\"table.delete:before\\\": \\\"table.delete:before\\\",\\n   \\\"table.delete:after\\\": \\\"table.delete:after\\\",\\n   \\\"files.saving\\\": \\\"files.saving\\\",\\n   \\\"files.saving:after\\\": \\\"files.saving:after\\\",\\n   \\\"files.thumbnail.saving\\\": \\\"files.thumbnail.saving\\\",\\n   \\\"files.thumbnail.saving:after\\\": \\\"files.thumbnail.saving:after\\\",\\n   \\\"files.deleting\\\": \\\"files.deleting\\\",\\n   \\\"files.deleting:after\\\": \\\"files.deleting:after\\\",\\n   \\\"files.thumbnail.deleting\\\": \\\"files.thumbnail.deleting\\\",\\n   \\\"files.thumbnail.deleting:after\\\": \\\"files.thumbnail.deleting:after\\\"\\n}\"}'),
	('app_webhooks', 'collections', 'VARCHAR', 'tags', NULL, NULL, NULL, NULL, NULL, 0, 0, 7, 'CSV of collections that are affected by this web hook - ignore if all collections are affected', NULL),
	('app_webhooks', 'secret', 'VARCHAR', 'text_input', NULL, NULL, NULL, NULL, NULL, 0, 0, 8, 'A hash string to send with the payload to validate request is from Directus', NULL),
	('app_webhooks', 'username', 'VARCHAR', 'text_input', NULL, NULL, NULL, NULL, NULL, 0, 0, 9, 'Username to use for authentication if required by your external application', NULL),
	('app_webhooks', 'password', 'VARCHAR', 'password', NULL, NULL, NULL, NULL, NULL, 0, 0, 10, 'Password to use for authentication if required by your external application', NULL),
	('app_webhooks', 'created_on', 'DATETIME', 'date_created', NULL, NULL, NULL, NULL, NULL, 0, 0, 11, 'The date and time this web hook was created.', NULL),
	('app_webhooks', 'created_by', 'INT', 'user_created', NULL, NULL, NULL, NULL, NULL, 0, 0, 12, 'The authenticated user that created the web hook', NULL),
	('app_webhooks', 'modified_on', 'DATETIME', 'date_modified', NULL, NULL, NULL, NULL, NULL, 0, 0, 13, 'The date and time this web hook was created', NULL),
	('app_webhooks', 'modified_by', 'INT', 'user_modified', NULL, NULL, NULL, NULL, NULL, 0, 0, 14, 'The authenticated user that modified this request.', NULL);
