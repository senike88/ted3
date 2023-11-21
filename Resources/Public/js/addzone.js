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