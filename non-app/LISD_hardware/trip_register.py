from gpsdata import *
import requests
import time
import json
from datetime import datetime

def trip_init(vehicle_id):
	comp=False
	trip_id=0
	while not comp:
		try:
			location=loc()
			dt=datetime.now()
			cur_time=int(time.mktime(dt.timetuple()))
			if location:
				latitude=(location[0])
				longitude=(location[1])
				if latitude != "0.0" and longitude != "0.0":
					userdata={}
					userdata["user_id"]="11"
					userdata["vehicle_id"]=vehicle_id
					userdata["timestamp"]=dt
					userdata["latitude"]=latitude
					userdata["longitude"]=longitude
					link = """https://lisd.backslapping80.hasura-app.io/api/insert_trip?user_id={str1}&vehicle_id={str2}&latitude={str3}&longitude={str4}&timestamp={str5}""".format(str1=userdata["user_id"],
																											str2=userdata["vehicle_id"],
																											str3=userdata["latitude"],
																											str4=userdata["longitude"],
																											str5=userdata["timestamp"])
					resp = requests.get(link,timeout=10)
					if resp.status_code==200:
						print(resp.content)
						getdata=json.loads(resp.content.decode('utf-8'))
						trip_id=getdata["trip_id"]
				comp=True
				return cur_time,trip_id
		except:
			pass

