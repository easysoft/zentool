package zentaoService

import (
	"encoding/json"
	"errors"
	"github.com/easysoft/z/src/model"
	constant "github.com/easysoft/z/src/utils/const"
	logUtils "github.com/easysoft/z/src/utils/log"
	"github.com/easysoft/z/src/utils/vari"
	zentaoUtils "github.com/easysoft/z/src/utils/zentao"
)

func GetRepoDefaultBuild(repoUrl string, site model.ZentaoSite) (build model.ZentaoRepoResponse, err error) {
	ok := Login(site)
	if !ok {
		err = errors.New("login fail")
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
	if !ok {
		err = errors.New("get repo default build fail")
		return
	}

	json.Unmarshal([]byte(dataStr), &build)

	return
}
