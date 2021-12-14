package serverConst

import constant "github.com/easysoft/zentaoatf/src/utils/const"

const (
	HeartBeatInterval    = 60
	CheckUpgradeInterval = 30

	LogDir = "log"

	QiNiuURL         = "https://dl.cnezsoft.com/" + constant.AppName + "/"
	VersionURL  = QiNiuURL + "version.txt"
	DownloadURL = QiNiuURL + "%s/%s/" + constant.AppName + ".zip"
)
