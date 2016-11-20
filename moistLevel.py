#!/usr/bin/python

import smbus, time, sys

if len(sys.argv) == 1:
	quit("Too few arguments");

sensorNr = int(sys.argv[1]);

bus = smbus.SMBus(1)

bus.write_byte_data(0x48,0x40 | ((sensorNr) & 0x03), 0)
v = bus.read_byte(0x48)
v = bus.read_byte(0x48) # Double read, got the same reading as last time before.. weird
print v
