IRL_IG.anim = {

	changePathSegTo: function(path, segment, attribute, to, speed, increment) {
		if (typeof(increment) == 'undefined') increment = 1;
		if (!path || !attribute) return;
		var path = $(path);
		var value = path.pathSegList.getItem(segment)[attribute];

		if (value > to) {
			value = path.pathSegList.getItem(segment)[attribute] -= speed(increment);
			if (value < to) { path.pathSegList.getItem(segment)[attribute] = to; return; }
		}
		else if (value < to) {
			value = path.pathSegList.getItem(segment)[attribute] += speed(increment);
			if (value > to) { path.pathSegList.getItem(segment)[attribute] = to; return; }
		}

		setTimeout(function(){
			IRL_IG.anim.changePathSegTo(path, segment, attribute, to, speed, increment++);
		}, 1);
		return;
	},

	changeAttribute: function(object, attribute, changer, duration, start_time, increment) {
		if (typeof(start_time) == 'undefined') start_time = new Date();
		if (typeof(increment) == 'undefined') increment = 1;
		if (!object || !attribute) return;

		object[attribute] = changer(increment);

		if (new Date() - start_time < duration)
		setTimeout(function(){
			IRL_IG.anim.changeAttribute(object, attribute, changer, increment++);
		}, 1);

		return;
	}


}