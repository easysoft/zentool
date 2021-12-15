package testing

import (
	"github.com/easysoft/z/src/model"
	gitlabService "github.com/easysoft/z/src/service/gitlab"
	logUtils "github.com/easysoft/z/src/utils/log"
	"testing"
)

const (
	gitlabUrl     = "http://192.168.1.161:51080/"
	gitlabAccount = ""
	gitlabToken   = "bKWnURUTkWT8rbV9Fkrm"
)

func TestCreateMr(t *testing.T) {
	logUtils.InitLogger()

	gitlabSite := model.GitLabSite{Url: gitlabUrl, Account: gitlabAccount, Token: gitlabToken}

	mr, err := gitlabService.CreateMr("2", "ci_branch", "master", gitlabSite)

	logUtils.Logf("%#v, %#v", mr, err)
}
