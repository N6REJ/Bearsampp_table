/**
 * @package         Tabs & Accordions
 * @version         2.1.2
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2024 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

(function() {
    'use strict';

    window.RegularLabs = window.RegularLabs || {};

    window.RegularLabs.TabsAccordionsButton = window.RegularLabs.TabsAccordionsButton || {

        form   : null,
        type   : 'tabs',
        options: {},

        setForm: function(form) {
            this.form = form;
        },

        insertText: function(editor_name) {
            this.options = Joomla.getOptions ? Joomla.getOptions('rl_tabsaccordions_button', {}) : Joomla.optionsStorage.rl_tabsaccordions_button || {};

            let html = this.renderHtml();

            if ( ! html) {
                return;
            }

            const editor = parent.Joomla.editors.instances[editor_name];

            html = this.prepareOutputForEditor(html, editor);

            editor.replaceSelection(html);
        },

        renderHtml: function() {
            const tag_tabs_open        = this.options.tag_tabs_open;
            const tag_tabs_close       = this.options.tag_tabs_close;
            const tag_accordions_open  = this.options.tag_accordions_open;
            const tag_accordions_close = this.options.tag_accordions_close;
            const tag_start            = this.options.tag_characters[0];
            const tag_end              = this.options.tag_characters[1];

            this.type = this.getValue('type');

            let tag_open  = this.type === 'tabs' ? tag_tabs_open : tag_accordions_open;
            let tag_close = this.type === 'tabs' ? tag_tabs_close : tag_accordions_close;

            if (this.getValue('nested')) {
                const nested_id = this.getValue('nested_id', 'nested');
                tag_open += '-' + nested_id;
                tag_close += '-' + nested_id;
            }

            const items = this.renderItems(tag_open);

            if (items === '') {
                return '';
            }

            return items + '<p>' + tag_start + '/' + tag_close + tag_end + '</p>';
        },

        prepareOutputForEditor: function(string, editor) {
            const editor_content   = editor.getValue();
            const editor_selection = editor.getSelection();

            // If the editor is CodeMirror
            if (editor_content === '' || editor_content[0] !== '<') {
                return string;
            }

            // If selection is empty or code is replacing a selection not starting with a html tag
            if (editor_selection.indexOf('<') !== 0) {
                // remove surrounding p tags
                return string.replace(/^<p>(.*)<\/p>$/g, '$1');
            }

            return string;
        },

        renderItems: function(tag_open) {
            const items = [];

            let is_first = true;

            this.form.querySelectorAll('input[name^="items[items"][name$="][title]"]').forEach((field) => {
                if (field.value.trim() === '') {
                    return;
                }

                const id = field.name.match(/\[items([0-9]+)\]/)[1] * 1;

                items.push(this.renderItem(id, tag_open, is_first));

                is_first = false;
            });

            let html = items.join('');

            html = html.replace(/ open="false"(.*? open="true")/g, '$1');

            return html;
        },

        getMainAttributes: function() {
            const attributes = [];

            this.addAttribute(attributes, 'theme');
            this.addAttribute(attributes, 'color_panels', 'color-panels');
            this.addAttribute(attributes, 'class');
            if (this.type === 'tabs') {
                this.addAttribute(attributes, 'alignment');
            }

            return attributes;
        },

        addItemAttribute: function(attributes, id, name, true_value = null) {
            this.addAttribute(attributes, 'items[items' + id + '][' + name + ']', name, true_value);
        },

        addAttribute: function(attributes, id, key = null, true_value = null) {
            key = key ? key : id;

            let value = this.getValue(id);

            // join value if it is an array
            if (Array.isArray(value)) {
                value = value.join(',');
            }

            if (value !== '' && value !== 'false' && value !== undefined && value !== null) {
                attributes.push(key + '="' + (true_value !== null ? true_value : value) + '"');
            }
        },

        renderItem: function(id, tag_open, is_first) {
            const tag_start = this.options.tag_characters[0];
            const tag_end   = this.options.tag_characters[1];

            const first_attributes = [];
            const extra_attributes = is_first ? this.getMainAttributes() : [];
            const item_attributes  = [];

            this.addItemAttribute(first_attributes, id, 'title');

            const open = this.getItemValue(id, 'open');

            if (
                (open && ! is_first)
                || ( ! open && is_first)
            ) {
                item_attributes.push('open' + '="' + (open ? 'true' : 'false') + '"');
            }


            const attributes = [
                ...first_attributes,
                ...extra_attributes,
                ...item_attributes,
            ];

            let content = this.getItemValue(id, 'content');
            content     = content === '' ? '<p>...</p>' : content;

            return '<p>' + tag_start + (tag_open + ' ' + attributes.join(' ')).trim() + tag_end + '</p>'
                + content;
        },

        getItemValue: function(id, name, default_value = '') {
            return this.getValue('items[items' + id + '][' + name + ']', default_value);
        },

        getValue: function(id, default_value = '') {
            let elements = this.form.querySelectorAll('[name="' + id + '"]');

            if ( ! elements.length) {
                elements = this.form.querySelectorAll('[name="' + id + '[]"]');
            }

            if ( ! elements.length) {
                return default_value;
            }

            const element = elements[0];

            if (element.type === 'textarea') {
                return this.fixType(this.getEditorContent(element));
            }

            let value = element.value ? element.value : default_value;

            if (element.type === 'select-one') {
                if (element.type === 'checkbox' && ! element.checked) {
                    return default_value;
                }

                return this.fixType(value);
            }

            if (element.type === 'select-multiple') {
                value = [];

                for (let i = 0; i < element.options.length; i++) {
                    if (element.options[i].selected && element.options[i].value !== '') {
                        value.push(element.options[i].value);
                    }
                }

                return this.fixType(value);
            }

            if (elements.length > 1) {
                value = [];

                for (let i = 0; i < elements.length; i++) {
                    if ((elements[i].selected || elements[i].checked) && elements[i].value !== '') {
                        value.push(elements[i].value);
                    }
                }

                if (element.type === 'radio') {
                    return this.fixType(value[0]);
                }

                return this.fixType(value);
            }

            return this.fixType(value);
        },

        fixType: function(value) {
            // if it is an array, run fixType on each value
            if (Array.isArray(value)) {
                value.forEach((val, index) => {
                    value[index] = this.fixType(val);
                });

                return value;
            }

            if (isNaN(value) || isNaN(parseInt(value))) {
                return value;
            }

            return Number(value);
        },

        getEditorContent: function(element) {
            const editor = this.form.editors[element.id];

            if (typeof editor === 'undefined') {
                return element.value;
            }

            const value = editor.getValue();

            if (typeof value === 'undefined') {
                return '';
            }

            return value.replace(/^<p><\/p>$/, '');
        },
    };
})();
