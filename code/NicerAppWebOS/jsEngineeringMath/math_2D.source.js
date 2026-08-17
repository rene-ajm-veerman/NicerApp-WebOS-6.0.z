jsem.math.twoDimensional = jsem.math.xy = {}; 
//jsem.m = jsem.math; // defined in math_basics.js!
jsem.m.xy = jsem.math.xy;
jsem.m.d2 = jsem.math.xy;


// SIMPLE FUNCTIONS

jsem.math.xy.pointOnCircle_angleInRadians = function (centerX, centerY, radiusInPixels, angleInRadians) {
	// found this answer at https://stackoverflow.com/questions/5583286/use-javascript-to-select-point-on-a-circle
	var x = Math.cos(angleInRadians) * radiusInPixels;
	var y = Math.sin(angleInRadians) * radiusInPixels;
	return {
		x : centerX + x,
		y : centerY + y
	}
};
jsem.math.xy.pocAir = jsem.math.xy.pointOnCircle_angleInRadians;

jsem.math.xy.pointOnCircle_angleInDegrees = function (centerX, centerY, radiusInPixels, angleInDegrees) {
	// found this answer at https://stackoverflow.com/questions/14096138/find-the-point-on-a-circle-with-given-center-point-radius-and-degree
	return {
		x : centerX + radiusInPixels * (Math.cos(angleInDegrees * Math.PI / 180)),
		y : centerY + radiusInPixels * (Math.sin(angleInDegrees * Math.PI / 180))
	}
};








// COMPLEX FUNCTIONS USED TO SPIN THE NICER.APP LOGO ARROWS AND SQUARES AROUND THE CIRCLES CENTRE

jsem.math.xy.angleBetweenTwoPoints = function (p1, p2) {
    // found this code at : https://gist.github.com/conorbuck/2606166
    return {
        angleRadians : Math.atan2(p2.y - p1.y, p2.x - p1.x),
        angleDegrees : Math.atan2(p2.y - p1.y, p2.x - p1.x) * 180 / Math.PI
    }
};

jsem.math.xy.distanceBetweenTwoPoints = function (xA, yA, xB, yB) { 
	var 
	xDiff = xA - xB,
	yDiff = yA - yB;

	return Math.sqrt(xDiff * xDiff + yDiff * yDiff);
}

jsem.math.xy.circles = {
    pointOnCircumference : function (centerX, centerY, angleDegrees, radius) {
        if (!centerX) centerX=0;
        if (!centerY) centerY=0;
        return {
            x : centerX + (radius * Math.cos(jsem.math.xy.circles.toRadians(angleDegrees))),
            y : centerY + (radius * Math.sin(jsem.math.xy.circles.toRadians(angleDegrees)))
        };
    },
    
    angleDegrees : function (center, p1) {
        var p0 = {
            x: center.x, 
            y: center.y 
                - Math.sqrt(
                    Math.abs(p1.x - center.x) * Math.abs(p1.x - center.x) + Math.abs(p1.y - center.y) * Math.abs(p1.y - center.y)
                )
        };
        return (2 * Math.atan2(p1.y - p0.y, p1.x - p0.x)) * 180 / Math.PI;
    },
    
    angleDegreesBetweenTwoPoints : function (center, p1, p2) {
        var 
        angle1 = jsem.math.xy.circles.angleDegrees (center, p1),
        angle2 = jsem.math.xy.circles.angleDegrees (center, p2);
        return angle2 - angle1;
    },
    
    toRadians : function (angleDegrees) {
        return angleDegrees * Math.PI / 180;
    }
};


