IRL_IG.classes.edit_hud = Class.create({

	elements: {},
	element: null,

	symbols: [],
	current_symbol: null,

	initialize: function(id, options) {
		this.element = $(id);
		Object.extend(this.elements, {
			symbol_preview: $('edit_hud_preview'),
			bar_right_arrow: $('edit_hud_rightarrow'),
			bar_left_arrow: $('edit_hud_leftarrow'),
			symbols_bar: $('edit_hud_symbols_bar')
		});

		// Place the hud, and keep it down
		this.onResizeWindow();
		Event.observe(window, 'resize', this.onResizeWindow.bind(this));

		// Get symbols
		this.symbols = $$('defs > *.map_element').invoke('getAttribute', 'id');

		console.log('Registered symbols : %o', this.symbols);

		// If there are some, select the first
		if (this.symbols.length) {
			this.symbols.each(this.addSymbol.bind(this));
			this.setSymbolPreview(this.symbols[0]);
		}

		// SElect observer
		Event.observe(this.elements.symbols_bar, 'click', function(event) {
			var e = event.element();
			if (e.tagName != 'use') return;
			this.setSymbolPreview(e.getAttributeNS('http://www.w3.org/1999/xlink', 'href').gsub('#', ''));
		}.bind(this));
	},

	onResizeWindow: function(event) {
		var bbox = this.element.getTBBox();
		this.element.translate(0, window.innerHeight - 200 - bbox.y);
	},

	addSymbol: function(symbol_id) {
		var symbol = $(symbol_id);
		var symbol_box = symbol.getTBBox();

		var scale = {
				x: 45 / symbol_box.height,
				y: Math.min(50 / symbol_box.width, 45 / symbol_box.height)
		};

		this.elements.symbols_bar.insert('use', {
				'xlink:href': '#'+symbol_id,
				x: (50*(1/scale.x)*(this.elements.symbols_bar.children.length+1)),
				y: 50,
				width: 50,
				height: 50,
				transform: 'scale( #{x} , #{y} )'.interpolate(scale)
			});
	},

	setSymbolPreview: function(symbol_id) {
		// Remove elements in preview
		$A(this.elements.symbol_preview.children).invoke('remove');

		var symbol = $(symbol_id);
		var symbol_box = symbol.getTBBox();

		// Set HUD's current symbol
		this.current_symbol = symbol_id;

		var scale = {
				x: 170 / symbol_box.height,
				y: Math.min(170 / symbol_box.width, 170 / symbol_box.height)
		}

		this.elements.symbol_preview.insert('use', {
				'xlink:href': '#'+symbol_id,
				x: 0,
				y: 0,
				width: 170,
				height: 170,
				transform: 'scale( #{x} , #{y} )'.interpolate(scale)
			});
	}

});