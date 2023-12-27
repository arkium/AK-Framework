<?php

namespace Applications\Frontend;

define('DIR_TPL_EMAIL', '/Applications/Frontend/Templates/tpl_email/');

/**
 * Classe de l'application "Frontend"
 */
class FrontendApplication extends \Library\Application {

	/**
	 * Constructeur de l'application "Frontend" avec l'initialisation des variables
	 */
	public function __construct() {
		parent::__construct();
		parent::$name = 'Frontend';

		// Liste des permissions
		parent::$param['permissions']['code'][0] = '0';
		parent::$param['permissions']['name'][0] = 'View';
		parent::$param['permissions']['code'][1] = '1';
		parent::$param['permissions']['name'][1] = 'Add';
		parent::$param['permissions']['code'][2] = '2';
		parent::$param['permissions']['name'][2] = 'Edit';
		parent::$param['permissions']['code'][3] = '3';
		parent::$param['permissions']['name'][3] = 'Delete';
		parent::$param['permissions']['code'][4] = '4';
		parent::$param['permissions']['name'][4] = 'Approval';
		parent::$param['permissions']['code'][5] = '5';
		parent::$param['permissions']['name'][5] = 'Admin';

		// Liste des status
		parent::$param['status']['code'][0] = '0';
		parent::$param['status']['name'][0] = 'Closed';
		parent::$param['status']['code'][1] = '1';
		parent::$param['status']['name'][1] = 'Open';

        // Liste des status_filter
		parent::$param['status_filter']['code'][0] = 'Closed';
		parent::$param['status_filter']['name'][0] = 'Closed';
		parent::$param['status_filter']['code'][1] = 'Open';
		parent::$param['status_filter']['name'][1] = 'Open';

		// Liste des types de société
		parent::$param['entity_type_id']['code'][1] = '1';
		parent::$param['entity_type_id']['name'][1] = 'Companies';
		parent::$param['entity_type_id']['code'][2] = '2';
		parent::$param['entity_type_id']['name'][2] = 'Customers';
		parent::$param['entity_type_id']['code'][3] = '3';
		parent::$param['entity_type_id']['name'][3] = 'Suppliers';

		// Liste des legal form
		parent::$param['legal_form']['code'][0] = '0';
		parent::$param['legal_form']['name'][0] = 'Please select';
		parent::$param['legal_form']['code'][1] = '1';
		parent::$param['legal_form']['name'][1] = 'SPRL';
		parent::$param['legal_form']['code'][2] = '2';
		parent::$param['legal_form']['name'][2] = 'SA';

		// Liste des types de contact
		parent::$param['contact_type_id']['code'][1] = '1';
		parent::$param['contact_type_id']['name'][1] = 'Accounting';
		parent::$param['contact_type_id']['code'][2] = '2';
		parent::$param['contact_type_id']['name'][2] = 'Commercial';
		parent::$param['contact_type_id']['code'][3] = '3';
		parent::$param['contact_type_id']['name'][3] = 'Other';

		// Liste des types de tâches
		parent::$param['type_chargeable']['code'][] = '0';
		parent::$param['type_chargeable']['name'][] = 'Non-chargeable';
		parent::$param['type_chargeable']['code'][] = '1';
		parent::$param['type_chargeable']['name'][] = 'Chargeable';

		// Liste des types de feuille de temps
		parent::$param['typetimesheet']['code'][] = '0';
		parent::$param['typetimesheet']['name'][] = 'Sans pointage des heures';
		parent::$param['typetimesheet']['code'][] = '1';
		parent::$param['typetimesheet']['name'][] = 'Avec pointage des heures';

		// Liste des couleurs par défaut
		parent::$param['type_color']['code'][] = "#FFFFFF";
		parent::$param['type_color']['name'][] = "#FFFFFF";
		parent::$param['type_color']['code'][] = "#EEEEEE";
		parent::$param['type_color']['name'][] = "#EEEEEE";
		parent::$param['type_color']['code'][] = "#FFFF88";
		parent::$param['type_color']['name'][] = "#FFFF88";
		parent::$param['type_color']['code'][] = "#FF7400";
		parent::$param['type_color']['name'][] = "#FF7400";
		parent::$param['type_color']['code'][] = "#CDEB8B";
		parent::$param['type_color']['name'][] = "#CDEB8B";
		parent::$param['type_color']['code'][] = "#6BBA70";
		parent::$param['type_color']['name'][] = "#6BBA70";
		parent::$param['type_color']['code'][] = "#006E2E";
		parent::$param['type_color']['name'][] = "#006E2E";
		parent::$param['type_color']['code'][] = "#C3D9FF";
		parent::$param['type_color']['name'][] = "#C3D9FF";
		parent::$param['type_color']['code'][] = "#4096EE";
		parent::$param['type_color']['name'][] = "#4096EE";
		parent::$param['type_color']['code'][] = "#356AA0";
		parent::$param['type_color']['name'][] = "#356AA0";
		parent::$param['type_color']['code'][] = "#FF0096";
		parent::$param['type_color']['name'][] = "#FF0096";
		parent::$param['type_color']['code'][] = "#B02B2C";
		parent::$param['type_color']['name'][] = "#B02B2C";
		parent::$param['type_color']['code'][] = "#000000";
		parent::$param['type_color']['name'][] = "#000000";
	}

	/**
	 * Execute l'application et retour le page
	 */
	public function run() {
		$controller = $this->getController(parent::$httpRequest->getUrlPath());
		$controller->execute();
		parent::$httpResponse->setPage($controller->page);
		parent::$httpResponse->send();
	}
}