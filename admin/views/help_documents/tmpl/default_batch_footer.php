<?php
/*----------------------------------------------------------------------------------|  www.giz.de  |----/
	Deutsche Gesellschaft für International Zusammenarbeit (GIZ) Gmb 
/-------------------------------------------------------------------------------------------------------/

	@version		3.4.x
	@build			14th August, 2019
	@created		15th June, 2012
	@package		Cost Benefit Projection
	@subpackage		default_batch_footer.php
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
<!-- clear the batch values if cancel -->
<button class="btn" type="button" onclick="" data-dismiss="modal">
	<?php echo JText::_('JCANCEL'); ?>
</button>
<!-- post the batch values if process -->
<button class="btn btn-success" type="submit" onclick="Joomla.submitbutton('help_document.batch');">
	<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
</button>