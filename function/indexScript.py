import glob,gzip,sys,timeit,re,os,datetime,urllib2,pymongo,errno

def mkdir_p(path):
	try:
		os.makedirs(path)
	except OSError as exc: # Python >2.5
		if exc.errno == errno.EEXIST and os.path.isdir(path):
			pass
		else: raise

def openLogFile():
	now = datetime.datetime.now()
	year = now.strftime('%Y')
	month = now.strftime('%m')
	day = now.strftime('%d')
	filePath = '/home/logsearch/harbinger/log/'
	filePath += year + month +'/'
	fileName = filePath + year + month + day + '.log'
	try:
			logFile = open(fileName, 'a')
			return logFile
	except IOError:
			mkdir_p(filePath)
			raise
		
#start = timeit.default_timer()
		
mode = sys.argv[1]

from pymongo import MongoClient
client = MongoClient("mongodb://127.0.0.1:2884")
#client = MongoClient()
db = client.logsearch

##############
# Test mode ##
###################################################################
if mode == 'test':
	print "In test mode, will show a first log file in log path.\nShow first 110 indexs in files\n"
	logPath = urllib2.unquote(sys.argv[2])
	logType = sys.argv[3]
	logStartTag = re.compile(urllib2.unquote(sys.argv[4]))
	logEndTag = re.compile(urllib2.unquote(sys.argv[5]))
	msisdnRegex = re.compile(urllib2.unquote(sys.argv[6]))
	dateHolder = sys.argv[7]
	dateRegex = re.compile(urllib2.unquote(sys.argv[8]))
	dateFormat = urllib2.unquote(sys.argv[9])
	timeRegex = re.compile(urllib2.unquote(sys.argv[10]))
	timeFormat = urllib2.unquote(sys.argv[11])
	# generate find command
	find_cmd = 'find ' + logPath + " -type f|sort -rn"
	#find $1 -type f -print0 | xargs -0 stat --format '%Y :%y %n' | sort -nr | cut -d: -f2- | head
###################################################################
###############
# Index mode ##
###################################################################
else:
	print "Start Indexing"
	# connect database and query parameter with id
	from bson.objectid import ObjectId
	collection = db.service_config
	cursor = collection.find_one({"_id": ObjectId(sys.argv[2])})
	service = cursor['service']
	system = cursor['system']
	node = cursor['node']
	process = cursor['process']
	logPath = cursor['path']
	logType = cursor['logType']
	logStartTag = re.compile(cursor['logStartTag'])
	logEndTag = re.compile(cursor['logEndTag'])
	msisdnRegex = re.compile(cursor['msisdnRegex'])
	dateHolder = cursor['dateHolder']
	dateRegex = re.compile(cursor['dateRegex'])
	dateFormat = cursor['dateFormat']
	timeRegex = re.compile(cursor['timeRegex'])
	timeFormat = cursor['timeFormat']
	mmin = cursor['mmin']
	interval = cursor['interval']
	# generate find command
	find_cmd = 'find ' + logPath + ' -type f'
	if mmin != "":
		find_cmd += ' -mmin -' + mmin
	if interval != "":
		find_cmd += ' -mmin +' + interval
	indexLogFile = openLogFile()
###################################################################

dateTimeFormat = dateFormat + ' ' + timeFormat

print "Find file with '"+ find_cmd +"'"
# find file
f = os.popen(find_cmd)
files = f.readlines()
#####################################################

for file in files:
	try:
		today = datetime.datetime.now().strftime('%Y/%m/%d %H:%M:%S')
		#########################
		## read file from path ##
		###############################################
		file_path = file.rstrip('\n')
		if mode == 'test':
			print "PATH: " + file_path + "\n"
			print '{0:6}  {1:11}  {2:19}  {3:8}  {4:6}'.format('index', 'msisdn', 'datetime', 'startTag', 'endTag')
		else:
			# check file already indexed?
			collection = db.log_file
			cursor = collection.find_one({"service":service, "system":system, "node":node, "process":process, "path":file_path})
			if cursor: # already indexed, skip
				print file_path + ", This file is already indexed."
				indexLogFile.write( today + " Skip " + file_path + " , This file is already indexed\n")
				continue
			else: # not indexed add path and date to database
				print file_path + ", This file not already indexed."
				indexLogFile.write( today + " Index " + file_path + " , This file is not already indexed\n")
				collection.insert({"service":service, "system":system, "node":node, "process":process, "path":file_path, "datetime":today})
				
		
		collection = db.log_index
		if '.gz' in file_path:
			fileContent = gzip.open(file_path,'r')
		else:
			fileContent = open(file_path,'r')
		###############################################

		##########################
		## define some variable ##
		###############################################
		lineNumber = 0
		msisdn = ''
		date = ''
		time = ''
		index = 0
		startTag = 0
		endTag = 0
		showRecord = 0
		###############################################

		#########################################
		## if date in path, get date from path ##
		#################################################################
		if dateHolder == 'outside' and dateRegex.search(file_path) != None:
			date = dateRegex.search(file_path).group(1)
		#################################################################

		for line in fileContent:
			lineNumber += 1
			if showRecord == 110: # in test mode exit when already show 110 indexs
				sys.exit(1)
			#############################
			## Find msisdn, date, time ##
			#############################################################
			if msisdn == '' and msisdnRegex.search(line) != None:
				msisdn = msisdnRegex.search(line).group(1)
				index = lineNumber
			if dateHolder == 'inside' and date == '' and dateRegex.search(line) != None:
				date = dateRegex.search(line).group(1)
			if time == '' and timeRegex.search(line) != None:
				time = timeRegex.search(line).group(1)
			#############################################################

			######################
			## if multiline log ##
			#############################################################
			if logType == 'multiLine':
				# when find start tag
				if logStartTag.search(line) != None:
					startTag = lineNumber
				# when find end tag
				if logEndTag.search(line) != None:
					endTag = lineNumber
					# if get all variable that require will print or insert in database
					if msisdn != '' and date != '' and time != '':
						# combine date time and change format
						fullDateTime = date + ' ' + time
						fullDateTime = datetime.datetime.strptime(fullDateTime, dateTimeFormat)
						fullDateTime = fullDateTime.strftime('%Y/%m/%d %H:%M:%S')
						if mode == 'test':
							print '{0:6d}  {1:11}  {2:19}  {3:8d}  {4:6d}'.format(index, msisdn, fullDateTime, startTag, endTag)
							showRecord += 1
						else:
							collection.insert({ "service": service,
											  	"system": system,
											   	"node": node,
												"process": process,
											   	"path": file_path,
											   	"msisdn": msisdn,
											   	"index": index,
											   	"datetime": fullDateTime,
											   	"startTag": startTag,
											   	"endTag": endTag })
					# clear variable when found end tag
					msisdn = ''
					time = ''
					startTag = 0
					endTag = 0
					if dateHolder == 'inside':
						date = ''	# if date in log
			#############################################################
			#######################
			## if singleline log ##
			#############################################################
			elif logType == 'singleLine':
				# if get all variable that require will print or insert in database
				if msisdn != '' and date != '' and time != '':
					# combine date time and change format
					fullDateTime = date + ' ' + time
					fullDateTime = datetime.datetime.strptime(fullDateTime, dateTimeFormat)
					fullDateTime = fullDateTime.strftime('%Y/%m/%d %H:%M:%S')
					if mode == 'test':
						print '{0:6d}  {1:11}  {2:19}  {3:8d}  {4:6d}'.format(index, msisdn, fullDateTime, index, index)
						showRecord += 1
					else:
						collection.insert({ "service": service,
										  	"system": system,
										   	"node": node,
											"process": process,
										   	"path": file_path,
										   	"msisdn": msisdn,
										   	"index": index,
										   	"datetime": fullDateTime,
										   	"startTag": index,
										   	"endTag": index })
				#clear variable every line
				msisdn = ''
				time = ''
				if dateHolder == 'inside':
					date = ''	#if date in log
			#############################################################
			

		fileContent.close()
		# for index test, index a file then exit
		if mode == 'test':
			break
	except IOError:
	    print "I/O error"

if mode != 'test':
	indexLogFile.close()
#stop = timeit.default_timer()
#print stop-start