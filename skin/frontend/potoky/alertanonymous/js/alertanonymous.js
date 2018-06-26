/**
 * Created by Alex on 6/22/2018.
 */
potokyAlertAnonymous = {

    createPopup: function (element) {
        jQuery(element).dialog({
            autoOpen: true,
            modal: true
        });
    },

    createForm: function (templateId, actionUrl) {
        var formId = templateId + '-email';
        var present = document.getElementById(formId);
        if (present) {
            present.remove();
        }
        var elem = document.getElementById(templateId);
        var form = document.createElement('form');
        this.addAttributes(form, {
            "id": formId,
            "action": actionUrl,
            "method": "post"
        });
        var fieldset = document.createElement('fieldset');
        var label = document.createElement('label');
        this.addAttributes(label, {
            "for": "email"
        });
        label.innerHTML = 'Please enter Your email';
        var input = document.createElement('input');
        this.addAttributes(input, {
            "type": "text",
            "name": "email",
            "id": formId + "input",
            "class": "text ui-widget-content ui-corner-all",
            "style": "width: auto"
        });
        var button = document.createElement('button');
        this.addAttributes(button,{
            "type": "submit",
            "style": {
                display: "inherit",
                margin: "0 auto",
                margin-top: "20px",
                padding: "1px 13px"
            }
        });
        button.innerHTML = "Submit";
        fieldset.appendChild(label);
        fieldset.appendChild(input);
        fieldset.appendChild(button);
        form.appendChild(fieldset);
        elem.appendChild(form);
        this.createPopup(form);
    },

    addAttributes: function (element, attributes) {
            for(var i in attributes) {
                element.setAttribute(i, attributes[i]);
            }
    }
};
