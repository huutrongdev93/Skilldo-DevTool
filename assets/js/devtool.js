let empty = {
	options: [],
	args: []
};

function TerminalDevTool(){}

TerminalDevTool.isLoaded = false;
TerminalDevTool.isLoading = false;
TerminalDevTool.loadPromise = null;

TerminalDevTool.commands = {
	'help': {...empty},
	'cms:version': {...empty},
	'cms:build:js': {options: ['no-obfuscate'], args: []},
	'cache:clear': {...empty},
	'cache:view': {...empty},
	'cache:lang': {...empty},
	'theme:child:copy': {...empty},
	'lang:build': {...empty},
	'plugin': {...empty},
	'plugin:activate': {...empty},
	'plugin:deactivate': {...empty},
	'db:show': {...empty},
	'db:table': {...empty},
	'db:empty': {...empty},
	'db:seed': {options: ['module'], args: []},
	'db:run:theme': {...empty},
	'db:run:plugin': {...empty},
	'make:db:theme': {...empty},
	'make:db:plugin': {...empty},
	'make:command': {...empty},
	'make:plugin': {...empty},
	'make:form-field:theme': {...empty},
	'make:form-field:plugin': {...empty},
	'make:macro:theme': {...empty},
	'make:macro:plugin': {...empty},
	'make:popover:theme': {...empty},
	'make:popover:plugin': {...empty},
	'make:lang:theme': {...empty},
	'make:lang:plugin': {...empty},
	'make:column:theme': {...empty},
	'make:column:plugin': {...empty},
	'make:element': {options: ['type'], args: []},
	'make:widget': {options: ['type'], args: []},
	'make:widget:sidebar': {...empty},
	'make:taxonomy': {options: ['plugin'], args: []},
	'make:ajax:theme': {...empty},
	'make:ajax:plugin': {...empty},
	'make:controller:theme': {...empty},
	'make:controller:plugin': {...empty},
	'make:supports:theme': {...empty},
	'make:supports:plugin': {...empty},
	'make:services:theme': {...empty},
	'make:services:plugin': {...empty},
	'make:table': {options: ['plugin'], args: []},
	'make:model:theme': {options: ['db'], args: []},
	'make:model:plugin': {options: ['db'], args: []},
	'make:module:theme': {...empty},
	'make:module:plugin': {...empty},
	'make:provider:theme': {...empty},
	'make:provider:plugin': {...empty},
	'make:middleware:theme': {...empty},
	'make:middleware:plugin': {...empty},
	'auth:logout': {...empty},
	'user:password': {...empty},
	'user:username': {...empty},
	'role:list': {...empty},
	'role:cap': {...empty},
	'license': {...empty},
	'license:change': {...empty},
	'close': {...empty},
	'builder:export': {...empty},
	'builder:import': {options: ['no-backup'], args: []}
}

TerminalDevTool.echo = function(response, term) {
	if(response.data?.type) {
		if(response.data.type === 'plugins') {
			TerminalDevTool.plugins(response.data.plugins, term)
		}
		if(response.data.type === 'plugin-info') {
			TerminalDevTool.pluginInfo(response.data.plugin, term)
		}
	}
	else {
		term.echo(response.message, {newline: true});
		response.data.map(function(message, index) {
			term.echo(message, {newline: true});
		})
		term.resume();
	}
};

TerminalDevTool.plugins = function(plugins, term) {

	let data = [['Name', 'Active']];

	Object.keys(plugins).map(function(value) {
		data.push([value, plugins[value]]);
	})

	term.echo(ascii_table(data, true));

	term.resume();
};

TerminalDevTool.pluginInfo = function(plugin, term) {

	let data = [['Name', 'Value']];

	Object.keys(plugin).map(function(value) {
		data.push([value, plugin[value]]);
	})

	term.echo(ascii_table(data, true));

	term.resume();
};

TerminalDevTool.errors = function(response, term) {
	term.error(response.message, {newline: true});
	response.data.map(function(message, index) {
		term.error(message, {newline: true});
	})
	term.resume();
};

TerminalDevTool.color = function (name, string, bold = 'b') {
	let colors = {
		blue:   '#55f',
		green:  '#59ebed',
		grey:   '#999',
		red:    '#A00',
		yellow: '#FF5',
		violet: '#6871ff',
		white:  '#fff'
	}
	if (colors[name]) {
		return '[['+bold+';' + colors[name] + ';]' + string + ']';
	} else {
		return string;
	}
}

TerminalDevTool.autoCompletePath = function(command, callback) {
	let input = command.split(" ").pop(); // Lấy phần cuối cùng của lệnh (tức là phần mà người dùng đang gõ)
	let matches = [];

	// Lặp qua các đường dẫn và tìm các đường dẫn khớp
	for (var i = 0; i < TerminalDevTool.paths.length; i++) {
		if (TerminalDevTool.paths[i].startsWith(input)) {
			matches.push(TerminalDevTool.paths[i]);
		}
	}

	// Gọi callback với danh sách các đường dẫn khớp
	callback(matches);
}

TerminalDevTool.ajax = function(command, term) {

	term.pause();

	let data = {
		action: 'DevTool\\Ajax\\DevToolAjax::terminal',
		command: command
	}

	request.post(ajax, data).then(function(response) {

		if(response.status === 'error')
		{
			TerminalDevTool.errors(response, term)
			return false;
		}

		if(response.status === 'success')
		{
			if(command.startsWith('make:plugin'))
			{
				TerminalDevTool.dataAutocompletion()
			}
		}

		TerminalDevTool.echo(response, term)

	}).catch(function() {
		term.echo('Lỗi chưa xác định', {newline: true});
		term.resume();
	})
}

TerminalDevTool.loadTerminalLibraries = function() {
	// Nếu đã load rồi thì return resolved promise
	if (TerminalDevTool.isLoaded) {
		return Promise.resolve();
	}

	// Nếu đang load thì return promise hiện tại
	if (TerminalDevTool.isLoading) {
		return TerminalDevTool.loadPromise;
	}

	// Bắt đầu load
	TerminalDevTool.isLoading = true;

	TerminalDevTool.loadPromise = new Promise(function(resolve, reject) {
		let terminalData = $('#terminal-data');

		// Load CSS
		let cssLink = $('<link>', {
			rel: 'stylesheet',
			href: terminalData.data('terminal-css')
		});
		$('head').append(cssLink);

		// Load Font (Roboto Mono)
		let fontLink = $('<link>', {
			rel: 'stylesheet',
			href: 'https://fonts.googleapis.com/css?family=Roboto+Mono&display=swap'
		});
		$('head').append(fontLink);

		// Load các script theo thứ tự
		let scripts = [
			terminalData.data('terminal-js'),      // jquery.terminal.min.js
			terminalData.data('prism-js'),          // prism.js
			terminalData.data('keyboard-js'),       // keyboard.js
			terminalData.data('wcwidth-js'),        // wcwidth.js
			terminalData.data('ascii-js'),          // ascii_table.js
			terminalData.data('autocomplete-js')    // autocomplete_menu.js
		];

		// Function để load script tuần tự
		function loadScriptSequentially(index) {
			if (index >= scripts.length) {
				TerminalDevTool.isLoaded = true;
				TerminalDevTool.isLoading = false;
				resolve();
				return;
			}

			let script = document.createElement('script');
			script.src = scripts[index];
			script.onload = function() {
				loadScriptSequentially(index + 1);
			};
			script.onerror = function() {
				TerminalDevTool.isLoading = false;
				reject(new Error('Failed to load: ' + scripts[index]));
			};
			document.head.appendChild(script);
		}

		loadScriptSequentially(0);
	});

	return TerminalDevTool.loadPromise;
}

TerminalDevTool.run = function (element) {

	let terminalDataElement = $('#terminal-data');

	let cmsVersion = terminalDataElement.data('cms-version');

	$(element).terminal(function(command, term) {

		if(command === 'close')
		{
			TerminalDevTool.close();
			return false;
		}
		else if(command === 'make:taxonomy')
		{
			term.read('enter post type: ').then(function(postType) {
				term.read('enter cate type: ').then(function(cateType) {
					command = command + ' ' + postType + ' ' + cateType;
					TerminalDevTool.ajax(command, term)
				});
			});
			return false;
		}
		else if(command === 'make:module:theme')
		{
			term.read('Enter Module name: ').then(function(module)
			{
				term.read('Enter database table name: ').then(function(modelTableName)
				{
					if(module.length === '' || module.length === undefined || module.length === null)
					{
						term.echo('module can\'t empty', {newline: true});
						term.resume();
						return false;
					}
					if(modelTableName.length === '' || modelTableName.length === undefined || modelTableName.length === null)
					{
						term.echo('database table name can\'t empty', {newline: true});
						term.resume();
						return false;
					}

					command = command.split(" ")[0];
					command = command + ' ' + module + ' ' + modelTableName;
					TerminalDevTool.ajax(command, term)
				});
			});
			return false;
		}
		else if(command === 'user:password')
		{
			term.read('username: ').then(function(username)
			{
				term.set_mask('*').read('Password: ').then(function(password)
				{
					command = command.split(" ")[0];
					command = command + ' ' + username + ' ' + password;
					term.set_mask(false);
					TerminalDevTool.ajax(command, term)
				});
			});
			return false;
		}
		else if(command === 'user:username')
		{
			term.read('username change: ').then(function(usernameOld)
			{
				term.read('username new: ').then(function(username)
				{
					command = command.split(" ")[0];
					command = command + ' ' + usernameOld + ' ' + username;
					TerminalDevTool.ajax(command, term)
				});
			});
			return false;
		}
		else
		{
			TerminalDevTool.ajax(command, term)
		}

	}, {
		autocompleteMenu: true,
		greetings: function () {
			return [
				TerminalDevTool.color('green', 'Cms SkillDo'),
				TerminalDevTool.color('white', ' version '),
				TerminalDevTool.color('yellow', cmsVersion),
				'\n',
				'Type command or access the link documentation for list commands',
				'\n',
				'Use '+ TerminalDevTool.color('red', 'close') +' command to close the terminal'
			].join('');
		},
		prompt: function(command, term) {
			let url = domain.replace(/(^\w+:|^)\/\//, '');

			url = url.replace(/\//g, '');

			return [
				TerminalDevTool.color('green', 'root&#64;'),
				TerminalDevTool.color('violet', url),
				TerminalDevTool.color('white', ' via '),
				TerminalDevTool.color('violet', 'php-' + term.attr('data-php') + ':'),
				TerminalDevTool.color('blue', '~/'),
				'$ '
			].join('');
		},
		completion: function() {
			let term = this;
			// return promise if completion need to be async
			// in this example it's not needed, you can just retrun an array
			return new Promise(function(resolve) {
				let command = term.get_command();
				let name = command.match(/^([^\s]*)/)[0];
				let list = [];
				if (name) {
					let word = term.before_cursor(true);
					if (name === word) {
						list = Object.keys(TerminalDevTool.commands);
					} else if (command.match(/\s/)) {
						if (word.match(/^--/)) {
							list = TerminalDevTool.commands[name].options.map(function(option) {
								return '--' + option;
							});
						} else {
							list = TerminalDevTool.commands[name].args;
						}
					}
				}
				resolve(list);
			});
		}
	});
}

TerminalDevTool.dataAutocompletion = function() {

	let data = {
		action: 'DevTool\\Ajax\\DevToolAjax::terminalData',
	}

	request.post(ajax, data).then(function(response) {

		if(response.status === 'success')
		{
			TerminalDevTool.commands['theme:child:copy'].args = response.data.themes;
			TerminalDevTool.commands['plugin'].args = response.data.plugins;
			TerminalDevTool.commands['plugin:activate'].args = response.data.plugins;
			TerminalDevTool.commands['plugin:deactivate'].args = response.data.plugins;
			TerminalDevTool.commands['make:db:plugin'].args = response.data.plugins;
			TerminalDevTool.commands['make:macro:plugin'].args = response.data.plugins;
			TerminalDevTool.commands['make:popover:plugin'].args = response.data.plugins;
			TerminalDevTool.commands['make:column:plugin'].args = response.data.plugins;
			TerminalDevTool.commands['make:middleware:plugin'].args = response.data.plugins;
			TerminalDevTool.commands['make:form-field:plugin'].args = response.data.plugins;
			TerminalDevTool.commands['make:lang:plugin'].args = response.data.plugins;
			TerminalDevTool.commands['make:ajax:plugin'].args = response.data.plugins;
			TerminalDevTool.commands['make:controller:plugin'].args = response.data.plugins;
			TerminalDevTool.commands['make:model:plugin'].args = response.data.plugins;
			TerminalDevTool.commands['make:module:plugin'].args = response.data.plugins;
			TerminalDevTool.commands['make:supports:plugin'].args = response.data.plugins;
			TerminalDevTool.commands['make:services:plugin'].args = response.data.plugins;
			TerminalDevTool.commands['make:provider:plugin'].args = response.data.plugins;
			TerminalDevTool.commands['db:run:plugin'].args = response.data.plugins;
		}
		else
		{
			SkilldoMessage.response(response);
		}
	})

}

TerminalDevTool.open = function(element) {

	// Load thư viện terminal nếu chưa load
	TerminalDevTool.loadTerminalLibraries().then(function() {
		// Sau khi load xong thư viện, load dữ liệu autocomplete
		if(TerminalDevTool.commands['plugin'].args?.length === 0 || TerminalDevTool.commands['theme:child:copy'].args?.length === 0 )
		{
			TerminalDevTool.dataAutocompletion()
		}

		// Khởi tạo terminal (chỉ khởi tạo 1 lần)
		if (!$('.terminal').data('terminal')) {
			TerminalDevTool.run('.terminal')
		}

		// Mở terminal
		let terminalWrapper = $('.devtool-terminal-wrapper');
		terminalWrapper.addClass('open')
	}).catch(function(error) {
		console.error('Error loading terminal libraries:', error);
		alert('Không thể tải terminal. Vui lòng thử lại.');
	});

	return false;
}

TerminalDevTool.close = function(element) {
	let terminalWrapper = $('.devtool-terminal-wrapper');
	terminalWrapper.removeClass('open')
	return false;
}

class DevToolSidebar {
	constructor() {

		this.sidebar = $('.devTools-sidebar');

		this.sidebar.show()

		this.open = localStorage.getItem('devTool-open')

		if(this.open === 'true') {
			this.sidebar.addClass('open')
		}

		this.tabElActive = localStorage.getItem('devTool-tab-active')

		if(this.tabElActive) {
			$(this.tabElActive).trigger('click');
		}

		this.event()
	}
	toggle() {
		localStorage.setItem('devTool-open', !this.sidebar.hasClass('open'))
		this.sidebar.toggleClass('open')
		return false
	}
	clickTab(element) {
		localStorage.setItem('devTool-tab-active', '#' + element.attr('id'))
	}
	cacheClear(element) {

		let button = SkilldoUtil.buttonLoading(element);

		button.start()

		let data =  {
			action: 'DevTool\\Ajax\\DevToolAjax::cacheClear',
			type: element.data('type')
		}

		request.post(ajax, data).then(function(response) {
			button.stop()
			SkilldoMessage.response(response);
		})
		.catch(function(error) {
			button.stop()
		})

		return false
	}
	saveLayout(element) {

		let button = SkilldoUtil.buttonLoading('button[form="devTools-form-layout"]');

		button.start()

		let data = element.serializeJSON();

		data.action = 'DevTool\\Ajax\\DevToolAjax::saveLayout'

		request.post(ajax, data).then(function (response) {
			button.stop();
			SkilldoMessage.response(response);
		})
			.catch(function (error) {
				button.stop();
			});

		return false;
	}
	debugBarAjax(element) {

		const self = this;

		let button = SkilldoUtil.buttonLoading(element);

		let data = element.serializeJSON();

		data.action = 'DevTool\\Ajax\\DevToolAjax::debugBarAjax';

		button.start()

		request.post(ajax, data).then(function (response) {

			button.stop();

			SkilldoMessage.response(response);

			response.data.html = decodeURIComponent(atob(response.data.html).split('').map(function (c) {
				return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
			}).join(''));

			self.sidebar.find('#devTools-queries-tab-ajax').html(response.data.html);
		})
		.catch(function (error) {
			button.stop();
		});

		return false;
	}
	setTheme(element) {

		let button = SkilldoUtil.buttonLoading(element);

		button.start()

		this.theme = element.attr('data-theme')

		this.sidebar.removeClass('light')

		this.sidebar.removeClass('dark')

		this.sidebar.addClass(this.theme)

		localStorage.setItem('devTool-theme', this.theme);

		let data =  {
			action: 'DevTool\\Ajax\\DevToolAjax::setting',
			name: 'theme',
			value: this.theme
		}

		request.post(ajax, data).then(function(response) {
			button.stop();
		})

		return false;
	}
	setLayout(element) {

		let button = SkilldoUtil.buttonLoading(element);

		button.start()

		this.layout = element.attr('data-layout')

		this.sidebar.removeClass('vertical-right')

		this.sidebar.removeClass('vertical-left')

		this.sidebar.removeClass('horizontal')

		this.sidebar.addClass(this.layout)

		localStorage.setItem('devTool-layout', this.layout);

		let data =  {
			action: 'DevTool\\Ajax\\DevToolAjax::setting',
			name: 'layout',
			value: this.layout
		}

		request.post(ajax, data).then(function(response) {
			button.stop();
		})

		return false;
	}
	event() {

		const self = this;

		$(document)
			.on('click', '.devTools-btn-toggle', function () {
				return self.toggle();
			})
			.on('click', '.devTools-btn-close', function () {
				return self.toggle();
			})
			.on('click', '#devTools-tabs li button', function () {
				return self.clickTab($(this))
			})
			.on('click', '.devTools-btn-cache', function () {
				return self.cacheClear($(this));
			})
			.on('click', '.devTools-btn-theme', function () {
				return self.setTheme($(this));
			})
			.on('click', '.devTools-btn-layout', function () {
				return self.setLayout($(this));
			})
			.on('click', '#devTools-debug-ajax', function () {
				return self.debugBarAjax($(this));
			})
			.on('submit', '#devTools-form-layout', function () {
				return self.saveLayout($(this));
			})
	}
}

$(function(){
	new DevToolSidebar();

	$(document).on('click', '.devTools-btn-terminal', function () { return TerminalDevTool.open(); });

	document.addEventListener("keydown", function(e) {

		if (e.key === "Escape") {
			TerminalDevTool.close()
		}

		if(e.key === '`') {
			if(e.ctrlKey) {
				return TerminalDevTool.open();
			}
		}
		if(e.key === 'f12') {
			if(e.altKey) {
				return TerminalDevTool.open();
			}
		}
	});
});