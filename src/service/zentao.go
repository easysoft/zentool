package service

import (
	"encoding/json"
	"errors"
	"fmt"
	"github.com/bitly/go-simplejson"
	"github.com/easysoft/z/src/model"
	"github.com/easysoft/z/src/utils/const"
	"github.com/easysoft/z/src/utils/i118"
	"github.com/easysoft/z/src/utils/log"
	"github.com/easysoft/z/src/utils/vari"
	zentaoUtils "github.com/easysoft/z/src/utils/zentao"
	"strings"
)

type ZentaoService struct {
	HttpService *HttpService `inject:""`
}

func NewZentaoService() *ZentaoService {
	return &ZentaoService{}
}

func (s *ZentaoService) GetConfig(baseUrl string) bool {
	if vari.RequestType != "" {
		return true
	}

	url := baseUrl + "?mode=getconfig"
	body, ok := s.HttpService.Get(url)
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
	body, ok = s.HttpService.Get(url)
	if !ok {
		return false
	}

	return true
}

func (s *ZentaoService) SubmitMergeInfo(merge model.ZentaoMerge, site model.ZentaoSite) (resp model.ZentaoMergeResponse, err error) {
	ok := s.Login(site)
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

	url := site.Url + zentaoUtils.GenApiUri("mr", "apiCreate", params)

	requestObj := map[string]interface{}{"data": merge}

	respStr := ""
	respStr, ok = s.HttpService.PostObject(url, requestObj, true)
	if !ok {
		err = errors.New(fmt.Sprintf("sent request to zentao failed, response %#v", resp))
		return
	}

	var zentaoMergeResponse model.ZentaoMergeResponse
	json.Unmarshal([]byte(respStr), &zentaoMergeResponse)

	return
}

func (s *ZentaoService) GetRepoDefaultBuild(repoUrl string, site model.ZentaoSite) (build model.ZentaoRepoResponse, err error) {
	ok := s.Login(site)
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

	dataStr, ok := s.HttpService.PostObject(url, requestObj, true)
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

func (s *ZentaoService) Login(site model.ZentaoSite) bool {
	ok := s.GetConfig(site.Url)

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
	url := site.Url + uri

	params := make(map[string]string)
	params["account"] = site.Account
	params["password"] = site.Password

	var body string
	body, ok = s.HttpService.PostStr(url, params)
	if !ok || (ok && strings.Index(body, "title") > 0) { // use PostObject to login again for new system
		_, ok = s.HttpService.PostObject(url, params, true)
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
