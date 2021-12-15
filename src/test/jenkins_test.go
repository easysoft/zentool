package testing

import (
	"github.com/easysoft/z/src/model"
	jenkinsService "github.com/easysoft/z/src/service/jenkins"
	logUtils "github.com/easysoft/z/src/utils/log"
	"testing"
)

const (
	jenkinsUrl     = ""
	jenkinsAccount = ""
	jenkinsToken   = ""
)

func TestJob(t *testing.T) {
	logUtils.InitLogger()

	jenkinsSite := model.JenkinsSite{Url: jenkinsUrl, Account: jenkinsAccount, Token: jenkinsToken}
	queueId, buildId := jenkinsService.BuildJob("ci_test", "/home/jenkins/dir", jenkinsSite, true)

	logUtils.Logf("%d, %d", queueId, buildId)
}
