#!/usr/bin/env python

# These two lines are only needed if you don't put the script directly into
# the installation directory
import sys
sys.path.append('/usr/share/inkscape/extensions')

# We will use the inkex module with the predefined Effect base class.
import inkex, gridmaps

# The simplestyle module provides functions for style parsing.
from simplestyle import *

class HexGridEffect(inkex.Effect):

    def __init__(self):
        """
        Constructor.
        Defines options of a script.
        """
        # Call the base class constructor.
        inkex.Effect.__init__(self)

        self.OptionParser.add_option("--margins", action="store", type="float", dest="margins", default=0.0, help="Side margins")
        self.OptionParser.add_option("--topmargin", action="store", type="float", dest="topmargin", default=1.0, help="Top margins")
        self.OptionParser.add_option("--units", action="store", type="string", dest="units", default="cm", help="Units: in or cm")
        self.OptionParser.add_option("--gridsize", action="store", type="float", dest="gridsize", default=10, help="Size of grid")
        self.OptionParser.add_option("--gridkind", action="store", type="string", dest="gridkind", default="hexpath", help="Kind of grid")
        self.OptionParser.add_option("--mapwidth", action="store", type="int", dest="mapwidth", default=5, help="Map width")
        self.OptionParser.add_option("--mapheight", action="store", type="int", dest="mapheight", default=5, help="Map height")
        self.OptionParser.add_option("--resolution", action="store", type="float", dest="resolution", default=90, help="Polygon resolution")

    def effect(self):

        # Get script's options values
        margins     = self.options.margins
        topMargin   = self.options.topmargin
        units       = self.options.units
        gridsize    = self.options.gridsize
        gridkind    = self.options.gridkind
        mapwidth    = self.options.mapwidth
        mapheight   = self.options.mapheight
        resolution  = self.options.resolution

        lineColor   = "black"
        fillColor   = "none"
        pen         = .01
        bigGrid     = None
        bigGridPen  = .02
        description = "map grid"
        bigGridColor="red"

        # Get access to main SVG document element and get its dimensions.
        svg = self.document.getroot()

        gridMap = gridmaps.grid(mapheight, mapwidth, description, gridsize, units, lineColor, fillColor, pen, margins, bigGrid, bigGridColor, bigGridPen, gridkind)

        # Connect elements together.
        svg.append(inkex.etree.fromstring(gridMap.save()))

# Create effect instance and apply it.
effect = HexGridEffect()
effect.affect()

