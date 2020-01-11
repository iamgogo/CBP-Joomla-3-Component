<?php
/*----------------------------------------------------------------------------------|  www.giz.de  |----/
	Deutsche Gesellschaft für International Zusammenarbeit (GIZ) Gmb 
/-------------------------------------------------------------------------------------------------------/

	@version		3.4.x
	@build			14th August, 2019
	@created		15th June, 2012
	@package		Cost Benefit Projection
	@subpackage		default.php
	@author			Llewellyn van der Merwe <http://www.vdm.io>	
	@owner			Deutsche Gesellschaft für International Zusammenarbeit (GIZ) Gmb
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html
	
/-------------------------------------------------------------------------------------------------------/
	Cost Benefit Projection Tool.
/------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.tooltip');

?>
<div id="j-main-container">
	<div class="form-horizontal">
	<?php echo JHtml::_('bootstrap.startTabSet', 'cpanel_tab', array('active' => 'cpanel')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'cpanel_tab', 'cpanel', JText::_('cPanel', true)); ?>
		<div class="row-fluid">
			<div class="span9">
				<?php echo JHtml::_('bootstrap.startAccordion', 'dashboard_left', array('active' => 'main')); ?>
					<?php echo JHtml::_('bootstrap.addSlide', 'dashboard_left', 'Control Panel', 'main'); ?>
						<?php echo $this->loadTemplate('main');?>
					<?php echo JHtml::_('bootstrap.endSlide'); ?>
				<?php echo JHtml::_('bootstrap.endAccordion'); ?>
			</div>
			<div class="span3">
				<?php echo JHtml::_('bootstrap.startAccordion', 'dashboard_right', array('active' => 'vdm')); ?>
					<?php echo JHtml::_('bootstrap.addSlide', 'dashboard_right', 'Deutsche Gesellschaft für International Zusammenarbeit (GIZ) Gmb', 'vdm'); ?>
						<?php echo $this->loadTemplate('vdm');?>
					<?php echo JHtml::_('bootstrap.endSlide'); ?>
				<?php echo JHtml::_('bootstrap.endAccordion'); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'cpanel_tab', 'usage_statistics', JText::_('Usage Statistics', true)); ?>
		<div class="row-fluid">
			<div class="span12">
				<?php  echo JHtml::_('bootstrap.startAccordion', 'usage_statistics_accordian', array('active' => 'usage_statistics_one')); ?>
					<?php  echo JHtml::_('bootstrap.addSlide', 'usage_statistics_accordian', 'Table', 'usage_statistics_one'); ?>
						<?php echo $this->loadTemplate('usage_statistics_table');?>
					<?php  echo JHtml::_('bootstrap.endSlide'); ?>
				<?php  echo JHtml::_('bootstrap.endAccordion'); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'cpanel_tab', 'open_issues', JText::_('Open Issues', true)); ?>
		<div class="row-fluid">
			<div class="span12">
				<?php  echo JHtml::_('bootstrap.startAccordion', 'open_issues_accordian', array('active' => 'open_issues_one')); ?>
					<?php  echo JHtml::_('bootstrap.addSlide', 'open_issues_accordian', 'The open issues on GitHub', 'open_issues_one'); ?>
						<?php echo $this->loadTemplate('open_issues_the_open_issues_on_github');?>
					<?php  echo JHtml::_('bootstrap.endSlide'); ?>
				<?php  echo JHtml::_('bootstrap.endAccordion'); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'cpanel_tab', 'closed_issues', JText::_('Closed Issues', true)); ?>
		<div class="row-fluid">
			<div class="span12">
				<?php  echo JHtml::_('bootstrap.startAccordion', 'closed_issues_accordian', array('active' => 'closed_issues_one')); ?>
					<?php  echo JHtml::_('bootstrap.addSlide', 'closed_issues_accordian', 'The closed issues on GitHub', 'closed_issues_one'); ?>
						<?php echo $this->loadTemplate('closed_issues_the_closed_issues_on_github');?>
					<?php  echo JHtml::_('bootstrap.endSlide'); ?>
				<?php  echo JHtml::_('bootstrap.endAccordion'); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'cpanel_tab', 'readme', JText::_('Readme', true)); ?>
		<div class="row-fluid">
			<div class="span12">
				<?php  echo JHtml::_('bootstrap.startAccordion', 'readme_accordian', array('active' => 'readme_one')); ?>
					<?php  echo JHtml::_('bootstrap.addSlide', 'readme_accordian', 'Information', 'readme_one'); ?>
						<?php echo $this->loadTemplate('readme_information');?>
					<?php  echo JHtml::_('bootstrap.endSlide'); ?>
				<?php  echo JHtml::_('bootstrap.endAccordion'); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'cpanel_tab', 'vast_development_method', JText::_('Vast Development Method', true)); ?>
		<div class="row-fluid">
			<div class="span12">
				<?php  echo JHtml::_('bootstrap.startAccordion', 'vast_development_method_accordian', array('active' => 'vast_development_method_one')); ?>
					<?php  echo JHtml::_('bootstrap.addSlide', 'vast_development_method_accordian', 'Notice Board', 'vast_development_method_one'); ?>
						<?php echo $this->loadTemplate('vast_development_method_notice_board');?>
					<?php  echo JHtml::_('bootstrap.endSlide'); ?>
				<?php  echo JHtml::_('bootstrap.endAccordion'); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

	<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</div>
</div>