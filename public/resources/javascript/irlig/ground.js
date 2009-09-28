Node.prototype.getSafeBBox = function () {
       var bbox = null;
       var parent;
       var hasparent;

       if ( this.parentNode ) {
           parent = this.parentNode;
           hasparent = true;
       }
       else {
           parent = null;
           hasparent = false;
       }

       document.documentElement.appendChild(this);	//put the child in the custody of the state

       if (this.getBBox) {
           bbox = this.getBBox();
       }

       if (hasparent) {
           parent.appendChild(this);		//give the child back
       }
       else  {
           document.documentElement.removeChild(this);		//put the orphan back on the street
       }

       return bbox;
   };

IRL_IG.classes.ground = Class.create({

	element: null,
	matrix:  null,
	mapName: null,

	main_player_instance: null,

	initialize: function(id, name, gridmatrix) {
		this.element = $(id);
		this.mapName = name;
		this.matrix  = gridmatrix;

		this.last_path = [ gridmatrix[0][0] ];
		this.current_action = '';

		// Adding the elements' layer
		var svgns = 'http://www.w3.org/2000/svg';
		this.elements_layer = document.createElementNS(svgns, 'g');
		this.elements_layer.setAttributeNS( null, 'id', 'eLayer' );
		this.elements_layer.setAttributeNS( null, 'x', 0 );
		this.elements_layer.setAttributeNS( null, 'y', 0 );
		this.elements_layer.setAttributeNS( null, 'width', '100%' );
		this.elements_layer.setAttributeNS( null, 'height', '100%' );
		this.element.parentNode.appendChild(this.elements_layer);

		/*
		var bbox = this.elements_layer.getBBox();
		var t = $$('svg')[0].createSVGTransform();
		t.setTranslate( -bbox.x, -bbox.y );
		this.elements_layer.transform.baseVal.appendItem(t);
		*/

		IRL_IG.debug('Initializing polygons');
		$$('svg polygon').each(this.initializeElement.bind(this));
		Event.observe(this.element, 'mouseover', function(e){
			e.element().classList.add('hover');
		});
		Event.observe(this.element, 'mouseout', function(e){
			e.element().classList.remove('hover');
		});

		this.initTest();
	},

	addPrototypeMethods: function(element) {
		element.hasClassName = function(name) { return this.classList.contains(name); };
		element.addClassName = function(name) { return this.classList.add(name); };
		element.removeClassName = function(name) { return this.classList.remove(name); };
		element.toggleClassName = function(name) { return this.classList.toggle(name); };
		return element;
	},

	initializeElement: function(element) {

		element = this.addPrototypeMethods(element);

		element = this.extendMapByPoly(element);

		//element.setAttribute('fill', 'transparent');

		element.isWalkable = function() {
			return (
					!this.classList.contains('is_wall')
				&&	(!this.hasAttribute('type') || this.getAttribute('type') != 'wall')
			);
		};

		element.getCenter = function() {
			var bbox = this.getBBox();
			return {
				x: (bbox.x + (bbox.width / 2)),
				y: (bbox.y + (bbox.height / 2))
			};
		};

		element.getMatrixElement = function(matrix) {
			return matrix.findGraphNodeById(this.id);
		};

		element.inspect = function() {
		   var bbox = this.getBBox();
		   var svgns = 'http://www.w3.org/2000/svg';

		   var outline = document.createElementNS(svgns, 'rect');
		   outline.setAttributeNS( null, 'x', bbox.x - 2 );
		   outline.setAttributeNS( null, 'y', bbox.y - 2 );
		   outline.setAttributeNS( null, 'width',  bbox.width + 4 );
		   outline.setAttributeNS( null, 'height', bbox.height + 4 );
		   outline.setAttributeNS( null, 'stroke', 'blue' );
		   outline.setAttributeNS( null, 'fill', 'yellow' );

		   this.parentNode.insertBefore( outline, this );

		   window.setTimeout(function(){ this.parentNode.removeChild(this); }.bind(outline), 5000);
		};

	},

	extendMapByPoly: function(element) {
		if (!element) return;

		if (element.classList && element.classList.contains('element-medium_grass')) {
			this.addElement('use', { 'x':element.getBBox().x, 'y':element.getBBox().y, 'xlink:href':'#medium_grass' });
			element.classList.add('is_wall');
		}
		return element;
	},

	addElement: function(name, attributes) {
		var svgns = 'http://www.w3.org/2000/svg';
		var tmp = document.createElementNS(svgns, name);

		var t = $H(attributes);

		$H(attributes).each(function(pair){
			//var value = pair.value;
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
	initTest: function() {

		IRL_IG.debug('Initializing map listeners');
		//$(this.element).addEventListener('mousedown', function(e){
		Event.observe(this.element, 'mousedown', function(e) {
			var cell = e.element();
			if (e.isMiddleClick()) {
				this.set_cell_type(cell.id, 'wall');
				this.current_action = 'filling_walls';
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

	set_cell_type: function(cell_id, type) {
		switch (type) {
			case 'wall':
				$(cell_id).setAttribute('type', 'wall');
				$(cell_id).setAttribute('fill', 'black');

				/* new Ajax.Request('/mapedit?map='+mapName+'&amp;map_action=set_cell_type'+'&amp;cell_id='+cell_id+'&amp;type='+type); */
			break;

		}
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