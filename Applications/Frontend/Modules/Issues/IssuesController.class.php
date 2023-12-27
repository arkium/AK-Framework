<?php

namespace Applications\Frontend\Modules\Issues;

class IssuesController extends \Library\BackController {

	public function __construct() {
		parent::__construct();
		parent::$param['user_id'] = isset($_REQUEST["user_id"]) ? $_REQUEST["user_id"] : parent::$user->data['user_id'];
		
		// Liste des utilisateurs
		$query = "SELECT user_id, first_name, last_name FROM ts_users WHERE user_id>0";
		$result = parent::$dao->query($query);
		$output = $result->fetchAll(\PDO::FETCH_ASSOC);
		foreach ($output as $row) {
			parent::$param['data_user_id'][$row['user_id']] = $row['last_name'] . ', ' . $row['first_name'];
		}
	}

	public function executeIndex(\Library\HTTPRequest $request) {
		$this->page->addVar('page_token', parent::$security->generer_token());
	}

	public function executeComments(\Library\HTTPRequest $request) {
		$this->page->addVar('page_token', parent::$security->generer_token());
		parent::$param['issue'] = $this->managers->getManagerOf('Issues')->getUnique($request->getData('id'));
		// Liste des fichiers
		parent::$param['data_file_id']['filename'] = array();
		parent::$param['data_file_id']['filesize'] = array();
		$output = $this->managers->getManagerOf('IssuesFiles')->getList("issue_id='" . $request->getData('id') . "'");
		// $query = "SELECT file_id, filename, filesize FROM ts_issues_files WHERE issue_id='" . $request->getData('id') . "'";
		// $result = parent::$dao->query($query);
		// $output = $result->fetchAll(\PDO::FETCH_ASSOC);
		foreach ($output as $row) {
			parent::$param['data_file_id']['filename'][$row['file_id']] = $row['filename'];
			parent::$param['data_file_id']['filesize'][$row['file_id']] = 'Size: ' . $row['filesize'] . ' byte(s)';
		}
	}

	public function executeList(\Library\HTTPRequest $request) {
		parent::$security->verifier_token();
		$ini = new \Library\Datatable();
		$ini->aColumnsDisplay = array(
				'id',
				'title',
				'attachment',
				'username',
				'type_id',
				'date',
				'status_' 
		);
		$ini->sQuery = "
		SELECT SQL_CALC_FOUND_ROWS *
		FROM (
			SELECT
			i.issue_id,
			i.issue_id as id,
			i.title,
			CONCAT(u.last_name, ', ', u.first_name) AS username,
			i.type_id,
			IF(COUNT(f.file_id)>0,'1','0') AS attachment,
			i.created_time as date,
			IF(i.status=0,'Closed','Open') AS status_
			FROM ts_users u, ts_issues i 
				LEFT JOIN ts_issues_files f ON i.issue_id=f.issue_id 
			WHERE i.user_id=u.user_id
			GROUP BY i.issue_id 
		) AS view";
		$ini->sIndexColumn = "issue_id";
		$ini->sTable = "ts_issues";
		$ini->sDisplay = array(
				"username" => function ($aRow, $key, $var = '') {
					return trim($aRow[$key], ', ');
				},
				"title" => function ($aRow, $key, $var = '') {
					// return "<a href=\"issues_comments?id=" . $aRow['issue_id'] . "\">" . $aRow[$key] . "</a>";
					return $aRow[$key];
				},
				"attachment" => function ($aRow, $key, $var = '') {
					return ($aRow[$key] == "1") ? '<span class="glyphicon glyphicon-paperclip"></span>' : '';
				},
				"status_" => function ($aRow, $key, $var = '') {
					return ($aRow[$key] == "Closed") ? '<span class="label label-warning">Closed</span>' : '<span class="label label-success">Open</span>';
				} 
		);
		$list = $this->managers->getManagerOf('Users')->getDatabases($request, $ini);
		parent::$httpResponse->json($list);
	}

	public function executeJson(\Library\HTTPRequest $request) {
		parent::$security->verifier_token();
		if ($request->postExists('op')) {
			switch ($request->postData('op')) {
				case 'view' :
					$json = $this->managers->getManagerOf('Issues')->getUnique($request->postData('issue_id'));
					if ($json['reponse'])
						$json['files'] = $this->managers->getManagerOf('IssuesFiles')->getList("issue_id='" . $request->postData('issue_id') . "'");
					break;
				case 'add' :
					$json = $this->managers->getManagerOf('Issues')->add($request);
					$issue_id = $this->managers->getManagerOf('Issues')->lastInsertId;
					if ($json['reponse'])
						$json = $this->addFile($json, $request->postData('file'), $request->postData('filesize'), $issue_id);
					break;
				case 'edit' :
					$json = $this->managers->getManagerOf('Issues')->modify($request);
					// manque l'edit des fichiers uploader
					break;
				case 'delete' :
					$json = $this->deleteComments($request->postData('issue_id'));
					if ($json['reponse'])
						$json = $this->managers->getManagerOf('Issues')->delete($request->postData('issue_id'));
					if ($json['reponse'])
						$json = $this->deleteFile($json, $request->postData('issue_id'));
					if ($json['reponse']) {
						$query = "DELETE FROM ts_issues_files WHERE issue_id='" . $request->postData('issue_id') . "'";
						$json['reponse'] = ($result = parent::$dao->exec($query) !== false) ? true : 'The update of the database is not successful!';
					}
					break;
				case 'status' :
					$json = $this->setStatus($request);
					break;
				case 'type' :
					$json = $this->setType($request);
					break;
				case 'lst_comment' :
					$output = $this->getComments($request->postData('issue_id'));
					$json['result'] = array_values($output);
					$json['reponse'] = true;
					break;
				case 'add_comment' :
					$json = $this->managers->getManagerOf('IssuesComments')->add($request);
					if ($json['reponse']) {
						$output = $this->managers->getManagerOf('IssuesComments')->getUnique($json['lastID']);
						$json = null;
						$json['result'] = $output;
						$json['result']['username'] = parent::$param['data_user_id'][$output['user_id']];
						$json['reponse'] = true;
					}
					break;
				case 'delete_comment' :
					$json = $this->managers->getManagerOf('IssuesComments')->delete($request->postData('comment_id'));
					break;
				case 'delete_file' :
					$json['reponse'] = $this->deleteFilename($request);
					break;
			}
			parent::$httpResponse->json($json);
		}
	}

	public function executeForm(\Library\HTTPRequest $request) {
		parent::$security->verifier_token();
		$fct = new \Library\Functions();
		include ('Applications/Frontend/Modules/Issues/Views/frmIssue.php');
		exit();
	}

	public function executeFile(\Library\HTTPRequest $request) {
		parent::$security->verifier_token();
		try {
			$file = new \Library\Upload($_FILES['file']);
			$json['reponse'] = $file->uploadFile();
			$json['filename'] = $file->getFilenameSanitized();
			parent::$httpResponse->json($json);
		} catch (\Library\ArkiumException $e) {
			$e->getMsg();
		}
	}

	public function executeView(\Library\HTTPRequest $request) {
		try {
			$file = new \Library\Upload();
			$json['reponse'] = $file->viewFile($request->getData('file'));
		} catch (\Library\ArkiumException $e) {
			$e->getMsg();
		}
		exit();
	}

	private function deleteFilename(\Library\HTTPRequest $request) {
		try {
			$file = new \Library\Upload();
			$json['reponse'] = $file->deleteFile($request->postData('file'));
			$json['filename'] = $file->getFilenameSanitized();
			parent::$httpResponse->json($json);
		} catch (\Library\ArkiumException $e) {
			$e->getMsg();
		}
	}

	private function getComments($issue_id) {
		// Récupération des comments d'un issue
		$output = null;
		$query = "SELECT ic.comment_id, CONCAT(u.last_name, ', ', u.first_name) as username, ic.comment, ic.created_time
		FROM ts_issues_comments ic, ts_users u
		WHERE ic.user_id=u.user_id 
			AND issue_id='$issue_id'
		ORDER BY ic.update_time ASC";
		$result = parent::$dao->query($query);
		$output = $result->fetchAll(\PDO::FETCH_ASSOC);
		return $output;
	}

	private function deleteComments($issue_id) {
		$query = "DELETE FROM ts_issues_comments
		WHERE issue_id='$issue_id'";
		$output['reponse'] = (parent::$dao->exec($query) !== false) ? true : 'The update of the database is not successful!';
		return $output;
	}

	private function setStatus(\Library\HTTPRequest $request) {
		// Change le status d'un issue
		$issue_id = (int) $request->postData('issue_id');
		$status = (int) $request->postData('status');
		$query = "UPDATE ts_issues SET status='$status'
		WHERE issue_id='$issue_id'";
		$output['reponse'] = (parent::$dao->exec($query)) ? true : 'The update of the database is not successful!';
		return $output;
	}

	private function setType(\Library\HTTPRequest $request) {
		// Change le type d'un issue
		$issue_id = (int) $request->postData('issue_id');
		$type = $request->postData('type');
		$query = "UPDATE ts_issues SET type_id='$type'
		WHERE issue_id='$issue_id'";
		$output['reponse'] = (parent::$dao->exec($query)) ? true : $query . ' The update of the database is not successful!';
		return $output;
	}

	private function addFile($json, $filename, $filesize, $issue_id) {
		if (!empty($filename) && is_array($filename) && $json['reponse']) {
			reset($filename);
			foreach ($filename as $key => $value) {
				$query = "INSERT INTO ts_issues_files SET
				issue_id = :issue_id,
				filename = :value,
				filesize = :filesize,
				created_time=NOW()";
				$result = parent::$dao->prepare($query);
				$result->bindValue(':issue_id', $issue_id);
				$result->bindValue(':value', $value);
				$result->bindValue(':filesize', $filesize[$key]);
				$json['reponse'] = ($result->execute() !== false) ? true : 'The update of the database is not successful!';
				if ($json['reponse'] !== true)
					break;
			}
		}
		return $json;
	}

	private function deleteFile($json, $issue_id) {
		if (!empty($issue_id) && $json['reponse']) {
			try {
				$file = new \Library\Upload();
				$query = "SELECT filename FROM ts_issues_files WHERE issue_id='$issue_id'";
				$result = parent::$dao->query($query);
				$output = $result->fetchAll(\PDO::FETCH_ASSOC);
				foreach ($output as $row) {
					$json['reponse'] = $file->deleteFile($row['filename']);
					if ($json['reponse'] !== true)
						break;
				}
			} catch (\Library\ArkiumException $e) {
				$e->getMsg();
			}
		}
		return $json;
	}
}