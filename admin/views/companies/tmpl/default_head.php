<?php
/*----------------------------------------------------------------------------------|  www.giz.de  |----/
	Deutsche Gesellschaft für International Zusammenarbeit (GIZ) Gmb 
/-------------------------------------------------------------------------------------------------------/

	@version		3.4.x
	@build			14th August, 2019
	@created		15th June, 2012
	@package		Cost Benefit Projection
	@subpackage		default_head.php
	@author			Llewellyn van der Merwe <http://www.vdm.io>	
	@owner			Deutsche Gesellschaft für International Zusammenarbeit (GIZ) Gmb
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html
	
/-------------------------------------------------------------------------------------------------------/
	Cost Benefit Projection Tool.
/------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

?>
<tr>
	<?php if ($this->canEdit&& $this->canState): ?>
		<th width="1%" class="nowrap center hidden-phone">
			<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'ordering', $this->listDirn, $this->listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
		</th>
		<th width="20" class="nowrap center">
			<?php echo JHtml::_('grid.checkall'); ?>
		</th>
	<?php else: ?>
		<th width="20" class="nowrap center hidden-phone">
			&#9662;
		</th>
		<th width="20" class="nowrap center">
			&#9632;
		</th>
	<?php endif; ?>
	<th class="nowrap" >
			<?php echo JHtml::_('grid.sort', 'COM_COSTBENEFITPROJECTION_COMPANY_NAME_LABEL', 'name', $this->listDirn, $this->listOrder); ?>
	</th>
	<th class="nowrap" >
			<?php echo JHtml::_('grid.sort', 'COM_COSTBENEFITPROJECTION_COMPANY_USER_LABEL', 'user_name', $this->listDirn, $this->listOrder); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo JHtml::_('grid.sort', 'COM_COSTBENEFITPROJECTION_COMPANY_DEPARTMENT_LABEL', 'department', $this->listDirn, $this->listOrder); ?>
	</th>
	<th class="nowrap" >
			<?php echo JHtml::_('grid.sort', 'COM_COSTBENEFITPROJECTION_COMPANY_COUNTRY_LABEL', 'country_name', $this->listDirn, $this->listOrder); ?>
	</th>
	<th class="nowrap" >
			<?php echo JHtml::_('grid.sort', 'COM_COSTBENEFITPROJECTION_COMPANY_SERVICE_PROVIDER_LABEL', 'service_provider_user', $this->listDirn, $this->listOrder); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo JHtml::_('grid.sort', 'COM_COSTBENEFITPROJECTION_COMPANY_PER_LABEL', 'per', $this->listDirn, $this->listOrder); ?>
	</th>
	<?php if ($this->canState): ?>
		<th width="10" class="nowrap center" >
			<?php echo JHtml::_('grid.sort', 'COM_COSTBENEFITPROJECTION_COMPANY_STATUS', 'published', $this->listDirn, $this->listOrder); ?>
		</th>
	<?php else: ?>
		<th width="10" class="nowrap center" >
			<?php echo JText::_('COM_COSTBENEFITPROJECTION_COMPANY_STATUS'); ?>
		</th>
	<?php endif; ?>
	<th width="5" class="nowrap center hidden-phone" >
			<?php echo JHtml::_('grid.sort', 'COM_COSTBENEFITPROJECTION_COMPANY_ID', 'id', $this->listDirn, $this->listOrder); ?>
	</th>
</tr>