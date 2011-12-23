/**
 * javascript for file view
 */
google.load("language", "1");

window.addEvent('domready', function(){

    $$('.mtcopy').addEvent('click', function(event){
        elcopy(event.target);
    });
    
    $$('.mtgtranslate').addEvent('click', function(event){
        //elgtranslate(event.target);
        //modified by Eddy
        elmstranslate(event.target);
    });
    
    var ids = [];
    var result_txts = [];
});

Joomla.submitbutton = function(task){
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
        
        
            ids = [];
            var txts = [];
            
            $$('.mtcopy').each(function(element){
                //elgtranslate(element);
                //modified by Eddy
                elmstranslate(element);
                
                
            });
            
        }
        return;
    }
    if (task == 'cancel') {
        submitform(task);
    }
    else 
        if (task == 'export') {
            document.adminForm.format.value = 'raw';
            submitform(task);
        }
        else {
            submitform(task);
        }
};

function elcopy(element){
    var tr = document.id(element).getParent().getParent();
    tr.getElement('.dest').value = tr.getElement('.src').innerHTML;
}

function elgtranslate(element){
    var tr = document.id(element).getParent().getParent();
    var content = tr.getElement('.src').innerHTML;
    google.language.translate(content, document.id('mtfrom').get('value').substr(0, 2), document.id('mtto').get('value').substr(0, 2), function(result){
        if (!result.error) {
            var div = new Element('div').set('html', result.translation); // trick to convert back html entities
            tr.getElement('.dest').value = div.innerHTML;
        }
    });
}

//added by Eddy for using Microsoft Translator Api
function elmstranslate(element){

    var tr = document.id(element).getParent().getParent();
    var content = tr.getElement('.src').innerHTML;
    
    
    var td_id = tr.getElementsByTagName("td")[0];
    var id_val = td_id.childNodes[0];
    var id = id_val.nodeValue;
    
    
    //lang code need to correct
    //more info:http://www.emreakkas.com/internationalization/microsoft-translator-api-languages-list-language-codes-and-names
    var languageFrom = document.id('mtfrom').get('value').substr(0, 2);
    var languageTo_origin = document.id('mtto').get('value');
    var languageTo = '';
    
    console.log(document.id('mtto').get('value'));
    
    //corret the lang code for Chinese
    if (languageTo_origin == 'zh-TW') {
        languageTo = 'zh-CHT';
    }
    else 
        if (languageTo_origin == 'zh-CN') {
            languageTo = 'zh-CHS';
        }
        else {
            languageTo = document.id('mtto').get('value').substr(0, 2);
        }
    
    
    //the id need to setup first (in options ?)
    //and regist at http://www.bing.com/developers/appids.aspx
    var myAppId = '';
    
    var el = document.createElement("script");
    el.src = 'http://api.microsofttranslator.com/V2/Ajax.svc/Translate';
    el.src += '?oncomplete=MicrosoftTranslateComplete' + id;
    el.src += '&appId=' + myAppId;
    el.src += '&text=' + escape(content);
    el.src += '&from=' + languageFrom + '&to=' + languageTo;
    
    document.getElementsByTagName('head')[0].appendChild(el);
    
    //use dymanic callback functions for multi string translate
    var code = "window.MicrosoftTranslateComplete" + id + " = function(result){var div = new Element('div').set('html', result);tr.getElement('.dest').value = div.innerHTML;}";
    eval(code);
    
    
}



