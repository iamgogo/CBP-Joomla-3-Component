<?php
/*----------------------------------------------------------------------------------|  www.giz.de  |----/
	Deutsche Gesellschaft für International Zusammenarbeit (GIZ) Gmb 
/-------------------------------------------------------------------------------------------------------/

	@version		3.4.x
	@build			14th August, 2019
	@created		15th June, 2012
	@package		Cost Benefit Projection
	@subpackage		cpanel.php
	@author			Llewellyn van der Merwe <http://www.vdm.io>	
	@owner			Deutsche Gesellschaft für International Zusammenarbeit (GIZ) Gmb
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html
	
/-------------------------------------------------------------------------------------------------------/
	Cost Benefit Projection Tool.
/------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Costbenefitprojection Model for Cpanel
 */
class CostbenefitprojectionModelCpanel extends JModelList
{
	/**
	 * Model user data.
	 *
	 * @var        strings
	 */
	protected $user;
	protected $userId;
	protected $guest;
	protected $groups;
	protected $levels;
	protected $app;
	protected $input;
	protected $uikitComp;

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return      string  An SQL query
	 */
	protected function getListQuery()
	{
		// Get the current user for authorisation checks
		$this->user = JFactory::getUser();
		$this->userId = $this->user->get('id');
		$this->guest = $this->user->get('guest');
		$this->groups = $this->user->get('groups');
		$this->authorisedGroups = $this->user->getAuthorisedGroups();
		$this->levels = $this->user->getAuthorisedViewLevels();
		$this->app = JFactory::getApplication();
		$this->input = $this->app->input;
		$this->initSet = true; 
		// Make sure all records load, since no pagination allowed.
		$this->setState('list.limit', 0);
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__costbenefitprojection_company as a
		$query->select($db->quoteName(
			array('a.id','a.asset_id','a.name','a.user','a.department','a.per','a.email','a.country','a.service_provider','a.datayear','a.working_days','a.total_salary','a.total_healthcare','a.productivity_losses','a.males','a.females','a.medical_turnovers_males','a.medical_turnovers_females','a.sick_leave_males','a.sick_leave_females','a.percentmale','a.percentfemale','a.causesrisks','a.not_required','a.published','a.checked_out','a.checked_out_time','a.created_by','a.modified_by','a.created','a.modified','a.version','a.hits','a.ordering'),
			array('id','asset_id','name','user','department','per','email','country','service_provider','datayear','working_days','total_salary','total_healthcare','productivity_losses','males','females','medical_turnovers_males','medical_turnovers_females','sick_leave_males','sick_leave_females','percentmale','percentfemale','causesrisks','not_required','published','checked_out','checked_out_time','created_by','modified_by','created','modified','version','hits','ordering')));
		$query->from($db->quoteName('#__costbenefitprojection_company', 'a'));

		// Get from #__costbenefitprojection_country as b
		$query->select($db->quoteName(
			array('b.name','b.user','b.publicname','b.publicemail','b.publicnumber','b.publicaddress'),
			array('country_name','country_user','country_publicname','country_publicemail','country_publicnumber','country_publicaddress')));
		$query->join('LEFT', ($db->quoteName('#__costbenefitprojection_country', 'b')) . ' ON (' . $db->quoteName('a.country') . ' = ' . $db->quoteName('b.id') . ')');

		// Get from #__costbenefitprojection_service_provider as c
		$query->select($db->quoteName(
			array('c.user','c.publicname','c.publicemail','c.publicnumber','c.publicaddress'),
			array('service_provider_user','service_provider_publicname','service_provider_publicemail','service_provider_publicnumber','service_provider_publicaddress')));
		$query->join('LEFT', ($db->quoteName('#__costbenefitprojection_service_provider', 'c')) . ' ON (' . $db->quoteName('a.service_provider') . ' = ' . $db->quoteName('c.id') . ')');

		// Get from #__costbenefitprojection_currency as g
		$query->select($db->quoteName(
			array('g.id','g.name','g.codethree','g.numericcode','g.symbol','g.thousands','g.decimalplace','g.decimalsymbol','g.positivestyle','g.negativestyle'),
			array('currency_id','currency_name','currency_codethree','currency_numericcode','currency_symbol','currency_thousands','currency_decimalplace','currency_decimalsymbol','currency_positivestyle','currency_negativestyle')));
		$query->join('LEFT', ($db->quoteName('#__costbenefitprojection_currency', 'g')) . ' ON (' . $db->quoteName('b.currency') . ' = ' . $db->quoteName('g.codethree') . ')');

		// Get from #__users as d
		$query->select($db->quoteName(
			array('d.name'),
			array('service_provider_name')));
		$query->join('LEFT', ($db->quoteName('#__users', 'd')) . ' ON (' . $db->quoteName('c.id') . ' = ' . $db->quoteName('d.id') . ')');
		$query->where('a.user = ' . (int) $this->userId);
		$query->order('a.ordering ASC');

		// return the query object
		return $query;
	}

	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$user = JFactory::getUser();
		// check if this user has permission to access item
		if (!$user->authorise('site.cpanel.access', 'com_costbenefitprojection'))
		{
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('COM_COSTBENEFITPROJECTION_NOT_AUTHORISED_TO_VIEW_CPANEL'), 'error');
			// redirect away to the home page if no access allowed.
			$app->redirect(JURI::root());
			return false;
		}
		// load parent items
		$items = parent::getItems();

		// Get the global params
		$globalParams = JComponentHelper::getParams('com_costbenefitprojection', true);

		// Get the whmcs encryption.
		$whmcskey = CostbenefitprojectionHelper::getCryptKey('whmcs');
		// Get the encryption object.
		$whmcs = new FOFEncryptAes($whmcskey);

		// Insure all item fields are adapted where needed.
		if (CostbenefitprojectionHelper::checkArray($items))
		{
			// Load the JEvent Dispatcher
			JPluginHelper::importPlugin('content');
			$this->_dispatcher = JEventDispatcher::getInstance();
			foreach ($items as $nr => &$item)
			{
				// Always create a slug for sef URL's
				$item->slug = (isset($item->alias) && isset($item->id)) ? $item->id.':'.$item->alias : $item->id;
				// Check if we can decode males
				if (!empty($item->males) && $whmcskey && !is_numeric($item->males) && $item->males === base64_encode(base64_decode($item->males, true)))
				{
					// Decode males
					$item->males = rtrim($whmcs->decryptString($item->males), "\0");
				}
				// Check if we can decode sick_leave_males
				if (!empty($item->sick_leave_males) && $whmcskey && !is_numeric($item->sick_leave_males) && $item->sick_leave_males === base64_encode(base64_decode($item->sick_leave_males, true)))
				{
					// Decode sick_leave_males
					$item->sick_leave_males = rtrim($whmcs->decryptString($item->sick_leave_males), "\0");
				}
				// Check if we can decode sick_leave_females
				if (!empty($item->sick_leave_females) && $whmcskey && !is_numeric($item->sick_leave_females) && $item->sick_leave_females === base64_encode(base64_decode($item->sick_leave_females, true)))
				{
					// Decode sick_leave_females
					$item->sick_leave_females = rtrim($whmcs->decryptString($item->sick_leave_females), "\0");
				}
				// Check if we can decode total_salary
				if (!empty($item->total_salary) && $whmcskey && !is_numeric($item->total_salary) && $item->total_salary === base64_encode(base64_decode($item->total_salary, true)))
				{
					// Decode total_salary
					$item->total_salary = rtrim($whmcs->decryptString($item->total_salary), "\0");
				}
				// Check if we can decode causesrisks
				if (CostbenefitprojectionHelper::checkJson($item->causesrisks))
				{
					// Decode causesrisks
					$item->causesrisks = json_decode($item->causesrisks, true);
				}
				// Check if we can decode total_healthcare
				if (!empty($item->total_healthcare) && $whmcskey && !is_numeric($item->total_healthcare) && $item->total_healthcare === base64_encode(base64_decode($item->total_healthcare, true)))
				{
					// Decode total_healthcare
					$item->total_healthcare = rtrim($whmcs->decryptString($item->total_healthcare), "\0");
				}
				// Check if we can decode females
				if (!empty($item->females) && $whmcskey && !is_numeric($item->females) && $item->females === base64_encode(base64_decode($item->females, true)))
				{
					// Decode females
					$item->females = rtrim($whmcs->decryptString($item->females), "\0");
				}
				// Check if we can decode medical_turnovers_males
				if (!empty($item->medical_turnovers_males) && $whmcskey && !is_numeric($item->medical_turnovers_males) && $item->medical_turnovers_males === base64_encode(base64_decode($item->medical_turnovers_males, true)))
				{
					// Decode medical_turnovers_males
					$item->medical_turnovers_males = rtrim($whmcs->decryptString($item->medical_turnovers_males), "\0");
				}
				// Check if we can decode medical_turnovers_females
				if (!empty($item->medical_turnovers_females) && $whmcskey && !is_numeric($item->medical_turnovers_females) && $item->medical_turnovers_females === base64_encode(base64_decode($item->medical_turnovers_females, true)))
				{
					// Decode medical_turnovers_females
					$item->medical_turnovers_females = rtrim($whmcs->decryptString($item->medical_turnovers_females), "\0");
				}
				// Check if item has params, or pass whole item.
				$params = (isset($item->params) && CostbenefitprojectionHelper::checkJson($item->params)) ? json_decode($item->params) : $item;
				// Make sure the content prepare plugins fire on country_publicaddress
				$_country_publicaddress = new stdClass();
				$_country_publicaddress->text =& $item->country_publicaddress; // value must be in text
				// Since all values are now in text (Joomla Limitation), we also add the field name (country_publicaddress) to context
				$this->_dispatcher->trigger("onContentPrepare", array('com_costbenefitprojection.cpanel.country_publicaddress', &$_country_publicaddress, &$params, 0));
				// Checking if country_publicaddress has uikit components that must be loaded.
				$this->uikitComp = CostbenefitprojectionHelper::getUikitComp($item->country_publicaddress,$this->uikitComp);
				// Check if item has params, or pass whole item.
				$params = (isset($item->params) && CostbenefitprojectionHelper::checkJson($item->params)) ? json_decode($item->params) : $item;
				// Make sure the content prepare plugins fire on service_provider_publicaddress
				$_service_provider_publicaddress = new stdClass();
				$_service_provider_publicaddress->text =& $item->service_provider_publicaddress; // value must be in text
				// Since all values are now in text (Joomla Limitation), we also add the field name (service_provider_publicaddress) to context
				$this->_dispatcher->trigger("onContentPrepare", array('com_costbenefitprojection.cpanel.service_provider_publicaddress', &$_service_provider_publicaddress, &$params, 0));
				// Checking if service_provider_publicaddress has uikit components that must be loaded.
				$this->uikitComp = CostbenefitprojectionHelper::getUikitComp($item->service_provider_publicaddress,$this->uikitComp);
				// set idCompanyScaling_factorD to the $item object.
				$item->idCompanyScaling_factorD = $this->getIdCompanyScaling_factorBdef_D($item->id);
				// set idCompanyInterventionE to the $item object.
				$item->idCompanyInterventionE = $this->getIdCompanyInterventionBdef_E($item->id);
			}
		}

		// return items
		return $items;
	}

	/**
	 * Method to get an array of Scaling_factor Objects.
	 *
	 * @return mixed  An array of Scaling_factor Objects on success, false on failure.
	 *
	 */
	public function getIdCompanyScaling_factorBdef_D($id)
	{
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__costbenefitprojection_scaling_factor as d
		$query->select($db->quoteName(
			array('d.id','d.asset_id','d.company','d.causerisk','d.reference','d.yld_scaling_factor_males','d.yld_scaling_factor_females','d.mortality_scaling_factor_males','d.mortality_scaling_factor_females','d.presenteeism_scaling_factor_males','d.presenteeism_scaling_factor_females','d.published','d.checked_out','d.checked_out_time','d.created_by','d.modified_by','d.created','d.modified','d.version','d.hits','d.ordering'),
			array('id','asset_id','company','causerisk','reference','yld_scaling_factor_males','yld_scaling_factor_females','mortality_scaling_factor_males','mortality_scaling_factor_females','presenteeism_scaling_factor_males','presenteeism_scaling_factor_females','published','checked_out','checked_out_time','created_by','modified_by','created','modified','version','hits','ordering')));
		$query->from($db->quoteName('#__costbenefitprojection_scaling_factor', 'd'));
		$query->where('d.company = ' . $db->quote($id));

		// Get from #__costbenefitprojection_causerisk as f
		$query->select($db->quoteName(
			array('f.name'),
			array('causerisk_name')));
		$query->join('LEFT', ($db->quoteName('#__costbenefitprojection_causerisk', 'f')) . ' ON (' . $db->quoteName('d.causerisk') . ' = ' . $db->quoteName('f.id') . ')');
		$query->order('d.published ASC');
		$query->order('d.id ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$db->execute();

		// check if there was data returned
		if ($db->getNumRows())
		{
			// Load the JEvent Dispatcher
			JPluginHelper::importPlugin('content');
			$this->_dispatcher = JEventDispatcher::getInstance();
			$items = $db->loadObjectList();

			// Convert the parameter fields into objects.
			foreach ($items as $nr => &$item)
			{
				// Check if item has params, or pass whole item.
				$params = (isset($item->params) && CostbenefitprojectionHelper::checkJson($item->params)) ? json_decode($item->params) : $item;
				// Make sure the content prepare plugins fire on reference
				$_reference = new stdClass();
				$_reference->text =& $item->reference; // value must be in text
				// Since all values are now in text (Joomla Limitation), we also add the field name (reference) to context
				$this->_dispatcher->trigger("onContentPrepare", array('com_costbenefitprojection.cpanel.reference', &$_reference, &$params, 0));
				// Checking if reference has uikit components that must be loaded.
				$this->uikitComp = CostbenefitprojectionHelper::getUikitComp($item->reference,$this->uikitComp);
			}
			return $items;
		}
		return false;
	}

	/**
	 * Method to get an array of Intervention Objects.
	 *
	 * @return mixed  An array of Intervention Objects on success, false on failure.
	 *
	 */
	public function getIdCompanyInterventionBdef_E($id)
	{
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__costbenefitprojection_intervention as e
		$query->select($db->quoteName(
			array('e.id','e.name','e.type','e.coverage','e.duration','e.share','e.description','e.reference','e.interventions','e.intervention','e.not_required','e.published','e.checked_out','e.checked_out_time','e.created_by','e.modified_by','e.created','e.modified','e.version','e.hits','e.ordering'),
			array('id','name','type','coverage','duration','share','description','reference','interventions','intervention','not_required','published','checked_out','checked_out_time','created_by','modified_by','created','modified','version','hits','ordering')));
		$query->from($db->quoteName('#__costbenefitprojection_intervention', 'e'));
		$query->where('e.company = ' . $db->quote($id));
		$query->order('e.published ASC');
		$query->order('e.name ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$db->execute();

		// check if there was data returned
		if ($db->getNumRows())
		{
			// Load the JEvent Dispatcher
			JPluginHelper::importPlugin('content');
			$this->_dispatcher = JEventDispatcher::getInstance();
			$items = $db->loadObjectList();

			// Convert the parameter fields into objects.
			foreach ($items as $nr => &$item)
			{
				// Check if we can decode interventions
				if (CostbenefitprojectionHelper::checkJson($item->interventions))
				{
					// Decode interventions
					$item->interventions = json_decode($item->interventions, true);
				}
				// Check if item has params, or pass whole item.
				$params = (isset($item->params) && CostbenefitprojectionHelper::checkJson($item->params)) ? json_decode($item->params) : $item;
				// Make sure the content prepare plugins fire on description
				$_description = new stdClass();
				$_description->text =& $item->description; // value must be in text
				// Since all values are now in text (Joomla Limitation), we also add the field name (description) to context
				$this->_dispatcher->trigger("onContentPrepare", array('com_costbenefitprojection.cpanel.description', &$_description, &$params, 0));
				// Checking if description has uikit components that must be loaded.
				$this->uikitComp = CostbenefitprojectionHelper::getUikitComp($item->description,$this->uikitComp);
			}
			return $items;
		}
		return false;
	}


	/**
	 * Get the uikit needed components
	 *
	 * @return mixed  An array of objects on success.
	 *
	 */
	public function getUikitComp()
	{
		if (isset($this->uikitComp) && CostbenefitprojectionHelper::checkArray($this->uikitComp))
		{
			return $this->uikitComp;
		}
		return false;
	}
}
