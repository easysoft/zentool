package zentaoService

import (
	"encoding/json"
	"errors"
	"github.com/easysoft/z/src/model"
	constant "github.com/easysoft/z/src/utils/const"
	i118Utils "github.com/easysoft/z/src/utils/i118"
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

	url := site.Url + zentaoUtils.GenApiUri("repo", "apiGetRepoByUrl", params)

	requestObj := map[string]interface{}{"repoUrl": repoUrl}

	dataStr, ok := PostObject(url, requestObj, true)
	if !ok {
		err = errors.New(i118Utils.Sprintf("http_request_fail", dataStr))
		return
	}

	json.Unmarshal([]byte(dataStr), &build)
	if build.FileServerUrl == "" {
		err = errors.New(i118Utils.Sprintf("get_repo_default_build_fail", dataStr))
	}

	return
}
