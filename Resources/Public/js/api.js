Ted3 = {};
Ted3.jQuery = jQuery.noConflict(true);
(function ($) {
    Ted3 = {
        jQuery: $,
        tinymce4: {
            //invalid_elements : "strong,em",
            convert_urls: false,
            schema: "html5",
            entity_encoding: "raw",
//            relative_urls : false,
//            remove_script_host: false,
            extended_valid_elements: '@[href|class|id|itemscope|itemtype|itemprop|rel|target|style|title],div,span,p,a',
            //custom_elements : 'mycustomblock,~mycustominline' // Notice the ~ prefix to force a span element for the element
            block_formats: 'p,div,h1,h2,h3,h4,h5',
            paste_auto_cleanup_on_paste: true,
            paste_remove_spans: true,
            paste_remove_styles: true,
            remove_linebreaks: true,
            forced_root_block: 'p',
            menubar: '',
            plugins: [
                'advlist autolink lists typo3link charmap visualblocks anchor nonbreaking',
                'searchreplace visualblocks code template',
                'insertdatetime table paste'
            ], //preview,save
            toolbar: [
                'undo redo |  styleselect | formatselect | charmap | visualblocks | code | pastetext | bold italic underline |  alignleft aligncenter alignright alignjustify | bullist numlist  | table | typo3link unlink '
            ],
//            spellchecker_language: 'de_DE',
            auto_focus: false,
            style_formats: [
                {title: 'Ted3 Dummy Class', selector: '*', styles: {'classes': 'ted3-tinystyle-dummy'}}
            ],
            block_formats: 'Paragraph (p)=p;Header 1=h1;Header 2=h2;Header 3=h3;Header 4=h4',
        },
        belang: 'default',
        reload: function () {
            Ted3.root.ted3root('reload');
        },
        findOverflowHiddenParent: function (element, sourcelement) {
            var sourcelement = sourcelement || element;
            if (element.length > 0) {
                if (element.offset().top <= sourcelement.offset().top - 25) {
                    return false;
                }
                if (element.css('overflow') == "hidden" && (element.css('height') || element.css('max-height'))) {

                    return element;
                } else {
                    if (element.parent()[0].localName == 'body') {
                        return false;
                    }
                    return this.findOverflowHiddenParent(element.parent(), sourcelement);
                }
            } else {
                return false;
            }
        },
        urls: {
            contentelements: '?type=4456&tx_ted3_fe[action]=route&tx_ted3_fe[controller]=Backend&route=ted3_new_content_element_wizard&returnUrl=closeWindow',
            filebrowser: '?type=4456&tx_ted3_fe[action]=route&tx_ted3_fe[controller]=Backend&route=wizard_element_browser&mode=filefe&returnUrl=closeWindow',
            linkbrowser: '?type=4456&tx_ted3_fe[action]=route&tx_ted3_fe[controller]=Backend&route=ted3_wizard_browse_links&returnUrl=closeWindow',
            createcontent: '?type=4455&tx_ted3_fe[action]=createcontent&tx_ted3_fe[controller]=Crud',
            tce: '?type=4455&tx_ted3_fe[action]=tce&tx_ted3_fe[controller]=Crud',
            settings: '?type=4455&tx_ted3_fe[action]=settings&tx_ted3_fe[controller]=Crud',
            translate: '?type=4455&tx_ted3_fe[action]=translate&tx_ted3_fe[controller]=Crud',
            delete: '?type=4455&tx_ted3_fe[action]=delete&tx_ted3_fe[controller]=Crud',
            copycontent: '?type=4455&tx_ted3_fe[action]=copycontent&tx_ted3_fe[controller]=Crud',
            movecontent: '?type=4455&tx_ted3_fe[action]=movecontent&tx_ted3_fe[controller]=Crud',
            closewindow: '/index.php?id=1&type=4457',
            t3edit: '?type=4456&tx_ted3_fe[action]=route&tx_ted3_fe[controller]=Backend&route=record_edit&table=',
            link: '?type=4456&tx_ted3_fe[action]=link&tx_ted3_fe[controller]=Backend'
        },
        newWindow: {
            open: function (link, afterClose) {
//                alert(link.url);
                var newWindow = window.open(link.url, link.title, link.options);
                newWindow.onload = function () {

                    newWindow.focus();
                    newWindow.document.title = link.title;
                    var intervalTimer = setInterval(function () {
                        if (newWindow.closed) {
                            clearInterval(intervalTimer);
                            afterClose();
                        }
                    }, 500);

                };
                return newWindow;
            }
        },
        paramsToData: function (params) {
            tmp = {};
            try {
                $.each(params, function (key, param) {
                    tmp['tx_ted3_fe[' + key + ']'] = param;

                });
            } catch (e) {
                console.log("NO PARAMS!");
            }
            return tmp;
        },
        ajax: function (params) {
            params.data = Ted3.paramsToData(params.data);

//            var currentPageId = $('[data-cpid]').eq(0).data('cpid') || 0;
//            if (currentPageId) {
//                params.data = $.extend(params.data, Ted3.paramsToData({pid: currentPageId}));
//            }
//
//             alert(currentPageId);

            var ajaxparams = $.extend({
                method: 'POST',
                success: function (data) {
                    if (data.success == false) {
                        $('<div class="ted3-dialog-content" />').dialog({
                            appendTo: "#ted3-jqueryui",
                            title: "Action failed",
                            dialogClass: "ted3-dialog  ted3-dialog-error",
                            position: {my: "center center", at: "center center"},
                            buttons: [
                                {
                                    text: "Ok",
                                    click: function () {
                                        $(this).dialog('close');
                                    }
                                }
                            ]
                        }).html(data.message);
                    }
                },
                error: function (data) {
                    $('<div class="ted3-dialog-content" />').dialog({
                        title: data.statusText,
                        appendTo: "#ted3-jqueryui",
                        dialogClass: "ted3-dialog  ted3-dialog-error",
                        position: {my: "center center", at: "center center"},
                        buttons: [
                            {
                                text: "Ok",
                                click: function () {
                                    $(this).dialog('close');
                                }
                            }
                        ]
                    }).html(data.responseText);

                }
            }, params);

            return $.ajax(ajaxparams);
        },
        load: function (selector, success) {
            try {
                var holder = $('<div />').load(location.href + " " + selector, function (response, status, xhr) {
                    if (status == "error") {
                        $('<div calss="ted3-dialog-content" />').dialog({
                            title: xhr.statusText,
                            appendTo: "#ted3-jqueryui",
                            dialogClass: "ted3-dialog  ted3-dialog-error",
                            position: {my: "center center", at: "center center"},
                            buttons: [
                                {
                                    text: "Ok",
                                    click: function () {
                                        $(this).dialog('close');
                                    }
                                }
                            ]
                        }).html(xhr.responseText);
                    } else {
                        var newelement = $(holder.html());
                        success(newelement);
                        newelement = null;
                    }

                    holder.remove();
                });
            } catch (e) {
                console.log("Error on loading html ...");
            }
        },
        memory: {
            set: function (key, value) {
                localStorage.setItem(key, JSON.stringify(value));
            },
            has: function () {

            },
            remove: function (key) {
                localStorage.removeItem(key);
            },
            get: function (key) {
                return JSON.parse(localStorage.getItem(key));
            }
        },
        browser: {
            content: function (done) {
                //target: element or container
                var that = this;
                //   alert( encodeURI(Ted3.urls.closewindow) );
                var link = {
                    url: Ted3.urls.contentelements + "&uid=" + Ted3.fedata.pid + "&returnUrl=" + encodeURIComponent(Ted3.urls.closewindow),
                    title: 'Content wählen',
                    options: "width=900,height=650,left=50,top=100"
                };
                var ctype = "";
                var cWindow = Ted3.newWindow.open(link, function () {
                });
                cWindow.makeCElement = function (element) {
                    // alert("makeCElement");
                };
                //  newWindow.opener = $(this);
                $(cWindow).on('load', function () {
                    //  var responseText = .innerText || newWindow.document.documentElement.textContent;
                    cWindow.makeCElement = function (element) {

                        //  console.log(element);
                        var nStr = element.dataset.params.replace("defVals", "tx_ted3_fe");
                        //   alert(nStr);

//                        var nStr = cWindow.document.editForm.defValues.value.replace("defVals", "tx_ted3_fe");

                        nStr = nStr.replace("defVals", "tx_ted3_fe");
                        //console.log(nStr);

                        //   alert(nStr);

                        window.focus();
                        cWindow.close();

                        done(nStr);
                    }
                });
            },
            selectDesktopFile: function () {
                var finput = $(document.createElement('input'));
                finput.attr("type", "file");
                finput.attr("multiple", "true");
                finput.trigger('click'); // opening dialog
                finput.on('change', function () {
//		    console.log(finput, 3);
                });

            },
            files: function (done) {
                var link = {
                    url: Ted3.urls.filebrowser,
                    title: 'Link test',
                    options: "width=1150,height=750,top=100,left=50"
                };
//                alert("linktest api 346");
                var newWindow = Ted3.newWindow.open(link, function () {
                    //   alert("test");
                    console.log(window.fileBrowserData, 3);
                    ///  alert(window.fileBrowserData);
                    if (window.fileBrowserData) {
                        done(window.fileBrowserData);
                    } else {
                        console.log("no file selected");
                    }

                });
            },
            link: function (settedTypolink, done) {

                var link = {
                    url: "",
                    attributeValues: {
                        target: "",
                        alt: "",
                        title: ""
                    }

                };
                var linkTitleExtraction = settedTypolink.split('"');
                //   console.log(linkTitleExtraction);
                if (linkTitleExtraction[1]) {
                    // alert(settedTypolink);
                    settedTypolink = settedTypolink.replace('"' + linkTitleExtraction[1] + '"', "");
                    //  alert(settedTypolink);
                }



                var linkArray = settedTypolink.split(" ");

                //console.log(linkArray);

                if (linkArray[1] == undefined) {
                    linkArray[1] = "";
                }
                if (linkTitleExtraction[1] == undefined) {
                    linkTitleExtraction[1] = "";
                }

                var dialoghtml = "<div id='ted3-linkdialog'>" +
                        "<input style='width:350px' placeholder='Link/T3-Pid' name='linkinput' type='text' value='" + linkArray[0] + "'/>" +
                        "<input style='width:150px' placeholder='Target'  name='linktarget' type='text' value='" + linkArray[1] + "'/>" +
                        "<input style='width:350px' placeholder='Title'  name='linktitle' type='text' value='" + linkTitleExtraction[1] + "'/>" +
                        "</div>";

                $(dialoghtml).dialog({
                    appendTo: "#ted3-jqueryui",
                    title: "Set Link",
                    minWidth: 450,
                    close: function () {
                        $('#ted3-linkdialog').remove();
                    },
                    dialogClass: "ted3-dialog ted3-dialog-delete",
                    position: {my: "center center", at: "center center"},
                    buttons: {
                        "Set Link": function () {
//                            alert();
                            // Link Target  Title
                            link.url = $('#ted3-linkdialog').find('[name="linkinput"]').val() + ' ' + $('#ted3-linkdialog').find('[name="linktarget"]').val() + '  "' + $('#ted3-linkdialog').find('[name="linktitle"]').val() + '"';
                            done(link);
                            $(this).dialog("close");
                        },

                        "Delete Link": function () {

                            link.url = "";
                            done(link);
                            $(this).dialog("close");
                        },
                        Cancel: function () {
                            $(this).dialog("close");

                        }
                    }
                })

            }

        }

    };
    Ted3.api = {};
    Ted3.api.record = {
        new : function (container, target) {

            var params = {data: {}, cmd: {}};
            var table = container.data('table');
            params.data[table] = {};
            params.data[table]['NEWRECORD'] = {
                'pid': container.data('pid'),
                'hidden': 0,
                'sys_language_uid': Ted3.fedata.currentlangId
            };
            if (target) {
                params.data[table]['NEWRECORD']['pid'] = "-" + target.data('uid');
            }
            return Ted3.ajax({
                url: Ted3.urls.tce,
                data: params
            });
        },
        delete: function (element) {
            return Ted3.ajax({
                url: Ted3.urls.delete,
                data: {
                    uid: element.data('uid'),
                    table: element.data('table')
                }
            });
        },
        link: function (element, link) {
            var params = {data: {}, cmd: {}};
            var table = element.data('table');

            var anchorParts = link.url.split("#");
            if (anchorParts.length > 1) {

                var anchorName = anchorParts[1].split(" ");
                // alert(anchorName[0]);

                link.url = link.url.replace("#" + anchorName[0], "");
            }

            //    alert(link.url);
            //Check if string withou slash
            var partsOfTypolink = link.url.split(" ");


            if (isNaN(partsOfTypolink[0])) {
                if (partsOfTypolink[0].slice(-1) != "/") {
                    //alert("addSlash");
                    link.url = partsOfTypolink[0] + "/";
                    partsOfTypolink[0] = partsOfTypolink[0] + "/";

                    if (partsOfTypolink[1]) {
                        link.url = link.url + " " + partsOfTypolink[1];
                    }
                    if (partsOfTypolink[2]) {
                        link.url = link.url + " " + partsOfTypolink[2];
                    }
                    if (partsOfTypolink[3]) {
                        link.url = link.url + " " + partsOfTypolink[3];
                    }
                }
            }

            if (anchorName) {
                if (anchorName[0]) {
                    link.url = link.url.replace(partsOfTypolink[0], partsOfTypolink[0] + "#" + anchorName[0]);
                }
            }

            params.data[table] = {};
            params.data[table][element.data('uid')] = {};
            params.data[table][element.data('uid')][element.data('linkfield')] = link.url;
//            console.log(params);
            return Ted3.ajax({
                url: Ted3.urls.tce,
                data: params
            });
        },
        hide: function (element) {
            var params = {data: {}, cmd: {}};
            var table = element.data('table');
            var disablefield = "hidden";
            if (element.data('disablefield')) {
                disablefield = element.data('disablefield');
            }
            params.data[table] = {};
            params.data[table][element.data('uid')] = {};
            params.data[table][element.data('uid')][disablefield] = 1;
            
            params.pid = Ted3.fedata.pid;
            return Ted3.ajax({
                url: Ted3.urls.tce,
                data: params
            });
        },
        hidemobile: function (element, val) {
            var params = {data: {}, cmd: {}};
            var table = element.data('table');
            params.data[table] = {};
            params.data[table][element.data('uid')] = {};
            params.data[table][element.data('uid')]['ted3_hidemobile'] = val;
            return Ted3.ajax({
                url: Ted3.urls.tce,
                data: params
            });
        },
        show: function (element) {
            var params = {data: {}, cmd: {}};
            var table = element.data('table');
            var disablefield = "hidden";
            if (element.data('disablefield')) {
                disablefield = element.data('disablefield');
            }
            params.data[table] = {};
            params.data[table][element.data('uid')] = {};
            params.data[table][element.data('uid')][disablefield] = 0;
            
            params.pid = Ted3.fedata.pid;
            return Ted3.ajax({
                url: Ted3.urls.tce,
                data: params
            });
        },
        savesettings: function (element, data) {

            return Ted3.ajax({
                url: Ted3.urls.settings,
                data: {
                    uid: element.data('uid'),
                    table: element.data('table'),
                    settings: data
                }
            });
        },
        edit: function (element) {
//            alert(location.href);
//            alert(Ted3.urls.t3edit + element.data('table') + '&uid=' + element.data('uid') + '&returnUrl=' + encodeURIComponent(Ted3.urls.closewindow));
            // console.log(Ted3.urls.t3edit + element.data('table') + '&uid=' + element.data('uid') + '&returnUrl=' + encodeURIComponent(Ted3.urls.closewindow));
            //  alert(location.href);

            var link = {
                url: Ted3.urls.t3edit + element.data('table') + '&uid=' + element.data('uid') + '&returnUrl=' + encodeURIComponent(location.href),
                title: 'Edit record',
                options: "width=810,height=600,top=50,left=100"
            };
            location.href = link.url;
            // window.open()

        },

        translate: function (element, langId) {
            return Ted3.ajax({
                url: Ted3.urls.translate,
                data: {
                    uid: element.data('uid'),
                    lang: langId,
                    table: element.data('table')
                }
            });

        },
        save: function (element) {
            var params = {data: {}};
            var table = element.data('table');
            params.data[table] = {};
            params.data[table][element.data('uid')] = {};
            //alert("do save direct proerties and no inline records")



            element.find('[data-widget="textedit"].ted3-changed').each(function (i, item) {
                if ($(this).closest('[data-element].ted3-element-saveable')[0] == element[0]) {
                    // alert(typeof($(this).data('field')) );
                    console.log("direct text");
                    if (typeof ($(this).data('field')) == "object") {
                        var str = JSON.stringify($(this).data('field'));

                        str = str.replace("VALUE", $(this).html());
                        var data = JSON.parse(str);
                        params.data[table][element.data('uid')] = data;
                    } else {
//                            console.log("direct dfadds");
                        params.data[table][element.data('uid')][$(this).data('field')] = $(this).html();
                    }
                    $(this).textedit('clearmemory');
                }
            });

            params.pid = Ted3.fedata.pid;
            //console.log(params);
            return Ted3.ajax({
                url: Ted3.urls.tce,
                data: params
            });
            //}
            // console.log(params);
        },
        copy: function (target, source) {
            var element = $(this);
            var params = {data: {}, cmd: {}};

            params.cmd[source.data('table')] = {};
            params.cmd[source.data('table')][source.data('uid')] = {};
            params.cmd[source.data('table')][source.data('uid')]['copy'] = '-' + target.data('uid');

            return Ted3.ajax({
                url: Ted3.urls.tce,
                data: params
//                fields: newWArray
            });
        },
        move: function (target, source) {
            var params = {data: {}, cmd: {}};
            var addUri = "";
            if (source.element('getContainer').data('field')) {

            } else {
                //Tedcht inline!
                var table = source.data('table');
                params.cmd[table] = {};
                params.cmd[table][source.data('uid')] = {};
                if (target.data('uid') != undefined) {
                    params.cmd[table][source.data('uid')]['move'] = '-' + target.data('uid');
                } else {
                    if (table == "pages") {
                        params.cmd[table][source.data('uid')]['move'] = source.element('getContainer').data('pid');
                    } else {
                        params.cmd[table][source.data('uid')]['move'] = 0;
                    }
                }
            }
            return Ted3.ajax({
                url: Ted3.urls.tce + addUri,
                data: params
            });
        }
    };
    Ted3.api.content = {
        delete: Ted3.api.record.delete,
        save: Ted3.api.record.save,
        show: Ted3.api.record.show,
        savesettings: Ted3.api.record.savesettings,
        hide: Ted3.api.record.hide,
        hidemobile: Ted3.api.record.hidemobile,
        link: Ted3.api.record.link,
        edit: Ted3.api.record.edit,
        translate: Ted3.api.record.translate,
        copy: function (target, source) {
            // target = container or element
            var uidToCopy = source.data('uid');
            return Ted3.ajax({
                url: Ted3.urls.copycontent,
                data: {
                    uid: uidToCopy,
                    pid: target.closest('[data-container="content"]').data('pid'),
                    colpos: target.closest('[data-container="content"]').data('colpos') || 0,
                    container: target.closest('[data-container="content"]').data('parent') || 0,
                    beforeUid: target.data('uid') || null
                }
//                fields: newWArray
            });
        },
        new : function (target, data) {
            var fields = {};
            var url = Ted3.urls.createcontent;
            var identifier = "";

            if (data.type == "fileref") {
                data.extension = data.extension.toLowerCase();
                if (Ted3.fileToContentMap[data.extension] == undefined) {

                    $('<div class="ted3-dialog-content" />').dialog({
                        title: "Info",
                        dialogClass: "ted3-dialog",
                        appendTo: "#ted3-jqueryui",
                        position: {my: "center center", at: "center center"},
                        buttons: [
                            {
                                text: "Ok",
                                click: function () {
                                    $(this).dialog('close');
                                }
                            }
                        ]
                    }).html("No content defined for file '" + data.extension + "'");



                    // var donothing = function(){return false};
                    return Ted3.ajax({
                        url: url,
                        data: {
                            identifier: 'notDefined'
                        }
                    });

                }

                var fileToContent = Ted3.fileToContentMap[data.extension];
                identifier = fileToContent.identifier;
                fields[fileToContent.field] = {
                    type: 'file',
                    data: data.value,
                    ted3_renderwidth: parseInt(target.width())
                };
            } else {
                url = url + data.value;
            }
            //Ted3.log("Content Create " + data.value);
            var container = target.closest('[data-container="content"]');


            var data = {
                identifier: identifier,
                pid: container.data('pid'),
                colpos: container.data('colpos'),
                container: container.data('parent'),
                beforeUid: target.data('uid') || null,
                currentLangId: Ted3.fedata.currentlangId,
                fields: fields
            };

            return Ted3.ajax({
                url: url,
                data: data
            });
        },
        move: function (target, source) {
            // target = container or element
            var element = $(this);
            var newWArray = [];
            newWArray = {};
            return Ted3.ajax({
                url: Ted3.urls.movecontent,
                data: {
                    uid: source.data('uid'),
                    pid: target.closest('[data-container="content"]').data('pid'),
                    colpos: target.closest('[data-container="content"]').data('colpos') || 0,
                    container: target.closest('[data-container="content"]').data('parent') || 0,
                    beforeUid: target.data('uid') || null,
                    fields: newWArray
                }

            });
        }
    };

}(Ted3.jQuery));
