import datetime, os, errno

def mkdir_p(path):
	try:
		os.makedirs(path)
	except OSError as exc: # Python >2.5
		if exc.errno == errno.EEXIST and os.path.isdir(path):
			pass
		else: raise

def openFile():
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

logFile = openFile()
logFile.write('hello, World\n')
logFile.close()