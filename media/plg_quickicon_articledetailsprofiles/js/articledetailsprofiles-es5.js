/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

(function (document) {
  // Ajax call to get the update status of the installed extension
  var fetchUpdateADP = function fetchUpdateADP() {
    if (Joomla.getOptions('js-article-details-profiles-check')) {
      var options = Joomla.getOptions('js-article-details-profiles-check');
      var update = function update(type, text, linkHref) {
        var link = document.getElementById('plg_quickicon_articledetailsprofiles');
        var linkSpans = [].slice.call(link.querySelectorAll('span.j-links-link'));
        if (link) {
          link.classList.add(type);
          if (linkHref) {
            link.setAttribute('href', linkHref);
          }
        }
        if (linkSpans.length) {
          linkSpans.forEach(function (span) {
            span.innerHTML = Joomla.sanitizeHtml(text);
          });
        }
      };

      /**
       * DO NOT use fetch() for QuickIcon requests. They must be queued.
       *
       * @see https://github.com/joomla/joomla-cms/issues/38001
       */
      Joomla.enqueueRequest({
        url: options.ajaxUrl,
        method: 'GET',
        promise: true
      }).then(function (xhr) {
        var response = xhr.responseText;
        var request = JSON.parse(response);
        if (Array.isArray(request)) {
          if (request.length === 0) {
            // No updates
            update('info', Joomla.Text._('PLG_QUICKICON_ARTICLEDETAILSPROFILES_UNKNOWN_MESSAGE'));
          } else {
            var updateInfo = request.shift();
            if (updateInfo.latest !== options.version) {
              update('danger', Joomla.Text._('PLG_QUICKICON_ARTICLEDETAILSPROFILES_VERSION_OUTDATED').replace('%s', "<span class=\"badge text-dark bg-light\"> \u200E ".concat(updateInfo.latest, "</span>")), options.updateUrl);
            } else {
              update('success', Joomla.Text._('PLG_QUICKICON_ARTICLEDETAILSPROFILES_VERSION_UPTODATE'));
            }
          }
        } else {
          // An error occurred
          update('info', Joomla.Text._('PLG_QUICKICON_ARTICLEDETAILSPROFILES_UNKNOWN_MESSAGE'));
        }
      })["catch"](function () {
        // An error occurred
        update('info', Joomla.Text._('PLG_QUICKICON_ARTICLEDETAILSPROFILES_UNKNOWN_MESSAGE'));
      });
    }
  };

  // Give some times to the layout and other scripts to settle their stuff
  window.addEventListener('load', function () {
    setTimeout(fetchUpdateADP, 360);
  });
})(document);