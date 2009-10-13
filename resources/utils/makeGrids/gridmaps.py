#Copyright 2006 Jerry Stratton
#Released under the Gnu General Public License version 2
#note that SVGdraw.py is not copyright Jerry Stratton and is thus not released under the GPL
import SVGdraw
import math

hexhalfheight = math.sin(math.radians(60))

class hexagon(SVGdraw.polygon):
	def __init__(self, width, xoffset, yoffset, units, **args):
		self.units = units
		self.xoffset = xoffset
		self.yoffset = yoffset

		#create points
		sidelength = width/2
		xmotion = sidelength/2
		ymotion = sidelength*hexhalfheight

		sides = []
		sides.append([0, ymotion])
		sides.append([xmotion, 2*ymotion])
		sides.append([xmotion + sidelength, 2*ymotion])
		sides.append([sidelength*2, ymotion])
		sides.append([xmotion + sidelength, 0])
		sides.append([xmotion, 0])

		points = map(self.makePoints, sides)

		#send points to parent
		SVGdraw.polygon.__init__(self, points, **args)

	def makePoints(self, points):
		x, y = points
		x += self.xoffset
		y += self.yoffset

		#you can't use units with polygons
		#I don't understand
		if self.units in unitpoints:
			pointconv = unitpoints[self.units]
			x *= pointconv
			y *= pointconv

		return x, y

class hexpath(SVGdraw.path):
	def __init__(self, width, xoffset, yoffset, units, **args):
		self.units = units
		self.xoffset = xoffset
		self.yoffset = yoffset

		#create points
		sidelength = width/2
		xmotion = sidelength/2
		ymotion = sidelength*hexhalfheight

		sides = []
		sides.append("M")
		sides.append([0, ymotion])
		sides.append("L")
		sides.append([xmotion, 2*ymotion])
		sides.append("L")
		sides.append([xmotion + sidelength, 2*ymotion])
		sides.append("L")
		sides.append([sidelength*2, ymotion])
		sides.append("L")
		sides.append([xmotion + sidelength, 0])
		sides.append("L")
		sides.append([xmotion, 0])
		sides.append("L")
		sides.append([0, ymotion])

		points = ' '.join(map(self.makePoints, sides)) + ' z'

		#send points to parent
		SVGdraw.path.__init__(self, points, **args)

	def makePoints(self, points):
		if points == "M" or points == "L" or points == "Z" or points == "m" or points == "l" or points == "z":
			return points

		x, y = points
		x += self.xoffset
		y += self.yoffset

		#you can't use units with polygons
		#I don't understand
		if self.units in unitpoints:
			pointconv = unitpoints[self.units]
			x *= pointconv
			y *= pointconv

		return "%f,%f" % ( x, y )

class grid:
	def __init__(self, height=11, width=8.5, description="map grid", gridSize=1.0, units="in", lineColor="black", fillColor="none", pen=.01, margins=.5, bigGrid=None, bigGridColor="red", bigGridPen=.02, type="box"):
		self.height=float(height)
		self.width=float(width)
		self.units = units
		self.description = description
		self.gridSize = float(gridSize)
		self.lineColor = lineColor
		self.fillColor = fillColor
		self.topMargin = margins
		self.botMargin = margins
		self.leftMargin = margins
		self.rightMargin = margins
		self.pen = pen
		self.bigGrid = bigGrid
		self.bigGridColor = bigGridColor
		self.bigGridPen = bigGridPen
		self.type = type

	def setType(self, type):
		self.type = type

	def setBigGrid(self, gridcount):
		gridcount = int(gridcount)
		self.bigGrid = gridcount

	def setPen(self, pen):
		pen = float(pen)
		self.pen = pen

	def setBigPen(self, pen):
		pen = float(pen)
		self.bigGridPen = pen

	def setPenColor(self, color):
		self.lineColor = color

	def setFillColor(self, color):
		self.fillColor = color

	def setBigPenColor(self, color):
		self.bigGridColor = color

	def setMargins(self, margins):
		margins = float(margins)
		self.topMargin = margins
		self.botMargin = margins
		self.leftMargin = margins
		self.rightMargin = margins

	def setTopMargin(self, margin):
		margin = float(margin)
		self.topMargin = margin

	def setGrid(self, gridSize):
		gridSize = float(gridSize)
		self.gridSize = gridSize

	def setWidth(self, width):
		width = float(width)
		self.width = width

	def setHeight(self, height):
		height = float(height)
		self.height = height

	def setUnits(self, units):
		self.units = units

	def makeGrid(self, canvas, lineColor=None, fillColor=None, pen=None):
		realWidth = self.width-(self.leftMargin+self.rightMargin)
		realHeight = self.height-(self.topMargin+self.botMargin)

		partWidth = self.gridSize
		if self.type == 'hex' or self.type == 'hexpath':
			partWidth *= 1.5
		partHeight = self.gridSize
		rowOffset = 0
		if self.type == 'hex' or self.type == 'hexpath':
			partHeight *= hexhalfheight/2
			rowOffset = 1

		columns = int(realWidth/partWidth)
		rows = int(realHeight/partHeight)

		horizontalOffset = (realWidth - columns*partWidth)/2 + self.leftMargin
		verticalOffset = (realHeight - (rows+rowOffset)*partHeight)/2 + self.topMargin
		if verticalOffset < 0:
			verticalOffset = 0
			rows -= 1

		if not lineColor:
			lineColor = self.lineColor
		if not fillColor:
			fillColor = self.fillColor
		if not pen:
			penWidth = str(self.pen) + self.units

		gridsize = self.gridSize
		canvas = self.doGrid(canvas, rows, verticalOffset, partHeight, columns, horizontalOffset, partWidth, gridsize, lineColor, fillColor, penWidth)

		#do we want larger grids over the smaller grids?
		#this is very kludgy (like the previous part isn't...)
		if self.bigGrid > 1:
			penWidth = str(self.bigGridPen) + self.units
			gridcount = self.bigGrid

			if self.type == 'hex' or self.type == 'hexpath':
				smallWidth = partWidth
				gridsize *= (gridcount+2)
				partWidth = gridsize*1.5
				partHeight = gridsize*hexhalfheight/2

				#determine row offset for big grid if necessary
				bigrows = int(realHeight/partHeight)
				if (bigrows+.5)*partHeight > realHeight:
					bigrows -= 1

				#determine column offset for big grid if necessary
				bigcolumns = int(realWidth/partWidth)
				if (bigcolumns+.5)*partWidth > realWidth:
					bigcolumns -= 1

				#Not at all sure about that hexhalfheight/4 modifier
				if gridcount % 2 == 1:
					horizontalOffset += 0
				else:
					horizontalOffset += (self.gridSize - (hexhalfheight/4)*self.gridSize)

				bigWidth = bigcolumns*partWidth + horizontalOffset
				leftover = int((realWidth - bigWidth)/(smallWidth*2))
				if leftover > 2:
					horizontalOffset += int(leftover/2)*smallWidth


			else:
				#determine row offset for big grid if necessary
				bigrows = int(rows/gridcount)
				if bigrows*gridcount != rows:
					rowOffset = rows-bigrows*gridcount
					if rowOffset % 2 == 1:
						rowOffset-=1
					if rowOffset:
						verticalOffset += rowOffset*gridsize/2

				#determine column offset for big grid if necessary
				bigcolumns = int(columns/gridcount)
				if bigcolumns*gridcount != columns:
					columnOffset = columns-bigcolumns*gridcount
					columnOffset -= columnOffset % 2

					if columnOffset:
						horizontalOffset += (columns-bigcolumns*gridcount)*gridsize/2

				gridsize *= gridcount
				partWidth *= gridcount
				partHeight *= gridcount

			canvas = self.doGrid(canvas, rows=bigrows, verticalOffset=verticalOffset, partHeight=partHeight, columns=bigcolumns, horizontalOffset=horizontalOffset, partWidth=partWidth, gridsize=gridsize, lineColor=self.bigGridColor, fillColor="none", penWidth=penWidth)

		return canvas

	def doGrid(self, canvas, rows, verticalOffset, partHeight, columns, horizontalOffset, partWidth, gridsize, lineColor, fillColor, penWidth):
		polySize = str(gridsize) + self.units

		group = SVGdraw.group()
		group.attributes['id'] = 'map'
		group.attributes['rows'] = rows
		group.attributes['columns'] = columns
		group.attributes['cells'] = rows * columns

		# for id
		index = 0

		print "%s COLUMNS" % columns

		for polyRow in range(0, rows):
			polyTop = verticalOffset + polyRow*partHeight
			polyTopStr = str(polyTop) + self.units

			#do stuff for hexagons
			rowcolumns = columns
			rowoffset = 0
			if self.type == 'hex' or self.type == 'hexpath':
				if polyRow % 2 == 0:
					if (self.width-(horizontalOffset+self.rightMargin-.01)) >= (columns*partWidth + gridsize):
						rowcolumns = columns + 1
				else:
					rowoffset = gridsize/2 + gridsize/4

			for polyColumn in range(0, rowcolumns):
				polyLeft = horizontalOffset + polyColumn*partWidth
				polyLeft += rowoffset
				polyLeftStr = str(polyLeft) + self.units

				if self.type == 'hex':
					gridshape = hexagon(gridsize, polyLeft, polyTop, self.units, stroke=lineColor, fill=fillColor, stroke_width=penWidth)

				elif self.type == 'hexpath':
					gridshape = hexpath(gridsize, polyLeft, polyTop, self.units, stroke=lineColor, fill=fillColor, stroke_width=penWidth)

				else:
					gridshape = SVGdraw.rect(polyLeftStr, polyTopStr, polySize, polySize, stroke=lineColor, fill=fillColor, stroke_width=penWidth)

				"""
				positions = {
					'cell_NW': (index - rows),
					'cell_N' : (index - rows * 2),
					'cell_NE': (index - rows + 1),
					'cell_SE': (index + rows),
					'cell_S' : (index + rows * 2),
					'cell_SW': (index + rows - 1)
				}
				"""

				gridshape.attributes['id'] = 'cell_' + str(index)

				neighbors_class = 'neighbors-'

				id = index - columns
				if id >= 0:
					#gridshape.attributes['cell_NW'] = "cell_" + str(id)
					neighbors_class += "cell_" + str(id) + '-'

				id = index - columns * 2
				if id >= 0:
					#gridshape.attributes['cell_N']  = "cell_" + str(id)
					neighbors_class += "cell_" + str(id) + '-'

				id = index - columns + 1
				if id >= 0:
					#gridshape.attributes['cell_NE'] = "cell_" + str(id)
					neighbors_class += "cell_" + str(id) + '-'

				id = index + columns - 1
				if id <= rows * columns:
					#gridshape.attributes['cell_SE'] = "cell_" + str(id)
					neighbors_class += "cell_" + str(id) + '-'

				id = index + columns * 2
				if id <= rows * columns:
					#gridshape.attributes['cell_S']  = "cell_" + str(id)
					neighbors_class += "cell_" + str(id) + '-'

				id = index + columns
				if id <= rows * columns:
					#gridshape.attributes['cell_SW'] = "cell_" + str(id)
					neighbors_class += "cell_" + str(id) + '-'

				gridshape.attributes['class'] = neighbors_class

				index = index + 1

				group.addElement(gridshape)

		canvas.addElement(group)
		return canvas

	def save(self, filename=None):
		document = SVGdraw.drawing()
		width = str(self.width) + self.units
		height = str(self.height) + self.units
		canvas = SVGdraw.svg(None, width, height)

		self.makeGrid(canvas)

		document.setSVG(canvas)

		if filename:
			document.toXml(filename)
		else:
			return document.toXml()

#for polygons, which can't have device-independent units
#defaults to 90 for inkscape, but 72 for illustrator is another useful value
def setPolygonResolution(resolution=90):
	global ppi, unitpoints

	ppi = int(resolution)
	unitpoints = {'in':ppi, 'cm':ppi/2.54}

setPolygonResolution()
