var ACF_Gallery_Init=function(){function t(){"undefined"!=typeof Dropzone&&this.init()}var e=t.prototype;return e.init=function(){var e=this;document.querySelectorAll("joomla-field-subform").forEach(function(t){t.addEventListener("subform-row-add",function(t){e.setup(e.getElements(t.detail.row),t.detail.row),e.initInstances(e.getElements(t.detail.row))})})},e.setup=function(t,e){var r=this;t.forEach(function(t){r.initMediaLibraryModal(t),r.updateAttributes(t,e)})},e.initInstances=function(t){t.forEach(function(t){new TF_Gallery_Manager(t).init()})},e.updateAttributes=function(t,e){var r=e.dataset.group.replace("row","");t.setAttribute("class",t.getAttribute("class").replace("__rowX__","__row"+r+"__"));var o=t.querySelector(".tf-gallery-browse-item-button");o.setAttribute("data-bs-target",o.getAttribute("data-bs-target").replace("__rowX__","__row"+r+"__"));var a=t.querySelector(".media_uploader_file");a.setAttribute("id",a.id.replace("__rowX__","__row"+r+"__"));var n=t.querySelector(".tf-gallery-dz");n.setAttribute("data-inputname",n.getAttribute("data-inputname").replace("[rowX]","[row"+r+"]"));var i=t.querySelector(".modal");i.setAttribute("id",i.id.replace("__rowX__","__row"+r+"__"));var u=t.querySelector("template.previewTemplate"),l=u.content.querySelector('input[type="checkbox"]');l.setAttribute("id",l.id.replace("[rowX]","[row"+r+"]"));var c=u.content.querySelector("label");c.setAttribute("for",c.getAttribute("for").replace("[rowX]","[row"+r+"]"));var d=u.content.querySelector("textarea");d.setAttribute("name",d.getAttribute("name").replace("[rowX]","[row"+r+"]"));var s=u.content.querySelector("input.item-source");s&&s.setAttribute("name",s.getAttribute("name").replace("[rowX]","[row"+r+"]"));var b=u.content.querySelector("input.item-thumbnail");b.setAttribute("name",b.getAttribute("name").replace("[rowX]","[row"+r+"]"));var p=u.content.querySelector("input.item-caption");p.setAttribute("name",p.getAttribute("name").replace("[rowX]","[row"+r+"]"));var _=u.content.querySelector("input.item-tags");_.setAttribute("name",_.getAttribute("name").replace("[rowX]","[row"+r+"]"))},e.initMediaLibraryModal=function(t){var e=t.querySelector(":scope > .modal"),r=Joomla.getOptions("bootstrap.modal")["#"+e.id],o={backdrop:!r.backdrop||r.backdrop,keyboard:!r.keyboard||r.keyboard,focus:!r.focus||r.focus};Joomla.initialiseModal(e,o)},e.getElements=function(t){return void 0===t&&(t=""),(t=""==t?document:t).querySelectorAll(".tf-gallery-manager")},t}();document.addEventListener("DOMContentLoaded",function(t){new ACF_Gallery_Init});
