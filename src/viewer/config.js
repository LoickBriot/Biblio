
(function() {
	
	var word = window.ControlUtils.getCustomData();
	
	
	$.extend(ReaderControl.config, {
        //configuration options go here
        customScript : 'defaultScriptExtension.js',
        ui:{
            hideZoom : false
        }
    });
	
	
    //=========================================================
    // Load a custom script for the "about" page
    //=========================================================
	function search(text){
		searchString = text.toLowerCase();
		readerControl.fullTextSearch(searchString);
	}
		
    
	
	
	$('<ul>').addClass('ui-widget ui-menu-dropdown').attr('id', 'optionsMenuList').hide()
		.append("<li data-lang='fr'><a href=\"javascript:void(0)\">Français</a></li>")
        .append("<li data-lang='en'><a href=\"javascript:void(0)\">English</a></li>")
        .menu({
            select: function(event, ui) {
                var languageCode = $(ui.item).data('lang');
                i18n.setLng(languageCode, function() {
                    $('body').i18n();
                });
            }
        })
        .appendTo('body');
    
    var rightAlignedElements = $('#control .right-aligned');
    var container = $('<div>').addClass('group');
    rightAlignedElements.prepend(container);
    
    var button = $('<span>')
        .addClass('glyphicons flag')
        .on('click', function() {
            var menu = $('#optionsMenuList');
            if (menu.data("isOpen")) {
                menu.hide();
                menu.data("isOpen", false);
            } else {
                menu.show().position({
                    my: "left top",
                    at: "left bottom",
                    of: this
                });
                       
                $(document).one( "click", function() {
                    menu.hide();
                    menu.data("isOpen", false);
                });
                menu.data("isOpen", true);
            }
            return false;
        });

    container.append(button);
	
	$(document).on('viewerLoaded', function() {
		i18n.setLng("fr", function() {
            $('body').i18n();
        });
    }); 
	
	$(document).on('documentLoaded', function() {
		search(word);
    });  

	
	
	
})();