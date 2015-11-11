<?php

namespace UniversiteRennes2\Isou;

class Service{
	public $id;
	public $name;
	public $url;
	public $state;
	public $comment;
	public $enable;
	public $visible;
	public $locked;
	public $rsskey;
	public $idtype;
	public $idcategory;
	public $category;

	const TYPE_ISOU = '1';
	const TYPE_NAGIOS_STATUSDAT = '2';
	const TYPE_SHINKEN_THRUK = '3';

	public static $TYPES = array(self::TYPE_ISOU, self::TYPE_NAGIOS_STATUSDAT, self::TYPE_SHINKEN_THRUK);

	public function __construct(){
		if(isset($this->idservice)){
			// PDO instance
			$this->id = $this->idservice;
		}else{
			// manual instance
			$this->id = 0;
			$this->name = '';
			$this->url = NULL;
			$this->state = 0;
			$this->comment = NULL;
			$this->enable = 1;
			$this->visible = 1;
			$this->locked = 0;
			$this->rsskey = NULL;
			$this->idtype = self::TYPE_ISOU;
			$this->idcategory = NULL;
			$this->category = '';
		}

		$this->is_closed = ($this->state === State::CLOSE);
		$this->is_forced = ($this->locked === '1');

		$this->dependencies = NULL;
		$this->reverse_dependencies = NULL;
	}

	function check_data($categories=array()){
		$errors = array();

		$this->name = htmlentities($this->name, ENT_NOQUOTES, 'UTF-8');
		if($this->name === ''){
			$errors[] = 'Le nom du service ne peut pas être vide.';
		}

		if(!in_array($this->idtype, self::$TYPES)){
			$errors[] = 'Le type de service choisi est invalide.';
		}

		if(!in_array($this->visible, array(0, 1))){
			$errors[] = 'La visibilité choisi est invalide.';
		}

		if($this->idtype === self::TYPE_ISOU){
			if($this->url === ''){
				$this->url = NULL;
			}

			if($this->rsskey === NULL){
				global $DB;

				$sql = "SELECT rsskey FROM services WHERE rsskey IS NOT NULL ORDER BY rsskey DESC";
				$query = $DB->query($sql);
				if($key = $query->fetch(\PDO::FETCH_OBJ)){
					$this->rsskey = ++$key->rsskey;
				}else{
					$this->rsskey = 1;
					// $errors[] = 'La clé rss n\'a pu être générée.';
				}
			}

			if(!isset($categories[$this->idcategory])){
				$errors[] = 'La catégorie choisie est invalide.';
			}
		}else{
			$this->url = NULL;
			$this->enable = 0;
			$this->visible = 0;
			$this->rsskey = NULL;
			$this->idcategory = NULL;
		}

		return $errors;
	}

	function save(){
		global $DB;

		$results = array('successes' => array(), 'errors' => array());
		$params = array($this->name, $this->url, $this->state, $this->comment, $this->enable, $this->visible, $this->locked, $this->rsskey, $this->idtype, $this->idcategory);

		if($this->id === 0){
			$sql = "INSERT INTO services(name, url, state, comment, enable, visible, locked, rsskey, idtype, idcategory) VALUES(?,?,?,?,?,?,?,?,?,?)";
		}else{
			$sql = "UPDATE services SET name=?, url=?, state=?, comment=?, enable=?, visible=?, locked=?, rsskey=?, idtype=?, idcategory=? WHERE idservice=?";
			$params[] = $this->id;
		}
		$query = $DB->prepare($sql);

		if($query->execute($params)){
			if($this->id === 0){
				$this->id = $DB->lastInsertId();
			}
			$results['successes'] = array('Les données ont été correctement enregistrées.');
		}else{
			// log db errors
			$sql_error = $query->errorInfo();
			file_put_contents(LOG_FILE, "[".strftime('%Y-%m-%d %H:%M', TIME)."] ".implode(', ', $sql_error)."\n", FILE_APPEND);

			$results['errors'] = array('Une erreur est survenue lors de l\'enregistrement des données.');
		}

		return $results;
	}

	function delete(){
		global $DB;

		$results = array('successes' => array(), 'errors' => array());
		$commit = 1;

		$previous_transaction = $DB->inTransaction();
		if(!$previous_transaction){
			$DB->beginTransaction();
		}

		// suppression des évènements, des dépendances et du service
		$queries = array();
		$queries[] = "DELETE FROM events WHERE idservice=?";
		$queries[] = "DELETE FROM dependencies_groups_content WHERE idservice=?";
		$queries[] = "DELETE FROM dependencies_groups WHERE idservice=?";
		$queries[] = "DELETE FROM services WHERE idservice=?";
		foreach($queries as $sql){
			$query = $DB->prepare($sql);
			$commit &= $query->execute(array($this->id));
		}

		// suppression des groupes sans contenu
		$sql = "DELETE FROM dependencies_groups WHERE idgroup NOT IN (SELECT DISTINCT idgroup FROM dependencies_groups_content)";
		$query = $DB->prepare($sql);
		$commit &= $query->execute();

		if($commit === 1){
			if(!$previous_transaction){
				$DB->commit();
			}
			$results['successes'] = array('Les données ont été correctement supprimées.');
		}else{
			// log db errors
			$sql_error = $query->errorInfo();
			file_put_contents(LOG_FILE, "[".strftime('%Y-%m-%d %H:%M', TIME)."] ".implode(', ', $sql_error)."\n", FILE_APPEND);

			if(!$previous_transaction){
				$DB->rollBack();
			}
			$results['errors'] = array('Une erreur est survenue lors de la suppression des données.');
		}

		return $results;
	}

	public function hide(){
		global $DB;

		$sql = "UPDATE services SET visible=0 WHERE idservice=?";
		$query = $DB->prepare($sql);
		if($query->execute(array($this->id))){
			$this->visible = '0';
			return TRUE;
		}else{
			file_put_contents(LOG_FILE, "[".strftime('%Y-%m-%d %H:%M', TIME)."] Error sql: ".__METHOD__."() in ".__FILE__." file\n\t".implode(', ', $sql_error)."\n", FILE_APPEND);
			return FALSE;
		}
	}

	public function visible(){
		global $DB;

		$sql = "UPDATE services SET visible=1 WHERE idservice=?";
		$query = $DB->prepare($sql);
		if($query->execute(array($this->id))){
			$this->visible = '1';
			return TRUE;
		}else{
			file_put_contents(LOG_FILE, "[".strftime('%Y-%m-%d %H:%M', TIME)."] Error sql: ".__METHOD__."() in ".__FILE__." file\n\t".implode(', ', $sql_error)."\n", FILE_APPEND);
			return FALSE;
		}
	}

	public function lock($state){
		global $DB;

		$sql = "UPDATE services SET state=?, locked=1 WHERE idservice = ?";
		$query = $DB->prepare($sql);
		if($query->execute(array($state, $this->id))){
			$this->state = $state;
			$this->locked = '1';
			return TRUE;
		}else{
			file_put_contents(LOG_FILE, "[".strftime('%Y-%m-%d %H:%M', TIME)."] Error sql: ".__METHOD__."() in ".__FILE__." file\n\t".implode(', ', $sql_error)."\n", FILE_APPEND);
			return FALSE;
		}
	}

	public function unlock(){
		global $DB;

		$sql = "UPDATE services SET locked=0 WHERE idservice = ?";
		$query = $DB->prepare($sql);
		if($query->execute(array($this->id))){
			$this->locked = '0';
			return TRUE;
		}else{
			file_put_contents(LOG_FILE, "[".strftime('%Y-%m-%d %H:%M', TIME)."] Error sql: ".__METHOD__."() in ".__FILE__." file\n\t".implode(', ', $sql_error)."\n", FILE_APPEND);
			return FALSE;
		}

	}

	public function get_dependencies(){
		if($this->dependencies === NULL){
			require_once PRIVATE_PATH.'/libs/dependencies.php';

			$this->dependencies = get_service_dependency_groups($this->id);
		}

		return $this->dependencies;
	}

	public function get_reverse_dependencies(){
		if($this->reverse_dependencies === NULL){
			require_once PRIVATE_PATH.'/libs/dependencies.php';

			$this->reverse_dependencies = get_service_reverse_dependency_groups($this->id);
		}

		return $this->reverse_dependencies;
	}

	public function __toString(){
		return $this->name.' (id: '.$this->id.')';
	}

	public function __destruct() {
		// object destructed
	}
}

?>
