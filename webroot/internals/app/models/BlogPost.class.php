<?php
require_once('mochi/db/PersistentObject.class.php');
require_once('mochi/db/PersistentObjectRepository.class.php');
require_once('mochi/db/Database.class.php');
require_once('mochi/utils/StringUtils.class.php');
require_once('mochi/utils/Timestamp.class.php');

class BlogPost extends PersistentObject
{
	const TABLE_DEF = "
		create table %s (
			id integer unsigned not null auto_increment,
		
			title varchar(255) not null,
			content text,
			register_datetime datetime not null,
			update_datetime datetime not null,
			
			primary key(id)
		) TYPE = InnoDB;
		";

	protected $p_title;
	protected $p_content;
	protected $p_register_datetime;
	protected $p_update_datetime;
}

class BlogPostRepository extends PersistentObjectRepository
{
	function __construct(Database $database) {
		parent::__construct($database);
	}
	
	function getObjectClassName() {
		return "BlogPost";
	}
}
?>
