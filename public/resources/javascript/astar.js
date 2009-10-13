/*
IRL_IG.classes.a_star_grid = Class.create();
Object.extend(IRL_IG.classes.a_star_grid.prototype, Array.prototype, {

	initialize_astar: function() {
		for(var x = 0; x < this.length; x++) {
			for(var y = 0; y < this[x].length; y++) {
				this[x][y].f = 0;
				this[x][y].g = 0;
				this[x][y].h = 0;
				this[x][y].debug = "";
				this[x][y].parent = null;
			}
		}
	},

	clear: function() {
		for(var x = 0; x < this.length; x++) {
			for(var y = 0; y < this[x].length; y++) {
				$(this[x][y].id).setAttribute('fill', '');
			}
		}
	},

	findGraphNode: function(node) {
		return this.find(function(e){
			return (e.pos.x == node.pos.x && e.pos.y == node.pos.y);
		});
	},

	removeGraphNode: function(node) {
		return this.reject(function(e){
			return (e.pos.x == node.pos.x && e.pos.y == node.pos.y);
		});
	}
});
*/

/**
 * For A* grid
 */
Array.prototype.findGraphNodeById = function(id) {
	var found = null;
	for(var x = 0; x < this.length; x++)
		for(var y = 0; y < this[x].length; y++)
			if (this[x][y].id == id) return this[x][y];
	return found;
};

/**
 * For A* lists
 */
Array.prototype.findGraphNode = function(node) {
	return this.find(function(e){
		return (e.pos.x == node.pos.x && e.pos.y == node.pos.y);
	});
};

Array.prototype.removeGraphNode = function(node) {
	return this.reject(function(e){
		return (e.pos.x == node.pos.x && e.pos.y == node.pos.y);
	});
};

var Astar = Class.create({

	grid : [],

	initialize: function(grid) {
		this.grid = grid;
		for(var x = 0; x < this.grid.length; x++) {
			for(var y = 0; y < this.grid[x].length; y++) {
				this.grid[x][y].f = 0;
				this.grid[x][y].g = 0;
				this.grid[x][y].h = 0;
				this.grid[x][y].debug = "";
				this.grid[x][y].parent = null;
			}
		}
	},
	search: function(grid, start, end) {

		this.initialize(this.grid);

		var grid 	   = this.grid.clone();
		var openList   = [];
		var closedList = [];
		openList.push(start);

		while(openList.length > 0) {

			// Grab the lowest f(x) to process next
			var lowInd = 0;
			for(var i=0; i<openList.length; i++) {
				if(openList[i].f < openList[lowInd].f) { lowInd = i; }
			}
			var currentNode = openList[lowInd];

			// End case -- result has been found, return the traced path
			if(currentNode.pos == end.pos) {
				var curr = currentNode;
				var ret = [];
				while(curr.parent) {
					ret.push(curr);
					curr = curr.parent;
				}
				return ret.reverse();
			}

			// Normal case -- move currentNode from open to closed, process each of its neighbors
			openList = openList.removeGraphNode(currentNode);
			//openList.without(currentNode);

			closedList.push(currentNode);
			var neighbors = this.neighbors(grid, currentNode);

			for(var i=0; i<neighbors.length;i++) {
				var neighbor = neighbors[i];
				if ( !$(neighbor.id).isWalkable() || closedList.findGraphNode(neighbor)) {
					// not a valid node to process, skip to next neighbor
					continue;
				}

				// g score is the shortest distance from start to current node, we need to check if
				//	 the path we have arrived at this neighbor is the shortest one we have seen yet
				var gScore = currentNode.g + 1; // 1 is the distance from a node to it's neighbor
				var gScoreIsBest = false;


				if (!openList.findGraphNode(neighbor)) {
					// This the the first time we have arrived at this node, it must be the best
					// Also, we need to take the h (heuristic) score since we haven't done so yet

					gScoreIsBest = true;
					neighbor.h = this.heuristic(neighbor.pos, end.pos);
					openList.push(neighbor);
				}
				else if(gScore < neighbor.g) {
					// We have already seen the node, but last time it had a worse g (distance from start)
					gScoreIsBest = true;
				}

				if(gScoreIsBest) {
					// Found an optimal (so far) path to this node.	 Store info on how we got here and
					//	just how good it really is...
					neighbor.parent = currentNode;
					neighbor.g = gScore;
					neighbor.f = neighbor.g + neighbor.h;
					//neighbor.debug = "F: " + neighbor.f + "<br />G: " + neighbor.g + "<br />H: " + neighbor.h;
				}
			}
		}

		// No result was found -- empty array signifies failure to find path
		return [];
	},
	heuristic: function(pos0, pos1) {
		var dx = Math.abs (pos1.x - pos0.x);
		var dy = Math.abs (pos1.y - pos0.y);

		// Maybe buggy here
		return (Math.abs(dx) + Math.abs(dy) + Math.abs(dx - dy)) / 2;
		//return (-(Math.abs(dx) + Math.abs(dy) + Math.abs(dx + dy))) / 2;
		//return dx + dy;
	},
	neighbors: function(grid, node) {

		var cell = $(node.id)
		var neighbors = cell.getMapNeighbors();

		neighbors = neighbors.map(function(cell_id){
			return grid.findGraphNodeById(cell_id);
		});

		return neighbors;
	}
});