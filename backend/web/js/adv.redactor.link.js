$.Redactor.prototype.link = function () {
	return {
		getTemplate: function () {
			return String()
				+ '<section id="redactor-modal-link-insert">'
				+ '<label>URL</label>'
				+ '<input type="url" id="redactor-link-url" aria-label="URL" />'
				+ '<label>' + this.lang.get('text') + '</label>'
				+ '<input type="text" id="redactor-link-url-text" aria-label="' + this.lang.get('text') + '" />'
				+ '<label>' + this.lang.get('title') + '</label>'
				+ '<input type="text" id="redactor-link-url-title" aria-label="' + this.lang.get('title') + '" />'
				+ '<label><input type="checkbox" id="redactor-link-blank"> ' + this.lang.get('link_new_tab') + '</label>'
				+ '</section>';
		},
		show: function (e) {
			if (typeof e != 'undefined' && e.preventDefault) e.preventDefault();

			this.modal.addTemplate('link', this.link.getTemplate());

			if (!this.observe.isCurrent('a')) {
				this.modal.load('link', this.lang.get('link_insert'), 600);
			}
			else {
				this.modal.load('link', this.lang.get('link_edit'), 600);
			}

			this.modal.createCancelButton();

			var buttonText = !this.observe.isCurrent('a') ? this.lang.get('insert') : this.lang.get('edit');

			this.link.buttonInsert = this.modal.createActionButton(buttonText);

			this.selection.get();

			this.link.getData();
			this.link.cleanUrl();

			if (this.link.target == '_blank') $('#redactor-link-blank').prop('checked', true);

			this.link.$inputUrl = $('#redactor-link-url');
			this.link.$inputText = $('#redactor-link-url-text');
			this.link.$inputTitle = $('#redactor-link-url-title');

			this.link.$inputTitle.val(this.link.title);
			this.link.$inputText.val(this.link.text);
			this.link.$inputUrl.val(this.link.url);

			this.link.buttonInsert.on('click', $.proxy(this.link.insert, this));

			// hide link's tooltip
			$('.redactor-link-tooltip').remove();

			// show modal
			this.selection.save();
			this.modal.show();
			this.link.$inputUrl.focus();
		},
		cleanUrl: function () {
			var thref = self.location.href.replace(/\/$/i, '');

			if (typeof this.link.url !== "undefined") {
				this.link.url = this.link.url.replace(thref, '');
				this.link.url = this.link.url.replace(/^\/#/, '#');
				this.link.url = this.link.url.replace('mailto:', '');

				// remove host from href
				if (!this.opts.linkProtocol) {
					var re = new RegExp('^(http|ftp|https)://' + self.location.host, 'i');
					this.link.url = this.link.url.replace(re, '');
				}
			}
		},
		getData: function () {
			this.link.$node = false;

			var $el = $(this.selection.getCurrent()).closest('a', this.$editor[0]);
			if ($el.length !== 0 && $el[0].tagName === 'A') {
				this.link.$node = $el;

				this.link.url = $el.attr('href');
				this.link.text = $el.text();
				this.link.title = $el.attr('title');
				this.link.target = $el.attr('target');
			}
			else {
				this.link.text = this.sel.toString();
				this.link.url = '';
				this.link.title = '';
				this.link.target = '';
			}

		},
		insert: function () {
			this.placeholder.remove();

			var target = '';
			var link = this.link.$inputUrl.val();
			var title = this.link.$inputTitle.val();
			var text = this.link.$inputText.val().replace(/(<([^>]+)>)/ig, "");

			if ($.trim(link) === '') {
				this.link.$inputUrl.addClass('redactor-input-error').on('keyup', function () {
					$(this).removeClass('redactor-input-error');
					$(this).off('keyup');

				});

				return;
			}

			// mailto
			if (link.search('@') != -1 && /(http|ftp|https):\/\//i.test(link) === false) {
				link = link.replace('mailto:', '');
				link = 'mailto:' + link;
			}
			// url, not anchor
			else if (link.search('#') !== 0) {
				if ($('#redactor-link-blank').prop('checked')) {
					target = '_blank';
				}

				// test url (add protocol)
				var pattern = '((xn--)?[a-z0-9]+(-[a-z0-9]+)*\\.)+[a-z]{2,}';
				var re = new RegExp('^(http|ftp|https)://' + pattern, 'i');
				var re2 = new RegExp('^' + pattern, 'i');
				var re3 = new RegExp('\.(html|php)$', 'i');
				if (link.search(re) == -1 && link.search(re3) == -1 && link.search(re2) === 0 && this.opts.linkProtocol) {
					link = this.opts.linkProtocol + '://' + link;
				}
			}

			this.link.set(text, link, title, target);
			this.modal.close();
		},
		set: function (text, link, title, target) {
			text = $.trim(text.replace(/<|>/g, ''));

			this.selection.restore();
			var blocks = this.selection.getBlocks();

			if (text === '' && link === '') return;
			if (text === '' && link !== '') text = link;

			if (this.link.$node) {
				this.buffer.set();

				var $link = this.link.$node,
					$el = $link.children();

				if ($el.length > 0) {
					while ($el.length) {
						$el = $el.children();
					}

					$el = $el.end();
				}
				else {
					$el = $link;
				}

				$link.attr('href', link);
				$link.attr('title', title);
				$el.text(text);

				if (target !== '') {
					$link.attr('target', target);
				}
				else {
					$link.removeAttr('target');
				}

				this.selection.selectElement($link);

				this.code.sync();
			}
			else {
				if (this.utils.browser('mozilla') && this.link.text === '') {
					var $a = $('<a />').attr('href', link).attr('title', title).text(text);
					if (target !== '') $a.attr('target', target);

					$a = $(this.insert.node($a));

					if (this.opts.linebreaks) {
						$a.after('&nbsp;');
					}

					this.selection.selectElement($a);
				}
				else {
					var $a;
					if (this.utils.browser('msie')) {
						$a = $('<a href="' + link + '" title="' + title + '">').text(text);
						if (target !== '') $a.attr('target', target);

						$a = $(this.insert.node($a));

						if (this.selection.getText().match(/\s$/)) {
							$a.after(" ");
						}

						this.selection.selectElement($a);
					}
					else {
						document.execCommand('createLink', false, link);

						$a = $(this.selection.getCurrent()).closest('a', this.$editor[0]);
						$a.attr('title', title);
						if (this.utils.browser('mozilla')) {
							$a = $('a[_moz_dirty=""]');
						}

						if (target !== '') $a.attr('target', target);
						$a.removeAttr('style').removeAttr('_moz_dirty');

						if (this.selection.getText().match(/\s$/)) {
							$a.after(" ");
						}

						if (this.link.text !== '' || this.link.text != text) {
							if (!this.opts.linebreaks && blocks && blocks.length <= 1) {
								$a.text(text);
							}
							else if (this.opts.linebreaks) {
								$a.text(text);
							}

							this.selection.selectElement($a);
						}
					}
				}

				this.code.sync();
				this.core.setCallback('insertedLink', $a);

			}

			// link tooltip
			setTimeout($.proxy(function () {
				this.observe.links();

			}, this), 5);
		},
		unlink: function (e) {
			if (typeof e != 'undefined' && e.preventDefault) {
				e.preventDefault();
			}

			var nodes = this.selection.getNodes();
			if (!nodes) return;

			this.buffer.set();

			var len = nodes.length;
			var links = [];
			for (var i = 0; i < len; i++) {
				if (nodes[i].tagName === 'A') {
					links.push(nodes[i]);
				}

				var $node = $(nodes[i]).closest('a', this.$editor[0]);
				$node.replaceWith($node.contents());
			}

			this.core.setCallback('deletedLink', links);

			// hide link's tooltip
			$('.redactor-link-tooltip').remove();

			this.code.sync();

		},
		toggleClass: function (className) {
			this.link.setClass(className, 'toggleClass');
		},
		addClass: function (className) {
			this.link.setClass(className, 'addClass');
		},
		removeClass: function (className) {
			this.link.setClass(className, 'removeClass');
		},
		setClass: function (className, func) {
			var links = this.selection.getInlinesTags(['a']);
			if (links === false) return;

			$.each(links, function () {
				$(this)[func](className);
			});
		}
	};
};
