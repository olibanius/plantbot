import sys;
#exit(0);

import time, sys
import RPi.GPIO as GPIO

if len(sys.argv) < 3:
	quit("Too few arguments");

motorGPIO = int(sys.argv[1]);
execTime = float(sys.argv[2]);

# Next we setup the pins for use!
GPIO.setmode(GPIO.BCM)
GPIO.setwarnings(False)
GPIO.setup(17,GPIO.OUT)
GPIO.setup(18,GPIO.OUT)
GPIO.setup(22,GPIO.OUT)
GPIO.setup(23,GPIO.OUT)

# Makes the motor spin one way for 2 seconds
GPIO.output(motorGPIO, True)
time.sleep(execTime)

GPIO.output(17, False)
GPIO.output(18, False)
GPIO.output(22, False)
GPIO.output(23, False)
quit()

# Returning true.. right?
print sys.argv[1];
