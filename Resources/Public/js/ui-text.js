(function ($) {
    $.widget("ted3.textedit", {
        // Default options.
        options: {
            changed: function () {
                $(this).addClass('ted3-changed');
                $(this).closest('[data-element].ted3-element-saveable').element('change');
                //   console.log("testchange");
            }
        },

        _create: function () {
            this.name = "";
            var mceIN = "";
            var that = this;
            this.parentElement = this.element.closest('[data-element].ted3-element-saveable');
            this.identifier = this.parentElement.data('uid') + this.element.data('field');
            this.originalContent = this.element.html();

//            console.log(this.originalContent);
//            alert("sdf");
            if (this.element.data('rte') == 1) {
                //  console.log("before tinymce");
                // setTimeout(function () {
                that.element.tinymce($.extend({
                    content_editable: false,
                    inline: true,
                    fixed_toolbar_container: '#ted3-editbar',
                    entity_encoding: "raw",
                    setup: function (editor) {
                        editor.on('init', function () {
                            //alert("sdf");
                            editor.hide();

                        });
                        editor.on('show', function () {
                            //   editor.focus();
                        });
                        var to = null;
                        editor.on('change', function () {
                            // console.log("tychange");
                            that.change();
                        });

                    }
                }, Ted3.tinymce4));
                //},500);


            } else {
                this.element.attr('contenteditable', false);

                this.element.on('blur', function () {
                    that.stopedit();
                });
//                var to = null;
                this.element.on('keydown', function (e) {
                    e.stopPropagation();
                    that.change();

                });
            }
            this.parentElement.on('elementselect', function () {
                if (that.element.text().length < 1) {
                    // Cleanup blank html to make selectable
                    if (that.element.data('rte') == 1) {
                        that.element.html('<p>' + that.element.data('default') + '</p>');
                    } else {
//                         that.element.html( that.element.data('default') );
                    }

                }
                if (that.element.data('rte') == 1) {
                    that.element.tinymce().show();
                    if (that.element.offset().top < 55) {
                        Ted3.root.addClass('ted3-tinymcebar-forceTopPadding');
                    }

                } else {
                    that.element.attr('contenteditable', true);
                    that.element.on('paste', function (e) {
                        e.preventDefault();
                        var text = (event.originalEvent || event).clipboardData.getData("text/plain");
                        document.execCommand("insertHTML", false, text);
                    });

                }
            });
            this.parentElement.on('elementunselect', function () {
                if (that.element.data('rte') == 1) {
                    that.element.tinymce().hide();
                    Ted3.root.removeClass('ted3-tinymcebar-forceTopPadding');
                } else {
                    that.element.attr('contenteditable', false);
                }
            });

            this.element.on({
                focus: function (e) {
                    e.stopPropagation();
                    if (that.parentElement.hasClass('ted3-element-selected')) {
                        that.change();
                    }

                },
                click: function (e) {

                    if (!Ted3.root.ted3root('hasMode', 'preview') && !Ted3.root.hasClass('ted3-mode-noconflict')) {

                        //Fix for links
//                        if(that.element.data('clicktrough') == 0){

                        e.preventDefault();
//                        }
                        if (that.parentElement.hasClass('ted3-element-selected')) {
                            // prevent user css click-events
                            e.stopPropagation();

                            that.enable();
                        }

                    }

                }
            });
        },
        enable: function () {
            var that = this;
            if (!Ted3.root.ted3root('hasMode', 'preview')) {

                if (this.element.data('rte') == 1) {

                } else {

                    this.element.attr('contenteditable', true);
                    //  this.element.focus();
                }
            }
        },
        change: function () {
            // localStorage.setItem('ted3textmemory-' + this.identifier, this.element.html());
            this._trigger('changed');
        },
        disable: function () {
            this.stopedit();
            $(this).removeClass('ted3-changed');
            this.element.html(this.originalContent);
            localStorage.removeItem('ted3textmemory-' + this.identifier);
        },
        stopedit: function () {
            var that = this;

            if (this.element.data('rte') == 1) {
                //  this.element.tinymce().hide();
                $('#ted3-editbar').css({
                    top: 0,
                    left: 0,
                });
            } else {
                this.element.attr('contenteditable', false);
            }
        },
        destroy: function () {
//           this.element.removeAttr('class'); 
            this.element.removeAttr('id');
            $.Widget.prototype.destroy.call(this);
            this.element.tinymce.remove();
        },
        preventForeignKeyevents: function () {
            this.element.on({
                keydown: function (e) {
                    if (Ted3.root.hasClass('ted3-mode-edit')) {
                        e.stopPropagation();
                    }
                },
                keyup: function (e) {
                    if (Ted3.root.hasClass('ted3-mode-edit')) {
                        e.stopPropagation();
                    }
                }
            });
        },
        clearmemory: function () {
            localStorage.removeItem('ted3textmemory-' + this.identifier);
        }

    });
}(Ted3.jQuery));