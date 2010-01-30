/**
 * GLOBALS
 */
var svgns = 'http://www.w3.org/2000/svg';

/**
 * Methods added on SVGNodes 
 */
IRL_IG_Node_methods = {

	/**
	 * Get the __transformed__ element's BBox
	 */
	getTBBox: function() {
		var bbox;

		try {
			bbox = this.getBBox();
		}
		catch (e) {
			//console.log(e);

			if (this.parentNode == 'map') {
				bbox = this.getClientRects()[0];
				bbox = {
					x: bbox.left,
					x: bbox.top
				}
				//console.log('... falling back to clientRects : %o', bbox);
			}

			else if (this.parentNode.tagName == 'defs') {

				bbox = this.getSafeBBox();
				//console.log('... falling back to getSafeBBox : %o', bbox);
			}

			else {

				bbox = { x:0, y:0, width:0, height:0 };
				//console.error('sorry, have not found BBox of %o', this);

				var found_attributes = false;
				[ 'x', 'y', 'width', 'height' ].each(function(argname){
					if (this.hasAttribute(argname)) bbox[argname] = parseFloat(this.getAttribute(argname));
					found_attributes = true;
				}.bind(this));

				if (found_attributes) {
					//console.error('  ... with attributes, say BBox is %o', bbox);
				}
			}

		}

		var ctm = this.getTransformToElement(document.documentElement);

		if (ctm) {
			ctm.scale = true;
			bbox = {
				x: (bbox.x * ctm.a) + ctm.e,
				y: (bbox.y * ctm.d) + ctm.f,
				height: (bbox.height) * ctm.a,
				width: (bbox.width) * ctm.d
			};

		}
		return bbox;
	},

	/**
	 * Workaround for no-parent-getBBox Firefox bug
	 */
	/*
	getSafeBBox: function () {
       var bbox = null;

       try {
		   bbox = this.getBBox();
       }
       catch (e) {
			console.log(e);
			if (this.parentNode) {
				this.parentNode.appendChild(this);	//put the child in the custody of the state
				bbox = this.getBBox();
			}
			else {
				document.documentElement.appendChild(this);	//put the child in the custody of the state
				bbox = this.getBBox();
				document.documentElement.removeChild(this);	//put the orphan back on the street
			}
       }

       return bbox;
	},
	*/
	getSafeBBox: function () {
	   var bbox = null;
	   var parent;
	   var hasparent;

	   if ( this.parentNode ) {
	       parent = this.parentNode;
	       brother = false && this.nextElementSibling; // Too long to insert ? :-/
	       hasparent = true;
	   }
	   else {
	       parent = null;
	       hasparent = false;
	   }

		//put the child in the custody of the state
	   document.documentElement.appendChild(this);

	   if (this.getBBox) {
	       bbox = this.getBBox();
	   }

	   if (hasparent) {
			if (brother)
				parent.insertBefore(this, brother);		//give the child back
			else
				parent.appendChild(this);		//give the child back
	   }
	   else  {
	       document.documentElement.removeChild(this);		//put the orphan back on the street
	   }

	   return bbox;
	},

	classNames: function() {
		return Element.classNames(this)
	},
	getClassName: function() {
		return Element.getClassName(this);
	},
	setClassName: function(name) {
		return Element.setClassName(this, name);
	},
	hasClassName: function(name) {
		Element.hasClassName(this, name);
	},
	addClassName: function(name) {
		Element.addClassName(this, name);
	},
	removeClassName: function(name) {
		Element.removeClassName(this, name);
	},
	toggleClassName: function(name) {
		Element.toggleClassName(this, name);
	},

	remove: function() {
		if (this.parentNode)
			return this.parentNode.removeChild(this);
	},

	isWalkable: function() {
		return (
				!this.hasClassName('is_wall')
			&&	(!this.hasAttribute('type') || this.getAttribute('type') != 'wall')
		);
	},

	getCenter: function() {
		var bbox = this.getTBBox();
		return {
			x: (bbox.x + (bbox.width / 2)),
			y: (bbox.y + (bbox.height / 2))
		};
	},

	getMatrixElement: function(matrix) {
		//return grid_assoc[this.id];
		return matrix.findGraphNodeById(this.id);
	},

	swapChildren:  function(c1, c2) {
		var parent = c1.parentNode;
		var tmp = c1.cloneNode( true );
		parent.replaceChild( tmp, c2 );
		parent.replaceChild( c2, c1 );
		return tmp;
	},

	getMapNeighbors: function(options) {
		var neighbors = $H();
		var _defaults = {
			with_elements: false,
			only_ids: false
		};
		if (!options)
			options = _defaults;
		else
			options = Object.extend(_defaults, options);

		var i = 0;
		$A(this.classNames()).each(function(classname){
			if (classname.indexOf('neighbors-') == 0)
				classname.gsub('neighbors-', '').split('-').each(function(n){
					if (n && !n.empty()) {
						if (n.indexOf(':')) {
							n = n.split(':');
							neighbors.set(n[0],n[1]);
						}
						else
							neighbors.set(i,n);
					}
				});
		});

		/*
		[ 'NW', 'N', 'NE', 'E', 'SE', 'S', 'SW', 'W' ].each(function(n){
			n = 'cell_'+n;
			if (this.hasAttributeNS(null, n))
				neighbors.push(this.getAttributeNS(null, n));
		}.bind(this));

		if (options.with_elements) {
			neighbors = neighbors.reject(function(cell){
				return $(cell).hasClassName('element-');
			});
		}
		*/

		return neighbors;
	},

	inspect: function() {
	   var bbox = this.getTBBox();
	   var svgns = 'http://www.w3.org/2000/svg';

	   var outline = document.createElementNS(svgns, 'rect');
	   outline.setAttributeNS( null, 'x', bbox.x - 2 );
	   outline.setAttributeNS( null, 'y', bbox.y - 2 );
	   outline.setAttributeNS( null, 'width',  bbox.width + 4 );
	   outline.setAttributeNS( null, 'height', bbox.height + 4 );
	   outline.setAttributeNS( null, 'stroke', 'blue' );
	   outline.setAttributeNS( null, 'fill', 'yellow' );

	   $('eLayer').appendChild( outline );

	   window.setTimeout(function(){ this.parentNode.removeChild(this); }.bind(outline), 5000);
	},

	insert: function(tagName, attributes) {
		var svgns = 'http://www.w3.org/2000/svg';
		var tmp = document.createElementNS(svgns, tagName);

		var t = $H(attributes);

		$H(attributes).each(function(pair){
			var key = new String(pair.key).split(':');
			var ns = null;
			if (key.length > 1) { ns = 'http://www.w3.org/1999/xlink'; key = key[1]; }
			tmp.setAttributeNS( ns, key, pair.value );
		});
		this.appendChild( tmp );
		return tmp;
	},

	scale: function(factor) {
		var ctm = this.getTransformToElement(document.documentElement);
		var m = (ctm.a * factor) + ' ' + ctm.b + ' ' + ctm.c + ' ' + (ctm.d * factor) + ' ' + ctm.e + ' ' + ctm.f;
		this.setAttributeNS(null, 'transform', 'matrix('+m+')');
		return this;
	},
	translate: function(dx, dy) {
		var ctm = this.getTransformToElement(document.documentElement);
		var m = ctm.a + ' ' + ctm.b + ' ' + ctm.c + ' ' + ctm.d + ' ' + (ctm.e + dx) + ' ' + (ctm.f + dy);
		this.setAttributeNS(null, 'transform', 'matrix('+m+')');
		return this;
	}

};
Node.prototype = Object.extend(Node.prototype, IRL_IG_Node_methods);
