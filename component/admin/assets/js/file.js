/**
 * javascript for file view
 */

google.load("language", "1");

window.addEvent('domready', function(){

	$$('.mtcopy').addEvent('click', function(event){
		elcopy(event.target);
	});

	$$('.mtgtranslate').addEvent('click', function(event){
		elgtranslate(event.target);
	});
});

Joomla.submitbutton = function(task)
{
	var form = document.adminForm;

	if (task == 'copyall') {
		if (confirm(Joomla.JText._('COM_MISSINGT_CONFIRM_COPYALL'))) {
			$$('.mtcopy').each(function(element){
				elcopy(element);
			});
		}
		return;
	}
	if (task == 'googleall') {
		if (confirm(Joomla.JText._('COM_MISSINGT_CONFIRM_GOOGLEALL'))) {
			$$('.mtcopy').each(function(element){
				elgtranslate(element);
			});
		}
		return;
	}
	if (task == 'cancel') {
		submitform( task );
	} else if (task == 'export'){
		document.adminForm.format.value = 'raw';
		submitform( task );
	} else {
		submitform( task );
	}
};

function elcopy(element)
{
	var tr = document.id(element).getParent().getParent();
	tr.getElement('.dest').value = tr.getElement('.src').innerHTML;
}

function elgtranslate(element)
{
	var tr = document.id(element).getParent().getParent();
	var content = tr.getElement('.src').innerHTML;
	google.language.translate(content, document.id('mtfrom').get('value').substr(0,2), document.id('mtto').get('value').substr(0,2), function(result) {
		  if (!result.error) {
			  var div = new Element('div').set('html', result.translation); // trick to convert back html entities
			  tr.getElement('.dest').value = div.innerHTML;
		  }
		});
}