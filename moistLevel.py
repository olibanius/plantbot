#!/usr/bin/python

import smbus
import time

bus = smbus.SMBus(1)

bus.write_byte_data(0x48,0x40 | ((0) & 0x03), 0)
v = bus.read_byte(0x48)
print v
