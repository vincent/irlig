IRL_IG.classes.edit_hud = Class.create({

	element:null,

	elements: { },

	symbols: [],
	current_symbol: null,

	initialize: function(id, options) {
		this.element = $(id);
		Object.extend(this.elements, {
			symbol_preview: $('edit_hud_preview'),
			bar_right_arrow: $('edit_hud_rightarrow'),
			bar_left_arrow: $('edit_hud_leftarrow'),
			symbols_bar: $('edit_hud_symbols_bar'),
			symbols_bar_group: $('edit_hud_symbols_bar_inner')
		});

		this.symbols = $$('defs > *.map_element').invoke('getAttribute', 'id');

		this.symbols.each(this.addSymbol.bind(this));
	},

	addSymbol: function(symbol_id) {
		var dims = this.elements.symbols_bar.getTBBox();

		var element_ctm = this.elements.symbols_bar.getTransformToElement(document.documentElement);
		element_ctm = new Array( element_ctm.a, element_ctm.b, element_ctm.c, element_ctm.d, element_ctm.e, element_ctm.f );

		document.documentElement.getMatrix().inverse();

		this.elements.symbols_bar_group.insert('use', { 'xlink:href':'#'+symbol_id, transform:'matrix('+element_ctm.join(' ')+')', x:dims.x, y:dims.y, width:'50px', height:(dims.height*0.99) });
	}

});