package testing

import (
	commService "github.com/easysoft/z/src/service/comm"
	fileUtils "github.com/easysoft/z/src/utils/file"
	logUtils "github.com/easysoft/z/src/utils/log"
	"testing"
)

func TestReadBin(t *testing.T) {
	logUtils.InitLogger()

	srcBranchDir := fileUtils.GetWorkDir()
	logUtils.Logf("%#v", srcBranchDir)

	conf := commService.GetConfig()

	logUtils.Logf("%#v", conf)
}
