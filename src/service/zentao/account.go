package zentaoService

import (
	"github.com/bitly/go-simplejson"
	"github.com/easysoft/z/src/utils/const"
	"github.com/easysoft/z/src/utils/i118"
	"github.com/easysoft/z/src/utils/log"
	"github.com/easysoft/z/src/utils/vari"
	"strings"
)

func Login(baseUrl string, account string, password string) bool {
	ok := GetConfig(baseUrl)

	if !ok {
		logUtils.Log(i118Utils.Sprintf("fail_to_login"))
		return false
	}

	uri := ""
	if vari.RequestType == constant.RequestTypePathInfo {
		uri = "user-login.json"
	} else {
		uri = "index.php?m=user&f=login&t=json"
	}
	url := baseUrl + uri

	params := make(map[string]string)
	params["account"] = account
	params["password"] = password

	var body string
	body, ok = PostStr(url, params)
	if ok && strings.Index(body, "title") > 0 { // use PostObject to login again for new system
		_, ok = PostObject(url, params, true)
	}
	if ok {
		if vari.Verbose {
			logUtils.Log(i118Utils.Sprintf("success_to_login"))
		}
	} else {
		logUtils.Log(i118Utils.Sprintf("fail_to_login"))
	}

	return ok
}

func GetConfig(baseUrl string) bool {
	if vari.RequestType != "" {
		return true
	}

	// get config
	url := baseUrl + "?mode=getconfig"
	body, ok := Get(url)
	if !ok {
		return false
	}

	json, _ := simplejson.NewJson([]byte(body))
	vari.ZenTaoVersion, _ = json.Get("version").String()
	vari.SessionId, _ = json.Get("sessionID").String()
	vari.SessionVar, _ = json.Get("sessionVar").String()
	vari.RequestType, _ = json.Get("requestType").String()
	vari.RequestFix, _ = json.Get("requestFix").String()

	// check site path by calling login interface
	uri := ""
	if vari.RequestType == constant.RequestTypePathInfo {
		uri = "user-login.json"
	} else {
		uri = "index.php?m=user&f=login&t=json"
	}
	url = baseUrl + uri
	body, ok = Get(url)
	if !ok {
		return false
	}

	return true
}
