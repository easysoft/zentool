package zentaoService

import (
	"encoding/json"
	"errors"
	"fmt"
	"github.com/easysoft/z/src/model"
	constant "github.com/easysoft/z/src/utils/const"
	logUtils "github.com/easysoft/z/src/utils/log"
	"github.com/easysoft/z/src/utils/vari"
	zentaoUtils "github.com/easysoft/z/src/utils/zentao"
)

func PostMergeInfo(merge model.ZentaoMerge, site model.ZentaoSite) (resp model.ZentaoMergeResponse, err error) {
	ok := Login(site)
	if !ok {
		err = errors.New("login to zentao failed")
		return
	}

	params := ""
	if vari.RequestType == constant.RequestTypePathInfo {
		params = ""
	} else {
		params = ""
	}

	url := site.Url + zentaoUtils.GenApiUri("merge", "info", params)

	requestObj := map[string]interface{}{"data": merge}

	if vari.Verbose {
		json, _ := json.Marshal(requestObj)
		logUtils.Log(string(json))
	}

	respStr := ""
	respStr, ok = PostObject(url, requestObj, true)
	if !ok {
		err = errors.New(fmt.Sprintf("sent request to zentao failed, response %#v", resp))
		return
	}

	var zentaoMergeResponse model.ZentaoMergeResponse
	json.Unmarshal([]byte(respStr), &zentaoMergeResponse)

	return
}
