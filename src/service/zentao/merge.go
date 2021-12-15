package zentaoService

import (
	"encoding/json"
	"github.com/easysoft/z/src/model"
	constant "github.com/easysoft/z/src/utils/const"
	logUtils "github.com/easysoft/z/src/utils/log"
	"github.com/easysoft/z/src/utils/vari"
	zentaoUtils "github.com/easysoft/z/src/utils/zentao"
)

func PostMergeInfo(merge model.ZentaoMerge, site model.ZentaoSite) (ok bool) {
	ok = Login(site)
	if !ok {
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

	_, ok = PostObject(url, requestObj, true)

	return
}
