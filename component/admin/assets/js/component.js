/**
 * javascript for component view
 */
var missingt_component = {
		
		tips: null,
		
		toggleRow : function(event) {
			var el = document.id(event.target);
			var img = new Element('img');
			var tr = el.getParent('tr');
			if (tr.hasClass('to-remove')) {
				tr.removeClass('to-remove');
				tr.getElements('input').removeProperty('disabled');
		        tr.getElements('textarea').removeProperty('disabled');
		        img.setProperty('src', 'components/com_missingt/assets/images/ok_16.png')
		           .setProperty('alt', 'click to remove')
		           .setProperty('title', Joomla.JText._('COM_MISSINGT_COMPONENT_CLICK_TO_REMOVE'));
				
			}
			else {
				tr.addClass('to-remove');
				tr.getElements('input').setProperty('disabled', 'disabled');
		        tr.getElements('textarea').setProperty('disabled', 'disabled');
		        img.setProperty('src', 'components/com_missingt/assets/images/remove.png')
		           .setProperty('alt', 'click to restore')
		           .setProperty('title', Joomla.JText._('COM_MISSINGT_COMPONENT_CLICK_TO_RESTORE'));
			}
			img.addEvent('click', missingt_component.toggleRow);
			tips.detach(el).attach(img);
			img.replaces(el);
		},
		
		initTips : function() {
			tips = new Tips('img.remove-row');
		}
};

Joomla.submitbutton = function(task)
{
	var form = document.adminForm;

	if (task == 'cancel') {
		submitform( task );
	} else if (task == 'export'){
		document.adminForm.format.value = 'raw';
		submitform( task );
	} else {
		submitform( task );
	}
};

window.addEvent('domready', function(){

	$$('.lg-refresh').addEvent('change', function(){
		document.id('mytask').value = "parse";
		document.adminForm.format.value = 'html';
		document.id('adminForm').submit(); 
	});	
	
	missingt_component.initTips();
	$$('img.remove-row').addEvent('click', missingt_component.toggleRow);
});