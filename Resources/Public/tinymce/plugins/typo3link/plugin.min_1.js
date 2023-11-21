
var plugin = tinymce.PluginManager.add('typo3link', function (editor, url) {

    var openLinkDialog = function () {
        var selectedElement = editor.selection.getNode();
        var element = editor.dom.getParent(selectedElement, 'a[href]');

        var additionalParameter = '';
        if (element) {
            additionalParameter = '&curUrl[url]=' + encodeURIComponent(element.getAttribute('href'));
            if (element.target) {
                additionalParameter += '&curUrl[target]=' + encodeURIComponent(element.target);
            }
            if (element.className) {
                additionalParameter += '&curUrl[class]=' + encodeURIComponent(element.className);
            }
            if (element.title) {
                additionalParameter += '&curUrl[title]=' + encodeURIComponent(element.title);
            }
        }
     
        Ted3.browser.link("",function (link) {
            var attributes = link.attributeValues;
            var curTitle = attributes.title ? attributes.title : '';
            var curClass = attributes.class ? attributes.class : '';
            var curTarget = attributes.target ? attributes.target : '';
            var curParams = attributes.params ? attributes.params : '';
            delete attributes;

           // console.log(link);
            // replace page: prefix
//            if (link.url.indexOf('page:') === 0) {
//                link.url = link.url.substr(5);
//            }
            //@todo > bereits in Linkbrowser
//            plugin.createLink(
//                    link.url + curParams,
//                    curTarget,
//                    curClass,
//                    curTitle,
//                    attributes
//                    );


            //
           // alert( link.url);
            Ted3.ajax({
                 url: Ted3.urls.link,
                 data : {
                     'typolink' : link.url
                 }
            }).done(function(data){
               console.log(data);
                if(data){
                        plugin.createLink(
                    data + curParams,
                    curTarget,
                    curClass,
                    curTitle,
                    attributes
                    ); 
                }else{
                    alert("Link konnte nicht gesetzt werden");
                   // alert("fd");
                      // plugin.createLink("","","","","");
                }
            
            })

        });
    };

    // add the buttons
    editor.addButton('typo3link', {
        title: 'TYPO3 Link',
        icon: 'link',
        shortcut: 'Ctrl+K',
        onclick: openLinkDialog,
        stateSelector: 'a[href]'
    });

    editor.addButton('unlink', {
        title: 'Unlink',
        icon: 'unlink',
        shortcut: 'Ctrl+M',
        cmd: 'unlink',
        stateSelector: 'a[href]'
    });


    // add the menu entries
    editor.addMenuItem('unlink', {
        text: 'Unlink',
        context: 'insert',
        prependToContext: true,
        shortcut: 'Ctrl+M',
        icon: 'unlink',
        cmd: 'unlink',
        stateSelector: 'a[href]'
    });

    editor.addMenuItem('typo3link', {
        text: 'TYPO3 Link',
        context: 'insert',
        prependToContext: true,
        shortcut: 'Ctrl+K',
        icon: 'link',
        onclick: openLinkDialog,
        stateSelector: 'a[href]'
    });


    // initialize the shortcuts
    editor.addShortcut('Ctrl+K', '', openLinkDialog);
    editor.addShortcut('Ctrl+M', '', 'unlink');
    //editor.addShortcut('Ctrl+L', '', openImageDialog);
});

/**
 * Renders a link
 *
 * @param {string} href
 * @param {string} target
 * @param {string} cssClass
 * @param {string} title
 * @param {object} additionalValues currently unused
 * @return {void}
 */
plugin.createLink = function (href, target, cssClass, title, additionalValues) {
    var linkAttrs = {
        href: href,
        target: target ? target : null,
        class: cssClass ? cssClass : null,
        title: title ? title : null,
        'data-htmlarea-external': null
    };

    for (var index in additionalValues) {
        if (additionalValues.hasOwnProperty(index)) {
            linkAttrs[index] = additionalValues[index];
        }
    }

    var selectedElement = tinymce.activeEditor.selection.getNode();
    var element = tinymce.activeEditor.dom.getParent(selectedElement, 'a[href]');
    tinymce.activeEditor.focus();
    if (element) {
        tinymce.activeEditor.dom.setAttribs(element, linkAttrs);
    } else {
        tinymce.activeEditor.execCommand('mceInsertLink', false, linkAttrs);
    }
    tinymce.activeEditor.selection.collapse();
    tinymce.activeEditor.undoManager.add();

    //   tinymce.activeEditor.windowManager.getWindows()[0].close();
};

/**
 * Unlinks the current selection
 *
 * @return {void}
 */
plugin.unLink = function () {
    tinymce.activeEditor.execCommand('unlink');
    tinymce.activeEditor.windowManager.getWindows()[0].close();
};


/**
 * Just returns null
 *
 * @returns {null}
 */
plugin.getButton = function () {
    return null;
};

/**
 * Closes the current open dialog
 *
 * @return {void}
 */
plugin.close = function () {
    tinymce.activeEditor.windowManager.getWindows()[0].close();
};