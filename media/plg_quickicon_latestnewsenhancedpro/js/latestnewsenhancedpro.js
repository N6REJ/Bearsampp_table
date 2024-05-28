/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

(document => {

	// Ajax call to get the update status of the installed extension
	const fetchUpdateLNEP = () => {
		if (Joomla.getOptions('js-latest-news-enhanced-check')) {
		    const options = Joomla.getOptions('js-latest-news-enhanced-check');
		    const update = (type, text, linkHref) => {
			    const link = document.getElementById('plg_quickicon_latestnewsenhancedpro');
			    const linkSpans = [].slice.call(link.querySelectorAll('span.j-links-link'));
			    if (link) {
			      link.classList.add(type);
		          if (linkHref) {
		            link.setAttribute('href', linkHref);
		          }
			    }
			    if (linkSpans.length) {
			      linkSpans.forEach(span => {
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
		    }).then(xhr => {
		      const response = xhr.responseText;
		      const request = JSON.parse(response);
		      if (Array.isArray(request)) {
		        if (request.length === 0) {
		          // No updates
		          update('info', Joomla.Text._('PLG_QUICKICON_LATESTNEWSENHANCEDEXTENDED_UNKNOWN_MESSAGE'));
		        } else {
		          const updateInfo = request.shift();
		          if (updateInfo.latest !== options.version) {
		            update('danger', Joomla.Text._('PLG_QUICKICON_LATESTNEWSENHANCEDEXTENDED_VERSION_OUTDATED').replace('%s', `<span class="badge text-dark bg-light"> \u200E ${updateInfo.latest}</span>`), options.updateUrl);
		          } else {
		            update('success', Joomla.Text._('PLG_QUICKICON_LATESTNEWSENHANCEDEXTENDED_VERSION_UPTODATE'));
		          }
		        }
		      } else {
		        // An error occurred
		        update('info', Joomla.Text._('PLG_QUICKICON_LATESTNEWSENHANCEDEXTENDED_UNKNOWN_MESSAGE'));
		      }
		    }).catch(() => {
		      // An error occurred
		      update('info', Joomla.Text._('PLG_QUICKICON_LATESTNEWSENHANCEDEXTENDED_UNKNOWN_MESSAGE'));
		    });
		}
	};

	// Give some times to the layout and other scripts to settle their stuff
	window.addEventListener('load', () => {
		setTimeout(fetchUpdateLNEP, 360);
	});
})(document);
