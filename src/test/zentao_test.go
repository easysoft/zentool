package testing

import (
	zentaoService "github.com/easysoft/z/src/service/zentao"
	logUtils "github.com/easysoft/z/src/utils/log"
	"testing"
)

func TestGetRepoDefaultBuild(t *testing.T) {
	logUtils.InitLogger()

	zentaoBuild := zentaoService.GetRepoDefaultBuild(
		"https://back.zcorp.cc/pms", "admin", "P2ssw0rd", "master")

	logUtils.Logf("%#v", zentaoBuild)
}
