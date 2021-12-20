package commService

import (
	"bytes"
	"encoding/json"
	"github.com/easysoft/z/src/model"
	commonUtils "github.com/easysoft/z/src/utils/common"
	fileUtils "github.com/easysoft/z/src/utils/file"
	i118Utils "github.com/easysoft/z/src/utils/i118"
	logUtils "github.com/easysoft/z/src/utils/log"
	"os"
	"strings"
)

func GetConfig() (zentaoSite model.ZentaoSite) {
	file := "/Users/aaron/z" // just for debug in IDE
	//logUtils.Logf("is release %t", commonUtils.IsRelease())

	if !commonUtils.IsRelease() {
		zentaoSite = model.ZentaoSite{
			Url:      "http://192.168.1.161:8080/zentao",
			Account:  "Admin",
			Password: "Admin123#",
		}
		zentaoSite.Url = commonUtils.AddSlashForUrl(zentaoSite.Url)
		return
	}

	exe := strings.ToLower(os.Args[0])
	file = fileUtils.AbsoluteFile(exe)
	//logUtils.Logf("exe file %s", file)

	bts := fileUtils.ReadConfFromBin(file)
	bts = bytes.TrimSpace(bts)
	logUtils.Logf(i118Utils.Sprintf("read_config", file, string(bts)))

	json.Unmarshal(bts, &zentaoSite)

	zentaoSite.Url = commonUtils.AddSlashForUrl(zentaoSite.Url)

	return
}
