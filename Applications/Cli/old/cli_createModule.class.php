<?php

namespace Cli;

class cli_createModule extends \Cli\Cli {

	private $tableName, $fileNameOld, $content, $db, $table, $id, $className_ucfirst, $date;

	public $className, $fileName;

	private $type = array(
			'int' => '(int)',
			'float' => '(float)',
			'double' => '(float)',
			'dec' => '(float)',
			'bool' => '(bool)',
			'char' => '(string)',
			'blob' => '(string)',
			'text' => '(string)',
			'enum' => '(string)',
			'date' => '(string)',
			'datetime' => '(string)',
			'time' => '(string)',
			'timestamp' => '(string)'
	);

	private $typeFunction = array(
			'(int)' => ' || !is_int($%s)',
			'(float)' => ' || !is_float($%s)',
			'(bool)' => ' || !is_bool($%s)',
			'(string)' => ' || !is_string($%s)'
	);

	public function __construct() {
		parent::__construct();
	}

	public function getOptions():bool {
		if ($this->cmd != 'createmodule')
			return false;

		switch ($this->arg[2]) {
			case "help":
				$this->getHelp();
				break;
			case "version":
				$this->getVersion();
				break;
		}

		if (count($this->arg) < 4) {
			$this->getHelp();
		}

		$this->className = $this->arg[2];
		$this->tableName = $this->arg[3];
		if (empty($this->className) || !is_string($this->className)) {
			die('Error: className is needed');
		}
		if (empty($this->tableName) || !is_string($this->tableName)) {
			die('Error: tableName is needed');
		}
		return true;
	}

	public function execute() {
		$this->className_ucfirst = ucfirst($this->className);
		$this->date = date(DATE_RFC2822);
		$this->table = array();
		$this->content = '';
		echo "\nReceived parameters before creating the module:\n";
		echo "\tclassName : " . $this->className . "\n";
		echo "\ttableName : " . $this->tableName . "\n";
		if ($this->getLine('Do you want to continue? (y/n) : ', $this->YesNo, 'y') == 'n')
			exit();

		$this->getTable();
		$this->getContentClass();
		$this->fileName = sprintf("%s.class.php", ucfirst($this->className));
		$this->write($this->fileName, $this->content);
		$this->getContentManager();
		$this->fileName = sprintf("%sManager.class.php", ucfirst($this->className));
		$this->write($this->fileName, $this->content);
		$this->getContentManager_PDO();
		$this->fileName = sprintf("%sManager_PDO.class.php", ucfirst($this->className));
		$this->write($this->fileName, $this->content);
		echo "\nProcess is complete\n";
	}

	public function getHelp() {
		echo <<<EOT

USAGE
  php arkium.php className tableName

DESCRIPTION
  This command allows you to automatically generate
  new controllers, views and data models for the tables
  in your database.

  It is recommended that you run this command
  in the "CLI" directory.

PARAMETERS
  className : Name of the class to create
  TableName : Name of the table to be converted into class
EOT;
		exit();
	}

	public function getVersion() {
		echo <<<EOT

AK-Cli v1.0 (based on AK Framework)
Please type 'help' for help. Type 'exit' to quit.
EOT;
		exit();
	}

	private function describeTable() {
		$query = sprintf('DESCRIBE `%s` ;', $this->tableName);
		try {
			$result = $this->db->query($query);
			return $result->fetchAll(\PDO::FETCH_ASSOC);
		} catch (\PDOException $e) {
			die('Unable to perform the query : ' . $query);
		}
	}

	public function getTable() {
		echo "Connexion a la table : " . $this->tableName . " :\n";
		$this->db = \Library\PDOFactory::getMysqlConnexion('Frontend');
		$this->table = $this->describeTable();
		foreach ($this->table as $keyRow => $row) {
			$this->id = ($row['Extra'] == 'auto_increment') ? $row['Field'] : $this->id;
			reset($this->type);
			foreach ($this->type as $key => $val) {
				if (stripos($row['Type'], $key) !== false) {
					$row['TypePHP'] = $val;
					$row['FunctionPHP'] = $this->typeFunction[$val];
					echo "\t[" . $keyRow . "] " . $row['Field'] . " -> " . $val . "\n";
					$this->table[$keyRow] = $row;
					break;
				}
			}
		}
		if ($this->getLine('Do you want the details of the table? (y/n) : ', $this->YesNo, 'n') == 'y')
			print_r($this->table);
		if ($this->getLine('Do you want to continue? (y/n) : ', $this->YesNo, 'y') == 'n')
			exit();
	}

	public function getContentClass() {
		$this->content = <<<EOT
<?php\n
// Class: {$this->className_ucfirst}.class.php
// Table: $this->tableName
// Generated by createEntity.php, written by Paulo Ferreira (paulo.ferreira@arkium.eu)
// Date : $this->date\n
namespace Library\Entities;\n
class $this->className_ucfirst extends \Library\Entity {\n
	protected\n
EOT;
		foreach ($this->table as $row) {
			$this->content .= ($row['Extra'] != 'auto_increment') ? "\t\$" . $row['Field'] . ",\n" : "";
		}
		$this->content = rtrim($this->content, ",\n") . ";\n";
		$i = 1;
		foreach ($this->table as $row) {
			$this->content .= ($row['Extra'] != 'auto_increment') ? "\n\tconst " . strtoupper($row['Field']) . "_INVALIDE = $i;" : "";
			$i ++;
		}
		$this->content .= "\n\n";
		$this->content .= $this->function_isValid();
		$this->content .= "\t// SETTERS //\n\n";
		foreach ($this->table as $row) {
			$id = ($row['Extra'] == 'auto_increment') ? true : false;
			if ($row['Null'] === 'NO' && $id == false) {
				$this->content .= $this->functionSetNotNull($row['Field'], $row['TypePHP'], $row['FunctionPHP']);
			} else {
				$this->content .= $this->functionSet($row['Field'], $row['TypePHP'], $id);
			}
		}
		$this->content .= "\t//GETTERS //\n\n";
		foreach ($this->table as $row) {
			$id = ($row['Extra'] == 'auto_increment') ? true : false;
			$this->content .= $this->functionGet($row['Field'], $id);
		}
		$this->content .= "}";
	}

	private function functionSet($field, $type, $id) {
		$field_ucfirst = ucfirst($field);
		$fieldId = ($id) ? 'id' : $field;
		return <<<EOT
	public function set$field_ucfirst(\$$field) {
		\$this->$fieldId = $type\$$field;
	}\n\n
EOT;
	}

	private function functionSetNotNull($field, $type, $function) {
		$field_ucfirst = ucfirst($field);
		$function = sprintf($function, $field);
		$field_const = strtoupper($field);
		return <<<EOT
	public function set$field_ucfirst(\$$field) {
		if (!isset(\$$field)$function) {
			\$this->erreurs[] = self::{$field_const}_INVALIDE;
		} else {
			\$this->$field = $type\$$field;
		}
	}\n\n
EOT;
	}

	private function functionGet($field, $id) {
		$fieldId = ($id) ? 'id' : $field;
		return <<<EOT
	public function $field() {
		return \$this->$fieldId;
	}\n\n
EOT;
	}

	private function function_isValid() {
		$content = "";
		foreach ($this->table as $row) {
			$content .= ($row['Extra'] != 'auto_increment') ? "\t\t\tisset(\$this->" . $row['Field'] . ") &&\n" : "";
		}
		$content = rtrim($content, "&&\n");
		return <<<EOT
	public function isValid() {
		return (\n$content\n\t\t);
	}\n\n
EOT;
	}

	public function getContentManager() {
		$this->content = <<<EOT
<?php\n
// Class: {$this->className_ucfirst}Manager.class.php
// Table: $this->tableName
// Generated by createEntity.php, written by Paulo Ferreira (paulo.ferreira@arkium.eu)
// Date : $this->date\n
namespace Library\Models;\n
abstract class {$this->className_ucfirst}Manager extends \Library\ApplicationComponent {\n
	public \$lastInsertId;\n
	abstract public function getDatabases(\Library\HTTPRequest \$request, \Library\Datatable \$ini);\n
	abstract public function getList(\$debut = -1, \$limite = -1);\n
	abstract public function getUnique(\$id);\n
	abstract public function count();\n
	abstract public function add(\Library\HTTPRequest \$request);\n
	abstract public function modify(\Library\HTTPRequest \$request);\n
	abstract public function delete(\$id);\n
}\n\n
EOT;
	}

	public function getContentManager_PDO() {
		$this->content = <<<EOT
<?php\n
// Class: {$this->className_ucfirst}Manager_PDO.class.php
// Table: $this->tableName
// Generated by createEntity.php, written by Paulo Ferreira (paulo.ferreira@arkium.eu)
// Date : $this->date\n
namespace Library\Models;\n
class {$this->className_ucfirst}Manager_PDO extends {$this->className_ucfirst}Manager {\n\n
EOT;
		$this->content .= $this->function_getData();
		$this->content .= $this->function_getDatabases();
		$this->content .= $this->function_getList();
		$this->content .= $this->function_getUnique();
		$this->content .= $this->function_count();
		$this->content .= $this->function_add();
		$this->content .= $this->function_modify();
		$this->content .= $this->function_delete();
		$this->content .= "}";
	}

	private function function_getDatabases() {
		return <<<EOT
	public function getDatabases(\Library\HTTPRequest \$request, \Library\Datatable \$ini) {
		\$table = new \Library\Datatables(\$request, \$ini);
		return \$table->run();
	}\n\n
EOT;
	}

	private function function_getList() {
		return <<<EOT
	public function getList(\$debut = -1, \$limite = -1) {
		\$query = "SELECT * FROM $this->tableName";
		if (\$debut != -1 || \$limite != -1) {
			\$query .= ' LIMIT ' . (int) \$limite . ' OFFSET ' . (int) \$debut;
		}
		try {
			\$result = parent::\$dao->query(\$query);
			\$output = \$result->fetchAll();
			\$result->closeCursor();
		} catch (\PDOException \$e) {
			\$output['reponse'] = 'The database is not able to be read!<br/>';
			\$output['reponse'] .= "Code Error: " . \$e->getCode() . "<br/>";
			\$output['reponse'] .= "Syntax Error: " . \$e->getMessage();
		}
		return \$output;
	}\n\n
EOT;
	}

	private function function_getUnique() {
		$content = $this->bindValue('getUnique');
		return <<<EOT
	public function getUnique(\$id) {
		\$query = "SELECT
			$this->id,{$content['SQL']}
		FROM $this->tableName
		WHERE $this->id = :id";
		try {
			\$result = parent::\$dao->prepare(\$query);
			\$result->bindValue(':id', (int) \$id, \\PDO::PARAM_INT);
			\$result->execute();
			// \$result->setFetchMode(\\PDO::FETCH_CLASS | \\PDO::FETCH_PROPS_LATE, '\Library\Entities\\$this->className_ucfirst');
			\$output = (array) \$result->fetch(\PDO::FETCH_NUM);

			\$output['reponse'] = true;
		} catch (\PDOException \$e) {
			\$output['reponse'] = 'The database is not able to be read!<br/>';
			\$output['reponse'] .= "Code Error: " . \$e->getCode() . "<br/>";
			\$output['reponse'] .= "Syntax Error: " . \$e->getMessage();
		}
		return \$output;
	}\n\n
EOT;
	}

	private function function_count() {
		return <<<EOT
	public function count() {
		\$query = "SELECT COUNT(*) FROM $this->tableName";
		return parent::\$dao->query(\$query)->fetchColumn();
	}\n\n
EOT;
	}

	private function function_add() {
		$content = $this->bindValue('add');
		return <<<EOT
	public function add(\Library\HTTPRequest \$request) {
		\$$this->className = \$this->getData(\$request);
		\$query = "INSERT INTO $this->tableName SET {$content['SQL']}";
		try {
			parent::\$dao->beginTransaction();
			\$result = parent::\$dao->prepare(\$query);\n\n{$content['bindValue']}
			\$output['reponse'] = \$result->execute();
			\$this->lastInsertId = parent::\$dao->lastInsertId();
			\$output['lastID'] = \$this->lastInsertId;
			parent::\$dao->commit();
		} catch (\PDOException \$e) {
			parent::\$dao->rollback();
			\$error = \$result->errorInfo();
			if (\$error[1] == '1062') {
				\$output['reponse'] = 'This code already exists in the database.';
			} else {
				\$output['reponse'] = 'The update of the database is not successful!<br/>';
				\$output['reponse'] .= "Code Error: " . \$e->getCode() . "<br/>";
				\$output['reponse'] .= "Syntax Error: " . \$e->getMessage();
			}
		}
		return \$output;
	}\n\n
EOT;
	}

	private function function_modify() {
		$content = $this->bindValue('modify');
		return <<<EOT
	public function modify(\Library\HTTPRequest \$request) {
		\$$this->className = \$this->getData(\$request);
		\$query = "UPDATE $this->tableName SET {$content['SQL']}
		WHERE $this->id = :id";
		try {
			parent::\$dao->beginTransaction();
			\$result = parent::\$dao->prepare(\$query);\n
			\$result->bindValue(':id', \$$this->className->id(), \\PDO::PARAM_INT);\n{$content['bindValue']}
			\$output['reponse'] = \$result->execute();
			\$output['lastID'] = \$$this->className->id();
			parent::\$dao->commit();
		} catch (\PDOException \$e) {
			parent::\$dao->rollback();
			\$error = \$result->errorInfo();
			if (\$error[1] == '1062') {
				\$output['reponse'] = 'This code already exists in the database.';
			} else {
				\$output['reponse'] = 'The update of the database is not successful!<br/>';
				\$output['reponse'] .= "Code Error: " . \$e->getCode() . "<br/>";
				\$output['reponse'] .= "Syntax Error: " . \$e->getMessage();
			}
		}
		return \$output;
	}\n\n
EOT;
	}

	private function bindValue($op) {
		$content['SQL'] = "";
		$content['bindValue'] = "";
		foreach ($this->table as $row) {
			if ($op == 'getData') {
				$content['SQL'] .= "\n\t\t\t\t'" . $row['Field'] . "' => " . $row['TypePHP'] . " \$request->postData('" . $row['Field'] . "'),";
			} elseif ($row['Extra'] != 'auto_increment') {
				if ($op == 'getUnique') {
					$content['SQL'] .= "\n\t\t\t" . $row['Field'] . ",";
				} else {
					if (!stristr($row['Extra'], 'on update')) {
						if ($row['Field'] != 'created_time') {
							$content['SQL'] .= "\n\t\t\t" . $row['Field'] . " = :" . $row['Field'] . ",";
							$content['bindValue'] .= "\t\t\t\$result->bindValue(':" . $row['Field'] . "', \$$this->className->" . $row['Field'] . "());\n";
						} elseif ($op == 'add') {
							$content['SQL'] .= "\n\t\t\t" . $row['Field'] . " = NOW(),";
						}
					}
				}
			}
		}
		$content['SQL'] = rtrim($content['SQL'], ",");
		return $content;
	}

	private function function_delete() {
		return <<<EOT
	public function delete(\$id) {
		\$query = "DELETE FROM $this->tableName WHERE $this->id = " . (int) \$id;
		try {
			parent::\$dao->beginTransaction();
			\$result = parent::\$dao->prepare(\$query);
			\$output['reponse'] = \$result->execute();
			parent::\$dao->commit();
		} catch (\PDOException \$e) {
			parent::\$dao->rollback();
			\$output['reponse'] = 'The update of the database is not successful!<br/>';
			\$output['reponse'] .= "Code Error: " . \$e->getCode() . "<br/>";
			\$output['reponse'] .= "Syntax Error: " . \$e->getMessage();
		}
		return \$output;
	}\n\n
EOT;
	}

	private function function_getData() {
		$content = $this->bindValue('getData');
		return <<<EOT
	private function getData(\Library\HTTPRequest \$request) {
		return new \Library\Entities\\{$this->className_ucfirst}(array({$content['SQL']}
		));
	}\n\n
EOT;
	}
}