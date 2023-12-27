<?php
/**
 * AK Framework
 *
 * @author Paulo Ferreira <paulo.ferreira@arkium.eu>
 * @copyright Copyright (c) 2012-2020, Arkium SCS
 */
namespace Library;

class Upload {

	public $file_maxsize = 5242880; // 5MB maximum file size
	public $storeFolder = "uploads"; // Répertoire de tranfère
	public $santized = false;

	private $targetPath = null;

	private $ds = DIRECTORY_SEPARATOR;

	private $file_types = "/^\.(jpg|jpeg|gif|png|doc|docx|txt|rtf|pdf|xls|xlsx|ppt|pptx){1}$/i";

	private $files = null;

	private $filename_sanitized = null;

	private $filename_original = null;

	public function __construct($files = null) {
		if (ini_get('file_uploads') == false)
			throw new \RuntimeException('File uploads are disabled in your PHP.ini file');
		$this->files = $files;
		$this->targetPath = CORE_PATH . $this->ds . $this->storeFolder . $this->ds;
	}

	public function getFilenameSanitized() {
		return $this->filename_sanitized;
	}

	public function getFilenameOriginal() {
		return $this->filename_original;
	}

	private function checkError() {
		if ($this->files['error'] !== UPLOAD_ERR_OK) {
			switch ($this->files['error']) {
				case UPLOAD_ERR_INI_SIZE :
					$message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
					break;
				case UPLOAD_ERR_FORM_SIZE :
					$message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
					break;
				case UPLOAD_ERR_PARTIAL :
					$message = "The uploaded file was only partially uploaded";
					break;
				case UPLOAD_ERR_NO_FILE :
					$message = "No file was uploaded";
					break;
				case UPLOAD_ERR_NO_TMP_DIR :
					$message = "Missing a temporary folder";
					break;
				case UPLOAD_ERR_CANT_WRITE :
					$message = "Failed to write file to disk";
					break;
				case UPLOAD_ERR_EXTENSION :
					$message = "File upload stopped by extension";
					break;
				default :
					$message = "Unknown upload error";
					break;
			}
			throw new \Library\ArkiumException($message);
		}
	}

	private function isUploadedFile() {
		if (!is_uploaded_file($this->files['tmp_name'])) {
			throw new \Library\ArkiumException("The file wasn't uploaded via HTTP POST :" . $this->files['tmp_name'][$cursor]);
		}
	}

	private function sanitize($filename = null) {
		if (empty($filename) && empty($this->files))
			throw new \InvalidArgumentException("No file to upload");

		$this->filename_original = (empty($filename)) ? $this->files['name'] : $filename;
		if ($this->santized) {
			// sanatize file name
			// - remove extra spaces/convert to _,
			// - remove non 0-9a-Z._- characters,
			// - remove leading/trailing spaces
			$safe_filename = preg_replace(array(
					"/\s+/",
					"/[^-\.\w]+/"
			), array(
					"_",
					""
			), trim($this->filename_original));
		} else {
			$safe_filename = $this->filename_original;
		}
		$this->filename_sanitized = $safe_filename;
	}

	private function fileSize() {
		if ($this->files['size'] > $this->file_maxsize)
			throw new \Library\ArkiumException("The uploaded file exceeds the MAX_FILE_SIZE");
	}

	private function extensionValid() {
		if (!preg_match($this->file_types, strrchr($this->filename_sanitized, '.')))
			throw new \Library\ArkiumException('File upload stopped by extension');
	}

	private function fileExists() {
		if (file_exists($this->targetPath . $this->filename_sanitized))
			throw new \Library\ArkiumException("The file $this->filename_sanitized does exist on server");
	}

	private function fileNoExists() {
		if (!file_exists($this->targetPath . $this->filename_sanitized))
			throw new \Library\ArkiumException("The file $this->filename_sanitized does not exist on server");
	}

	private function saveUploadedFile() {
		if (!move_uploaded_file($this->files['tmp_name'], $this->targetPath . $this->filename_sanitized))
			throw new \Library\ArkiumException("No file was uploaded");
	}

	public function uploadFile() {
		$this->checkError();
		$this->isUploadedFile();
		$this->sanitize();
		$this->fileSize();
		$this->extensionValid();
		$this->fileExists();
		$this->saveUploadedFile();
		return true;
	}

	public function deleteFile($filename) {
		$this->sanitize($filename);
		$this->fileNoExists();
		return unlink($this->targetPath . $this->filename_sanitized);
	}

	public function viewFile($filename) {
		$this->sanitize($filename);
		$this->fileNoExists();
		// header('Content-type: application/pdf');
		header('Content-Disposition: attachment; filename="' . urlencode($this->filename_sanitized) . '"');
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		// header("Content-Description: File Transfer");
		// header("Content-Length: " . filesize($file));
		readfile($this->targetPath . $this->filename_sanitized);
	}

	public static function humanReadableToBytes($input) {
		$units = array(
				'b' => 1,
				'k' => 1024,
				'm' => 1048576,
				'g' => 1073741824
		);
		$unit = strtolower(substr($input, -1));
		return (isset($units[$unit])) ? (int) $input * $units[$unit] : (int) $input;
	}

}