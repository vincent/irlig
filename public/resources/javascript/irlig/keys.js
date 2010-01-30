IRL_IG.classes.keys = Class.create({
	
	initialize: function() {
		var root = document.documentElement;
		
		Event.observe(root, 'keydown', this.onKeypress.bind(this));
	},

	onKeypress: function(event) {
		
		switch (event.keyCode) {
		
			case 32:
				IRL_IG.player.cast();
				break;
			
			case 40:// go south
			case 38:// go north
			case 39:// go est
			case 37:// go west
				
				// Handle walking
				var dir = {
					40:'S',
					38:'N',
					37:'W',
					39:'E'
				}[event.keyCode];
				var neighbors = IRL_IG.player.cell.getMapNeighbors();
				
				if (dir == 'E' && neighbors.get('NE')) dir = 'NE';
				if (dir == 'E' && neighbors.get('SE')) dir = 'SE';
				if (dir == 'W' && neighbors.get('NW')) dir = 'NW';
				if (dir == 'W' && neighbors.get('SW')) dir = 'SW';
			
				if (neighbors.get(dir)) {
					IRL_IG.player.enterCell($(neighbors.get(dir)), dir);
				}
				break;

			default:
				console.log(event);
				break;
				
		}
		
		return event;
	}
	
});