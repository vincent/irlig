IRL_IG.classes.player = Class.create({

	CHANGE_PATH_SPEED: 200,
	MOVE_PATH_SPEED: 90,

	instance: null,
	element: null,
	cell: null,
	path: [],
	path_step: 0,

	position: {
		x:0,
		y:0
	},

	initialize: function(id, options) {
		this.def_element = $(id);

		hotpoint = this.def_element.getElementsByClassName('hotpoint')[0];
		this.hot_d = {
				x: parseInt(hotpoint.getAttribute('x')),
				y: parseInt(hotpoint.getAttribute('y'))
		};
		hotpoint = null;

		/*
		var t = this.getTransform();
		t.setTranslate( this.hot.x, this.hot.y );
		this.player_transform_list.appendItem(t);

		this.position.x = this.hot.x;
		this.position.y = this.hot.y;
		*/
	},

	getInterface: function() {
		return $('player_buddy');
	},

	getInstance: function() {
		return $('player_instance');
	},

	position: function() {
		//var pib = this.getInstance().getBBox();
		var pib = $('player_instance').getBBox();
		return {
			x: pib.x + this.hot_d.x,
			y: pib.y + this.hot_d.y
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
		var t = this.getTransform();
		t.setTranslate( dx, dy );
		this.getInstance().transform.baseVal.appendItem(t);

		/*
		var eLayerElements = $A($('eLayer').children);
		var player_instance_index = 0;

		for (i=0; i<eLayerElements.length; i++) {
			if (eLayerElements[i].getAttribute('href') == '#player_buddy') {
				player_instance_index = i;
				break;
			}
		}

		// TODO : Maybe use .getCTM() here

		//.each(function(element){
		for (i=0; i<eLayerElements.length; i++) {
			var element = eLayerElements[i];
			this.instance = $('player_instance');

			if (element.getAttribute('href') == '#player_buddy') continue;

			var shape_y = element.getBBox(); shape_y = shape_y.y + shape_y.height;
			var player_y = this.position().y;

			if (shape_y > player_y && i <= player_instance_index) {
				console.log('player (%s) is under grass (%s)', player_y, shape_y);
				IRL_IG.swapChildren( eLayerElements[i], this.instance );
				this.instance = $('player_instance');
			}
			else if (shape_y < player_y && i >= player_instance_index) {
				console.log('player (%s) is above grass (%s)', player_y, shape_y);
				IRL_IG.swapChildren( eLayerElements[i], this.instance );
				this.instance = $('player_instance');
			}

		}
		*/

	},

	mirror_y: function() {
		var t = this.getTransform();
		t.setMatrix(1, 0, 0, -1, 0, 200);
		this.getInstance().transform.baseVal.appendItem(t);
	},

	enterCell: function(cell) {
		this.cell = $(cell);
		var pos = cell.getCenter();

		/*
		var dx = parseInt(pos.x - this.position().x);
		var dy = parseInt(pos.y - this.position().y);
		*/

		/*
		var dx = parseInt(pos.x - (this.getInstance().getBBox().x - this.hot_d.x ));
		var dy = parseInt(pos.y - (this.getInstance().getBBox().y - this.hot_d.y ));
		*/

		/*
		var dx = parseInt(pos.x - this.getInstance().getBBox().x );
		var dy = parseInt(pos.y - this.getInstance().getBBox().y );
		*/

		// TODO : Maybe use .getCTM() here

		var dx = parseInt(pos.x - this.getInstance().getClientRects()[0].left );
		var dy = parseInt(pos.y - this.getInstance().getClientRects()[0].top );

		this.translate( dx, dy );
	},

	followPathAndStop: function(path) {
		this.path = path;
		this.path_step = 0;

		this.def_element.followingPath();

		var goNextStep;
		goNextStep = function(){
			if (this.path_step >= this.path.length) {
				this.def_element.endFollowingPath();
				return;
			}

			this.enterCell(this.path[this.path_step++]);

			setTimeout(arguments.callee.bind(this), this.MOVE_PATH_SPEED);
		};

		setTimeout(goNextStep.bind(this), this.CHANGE_PATH_SPEED);
	}

});