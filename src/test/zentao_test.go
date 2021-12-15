package testing

import (
	"github.com/easysoft/z/src/model"
	zentaoService "github.com/easysoft/z/src/service/zentao"
	logUtils "github.com/easysoft/z/src/utils/log"
	"testing"
)

const (
	BaseUrl  = "http://127.0.0.1:20080"
	Account  = "admin"
	Password = "P2ssw0rd"
)

func TestGetRepoDefaultBuild(t *testing.T) {
	logUtils.InitLogger()

	zentaoBuild := zentaoService.GetRepoDefaultBuild("http://192.168.1.161:51080/root/ci_test_testng.git", GenSite())

	logUtils.Logf("%#v", zentaoBuild)
}

func TestPostMergeInfo(t *testing.T) {
	logUtils.InitLogger()

	merger := model.ZentaoMerge{}

	zentaoBuild := zentaoService.PostMergeInfo(merger, GenSite())

	logUtils.Logf("%#v", zentaoBuild)
}

func GenSite() (site model.ZentaoSite) {
	site = model.ZentaoSite{
		BaseUrl:  BaseUrl,
		Account:  Account,
		Password: Password,
	}

	return
}
