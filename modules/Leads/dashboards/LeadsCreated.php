<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Leads_LeadsCreated_Dashboard extends Vtiger_IndexAjax_View
{

	/**
	 * Function to get the list of Script models to be included
	 * @param \App\Request $request
	 * @return <Array> - List of Vtiger_JsScript_Model instances
	 */
	public function getFooterScripts(\App\Request $request)
	{

		$jsFileNames = [
//			'~libraries/jquery/jqplot/plugins/jqplot.cursor.min.js',
//			'~libraries/jquery/jqplot/plugins/jqplot.dateAxisRenderer.min.js',
//			'~libraries/jquery/jqplot/plugins/jqplot.logAxisRenderer.min.js',
//			'~libraries/jquery/jqplot/plugins/jqplot.canvasTextRenderer.min.js',
//			'~libraries/jquery/jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js'
		];

		$headerScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
		return $headerScriptInstances;
	}

	public function process(\App\Request $request)
	{
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$viewer = $this->getViewer($request);
		$moduleName = $request->getModule();

		$linkId = $request->getInteger('linkid');
		$createdTime = $request->getDateRange('createdtime');
		$owner = $request->getByType('owner', 2);

		//Date conversion from user to database format
		if (!empty($createdTime)) {
			$dates['start'] = Vtiger_Date_UIType::getDBInsertedValue($createdTime['start']);
			$dates['end'] = Vtiger_Date_UIType::getDBInsertedValue($createdTime['end']);
		}

		$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		$data = $moduleModel->getLeadsCreated($owner, $dates);

		$widget = Vtiger_Widget_Model::getInstance($linkId, $currentUser->getId());

		//Include special script and css needed for this widget
		$viewer->assign('SCRIPTS', $this->getHeaderScripts($request));

		$viewer->assign('WIDGET', $widget);
		$viewer->assign('MODULE_NAME', $moduleName);
		$viewer->assign('DATA', $data);
		$viewer->assign('CURRENTUSER', $currentUser);

		$accessibleUsers = \App\Fields\Owner::getInstance('Leads', $currentUser)->getAccessibleUsersForModule();
		$viewer->assign('ACCESSIBLE_USERS', $accessibleUsers);
		if ($request->has('content')) {
			$viewer->view('dashboards/DashBoardWidgetContents.tpl', $moduleName);
		} else {
			$viewer->view('dashboards/LeadsCreated.tpl', $moduleName);
		}
	}
}
