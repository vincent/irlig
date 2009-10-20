#!/usr/bin/env python

# These two lines are only needed if you don't put the script directly into
# the installation directory
import sys
sys.path.append('/usr/share/inkscape/extensions')

# We will use the inkex module with the predefined Effect base class.
import inkex, irlig

# The simplestyle module provides functions for style parsing.
from simplestyle import *

class HexNeighborsEffect(inkex.Effect):

    def __init__(self):
        """
        Constructor.
        Defines options of a script.
        """
        # Call the base class constructor.
        inkex.Effect.__init__(self)

    def effect(self):
        for id, node in self.selected.iteritems():
            irlig.Irlig.getNeighbors(node)
            pass

# Create effect instance and apply it.
effect = HexNeighborsEffect()
effect.affect()

