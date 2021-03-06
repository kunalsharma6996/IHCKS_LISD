import os
import RPi.GPIO as GPIO
from theft_check import *
from time import sleep
from trip_register import *
from trip_update import *
from CFstat import *
import peewee as pw

RELAY = 23
WHITE_LED = 24
GREEN_LED = 17

GPIO.setwarnings(False)
GPIO.setmode(GPIO.BCM)
GPIO.setup(RELAY,GPIO.OUT)
GPIO.setup(WHITE_LED,GPIO.OUT)
GPIO.setup(GREEN_LED,GPIO.OUT)

theft=False
vehicle_id="1"
while not theft:
        x=os.popen("node card2.js").read().split('\n')
        if x[0] == "card removed":
                GPIO.output(RELAY,GPIO.LOW)
                GPIO.output(WHITE_LED,GPIO.LOW)
                GPIO.output(GREEN_LED,GPIO.LOW)
                print("Not available")
        else:
                db=pw.SqliteDatabase('admin')
                db.connect()
                cur=db.execute_sql("select * from admin_cards where id='%s'"%x[1])
                z=list(cur.fetchall())
                try:
                        per=z[0][1]
                except:
                        per=None
                if per=="add":
                        GPIO.output(RELAY,GPIO.LOW)
                        GPIO.output(WHITE_LED,GPIO.HIGH)
                        GPIO.output(GREEN_LED,GPIO.LOW)
                        print("Add card for addition")
                        sleep(5)
                        x=os.popen("node card2.js").read().split('\n')
                        while x[0] == "card removed":
                                x=os.popen("node card2.js").read().split('\n')
                                continue
                        cur=db.execute_sql("select * from admin_cards where id='%s';"%x[1])
                        z=len(cur.fetchall())
                        if z!=0:
                                print("Card already registered")
                        else:
                                db.execute_sql("insert into admin_cards values('%s','access');"%x[1])
                                print("Card Registered")
                        for i in range(3):
                                GPIO.output(GREEN_LED,GPIO.LOW)
                                sleep(0.5)
                                GPIO.output(GREEN_LED,GPIO.HIGH)
                                sleep(0.5)
                elif per=="delete":
                        GPIO.output(RELAY,GPIO.LOW)
                        GPIO.output(WHITE_LED,GPIO.HIGH)
                        GPIO.output(GREEN_LED,GPIO.LOW)
                        print("Add card for deletion")
                        sleep(2)
                        x=os.popen("node card2.js").read().split('\n')
                        while x[0] == "card removed":
                                x=os.popen("node card2.js").read().split('\n')
                                continue
                        cur=db.execute_sql("select * from admin_cards where id='%s' and purpose='access';"%x[1])
                        z=len(cur.fetchall())
                        if z==0:
                                print("Card not registered yet")
                        else:
                                for i in range(3):
                                        GPIO.output(GREEN_LED,GPIO.LOW)
                                        sleep(0.5)
                                        GPIO.output(GREEN_LED,GPIO.HIGH)
                                        sleep(0.5)
                                db.execute_sql("delete from admin_cards where id='%s';"%x[1])
                                print("Card Deleted")
                elif per=="access":
                        istrip=False;
                        init_time=0;
                        GPIO.output(RELAY,GPIO.HIGH)
                        GPIO.output(WHITE_LED,GPIO.LOW)
                        print("Card Accepted")
                        x=os.popen("node card2.js").read().split('\n')
                        trip_id=-1;
                        while x[0] != "card removed":
                                print("Sending location")
                                if(not istrip):
                                        init_time,trip_id=trip_init(vehicle_id)
                                        if trip_id!=-1:
                                                istrip = True
                                else:
                                        trip_cont(init_time,istrip,trip_id,vehicle_id)
                                x=os.popen("node card2.js").read().split('\n')
                                #poll_count(x[1],100)
                                theft=stop_command(vehicle_id)
                                if theft:
                                        print("Vehicle stolen")
                                        GPIO.output(RELAY,GPIO.LOW)
                                        break
                        trip_time=trip_cont(init_time,False,trip_id,vehicle_id)
                else:
                        print("Card not registered")
 #   except:
#       comp=True
while True:
        GPIO.output(GREEN_LED,GPIO.LOW)
        GPIO.output(WHITE_LED,GPIO.HIGH)
        sleep(2)
        GPIO.output(GREEN_LED,GPIO.HIGH)
        GPIO.output(WHITE_LED,GPIO.LOW)
        sleep(2)
GPIO.cleanup()
