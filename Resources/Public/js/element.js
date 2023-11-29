
(function ($) {
    $.widget("ted3.element", {
//        widgetEventPrefix: "element",
        options: {
            selectable: true,
            move: false,
            save: true,
            addzone: false
        },
        _create: function () {

            this.name = "";
            this.element.addClass('ted3-element');
            this.options = $.extend({}, this.options, this.element.data('settings'));
            var that = this;
            this.type = this.element.data('element');
            this.api = Ted3.api[this.type];
            that.element.find('a').on({
                dragstart: function (e) {
                    //Commented out because image-dragging not possible if link arround
                    if ($(this).find('[data-widget="textedit"][contenteditable="true"]').length > 0) {
                        e.preventDefault();
                    }
                }
            });



            if (this.element.css('position') == "static") {
                this.element.css('position', 'relative');
            }
            if (this.options.save) {
                this.element.addClass('ted3-element-saveable');
            }

            if (this.options.selectable) {
                this.element.on('click', function (e) {

                    //!Ted3.root.ted3root('hasMode', 'change') && -> WRITING MODE
                    if ((!Ted3.root.ted3root('hasMode', 'preview') || that.element.closest('.ted3-element-selected').length > 0)) {
                        //prevent parent selection on edit
                        if (Ted3.root.ted3root('hasMode', 'change') && that.element.find('.ted3-element-selected').length > 0) {
                            return false;
                        }

                        e.stopPropagation();
                        that.select();
                    }
                });
            }

            this._createDirectFieldWidgetsOnce();
            this._createDirectContainers();

            this._createAddzone();

            if (this.options.move && !(this.options.buttonsort || this.getContainer().data('buttonsorting'))) {
                this.dragger = $('<div /> ', {
                    class: 'ted3-dragger ted3-btn'
                }).prependTo(this.element);
                this.element.draggable({
                    handle: that.dragger,
                    refreshPositions: true,
                    cursor: "move",
                    delay: 90,
                    helper: 'clone',
                    cursorAt: {top: -30},
                    start: function (e, ui) {
                        var that = $(this);
                        $('body').addClass('ted3-mode-dragging');
                        ui.helper.css("width",that.width());
                        setTimeout(function () {
                            that.draggable('option', 'refreshPositions', false);
                        }, 1000);
                    },
                    stop: function (e, ui) {
                        $('body').removeClass('ted3-mode-dragging');
                    },
                    revert: 'invalid'
                });
            }


        },
        _createAddzone: function () {
            var that = this;
            this.addzone = $('<div />', {
                class: 'ted3-addzone'
            }).addzone({
                createRecord: function (e, newdata) {
                    var addzone = $(this);
                    that.api.new(that.getContainer(), that.element).done(function (data) {
                        if (that.forceReload()) {
                            Ted3.root.ted3root('reload');
                            return false;
                        }
                        var data = JSON.parse(data);
                        Ted3.load('div[data-uid="' + data.newuid + '"][data-table="' + that.element.data('table') + '"]', function (newelement) {
                            that.addzone.after(newelement);
                            newelement.element();
                            newelement.element('select');
                            addzone.addzone('endLoading');
                        });
                    });
                },
                receiveNew: function (e, newdata) {
                    var addzone = $(this);
                    that.api.new(that.element, newdata).done(function (data) {
                        if (that.forceReload()) {
                            Ted3.root.ted3root('reload');
                            return false;
                        }
                        var data = JSON.parse(data);
                        Ted3.load('div[data-uid="' + data.newuid + '"][data-table="' + that.element.data('table') + '"]', function (newelement) {
                            that.addzone.after(newelement);
                            newelement.element();
                            setTimeout(function () {
                                newelement.element('select');
                            }, 300);
                            addzone.addzone('endLoading');
                        });
                    });
                },
                receiveElement: function (e, element) {
                    var addzone = $(this);
                    that.addzone.after(element);
                    element.element(); //reinit
                    //element.css({left: 0, top: 0});
                    that.api.move(that.element, element).done(function (data) {
                        //    Ted3.log("Element moved to dropzone");
                        //that.reload();
                        //element.select();
                        addzone.addzone('endLoading');

                    });
                },
                receiveFromClipboard: function () {
                    var addzone = $(this);
                    var elementData = Ted3.root.ted3root('getFromClipboard');
                    var tmpElement = $('<div />').data(elementData);
                    that.api[elementData.mode](that.element, tmpElement).done(function (data) {

                        var data = JSON.parse(data);
                        if (elementData.mode == "move") {
                            data.newuid = elementData.uid;
                            $('[data-element][data-uid="' + data.newuid + '"]').remove();
                        }
                        if (that.forceReload()) {
                            Ted3.reload();
                        } else {
                            Ted3.load('div[data-uid="' + data.newuid + '"][data-table="' + that.element.data('table') + '"]', function (newelement) {
                                that.addzone.after(newelement);
                                newelement.element();
                                addzone.addzone('endLoading');
                            });
                        }
                    });
                    tmpElement = null;
                }
            });

            if (this.getContainer().data('name')) {
                this.addzone.prepend('<div class="ted3-addzone-name">' + this.getContainer().data('name') + '</div>');
            }
            Ted3.root.on('ted3rootaddzone', function (e, data) {
                if (data.active) {
                    var addzoneoptions = that.getContainer().container('option', 'addzone');
                    if (!that.getContainer().container('option', 'allowOnlyEmptyAddzone') && addzoneoptions) {
                        that.element.after(that.addzone);
                    }
                } else {

                    that.addzone.detach();
                }
                that.initBarsPositions();
            });
        },
        select: function () {
            var that = this;

            if (this.element.hasClass('ted3-element-selected')) {
                //return false;
            } else if (Ted3.root.hasClass('ted3-mode-change') && $('.ted3-element-selected').length > 0) {
                $('.ted3-element-selected').element('cancel');
                setTimeout(function () {
                    that.select();
                }, 100);

            } else {

                this._createToolbarsOnce();

                that.element.find('a').on({
                    click: function (e) {
                        if (Ted3.root.hasClass('ted3-mode-edit') && !Ted3.root.hasClass('ted3-mode-noconflict')) {
                            e.preventDefault();
                        }
                    }
                });
                //Unselect all other Elements
                $('.ted3-element-selected').element('unselect');
                // $('.ted3-focus').removeClass('ted3-focus');
                Ted3.root.addClass('ted3-mode-selected');
                this.element.addClass('ted3-element-selected');
                if (this.toolbar) {
                    this.toolbar.addClass('ted3-element-toolbar-selected');
                }

                that.showBars();
                this._trigger('select');
                //Should fix animationfails do better on document-resize
                var checkresizeing = 120;
                while (checkresizeing < 800) {

                    setTimeout(function () {
                        that.initBarsPositions();
                    }, checkresizeing);
                    checkresizeing = checkresizeing + 32;
                }
            }

        },
        unselect: function () {
            var that = this;
            this.cancel();
            that.hideBars();
            $('body').removeClass('ted3-mode-selected');
            this.element.removeClass('ted3-element-changed');
            this.element.removeClass('ted3-element-selected');
            if (this.toolbar) {
                this.toolbar.removeClass('ted3-element-selected');
            }
            this._trigger('unselect');
        },
        cancel: function () {
            var that = this;
            this.element.find('[data-widget="text"]').textedit('blur');

            this.element.find('[data-widget="textedit"]').each(function (i, item) {

                if ($(this).closest('[data-element]')[0] == that.element[0]) {
                    $(this).removeClass('ted3-changed');
                    if (!$(this).closest('[data-element]').data('translateable')) {
                        $(this).textedit('disable');
                    }
                }
            });
            if (this.options.save && this.savebuttons) {
                this.savebuttons.hide();
            }

            this.showBars();
            if (this.toolbar) {
                this.toolbar.removeClass('ted3-element-toolbar-changed');
            }

            this.element.removeClass('ted3-element-changed');
            Ted3.root.removeClass('ted3-mode-change');
            this._trigger('cancel');
        },
        _toolbarsCreated: false,
        _createToolbarsOnce: function () {
            if (!this._toolbarsCreated && this.element.data('editingaccess')) {
                var that = this;
                this.toolbar = $('<div /> ', {
                    class: 'ted3-element-toolbar'
                }).prependTo('#ted3-tmpbar');
                this.toolbarbottom = $('<div />', {
                    class: 'ted3-element-toolbar ted3-element-toolbar-bottom'
                }).prependTo('#ted3-tmpbar');
                this.mainbuttons = $('<div /> ', {
                    class: 'ted3-element-mainbuttons'
                }).appendTo(this.toolbar);


                if (this.element.data('translateable') == 1) {
                    this.toolbar.addClass('tb-translateable');
                    $('<div class="ted3-btn ted3-btn-world" title="Translate" />')
                            .appendTo(that.mainbuttons)
                            .on('click', function (e) {
                                that.api.translate(that.element, Ted3.fedata.currentlangId)
                                        .done(function () {
                                            Ted3.reload();
                                        });
                            });
                }
                if (this.element.data('hidden') == 1 || this.element.data('outofdate') == 1) {
                    this.toolbar.addClass('ted3-element-toolbar-hidden');
                }
                if (this.options.save) {
                    this.savebuttons = $('<div /> ', {
                        class: 'ted3-element-savebuttons'
                    }).appendTo(this.toolbar);
                    this.savebtn = $('<div class="ted3-btn ted3-btn-save" title="Save" />')
                            .appendTo(that.savebuttons).on('click', function (e) {
                        e.stopPropagation();
                        that.save();
                    });
                    this.cancelbtn = $('<div class="ted3-btn ted3-btn-cancel" title="Cancel"  />')
                            .appendTo(that.savebuttons).on('click', function (e) {
                        e.stopPropagation();
                        that.cancel();
                    });
                }

                if (this.options.hide && this.element.data('translateable') == undefined) {
                    if (this.element.data('hidden') == 1) {
                        this.showbtn = $('<div class="ted3-btn ted3-btn-show" title="Show"  />')
                                .appendTo(that.mainbuttons).on('click', function (e) {
                            e.stopPropagation();
                            that.show();
                        });
                    } else {
                        this.hidebtn = $('<div class="ted3-btn ted3-btn-hide" title="Hide" />')
                                .appendTo(that.mainbuttons).on('click', function (e) {
                            e.stopPropagation();
                            that.hide();
                        });
                    }
                }

                if (this.options.t3edit) {
                    this.editbtn = $('<div class="ted3-btn ted3-btn-edit" title="Edit in TYPO3" />')
                            .appendTo(that.mainbuttons).on('click', function (e) {
                        e.stopPropagation();
                        that.api.edit(that.element);
                    });
                }
                if (this.options.hidemobile && this.element.data('translateable') == undefined) {
                    if (this.element.data('hidemobile') == 1) {
                        this.hidem = $('<div class="ted3-btn ted3-btn-hidemobile ted3-btn-hidemobile-hidden" title="Show on mobile devices"  />')
                                .appendTo(that.mainbuttons).on('click', function (e) {
                            e.stopPropagation();
                            that.api.hidemobile(that.element, 0).done(function () {
                                that.reload();
                            });
                        });
                    } else {
                        this.hidem = $('<div class="ted3-btn ted3-btn-hidemobile" title="Hide on mobile devices" />')
                                .appendTo(that.mainbuttons).on('click', function (e) {
                            e.stopPropagation();
                            that.api.hidemobile(that.element, 1).done(function () {
                                that.reload();
                            });
                        });
                    }
                }

                if (this.options.edithelper) {
                    this.editbtn = $('<div class="ted3-btn ted3-btn-edithelper" title="Edithelper" />')
                            .appendTo(that.mainbuttons).on('click', function (e) {
                        e.stopPropagation();
                        that.element.toggleClass('ted3-element-edithelp');
                        that.initBarsPositions();
                    });
                }


                if (this.element.children('.ted3-element-settingsform').length > 0) {
                    this.settingsopen = false;
                    this.settingsbtn = $('<div class="ted3-btn ted3-btn-cog" title="Settings" />')
                            .appendTo(that.mainbuttons).on('click', function (e) {
                        e.stopPropagation();
                        if (!that.settingsopen) {
                            that.element.children('.ted3-element-settingsform').clone().dialog({
                                appendTo: "#ted3-jqueryui",
                                title: "Settings",
                                minWidth: 450,
                                close: function () {
                                    that.settingsopen = false;
                                },
                                dialogClass: "ted3-dialog ted3-dialog-delete",
                                position: {my: "center center", at: "center top", of: that.element},
                            }).one('submit', function (e) {
                                e.preventDefault();
                                var array = $(this).serializeArray();
                                var json = {};
                                that.toolbar.addClass('ted3-element-saving');
                                $.each(array, function () {
                                    if (json[this.name]) { //multiple select
                                        json[this.name] = json[this.name] + "," + this.value;
                                    } else {
                                        json[this.name] = this.value || '';
                                    }
                                });
                                //console.log();
                                $(this).dialog('close');
                                that.settingsopen = false;
                                that.api.savesettings(that.element, json).done(function () {
                                    that.reload();
                                });
                            });
                        }
                        that.settingsopen = true;
                    });
                }
                if (this.element.data('linkfield')) {
                    this.linkbutton = $('<div class="ted3-btn ted3-btn-link"  title="Set link"  />')
                            .appendTo(that.mainbuttons).on('click', function (e) {
                        e.stopPropagation();
                        var setedTypolink = that.element.data('typolink') || "";
                        Ted3.browser.link(setedTypolink, function (link) {
                            if (link.url == "del") {
                                link.url = "";
                            } else if (link.url.indexOf('page:') === 0) {
                                link.url = link.url.substr(5);
                            }
                            that.api.link(that.element, link).done(function () {
                                that.reload();
                            });
                        });
                    });
                }
                if (this.options.copycutpaste) {
                    this.copybtn = $('<div class="ted3-btn ted3-btn-copy" title="Copy" />')
                            .appendTo(that.mainbuttons).on('click', function (e) {
                        e.stopPropagation();
                        that.copy();
                    });
                    this.copybtn = $('<div class="ted3-btn ted3-btn-cut" title="Cut" />')
                            .appendTo(that.mainbuttons).on('click', function (e) {
                        e.stopPropagation();
                        that.cut();
                    });
                }

                if (this.options.clone) {

                    this.clonebtn = $('<div class="ted3-btn ted3-btn-clone" title="Clone"  />')
                            .appendTo(that.toolbarbottom).on('click', function (e) {
                        e.stopPropagation();
                        that.clone();
                    });
                }

                if (this.options.buttonsort || this.getContainer().data('buttonsorting')) {
                    this._createSortingbuttons();
                }

                if (this.options.delete) {
                    if (that.element.data('origuid')) { // DELETE TRANSLATION
                        this.deletebtn = $('<div class="ted3-btn ted3-btn-delete" title="Delete translation" />')
                                .appendTo(that.mainbuttons).on('click', function (e) {
                            e.stopPropagation();
                            that.delete(true, "Delete the translation of this element?");
                        });
                    } else {
                        this.deletebtn = $('<div class="ted3-btn ted3-btn-delete" title="Delete element" />')
                                .appendTo(that.mainbuttons).on('click', function (e) {
                            e.stopPropagation();
                            that.delete();
                        });
                    }
                }

                this.upbtn = $('<div class="ted3-btn ted3-btn-up" title="Jump to parent" />')
                        .appendTo(that.mainbuttons).on('click', function (e) {
                    e.stopPropagation();
                    that.up();
                });

                this.name = $('<div />', {
                    class: 'ted3-toolbar-name',
                    text: this.options.name
                }).prependTo(this.toolbar);

                if (this.element.parent().closest('.ted3-element').length < 1) {
                    this.element.addClass('ted3-element-root');
                    this.upbtn.hide();
                } else {
                    this.upbtn.show();
                }
                this.initBarsPositions();

                this._toolbarsCreated = true;
            }

        }, // createToolbarsOnce
        _createSortingbuttons: function () {
            var that = this;
            this.prev = $('<div class="ted3-btn ted3-btn-prev" title="Move back" />').appendTo(that.mainbuttons).on('click', function (e) {
                e.stopPropagation();
                if (that.element.prevAll('[data-element]').first().length > 0) {
                    var prev = that.element.prevAll('[data-element]').first();
                    prev.before(that.element);
                    that._initAddzone();
                    that.initBarsPositions();

                    if (that.element.prevAll('[data-element]').first().length > 0) {
                        var target = that.element.prevAll('[data-element]').first();
                    } else {
                        var target = that.getContainer();
                    }

                    that.api.move(target, that.element).done(function () {
                        if (that.forceReload()) {
                            Ted3.reload();
                        }
                        that._trigger("resort");
                    });
                } else {
                    console.log("no Prev");
                }
            });
            this.next = $('<div class="ted3-btn ted3-btn-next" title="Move forward" />').appendTo(that.mainbuttons).on('click', function (e) {
                e.stopPropagation();
                if (that.element.nextAll('[data-element]').first().length > 0) {
                    var next = that.element.nextAll('[data-element]').first();
                    next.element('getAddzone').after(that.element);
                    that._initAddzone();
                    that.initBarsPositions();

                    that.api.move(next, that.element).done(function () {
                        if (that.forceReload()) {
                            Ted3.reload();
                        }
                        that._trigger("resort");
                    });
                }

            });
        },
        _fieldWidgetsCreated: false,
        _createDirectFieldWidgetsOnce: function () {
            if (!this._fieldWidgetsCreated) {
                var that = this;

                this.element.find('[data-widget="textedit"]').each(function () {
                    var $thisItem = $(this);
                    if ($(this).closest('[data-element]')[0] == that.element[0] && !$(this).closest('[data-element]').data('translateable')) {
                        //  console.log("text_edit");
                        setTimeout(function () {
                            $thisItem.textedit();
                        }, 150);

                    }
                });
                this._fieldWidgetsCreated = true;
            } else {
                alert("try to create Fieldwidgets again - check!");
            }

        },
        // Initialize its children
        _createDirectContainers: function () {
            var that = this;
            this.element.find('[data-container]').each(function () {
                if ($(this).closest('[data-element]')[0] == that.element[0]) {
                    $(this).container();
                }
            });
        },
        _init: function () {
            var that = this;
            if (this._toolbarsCreated && this.element.parent().closest('.ted3-element').length < 1) {
                this.element.addClass('ted3-element-root');
                this.upbtn.hide();
            } else if (this._toolbarsCreated) {
                this.upbtn.show();
            }



            if (this.element.find('[data-container]').length > 0) {
                // Can have children
            } else {
                // No children
                this.selectoverlay = $('<div /> ', {
                    class: 'ted3-element-selectoverlay'
                }).appendTo(this.element);
            }
            //
            if (Ted3.fedata.pid != this.element.data('cpid')) {
                //  this.element.addClass('ted3-element-slideelement');
                if (this.type == "content" && this.getContainer().parent().closest('[data-container]').length == 0) {
                    this.getContainer().addClass('ted3-element-slidecontainer');
                }
            }
            if (this.addzone) {
                this._initAddzone();
            }
            this.initBarsPositions();

            this._trigger('initialized');
        },
        _initAddzone: function () {
            var that = this;
            this.addzone.detach();
            var addzoneoptions = this.getContainer().container('option', 'addzone');
            if (!this.getContainer().container('option', 'allowOnlyEmptyAddzone') && addzoneoptions) {

                that.addzone.addzone('option', 'settings', addzoneoptions);

                that.element.after(that.addzone);
            }
        },
        getAddzone: function () {
            return this.addzone;
        },
        initBarsPositions: function () {

            if (this._toolbarsCreated) {
                var that = this;
                if (that.element.outerWidth(true) < 260) {
                    this.toolbar.find('.ted3-toolbar-name').css('max-width', 200);
                }
                var width = that.element.outerWidth();
                if ((that.element.offset().left + that.element.outerWidth()) > $(window).width()) {
                    width = width - ((that.element.offset().left + that.element.outerWidth()) - $(window).width());

                }
                // alert(Ted3.root.css('marginLeft'));


                var topOffset = that.element.offset().top - 26;
                if (topOffset < 0) {
                    topOffset = 0;
                }

                this.toolbar.css({
                    top: topOffset,
                    left: that.element.offset().left,
                    width: width,
                    minWidth: 150
                });

                this.toolbarbottom.css({
                    top: that.element.offset().top + that.element.outerHeight(),
                    left: that.element.offset().left,
                    width: width
                });

                if (that.element.outerWidth() < this.toolbar.width()) {
                    //Rightaligned
                    this.toolbar.css('left', parseInt(this.toolbar.css('left')) - (parseInt(this.toolbar.width()) - parseInt(that.element.outerWidth())));
                }
            }
        },
        forceReload: function () {
            if (this.element.data('forcereload')) {
                return true;
            }
            if (this.getContainer().data('forcereload')) {
                return true;
            }

            return false;
        },
        save: function () {
            if (!this.element.hasClass('ted3-element-saving')) {
                var that = this;
                that.addClass('ted3-element-saving');
                this.api.save(this.element).done(function (data) {
                    var data = JSON.parse(data);
                    if (that.forceReload()) {
                        Ted3.reload();
                    } else {
                        that.reload();
                    }
                });
            }
        },
        clone: function () {
            var that = this;
            that.unselect();
            that.addzone.addzone("startLoading", false, "2s");
            this.api.copy(this.element, this.element).done(function (data) {
                var data = JSON.parse(data);
                var uid = data.newuid || data.copyuid;
                if (that.forceReload()) {
                    Ted3.reload();
                } else {

                    Ted3.load('div[data-uid="' + uid + '"][data-table="' + that.element.data('table') + '"]', function (newelement) {
                        that.addzone.addzone("endLoading");
                        if (that.addzone) {
                            that.addzone.after(newelement);
                        } else {
                            that.element.after(newelement);
                        }

                        newelement.element();
                    });
                }
            });
            this._trigger('clone');
        },
        delete: function (elementReload = false, dialogText = "Delete this element?") {
            var that = this;
            $('<div class="ted3-dialog-content" />').dialog({
                appendTo: "#ted3-jqueryui",
                dialogClass: "ted3-dialog ted3-dialog-delete",
                position: {my: "center center", at: "center center", of: that.element},
                buttons: [
                    {
                        text: "Yes",
                        click: function () {
                            that.api.delete(that.element).done(function () {
                                that._trigger("delete");
                                if (elementReload) { // translated elements
                                    Ted3.reload();
                                } else {
                                    var container = that.getContainer();
                                    that.element.remove();
                                    if (that.forceReload()) {
                                        Ted3.reload();
                                    } else {
                                        container.container('reinitAddzone');
                                    }
                                }

                            });
                            $(this).dialog("close");

                        }
                    },
                    {
                        text: "No",
                        click: function () {
                            $(this).dialog("close");

                        }
                    }
                ]
            }).text(dialogText);
        },
        copy: function () {
            var that = this;

            Ted3.root.ted3root('setToClipboard', {
                mode: 'copy',
                table: that.element.data('table'),
                uid: that.element.data('uid')
            });
            that.unselect();
        },
        cut: function () {
            var that = this;
            Ted3.root.ted3root('setToClipboard', {
                mode: 'move',
                table: that.element.data('table'),
                uid: that.element.data('uid')
            });
            that.unselect();
        },
        hide: function () {
            var that = this;
            this.api.hide(this.element).done(function (data) {
                var data = JSON.parse(data);
                if (that.forceReload()) {
                    Ted3.reload();
                } else {
                    that.reload();
                }
            });
        },
        show: function () {
            var that = this;
            this.api.show(this.element).done(function (data) {
                var data = JSON.parse(data);
                if (that.forceReload()) {
                    Ted3.reload();
                } else {
                    that.reload();
                }
            });
        },
        up: function () {
            this.element.parent().closest('.ted3-element').element('select');
        },
        siblings: function () {
            var selector = '[data-element]';
            if (this.element.siblings(selector).length > 0) {
                return this.element.siblings(selector);
            }
            return false;
        },
        showBars: function () {
            this.initBarsPositions();
            if (this.toolbar) {
                this.toolbar.show();
                this.mainbuttons.show();
            }
            if (this.toolbarbottom) {
                this.toolbarbottom.show();
            }

            if (this.dragger) {
                this.dragger.show();
            }
        },
        hideBars: function () {
            if (this.toolbar) {
                this.toolbar.hide();
            }
            if (this.toolbarbottom) {
                this.toolbarbottom.hide();
            }
            if (this.dragger) {
                this.dragger.hide();
            }
        },

        change: function () {
            this.element.addClass('ted3-element-changed');
            this.toolbar.addClass('ted3-element-toolbar-changed');
            this.savebuttons.show();
            this.mainbuttons.hide();
            this.toolbarbottom.hide();
            Ted3.root.ted3root('change');
            this._trigger('change');
        },
        getname: function () {
            return this.options.name;
        },
        getData: function (key) {
            try {
                return this.options[key] || this.element.data(key) || this.element.closest('[data-container][data-field]').data(key);
            } catch (e) {
                return {};
            }
        },
        parent: function () {
            return this.element.parent().closest('.ted3-element');
        },
        getContainer: function () {
            return this.element.closest('[data-container="' + this.type + '"]');
        },
        savesettings: function () {
            this._trigger('savesettings');
        },
        destroy: function () {
            if (this.toolbar) {
                this.toolbar.remove();
            }
            if (this.addzone) {
                this.addzone.remove();
            }
            if (this.toolbarbottom) {
                this.toolbarbottom.remove();
            }
            this.element.removeClass('ted3-element');
            this.element.removeClass('ted3-element-selected');
            this.element.removeClass('ted3-element-changed');
            this.element.removeClass('ted3-element-saveable');
            this.element.removeClass('ted3-element-root');
            this._destroy();
        },
        isInit: function () {
        },
        reload: function () {

            var that = this;
            if (that.forceReload()) {
                Ted3.reload();
            } else {

//		console.log("RELOAD " + that.element.data('uid'));
                this.addClass('ted3-element-saving');
                //@todo div
                //  alert('div[data-uid="' + that.element.data('uid') + '"][data-table="' + that.element.data('table') + '"]');
                Ted3.load('div[data-uid="' + that.element.data('uid') + '"][data-table="' + that.element.data('table') + '"]', function (newelement) {
                    if (newelement.length > 1) {
                        newelement = $(newelement[0]);
                        console.log("2 new elements");
                    }
                    that._trigger('reload');
//                    console.log(newelement[0]);
//                    alert(newelement.length);
                    that.element.before(newelement);
                    that.unselect();
                    that.element.detach();
                    newelement.element();
                    that.destroy();
                    that.element.remove();
                    $('body').removeClass('ted3-mode-change');
                    //done(newelement);
                });
            }

        },
        addClass: function (c) {
            this.element.addClass(c);
            if (this.toolbar) {
                this.toolbar.addClass(c);
            }

        },
        removeClass: function (c) {
            this.toolbar.addClass(c);
        },
        trigger: function (event) {
            this._trigger(event);
        }
    });
}(Ted3.jQuery));