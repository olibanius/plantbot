import sys;
#exit(0);

import time
import RPi.GPIO as GPIO

# Next we setup the pins for use!
GPIO.setmode(GPIO.BCM)
GPIO.setwarnings(False)
GPIO.setup(17,GPIO.OUT)
GPIO.setup(18,GPIO.OUT)

# Makes the motor spin one way for 2 seconds
GPIO.output(17, True)
GPIO.output(18, False)
time.sleep(2)

GPIO.output(17, False)
GPIO.output(18, False)
quit()

# Returning true.. right?
print sys.argv[1];
