function Toolbox(container)
{
    this.container = container;

    this.initEvents();
}

Toolbox.prototype = {
    initEvents: function() {
        var self = this;
        this.container.on("click", "button", function() {
            jQuery(document).trigger("pictorials:" + this.dataset.event);
        });
        jQuery(document).on("pictorials:path_changed", function() {
            self.container.hide();
        });
        jQuery(document).on("pictorials:path_chosen", function() {
            self.container.show();
        });
    }
};
