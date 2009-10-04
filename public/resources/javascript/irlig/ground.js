IRL_IG.classes.ground = Class.create({

	element: null,
	matrix:  null,
	mapName: null,

	main_player_instance: null,

	initialize: function(id, name, gridmatrix, mode) {
		this.element = $(id);
		this.mapName = name;
		this.matrix  = gridmatrix;
		this.mode  = mode;

		/**/
		// Adding the elements' layer
		var svgns = 'http://www.w3.org/2000/svg';
		this.elements_layer = document.createElementNS(svgns, 'g');
		this.elements_layer.setAttributeNS( null, 'id', 'eLayer' );

		// .. just after the map, so the HUD will not collide
		if (this.element.nextElementSibling)
			this.element.parentNode.insertBefore(this.elements_layer, this.element.nextElementSibling);
		else
			this.element.parentNode.appendChild(this.elements_layer);

		IRL_IG.debug('Initializing map polygons');
		var next_child = this.element.firstElementChild;
		while (next_child) {
			var element = next_child;

			this.extendMapByPoly(element);
			next_child = next_child.nextElementSibling;
		}

		Event.observe(this.element, 'mouseover', function(e){
			e.element().classList.add('hover');
		});
		Event.observe(this.element, 'mouseout', function(e){
			e.element().classList.remove('hover');
		});

		if (mode == 'edit') {
			this.last_path = [ gridmatrix[0][0] ];
			this.current_action = '';
			this.initEdition();
		}

	},

	extendMapByPoly: function(element) {
		if (!element || !element.classList|| !element.classList.length) return;

		var map_elements = $A(element.classList).findAll(function(c){
			var name = c.gsub('element-', '');
			return (name != c);
		});

		map_elements.each(function(map_element){
			map_element = map_element.gsub('element-', '');
			if (!$(map_element)) {
				console.warn('Attempt to add an unknown map element : %o', map_element);
				return;
			}

			var element_center = element.getCenter();
			var symbol = $(map_element);
			var symbol_bbox = symbol.getTBBox();

			// Assume hotpoint it's ( width/2 , height )
			var element_bbox = {
				x: element_center.x - symbol_bbox.x - (0.5 * symbol_bbox.width),	// HEREISHIT
				y: element_center.y - symbol_bbox.y - (0.8 * symbol_bbox.height),	// HEREISHIT
			};
			var element_ctm = element.getTransformToElement($('eLayer'));
			element_ctm = new Array( element_ctm.a, element_ctm.b, element_ctm.c, element_ctm.d, element_ctm.e, element_ctm.f );

			var new_el = this.addElement('use', { transform:'matrix('+element_ctm.join(' ')+')', x:element_bbox.x, y:element_bbox.y, 'xlink:href':'#'+map_element });

			var classNames = $A(symbol.classList).reject(function(classname){ return classname && classname != ''; });
			if (classNames.length) {
				classNames = classNames.join(' ');
				element.addClassName(classNames);
			}

			//element.setAttributeNS(null, 'style', 'fill:red;');
		}.bind(this));
	},

	addElement: function(name, attributes) {
		var svgns = 'http://www.w3.org/2000/svg';
		var tmp = document.createElementNS(svgns, name);

		var t = $H(attributes);

		$H(attributes).each(function(pair){
			var key = new String(pair.key).split(':');
			var ns = null;
			if (key.length > 1) { ns = 'http://www.w3.org/1999/xlink'; key = key[1]; }
			tmp.setAttributeNS( ns, key, pair.value );
		});
		this.elements_layer.appendChild( tmp );
		return tmp;
	},

	last_path: [],
	current_action: '',
	initEdition: function() {

		IRL_IG.debug('Initializing map listeners');
		//$(this.element).addEventListener('mousedown', function(e){
		Event.observe(this.element, 'mousedown', function(e) {
			var cell = e.element();
			if (e.isMiddleClick()) {
				var current_symbol_class = this.getCurrentHUDSymbol();
				console.log('current symbol is %o', current_symbol_class)
				if (current_symbol_class) {
					current_symbol_class = 'element-'+current_symbol_class;
					cell.addClassName( current_symbol_class );
					this.updateMapCell(cell.id, { class:'wall '+current_symbol_class });
				}
			}
			return false;
		}.bind(this));


		//$(this.element).addEventListener('mousemove', function(e){
		Event.observe(this.element, 'mousemove', function(e) {
			var cell = e.element();
			if (this.current_action == 'filling_walls') {
				this.set_cell_type(cell.id, 'wall');
			}
			return false;
		}.bind(this));

		//$(this.element).addEventListener('mouseup', function(e){
		Event.observe(this.element, 'mouseup', function(e){
			this.current_action = '';
			return false;
		}.bind(this));

	},

	getCurrentHUDSymbol: function() {
		var hud = window.IRL_IG.edit_hud;
		if (!hud) {
			console.error('Editing HUD is not here');
			return false;
		};
		return hud.current_symbol;
	},

	updateMapCell: function(cell_id, attributes) {
		var cell = $(cell_id);
		attributes = $H(attributes);

		attributes.each(function(pair){
			cell.setAttributeNS(null, pair.key, pair.value);
		});

		this.extendMapByPoly($(cell_id));

		new Ajax.Request('/mapedit/updatecell?map='+this.mapName+'&cell_id='+cell_id+'&'+attributes.toQueryString());
	},

	clear_path: function(path) {
		path.each(function(e){
			e = $(e.id);
			e.setAttribute('fill', '#efefef');
		});
	},

	highlight_path: function(path) {
		path.each(function(e){
			e = $(e.id);
			e.setAttribute('fill', 'red');
		});
	},

	registerMainPlayer: function(player) {
		if (!player) IRL_IG.debug('No player given');

		var first_cell = this.element.getElementsByTagName('polygon')[0];
		first_cell_center = first_cell.getCenter();

		this.addElement('use', { 'x':0, 'y':0, 'xlink:href':'#'+(player.def_element.id), 'id':'player_instance' });

		this.main_player = player;

		Event.observe(this.element, 'click', function(e){
			var dest = e.element();
			if (e.isLeftClick() && dest.isWalkable()) {
				var path = astar.search(this.matrix, this.main_player.cell.getMatrixElement(this.matrix), dest.getMatrixElement(this.matrix));
				path = path.map(function(cell){ return $(cell.id) });
				this.main_player.followPathAndStop(path);
			}
		}.bind(this));

		this.main_player.enterCell(first_cell);
		console.log('player registred');
	}

});