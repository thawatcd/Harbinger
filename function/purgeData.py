import sys,datetime,dateutil.relativedelta,pymongo

keep = sys.argv[1]
now = datetime.datetime.now()
dateTarget = now - dateutil.relativedelta.relativedelta(months=keep)
dateTarget = dateTarget.strftime('%Y/%m/%d %H:%M:%S')
#print dateTarget
from pymongo import MongoClient
client = MongoClient("mongodb://127.0.0.1:2884")
#client = MongoClient()
db = client.logsearch
collection = db.log_index
collection.remove({"datetime": {"$lte": dateTarget}})
collection = db.log_file
collection.remove({"datetime": {"$lte": dateTarget}})