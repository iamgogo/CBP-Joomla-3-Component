<?php
/*----------------------------------------------------------------------------------|  www.giz.de  |----/
	Deutsche Gesellschaft für International Zusammenarbeit (GIZ) Gmb 
/-------------------------------------------------------------------------------------------------------/

	@version		3.4.x
	@build			14th August, 2019
	@created		15th June, 2012
	@package		Cost Benefit Projection
	@subpackage		service_providers.php
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
 * Service_providers Model
 */
class CostbenefitprojectionModelService_providers extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
        {
			$config['filter_fields'] = array(
				'a.id','id',
				'a.published','published',
				'a.ordering','ordering',
				'a.created_by','created_by',
				'a.modified_by','modified_by',
				'a.user','user',
				'a.country','country',
				'a.publicname','publicname',
				'a.publicemail','publicemail',
				'a.publicnumber','publicnumber'
			);
		}

		parent::__construct($config);
	}
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * @return  void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->get('layout'))
		{
			$this->context .= '.' . $layout;
		}
		$user = $this->getUserStateFromRequest($this->context . '.filter.user', 'filter_user');
		$this->setState('filter.user', $user);

		$country = $this->getUserStateFromRequest($this->context . '.filter.country', 'filter_country');
		$this->setState('filter.country', $country);

		$publicname = $this->getUserStateFromRequest($this->context . '.filter.publicname', 'filter_publicname');
		$this->setState('filter.publicname', $publicname);

		$publicemail = $this->getUserStateFromRequest($this->context . '.filter.publicemail', 'filter_publicemail');
		$this->setState('filter.publicemail', $publicemail);

		$publicnumber = $this->getUserStateFromRequest($this->context . '.filter.publicnumber', 'filter_publicnumber');
		$this->setState('filter.publicnumber', $publicnumber);
        
		$sorting = $this->getUserStateFromRequest($this->context . '.filter.sorting', 'filter_sorting', 0, 'int');
		$this->setState('filter.sorting', $sorting);
        
		$access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access', 0, 'int');
		$this->setState('filter.access', $access);
        
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);
        
		$created_by = $this->getUserStateFromRequest($this->context . '.filter.created_by', 'filter_created_by', '');
		$this->setState('filter.created_by', $created_by);

		$created = $this->getUserStateFromRequest($this->context . '.filter.created', 'filter_created');
		$this->setState('filter.created', $created);

		// List state information.
		parent::populateState($ordering, $direction);
	}
	
	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getItems()
	{
		// check in items
		$this->checkInNow();

		// load parent items
		$items = parent::getItems();

		// set values to display correctly.
		if (CostbenefitprojectionHelper::checkArray($items))
		{
			foreach ($items as $nr => &$item)
			{
				$access = (JFactory::getUser()->authorise('service_provider.access', 'com_costbenefitprojection.service_provider.' . (int) $item->id) && JFactory::getUser()->authorise('service_provider.access', 'com_costbenefitprojection'));
				if (!$access)
				{
					unset($items[$nr]);
					continue;
				}

			}
		}
        
		// return items
		return $items;
	}
	
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 */
	protected function getListQuery()
	{
		// Get the user object.
		$user = JFactory::getUser();
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select('a.*');

		// From the costbenefitprojection_item table
		$query->from($db->quoteName('#__costbenefitprojection_service_provider', 'a'));

		// Filter the providers (admin sees all)
		if (!$user->authorise('core.options', 'com_costbenefitprojection'))
		{
			$serviceProviders = CostbenefitprojectionHelper::hisServiceProviders($user->id);
			if (CostbenefitprojectionHelper::checkArray($serviceProviders))
			{
				$serviceProviders = implode(',',$serviceProviders);
				// only load this users service providers
				$query->where('a.id IN (' . $serviceProviders . ')');
			}
			else
			{
				// don't allow user to see any service providers
				$query->where('a.id = -4');
			}
		}

		// From the users table.
		$query->select($db->quoteName('g.name','user_name'));
		$query->join('LEFT', $db->quoteName('#__users', 'g') . ' ON (' . $db->quoteName('a.user') . ' = ' . $db->quoteName('g.id') . ')');

		// From the costbenefitprojection_country table.
		$query->select($db->quoteName('h.name','country_name'));
		$query->join('LEFT', $db->quoteName('#__costbenefitprojection_country', 'h') . ' ON (' . $db->quoteName('a.country') . ' = ' . $db->quoteName('h.id') . ')');

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published))
		{
			$query->where('a.published = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.published = 0 OR a.published = 1)');
		}
		// Filter by search.
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . $db->escape($search) . '%');
				$query->where('(a.user LIKE '.$search.' OR g.name LIKE '.$search.' OR a.country LIKE '.$search.' OR h.name LIKE '.$search.' OR a.publicname LIKE '.$search.' OR a.publicemail LIKE '.$search.' OR a.publicnumber LIKE '.$search.' OR a.publicaddress LIKE '.$search.')');
			}
		}

		// Filter by country.
		if ($country = $this->getState('filter.country'))
		{
			$query->where('a.country = ' . $db->quote($db->escape($country)));
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'a.id');
		$orderDirn = $this->state->get('list.direction', 'asc');	
		if ($orderCol != '')
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Method to get list export data.
	 *
	 * @return mixed  An array of data items on success, false on failure.
	 */
	public function getExportData($pks)
	{
		// setup the query
		if (CostbenefitprojectionHelper::checkArray($pks))
		{
			// Set a value to know this is exporting method.
			$_export = true;
			// Get the user object.
			$user = JFactory::getUser();
			// Create a new query object.
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);

			// Select some fields
			$query->select('a.*');

			// From the costbenefitprojection_service_provider table
			$query->from($db->quoteName('#__costbenefitprojection_service_provider', 'a'));
			$query->where('a.id IN (' . implode(',',$pks) . ')');

			// Filter the providers (admin sees all)
		if (!$user->authorise('core.options', 'com_costbenefitprojection'))
		{
			$serviceProviders = CostbenefitprojectionHelper::hisServiceProviders($user->id);
			if (CostbenefitprojectionHelper::checkArray($serviceProviders))
			{
				$serviceProviders = implode(',',$serviceProviders);
				// only load this users service providers
				$query->where('a.id IN (' . $serviceProviders . ')');
			}
			else
			{
				// don't allow user to see any service providers
				$query->where('a.id = -4');
			}
		}

			// Order the results by ordering
			$query->order('a.ordering  ASC');

			// Load the items
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows())
			{
				$items = $db->loadObjectList();

				// set values to display correctly.
				if (CostbenefitprojectionHelper::checkArray($items))
				{
					foreach ($items as $nr => &$item)
					{
						$access = (JFactory::getUser()->authorise('service_provider.access', 'com_costbenefitprojection.service_provider.' . (int) $item->id) && JFactory::getUser()->authorise('service_provider.access', 'com_costbenefitprojection'));
						if (!$access)
						{
							unset($items[$nr]);
							continue;
						}

						// unset the values we don't want exported.
						unset($item->asset_id);
						unset($item->checked_out);
						unset($item->checked_out_time);
					}
				}
				// Add headers to items array.
				$headers = $this->getExImPortHeaders();
				if (CostbenefitprojectionHelper::checkObject($headers))
				{
					array_unshift($items,$headers);
				}
				return $items;
			}
		}
		return false;
	}

	/**
	* Method to get header.
	*
	* @return mixed  An array of data items on success, false on failure.
	*/
	public function getExImPortHeaders()
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		// get the columns
		$columns = $db->getTableColumns("#__costbenefitprojection_service_provider");
		if (CostbenefitprojectionHelper::checkArray($columns))
		{
			// remove the headers you don't import/export.
			unset($columns['asset_id']);
			unset($columns['checked_out']);
			unset($columns['checked_out_time']);
			$headers = new stdClass();
			foreach ($columns as $column => $type)
			{
				$headers->{$column} = $column;
			}
			return $headers;
		}
		return false;
	}
	
	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * @return  string  A store id.
	 *
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.id');
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.published');
		$id .= ':' . $this->getState('filter.ordering');
		$id .= ':' . $this->getState('filter.created_by');
		$id .= ':' . $this->getState('filter.modified_by');
		$id .= ':' . $this->getState('filter.user');
		$id .= ':' . $this->getState('filter.country');
		$id .= ':' . $this->getState('filter.publicname');
		$id .= ':' . $this->getState('filter.publicemail');
		$id .= ':' . $this->getState('filter.publicnumber');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to checkin all items left checked out longer then a set time.
	 *
	 * @return  a bool
	 *
	 */
	protected function checkInNow()
	{
		// Get set check in time
		$time = JComponentHelper::getParams('com_costbenefitprojection')->get('check_in');

		if ($time)
		{

			// Get a db connection.
			$db = JFactory::getDbo();
			// reset query
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from($db->quoteName('#__costbenefitprojection_service_provider'));
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows())
			{
				// Get Yesterdays date
				$date = JFactory::getDate()->modify($time)->toSql();
				// reset query
				$query = $db->getQuery(true);

				// Fields to update.
				$fields = array(
					$db->quoteName('checked_out_time') . '=\'0000-00-00 00:00:00\'',
					$db->quoteName('checked_out') . '=0'
				);

				// Conditions for which records should be updated.
				$conditions = array(
					$db->quoteName('checked_out') . '!=0', 
					$db->quoteName('checked_out_time') . '<\''.$date.'\''
				);

				// Check table
				$query->update($db->quoteName('#__costbenefitprojection_service_provider'))->set($fields)->where($conditions); 

				$db->setQuery($query);

				$db->execute();
			}
		}

		return false;
	}
}
