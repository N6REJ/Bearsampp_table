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

    window.RegularLabs.TabsAccordionsPopup = window.RegularLabs.TabsAccordionsPopup || {
        form: null,

        init: function() {

            if ( ! parent.RegularLabs.TabsAccordionsButton) {
                document.querySelector('body').innerHTML = '<div class="alert alert-error">This page cannot function on its own.</div>';
                return;
            }

            const first_editor = Joomla.editors.instances['items_items0__content'];

            try {
                first_editor.getValue();
            } catch (err) {
                setTimeout(() => {
                    RegularLabs.TabsAccordionsPopup.init();
                }, 100);
                return;
            }

            this.form         = document.getElementById('tabsAccordionsForm');
            this.form.editors = Joomla.editors.instances;

            parent.RegularLabs.TabsAccordionsButton.setForm(this.form);

            setTimeout(() => {
                document.addEventListener('joomla:updated', () => {
                    addEventListeners();
                });
                addEventListeners();
            }, 10);

            this.form.querySelector('input[name^="items[items0][open]"][value="1"]').checked = true;

            const self = this;

            function addEventListeners() {
                self.form.querySelectorAll('input[name="type"][value="accordions"]:not(.has-listener)').forEach((el) => {
                    el.addEventListener('change', () => {
                        if ( ! el.checked) {
                            return;
                        }

                        const first_open_false = self.form.querySelector('input[name^="items[items"][name$="][open]"]');

                        if ( ! first_open_false) {
                            return;
                        }

                        first_open_false.checked = true;
                    });
                    Regular.addClass(el, 'has-listener');
                });

                self.form.querySelectorAll('input[name^="items[items"][name$="][open]"]:not(.has-listener)').forEach((el) => {
                    el.addEventListener('change', () => {
                        self.deselectOtherDefaults(el);
                    });
                    Regular.addClass(el, 'has-listener');
                });

                // Fix broken references to fields in subform (stupid Joomla!)
                self.form.querySelectorAll('.subform-repeatable-group').forEach((group) => {
                    const group_name = group.dataset['group'];
                    const x_name     = group.dataset['baseName'] + 'X';

                    const regex = new RegExp(x_name, 'g');

                    const sub_elements = group.querySelectorAll(
                        `[id*="${group_name}_"],`
                        + `[id*="${x_name}_"],`
                        + `[data-for*="${x_name}_"],`
                        + `[data-for*="${x_name}]"],`
                        + `span[onclick*="${x_name}_"],`
                        + `span[onclick*="${x_name}]"]`
                    );

                    sub_elements.forEach((el) => {
                        if (el.dataset['for']) {
                            el.dataset['for'] = el.dataset['for'].replace(regex, group_name);
                        }
                        if (el.getAttribute('onclick')) {
                            el.setAttribute('onclick',
                                el.getAttribute('onclick').replace(regex, group_name)
                            );
                        }
                        if (el.id) {
                            el.id = el.id.replace(regex, group_name);
                        }
                    });
                });
            }
        },

        deselectOtherDefaults: function(el) {
            if (el.value !== '1' || ! el.checked) {
                return;
            }

            this.form.querySelectorAll('input[name^="items[items"][name$="][open]"]').forEach((el) => {
                el.checked = (el.value !== '1');
            });

            el.checked = true;
        }
    };
})();
