IRL_IG.classes.player = Class.create({

	CHANGE_PATH_SPEED: 200,
	MOVE_PATH_SPEED: 90,

	instance: null,
	element: null,
	cell: null,
	path: [],
	path_step: 0,

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

		var eLayerElements = $A($('eLayer').children);
		var player_instance_index = 0;

		for (i=0; i<eLayerElements.length; i++) {
			if (eLayerElements[i].getAttribute('href') == '#player_buddy') {
				player_instance_index = i;
				break;
			}
		}

		//.each(function(element){
		for (i=0; i<eLayerElements.length; i++) {
			var element = eLayerElements[i];

			if (element.getAttribute('href') == '#player_buddy') continue;

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

		}

	},

	mirror_y: function() {
		var t = this.getTransform();
		t.setMatrix(1, 0, 0, -1, 0, 200);
		this.getInstance().transform.baseVal.appendItem(t);
	},

	enterCell: function(cell) {
		this.cell = $(cell);
		var cell_pos__ = cell.getTBBox();
		var cell_pos = cell.getCenter();
		var player_pos = this.position();

		var dx = parseFloat(cell_pos.x - player_pos.x);
		var dy = parseFloat(cell_pos.y - player_pos.y);

		this.translate( dx, dy );
	},

	followPathAndStop: function(path) {
		this.path = path;
		this.path_step = 0;

		this.def_element.followingPath(path[0]);

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