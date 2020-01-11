<?php
/*----------------------------------------------------------------------------------|  www.giz.de  |----/
	Deutsche Gesellschaft für International Zusammenarbeit (GIZ) Gmb 
/-------------------------------------------------------------------------------------------------------/

	@version		3.4.x
	@build			14th August, 2019
	@created		15th June, 2012
	@package		Cost Benefit Projection
	@subpackage		view.html.php
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
 * Costbenefitprojection View class for the Scaling_factors
 */
class CostbenefitprojectionViewScaling_factors extends JViewLegacy
{
	/**
	 * Scaling_factors view display method
	 * @return void
	 */
	function display($tpl = null)
	{
		if ($this->getLayout() !== 'modal')
		{
			// Include helper submenu
			CostbenefitprojectionHelper::addSubmenu('scaling_factors');
		}

		// Assign data to the view
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->user = JFactory::getUser();
		$this->listOrder = $this->escape($this->state->get('list.ordering'));
		$this->listDirn = $this->escape($this->state->get('list.direction'));
		$this->saveOrder = $this->listOrder == 'ordering';
		// set the return here value
		$this->return_here = urlencode(base64_encode((string) JUri::getInstance()));
		// get global action permissions
		$this->canDo = CostbenefitprojectionHelper::getActions('scaling_factor');
		$this->canEdit = $this->canDo->get('scaling_factor.edit');
		$this->canState = $this->canDo->get('scaling_factor.edit.state');
		$this->canCreate = $this->canDo->get('scaling_factor.create');
		$this->canDelete = $this->canDo->get('scaling_factor.delete');
		$this->canBatch = $this->canDo->get('core.batch');

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			$this->addToolbar();
			$this->sidebar = JHtmlSidebar::render();
			// load the batch html
			if ($this->canCreate && $this->canEdit && $this->canState)
			{
				$this->batchDisplay = JHtmlBatch_::render();
			}
		}
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar()
	{
		JToolBarHelper::title(JText::_('COM_COSTBENEFITPROJECTION_SCALING_FACTORS'), 'equalizer');
		JHtmlSidebar::setAction('index.php?option=com_costbenefitprojection&view=scaling_factors');
		JFormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');

		if ($this->canCreate)
		{
			JToolBarHelper::addNew('scaling_factor.add');
		}

		// Only load if there are items
		if (CostbenefitprojectionHelper::checkArray($this->items))
		{
			if ($this->canEdit)
			{
				JToolBarHelper::editList('scaling_factor.edit');
			}

			if ($this->canState)
			{
				JToolBarHelper::publishList('scaling_factors.publish');
				JToolBarHelper::unpublishList('scaling_factors.unpublish');
				JToolBarHelper::archiveList('scaling_factors.archive');

				if ($this->canDo->get('core.admin'))
				{
					JToolBarHelper::checkin('scaling_factors.checkin');
				}
			}

			// Add a batch button
			if ($this->canBatch && $this->canCreate && $this->canEdit && $this->canState)
			{
				// Get the toolbar object instance
				$bar = JToolBar::getInstance('toolbar');
				// set the batch button name
				$title = JText::_('JTOOLBAR_BATCH');
				// Instantiate a new JLayoutFile instance and render the batch button
				$layout = new JLayoutFile('joomla.toolbar.batch');
				// add the button to the page
				$dhtml = $layout->render(array('title' => $title));
				$bar->appendButton('Custom', $dhtml, 'batch');
			}

			if ($this->state->get('filter.published') == -2 && ($this->canState && $this->canDelete))
			{
				JToolbarHelper::deleteList('', 'scaling_factors.delete', 'JTOOLBAR_EMPTY_TRASH');
			}
			elseif ($this->canState && $this->canDelete)
			{
				JToolbarHelper::trash('scaling_factors.trash');
			}

			if ($this->canDo->get('core.export') && $this->canDo->get('scaling_factor.export'))
			{
				JToolBarHelper::custom('scaling_factors.exportData', 'download', '', 'COM_COSTBENEFITPROJECTION_EXPORT_DATA', true);
			}
		}

		if ($this->canDo->get('core.import') && $this->canDo->get('scaling_factor.import'))
		{
			JToolBarHelper::custom('scaling_factors.importData', 'upload', '', 'COM_COSTBENEFITPROJECTION_IMPORT_DATA', false);
		}

		// set help url for this view if found
		$help_url = CostbenefitprojectionHelper::getHelpUrl('scaling_factors');
		if (CostbenefitprojectionHelper::checkString($help_url))
		{
				JToolbarHelper::help('COM_COSTBENEFITPROJECTION_HELP_MANAGER', false, $help_url);
		}

		// add the options comp button
		if ($this->canDo->get('core.admin') || $this->canDo->get('core.options'))
		{
			JToolBarHelper::preferences('com_costbenefitprojection');
		}

		if ($this->canState)
		{
			JHtmlSidebar::addFilter(
				JText::_('JOPTION_SELECT_PUBLISHED'),
				'filter_published',
				JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true)
			);
			// only load if batch allowed
			if ($this->canBatch)
			{
				JHtmlBatch_::addListSelection(
					JText::_('COM_COSTBENEFITPROJECTION_KEEP_ORIGINAL_STATE'),
					'batch[published]',
					JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('all' => false)), 'value', 'text', '', true)
				);
			}
		}

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_ACCESS'),
			'filter_access',
			JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'))
		);

		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			JHtmlBatch_::addListSelection(
				JText::_('COM_COSTBENEFITPROJECTION_KEEP_ORIGINAL_ACCESS'),
				'batch[access]',
				JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text')
			);
		}

		// Set Causerisk Name Selection
		$this->causeriskNameOptions = JFormHelper::loadFieldType('Causesrisks')->options;
		// We do some sanitation for Causerisk Name filter
		if (CostbenefitprojectionHelper::checkArray($this->causeriskNameOptions) &&
			isset($this->causeriskNameOptions[0]->value) &&
			!CostbenefitprojectionHelper::checkString($this->causeriskNameOptions[0]->value))
		{
			unset($this->causeriskNameOptions[0]);
		}
		// Only load Causerisk Name filter if it has values
		if (CostbenefitprojectionHelper::checkArray($this->causeriskNameOptions))
		{
			// Causerisk Name Filter
			JHtmlSidebar::addFilter(
				'- Select '.JText::_('COM_COSTBENEFITPROJECTION_SCALING_FACTOR_CAUSERISK_LABEL').' -',
				'filter_causerisk',
				JHtml::_('select.options', $this->causeriskNameOptions, 'value', 'text', $this->state->get('filter.causerisk'))
			);

			if ($this->canBatch && $this->canCreate && $this->canEdit)
			{
				// Causerisk Name Batch Selection
				JHtmlBatch_::addListSelection(
					'- Keep Original '.JText::_('COM_COSTBENEFITPROJECTION_SCALING_FACTOR_CAUSERISK_LABEL').' -',
					'batch[causerisk]',
					JHtml::_('select.options', $this->causeriskNameOptions, 'value', 'text')
				);
			}
		}

		// Set Company Name Selection
		$this->companyNameOptions = JFormHelper::loadFieldType('Company')->options;
		// We do some sanitation for Company Name filter
		if (CostbenefitprojectionHelper::checkArray($this->companyNameOptions) &&
			isset($this->companyNameOptions[0]->value) &&
			!CostbenefitprojectionHelper::checkString($this->companyNameOptions[0]->value))
		{
			unset($this->companyNameOptions[0]);
		}
		// Only load Company Name filter if it has values
		if (CostbenefitprojectionHelper::checkArray($this->companyNameOptions))
		{
			// Company Name Filter
			JHtmlSidebar::addFilter(
				'- Select '.JText::_('COM_COSTBENEFITPROJECTION_SCALING_FACTOR_COMPANY_LABEL').' -',
				'filter_company',
				JHtml::_('select.options', $this->companyNameOptions, 'value', 'text', $this->state->get('filter.company'))
			);

			if ($this->canBatch && $this->canCreate && $this->canEdit)
			{
				// Company Name Batch Selection
				JHtmlBatch_::addListSelection(
					'- Keep Original '.JText::_('COM_COSTBENEFITPROJECTION_SCALING_FACTOR_COMPANY_LABEL').' -',
					'batch[company]',
					JHtml::_('select.options', $this->companyNameOptions, 'value', 'text')
				);
			}
		}
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		if (!isset($this->document))
		{
			$this->document = JFactory::getDocument();
		}
		$this->document->setTitle(JText::_('COM_COSTBENEFITPROJECTION_SCALING_FACTORS'));
		$this->document->addStyleSheet(JURI::root() . "administrator/components/com_costbenefitprojection/assets/css/scaling_factors.css", (CostbenefitprojectionHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/css');
	}

	/**
	 * Escapes a value for output in a view script.
	 *
	 * @param   mixed  $var  The output to escape.
	 *
	 * @return  mixed  The escaped value.
	 */
	public function escape($var)
	{
		if(strlen($var) > 50)
		{
			// use the helper htmlEscape method instead and shorten the string
			return CostbenefitprojectionHelper::htmlEscape($var, $this->_charset, true);
		}
		// use the helper htmlEscape method instead.
		return CostbenefitprojectionHelper::htmlEscape($var, $this->_charset);
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 */
	protected function getSortFields()
	{
		return array(
			'a.sorting' => JText::_('JGRID_HEADING_ORDERING'),
			'a.published' => JText::_('JSTATUS'),
			'g.name' => JText::_('COM_COSTBENEFITPROJECTION_SCALING_FACTOR_CAUSERISK_LABEL'),
			'h.name' => JText::_('COM_COSTBENEFITPROJECTION_SCALING_FACTOR_COMPANY_LABEL'),
			'a.yld_scaling_factor_males' => JText::_('COM_COSTBENEFITPROJECTION_SCALING_FACTOR_YLD_SCALING_FACTOR_MALES_LABEL'),
			'a.yld_scaling_factor_females' => JText::_('COM_COSTBENEFITPROJECTION_SCALING_FACTOR_YLD_SCALING_FACTOR_FEMALES_LABEL'),
			'a.mortality_scaling_factor_males' => JText::_('COM_COSTBENEFITPROJECTION_SCALING_FACTOR_MORTALITY_SCALING_FACTOR_MALES_LABEL'),
			'a.mortality_scaling_factor_females' => JText::_('COM_COSTBENEFITPROJECTION_SCALING_FACTOR_MORTALITY_SCALING_FACTOR_FEMALES_LABEL'),
			'a.presenteeism_scaling_factor_males' => JText::_('COM_COSTBENEFITPROJECTION_SCALING_FACTOR_PRESENTEEISM_SCALING_FACTOR_MALES_LABEL'),
			'a.presenteeism_scaling_factor_females' => JText::_('COM_COSTBENEFITPROJECTION_SCALING_FACTOR_PRESENTEEISM_SCALING_FACTOR_FEMALES_LABEL'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
