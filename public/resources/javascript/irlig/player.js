IRL_IG.classes.player = Class.create({

	CHANGE_PATH_SPEED: 200,
	MOVE_PATH_SPEED: 200,

	instance: null,
	element: null,
	cell: null,
	path: [],
	path_step: 0,
	
	main_aura: null,

	spell_speed_factor: 1,
	
	initialize: function(id, options) {
		this.def_element = $(id);
	},

	getInterface: function() {
		return $('player_buddy');
	},

	getInstance: function() {
		return $('player_instance');
	},

	getHotPoint: function() {
		return $('hotpoint');
	},

	setMainAura: function(object) {
		this.main_aura = object;
	},
	
	position: function() {
		var pib = this.getInstance().getTBBox();
		var hotpoint = this.getHotPoint().getCenter();
		return {
			x: pib.x + hotpoint.x,
			y: pib.y + hotpoint.y
		};
	},

	getTransform: function() {
		return this.globalSVGTransform = $$('svg')[0].createSVGTransform();
	},

	/*
	getHotPoint: function() {
		var hotpoint = this.element.querySelector('.hotpoint');
		return { x:hotpoint.getAttribute('x'), y:hotpoint.getAttribute('y') };
	},
	*/

	translate: function(dx, dy) {
		/*
		var t = this.getTransform();
		t.setTranslate( dx, dy );
		this.getInstance().transform.baseVal.appendItem(t);
		*/

		var ctm = this.getInstance().getTransformToElement(document.documentElement);
		var m = ctm.a + ' ' + ctm.b + ' ' + ctm.c + ' ' + ctm.d + ' ' + (ctm.e + dx) + ' ' + (ctm.f + dy);
		this.getInstance().setAttributeNS(null, 'transform', 'matrix('+m+')');

		return;

		var neighbors = this.cell.getMapNeighbors({with_elements:true, only_ids:true});
		//var eLayerElements = $A($('eLayer').children);

		var eLayerElements = [];
		neighbors.each(function(mapelement){
			var mapped = window.IRL_IG.ground.mapping[mapelement];
			if (mapped) mapped.each(function(m){ eLayerElements.push(m); });
		});

		if (eLayerElements.length == 0) return;

		console.log('See if some of %o must be replaced', eLayerElements);

		var player_instance_index = 0;
		var get_player_instance_position = function(){
			for (i=0; i<eLayerElements.length; i++) {
				if (eLayerElements[i].getAttribute('href') == '#player_buddy') {
					player_instance_index = i;
					break;
				}
			}
		};

		//.each(function(element){
		for (i=0; i<eLayerElements.length; i++) {
			var element = eLayerElements[i];

			if (!element || !element.getTBBox()) continue;

			var shape_y = element.getTBBox();
			shape_y = shape_y.y + shape_y.height;
			var player_y = this.position().y;

			if (shape_y > player_y && i <= player_instance_index) {
				//console.log('player (%s) is under grass (%s)', player_y, shape_y);
				eLayerElements[i].parentNode.swapChildren( eLayerElements[i], this.getInstance() );
			}
			else if (shape_y < player_y && i >= player_instance_index) {
				//console.log('player (%s) is above grass (%s)', player_y, shape_y);
				eLayerElements[i].parentNode.swapChildren( eLayerElements[i], this.getInstance() );
			}

			get_player_instance_position();
		}

	},

	mirror_y: function() {
		var t = this.getTransform();
		t.setMatrix(1, 0, 0, -1, 0, 200);
		this.getInstance().transform.baseVal.appendItem(t);
	},

	enterCell: function(cell, by_dir) {
		// Abort any cast
		if (this.current_cast)
			this.current_cast.abort();
		
		//console.log('Entering cell %o', cell);
		var cell_pos__ = cell.getTBBox();
		var cell_pos = cell.getCenter();
		var player_pos = this.position();

		var dx = parseFloat(cell_pos.x - player_pos.x);
		var dy = parseFloat(cell_pos.y - player_pos.y);

		this.def_element.leaveCell(cell, by_dir);

		clearTimeout(this.cell_to_cell_moving);
		this.current_translation = {
			x:0,
			y:0,
			dx:dx,
			dy:dy,
		};

		this.cell_to_cell_moving = setTimeout(this.moving.bind(this), 10);
		this.cell = $(cell);

		this.def_element.enterCell(cell, by_dir);
	},

	moving: function() {
		var x_move_positive = (this.current_translation.dx >= 0);
		var y_move_positive = (this.current_translation.dy >= 0);
		
		var x_done = (x_move_positive  && this.current_translation.x >= this.current_translation.dx) 
				  || (!x_move_positive && this.current_translation.x <= this.current_translation.dx);
		
		var y_done = (y_move_positive  && this.current_translation.y >= this.current_translation.dy) 
				  || (!y_move_positive && this.current_translation.y <= this.current_translation.dy);
		
		if (x_done && y_done) {
			clearTimeout(this.cell_to_cell_moving);
			return;
		}
		
		this.translate(
			x_done ? 0 : x_move_positive ? 5 : -5,
			y_done ? 0 : y_move_positive ? 5 : -5
		);
		if (this.main_aura)
			this.main_aura.translate(
				x_done ? 0 : x_move_positive ? 5 : -5,
				y_done ? 0 : y_move_positive ? 5 : -5
			);

		if (!x_done) this.current_translation.x += (5 * (x_move_positive ? 1 : -1 ));
		if (!y_done) this.current_translation.y += (5 * (y_move_positive ? 1 : -1 ));
		
		this.cell_to_cell_moving = setTimeout( arguments.callee.bind(this) , 10);
	},
	
	followPathAndStop: function(path) {
		this.path = path;
		this.path_step = 0;

		this.def_element.followingPath(path[0]);

		//console.log("following path: %o", path);
		
		var goNextStep;
		goNextStep = function() {
			if (this.path_step >= this.path.length) {
				//console.log('path step (%o) > path length (%o) so endFollowingPath', this.path_step, this.path.length);
				this.def_element.endFollowingPath();
				return;
			}

			this.enterCell($(this.path[this.path_step].id), this.path[this.path_step].by_dir);
			this.path_step++;

			setTimeout(arguments.callee.bind(this), this.MOVE_PATH_SPEED);
		};

		setTimeout(goNextStep.bind(this), this.CHANGE_PATH_SPEED);
	},
	
	cast: function(spell) {
		var cast = new IRL_IG.classes.cast({
			spell_time: 2000,
			onComplete: function(){
				new IRL_IG.spells.aura_stars(IRL_IG.player);
			}
		});
		this.current_cast = cast;
		return cast;
	}

});