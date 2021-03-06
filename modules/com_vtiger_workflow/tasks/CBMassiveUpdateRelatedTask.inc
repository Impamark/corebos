<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTWorkflowUtils.php';
require_once 'modules/com_vtiger_workflow/VTTaskQueue.inc';
require_once 'modules/cbMap/cbMap.php';

class CBMassiveUpdateRelatedTask extends VTTask {
	public $executeImmediately = true;

	public function getFieldNames() {
		return array('field_value_mapping','bmapid', 'bmapid_display');
	}

	public function doTask(&$entity) {
		global $adb, $current_user, $logbg, $from_wf;
		$from_wf = true;
		$logbg->debug('> CBMassiveUpdateRelatedTask');
		$util = new VTWorkflowUtils();
		$util->adminUser();
		$isqueue=$entity->isqueue;
		$taskQueue = new VTTaskQueue($adb);
		$moduleName = $entity->getModuleName();
		$entityId = $entity->getId();
		$recordId = vtws_getIdComponents($entityId);
		$recordId = $recordId[1];
		$bmapid = $this->bmapid;
		$logbg->debug("Module: $moduleName, Record: $entityId");
		$moduleHandler = vtws_getModuleHandlerFromName($moduleName, Users::getActiveAdminUser());
		$handlerMeta = $moduleHandler->getMeta();
		$moduleFields = $handlerMeta->getModuleFields();
		$fieldValueMapping = array();
		if (!empty($bmapid)) {
			// Business Map
			$focusmap = new cbMap();
			$focusmap->id = $bmapid;
			$focusmap->mode = '';
			$focusmap->retrieve_entity_info($bmapid, 'cbMap');
			$businessMap = $focusmap->column_fields['maptype'];
		} else {
			$businessMap = '';
		}
		if (!empty($this->field_value_mapping)) {
			$fieldValueMapping = json_decode($this->field_value_mapping, true);
		}
		if (!empty($fieldValueMapping) && count($fieldValueMapping) > 0) {
			include_once 'data/CRMEntity.php';
			$focus = CRMEntity::getInstance($moduleName);
			$focus->id = $recordId;
			$focus->mode = 'edit';
			$focus->retrieve_entity_info($recordId, $moduleName, false, $from_wf);
			$focus->clearSingletonSaveFields();

			$hold_user = $current_user;
			$util->loggedInUser();
			if (is_null($current_user)) {
				$current_user = $hold_user; // make sure current_user is defined
			}
			$relmodule = array();
			$handlerMetarel[] = array();
			$fieldValue = array();
			$fieldmodule = array();

			foreach ($fieldValueMapping as $fieldInfo) {
				$fieldName = $fieldInfo['fieldname'];
				$fieldType = '';
				$fldmod = '';
				$fieldValueType = $fieldInfo['valuetype'];
				$fieldValue1 = trim($fieldInfo['value']);
				if (array_key_exists('fieldmodule', $fieldInfo)) {
					$fldmod = trim($fieldInfo['fieldmodule']);
					$fieldmodule = explode('__', trim($fieldInfo['fieldmodule']));
				}
				$module = $fieldmodule[0];
				$moduleHandlerrel = vtws_getModuleHandlerFromName($module, Users::getActiveAdminUser());
				$handlerMetarel[$fldmod] = $moduleHandlerrel->getMeta();
				$moduleFieldsrel = $handlerMetarel[$fldmod]->getModuleFields();
				$fieldValue[$fldmod][$fieldName]=$util->fieldvaluebytype($moduleFieldsrel, $fieldValueType, $fieldValue1, $fieldName, $focus, $entity, $handlerMeta);
			}
			$hold_ajxaction = isset($_REQUEST['ajxaction']) ? $_REQUEST['ajxaction'] : '';
			$_REQUEST['ajxaction'] = 'Workflow';
			if ($fldmod!='') {
				$focusrel[] = array();
				$fieldmodule = explode('__', $fldmod);
				$relmodule = $fieldmodule[0];
				$relfield = $fieldmodule[1];
				$focusrel[$fldmod] = CRMEntity::getInstance($relmodule);
				$maintable = $focusrel[$fldmod]->table_name;
				$mainid = $focusrel[$fldmod]->table_index;
				if ($businessMap == 'Condition Query') {
					$ids = $focusmap->ConditionQuery($recordId);
					foreach ($ids as $recid) {
						$getids[] = $recid["$mainid"];
					}
					if (count($getids)>0) {
						$condition = " and $mainid in (".implode(",", $getids).")";
					} else {
						$condition = "";
					}
				}
				$records=$adb->query("select $mainid from $maintable join vtiger_crmentity on crmid=$mainid where deleted=0 and $relfield='$recordId' $condition");
				for ($nr=0; $nr<$adb->num_rows($records); $nr++) {
					$relId = $adb->query_result($records, $nr, '0');
					$entity = vtws_getEntityId($relmodule);
					if ($businessMap != 'Condition Expression'  || ($businessMap == 'Condition Expression' && $focusmap->ConditionExpression($entity.'x'.$relId))) {
						$focusrel[$fldmod]->id = $relId;
						$focusrel[$fldmod]->mode = 'edit';
						$focusrel[$fldmod]->retrieve_entity_info($relId, $relmodule, false, $from_wf);
						$focusrel[$fldmod]->clearSingletonSaveFields();
						$fval = $fieldValue[$fldmod];
						foreach ($fval as $key => $value) {
							$focusrel[$fldmod]->column_fields[$key]=$value;
						}
						$focusrel[$fldmod]->column_fields = DataTransform::sanitizeRetrieveEntityInfo($focusrel[$fldmod]->column_fields, $handlerMetarel[$fldmod]);
						$focusrel[$fldmod]->saveentity($relmodule);
					}
				}
			}
			$_REQUEST['ajxaction'] = $hold_ajxaction;
		}
		$util->revertUser();
		$from_wf = false;
		$logbg->debug('< CBMassiveUpdateRelatedTask');
	}
}
?>
