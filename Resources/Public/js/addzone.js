(function ($) {
    $.widget("ted3.addzone", {
        options: {
            receiveNew: $.noop,
            receiveElement: $.noop,
            settings: {
                elements: 0,
                directelement: 0,
                files: 0,
                fromfiles: 0,
                cleanrecords: 0
            }
        },
        _create: function () {
            var that = this;
            this.buttons = {};
            this.progressbar = $('<div />', {class: 'ted3-progressbar ted3-addzone-progressbar'});
            //  console.log("Addzone create", 0);
            if (this.options.settings.elements != false) {
                this._initElementBehavior();
            }
            if (this.options.settings.files != false) {
                this._initFileBehavior();
            }

            if (this.options.settings.cleanrecords != false) {
                this._initRecordsBehavior();
            }
        },
        _initRecordsBehavior: function () {
            var that = this;
            this.buttons.records = $('<div class="ted3-btn ted3-btn-elementwizard" title="New Element"  />')
                    .appendTo(that.element).on('click', function (e) {
                e.stopPropagation();
                that._trigger('createRecord', e);
                that.startLoading();
            });
//	    this.buttons.paste = $('<div class="ted3-btn ted3-btn-paste" title="einfügen"  />')
//		    .appendTo(that.element).on('click', function(e) {
//		e.stopPropagation();
//		that.startLoading();
//		that._trigger('receiveFromClipboard');
//	    });

        },
        _initElementBehavior: function () {
            var that = this;
            this.buttons.elements = $('<div class="ted3-btn ted3-btn-elementwizard" title="New Content-Element"  />')
                    .appendTo(that.element).on('click', function (e) {
                e.stopPropagation();

                if (!that.options.settings.directelement) {
                    Ted3.browser.content(function (data) {

                        that.startLoading();
                        that._trigger('receiveNew', e, {type: 'content', value: data});
                    });
                } else {
                    //alert("direct"); 
                    that.startLoading();
                    that._trigger('receiveNew', e, {type: 'content', value: that.options.settings.directelement});
                }

            });
            this.buttons.paste = $('<div class="ted3-btn ted3-btn-paste" title="Paste"  />')
                    .appendTo(that.element).on('click', function (e) {
                e.stopPropagation();
                that.startLoading();
                that._trigger('receiveFromClipboard');
            });
            this.element.droppable({
                hoverClass: "ted3-drop-hover",
                tolerance: 'pointer',
                accept: '.ted3-element',
                drop: function (e, ui) {
                    that.startLoading();
                    that._trigger('receiveElement', e, ui.draggable);

                }
            });
        },
        _initFileBehavior: function () {
            var that = this;
            if (this.options.settings.fromfiles == 1) {
                this.buttons.falwizard = $('<div class="ted3-btn ted3-btn-file" title="New Content-Element from file"  />')
                        .appendTo(that.element).on('click', function (e) {
                    e.stopPropagation();


                    Ted3.filepool(function (filedata) {
                        console.log(filedata);
                        that.startLoading();
                        that._trigger('receiveNew', e, {type: 'fileref', value: filedata.uid, extension: filedata.ext});
                    });


                });
            } else {
                this.buttons.falwizard = $('<div class="ted3-btn ted3-btn-falwizard" title="Select file"  />')
                        .appendTo(that.element).on('click', function (e) {
                    e.stopPropagation();
                    Ted3.filepool(function (filedata) {
                        //    console.log(filedata);
                        that.startLoading();
                        that._trigger('receiveNew', e, {type: 'fileref', value: filedata.uid, extension: filedata.ext});
                    });

                });
            }
            this.buttons.fileupload = $('<div class="ted3-btn ted3-btn-upload" title="Select file"  />')
                    .appendTo(that.element).on('click', function (e) {
                e.stopPropagation();
                $('#ted3-fileselector-input').one('change', function (e) {
                    if (e.target.files.length > 0) {
//                            console.log(e.originalEvent.dataTransfer);
                        var extension = e.target.files[0].name.split('.').pop();
                        that.startLoading(true);
                        Ted3.file.upload(e.target.files, function (e, xhr) {
                            var percent = Math.round((e.loaded / e.total * 100) * 100) / 100 + "%";
                            that.progressbar.clearQueue().animate({
                                width: percent
                            }, 800);
                        }).done(function (data) {
                            var data = JSON.parse(data);
                            if (data.success == true) {
                                that._trigger('receiveNew', e, {type: 'fileref', value: data.files[0], extension: extension});
                            } else {
                                that.endLoading();
                            }
                        });
                    }
                });
                $('#ted3-fileselector-input').trigger('click');

            });

            if (this.options.settings.remove == 1) {
                that.buttons.removeFile = $('<div class="ted3-btn ted3-btn-delete" title="Remove file"  />')
                        .appendTo(that.element).on('click', function (e) {
                    e.stopPropagation();
                    that._trigger('remove');
                });
            }
            this.element.on({// Native-dd
                dragenter: function (e) {
                    e.preventDefault();
                    $(this).addClass('ted3-drop-hover');
                },
                drop: function (e) {
                    e.preventDefault();
                    if (e.originalEvent.dataTransfer) {
                        if (e.originalEvent.dataTransfer.files.length > 0) {
                            var extension = e.originalEvent.dataTransfer.files[0].name.split('.').pop();
                            var lowerExt = extension.toLowerCase();
                            // console.log(that.element);
                            if (that.element.hasClass('ted3-addzone-image')) {

                                // console.log( Object.values(Ted3.fedata.imageFileExtensions));
                                if (!Object.values(Ted3.fedata.imageFileExtensions).includes(lowerExt)) {


                                    $('<div class="ted3-dialog-content" />').dialog({
                                        title: "Info",
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
                                    }).html("Only images allowed");


                                    return false;
                                }
                            }
                            that.startLoading(true);
                            Ted3.file.upload(e.originalEvent.dataTransfer.files, function (e, xhr) {
                                var percent = Math.round((e.loaded / e.total * 100) * 100) / 100 + "%";
                                that.progressbar.clearQueue().animate({
                                    width: percent
                                }, 800);
                            }).done(function (data) {
                                var data = JSON.parse(data);
                                if (data.success == true) {
                                    that._trigger('receiveNew', e, {type: 'fileref', value: data.files[0], extension: extension});
                                } else {
                                    that.endLoading();
                                }


                            });
                        }
                    }
                    $(this).removeClass('ted3-drop-hover');
                },
                dragleave: function (e) {
                    e.preventDefault();
                    $(this).removeClass('ted3-drop-hover');
                }
            });
        },
        _setOption: function (key, value) {
            this.options[key] = value;
            if (key == "settings") {
                if (value.elements && this.buttons.elements == undefined) {
                    this._initElementBehavior();
                } else if (value.elements == 0 && this.buttons.elements) {
                    this.buttons.elements.remove();
                    this.element.droppable('destroy');
                }

                if (value.files && this.buttons.falwizard == undefined) {
                    this._initFileBehavior();
                } else if (value.files == 0 && this.buttons.falwizard) {
                    this.buttons.falwizard.remove();
                    this.element.off('dragenter').off('drop').off('dragleave');
                }

                if (value.cleanrecords && this.buttons.records == undefined) {
                    this._initRecordsBehavior();
                } else if (value.cleanrecords == 0 && this.buttons.records) {
                    this.buttons.records.remove();
                }

            }

        },
        startLoading: function (progress, duration) {
            var that = this;
            var duration = duration || "0.6s";
            this.element.append(this.progressbar);
            if (progress) {
//		console.log("start upload progress");
                //this.progressbar.css('transition','width 1s');
            } else {
                this.progressbar.css('transition', 'width ' + duration);
                setTimeout(function () {
                    that.progressbar.css('width', '100%');
                }, 100)

            }
        },
        endLoading: function () {
            var that = this;
            setTimeout(function () {
                that.progressbar.css('width', '0');
                that.progressbar.detach();
            }, 100);
        }
    });

}(Ted3.jQuery));