package zentaoService

import (
	"encoding/json"
	"github.com/easysoft/z/src/model"
	constant "github.com/easysoft/z/src/utils/const"
	logUtils "github.com/easysoft/z/src/utils/log"
	"github.com/easysoft/z/src/utils/vari"
	zentaoUtils "github.com/easysoft/z/src/utils/zentao"
)

func GetRepoDefaultBuild(repoUrl string, site model.ZentaoSite) (build model.ZentaoRepoResponse) {
	ok := Login(site)
	if !ok {
		return
	}

	params := ""
	if vari.RequestType == constant.RequestTypePathInfo {
		params = ""
	} else {
		params = ""
	}

	url := site.Url + zentaoUtils.GenApiUri("repo", "info", params)

	requestObj := map[string]interface{}{"repoUrl": repoUrl}

	if vari.Verbose {
		json, _ := json.Marshal(requestObj)
		logUtils.Log(string(json))
	}

	dataStr, ok := PostObject(url, requestObj, true)
	json.Unmarshal([]byte(dataStr), &build)

	return
}
